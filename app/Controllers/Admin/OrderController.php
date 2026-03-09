<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class OrderController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $orderModel = $this->model('Order');
        $orders = $orderModel->getAllWithDetails();

        $this->view('admin.orders.index', [
            'pageTitle' => 'Quản lý đơn hàng',
            'orders' => $orders,
        ], 'admin');
    }

    public function show($id)
    {
        $orderModel = $this->model('Order');
        $order = $orderModel->getDetail($id);

        if (!$order) {
            setFlash('danger', 'Đơn hàng không tồn tại.');
            redirect('/admin/orders');
        }

        $this->view('admin.orders.show', [
            'pageTitle' => 'Chi tiết đơn hàng #' . $id,
            'order' => $order,
        ], 'admin');
    }

    public function updateStatus($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/orders');
        }

        $status = $_POST['status'] ?? '';
        $orderModel = $this->model('Order');
        $order = $orderModel->findById($id);

        if (!$order) {
            setFlash('danger', 'Đơn hàng không tồn tại.');
            redirect('/admin/orders');
        }

        // Nếu hủy đơn, hoàn tiền cho user
        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            $userModel = $this->model('User');
            $userModel->updateBalance($order['user_id'], $order['total_price']);

            // Ghi transaction hoàn tiền
            require_once BASE_PATH . '/app/Models/Transaction.php';
            $transModel = new Transaction();
            $newBalance = $userModel->getBalance($order['user_id']);
            $transModel->log($order['user_id'], 'refund', $order['total_price'], $newBalance, 'Hoàn tiền đơn hàng #' . $id);

            // Nếu là sản phẩm, đặt lại status available
            if ($order['order_type'] === 'product' && $order['product_id']) {
                $productModel = $this->model('Product');
                $productModel->update($order['product_id'], ['status' => 'available']);
            }
        }

        // Nếu hoàn thành đơn mua acc, lưu account data vào order
        if ($status === 'completed' && $order['order_type'] === 'product' && $order['product_id']) {
            $productModel = $this->model('Product');
            $product = $productModel->findById($order['product_id']);
            if ($product) {
                $orderModel->update($id, ['account_data' => $product['account_info']]);
            }
        }

        $orderModel->update($id, ['status' => $status]);

        setFlash('success', 'Cập nhật trạng thái đơn hàng thành công!');
        redirect('/admin/orders/' . $id);
    }
}
