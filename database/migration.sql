-- =============================================
-- ShopAcc VN - Database Migration
-- =============================================

CREATE DATABASE IF NOT EXISTS `shopacc_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `shopacc_db`;

-- =============================================
-- Bảng Users
-- =============================================
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

-- =============================================
-- Bảng Categories
-- =============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('game', 'social') NOT NULL DEFAULT 'game',
    `icon` VARCHAR(50) DEFAULT 'fa-folder',
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng Products (Tài khoản game)
-- =============================================
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

-- =============================================
-- Bảng Services (Dịch vụ MXH)
-- =============================================
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

-- =============================================
-- Bảng Orders
-- =============================================
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

-- =============================================
-- Bảng Transactions (Lịch sử giao dịch)
-- =============================================
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

-- =============================================
-- Seed Data
-- =============================================

-- Admin mặc định (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `balance`) VALUES
('admin', 'admin@shopacc.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0.00);

-- Danh mục mẫu
INSERT INTO `categories` (`name`, `type`, `icon`, `status`) VALUES
('Liên Quân Mobile', 'game', 'fa-gamepad', 1),
('Free Fire', 'game', 'fa-fire', 1),
('PUBG Mobile', 'game', 'fa-crosshairs', 1),
('Genshin Impact', 'game', 'fa-star', 1),
('Facebook', 'social', 'fa-facebook', 1),
('TikTok', 'social', 'fa-tiktok', 1),
('Instagram', 'social', 'fa-instagram', 1),
('YouTube', 'social', 'fa-youtube', 1);

-- Sản phẩm mẫu
INSERT INTO `products` (`category_id`, `title`, `description`, `price`, `account_info`, `image`, `status`) VALUES
(1, 'Acc Liên Quân - 50 Tướng Full Ngọc', 'Tài khoản Liên Quân Mobile 50 tướng, full ngọc, nhiều skin đẹp.', 150000, 'Sẽ gửi sau khi mua', NULL, 'available'),
(1, 'Acc Liên Quân VIP - 80 Tướng', 'Tài khoản VIP 80 tướng, skin SS limited, rank Cao Thủ.', 500000, 'Sẽ gửi sau khi mua', NULL, 'available'),
(2, 'Acc Free Fire - Rank Huyền Thoại', 'Acc Free Fire full nhân vật, nhiều súng VIP.', 200000, 'Sẽ gửi sau khi mua', NULL, 'available'),
(3, 'Acc PUBG Mobile - Skin M416 Huyền Thoại', 'Acc PUBG Mobile nhiều skin súng, rank Ace.', 350000, 'Sẽ gửi sau khi mua', NULL, 'available'),
(4, 'Acc Genshin AR55 - Nhiều 5 sao', 'Tài khoản Genshin AR55, có Raiden, Zhongli, Hu Tao.', 800000, 'Sẽ gửi sau khi mua', NULL, 'available');

-- Dịch vụ MXH mẫu
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_quantity`, `max_quantity`, `status`) VALUES
(5, 'Tăng Like Facebook Post', 'Like bài viết Facebook từ tài khoản thật Việt Nam.', 25000, 100, 50000, 1),
(5, 'Tăng Follow Facebook', 'Tăng người theo dõi Facebook profile.', 30000, 100, 100000, 1),
(5, 'Clone Facebook Việt', 'Bán clone Facebook Việt Nam XMDT.', 5000, 10, 1000, 1),
(6, 'Tăng Follow TikTok', 'Tăng người theo dõi TikTok, tài khoản thật.', 35000, 100, 100000, 1),
(6, 'Tăng Like TikTok Video', 'Like video TikTok, tốc độ nhanh.', 20000, 100, 50000, 1),
(7, 'Tăng Follow Instagram', 'Tăng follower Instagram, có avatar.', 40000, 100, 50000, 1),
(8, 'Tăng Sub YouTube', 'Tăng subscribe kênh YouTube.', 80000, 100, 10000, 1),
(8, 'Tăng View YouTube', 'Tăng lượt xem video YouTube.', 15000, 1000, 1000000, 1);
