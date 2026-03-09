<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class LuckyWheelController extends Controller
{
    /**
     * Hiển thị trang vòng quay
     */
    public function index()
    {
        $prizeModel = $this->model('LuckyWheelPrize');
        $settingModel = $this->model('Setting');

        $prizes = $prizeModel->getActivePrizes();
        $spinCost = $settingModel->get('wheel_spin_cost', 10000); // 10k 1 lượt mặc định

        // Lấy lịch sử trúng thưởng gần đây (của tất cả user)
        $db = $prizeModel->getDb();
        $stmt = $db->query("
            SELECT h.*, u.username 
            FROM lucky_wheel_history h 
            JOIN users u ON h.user_id = u.id 
            ORDER BY h.created_at DESC 
            LIMIT 10
        ");
        $history = $stmt->fetchAll();

        $this->view('user.lucky_wheel', [
            'pageTitle' => 'Vòng Quay May Mắn',
            'prizes' => $prizes,
            'spinCost' => $spinCost,
            'history' => $history
        ]);
    }

    /**
     * Xử lý quay thưởng (POST)
     */
    public function spin()
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        if (!isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để quay']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $prizeModel = $this->model('LuckyWheelPrize');
        $settingModel = $this->model('Setting');
        $transModel = $this->model('Transaction');

        $spinCost = intval($settingModel->get('wheel_spin_cost', 10000));
        $userBalance = $userModel->getBalance($userId);

        if ($userBalance < $spinCost) {
            $this->json(['status' => 'error', 'message' => 'Bạn không đủ tiền để quay. Vui lòng nạp thêm!']);
            return;
        }

        // Trừ tiền quay
        $userModel->updateBalance($userId, -$spinCost);
        $newBalance = $userModel->getBalance($userId);
        $transModel->log($userId, 'wheel_spin', $spinCost, $newBalance, 'Chơi vòng quay may mắn');

        // Quay thưởng
        $prize = $prizeModel->spin();

        if (!$prize) {
            // Hoàn tiền nếu lỗi
            $userModel->updateBalance($userId, $spinCost);
            $this->json(['status' => 'error', 'message' => 'Hệ thống vòng quay đang bảo trì']);
            return;
        }

        // Lưu lịch sử
        $prizeModel->logHistory($userId, $prize);

        // Trả thưởng
        $message = 'Bạn đã quay trúng: ' . $prize['name'];
        if ($prize['type'] == 'money') {
            $userModel->updateBalance($userId, $prize['value']);
            $finalBalance = $userModel->getBalance($userId);
            $transModel->log($userId, 'wheel_reward', $prize['value'], $finalBalance, 'Trúng thưởng vòng quay');
            $message .= ' (Số dư: +' . formatMoney($prize['value']) . ')';
        }

        // Cập nhật session balance mới nhất
        $_SESSION['user_balance'] = $userModel->getBalance($userId);

        $this->json([
            'status' => 'success',
            'prize_id' => $prize['id'],
            'prize_name' => $prize['name'],
            'message' => $message,
            'balance' => formatMoney($_SESSION['user_balance'])
        ]);
    }
}
