<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class LuckyWheelController extends Controller
{
    public function __construct() { AuthMiddleware::requireAdmin(); }

    public function index()
    {
        $db = getDatabaseConnection();
        $prizes = $db->query("SELECT * FROM lucky_wheel_prizes ORDER BY id")->fetchAll();
        $settings = $db->query("SELECT * FROM settings WHERE name = 'wheel_spin_cost'")->fetch();
        $spinCost = $settings['value'] ?? 10000;

        $this->view('admin.lucky_wheel.index', [
            'pageTitle' => 'Quản lý Vòng Quay',
            'prizes' => $prizes,
            'spinCost' => $spinCost
        ], 'admin');
    }

    public function update()
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/lucky-wheel'); }
        $db = getDatabaseConnection();

        // Update spin cost
        if (isset($_POST['spin_cost'])) {
            $db->prepare("UPDATE settings SET value = ? WHERE name = 'wheel_spin_cost'")
                ->execute([$_POST['spin_cost']]);
        }

        // Update prizes
        if (isset($_POST['prize_name']) && is_array($_POST['prize_name'])) {
            foreach ($_POST['prize_name'] as $id => $name) {
                $db->prepare("UPDATE lucky_wheel_prizes SET name=?, type=?, value=?, probability=?, color=?, status=? WHERE id=?")
                    ->execute([
                        $name,
                        $_POST['prize_type'][$id] ?? 'nothing',
                        $_POST['prize_value'][$id] ?? 0,
                        $_POST['prize_probability'][$id] ?? 10,
                        $_POST['prize_color'][$id] ?? '#6c63ff',
                        isset($_POST['prize_status'][$id]) ? 1 : 0,
                        $id
                    ]);
            }
        }

        setFlash('success', 'Cập nhật vòng quay thành công');
        redirect('/admin/lucky-wheel');
    }
}
