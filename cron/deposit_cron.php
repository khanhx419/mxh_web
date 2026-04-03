<?php
/**
 * Cron Job - Multi-Bank Deposit Processing
 * 
 * Supports: MBBank, Vietcombank, ACB, Momo, TheSieuRe, Card charging
 * 
 * Usage:
 *   php cron/deposit_cron.php                    # Default: MBBank
 *   php cron/deposit_cron.php --type=mbbank
 *   php cron/deposit_cron.php --type=vietcombank
 *   php cron/deposit_cron.php --type=acb
 *   php cron/deposit_cron.php --type=momo
 *   php cron/deposit_cron.php --type=thesieure
 *   php cron/deposit_cron.php --type=card
 *
 * Crontab (mỗi 1-2 phút):
 *   * * * * * php /path/to/cron/deposit_cron.php --type=mbbank >> /path/to/logs/deposit.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/app/Models/User.php';
require_once BASE_PATH . '/app/Models/Invoice.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Models/Setting.php';
require_once BASE_PATH . '/app/Models/CardList.php';

// Parse arguments
$type = 'mbbank';
foreach ($argv ?? [] as $arg) {
    if (strpos($arg, '--type=') === 0) {
        $type = substr($arg, 7);
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Deposit Cron Started — Type: {$type}\n";

$settingModel = new Setting();
$invoiceModel = new Invoice();
$userModel = new User();
$transModel = new Transaction();

$db = getDatabaseConnection();
$prefix = strtoupper($settingModel->get('bank_prefix', 'NAP'));
$discount = intval($settingModel->get('deposit_discount', 0));

// ============================================================
// 1. Hủy hoá đơn quá hạn (> 30 phút)
// ============================================================
$timeoutStr = date('Y-m-d H:i:s', time() - 30 * 60);
$expired = $db->prepare("UPDATE invoices SET status = 2 WHERE status = 0 AND created_at < ?");
$expired->execute([$timeoutStr]);
$expiredCount = $expired->rowCount();
if ($expiredCount > 0) {
    echo "Đã hủy {$expiredCount} hoá đơn quá hạn.\n";
}

// ============================================================
// Card Charging - separate logic
// ============================================================
if ($type === 'card') {
    processCards($settingModel, $userModel, $transModel, $db, $discount);
    exit;
}

// ============================================================
// 2. Determine API config based on bank type
// ============================================================
$apiConfig = getBankApiConfig($type, $settingModel);

if (!$apiConfig) {
    echo "Lỗi: Chưa cấu hình API cho {$type}\n";
    exit;
}

// ============================================================
// 3. Call bank API
// ============================================================
$response = callBankApiCurl($apiConfig['url']);
if (!$response) {
    echo "Lỗi: Không thể kết nối API\n";
    exit;
}

$data = json_decode($response, true);
if (!$data) {
    echo "Lỗi: Không parse được JSON\n";
    exit;
}

// ============================================================
// 4. Parse transactions based on bank type
// ============================================================
$list_transaction = parseTransactions($data, $type, $prefix);

echo "Tìm thấy " . count($list_transaction) . " giao dịch hợp lệ.\n";

if (empty($list_transaction)) {
    echo "Không có giao dịch mới.\n";
    exit;
}

// ============================================================
// 5. Process each transaction
// ============================================================
$processedCount = 0;

// Get all pending invoices
$pendingInvoices = $invoiceModel->getPending();
$invoiceMap = [];
foreach ($pendingInvoices as $inv) {
    $key = strtoupper(trim($inv['trans_id']));
    $invoiceMap[$key] = $inv;
}

foreach ($list_transaction as $item) {
    $txId = (string) $item['transactionID'];
    $txAmount = intval($item['amount']);
    $txDesc = strtoupper($item['description']);

    // Check duplicate
    if ($invoiceModel->isTransactionProcessed($txId)) {
        continue;
    }

    // Match against pending invoices
    $matchedInvoice = null;
    foreach ($invoiceMap as $transId => $invoice) {
        if (strpos($txDesc, $transId) !== false) {
            $matchedInvoice = $invoice;
            break;
        }
    }

    if (!$matchedInvoice) {
        continue;
    }

    if ($txAmount < intval($matchedInvoice['pay'])) {
        echo "⚠ GD {$txId}: Số tiền ({$txAmount}) < hoá đơn ({$matchedInvoice['pay']}). Bỏ qua.\n";
        continue;
    }

    // Process deposit
    $userId = $matchedInvoice['user_id'];
    $payAmount = intval($matchedInvoice['pay']);

    // Apply discount
    $finalAmount = $payAmount;
    if ($discount > 0) {
        $finalAmount = $payAmount + ($payAmount * $discount) / 100;
    }

    // Update invoice
    $invoiceModel->update($matchedInvoice['id'], [
        'status' => 1,
        'tid' => $txId
    ]);

    // Credit user
    $userModel->updateBalance($userId, $finalAmount);
    $userModel->incrementField($userId, 'total_deposit', $finalAmount);
    $newBalance = $userModel->getBalance($userId);

    // Log transaction
    $transModel->log(
        $userId,
        'deposit',
        $finalAmount,
        $newBalance,
        'Nạp tiền tự động ' . strtoupper($type) . ' - GD: ' . $txId . ($discount > 0 ? ' - KM: ' . $discount . '%' : '')
    );

    // Commission
    processCommission($userId, $finalAmount, $userModel, $settingModel, $db);

    // Remove matched invoice from map
    unset($invoiceMap[strtoupper($matchedInvoice['trans_id'])]);

    $processedCount++;
    echo "✅ Nạp: User #{$userId} | +{$finalAmount}đ | GD: {$txId}\n";
}

echo "\n[" . date('Y-m-d H:i:s') . "] Hoàn tất. Đã xử lý {$processedCount} giao dịch.\n";

// ============================================================
// Helper Functions
// ============================================================

function getBankApiConfig($type, $settingModel)
{
    $apiNames = [
        'vietcombank' => 'historyapivcbv2',
        'mbbank'      => 'historyapimbbankv2',
        'acb'         => 'historyapiacbv2',
        'momo'        => 'historyapimomo',
        'thesieure'   => 'historyapithesieure',
    ];

    $apiName = $apiNames[$type] ?? null;
    if (!$apiName) return null;

    // Try settings DB first, then .env
    $token = $settingModel->get('bank_' . substr($type, 0, 3) . '_api_token', '');
    if (empty($token)) {
        $token = env('BANK_API_TOKEN', '');
    }

    if (empty($token) || $token === 'your_api_token_here') {
        return null;
    }

    return [
        'url' => "https://thueapibank.vn/{$apiName}/{$token}",
        'api_name' => $apiName,
        'token' => $token,
    ];
}

function callBankApiCurl($url)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error: {$error}\n";
        return null;
    }
    return $response;
}

function parseTransactions($data, $type, $prefix)
{
    $list = [];

    if ($type === 'momo') {
        $transactions = $data['momoMsg']['tranList'] ?? [];
        foreach ($transactions as $value) {
            if (!stripos($value['comment'] ?? '', $prefix) !== false) continue;
            $list[] = [
                'amount'        => $value['amount'],
                'description'   => $value['comment'] ?? '',
                'transactionID' => (string) $value['tranId'],
            ];
        }
    } elseif ($type === 'thesieure') {
        $transactions = $data['tranList'] ?? [];
        foreach ($transactions as $value) {
            if (stripos($value['comment'] ?? '', $prefix) === false) continue;
            $list[] = [
                'amount'        => (float) str_replace([',', 'đ'], '', $value['amount']),
                'description'   => (string) ($value['description'] ?? ''),
                'transactionID' => (string) ($value['description'] ?? uniqid()),
            ];
        }
    } else {
        // Bank (VCB, MB, ACB)
        $transactions = $data['transactions'] ?? [];
        foreach ($transactions as $value) {
            if (($value['type'] ?? '') !== 'IN') continue;
            if (stripos($value['description'] ?? '', $prefix) === false) continue;
            $list[] = [
                'amount'        => $value['amount'],
                'description'   => $value['description'],
                'transactionID' => (string) $value['transactionID'],
            ];
        }
    }

    return $list;
}

function processCards($settingModel, $userModel, $transModel, $db, $discount)
{
    $apiUrl = $settingModel->get('card_api_url', '');
    $partnerId = $settingModel->get('card_partner_id', '');
    $partnerKey = $settingModel->get('card_partner_key', '');

    if (empty($apiUrl) || empty($partnerId) || empty($partnerKey)) {
        echo "Chưa cấu hình Card API.\n";
        return;
    }

    $stmt = $db->prepare("SELECT * FROM card_lists WHERE status = 'Processing'");
    $stmt->execute();
    $cards = $stmt->fetchAll();

    if (empty($cards)) {
        echo "Không có thẻ nào đang chờ.\n";
        return;
    }

    foreach ($cards as $item) {
        $fees = intval($settingModel->get('card_fees_' . strtolower($item['type']), 20));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl . '/chargingws/v2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'telco'      => strtoupper($item['type']),
                'code'       => $item['code'],
                'serial'     => $item['serial'],
                'amount'     => $item['amount'],
                'request_id' => $item['request_id'],
                'partner_id' => $partnerId,
                'sign'       => md5($partnerKey . $item['code'] . $item['serial']),
                'command'    => 'check',
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if (!isset($result['status'])) continue;

        switch ($result['status']) {
            case 1: // Success
                $amount = $result['declared_value'];
                $realAmount = $amount - ($amount * $fees) / 100;
                $code = 'CARD-' . substr(md5(uniqid()), 0, 6);

                $userModel->updateBalance($item['user_id'], $realAmount);
                $userModel->incrementField($item['user_id'], 'total_deposit', $realAmount);
                $newBalance = $userModel->getBalance($item['user_id']);

                $transModel->log(
                    $item['user_id'], 'deposit', $realAmount, $newBalance,
                    'Nạp thẻ thành công #' . $item['serial'] . '; phí ' . $fees . '%'
                );

                $stmt = $db->prepare("UPDATE card_lists SET value=?, status='Completed', amount=?, content=?, transaction_code=? WHERE id=?");
                $stmt->execute([$amount, $realAmount, $result['message'], $code, $item['id']]);

                processCommission($item['user_id'], $realAmount, $userModel, new Setting(), $db);

                echo "✅ Thẻ #{$item['id']}: {$item['serial']} => +{$realAmount}đ\n";
                break;
            case 2: // Cancelled
                $stmt = $db->prepare("UPDATE card_lists SET status='Cancelled', amount=0, content=? WHERE id=?");
                $stmt->execute([$result['message'] ?? 'Unknown', $item['id']]);
                echo "❌ Thẻ #{$item['id']}: {$result['message']}\n";
                break;
            case 3: // Error
                $stmt = $db->prepare("UPDATE card_lists SET status='Error', amount=0, content=? WHERE id=?");
                $stmt->execute([$result['message'] ?? 'Unknown', $item['id']]);
                echo "⚠ Thẻ #{$item['id']}: {$result['message']}\n";
                break;
            default:
                echo "? Thẻ #{$item['id']}: Status={$result['status']}\n";
                break;
        }
    }
}

function processCommission($userId, $amount, $userModel, $settingModel, $db)
{
    // Simple commission: find referrer if exists
    $percent = intval($settingModel->get('comm_percent', 10));
    if ($percent <= 0) return;

    // Check if user has a referrer (would need a referrer_id column - skip if not exists)
    try {
        $stmt = $db->prepare("SHOW COLUMNS FROM users LIKE 'referrer_id'");
        $stmt->execute();
        if ($stmt->rowCount() === 0) return;

        $stmt2 = $db->prepare("SELECT referrer_id FROM users WHERE id = ?");
        $stmt2->execute([$userId]);
        $row = $stmt2->fetch();
        if (!$row || !$row['referrer_id']) return;

        $commission = ($amount * $percent) / 100;
        $userModel->incrementField($row['referrer_id'], 'balance_1', $commission);
    } catch (Exception $e) {
        // Silently skip if referrer system not set up
    }
}
