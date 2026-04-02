<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class SettingsController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $db = getDatabaseConnection();
        $rows = $db->query("SELECT * FROM settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['name']] = $r['value'];
        }

        $this->view('admin.settings.index', [
            'pageTitle' => 'Cài Đặt Chung',
            'settings' => $settings
        ], 'admin');
    }

    public function update()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên hết hạn');
            redirect('/admin/settings');
        }

        $db = getDatabaseConnection();
        $fields = [
            'bank_prefix', 'bank_acc_name', 'bank_acc_number', 'bank_name',
            'site_notice', 'wheel_spin_cost',
            'checkin_spins_per_day', 'checkin_bonus_day7', 'checkin_green_points',
            'deposit_notice',
            'popup_enabled', 'popup_owner_name', 'popup_phone', 'popup_notice_text'
        ];

        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$f, $_POST[$f], $_POST[$f]]);
            }
        }

        // Xử lý logo từ Cropper.js (base64 data)
        $logoData = $_POST['site_logo_data'] ?? '';
        if (!empty($logoData) && strpos($logoData, 'data:image/') === 0) {
            $uploadDir = BASE_PATH . '/public/uploads/logo/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Decode base64
            $parts = explode(',', $logoData, 2);
            $imageData = base64_decode($parts[1]);
            $fileName = 'logo_' . time() . '.png';
            $targetPath = $uploadDir . $fileName;

            if (file_put_contents($targetPath, $imageData)) {
                $dbPath = 'uploads/logo/' . $fileName;
                $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES ('site_logo', ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$dbPath, $dbPath]);
            }
        }

        setFlash('success', 'Cập nhật cài đặt thành công');
        redirect('/admin/settings');
    }
}
