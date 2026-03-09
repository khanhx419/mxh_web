<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class ShopController extends Controller
{

    public function index()
    {
        redirect('/shop/games');
    }

    /**
     * Trang shop acc game
     */
    public function games()
    {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        $categoryId = $_GET['category'] ?? null;
        $products = $productModel->getAvailable($categoryId);
        $categories = $categoryModel->getGameCategories();

        $this->view('user.shop_games', [
            'pageTitle' => 'Mua tài khoản Game',
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $categoryId,
        ]);
    }

    /**
     * Trang shop dịch vụ MXH
     */
    public function services()
    {
        $serviceModel = $this->model('Service');
        $categoryModel = $this->model('Category');

        $categoryId = $_GET['category'] ?? null;
        $services = $serviceModel->getActive($categoryId);
        $categories = $categoryModel->getSocialCategories();

        $this->view('user.shop_services', [
            'pageTitle' => 'Dịch vụ Mạng Xã Hội',
            'services' => $services,
            'categories' => $categories,
            'currentCategory' => $categoryId,
        ]);
    }

    /**
     * Chi tiết sản phẩm
     */
    public function productDetail($id)
    {
        $productModel = $this->model('Product');
        $product = $productModel->getDetail($id);

        if (!$product || $product['status'] !== 'available') {
            setFlash('danger', 'Sản phẩm không tồn tại hoặc đã bán.');
            redirect('/shop/games');
        }

        $this->view('user.product_detail', [
            'pageTitle' => $product['title'],
            'product' => $product,
        ]);
    }

    /**
     * Chi tiết dịch vụ
     */
    public function serviceDetail($id)
    {
        $serviceModel = $this->model('Service');
        $service = $serviceModel->getDetail($id);

        if (!$service || !$service['status']) {
            setFlash('danger', 'Dịch vụ không tồn tại hoặc đã tắt.');
            redirect('/shop/services');
        }

        $this->view('user.service_detail', [
            'pageTitle' => $service['name'],
            'service' => $service,
        ]);
    }

    /**
     * Mua acc game
     */
    public function buyProduct($id)
    {
        AuthMiddleware::requireLogin();

        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/product/' . $id);
        }

        $productModel = $this->model('Product');
        $product = $productModel->getDetail($id);

        if (!$product || $product['status'] !== 'available') {
            setFlash('danger', 'Sản phẩm không còn khả dụng.');
            redirect('/shop/games');
        }

        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $balance = $userModel->getBalance($userId);

        if ($balance < $product['price']) {
            setFlash('danger', 'Số dư không đủ! Cần ' . formatMoney($product['price']) . ', bạn có ' . formatMoney($balance));
            redirect('/product/' . $id);
        }

        // Trừ tiền
        $userModel->updateBalance($userId, -$product['price']);
        $newBalance = $userModel->getBalance($userId);
        $_SESSION['user_balance'] = $newBalance;

        // Đánh dấu sản phẩm đã bán
        $productModel->update($id, ['status' => 'sold']);

        // Tạo đơn hàng
        $orderModel = $this->model('Order');
        $orderId = $orderModel->createProductOrder($userId, $id, $product['price']);

        // Lưu account data vào order
        $orderModel->update($orderId, ['account_data' => $product['account_info']]);

        // Ghi giao dịch
        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $transModel->log($userId, 'purchase', $product['price'], $newBalance, 'Mua acc game: ' . $product['title']);

        setFlash('success', 'Mua thành công! Kiểm tra thông tin tài khoản trong mục "Đơn hàng".');
        redirect('/my-orders');
    }

    /**
     * Mua dịch vụ MXH - Tự động đẩy đơn lên web mẹ
     */
    public function buyService($id)
    {
        AuthMiddleware::requireLogin();

        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/service/' . $id);
        }

        $serviceModel = $this->model('Service');
        $service = $serviceModel->getDetail($id);

        if (!$service || !$service['status']) {
            setFlash('danger', 'Dịch vụ không khả dụng.');
            redirect('/shop/services');
        }

        $quantity = intval($_POST['quantity'] ?? 0);
        $targetLink = trim($_POST['target_link'] ?? '');

        if ($quantity < $service['min_quantity'] || $quantity > $service['max_quantity']) {
            setFlash('danger', 'Số lượng phải từ ' . number_format($service['min_quantity']) . ' đến ' . number_format($service['max_quantity']));
            redirect('/service/' . $id);
        }

        if (empty($targetLink)) {
            setFlash('danger', 'Vui lòng nhập link mục tiêu.');
            redirect('/service/' . $id);
        }

        $totalPrice = ($quantity / 1000) * $service['price_per_1000'];

        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $balance = $userModel->getBalance($userId);

        if ($balance < $totalPrice) {
            setFlash('danger', 'Số dư không đủ! Cần ' . formatMoney($totalPrice) . ', bạn có ' . formatMoney($balance));
            redirect('/service/' . $id);
        }

        // Trừ tiền
        $userModel->updateBalance($userId, -$totalPrice);
        $newBalance = $userModel->getBalance($userId);
        $_SESSION['user_balance'] = $newBalance;

        // ============================================================
        // Đẩy đơn lên web mẹ (nếu dịch vụ có smm_service_id)
        // ============================================================
        $smmOrderId = null;
        $smmServiceId = $service['smm_service_id'] ?? null;

        if ($smmServiceId) {
            require_once BASE_PATH . '/app/Services/SmmApiService.php';
            $smm = new SmmApiService();
            $apiResult = $smm->addOrder($smmServiceId, $targetLink, $quantity);

            if (isset($apiResult['order'])) {
                $smmOrderId = intval($apiResult['order']);
            } else {
                // API lỗi → vẫn tạo đơn nội bộ, admin xử lý thủ công
                $errorMsg = $apiResult['error'] ?? 'Unknown API error';
                error_log("[SMM API Error] Service #{$id}, User #{$userId}: {$errorMsg}");
            }
        }

        // Tạo đơn hàng (với smm_order_id nếu có)
        $orderModel = $this->model('Order');
        $orderModel->createServiceOrder($userId, $id, $quantity, $targetLink, $totalPrice, $smmOrderId);

        // Ghi giao dịch
        require_once BASE_PATH . '/app/Models/Transaction.php';
        $transModel = new Transaction();
        $transModel->log($userId, 'purchase', $totalPrice, $newBalance, 'Dịch vụ: ' . $service['name'] . ' x' . number_format($quantity));

        if ($smmOrderId) {
            setFlash('success', 'Đặt dịch vụ thành công! Đơn hàng #' . $smmOrderId . ' đang được xử lý tự động.');
        } else {
            setFlash('success', 'Đặt dịch vụ thành công! Đơn hàng đang chờ xử lý.');
        }
        redirect('/my-orders');
    }
}
