<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class MysteryBagController extends Controller
{
    /**
     * Lấy danh sách túi mù
     */
    public function index()
    {
        $bagModel = $this->model('MysteryBag');
        $bags = $bagModel->getActiveBags();

        // Lấy chi tiết items cho mỗi túi để hiển thị khả năng rơi đồ
        foreach ($bags as &$bag) {
            $bag['items'] = $bagModel->getItems($bag['id']);
        }

        // Lấy lịch sử mở gần nhất
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
            'pageTitle' => 'Mở Túi Mù May Mắn',
            'bags' => $bags,
            'history' => $history
        ]);
    }

    /**
     * Xử lý mở túi
     */
    public function open($bagId)
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        if (!isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để mở túi mù']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $bagModel = $this->model('MysteryBag');
        $transModel = $this->model('Transaction');

        $bag = $bagModel->findById($bagId);
        if (!$bag || $bag['status'] != 1) {
            $this->json(['status' => 'error', 'message' => 'Túi mù không tồn tại hoặc đã bị khóa']);
            return;
        }

        $price = intval($bag['price']);
        $userBalance = intval($userModel->getBalance($userId));

        if ($userBalance < $price) {
            $this->json(['status' => 'error', 'message' => 'Bạn không đủ tiền để mở túi này. Vui lòng nạp thêm!']);
            return;
        }

        // Trừ tiền mua túi
        $userModel->updateBalance($userId, -$price);
        $newBalance = $userModel->getBalance($userId);
        $transModel->log($userId, 'mystery_bag_buy', $price, $newBalance, 'Mua túi mù: ' . $bag['name']);

        // Mở túi 
        $item = $bagModel->open($bagId);
        if (!$item) {
            // Lỗi thì hoàn tiền
            $userModel->updateBalance($userId, $price);
            $this->json(['status' => 'error', 'message' => 'Lỗi hệ thống mở túi mù. Đã hoàn tiền.']);
            return;
        }

        // Lưu lịch sử
        $bagModel->logHistory($userId, $bagId, $item);

        // Trả thưởng item. Ở form này, Túi mù thường chứa "Acc" hoặc item ảo giá trị (quy đổi ra tiền nội bộ hoặc thông báo nhận thủ công)
        // Nếu túi mù là Acc giá trị tiền, ta sẽ cộng lại Balance
        $itemValue = intval($item['value']);
        if ($itemValue > 0) {
            $userModel->updateBalance($userId, $itemValue);
            $finalBalance = $userModel->getBalance($userId);
            $transModel->log($userId, 'mystery_bag_reward', $itemValue, $finalBalance, 'Phần thưởng túi mù: ' . $item['name']);
        }

        // Cập nhật session balance 
        $_SESSION['user_balance'] = $userModel->getBalance($userId);

        $this->json([
            'status' => 'success',
            'item_name' => $item['name'],
            'item_content' => $item['content'],
            'item_value' => $itemValue,
            'message' => 'Chúc mừng! Bạn đã mở được: ' . $item['name'] . ' (' . $item['content'] . ')',
            'balance' => formatMoney($_SESSION['user_balance'])
        ]);
    }
}
