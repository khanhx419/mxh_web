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

    // ===== ITEM / ACCOUNT MANAGEMENT =====

    public function items($bagId)
    {
        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($bagId);
        if (!$bag) { setFlash('danger', 'Không tìm thấy túi'); redirect('/admin/mystery-bag'); }

        $items = $bagModel->getItems($bagId);

        $this->view('admin.mystery_bag.items', [
            'pageTitle' => 'Tài khoản - ' . $bag['name'],
            'bag' => $bag,
            'items' => $items
        ], 'admin');
    }

    public function addItem($bagId)
    {
        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($bagId);
        if (!$bag) { setFlash('danger', 'Không tìm thấy túi'); redirect('/admin/mystery-bag'); }

        $this->view('admin.mystery_bag.item_form', [
            'pageTitle' => 'Thêm tài khoản - ' . $bag['name'],
            'bag' => $bag,
            'item' => null
        ], 'admin');
    }

    public function storeItem($bagId)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag/' . $bagId . '/items'); }

        $db = getDatabaseConnection();
        $stmt = $db->prepare("INSERT INTO mystery_bag_items (bag_id, name, value, content, probability) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $bagId,
            $_POST['name'],
            $_POST['value'] ?? 0,
            $_POST['content'] ?? '',
            $_POST['probability'] ?? 10
        ]);

        setFlash('success', 'Thêm tài khoản thành công');
        redirect('/admin/mystery-bag/' . $bagId . '/items');
    }

    public function editItem($itemId)
    {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM mystery_bag_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        if (!$item) { setFlash('danger', 'Không tìm thấy'); redirect('/admin/mystery-bag'); }

        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($item['bag_id']);

        $this->view('admin.mystery_bag.item_form', [
            'pageTitle' => 'Sửa tài khoản',
            'bag' => $bag,
            'item' => $item
        ], 'admin');
    }

    public function updateItem($itemId)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag'); }

        $db = getDatabaseConnection();
        
        // Get bag_id first
        $stmt = $db->prepare("SELECT bag_id FROM mystery_bag_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        if (!$item) { setFlash('danger', 'Không tìm thấy'); redirect('/admin/mystery-bag'); }

        $stmt = $db->prepare("UPDATE mystery_bag_items SET name = ?, value = ?, content = ?, probability = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['value'] ?? 0,
            $_POST['content'] ?? '',
            $_POST['probability'] ?? 10,
            $itemId
        ]);

        setFlash('success', 'Cập nhật thành công');
        redirect('/admin/mystery-bag/' . $item['bag_id'] . '/items');
    }

    public function deleteItem($itemId)
    {
        $db = getDatabaseConnection();
        
        $stmt = $db->prepare("SELECT bag_id FROM mystery_bag_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        $bagId = $item ? $item['bag_id'] : '';

        $stmt = $db->prepare("DELETE FROM mystery_bag_items WHERE id = ?");
        $stmt->execute([$itemId]);

        setFlash('success', 'Đã xoá tài khoản');
        redirect('/admin/mystery-bag/' . $bagId . '/items');
    }
}
