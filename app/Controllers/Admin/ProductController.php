<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class ProductController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $productModel = $this->model('Product');
        $products = $productModel->getAllWithCategory();

        $this->view('admin.products.index', [
            'pageTitle' => 'Quản lý tài khoản Game',
            'products' => $products,
        ], 'admin');
    }

    public function create()
    {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getGameCategories();

        $this->view('admin.products.form', [
            'pageTitle' => 'Thêm tài khoản Game',
            'product' => null,
            'categories' => $categories,
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/products');
        }

        $data = [
            'category_id' => $_POST['category_id'] ?? 0,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'account_info' => trim($_POST['account_info'] ?? ''),
            'status' => 'available',
        ];

        // Upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data['image'] = $this->uploadImage($_FILES['image']);
        }

        $productModel = $this->model('Product');
        $productModel->create($data);

        setFlash('success', 'Thêm tài khoản game thành công!');
        redirect('/admin/products');
    }

    public function edit($id)
    {
        $productModel = $this->model('Product');
        $product = $productModel->findById($id);

        if (!$product) {
            setFlash('danger', 'Sản phẩm không tồn tại.');
            redirect('/admin/products');
        }

        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getGameCategories();

        $this->view('admin.products.form', [
            'pageTitle' => 'Sửa tài khoản Game',
            'product' => $product,
            'categories' => $categories,
        ], 'admin');
    }

    public function update($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/products');
        }

        $data = [
            'category_id' => $_POST['category_id'] ?? 0,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'account_info' => trim($_POST['account_info'] ?? ''),
            'status' => $_POST['status'] ?? 'available',
        ];

        // Upload ảnh mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data['image'] = $this->uploadImage($_FILES['image']);
        }

        $productModel = $this->model('Product');
        $productModel->update($id, $data);

        setFlash('success', 'Cập nhật thành công!');
        redirect('/admin/products');
    }

    public function delete($id)
    {
        $productModel = $this->model('Product');
        $productModel->delete($id);

        setFlash('success', 'Xóa sản phẩm thành công!');
        redirect('/admin/products');
    }

    private function uploadImage($file)
    {
        $uploadDir = BASE_PATH . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

        return $filename;
    }
}
