<?php
define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/Helpers/helpers.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = getDatabaseConnection();

    // 1. Invoices (Hoá đơn nạp tiền)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `invoices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `trans_id` VARCHAR(50) NOT NULL UNIQUE,
            `amount` DECIMAL(15,2) NOT NULL,
            `pay` DECIMAL(15,2) NOT NULL,
            `method` VARCHAR(50) DEFAULT 'MBBank',
            `status` TINYINT(1) DEFAULT 0,
            `description` TEXT,
            `tid` VARCHAR(100) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Settings (Cấu hình)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `value` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
        ('bank_prefix', 'NAP'),
        ('bank_acc_name', 'NGUYEN VAN A'),
        ('bank_acc_number', '0123456789'),
        ('bank_name', 'MBBank'),
        ('site_notice', 'Chào mừng đến với ShopAcc VN!')
    ");

    // 3. Lucky Wheel Prizes
    $db->exec("
        CREATE TABLE IF NOT EXISTS `lucky_wheel_prizes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `type` ENUM('money','product','nothing') DEFAULT 'money',
            `value` DECIMAL(15,2) DEFAULT 0,
            `probability` INT DEFAULT 10,
            `color` VARCHAR(20) DEFAULT '#6c63ff',
            `status` TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        INSERT IGNORE INTO `lucky_wheel_prizes` (`name`, `type`, `value`, `probability`, `color`, `status`) VALUES
        ('10,000đ', 'money', 10000, 20, '#6c63ff', 1),
        ('Chúc may mắn', 'nothing', 0, 40, '#e94560', 1),
        ('50,000đ', 'money', 50000, 5, '#00d4aa', 1),
        ('Acc Random', 'product', 0, 10, '#ffa726', 1),
        ('5,000đ', 'money', 5000, 25, '#29b6f6', 1)
    ");

    // 4. Lucky Wheel History
    $db->exec("
        CREATE TABLE IF NOT EXISTS `lucky_wheel_history` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `prize_id` INT NOT NULL,
            `prize_name` VARCHAR(200),
            `prize_value` DECIMAL(15,2),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 5. Mystery Bags
    $db->exec("
        CREATE TABLE IF NOT EXISTS `mystery_bags` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `price` DECIMAL(15,2) NOT NULL,
            `image` VARCHAR(255),
            `status` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        INSERT IGNORE INTO `mystery_bags` (`name`, `description`, `price`, `status`) VALUES
        ('Túi mù cơ bản', 'Mở ra cơ hội nhận tài khoản trị giá lên tới 50K', 20000, 1),
        ('Túi mù VIP', 'Cơ hội cực cao nhận tài khoản VIP hoặc siêu phẩm', 100000, 1)
    ");

    // 6. Mystery Bag Items
    $db->exec("
        CREATE TABLE IF NOT EXISTS `mystery_bag_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `bag_id` INT NOT NULL,
            `name` VARCHAR(200) NOT NULL,
            `value` DECIMAL(15,2) NOT NULL,
            `content` TEXT,
            `probability` INT DEFAULT 10,
            FOREIGN KEY (`bag_id`) REFERENCES `mystery_bags`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        INSERT IGNORE INTO `mystery_bag_items` (`bag_id`, `name`, `value`, `content`, `probability`) VALUES
        (1, 'Acc Rác', 5000, 'Tài khoản chưa có thông tin', 60),
        (1, 'Acc Tầm Trung', 30000, 'Tài khoản có sẵn 10 tướng', 30),
        (1, 'Acc Ngon', 50000, 'Tài khoản full ngọc', 10),
        (2, 'Acc Tầm Trung', 30000, 'Tài khoản có sẵn 10 tướng', 50),
        (2, 'Acc VIP', 150000, 'Tài khoản rank cao thủ, nhiều skin', 40),
        (2, 'Acc Siêu Phẩm VIP', 500000, 'Tài khoản skin SS cực hiếm', 10)
    ");

    // 7. Mystery Bag History
    $db->exec("
        CREATE TABLE IF NOT EXISTS `mystery_bag_history` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `bag_id` INT NOT NULL,
            `item_id` INT NOT NULL,
            `item_name` VARCHAR(200),
            `item_content` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 8. Contact Messages
    $db->exec("
        CREATE TABLE IF NOT EXISTS `contact_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT DEFAULT NULL,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(200),
            `message` TEXT NOT NULL,
            `status` ENUM('new','read','replied') DEFAULT 'new',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Phần mở rộng Database hoàn tất!";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
