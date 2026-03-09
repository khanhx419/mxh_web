<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class ProfileController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function index()
    {
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        // Cập nhật session balance
        $_SESSION['user_balance'] = $user['balance'];

        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $transactions = $transModel->getUserTransactions($_SESSION['user_id']);

        $this->view('user.profile', [
            'pageTitle' => 'Tài khoản của tôi',
            'user' => $user,
            'transactions' => array_slice($transactions, 0, 20),
        ]);
    }
}
