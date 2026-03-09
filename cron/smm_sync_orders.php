<?php
/**
 * Cron: Đồng bộ trạng thái đơn hàng MXH từ web mẹ
 *
 * Chạy mỗi 1-2 phút:
 *   * * * * * php /path/to/cron/smm_sync_orders.php >> /path/to/logs/smm_orders.log 2>&1
 *
 * Chức năng:
 *   1. Lấy các đơn hàng service có smm_order_id chưa hoàn
 *   2. Gọi API action=status cho từng đơn
 *   3. Cập nhật trạng thái (Completed, Processing, In progress, Partial, Cancelled)
 *   4. Hoàn tiền nếu đơn bị Cancelled hoặc Partial
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/app/Models/Order.php';
require_once BASE_PATH . '/app/Models/User.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Services/SmmApiService.php';

echo "[" . date('Y-m-d H:i:s') . "] === SMM Sync Orders ===\n";

$smm = new SmmApiService();
$db = getDatabaseConnection();
$userModel = new User();
$transModel = new Transaction();

// 1. Lấy đơn hàng cần check
$stmt = $db->query("
    SELECT * FROM orders
    WHERE order_type = 'service'
      AND smm_order_id IS NOT NULL
      AND status NOT IN ('completed', 'cancelled')
    ORDER BY created_at ASC
    LIMIT 50
");
$orders = $stmt->fetchAll();

if (empty($orders)) {
    echo "Không có đơn hàng cần kiểm tra.\n";
    exit;
}

echo "🔍 Kiểm tra " . count($orders) . " đơn hàng...\n\n";

$completedCount = 0;
$cancelledCount = 0;
$partialCount = 0;

foreach ($orders as $order) {
    $smmOrderId = $order['smm_order_id'];
    $result = $smm->getOrderStatus($smmOrderId);

    if (isset($result['error'])) {
        echo "⚠ Đơn #{$order['id']} (SMM#{$smmOrderId}): {$result['error']}\n";
        continue;
    }

    $smmStatus = $result['status'] ?? 'Unknown';
    $startCount = intval($result['start_count'] ?? 0);
    $remains = intval($result['remains'] ?? 0);

    // Cập nhật thông tin từ API
    $updateData = [
        'smm_status' => $smmStatus,
        'start_count' => $startCount,
        'remains' => $remains,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Map trạng thái web mẹ → trạng thái nội bộ
    $newStatus = null;
    switch (strtolower($smmStatus)) {
        case 'completed':
            $newStatus = 'completed';
            $completedCount++;
            echo "✅ Đơn #{$order['id']} (SMM#{$smmOrderId}): Hoàn thành\n";
            break;

        case 'processing':
        case 'in progress':
            $newStatus = 'processing';
            echo "⏳ Đơn #{$order['id']} (SMM#{$smmOrderId}): Đang xử lý\n";
            break;

        case 'partial':
            $newStatus = 'completed'; // Xong nhưng không đủ
            $partialCount++;

            // Hoàn tiền phần chưa xử lý
            if ($remains > 0 && $order['quantity'] > 0) {
                $refundRate = $remains / $order['quantity'];
                $refundAmount = round($order['total_price'] * $refundRate, 2);

                if ($refundAmount > 0) {
                    $userModel->updateBalance($order['user_id'], $refundAmount);
                    $newBalance = $userModel->getBalance($order['user_id']);

                    $transModel->log(
                        $order['user_id'],
                        'refund',
                        $refundAmount,
                        $newBalance,
                        "Hoàn tiền đơn #{$order['id']} (partial: còn {$remains})"
                    );

                    echo "💰 Hoàn tiền: " . $refundAmount . "đ cho user #{$order['user_id']}\n";
                }
            }
            echo "⚠ Đơn #{$order['id']} (SMM#{$smmOrderId}): Partial (còn {$remains})\n";
            break;

        case 'cancelled':
        case 'canceled':
            $newStatus = 'cancelled';
            $cancelledCount++;

            // Hoàn tiền toàn bộ
            $refundAmount = floatval($order['total_price']);
            if ($refundAmount > 0) {
                $userModel->updateBalance($order['user_id'], $refundAmount);
                $newBalance = $userModel->getBalance($order['user_id']);

                $transModel->log(
                    $order['user_id'],
                    'refund',
                    $refundAmount,
                    $newBalance,
                    "Hoàn tiền đơn bị hủy #{$order['id']} (SMM#{$smmOrderId})"
                );

                echo "💰 Hoàn tiền: " . $refundAmount . "đ cho user #{$order['user_id']}\n";
            }
            echo "❌ Đơn #{$order['id']} (SMM#{$smmOrderId}): Đã hủy\n";
            break;

        default:
            echo "❓ Đơn #{$order['id']} (SMM#{$smmOrderId}): Status={$smmStatus}\n";
            break;
    }

    if ($newStatus) {
        $updateData['status'] = $newStatus;
    }

    // Cập nhật DB
    $setClauses = [];
    $params = [];
    foreach ($updateData as $col => $val) {
        $setClauses[] = "`{$col}` = ?";
        $params[] = $val;
    }
    $params[] = $order['id'];
    $sql = "UPDATE orders SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
}

echo "\n📊 Kết quả: ✅{$completedCount} | ⚠{$partialCount} partial | ❌{$cancelledCount} hủy\n";
echo "[" . date('Y-m-d H:i:s') . "] ✅ Đồng bộ hoàn tất.\n";
