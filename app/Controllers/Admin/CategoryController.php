<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class CategoryController extends Controller
{

    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->findAll('type ASC, name ASC');

        $this->view('admin.categories.index', [
            'pageTitle' => 'Quản lý danh mục',
            'categories' => $categories,
        ], 'admin');
    }

    public function create()
    {
        $this->view('admin.categories.form', [
            'pageTitle' => 'Thêm danh mục',
            'category' => null,
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/categories');
        }

        $categoryModel = $this->model('Category');
        $categoryModel->create([
            'name' => trim($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? 'game',
            'icon' => trim($_POST['icon'] ?? 'fa-folder'),
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);

        setFlash('success', 'Thêm danh mục thành công!');
        redirect('/admin/categories');
    }

    public function edit($id)
    {
        $categoryModel = $this->model('Category');
        $category = $categoryModel->findById($id);

        if (!$category) {
            setFlash('danger', 'Danh mục không tồn tại.');
            redirect('/admin/categories');
        }

        $this->view('admin.categories.form', [
            'pageTitle' => 'Sửa danh mục',
            'category' => $category,
        ], 'admin');
    }

    public function update($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/admin/categories');
        }

        $categoryModel = $this->model('Category');
        $categoryModel->update($id, [
            'name' => trim($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? 'game',
            'icon' => trim($_POST['icon'] ?? 'fa-folder'),
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);

        setFlash('success', 'Cập nhật danh mục thành công!');
        redirect('/admin/categories');
    }

    public function delete($id)
    {
        $categoryModel = $this->model('Category');
        $categoryModel->delete($id);

        setFlash('success', 'Xóa danh mục thành công!');
        redirect('/admin/categories');
    }
}
