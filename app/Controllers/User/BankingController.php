<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class BankingController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    /**
     * Trang nạp tiền
     */
    public function index()
    {
        $settingModel = $this->model('Setting');
        $invoiceModel = $this->model('Invoice');

        $userId = $_SESSION['user_id'];
        $invoices = $invoiceModel->getUserInvoices($userId);

        $bankPrefix = strtoupper($settingModel->get('bank_prefix', 'NAP'));
        $transferContent = $bankPrefix . $userId; // VD: NAP59

        $bankConfig = [
            'bank_name' => $settingModel->get('bank_name', 'MBBank'),
            'bank_acc_name' => $settingModel->get('bank_acc_name', 'NGUYEN NHAT LOC'),
            'bank_acc_number' => $settingModel->get('bank_acc_number', '90919072000'),
            'bank_prefix' => $bankPrefix,
            'transfer_content' => $transferContent
        ];

        $depositNotice = $settingModel->get('deposit_notice', 'Vui lòng nạp theo nội dung sau: [nội dung]. Nếu sau 10p tiền không vào tài khoản thì liên hệ admin.');
        $depositQrImage = $settingModel->get('deposit_qr_image', '');
        $depositTransferDetails = $settingModel->get('deposit_transfer_details', '');

        $this->view('user.banking', [
            'pageTitle' => 'Nạp tiền vào tài khoản',
            'invoices' => $invoices,
            'bankConfig' => $bankConfig,
            'transferContent' => $transferContent,
            'depositNotice' => $depositNotice,
            'depositQrImage' => $depositQrImage,
            'depositTransferDetails' => $depositTransferDetails
        ]);
    }

    /**
     * Xử lý tạo hóa đơn nạp tiền
     */
    public function createInvoice()
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        $amount = intval($_POST['amount'] ?? 0);
        $method = trim($_POST['method'] ?? 'MBBank');

        if ($amount < 10000) {
            $this->json(['status' => 'error', 'message' => 'Số tiền nạp tối thiểu là 10,000đ']);
            return;
        }

        $invoiceModel = $this->model('Invoice');
        $userId = $_SESSION['user_id'];

        // Giới hạn 3 invoice pending
        $pendingCount = $invoiceModel->count(['user_id' => $userId, 'status' => 0]);
        if ($pendingCount >= 3) {
            $this->json(['status' => 'error', 'message' => 'Bạn có quá nhiều giao dịch đang chờ xử lý.']);
            return;
        }

        $invoice = $invoiceModel->createInvoice($userId, $amount, $method);

        if ($invoice) {
            $this->json([
                'status' => 'success',
                'message' => 'Tạo hóa đơn thành công! Nội dung CK: ' . $invoice['trans_id'],
                'redirect' => url('/banking')
            ]);
        } else {
            $this->json(['status' => 'error', 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }
    }

    /**
     * Kiểm tra trạng thái nạp tiền (user bấm nút "Kiểm tra")
     * Gọi API ngân hàng real-time 1 lần để check
     */
    public function checkStatus()
    {
        $userId = $_SESSION['user_id'];
        $invoiceModel = $this->model('Invoice');
        $userModel = $this->model('User');
        $transModel = $this->model('Transaction');
        $settingModel = $this->model('Setting');

        $prefix = strtoupper($settingModel->get('bank_prefix', 'NAP'));
        $transId = $prefix . $userId;

        // Tìm invoice pending của user này
        $invoice = $invoiceModel->findOneWhere([
            'trans_id' => $transId,
            'status' => 0
        ]);

        if (!$invoice) {
            $this->json(['status' => 'info', 'message' => 'Không có hoá đơn nào đang chờ xử lý.']);
            return;
        }

        // Gọi API ngân hàng
        $apiUrl = env('BANK_API_URL', '');
        $apiToken = env('BANK_API_TOKEN', '');

        if (empty($apiToken) || $apiToken === 'your_api_token_here') {
            $this->json(['status' => 'warning', 'message' => 'Hệ thống đang bảo trì API ngân hàng. Vui lòng chờ cron tự động xử lý.']);
            return;
        }

        // Dùng cURL thay vì file_get_contents cho độ tin cậy cao hơn
        $fullUrl = $apiUrl . '/' . $apiToken;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || !$response) {
            $this->json(['status' => 'warning', 'message' => 'Không thể kết nối API ngân hàng. Vui lòng thử lại sau.']);
            return;
        }

        if ($httpCode !== 200) {
            $this->json(['status' => 'warning', 'message' => 'API ngân hàng trả về lỗi (HTTP ' . $httpCode . '). Thử lại sau.']);
            return;
        }

        $data = json_decode($response, true);
        if (!$data || ($data['status'] ?? '') !== 'success') {
            $this->json(['status' => 'warning', 'message' => 'API ngân hàng không phản hồi. Thử lại sau.']);
            return;
        }

        $transactions = $data['transactions'] ?? [];
        $found = false;

        foreach ($transactions as $tx) {
            if (($tx['type'] ?? '') !== 'IN')
                continue;

            $txId = $tx['transactionID'] ?? '';
            $txAmount = intval($tx['amount'] ?? 0);
            $txDesc = strtoupper($tx['description'] ?? '');

            // Đã xử lý rồi?
            if ($invoiceModel->isTransactionProcessed($txId))
                continue;

            // Khớp nội dung CK?
            if (strpos($txDesc, strtoupper($transId)) === false)
                continue;

            // Số tiền đủ?
            if ($txAmount < intval($invoice['pay']))
                continue;

            // ✅ Khớp! Cộng tiền
            $invoiceModel->update($invoice['id'], [
                'status' => 1,
                'tid' => $txId
            ]);

            $userModel->updateBalance($userId, intval($invoice['pay']));
            $newBalance = $userModel->getBalance($userId);
            $_SESSION['user_balance'] = $newBalance;

            $transModel->log(
                $userId,
                'deposit',
                intval($invoice['pay']),
                $newBalance,
                'Nạp tiền tự động - Mã GD: ' . $txId
            );

            // Gửi thông báo Telegram
            try {
                require_once BASE_PATH . '/app/Services/TelegramService.php';
                $telegram = new TelegramService();
                $telegram->notifyDeposit(
                    $userId,
                    $_SESSION['username'] ?? 'User#' . $userId,
                    intval($invoice['pay']),
                    $txId,
                    $newBalance
                );
            } catch (Exception $e) {
                // Không block flow nếu Telegram lỗi
            }

            $found = true;
            $this->json([
                'status' => 'success',
                'message' => 'Nạp thành công ' . formatMoney($invoice['pay']) . '! Số dư mới: ' . formatMoney($newBalance),
                'balance' => formatMoney($newBalance)
            ]);
            return;
        }

        if (!$found) {
            $this->json([
                'status' => 'pending',
                'message' => 'Chưa tìm thấy giao dịch khớp. Hãy đảm bảo nội dung CK là "' . $transId . '" và thử lại sau 1-2 phút.'
            ]);
        }
    }

    /**
     * Trang lịch sử nạp
     */
    public function history()
    {
        $invoiceModel = $this->model('Invoice');
        $userId = $_SESSION['user_id'];

        $invoices = $invoiceModel->getUserInvoices($userId);

        $this->view('user.deposit_history', [
            'pageTitle' => 'Lịch sử nạp tiền',
            'invoices' => $invoices
        ]);
    }

    /**
     * Xử lý nạp thẻ cào
     */
    public function cardDeposit()
    {
        header('Content-Type: application/json');

        if (!verifyCsrf()) {
            echo json_encode(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $type = strtoupper(trim($_POST['card_type'] ?? ''));
        $serial = trim($_POST['card_serial'] ?? '');
        $code = trim($_POST['card_code'] ?? '');
        $amount = intval($_POST['card_amount'] ?? 0);

        // Validate
        $validTypes = ['VIETTEL', 'MOBIFONE', 'VINAPHONE', 'VIETNAMOBILE', 'ZING', 'GARENA'];
        if (!in_array($type, $validTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Loại thẻ không hợp lệ']);
            return;
        }

        if (empty($serial) || strlen($serial) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Số serial không hợp lệ']);
            return;
        }

        if (empty($code) || strlen($code) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Mã thẻ không hợp lệ']);
            return;
        }

        $validAmounts = [10000, 20000, 30000, 50000, 100000, 200000, 300000, 500000, 1000000];
        if (!in_array($amount, $validAmounts)) {
            echo json_encode(['status' => 'error', 'message' => 'Mệnh giá không hợp lệ']);
            return;
        }

        // Check pending cards limit (max 5)
        $db = getDatabaseConnection();
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM card_lists WHERE user_id = ? AND status = 'Processing'");
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
            if (intval($row['cnt'] ?? 0) >= 5) {
                echo json_encode(['status' => 'error', 'message' => 'Bạn có quá nhiều thẻ đang chờ xử lý (tối đa 5).']);
                return;
            }
        } catch (Exception $e) {
            // Table might not exist
        }

        // Save card to card_lists table
        try {
            require_once BASE_PATH . '/app/Models/CardList.php';
            $cardModel = new CardList();
            $cardModel->createCard($userId, $type, $serial, $code, $amount);

            echo json_encode([
                'status' => 'success',
                'message' => 'Gửi thẻ thành công! Hệ thống đang xử lý, vui lòng chờ 1-3 phút.',
                'redirect' => url('/banking')
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
}

