<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class SettingsController extends Controller
{
    public function __construct() { AuthMiddleware::requireAdmin(); }

    public function index()
    {
        $db = getDatabaseConnection();
        $rows = $db->query("SELECT * FROM settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) { $settings[$r['name']] = $r['value']; }

        $this->view('admin.settings.index', [
            'pageTitle' => 'Cài Đặt Chung',
            'settings' => $settings
        ], 'admin');
    }

    public function update()
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/settings'); }

        $db = getDatabaseConnection();
        $fields = [
            'bank_prefix', 'bank_acc_name', 'bank_acc_number', 'bank_name',
            'site_notice', 'wheel_spin_cost',
            'checkin_spins_per_day', 'checkin_bonus_day7', 'checkin_green_points'
        ];

        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$f, $_POST[$f], $_POST[$f]]);
            }
        }

        setFlash('success', 'Cập nhật cài đặt thành công');
        redirect('/admin/settings');
    }
}
