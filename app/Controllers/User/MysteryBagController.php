<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class MysteryBagController extends Controller
{
    /**
     * Lấy danh sách túi mù + thông tin điểm danh
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

        // Lấy thông tin điểm danh
        $checkinData = [
            'current_day' => 0,
            'has_checked_in_today' => false,
            'free_spins' => 0,
            'checkins' => [],
            'cycle_start' => null,
        ];

        if (isLoggedIn()) {
            $checkinModel = $this->model('DailyCheckin');
            $userId = $_SESSION['user_id'];
            $spinInfo = $checkinModel->getUserSpinInfo($userId);
            $checkins = $checkinModel->getCurrentCycleCheckins($userId);

            $checkinData = [
                'current_day' => intval($spinInfo['current_day']),
                'has_checked_in_today' => $checkinModel->hasCheckedInToday($userId),
                'free_spins' => intval($spinInfo['free_spins']),
                'checkins' => $checkins,
                'cycle_start' => $spinInfo['cycle_start'],
            ];
        }

        $this->view('user.mystery_bag', [
            'pageTitle' => 'Mở Túi Mù May Mắn',
            'bags' => $bags,
            'history' => $history,
            'checkin' => $checkinData
        ]);
    }

    /**
     * API Điểm danh hôm nay
     */
    public function checkin()
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        if (!isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        $checkinModel = $this->model('DailyCheckin');
        $result = $checkinModel->checkin($_SESSION['user_id']);
        $result['csrf_token'] = $_SESSION['csrf_token'];

        $this->json($result);
    }

    /**
     * Xử lý mở túi (free hoặc trả tiền)
     * Logic: 80% ra "Chúc may mắn lần sau", 20% ra tiền bằng đúng giá túi
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
        $checkinModel = $this->model('DailyCheckin');

        $bag = $bagModel->findById($bagId);
        if (!$bag || $bag['status'] != 1) {
            $this->json(['status' => 'error', 'message' => 'Túi mù không tồn tại hoặc đã bị khóa']);
            return;
        }

        $price = intval($bag['price']);
        $isFree = isset($_POST['use_free_spin']) && $_POST['use_free_spin'] == '1';

        if ($isFree) {
            // Kiểm tra lượt quay miễn phí
            $freeSpins = $checkinModel->getFreeSpins($userId);
            if ($freeSpins <= 0) {
                $this->json(['status' => 'error', 'message' => 'Bạn không còn lượt quay miễn phí!']);
                return;
            }
            // Trừ lượt quay
            if (!$checkinModel->useFreeSpin($userId)) {
                $this->json(['status' => 'error', 'message' => 'Lỗi trừ lượt quay miễn phí']);
                return;
            }
        } else {
            // Trả tiền bình thường
            $userBalance = intval($userModel->getBalance($userId));
            if ($userBalance < $price) {
                $this->json(['status' => 'error', 'message' => 'Bạn không đủ tiền để mở túi này. Vui lòng nạp thêm!']);
                return;
            }
            // Trừ tiền mua túi
            $userModel->updateBalance($userId, -$price);
            $newBalance = $userModel->getBalance($userId);
            $transModel->log($userId, 'mystery_bag_buy', $price, $newBalance, 'Mua túi mù: ' . $bag['name']);
        }

        // === LOGIC QUAY ===
        // Nếu có items trong DB → dùng hệ thống xác suất theo items
        // Nếu không có items → fallback 80/20 cũ
        $items = $bagModel->getItems($bagId);

        if (!empty($items)) {
            // --- Item-based probability ---
            $wonItem = $bagModel->open($bagId);

            if ($wonItem) {
                $itemName = $wonItem['name'];
                $itemContent = $wonItem['content'];
                $itemValue = intval($wonItem['value']);

                if ($itemValue > 0) {
                    // Cộng tiền thưởng
                    $userModel->updateBalance($userId, $itemValue);
                    $finalBalance = $userModel->getBalance($userId);
                    $transModel->log($userId, 'mystery_bag_reward', $itemValue, $finalBalance, 'Phần thưởng túi mù: ' . $itemName);
                }

                // Lưu lịch sử
                $bagModel->logHistory($userId, $bagId, $wonItem);
            } else {
                $itemName = 'Chúc May Mắn Lần Sau';
                $itemContent = 'Rất tiếc, chúc bạn may mắn lần sau nhé! 🍀';
                $itemValue = 0;
                $bagModel->logHistoryCustom($userId, $bagId, $itemName, $itemContent);
            }
        } else {
            // --- Fallback: 80/20 logic ---
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
                $transModel->log($userId, 'mystery_bag_reward', $itemValue, $finalBalance, 'Phần thưởng túi mù: ' . $itemName);
                $bagModel->logHistoryCustom($userId, $bagId, $itemName, $itemContent);
            }
        }

        // Cập nhật session balance
        $_SESSION['user_balance'] = $userModel->getBalance($userId);

        // Lấy số lượt free còn lại
        $remainingSpins = $checkinModel->getFreeSpins($userId);

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
            'free_spins' => $remainingSpins,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }
}
