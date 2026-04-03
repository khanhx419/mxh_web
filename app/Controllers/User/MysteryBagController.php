<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class MysteryBagController extends Controller
{
    /**
     * Trang túi mù — Hiển thị danh sách túi mù + stock
     */
    public function index()
    {
        $bagModel = $this->model('MysteryBag');
        $bags = $bagModel->getActiveBags();

        foreach ($bags as &$bag) {
            // Đếm stock còn lại cho mỗi túi
            $available = $bagModel->getAvailableItems($bag['id']);
            $bag['stock'] = count($available);
        }

        // Lấy lịch sử mua gần nhất (public feed)
        $db = $bagModel->getDb();
        $stmt = $db->query("
            SELECT h.*, u.username, b.name as bag_name 
            FROM mystery_bag_history h 
            JOIN users u ON h.user_id = u.id 
            JOIN mystery_bags b ON h.bag_id = b.id
            ORDER BY h.created_at DESC 
            LIMIT 10
        ");
        $history = $stmt->fetchAll();

        $this->view('user.mystery_bag', [
            'pageTitle' => 'Túi Mù - Mua Tài Khoản Ngẫu Nhiên',
            'bags' => $bags,
            'history' => $history
        ]);
    }

    /**
     * Xử lý mua túi mù
     * Logic: Trừ tiền → Random 1 acc từ stock → Đánh dấu "Sold" → Trả về info acc cho user
     */
    public function open($bagId)
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn. Vui lòng tải lại trang.']);
            return;
        }

        if (!isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để mua túi mù']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $bagModel = $this->model('MysteryBag');
        $transModel = $this->model('Transaction');

        // 1. Kiểm tra túi tồn tại & đang hoạt động
        $bag = $bagModel->findById($bagId);
        if (!$bag || $bag['status'] != 1) {
            $this->json(['status' => 'error', 'message' => 'Túi mù không tồn tại hoặc đã bị khóa']);
            return;
        }

        $price = intval($bag['price']);

        // 2. Kiểm tra số dư
        $userBalance = intval($userModel->getBalance($userId));
        if ($userBalance < $price) {
            $this->json(['status' => 'error', 'message' => 'Số dư không đủ. Vui lòng nạp thêm tiền!']);
            return;
        }

        // 3. Kiểm tra còn tài khoản trong kho không
        $availableItems = $bagModel->getAvailableItems($bagId);
        if (empty($availableItems)) {
            $this->json(['status' => 'error', 'message' => 'Túi mù đã hết hàng! Vui lòng chờ admin bổ sung thêm.']);
            return;
        }

        // 4. Trừ tiền
        $userModel->updateBalance($userId, -$price);
        $newBalance = $userModel->getBalance($userId);
        $transModel->log($userId, 'purchase', $price, $newBalance, 'Mua túi mù: ' . $bag['name']);

        // 5. Random 1 tài khoản & đánh dấu đã bán (status=0)
        $soldItem = $bagModel->open($bagId);

        // 6. Ghi lịch sử mua
        $bagModel->logHistory($userId, $bagId, $soldItem);

        // 7. Cập nhật session
        $_SESSION['user_balance'] = $newBalance;

        // 8. Parse account info để hiển thị đẹp cho user
        $content = $soldItem['content'] ?? '';
        $accountInfo = $this->parseAccountContent($content);

        // 9. Trả kết quả
        $this->json([
            'status' => 'success',
            'message' => 'Mua thành công! Đây là tài khoản của bạn:',
            'item_name' => $soldItem['name'],
            'account' => $accountInfo,
            'raw_content' => $content,
            'balance' => formatMoney($newBalance),
            'stock_left' => count($availableItems) - 1,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Parse nội dung tài khoản thành các trường riêng
     */
    private function parseAccountContent($content)
    {
        $result = [
            'username' => '',
            'password' => '',
            'email' => '',
            'extra' => ''
        ];

        $lines = explode("\n", $content);
        $extraLines = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (stripos($line, 'Tài khoản:') === 0) {
                $result['username'] = trim(substr($line, strlen('Tài khoản:')));
            } elseif (stripos($line, 'Mật khẩu:') === 0) {
                $result['password'] = trim(substr($line, strlen('Mật khẩu:')));
            } elseif (stripos($line, 'Email:') === 0) {
                $result['email'] = trim(substr($line, strlen('Email:')));
            } else {
                $extraLines[] = $line;
            }
        }

        $result['extra'] = implode("\n", $extraLines);
        return $result;
    }

    /**
     * Điểm danh hàng ngày (API)
     */
    public function checkin()
    {
        header('Content-Type: application/json');

        if (!verifyCsrf()) {
            echo json_encode(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $checkinModel = $this->model('DailyCheckin');
        $result = $checkinModel->checkin($userId);
        $result['csrf_token'] = $_SESSION['csrf_token'] ?? '';

        echo json_encode($result);
    }
}
