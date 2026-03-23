<?php
/**
 * 🔧 Debug & Diagnostic Script cho cPanel
 * 
 * Upload file này lên public_html/debug.php
 * Truy cập: https://metaultra.shop/debug.php
 * ⚠️ XÓA FILE NÀY SAU KHI DEBUG XONG!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Debug Tool</title>';
echo '<style>body{font-family:monospace;background:#1a1a2e;color:#eee;padding:30px}';
echo '.ok{color:#00d4aa;font-weight:bold}.fail{color:#e94560;font-weight:bold}';
echo '.section{background:#16213e;padding:15px;margin:10px 0;border-radius:8px}';
echo 'h1{color:#6c63ff}h2{color:#ffa726;margin:0 0 10px}</style></head><body>';
echo '<h1>🔧 ShopAcc VN - Diagnostic Tool</h1>';

$passed = 0;
$failed = 0;

function check($label, $ok, $detail = '') {
    global $passed, $failed;
    if ($ok) { $passed++; $icon = '✅'; $class = 'ok'; }
    else { $failed++; $icon = '❌'; $class = 'fail'; }
    echo "<p>{$icon} <span class='{$class}'>{$label}</span>";
    if ($detail) echo " — <small>{$detail}</small>";
    echo "</p>";
}

// === 1. PHP Info ===
echo '<div class="section"><h2>1. PHP Environment</h2>';
check('PHP Version >= 7.4', version_compare(PHP_VERSION, '7.4.0', '>='), 'Current: ' . PHP_VERSION);
check('PDO extension', extension_loaded('pdo'));
check('PDO MySQL extension', extension_loaded('pdo_mysql'));
check('cURL extension', extension_loaded('curl'));
check('mbstring extension', extension_loaded('mbstring'));
check('JSON extension', extension_loaded('json'));
echo '</div>';

// === 2. File System ===
echo '<div class="section"><h2>2. File System</h2>';
$basePath = dirname(__DIR__);
check('BASE_PATH exists', is_dir($basePath), $basePath);
check('.env file exists', file_exists($basePath . '/.env'), $basePath . '/.env');
check('.env is readable', is_readable($basePath . '/.env'));
check('public/uploads/ exists', is_dir($basePath . '/public/uploads/'));
check('public/uploads/ is writable', is_writable($basePath . '/public/uploads/'), 'Cần chmod 775');
check('views/ directory exists', is_dir($basePath . '/views/'));
check('app/ directory exists', is_dir($basePath . '/app/'));
check('core/ directory exists', is_dir($basePath . '/core/'));
check('config/ directory exists', is_dir($basePath . '/config/'));
echo '</div>';

// === 3. .env Content ===
echo '<div class="section"><h2>3. .env Configuration</h2>';
if (file_exists($basePath . '/.env')) {
    $lines = file($basePath . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Mask sensitive values
        $masked = $value;
        if (in_array($key, ['DB_PASSWORD', 'BANK_API_TOKEN', 'SMM_API_KEY', 'TELEGRAM_BOT_TOKEN'])) {
            $masked = $value ? str_repeat('*', min(strlen($value), 8)) : '(trống)';
        }
        $isOk = !empty($value) || in_array($key, ['TELEGRAM_BOT_TOKEN', 'TELEGRAM_CHAT_ID', 'DB_PASSWORD']);
        check("{$key} = {$masked}", $isOk);
    }
} else {
    check('.env file missing!', false, 'Tạo file .env với cấu hình DB đúng');
}
echo '</div>';

// === 4. Database Connection ===
echo '<div class="section"><h2>4. Database Connection</h2>';
// Load env manually
define('BASE_PATH', $basePath);
require_once $basePath . '/app/Helpers/helpers.php';

$dbHost = env('DB_HOST', 'localhost');
$dbPort = env('DB_PORT', '3306');
$dbName = env('DB_DATABASE', '');
$dbUser = env('DB_USERNAME', '');
$dbPass = env('DB_PASSWORD', '');

check("DB_HOST = {$dbHost}", !empty($dbHost));
check("DB_DATABASE = {$dbName}", !empty($dbName));
check("DB_USERNAME = {$dbUser}", !empty($dbUser));

$dbOk = false;
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $dbOk = true;
    check('Database connection', true, 'Kết nối thành công!');
} catch (PDOException $e) {
    check('Database connection', false, $e->getMessage());
}
echo '</div>';

// === 5. Database Tables ===
if ($dbOk) {
    echo '<div class="section"><h2>5. Database Tables</h2>';
    $requiredTables = [
        'users', 'categories', 'products', 'services', 'orders',
        'transactions', 'invoices', 'settings',
        'lucky_wheel_prizes', 'lucky_wheel_history',
        'mystery_bags', 'mystery_bag_items', 'mystery_bag_history',
        'contact_messages'
    ];

    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingTables = [];
    foreach ($requiredTables as $table) {
        $exists = in_array($table, $existingTables);
        check("Table: {$table}", $exists, $exists ? 'OK' : '⚠ THIẾU - cần chạy migration');
        if (!$exists) $missingTables[] = $table;
    }

    if (!empty($missingTables)) {
        echo '<p class="fail">👉 Chạy migration: <code>php run_migration.php</code> hoặc truy cập URL migration</p>';
    }
    echo '</div>';
}

// === 6. Session ===
echo '<div class="section"><h2>6. Session</h2>';
$sessionPath = session_save_path();
check('Session save path', !empty($sessionPath), $sessionPath ?: '(default)');
if (!empty($sessionPath)) {
    check('Session path is writable', is_writable($sessionPath));
}
@session_start();
$_SESSION['debug_test'] = 'ok';
check('Session write test', isset($_SESSION['debug_test']) && $_SESSION['debug_test'] === 'ok');
echo '</div>';

// === Summary ===
echo '<div class="section"><h2>📊 Summary</h2>';
echo "<p class='ok'>✅ Passed: {$passed}</p>";
echo "<p class='fail'>❌ Failed: {$failed}</p>";
if ($failed === 0) {
    echo '<p style="color:#00d4aa;font-size:18px">🎉 Tất cả kiểm tra đều OK! Nếu vẫn lỗi 500, bật APP_DEBUG=true trong .env và check lại error log.</p>';
} else {
    echo '<p style="color:#e94560;font-size:18px">⚠ Có ' . $failed . ' vấn đề cần khắc phục. Sửa theo hướng dẫn bên trên.</p>';
}
echo '</div>';

echo '<p style="margin-top:30px;color:#666">⚠️ <strong>XÓA FILE NÀY SAU KHI DEBUG XONG!</strong></p>';
echo '</body></html>';
