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

        $this->view('home.index', [
            'pageTitle' => 'Trang chủ',
            'products' => array_slice($products, 0, 8),
            'services' => array_slice($services, 0, 8),
            'gameCategories' => $gameCategories,
            'socialCategories' => $socialCategories,
        ]);
    }
}
