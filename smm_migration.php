<?php
/**
 * Migration: Thêm cột SMM cho bảng services và orders
 * Chạy 1 lần: php smm_migration.php
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

$db = getDatabaseConnection();

$queries = [
    // Thêm cột cho bảng services
    "ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `smm_service_id` INT DEFAULT NULL COMMENT 'ID dịch vụ trên web mẹ' AFTER `category_id`",
    "ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `smm_type` VARCHAR(50) DEFAULT NULL COMMENT 'Loại DV trên web mẹ' AFTER `smm_service_id`",
    "ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `refill` TINYINT(1) DEFAULT 0 COMMENT 'Có hỗ trợ refill' AFTER `max_quantity`",
    "ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `rate_original` DECIMAL(15,4) DEFAULT 0 COMMENT 'Giá gốc từ web mẹ (per 1000)' AFTER `price_per_1000`",

    // Thêm cột cho bảng orders
    "ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `smm_order_id` INT DEFAULT NULL COMMENT 'Mã đơn trên web mẹ' AFTER `status`",
    "ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `smm_status` VARCHAR(50) DEFAULT NULL COMMENT 'Trạng thái trên web mẹ' AFTER `smm_order_id`",
    "ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `start_count` INT DEFAULT NULL COMMENT 'Số đếm bắt đầu' AFTER `smm_status`",
    "ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `remains` INT DEFAULT NULL COMMENT 'Số lượng còn lại' AFTER `start_count`",

    // Index
    "ALTER TABLE `services` ADD INDEX IF NOT EXISTS `idx_smm_service_id` (`smm_service_id`)",
    "ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_smm_order_id` (`smm_order_id`)",
];

echo "=== SMM Migration ===\n\n";

foreach ($queries as $sql) {
    try {
        $db->exec($sql);
        echo "✅ " . substr($sql, 0, 80) . "...\n";
    } catch (PDOException $e) {
        // Bỏ qua lỗi duplicate column (đã thêm rồi)
        if (
            strpos($e->getMessage(), 'Duplicate column') !== false
            || strpos($e->getMessage(), 'Duplicate key') !== false
        ) {
            echo "⏭ Đã tồn tại: " . substr($sql, 0, 60) . "...\n";
        } else {
            echo "❌ Error: {$e->getMessage()}\n   SQL: {$sql}\n";
        }
    }
}

echo "\n✅ Migration hoàn tất!\n";
