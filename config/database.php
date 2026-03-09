<?php

/**
 * Database Configuration
 * Đọc thông tin kết nối từ file .env
 */

function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = env('DB_HOST', 'localhost');
        $port = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'shopacc_db');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (env('APP_DEBUG', false)) {
                die("Database Connection Error: " . $e->getMessage());
            }
            die("Database Connection Error. Please check your configuration.");
        }
    }

    return $pdo;
}
