<?php
/**
 * Cron: Đồng bộ dịch vụ MXH từ web mẹ (sub6sao.com)
 *
 *   Chạy mỗi 30 phút (crontab: 0,30 * * * *):
 *
 * Chức năng:
 *   1. Gọi API action=services → lấy toàn bộ danh sách dịch vụ
 *   2. Tự động tạo/cập nhật dịch vụ trong DB
 *   3. Áp dụng markup giá bán lẻ (mặc định 40%)
 *   4. Tự động tạo category nếu chưa có
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/app/Models/Service.php';
require_once BASE_PATH . '/app/Models/Category.php';
require_once BASE_PATH . '/app/Services/SmmApiService.php';

echo "[" . date('Y-m-d H:i:s') . "] === SMM Sync Services ===\n";

$smm = new SmmApiService();
$markup = floatval(env('SMM_PRICE_MARKUP', 40)); // % markup

// 1. Gọi API lấy danh sách
$services = $smm->getServices();

if (isset($services['error'])) {
    echo "❌ API Error: {$services['error']}\n";
    exit(1);
}

if (!is_array($services) || empty($services)) {
    echo "⚠ Không có dịch vụ nào từ API.\n";
    exit;
}

echo "📦 Nhận được " . count($services) . " dịch vụ từ web mẹ.\n";
echo "💰 Markup giá: {$markup}%\n\n";

$db = getDatabaseConnection();
$categoryCache = []; // category_name => category_id
$created = 0;
$updated = 0;

foreach ($services as $svc) {
    $smmId = intval($svc['service'] ?? 0);
    $name = trim($svc['name'] ?? '');
    $category = trim($svc['category'] ?? 'Khác');
    $type = trim($svc['type'] ?? 'Default');
    $rateOrig = floatval($svc['rate'] ?? 0);
    $min = intval($svc['min'] ?? 100);
    $max = intval($svc['max'] ?? 100000);
    $refill = !empty($svc['refill']) ? 1 : 0;

    if (empty($name) || $smmId <= 0)
        continue;

    // Tính giá bán lẻ (giá gốc + markup%)
    $retailPrice = round($rateOrig * (1 + $markup / 100), 2);

    // Tìm hoặc tạo category
    if (!isset($categoryCache[$category])) {
        $stmt = $db->prepare("SELECT id FROM categories WHERE name = ? AND type = 'service' LIMIT 1");
        $stmt->execute([$category]);
        $cat = $stmt->fetch();

        if ($cat) {
            $categoryCache[$category] = $cat['id'];
        } else {
            // Tạo category mới
            $stmt = $db->prepare("INSERT INTO categories (name, type, description, status) VALUES (?, 'service', ?, 1)");
            $stmt->execute([$category, "Dịch vụ {$category} - Auto sync"]);
            $categoryCache[$category] = $db->lastInsertId();
            echo "📁 Tạo danh mục mới: {$category}\n";
        }
    }
    $categoryId = $categoryCache[$category];

    // Kiểm tra dịch vụ đã tồn tại chưa (theo smm_service_id)
    $stmt = $db->prepare("SELECT id FROM services WHERE smm_service_id = ? LIMIT 1");
    $stmt->execute([$smmId]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Cập nhật giá + thông tin
        $stmt = $db->prepare("
            UPDATE services SET
                name = ?,
                category_id = ?,
                smm_type = ?,
                rate_original = ?,
                price_per_1000 = ?,
                min_quantity = ?,
                max_quantity = ?,
                refill = ?,
                updated_at = NOW()
            WHERE smm_service_id = ?
        ");
        $stmt->execute([
            $name,
            $categoryId,
            $type,
            $rateOrig,
            $retailPrice,
            $min,
            $max,
            $refill,
            $smmId
        ]);
        $updated++;
    } else {
        // Tạo mới
        $stmt = $db->prepare("
            INSERT INTO services
                (smm_service_id, category_id, name, smm_type, rate_original, price_per_1000, min_quantity, max_quantity, refill, status)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            $smmId,
            $categoryId,
            $name,
            $type,
            $rateOrig,
            $retailPrice,
            $min,
            $max,
            $refill
        ]);
        $created++;
    }
}

echo "\n📊 Kết quả: Tạo mới {$created} | Cập nhật {$updated}\n";
echo "[" . date('Y-m-d H:i:s') . "] ✅ Đồng bộ hoàn tất.\n";
