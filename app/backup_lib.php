<?php
declare(strict_types=1);
require_once __DIR__ . '/operations.php';

function backup_connection(): array
{
    $config = require dirname(__DIR__) . '/config/config.local.php';
    $d = $config['db'];
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $d['host'], (int) $d['port'], $d['name']),
        $d['user'], $d['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_TIMEOUT => 5]);
    return [$pdo, (string) $d['name']];
}

function backup_directory(): string
{
    $configured = getenv('MENU_BACKUP_DIR');
    $path = $configured !== false && $configured !== '' ? $configured : dirname(ops_directory()) . '/backups';
    if (!str_starts_with($path, '/') || is_link($path)) { throw new RuntimeException('An absolute non-symlink backup directory is required.'); }
    if (!is_dir($path) && !mkdir($path, 0700, true) && !is_dir($path)) { throw new RuntimeException('Backup directory unavailable.'); }
    chmod($path, 0700);
    if (!is_file($path . '/.htaccess')) { file_put_contents($path . '/.htaccess', "Require all denied\n", LOCK_EX); }
    return $path;
}

function backup_rows(PDO $pdo): array
{
    $rows = $pdo->query('SELECT id,name,category,price,description,is_available,created_at,updated_at FROM dishes ORDER BY id LIMIT 20001')->fetchAll();
    if (count($rows) > 20000) { throw new RuntimeException('Use a database-native backup for more than 20000 dishes.'); }
    return backup_validate_rows($rows);
}

function backup_validate_rows(array $rows): array
{
    $out = []; $ids = [];
    foreach ($rows as $row) {
        if (!is_array($row) || array_keys($row) !== ['id','name','category','price','description','is_available','created_at','updated_at']) {
            throw new RuntimeException('Invalid backup row schema.');
        }
        $id = filter_var($row['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id || isset($ids[$id]) || !is_string($row['name']) || mb_strlen($row['name']) < 1 || mb_strlen($row['name']) > 160
            || !in_array($row['category'], ['main','starter','drink','dessert'], true)
            || !preg_match('/\A(?:0|[1-9][0-9]{0,8})\z/', (string) $row['price'])
            || !is_string($row['description']) || mb_strlen($row['description']) > 1000
            || !in_array((string) $row['is_available'], ['0','1'], true)) {
            throw new RuntimeException('Invalid backup row values.');
        }
        foreach (['created_at','updated_at'] as $key) {
            if (!is_string($row[$key]) || !preg_match('/\A\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\z/', $row[$key])) { throw new RuntimeException('Invalid timestamp.'); }
        }
        $ids[$id] = true; $row['id'] = $id; $row['price'] = (string) $row['price']; $row['is_available'] = (int) $row['is_available']; $out[] = $row;
    }
    usort($out, static fn(array $a, array $b): int => $a['id'] <=> $b['id']);
    return $out;
}

function backup_digest(array $rows): string
{
    return hash('sha256', json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
}

function backup_create(int $keep = 14): array
{
    if ($keep < 1 || $keep > 90) { throw new RuntimeException('Retention must be between 1 and 90 snapshots.'); }
    $directory = backup_directory();
    $guard = fopen($directory . '/backup.lock', 'c');
    if (!$guard || !flock($guard, LOCK_EX)) { throw new RuntimeException('Backup lock unavailable.'); }
    try {
        [$pdo, $database] = backup_connection();
        $pdo->beginTransaction();
        try { $rows = backup_rows($pdo); $pdo->commit(); }
        catch (Throwable $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } throw $e; }
        $digest = backup_digest($rows);
        $archive = ['format' => 'devopqlyfood-dishes-v1', 'created_at' => ops_time(), 'database' => $database,
            'row_count' => count($rows), 'sha256' => $digest, 'rows' => $rows];
        $name = 'menu-' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
        $temp = tempnam($directory, 'pending-');
        file_put_contents($temp, json_encode($archive, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)); chmod($temp, 0600);
        rename($temp, $directory . '/' . $name);
        $snapshots = glob($directory . '/menu-*.json') ?: []; rsort($snapshots, SORT_STRING);
        foreach (array_slice($snapshots, $keep) as $old) { if (!is_link($old)) { unlink($old); } }
        ops_event('backup_created', ['row_count' => count($rows), 'sha256' => $digest]);
        $summary = ['file' => $name, 'row_count' => count($rows), 'sha256' => $digest, 'created_at' => $archive['created_at']];
        file_put_contents(ops_directory() . '/backup-latest.json', json_encode($summary, JSON_THROW_ON_ERROR), LOCK_EX);
        @chmod(ops_directory() . '/backup-latest.json', 0600);
        return $summary;
    } finally { flock($guard, LOCK_UN); fclose($guard); }
}

function backup_restore(string $name, string $confirmation): array
{
    if (!preg_match('/\Amenu-\d{8}-\d{6}-[a-f0-9]{8}\.json\z/', $name)) { throw new RuntimeException('Select a snapshot filename from the private backup directory.'); }
    [$pdo, $database] = backup_connection();
    // Classroom recovery exercises must never delete a live/non-test database.
    if (!preg_match('/_(?:test|ci)\z/', $database) || !hash_equals($database, $confirmation)
        || $pdo->query('SELECT DATABASE()')->fetchColumn() !== $database) {
        throw new RuntimeException('Restore requires an isolated *_test or *_ci database and exact confirmation.');
    }
    $path = backup_directory() . '/' . $name;
    if (!is_file($path) || is_link($path) || filesize($path) > 33554432) { throw new RuntimeException('Invalid or oversized backup file.'); }
    $archive = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (($archive['format'] ?? '') !== 'devopqlyfood-dishes-v1' || !isset($archive['rows']) || !is_array($archive['rows']) || count($archive['rows']) > 20000) {
        throw new RuntimeException('Unsupported backup format.');
    }
    $rows = backup_validate_rows($archive['rows']); $digest = backup_digest($rows);
    if (count($rows) !== ($archive['row_count'] ?? -1) || !is_string($archive['sha256'] ?? null) || !hash_equals($digest, $archive['sha256'])) {
        throw new RuntimeException('Backup integrity check failed.');
    }
    $pdo->beginTransaction();
    try {
        $pdo->exec('DELETE FROM dishes');
        $query = $pdo->prepare('INSERT INTO dishes (id,name,category,price,description,is_available,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)');
        foreach ($rows as $row) { $query->execute(array_values($row)); }
        if (!hash_equals($digest, backup_digest(backup_rows($pdo)))) { throw new RuntimeException('Restored data does not match the snapshot.'); }
        $pdo->commit();
    } catch (Throwable $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } throw $e; }
    ops_event('restore_verified', ['row_count' => count($rows), 'sha256' => $digest]);
    return ['row_count' => count($rows), 'sha256' => $digest, 'verified' => true];
}
