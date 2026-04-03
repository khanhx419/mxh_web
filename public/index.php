<?php

/**
 * Entry Point - Public/index.php
 * Tất cả request đều đi qua file này
 */

// Định nghĩa base path
define('BASE_PATH', dirname(__DIR__));

// Load helpers (cần load trước vì có hàm env())
require_once BASE_PATH . '/app/Helpers/helpers.php';

// Load config
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

// Load core
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';

// Start session (an toàn cho cPanel)
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Global Error Handler - chuyển PHP errors thành exceptions
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {

    // Khởi tạo Router
    $router = new Router();

    // =============================================
// ROUTES DEFINITION
// =============================================

    // --- Public Routes ---
    $router->get('/', 'HomeController@index');

    // --- Auth Routes ---
    $router->get('/login', 'AuthController@showLogin');
    $router->post('/login', 'AuthController@login');
    $router->get('/register', 'AuthController@showRegister');
    $router->post('/register', 'AuthController@register');
    $router->get('/logout', 'AuthController@logout');

    // --- User Routes ---
    $router->get('/shop', 'User/ShopController@index');
    $router->get('/shop/games', 'User/ShopController@games');
    $router->get('/shop/services', 'User/ShopController@services');
    $router->get('/product/{id}', 'User/ShopController@productDetail');
    $router->get('/service/{id}', 'User/ShopController@serviceDetail');
    $router->post('/order/product/{id}', 'User/ShopController@buyProduct');
    $router->post('/order/service/{id}', 'User/ShopController@buyService');
    $router->get('/my-orders', 'User/OrderController@index');
    $router->get('/profile', 'User/ProfileController@index');

    // --- Banking Routes ---
    $router->get('/banking', 'User/BankingController@index');
    $router->post('/banking/create', 'User/BankingController@createInvoice');
    $router->post('/banking/check-status', 'User/BankingController@checkStatus');
    $router->get('/banking/history', 'User/BankingController@history');
    $router->post('/banking/card-deposit', 'User/BankingController@cardDeposit');

    // --- Page & Feature Routes ---
    $router->get('/search', 'User/PageController@search');
    $router->get('/leaderboard', 'User/PageController@leaderboard');
    $router->get('/events', 'User/PageController@events');
    $router->get('/green-points', 'User/PageController@greenPoints');
    $router->get('/guide', 'User/PageController@guide');
    $router->get('/contact', 'User/PageController@contact');
    $router->post('/contact/send', 'User/PageController@submitContact');
    $router->get('/colors', 'User/PageController@colors');
    $router->post('/green-points/exchange', 'User/PageController@exchangeGreenPoints');
    $router->get('/lucky-wheel', 'User/LuckyWheelController@index');
    $router->post('/lucky-wheel/spin', 'User/LuckyWheelController@spin');
    $router->get('/mystery-bag', 'User/MysteryBagController@index');
    $router->post('/mystery-bag/open/{id}', 'User/MysteryBagController@open');
    $router->post('/mystery-bag/checkin', 'User/MysteryBagController@checkin');

    // --- Chess ---
    $router->get('/chess', 'User/ChessController@index');
    $router->post('/chess/record-win', 'User/ChessController@recordWin');

    // --- Admin Routes ---
    $router->get('/admin', 'Admin/DashboardController@index');

    // Admin - Categories
    $router->get('/admin/categories', 'Admin/CategoryController@index');
    $router->get('/admin/categories/create', 'Admin/CategoryController@create');
    $router->post('/admin/categories/store', 'Admin/CategoryController@store');
    $router->get('/admin/categories/edit/{id}', 'Admin/CategoryController@edit');
    $router->post('/admin/categories/update/{id}', 'Admin/CategoryController@update');
    $router->get('/admin/categories/delete/{id}', 'Admin/CategoryController@delete');

    // Admin - Products
    $router->get('/admin/products', 'Admin/ProductController@index');
    $router->get('/admin/products/create', 'Admin/ProductController@create');
    $router->post('/admin/products/store', 'Admin/ProductController@store');
    $router->get('/admin/products/edit/{id}', 'Admin/ProductController@edit');
    $router->post('/admin/products/update/{id}', 'Admin/ProductController@update');
    $router->get('/admin/products/delete/{id}', 'Admin/ProductController@delete');

    // Admin - Services
    $router->get('/admin/services', 'Admin/ServiceController@index');
    $router->get('/admin/services/create', 'Admin/ServiceController@create');
    $router->post('/admin/services/store', 'Admin/ServiceController@store');
    $router->get('/admin/services/edit/{id}', 'Admin/ServiceController@edit');
    $router->post('/admin/services/update/{id}', 'Admin/ServiceController@update');
    $router->get('/admin/services/delete/{id}', 'Admin/ServiceController@delete');

    // Admin - Orders
    $router->get('/admin/orders', 'Admin/OrderController@index');
    $router->get('/admin/orders/{id}', 'Admin/OrderController@show');
    $router->post('/admin/orders/update-status/{id}', 'Admin/OrderController@updateStatus');

    // Admin - Users
    $router->get('/admin/users', 'Admin/UserController@index');
    $router->get('/admin/users/{id}', 'Admin/UserController@show');
    $router->post('/admin/users/update-balance/{id}', 'Admin/UserController@updateBalance');
    $router->post('/admin/users/add-spins/{id}', 'Admin/UserController@addSpins');

    // Admin - Invoices (Nạp tiền)
    $router->get('/admin/invoices', 'Admin/InvoiceController@index');

    // Admin - Lucky Wheel
    $router->get('/admin/lucky-wheel', 'Admin/LuckyWheelController@index');
    $router->post('/admin/lucky-wheel/update', 'Admin/LuckyWheelController@update');

    // Admin - Mystery Bag
    $router->get('/admin/mystery-bag', 'Admin/MysteryBagController@index');
    $router->get('/admin/mystery-bag/create', 'Admin/MysteryBagController@create');
    $router->post('/admin/mystery-bag/store', 'Admin/MysteryBagController@store');
    $router->get('/admin/mystery-bag/edit/{id}', 'Admin/MysteryBagController@edit');
    $router->post('/admin/mystery-bag/update/{id}', 'Admin/MysteryBagController@update');
    $router->get('/admin/mystery-bag/delete/{id}', 'Admin/MysteryBagController@delete');

    // Admin - Mystery Bag Items (Accounts)
    $router->get('/admin/mystery-bag/{id}/items', 'Admin/MysteryBagController@items');
    $router->get('/admin/mystery-bag/{id}/items/add', 'Admin/MysteryBagController@addItem');
    $router->post('/admin/mystery-bag/{id}/items/store', 'Admin/MysteryBagController@storeItem');
    $router->get('/admin/mystery-bag/items/edit/{id}', 'Admin/MysteryBagController@editItem');
    $router->post('/admin/mystery-bag/items/update/{id}', 'Admin/MysteryBagController@updateItem');
    $router->get('/admin/mystery-bag/items/delete/{id}', 'Admin/MysteryBagController@deleteItem');
    $router->post('/admin/mystery-bag/{id}/items/bulk-import', 'Admin/MysteryBagController@bulkImportAccounts');

    // Admin - Events
    $router->get('/admin/events', 'Admin/EventController@index');
    $router->get('/admin/events/create', 'Admin/EventController@create');
    $router->post('/admin/events/store', 'Admin/EventController@store');
    $router->get('/admin/events/edit/{id}', 'Admin/EventController@edit');
    $router->post('/admin/events/update/{id}', 'Admin/EventController@update');
    $router->get('/admin/events/delete/{id}', 'Admin/EventController@delete');

    // Admin - Settings
    $router->get('/admin/settings', 'Admin/SettingsController@index');
    $router->post('/admin/settings/update', 'Admin/SettingsController@update');
    $router->get('/admin/settings/deposit', 'Admin/SettingsController@deposit');
    $router->post('/admin/settings/deposit/update', 'Admin/SettingsController@updateDeposit');

    // Dispatch
    $router->dispatch();

} catch (Throwable $e) {
    http_response_code(500);
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<div style="font-family:monospace;padding:20px;background:#1a1a2e;color:#e94560;">';
        echo '<h1>⚠ Application Error</h1>';
        echo '<p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
        echo '<p>File: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
        echo '<pre style="color:#0f3460;background:#e0e0e0;padding:15px;overflow:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        require_once BASE_PATH . '/views/errors/500.php';
    }
}
