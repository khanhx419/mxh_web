<?php

require_once BASE_PATH . '/core/Controller.php';

class HomeController extends Controller
{

    public function index()
    {
        $productModel = $this->model('Product');
        $serviceModel = $this->model('Service');
        $categoryModel = $this->model('Category');

        $products = $productModel->getAvailable();
        $services = $serviceModel->getActive();
        $gameCategories = $categoryModel->getGameCategories();
        $socialCategories = $categoryModel->getSocialCategories();

        // Lấy thông tin điểm danh
        $checkinData = [
            'current_day' => 0,
            'has_checked_in_today' => false,
            'free_spins' => 0,
        ];
        if (isLoggedIn()) {
            $checkinModel = $this->model('DailyCheckin');
            $userId = $_SESSION['user_id'];
            $spinInfo = $checkinModel->getUserSpinInfo($userId);
            $checkinData = [
                'current_day' => intval($spinInfo['current_day']),
                'has_checked_in_today' => $checkinModel->hasCheckedInToday($userId),
                'free_spins' => intval($spinInfo['free_spins']),
            ];
        }

        $this->view('home.index', [
            'pageTitle' => 'Trang chủ',
            'products' => array_slice($products, 0, 8),
            'services' => array_slice($services, 0, 8),
            'gameCategories' => $gameCategories,
            'socialCategories' => $socialCategories,
            'checkin' => $checkinData,
        ]);
    }
}
