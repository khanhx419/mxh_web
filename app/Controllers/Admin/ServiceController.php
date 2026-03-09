<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class ServiceController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $serviceModel = $this->model('Service');
        $services = $serviceModel->getAllWithCategory();

        $this->view('admin.services.index', [
            'pageTitle' => 'Quản lý dịch vụ MXH',
            'services' => $services,
        ], 'admin');
    }

    public function create()
    {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getSocialCategories();

        $this->view('admin.services.form', [
            'pageTitle' => 'Thêm dịch vụ MXH',
            'service' => null,
            'categories' => $categories,
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/services');
        }

        $serviceModel = $this->model('Service');
        $serviceModel->create([
            'category_id' => $_POST['category_id'] ?? 0,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price_per_1000' => floatval($_POST['price_per_1000'] ?? 0),
            'min_quantity' => intval($_POST['min_quantity'] ?? 100),
            'max_quantity' => intval($_POST['max_quantity'] ?? 100000),
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);

        setFlash('success', 'Thêm dịch vụ thành công!');
        redirect('/admin/services');
    }

    public function edit($id)
    {
        $serviceModel = $this->model('Service');
        $service = $serviceModel->findById($id);

        if (!$service) {
            setFlash('danger', 'Dịch vụ không tồn tại.');
            redirect('/admin/services');
        }

        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getSocialCategories();

        $this->view('admin.services.form', [
            'pageTitle' => 'Sửa dịch vụ MXH',
            'service' => $service,
            'categories' => $categories,
        ], 'admin');
    }

    public function update($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/services');
        }

        $serviceModel = $this->model('Service');
        $serviceModel->update($id, [
            'category_id' => $_POST['category_id'] ?? 0,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price_per_1000' => floatval($_POST['price_per_1000'] ?? 0),
            'min_quantity' => intval($_POST['min_quantity'] ?? 100),
            'max_quantity' => intval($_POST['max_quantity'] ?? 100000),
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);

        setFlash('success', 'Cập nhật dịch vụ thành công!');
        redirect('/admin/services');
    }

    public function delete($id)
    {
        $serviceModel = $this->model('Service');
        $serviceModel->delete($id);

        setFlash('success', 'Xóa dịch vụ thành công!');
        redirect('/admin/services');
    }
}
