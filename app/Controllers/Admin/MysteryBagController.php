<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class MysteryBagController extends Controller
{
    public function __construct() { AuthMiddleware::requireAdmin(); }

    public function index()
    {
        $bagModel = $this->model('MysteryBag');
        $bags = $bagModel->findAll();
        foreach ($bags as &$bag) { $bag['items'] = $bagModel->getItems($bag['id']); }

        $this->view('admin.mystery_bag.index', [
            'pageTitle' => 'Quản lý Túi Mù',
            'bags' => $bags
        ], 'admin');
    }

    public function create()
    {
        $this->view('admin.mystery_bag.form', [
            'pageTitle' => 'Thêm Túi Mù',
            'bag' => null
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag'); }

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid('bag_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], BASE_PATH . '/public/uploads/mystery_bags/' . $image);
        }

        $bagModel = $this->model('MysteryBag');
        $bagModel->create([
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'],
            'image' => $image,
            'status' => isset($_POST['status']) ? 1 : 0
        ]);

        setFlash('success', 'Thêm túi mù thành công');
        redirect('/admin/mystery-bag');
    }

    public function edit($id)
    {
        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($id);
        if (!$bag) { setFlash('danger', 'Không tìm thấy'); redirect('/admin/mystery-bag'); }

        $this->view('admin.mystery_bag.form', [
            'pageTitle' => 'Sửa Túi Mù',
            'bag' => $bag
        ], 'admin');
    }

    public function update($id)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag'); }

        $bagModel = $this->model('MysteryBag');
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'],
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $data['image'] = uniqid('bag_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], BASE_PATH . '/public/uploads/mystery_bags/' . $data['image']);
        }

        $bagModel->update($id, $data);
        setFlash('success', 'Cập nhật thành công');
        redirect('/admin/mystery-bag');
    }

    public function delete($id)
    {
        $bagModel = $this->model('MysteryBag');
        $bagModel->delete($id);
        setFlash('success', 'Đã xoá túi mù');
        redirect('/admin/mystery-bag');
    }
}
