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
        $spinCost = $settingModel->get('wheel_spin_cost', 10000);

        // Lấy lượt free
        $freeSpins = 0;
        if (isLoggedIn()) {
            $checkinModel = $this->model('DailyCheckin');
            $freeSpins = $checkinModel->getFreeSpins($_SESSION['user_id']);
        }

        $this->view('user.lucky_wheel', [
            'pageTitle' => 'Vòng Quay May Mắn',
            'prizes' => $prizes,
            'spinCost' => $spinCost,
            'freeSpins' => $freeSpins
        ]);
    }

    /**
     * Xử lý quay thưởng (POST)
     */
    public function spin()
    {
        if (!verifyCsrf()) {
            $this->json(['status' => 'error', 'message' => 'Phiên làm việc hết hạn', 'new_csrf_token' => csrfToken()]);
            return;
        }

        if (!isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để quay', 'new_csrf_token' => csrfToken()]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $prizeModel = $this->model('LuckyWheelPrize');
        $settingModel = $this->model('Setting');
        $transModel = $this->model('Transaction');
        $checkinModel = $this->model('DailyCheckin');

        $spinCost = intval($settingModel->get('wheel_spin_cost', 10000));
        $userBalance = $userModel->getBalance($userId);
        $freeSpins = $checkinModel->getFreeSpins($userId);

        // Ưu tiên dùng lượt free nếu có
        $usedFreeSpin = false;
        if ($freeSpins > 0) {
            $checkinModel->useFreeSpin($userId);
            $usedFreeSpin = true;
        } elseif ($userBalance < $spinCost) {
            $this->json(['status' => 'error', 'message' => 'Bạn không đủ tiền để quay. Hãy điểm danh để nhận lượt free!', 'new_csrf_token' => csrfToken()]);
            return;
        } else {
            // Trừ tiền quay
            $userModel->updateBalance($userId, -$spinCost);
            $newBalance = $userModel->getBalance($userId);
            $transModel->log($userId, 'wheel_spin', $spinCost, $newBalance, 'Chơi vòng quay may mắn');
        }

        // Quay thưởng
        $prize = $prizeModel->spin();

        if (!$prize) {
            if (!$usedFreeSpin) {
                $userModel->updateBalance($userId, $spinCost);
            }
            $this->json(['status' => 'error', 'message' => 'Hệ thống vòng quay đang bảo trì', 'new_csrf_token' => csrfToken()]);
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
            $message .= ' (+' . formatMoney($prize['value']) . ')';
        }

        if ($usedFreeSpin) {
            $message .= ' [Lượt free]';
        }

        $_SESSION['user_balance'] = $userModel->getBalance($userId);

        $this->json([
            'status' => 'success',
            'prize_id' => $prize['id'],
            'prize_name' => $prize['name'],
            'message' => $message,
            'balance' => formatMoney($_SESSION['user_balance']),
            'free_spins' => $checkinModel->getFreeSpins($userId),
            'new_csrf_token' => csrfToken()
        ]);
    }
}
