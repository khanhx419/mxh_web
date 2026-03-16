<?php
/**
 * Migration v2 - Events, Green Points tables
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDatabaseConnection();

    // 1. Events table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `events` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `image` VARCHAR(255) DEFAULT NULL,
            `start_date` DATETIME NOT NULL,
            `end_date` DATETIME NOT NULL,
            `reward_type` ENUM('money','points','discount','item') DEFAULT 'points',
            `reward_value` DECIMAL(15,2) DEFAULT 0,
            `status` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Green Points table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `green_points` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `points` INT NOT NULL DEFAULT 0,
            `reason` VARCHAR(200) NOT NULL,
            `reference_type` VARCHAR(50) DEFAULT NULL,
            `reference_id` INT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Add green_points_total column to users (if not exists)
    $cols = $db->query("SHOW COLUMNS FROM `users` LIKE 'green_points_total'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `green_points_total` INT DEFAULT 0 AFTER `balance`");
    }

    // 4. Seed sample events
    $db->exec("
        INSERT IGNORE INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `reward_type`, `reward_value`, `status`) VALUES
        (1, 'Nạp lần đầu x2', 'Nhận gấp đôi số tiền khi nạp lần đầu tiên!', '2026-03-01 00:00:00', '2026-04-30 23:59:59', 'money', 2, 1),
        (2, 'Top nạp tháng 3', 'Top 3 nạp nhiều nhất tháng 3 nhận thưởng đặc biệt', '2026-03-01 00:00:00', '2026-03-31 23:59:59', 'money', 500000, 1),
        (3, 'Tích điểm xanh x3', 'Tất cả giao dịch trong sự kiện nhận x3 điểm xanh', '2026-03-15 00:00:00', '2026-03-30 23:59:59', 'points', 3, 1)
    ");

    echo "Migration v2 completed successfully!\n";
    echo "- Created `events` table\n";
    echo "- Created `green_points` table\n";
    echo "- Added `green_points_total` to users\n";
    echo "- Seeded sample events\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
