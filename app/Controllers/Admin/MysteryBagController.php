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
            $dir = BASE_PATH . '/public/uploads/mystery_bags/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $dir . $image);
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
            $dir = BASE_PATH . '/public/uploads/mystery_bags/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $dir . $data['image']);
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

        $items = $bagModel->getItemsWithPercentages($bagId);

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
        $stmt = $db->prepare("INSERT INTO mystery_bag_items (bag_id, name, value, content, probability, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $bagId,
            $_POST['name'],
            $_POST['value'] ?? 0,
            $_POST['content'] ?? '',
            $_POST['probability'] ?? 10,
            isset($_POST['status']) ? 1 : 1
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
        
        $stmt = $db->prepare("SELECT bag_id FROM mystery_bag_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        if (!$item) { setFlash('danger', 'Không tìm thấy'); redirect('/admin/mystery-bag'); }

        $stmt = $db->prepare("UPDATE mystery_bag_items SET name = ?, value = ?, content = ?, probability = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['value'] ?? 0,
            $_POST['content'] ?? '',
            $_POST['probability'] ?? 10,
            isset($_POST['status']) ? 1 : 0,
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

    // ===== PROBABILITY MANAGEMENT =====

    public function updateProbabilities($bagId)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag/' . $bagId . '/items'); }

        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($bagId);
        if (!$bag) { setFlash('danger', 'Không tìm thấy túi'); redirect('/admin/mystery-bag'); }

        $probabilities = $_POST['probability'] ?? [];
        if (!empty($probabilities)) {
            $bagModel->bulkUpdateProbabilities($bagId, $probabilities);
        }

        setFlash('success', 'Cập nhật xác suất thành công');
        redirect('/admin/mystery-bag/' . $bagId . '/items');
    }

    public function bulkAddItems($bagId)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag/' . $bagId . '/items'); }

        $bulkData = trim($_POST['bulk_items'] ?? '');
        if (empty($bulkData)) {
            setFlash('danger', 'Không có dữ liệu');
            redirect('/admin/mystery-bag/' . $bagId . '/items');
        }

        $db = getDatabaseConnection();
        $lines = explode("\n", $bulkData);
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Format: name|value|content|probability
            $parts = explode('|', $line);
            $name = trim($parts[0] ?? 'Item mới');
            $value = intval($parts[1] ?? 0);
            $content = trim($parts[2] ?? '');
            $probability = intval($parts[3] ?? 10);

            $stmt = $db->prepare("INSERT INTO mystery_bag_items (bag_id, name, value, content, probability, status) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$bagId, $name, $value, $content, $probability]);
            $count++;
        }

        setFlash('success', "Đã thêm {$count} tài khoản");
        redirect('/admin/mystery-bag/' . $bagId . '/items');
    }

    // ===== BULK IMPORT ACCOUNTS (username|password|email) =====

    public function bulkImportAccounts($bagId)
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/mystery-bag/' . $bagId . '/items'); }

        $bagModel = $this->model('MysteryBag');
        $bag = $bagModel->findById($bagId);
        if (!$bag) { setFlash('danger', 'Không tìm thấy túi'); redirect('/admin/mystery-bag'); }

        $rawText = trim($_POST['bulk_accounts'] ?? '');
        $lines = [];

        // Source 1: JSON/TXT file upload
        if (!empty($_FILES['bulk_file']['name']) && $_FILES['bulk_file']['error'] === UPLOAD_ERR_OK) {
            $fileContent = file_get_contents($_FILES['bulk_file']['tmp_name']);
            $fileContent = trim($fileContent);

            // Try parsing as JSON first
            $jsonData = json_decode($fileContent, true);
            if (is_array($jsonData)) {
                foreach ($jsonData as $entry) {
                    if (is_string($entry)) {
                        // JSON array of strings: ["user|pass|email", ...]
                        $lines[] = trim($entry);
                    } elseif (is_array($entry)) {
                        // JSON array of objects: [{"username":"..","password":"..","email":".."}]
                        $u = $entry['username'] ?? $entry['user'] ?? '';
                        $p = $entry['password'] ?? $entry['pass'] ?? '';
                        $e = $entry['email'] ?? $entry['mail'] ?? '';
                        if (!empty($u)) {
                            $lines[] = "{$u}|{$p}|{$e}";
                        }
                    }
                }
            } else {
                // Plain text file — each line is username|password|email
                $fileLines = explode("\n", $fileContent);
                foreach ($fileLines as $fl) {
                    $fl = trim($fl);
                    if (!empty($fl)) $lines[] = $fl;
                }
            }
        }

        // Source 2: Pasted raw text
        if (!empty($rawText)) {
            $pastedLines = explode("\n", $rawText);
            foreach ($pastedLines as $pl) {
                $pl = trim($pl);
                if (!empty($pl)) $lines[] = $pl;
            }
        }

        if (empty($lines)) {
            setFlash('danger', 'Không có dữ liệu tài khoản để import');
            redirect('/admin/mystery-bag/' . $bagId . '/items');
        }

        // Deduplicate
        $lines = array_unique($lines);

        $db = getDatabaseConnection();
        $defaultProb = intval($_POST['default_probability'] ?? 10);
        $count = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $username = trim($parts[0] ?? '');
            $password = trim($parts[1] ?? '');
            $email    = trim($parts[2] ?? '');

            if (empty($username)) { $skipped++; continue; }

            // Build content string
            $content = "Tài khoản: {$username}";
            if (!empty($password)) $content .= "\nMật khẩu: {$password}";
            if (!empty($email))    $content .= "\nEmail: {$email}";

            $stmt = $db->prepare("INSERT INTO mystery_bag_items (bag_id, name, value, content, probability, status) VALUES (?, ?, 0, ?, ?, 1)");
            $stmt->execute([$bagId, $username, $content, $defaultProb]);
            $count++;
        }

        $msg = "Đã import {$count} tài khoản thành công";
        if ($skipped > 0) $msg .= " ({$skipped} dòng bỏ qua)";
        setFlash('success', $msg);
        redirect('/admin/mystery-bag/' . $bagId . '/items');
    }
}
