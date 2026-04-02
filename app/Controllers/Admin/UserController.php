<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class UserController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $userModel = $this->model('User');
        $users = $userModel->findAll('created_at DESC');

        $this->view('admin.users.index', [
            'pageTitle' => 'Quản lý người dùng',
            'users' => $users,
        ], 'admin');
    }

    public function show($id)
    {
        $userModel = $this->model('User');
        $user = $userModel->findById($id);

        if (!$user) {
            setFlash('danger', 'Người dùng không tồn tại.');
            redirect('/admin/users');
        }

        $orderModel = $this->model('Order');
        $orders = $orderModel->getUserOrders($id);

        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $transactions = $transModel->getUserTransactions($id);

        $checkinModel = $this->model('DailyCheckin');
        $freeSpins = $checkinModel->getFreeSpins($id);

        $this->view('admin.users.show', [
            'pageTitle' => 'Chi tiết người dùng',
            'user' => $user,
            'orders' => $orders,
            'transactions' => $transactions,
            'freeSpins' => $freeSpins,
        ], 'admin');
    }

    public function updateBalance($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/users/' . $id);
        }

        $amount = floatval($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            setFlash('danger', 'Số tiền không hợp lệ.');
            redirect('/admin/users/' . $id);
        }

        $userModel = $this->model('User');
        $userModel->updateBalance($id, $amount);

        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $newBalance = $userModel->getBalance($id);
        $transModel->log($id, 'deposit', $amount, $newBalance, 'Admin nạp tiền');

        setFlash('success', 'Nạp ' . formatMoney($amount) . ' thành công!');
        redirect('/admin/users/' . $id);
    }

    public function addSpins($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/users/' . $id);
        }

        $spins = intval($_POST['spins'] ?? 0);
        if ($spins <= 0) {
            setFlash('danger', 'Số lượt quay không hợp lệ.');
            redirect('/admin/users/' . $id);
        }

        $checkinModel = $this->model('DailyCheckin');
        $info = $checkinModel->getUserSpinInfo($id);
        $newSpins = intval($info['free_spins']) + $spins;

        $db = getDatabaseConnection();
        $stmt = $db->prepare("UPDATE user_free_spins SET free_spins=? WHERE user_id=?");
        $stmt->execute([$newSpins, $id]);

        setFlash('success', 'Đã thêm ' . $spins . ' lượt quay thành công!');
        redirect('/admin/users/' . $id);
    }
}
