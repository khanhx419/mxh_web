<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class OrderController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    public function index()
    {
        $orderModel = $this->model('Order');
        $orders = $orderModel->getUserOrders($_SESSION['user_id']);

        $this->view('user.orders', [
            'pageTitle' => 'Đơn hàng của tôi',
            'orders' => $orders,
        ]);
    }
}
