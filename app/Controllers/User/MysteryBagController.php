<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class MysteryBagController extends Controller
{
    /**
     * Trang túi mù
     */
    public function index()
    {
        $bagModel = $this->model('MysteryBag');
        $bags = $bagModel->getActiveBags();

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
     * Xử lý mở túi (CHỈ trả tiền - KHÔNG dùng free spin)
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

        // Chỉ chấp nhận thanh toán bằng số dư
        $userBalance = intval($userModel->getBalance($userId));
        if ($userBalance < $price) {
            $this->json(['status' => 'error', 'message' => 'Bạn không đủ tiền để mở túi này. Vui lòng nạp thêm!']);
            return;
        }

        // Trừ tiền mua túi
        $userModel->updateBalance($userId, -$price);
        $newBalance = $userModel->getBalance($userId);
        $transModel->log($userId, 'purchase', $price, $newBalance, 'Mua túi mù: ' . $bag['name']);

        // === LOGIC QUAY ===
        $items = $bagModel->getAvailableItems($bagId);

        if (!empty($items)) {
            // Item-based probability
            $wonItem = $bagModel->open($bagId);

            if ($wonItem) {
                $itemName = $wonItem['name'];
                $itemContent = $wonItem['content'];
                $itemValue = intval($wonItem['value']);

                if ($itemValue > 0) {
                    $userModel->updateBalance($userId, $itemValue);
                    $finalBalance = $userModel->getBalance($userId);
                    $transModel->log($userId, 'refund', $itemValue, $finalBalance, 'Phần thưởng túi mù: ' . $itemName);
                }

                $bagModel->logHistory($userId, $bagId, $wonItem);
            } else {
                $itemName = 'Chúc May Mắn Lần Sau';
                $itemContent = 'Rất tiếc, chúc bạn may mắn lần sau nhé! 🍀';
                $itemValue = 0;
                $bagModel->logHistoryCustom($userId, $bagId, $itemName, $itemContent);
            }
        } else {
            // Fallback: 80/20 logic
            $randomChance = mt_rand(1, 100);

            if ($randomChance <= 80) {
                $itemName = 'Chúc May Mắn Lần Sau';
                $itemContent = 'Rất tiếc, chúc bạn may mắn lần sau nhé! 🍀';
                $itemValue = 0;
                $bagModel->logHistoryCustom($userId, $bagId, $itemName, $itemContent);
            } else {
                $itemName = 'Thưởng ' . formatMoney($price);
                $itemContent = 'Bạn nhận được ' . formatMoney($price) . ' vào tài khoản!';
                $itemValue = $price;

                $userModel->updateBalance($userId, $itemValue);
                $finalBalance = $userModel->getBalance($userId);
                $transModel->log($userId, 'refund', $itemValue, $finalBalance, 'Phần thưởng túi mù: ' . $itemName);
                $bagModel->logHistoryCustom($userId, $bagId, $itemName, $itemContent);
            }
        }

        // Cập nhật session balance
        $_SESSION['user_balance'] = $userModel->getBalance($userId);

        $this->json([
            'status' => 'success',
            'item_name' => $itemName,
            'item_content' => $itemContent,
            'item_value' => $itemValue,
            'is_lucky' => ($itemValue > 0),
            'message' => ($itemValue > 0) 
                ? 'Chúc mừng! Bạn đã nhận được: ' . $itemName 
                : $itemContent,
            'balance' => formatMoney($_SESSION['user_balance']),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }
}
