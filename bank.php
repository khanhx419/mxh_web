<?php
/**
 * bank.php - Script gọi API ngân hàng để lấy lịch sử giao dịch
 * và tự động cộng tiền cho user dựa trên nội dung chuyển khoản.
 *
 * Chạy bằng cron job hoặc gọi thủ công:
 *   php bank.php
 *
 * API Response format:
 * {
 *   "status": "success",
 *   "transactions": [
 *     {
 *       "transactionID": "FT24032059009306B39",
 *       "amount": "30000",
 *       "description": "CUSTOMER Thanh toan QR-NAP59. TU: NGUYEN VAN A",
 *       "transactionDate": "31/01/2024 23:46:00",
 *       "type": "IN"
 *     }
 *   ]
 * }
 */

// Bootstrap
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/app/Models/User.php';
require_once BASE_PATH . '/app/Models/Invoice.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Models/Setting.php';
require_once BASE_PATH . '/app/Services/TelegramService.php';

// ============================================================
// Cấu hình API
// ============================================================
$apiUrl = env('BANK_API_URL', 'https://api.web2m.com/historyapimbfull');
$apiToken = env('BANK_API_TOKEN', '');
$bankPrefix = env('BANK_PREFIX', ''); // Lấy từ env hoặc setting DB

// Lấy prefix từ settings DB nếu chưa có trong env
$settingModel = new Setting();
if (empty($bankPrefix)) {
    $bankPrefix = strtoupper($settingModel->get('bank_prefix', 'NAP'));
}

echo "[" . date('Y-m-d H:i:s') . "] Bank Cron Started\n";
echo "API URL: {$apiUrl}\n";
echo "Prefix: {$bankPrefix}\n\n";

// ============================================================
// 1. Hủy hoá đơn quá hạn (> 30 phút)
// ============================================================
$invoiceModel = new Invoice();
$userModel = new User();
$transModel = new Transaction();

$db = $invoiceModel->getDb();
$timeoutStr = date('Y-m-d H:i:s', time() - 30 * 60);
$expired = $db->prepare("UPDATE invoices SET status = 2 WHERE status = 0 AND created_at < ?");
$expired->execute([$timeoutStr]);
$expiredCount = $expired->rowCount();
if ($expiredCount > 0) {
    echo "Đã hủy {$expiredCount} hoá đơn quá hạn.\n";
}

// ============================================================
// 2. Lấy danh sách invoice đang pending
// ============================================================
$pendingInvoices = $invoiceModel->getPending();
if (empty($pendingInvoices)) {
    echo "Không có hoá đơn pending nào.\n";
    exit;
}

echo "Tìm thấy " . count($pendingInvoices) . " hoá đơn đang chờ.\n";

// Tạo map: trans_id (uppercase) => invoice data
$invoiceMap = [];
foreach ($pendingInvoices as $inv) {
    $key = strtoupper(trim($inv['trans_id']));
    $invoiceMap[$key] = $inv;
}

// ============================================================
// 3. Gọi API ngân hàng
// ============================================================
$apiData = callBankApi($apiUrl, $apiToken);

if (!$apiData) {
    echo "Lỗi: Không thể kết nối API ngân hàng.\n";
    exit;
}

if ($apiData['status'] !== 'success') {
    echo "API Error: " . ($apiData['message'] ?? 'Unknown') . "\n";
    exit;
}

$transactions = $apiData['transactions'] ?? [];
echo "API trả về " . count($transactions) . " giao dịch.\n\n";

// ============================================================
// 4. Duyệt từng giao dịch và khớp với invoice
// ============================================================
$processedCount = 0;

foreach ($transactions as $tx) {
    // Chỉ xử lý giao dịch tiền VÀO
    if (($tx['type'] ?? '') !== 'IN') {
        continue;
    }

    $txId = $tx['transactionID'] ?? '';
    $txAmount = intval($tx['amount'] ?? 0);
    $txDesc = strtoupper($tx['description'] ?? '');

    // Kiểm tra giao dịch đã xử lý chưa (tránh nạp trùng)
    if ($invoiceModel->isTransactionProcessed($txId)) {
        continue;
    }

    // Tìm mã nạp trong nội dung chuyển khoản
    // Nội dung CK có thể ở dạng: "...NAP59..." hoặc "...QR-NAP59..."
    // Tìm tất cả mã trans_id đang pending xem có khớp không
    $matchedInvoice = null;

    foreach ($invoiceMap as $transId => $invoice) {
        // Kiểm tra trans_id có xuất hiện trong description không
        if (strpos($txDesc, $transId) !== false) {
            $matchedInvoice = $invoice;
            break;
        }
    }

    if (!$matchedInvoice) {
        continue; // Không khớp invoice nào
    }

    // Kiểm tra số tiền (phải >= số tiền invoice)
    if ($txAmount < intval($matchedInvoice['pay'])) {
        echo "⚠ GD {$txId}: Số tiền ({$txAmount}) < hoá đơn ({$matchedInvoice['pay']}). Bỏ qua.\n";
        continue;
    }

    // ============================================================
    // 5. Xử lý cộng tiền
    // ============================================================
    $userId = $matchedInvoice['user_id'];
    $payAmount = intval($matchedInvoice['pay']);

    // Cập nhật hoá đơn thành công
    $invoiceModel->update($matchedInvoice['id'], [
        'status' => 1,
        'tid' => $txId
    ]);

    // Cộng tiền vào tài khoản user
    $userModel->updateBalance($userId, $payAmount);
    $newBalance = $userModel->getBalance($userId);

    // Ghi lịch sử giao dịch
    $transModel->log(
        $userId,
        'deposit',
        $payAmount,
        $newBalance,
        'Nạp tiền tự động - Mã GD: ' . $txId . ' | HĐ: ' . $matchedInvoice['trans_id']
    );

    // Xoá invoice khỏi map để không match lại
    unset($invoiceMap[strtoupper($matchedInvoice['trans_id'])]);

    // Gửi thông báo Telegram
    try {
        $telegram = new TelegramService();
        $user = $userModel->findById($userId);
        $telegram->notifyDeposit($userId, $user['username'] ?? 'User#'.$userId, $payAmount, $txId, $newBalance);
    } catch (Exception $e) {
        echo "⚠ Telegram notification failed: " . $e->getMessage() . "\n";
    }

    $processedCount++;
    echo "✅ Nạp thành công: User #{$userId} | +{$payAmount}đ | GD: {$txId} | HĐ: #{$matchedInvoice['trans_id']}\n";
}

echo "\n[" . date('Y-m-d H:i:s') . "] Hoàn tất. Đã xử lý {$processedCount} giao dịch.\n";

// ============================================================
// Hàm gọi API ngân hàng
// ============================================================
function callBankApi($url, $token)
{
    // Nếu không có token hoặc URL → dùng file local để test
    if (empty($token) || $token === 'your_api_token_here') {
        echo "⚠ Chưa cấu hình API Token. Đang chạy ở chế độ TEST (đọc từ sample data).\n";
        // Trả về dữ liệu mẫu để test (có thể đọc từ file JSON nếu cần)
        return [
            'status' => 'success',
            'transactions' => []
        ];
    }

    // Gọi API thật bằng cURL
    $fullUrl = $url . '/' . $token;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error: {$error}\n";
        return null;
    }

    if ($httpCode !== 200) {
        echo "HTTP Error: {$httpCode}\n";
        return null;
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Parse Error: " . json_last_error_msg() . "\n";
        return null;
    }

    return $data;
}