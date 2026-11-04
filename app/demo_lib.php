<?php
declare(strict_types=1);
require_once __DIR__ . '/backup_lib.php';

final class DemoActionError extends RuntimeException {}

function demo_directory(): string
{
    $path = dirname(ops_directory()) . '/classroom-demo';
    if (is_link($path) || (!is_dir($path) && !mkdir($path, 0700, true) && !is_dir($path))) {
        throw new RuntimeException('Demo storage unavailable.');
    }
    chmod($path, 0700);
    return $path;
}

function demo_available(): bool { return in_array('sqlite', PDO::getAvailableDrivers(), true); }

function demo_connection(bool $create = false): PDO
{
    $path = demo_directory() . '/demo.sqlite';
    if (!$create && !is_file($path)) { throw new DemoActionError('Kết nối database demo đang bị ngắt.'); }
    if (is_link($path)) { throw new RuntimeException('Invalid demo database.'); }
    $pdo = new PDO('sqlite:' . $path, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    chmod($path, 0600);
    $pdo->exec('PRAGMA busy_timeout=2000');
    return $pdo;
}

function demo_rows(PDO $pdo): array
{
    return backup_validate_rows($pdo->query('SELECT id,name,category,price,description,is_available,created_at,updated_at FROM demo_dishes ORDER BY id LIMIT 2')->fetchAll());
}

function demo_event(array &$state, string $event, array $fields = []): void
{
    $entry = ['time' => ops_time(), 'event' => $event] + $fields;
    $state['events'][] = $entry;
    $state['events'] = array_slice($state['events'], -60);
    ops_event($event, $fields);
}

function demo_archive(array $state): array
{
    $path = demo_directory() . '/latest-backup.json';
    if (!is_file($path) || is_link($path) || filesize($path) > 65536) {
        throw new DemoActionError('Hãy tạo backup trước.');
    }
    $archive = json_decode((string) file_get_contents($path), true, 32, JSON_THROW_ON_ERROR);
    if (($archive['format'] ?? '') !== 'devopqlyfood-isolated-demo-v1'
        || !is_string($archive['demo_id'] ?? null)
        || !hash_equals((string) ($state['demo_id'] ?? ''), $archive['demo_id'])
        || !isset($archive['rows']) || !is_array($archive['rows']) || count($archive['rows']) !== 1) {
        throw new DemoActionError('Bản backup không thuộc lượt demo hiện tại.');
    }
    $rows = backup_validate_rows($archive['rows']);
    if (!is_string($archive['sha256'] ?? null) || !hash_equals(backup_digest($rows), $archive['sha256'])) {
        throw new DemoActionError('Kiểm tra toàn vẹn backup không đạt.');
    }
    $archive['rows'] = $rows;
    return $archive;
}

function demo_reconnect(array &$state, bool $automatic = false): void
{
    $dir = demo_directory();
    if (is_file($dir . '/demo.sqlite.offline')) {
        if (is_file($dir . '/demo.sqlite') || !rename($dir . '/demo.sqlite.offline', $dir . '/demo.sqlite')) {
            throw new RuntimeException('Demo reconnect failed.');
        }
        demo_event($state, $automatic ? 'demo_auto_reconnected' : 'demo_database_reconnected');
    }
    $state['offline_until'] = 0;
    $state['sampled_at'] = 0;
}

function demo_action(array &$state, string $action): void
{
    $dir = demo_directory();
    if (!demo_available()) { throw new DemoActionError('Hosting cần bật extension pdo_sqlite để dùng trang demo.'); }
    if ($action === 'reconnect') { demo_reconnect($state); return; }
    if (is_file($dir . '/demo.sqlite.offline')) { throw new DemoActionError('Bấm Phục hồi kết nối trước khi thao tác dữ liệu.'); }
    if ($action === 'prepare') {
        $pdo = demo_connection(true);
        $pdo->exec('CREATE TABLE IF NOT EXISTS demo_dishes (id INTEGER PRIMARY KEY, name TEXT NOT NULL, category TEXT NOT NULL, price TEXT NOT NULL, description TEXT NOT NULL, is_available INTEGER NOT NULL, created_at TEXT NOT NULL, updated_at TEXT NOT NULL)');
        $pdo->beginTransaction();
        try {
            $pdo->exec('DELETE FROM demo_dishes');
            $stamp = gmdate('Y-m-d H:i:s');
            $q = $pdo->prepare('INSERT INTO demo_dishes VALUES (1,?,?,?,?,?,?,?)');
            $q->execute(['Cơm gà demo Week 3', 'main', '45000', 'Dữ liệu thử riêng trên hosting', 1, $stamp, $stamp]);
            $pdo->commit();
        } catch (Throwable $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } throw $e; }
        $state = ['demo_id' => bin2hex(random_bytes(16)), 'initialized' => true, 'events' => [], 'history' => []];
        demo_event($state, 'demo_data_created', ['row_count' => 1]);
        return;
    }
    if (empty($state['initialized'])) { throw new DemoActionError('Bấm Tạo dữ liệu thử trước.'); }
    if ($action === 'disconnect') {
        if (!is_file($dir . '/demo.sqlite') || !rename($dir . '/demo.sqlite', $dir . '/demo.sqlite.offline')) {
            throw new RuntimeException('Demo disconnect failed.');
        }
        $state['offline_until'] = time() + 180;
        $state['sampled_at'] = 0;
        demo_event($state, 'demo_database_disconnected');
        return;
    }
    $pdo = demo_connection();
    if ($action === 'backup') {
        $rows = demo_rows($pdo);
        if (count($rows) !== 1) { throw new DemoActionError('Tạo lại dữ liệu thử trước khi backup.'); }
        $archive = ['format' => 'devopqlyfood-isolated-demo-v1', 'demo_id' => $state['demo_id'],
            'created_at' => ops_time(), 'sha256' => backup_digest($rows), 'rows' => $rows];
        ops_write_json_atomic($dir . '/latest-backup.json', $archive);
        $state['backup'] = ['created_at' => $archive['created_at'], 'row_count' => 1, 'sha256' => $archive['sha256']];
        $state['restored'] = false;
        demo_event($state, 'demo_backup_created', ['row_count' => 1, 'sha256' => $archive['sha256']]);
        return;
    }
    $archive = demo_archive($state);
    if ($action === 'delete') {
        if (!hash_equals($archive['sha256'], backup_digest(demo_rows($pdo)))) {
            throw new DemoActionError('Dữ liệu chưa khớp backup của lượt demo này.');
        }
        $pdo->exec('DELETE FROM demo_dishes');
        $state['restored'] = false;
        demo_event($state, 'demo_data_deleted', ['row_count' => 0]);
        return;
    }
    if ($action === 'restore') {
        $pdo->beginTransaction();
        try {
            $pdo->exec('DELETE FROM demo_dishes');
            $q = $pdo->prepare('INSERT INTO demo_dishes (id,name,category,price,description,is_available,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)');
            foreach ($archive['rows'] as $row) { $q->execute(array_values($row)); }
            if (!hash_equals($archive['sha256'], backup_digest(demo_rows($pdo)))) { throw new RuntimeException('Demo restore verification failed.'); }
            $pdo->commit();
        } catch (Throwable $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } throw $e; }
        $state['restored'] = true;
        demo_event($state, 'demo_restore_verified', ['row_count' => 1, 'sha256' => $archive['sha256']]);
        return;
    }
    throw new DemoActionError('Thao tác demo không hợp lệ.');
}

function demo_probe(): array
{
    $start = microtime(true);
    try {
        $pdo = demo_connection();
        $rows = demo_rows($pdo);
        return ['health' => 'ok', 'rows' => $rows, 'db_ms' => round((microtime(true) - $start) * 1000, 2)];
    } catch (Throwable) {
        return ['health' => 'unavailable', 'rows' => null, 'db_ms' => round((microtime(true) - $start) * 1000, 2)];
    }
}

function demo_state(?string $action = null): array
{
    $dir = demo_directory();
    $guard = fopen($dir . '/demo.lock', 'c');
    if (!$guard || !flock($guard, LOCK_EX)) { throw new RuntimeException('Demo lock unavailable.'); }
    try {
        $raw = ops_read($dir . '/state.json');
        $state = $raw === null ? [] : json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        if (is_file($dir . '/demo.sqlite.offline') && time() >= ($state['offline_until'] ?? PHP_INT_MAX)) {
            demo_reconnect($state, true);
        }
        if ($action !== null && $action !== 'download') { demo_action($state, $action); }
        $probe = !empty($state['initialized']) ? demo_probe() : ['health' => 'not_ready', 'rows' => [], 'db_ms' => 0];
        if (!empty($state['initialized']) && microtime(true) - ($state['sampled_at'] ?? 0) >= 5) {
            $previous = $state['alert']['state'] ?? 'ok';
            $state['alert'] = ops_update_alert($state['alert'] ?? [], $probe['health'] !== 'ok', true, 'demo_database_unavailable');
            if ($previous !== $state['alert']['state']) {
                demo_event($state, 'demo_alert_transition', ['alert' => 'demo_database_unavailable', 'state' => $state['alert']['state']]);
            }
            $state['sampled_at'] = microtime(true);
            $state['history'][] = ['time' => ops_time(), 'health' => $probe['health'], 'state' => $state['alert']['state']];
            $state['history'] = array_slice($state['history'], -60);
        }
        if ($action !== null || !empty($state['initialized'])) { ops_write_json_atomic($dir . '/state.json', $state); }
        if ($action === 'download') { return demo_archive($state); }
        unset($state['demo_id'], $state['sampled_at']);
        return $state + ['initialized' => false, 'driver_available' => demo_available(), 'scope' => 'SQLite demo riêng trên cPanel',
            'probe' => $probe, 'alert' => ['state' => 'idle'], 'events' => [], 'history' => []];
    } finally { flock($guard, LOCK_UN); fclose($guard); }
}
