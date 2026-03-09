<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class DashboardController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $userModel = $this->model('User');
        $productModel = $this->model('Product');
        $serviceModel = $this->model('Service');
        $orderModel = $this->model('Order');

        $totalUsers = $userModel->count(['role' => 'user']);
        $totalProducts = $productModel->countAvailable();
        $totalServices = $serviceModel->count(['status' => 1]);
        $totalOrders = $orderModel->count();
        $totalRevenue = $orderModel->totalRevenue();
        $recentOrders = array_slice($orderModel->getAllWithDetails(), 0, 10);

        $this->view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalServices' => $totalServices,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
        ], 'admin');
    }
}
