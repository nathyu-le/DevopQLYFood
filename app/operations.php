<?php
declare(strict_types=1);

// Runtime state contains metrics and allowlisted events, never request input or credentials.
function ops_directory(): string
{
    $directory = dirname(__DIR__) . '/var/operations';
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Operations storage unavailable.');
    }
    @chmod(dirname($directory), 0700);
    @chmod($directory, 0700);
    $protection = dirname($directory) . '/.htaccess';
    if (!is_file($protection)) {
        file_put_contents($protection, "Require all denied\n", LOCK_EX);
    }
    return $directory;
}

function ops_time(): string { return gmdate('Y-m-d\TH:i:s\Z'); }

function ops_write_json_atomic(string $path, array $value): void
{
    $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    if (strlen($encoded) > 33554432) { throw new RuntimeException('Snapshot exceeds the supported 32 MiB limit.'); }
    $temp = tempnam(dirname($path), 'pending-');
    if ($temp === false) { throw new RuntimeException('Temporary file unavailable.'); }
    try {
        if (file_put_contents($temp, $encoded) !== strlen($encoded) || !chmod($temp, 0600) || !rename($temp, $path)) {
            throw new RuntimeException('Atomic storage write failed.');
        }
    } finally { if (is_file($temp)) { unlink($temp); } }
}

function ops_event(string $event, array $fields = []): void
{
    try {
        $allowed = ['route', 'method', 'status', 'duration_ms', 'request_id', 'exception',
            'alert', 'state', 'row_count', 'sha256', 'error_class'];
        $entry = ['time' => ops_time(), 'event' => $event];
        foreach ($allowed as $key) {
            if (isset($fields[$key]) && (is_scalar($fields[$key]) || $fields[$key] === null)) {
                $entry[$key] = is_string($fields[$key]) ? substr($fields[$key], 0, 160) : $fields[$key];
            }
        }
        $path = ops_directory() . '/application.jsonl';
        $guard = fopen($path . '.lock', 'c');
        if (!$guard || !flock($guard, LOCK_EX)) { return; }
        try {
            clearstatcache(true, $path);
            if (is_file($path) && filesize($path) > 1048576) {
                for ($i = 6; $i >= 1; $i--) {
                    $old = $path . '.' . $i;
                    if (is_file($old)) {
                        if ($i === 6) { unlink($old); } else { rename($old, $path . '.' . ($i + 1)); }
                    }
                }
                rename($path, $path . '.1');
            }
            file_put_contents($path, json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n", FILE_APPEND | LOCK_EX);
            @chmod($path, 0600);
        } finally { flock($guard, LOCK_UN); fclose($guard); }
    } catch (Throwable) {
        // Monitoring must not break normal menu requests or disclose filesystem paths.
        error_log('Restaurant Menu: operations log unavailable');
    }
}

function ops_register_request(): void
{
    static $registered = false;
    if ($registered || PHP_SAPI === 'cli') { return; }
    $registered = true;
    $start = microtime(true);
    $id = bin2hex(random_bytes(8));
    header('X-Request-ID: ' . $id);
    register_shutdown_function(static function () use ($start, $id): void {
        $route = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'unknown'));
        $route = preg_match('/\A[a-zA-Z0-9_-]+\.php\z/', $route) ? $route : 'unknown';
        $method = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? 'POST' : 'GET';
        ops_event('http_request', ['route' => $route, 'method' => $method,
            'status' => http_response_code() ?: 200,
            'duration_ms' => round((microtime(true) - $start) * 1000, 2), 'request_id' => $id]);
    });
}

function ops_read(string $path): ?string
{
    if (!is_readable($path)) { return null; }
    $data = @file_get_contents($path);
    return is_string($data) ? trim($data) : null;
}

function ops_memory_limit(): ?int
{
    $raw = trim((string) ini_get('memory_limit'));
    if (!preg_match('/\A(\d+)([KMG]?)\z/i', $raw, $match)) { return null; }
    return (int) $match[1] * (1024 ** (int) array_search(strtoupper($match[2]), ['', 'K', 'M', 'G'], true));
}

function ops_resources(array $previous): array
{
    $now = microtime(true);
    $cpu = null; $cpuSource = 'unavailable'; $counter = [];
    $cpuStat = ops_read('/sys/fs/cgroup/cpu.stat');
    $quota = ops_read('/sys/fs/cgroup/cpu.max');
    if ($cpuStat !== null && $quota !== null && preg_match('/usage_usec (\d+)/', $cpuStat, $m)
        && preg_match('/\A(\d+) (\d+)\z/', $quota, $q) && (int) $q[2] > 0 && (int) $q[1] > 0) {
        $counter = ['kind' => 'cgroup', 'usage' => (float) $m[1], 'time' => $now];
        if (($previous['kind'] ?? '') === 'cgroup' && $now > ($previous['time'] ?? $now)) {
            $cpu = (($counter['usage'] - $previous['usage']) / 1000000) / ($now - $previous['time']) / ((float) $q[1] / (float) $q[2]) * 100;
        }
        $cpuSource = 'cgroup quota';
    } elseif (($raw = ops_read('/proc/stat')) !== null && preg_match('/\Acpu\s+([^\n]+)/', $raw, $m)) {
        // First eight counters exclude guest counters already included in user/nice.
        $numbers = array_map('floatval', preg_split('/\s+/', trim($m[1])));
        $counter = ['kind' => 'proc', 'total' => array_sum(array_slice($numbers, 0, 8)),
            'idle' => ($numbers[3] ?? 0) + ($numbers[4] ?? 0), 'time' => $now];
        $delta = $counter['total'] - ($previous['total'] ?? $counter['total']);
        if (($previous['kind'] ?? '') === 'proc' && $delta > 0) {
            $cpu = 100 * (1 - ($counter['idle'] - $previous['idle']) / $delta);
        }
        $cpuSource = 'OS visible to PHP; may be shared host';
    }
    $used = null; $limit = null; $memorySource = 'unavailable';
    $cgUsed = ops_read('/sys/fs/cgroup/memory.current');
    $cgMax = ops_read('/sys/fs/cgroup/memory.max');
    if ($cgUsed !== null && $cgMax !== null && preg_match('/\A[0-9]+\z/', $cgUsed) && preg_match('/\A[0-9]+\z/', $cgMax) && (float) $cgMax > 0) {
        $used = (float) $cgUsed; $limit = (float) $cgMax; $memorySource = 'cgroup memory';
    } elseif (($mem = ops_read('/proc/meminfo')) !== null
        && preg_match('/MemTotal:\s+(\d+)/', $mem, $total) && preg_match('/MemAvailable:\s+(\d+)/', $mem, $available)) {
        $limit = (float) $total[1] * 1024; $used = $limit - (float) $available[1] * 1024;
        $memorySource = 'OS visible to PHP; may be shared host';
    } else {
        $used = memory_get_usage(true); $limit = ops_memory_limit();
        $memorySource = 'PHP allocation, not account RAM';
    }
    return ['cpu_percent' => $cpu === null ? null : round(max(0, min(100, $cpu)), 2),
        'cpu_source' => $cpuSource, 'memory_mb' => round($used / 1048576, 2),
        'memory_percent' => $limit ? round(100 * $used / $limit, 2) : null,
        'memory_source' => $memorySource, 'php_memory_mb' => round(memory_get_usage(true) / 1048576, 2),
        '_counter' => $counter];
}

function ops_database_health(): array
{
    $start = microtime(true);
    try {
        $config = require dirname(__DIR__) . '/config/config.local.php';
        $d = $config['db'];
        $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $d['host'], (int) $d['port'], $d['name']),
            $d['user'], $d['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 2]);
        $pdo->query('SELECT id FROM dishes LIMIT 1');
        return ['health' => 'ok', 'db_ms' => round((microtime(true) - $start) * 1000, 2)];
    } catch (Throwable $e) {
        ops_event('database_unavailable', ['error_class' => get_class($e)]);
        return ['health' => 'unavailable', 'db_ms' => round((microtime(true) - $start) * 1000, 2)];
    }
}

function ops_update_alert(array $old, bool $bad, bool $known, string $name): array
{
    $old += ['state' => 'ok', 'failures' => 0, 'successes' => 0, 'changed_at' => ops_time()];
    if (!$known) { return $old; }
    $next = $old;
    $next['failures'] = $bad ? min(2, $old['failures'] + 1) : 0;
    $next['successes'] = $bad ? 0 : min(2, $old['successes'] + 1);
    if ($bad) { $next['state'] = $next['failures'] >= 2 ? 'firing' : ($old['state'] === 'firing' ? 'firing' : 'pending'); }
    elseif ($old['state'] === 'firing' && $next['successes'] < 2) { $next['state'] = 'firing'; }
    else { $next['state'] = 'ok'; }
    if ($old['state'] !== $next['state']) {
        $next['changed_at'] = ops_time();
        ops_event('alert_transition', ['alert' => $name, 'state' => $next['state']]);
    }
    return $next;
}

function ops_collect(?array $health = null): array
{
    $directory = ops_directory();
    $guard = fopen($directory . '/state.lock', 'c');
    if (!$guard || !flock($guard, LOCK_EX)) { throw new RuntimeException('Collector lock unavailable.'); }
    try {
        $raw = ops_read($directory . '/state.json');
        $state = $raw === null ? [] : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (isset($state['sampled_at']) && microtime(true) - (float) $state['sampled_at'] < 5) { return $state; }
        $metrics = ops_resources($state['counter'] ?? []);
        $counter = $metrics['_counter']; unset($metrics['_counter']);
        $sample = ['time' => ops_time()] + ($health ?? ops_database_health()) + $metrics;
        $alerts = $state['alerts'] ?? [];
        $conditions = ['database_unavailable' => [$sample['health'] !== 'ok', true],
            'cpu_high' => [($sample['cpu_percent'] ?? 0) >= 85, $sample['cpu_percent'] !== null],
            'memory_high' => [($sample['memory_percent'] ?? 0) >= 85, $sample['memory_percent'] !== null]];
        foreach ($conditions as $name => [$bad, $known]) { $alerts[$name] = ops_update_alert($alerts[$name] ?? [], $bad, $known, $name); }
        $history = $state['history'] ?? []; $history[] = $sample;
        $state = ['sampled_at' => microtime(true), 'latest' => $sample, 'history' => array_slice($history, -360),
            'alerts' => $alerts, 'counter' => $counter];
        ops_write_json_atomic($directory . '/state.json', $state);
        return $state;
    } finally { flock($guard, LOCK_UN); fclose($guard); }
}

function ops_log_tail(): array
{
    $path = ops_directory() . '/application.jsonl';
    if (!is_file($path)) { return []; }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_values(array_filter(array_map(static fn(string $line) => json_decode($line, true), array_slice($lines ?: [], -100)), 'is_array'));
}

function ops_public_state(array $state): array
{
    unset($state['counter']);
    $state['revision'] = trim((string) @file_get_contents(dirname(__DIR__) . '/REVISION'));
    $state['rules'] = ['sample_seconds' => 5, 'consecutive_failures' => 2, 'consecutive_recoveries' => 2,
        'cpu_percent' => 85, 'memory_percent' => 85, 'history_limit' => 360];
    $state['logs'] = ops_log_tail();
    $backup = ops_read(ops_directory() . '/backup-latest.json');
    $state['last_backup'] = $backup === null ? null : json_decode($backup, true);
    return $state;
}
