<?php

/**
 * Helper Functions
 */

/**
 * Đọc biến môi trường từ file .env
 */
function env($key, $default = null)
{
    static $env = null;

    if ($env === null) {
        $envFile = BASE_PATH . '/.env';
        if (!file_exists($envFile)) {
            return $default;
        }

        $env = [];
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0)
                continue;
            if (strpos($line, '=') === false)
                continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Parse booleans
            if (strtolower($value) === 'true')
                $value = true;
            elseif (strtolower($value) === 'false')
                $value = false;
            elseif (strtolower($value) === 'null')
                $value = null;

            $env[$name] = $value;
        }
    }

    return $env[$key] ?? $default;
}

/**
 * Redirect helper
 */
function redirect($url)
{
    header("Location: " . APP_URL . $url);
    exit;
}

/**
 * Lấy URL đầy đủ
 */
function url($path = '')
{
    return APP_URL . $path;
}

/**
 * Lấy URL cho assets
 */
function asset($path)
{
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Escape output (chống XSS)
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message helper
 */
function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Kiểm tra user đã đăng nhập
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra user là admin
 */
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Lấy thông tin user hiện tại
 */
function currentUser()
{
    if (!isLoggedIn())
        return null;
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['user_role'],
        'balance' => $_SESSION['user_balance'] ?? 0
    ];
}

/**
 * Format tiền VND
 */
function formatMoney($amount)
{
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Format ngày giờ
 */
function formatDate($date)
{
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Generate CSRF token
 */
function csrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf()
{
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    $valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    // Regenerate token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $valid;
}

/**
 * Lấy trạng thái order hiển thị
 */
function orderStatusLabel($status)
{
    $labels = [
        'pending' => '<span class="badge badge-warning">Chờ xử lý</span>',
        'processing' => '<span class="badge badge-info">Đang xử lý</span>',
        'completed' => '<span class="badge badge-success">Hoàn thành</span>',
        'cancelled' => '<span class="badge badge-danger">Đã hủy</span>',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Lấy setting từ database nhanh (dùng trong layout)
 */
function app_setting($key, $default = null)
{
    static $settings = null;

    if ($settings === null) {
        $settings = [];
        try {
            $db = getDatabaseConnection();
            $stmt = $db->query("SELECT name, value FROM settings");
            if ($stmt) {
                while ($row = $stmt->fetch()) {
                    $settings[$row['name']] = $row['value'];
                }
            }
        } catch (Exception $e) {
            // Ignore
        }
    }

    return $settings[$key] ?? $default;
}
