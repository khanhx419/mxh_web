<?php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

$isWeb = php_sapi_name() !== 'cli';
if ($isWeb)
    echo '<pre style="font-family:monospace;background:#1a1a2e;color:#00d4aa;padding:20px;">';

function msg($text)
{
    echo $text . "\n";
}

try {
    $db = getDatabaseConnection();
    msg("✅ Kết nối database thành công!\n");
    msg("=== PHẦN 1: Bảng nền tảng ===");
    $db->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
            `balance` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: users");

    // 2. Categories
    $db->exec("
        CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `type` ENUM('game', 'social') NOT NULL DEFAULT 'game',
            `icon` VARCHAR(50) DEFAULT 'fa-folder',
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: categories");

    // 3. Products
    $db->exec("
        CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `account_info` TEXT COMMENT 'Thông tin tài khoản game',
            `image` VARCHAR(255) DEFAULT NULL,
            `status` ENUM('available', 'sold') NOT NULL DEFAULT 'available',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: products");

    // 4. Services
    $db->exec("
        CREATE TABLE IF NOT EXISTS `services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `name` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `price_per_1000` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `min_quantity` INT NOT NULL DEFAULT 100,
            `max_quantity` INT NOT NULL DEFAULT 100000,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: services");

    // 5. Orders
    $db->exec("
        CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `order_type` ENUM('product', 'service') NOT NULL,
            `product_id` INT DEFAULT NULL,
            `service_id` INT DEFAULT NULL,
            `quantity` INT DEFAULT 1,
            `target_link` VARCHAR(500) DEFAULT NULL COMMENT 'Link mục tiêu cho dịch vụ MXH',
            `total_price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `status` ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
            `account_data` TEXT DEFAULT NULL COMMENT 'Thông tin acc game sau khi mua',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
            FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: orders");

    // 6. Transactions
    $db->exec("
        CREATE TABLE IF NOT EXISTS `transactions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `type` ENUM('deposit', 'purchase', 'refund') NOT NULL,
            `amount` DECIMAL(15, 2) NOT NULL,
            `balance_after` DECIMAL(15, 2) NOT NULL,
            `description` VARCHAR(500) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: transactions");

    msg("\n=== PHẦN 2: Bảng mở rộng ===");

    // 7. Invoices
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
    msg("✅ Table: invoices");

    // 8. Settings
    $db->exec("
        CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `value` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: settings");

    // 9. Lucky Wheel Prizes
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
    msg("✅ Table: lucky_wheel_prizes");

    // 10. Lucky Wheel History
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
    msg("✅ Table: lucky_wheel_history");

    // 11. Mystery Bags
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
    msg("✅ Table: mystery_bags");

    // 12. Mystery Bag Items
    $db->exec("
        CREATE TABLE IF NOT EXISTS `mystery_bag_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `bag_id` INT NOT NULL,
            `name` VARCHAR(200) NOT NULL,
            `value` DECIMAL(15,2) NOT NULL,
            `content` TEXT,
            `probability` INT DEFAULT 10,
            `status` TINYINT(1) DEFAULT 1,
            FOREIGN KEY (`bag_id`) REFERENCES `mystery_bags`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: mystery_bag_items");

    // Thêm cột status vào mystery_bag_items (nếu chưa có)
    $cols = $db->query("SHOW COLUMNS FROM `mystery_bag_items` LIKE 'status'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `mystery_bag_items` ADD COLUMN `status` TINYINT(1) DEFAULT 1 AFTER `probability`");
        msg("✅ Thêm cột status vào mystery_bag_items");
    } else {
        msg("⏭️ Cột status (mystery_bag_items) đã tồn tại");
    }

    // 13. Mystery Bag History
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
    msg("✅ Table: mystery_bag_history");

    // 13b. Daily Check-in (Điểm danh nhận lượt quay miễn phí)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `daily_checkin` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `day_number` TINYINT NOT NULL COMMENT 'Ngày thứ mấy trong chu kỳ 7 ngày (1-7)',
            `cycle_start` DATE NOT NULL COMMENT 'Ngày bắt đầu chu kỳ 7 ngày',
            `free_spins_earned` INT NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: daily_checkin");

    // 13c. User Free Spins (Lượt quay miễn phí)
    $db->exec("
        CREATE TABLE IF NOT EXISTS `user_free_spins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL UNIQUE,
            `free_spins` INT NOT NULL DEFAULT 0,
            `last_checkin_date` DATE DEFAULT NULL,
            `cycle_start` DATE DEFAULT NULL,
            `current_day` TINYINT DEFAULT 0,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    msg("✅ Table: user_free_spins");

    // 14. Contact Messages
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
    msg("✅ Table: contact_messages");
    msg("\n=== PHẦN 3: Events & Green Points ===");

    // 15. Events
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
    msg("✅ Table: events");

    // 16. Green Points
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
    msg("✅ Table: green_points");

    // Thêm cột green_points_total vào users (nếu chưa có)
    $cols = $db->query("SHOW COLUMNS FROM `users` LIKE 'green_points_total'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `green_points_total` INT DEFAULT 0 AFTER `balance`");
        msg("✅ Thêm cột green_points_total vào users");
    }
    else {
        msg("⏭️ Cột green_points_total đã tồn tại");
    }

    // Thêm cột chess_score vào users (nếu chưa có)
    $cols = $db->query("SHOW COLUMNS FROM `users` LIKE 'chess_score'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `chess_score` INT DEFAULT 0 AFTER `green_points_total`");
        msg("✅ Thêm cột chess_score vào users");
    }
    else {
        msg("⏭️ Cột chess_score đã tồn tại");
    }

    // Thêm cột SMM vào services (nếu chưa có)
    $smmServiceCols = [
        'smm_service_id' => "INT DEFAULT NULL COMMENT 'ID dịch vụ trên web mẹ' AFTER `category_id`",
        'smm_type' => "VARCHAR(50) DEFAULT NULL COMMENT 'Loại DV trên web mẹ' AFTER `smm_service_id`",
        'refill' => "TINYINT(1) DEFAULT 0 COMMENT 'Có hỗ trợ refill' AFTER `max_quantity`",
        'rate_original' => "DECIMAL(15,4) DEFAULT 0 COMMENT 'Giá gốc từ web mẹ (per 1000)' AFTER `price_per_1000`",
    ];
    foreach ($smmServiceCols as $col => $def) {
        $check = $db->query("SHOW COLUMNS FROM `services` LIKE '{$col}'")->fetchAll();
        if (empty($check)) {
            $db->exec("ALTER TABLE `services` ADD COLUMN `{$col}` {$def}");
            msg("✅ Thêm cột {$col} vào services");
        } else {
            msg("⏭️ Cột {$col} (services) đã tồn tại");
        }
    }

    // Thêm cột SMM vào orders (nếu chưa có)
    $smmOrderCols = [
        'smm_order_id' => "INT DEFAULT NULL COMMENT 'Mã đơn trên web mẹ' AFTER `status`",
        'smm_status' => "VARCHAR(50) DEFAULT NULL COMMENT 'Trạng thái trên web mẹ' AFTER `smm_order_id`",
        'start_count' => "INT DEFAULT NULL COMMENT 'Số đếm bắt đầu' AFTER `smm_status`",
        'remains' => "INT DEFAULT NULL COMMENT 'Số lượng còn lại' AFTER `start_count`",
    ];
    foreach ($smmOrderCols as $col => $def) {
        $check = $db->query("SHOW COLUMNS FROM `orders` LIKE '{$col}'")->fetchAll();
        if (empty($check)) {
            $db->exec("ALTER TABLE `orders` ADD COLUMN `{$col}` {$def}");
            msg("✅ Thêm cột {$col} vào orders");
        } else {
            msg("⏭️ Cột {$col} (orders) đã tồn tại");
        }
    }

    msg("\n=== PHẦN 4: Dữ liệu mẫu ===");

    // Settings mặc định
    $db->exec("
        INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
        ('bank_prefix', 'NAP'),
        ('bank_acc_name', 'NGUYEN VAN A'),
        ('bank_acc_number', '0123456789'),
        ('bank_name', 'MBBank'),
        ('site_notice', 'Chào mừng đến với ShopAcc VN!'),
        ('wheel_spin_cost', '10000')
    ");
    msg("✅ Seed: settings");

    // Lucky Wheel Prizes
    $db->exec("
        INSERT IGNORE INTO `lucky_wheel_prizes` (`name`, `type`, `value`, `probability`, `color`, `status`) VALUES
        ('10,000đ', 'money', 10000, 20, '#6c63ff', 1),
        ('Chúc may mắn', 'nothing', 0, 40, '#e94560', 1),
        ('50,000đ', 'money', 50000, 5, '#00d4aa', 1),
        ('Acc Random', 'product', 0, 10, '#ffa726', 1),
        ('5,000đ', 'money', 5000, 25, '#29b6f6', 1)
    ");
    msg("✅ Seed: lucky_wheel_prizes");

    // Mystery Bags
    $db->exec("
        INSERT IGNORE INTO `mystery_bags` (`name`, `description`, `price`, `status`) VALUES
        ('Túi mù cơ bản', 'Mở ra cơ hội nhận tài khoản trị giá lên tới 50K', 20000, 1),
        ('Túi mù VIP', 'Cơ hội cực cao nhận tài khoản VIP hoặc siêu phẩm', 100000, 1)
    ");
    msg("✅ Seed: mystery_bags");

    // Mystery Bag Items
    $db->exec("
        INSERT IGNORE INTO `mystery_bag_items` (`bag_id`, `name`, `value`, `content`, `probability`) VALUES
        (1, 'Acc Rác', 5000, 'Tài khoản chưa có thông tin', 60),
        (1, 'Acc Tầm Trung', 30000, 'Tài khoản có sẵn 10 tướng', 30),
        (1, 'Acc Ngon', 50000, 'Tài khoản full ngọc', 10),
        (2, 'Acc Tầm Trung', 30000, 'Tài khoản có sẵn 10 tướng', 50),
        (2, 'Acc VIP', 150000, 'Tài khoản rank cao thủ, nhiều skin', 40),
        (2, 'Acc Siêu Phẩm VIP', 500000, 'Tài khoản skin SS cực hiếm', 10)
    ");
    msg("✅ Seed: mystery_bag_items");

    // Events mẫu
    $db->exec("
        INSERT IGNORE INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `reward_type`, `reward_value`, `status`) VALUES
        (1, 'Nạp lần đầu x2', 'Nhận gấp đôi số tiền khi nạp lần đầu tiên!', '2026-03-01 00:00:00', '2026-04-30 23:59:59', 'money', 2, 1),
        (2, 'Top nạp tháng 3', 'Top 3 nạp nhiều nhất tháng 3 nhận thưởng đặc biệt', '2026-03-01 00:00:00', '2026-03-31 23:59:59', 'money', 500000, 1),
        (3, 'Tích điểm xanh x3', 'Tất cả giao dịch trong sự kiện nhận x3 điểm xanh', '2026-03-15 00:00:00', '2026-03-30 23:59:59', 'points', 3, 1)
    ");
    msg("✅ Seed: events");
    msg("\n🎉 ====================================");
    msg("🎉 MIGRATION HOÀN TẤT - 18 bảng + seed data");
    msg("🎉 ====================================");
    msg("\n⚠️ Nhớ XÓA FILE NÀY sau khi chạy xong!");

}
catch (PDOException $e) {
    msg("\n❌ Database Error: " . $e->getMessage());
}

if ($isWeb)
    echo '</pre>';
