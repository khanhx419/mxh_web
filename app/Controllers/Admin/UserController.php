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

        $this->view('admin.users.show', [
            'pageTitle' => 'Chi tiết người dùng',
            'user' => $user,
            'orders' => $orders,
            'transactions' => $transactions,
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

        // Ghi transaction
        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $newBalance = $userModel->getBalance($id);
        $transModel->log($id, 'deposit', $amount, $newBalance, 'Admin nạp tiền');

        setFlash('success', 'Nạp ' . formatMoney($amount) . ' thành công!');
        redirect('/admin/users/' . $id);
    }
}
