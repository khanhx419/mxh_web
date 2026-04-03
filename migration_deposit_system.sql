-- ============================================================
-- Migration: Deposit System + Chess Wins + Mystery Bag Status
-- Run once: Import via phpMyAdmin or `mysql -u root shopacc_db < migration_deposit_system.sql`
-- ============================================================

-- 1. Card charging table
CREATE TABLE IF NOT EXISTS `card_lists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL COMMENT 'Nhà mạng: VIETTEL, MOBIFONE, VINAPHONE...',
    `serial` VARCHAR(100) NOT NULL,
    `code` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(15,2) DEFAULT 0 COMMENT 'Mệnh giá khai báo',
    `value` DECIMAL(15,2) DEFAULT 0 COMMENT 'Giá trị thực tế',
    `request_id` VARCHAR(100) NOT NULL UNIQUE,
    `order_id` INT DEFAULT NULL,
    `status` ENUM('Processing','Completed','Cancelled','Error') DEFAULT 'Processing',
    `content` TEXT DEFAULT NULL,
    `transaction_code` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Chess wins tracking (per difficulty)
CREATE TABLE IF NOT EXISTS `chess_wins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `difficulty` ENUM('easy','medium','hard','hell') NOT NULL,
    `points` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add deposit-related columns to users
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `total_deposit` DECIMAL(15,2) DEFAULT 0 AFTER `balance`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `balance_1` DECIMAL(15,2) DEFAULT 0 COMMENT 'Commission wallet' AFTER `total_deposit`;

-- 4. Add type and request_id to invoices for multi-payment support
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `type` VARCHAR(50) DEFAULT 'bank' AFTER `method`;
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `request_id` VARCHAR(100) DEFAULT NULL AFTER `type`;

-- 5. Add status to mystery_bag_items (enable/disable items without deleting)
ALTER TABLE `mystery_bag_items` ADD COLUMN IF NOT EXISTS `status` TINYINT(1) DEFAULT 1 AFTER `probability`;

-- 6. New settings for multi-bank deposit system
INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
('deposit_discount', '0'),
('deposit_prefix', 'NAP'),
('comm_percent', '10'),
('card_api_url', ''),
('card_partner_id', ''),
('card_partner_key', ''),
('card_fees_viettel', '20'),
('card_fees_mobifone', '20'),
('card_fees_vinaphone', '20'),
('bank_vcb_api_token', ''),
('bank_vcb_account_number', ''),
('bank_vcb_account_password', ''),
('bank_mb_api_token', ''),
('bank_mb_account_number', ''),
('bank_mb_account_password', ''),
('bank_acb_api_token', ''),
('bank_acb_account_number', ''),
('bank_acb_account_password', ''),
('bank_momo_api_token', ''),
('bank_thesieure_api_token', '');

-- Index for chess_wins queries
CREATE INDEX IF NOT EXISTS `idx_chess_wins_user_diff` ON `chess_wins` (`user_id`, `difficulty`);
CREATE INDEX IF NOT EXISTS `idx_chess_wins_difficulty` ON `chess_wins` (`difficulty`, `points`);
CREATE INDEX IF NOT EXISTS `idx_card_lists_status` ON `card_lists` (`status`);
