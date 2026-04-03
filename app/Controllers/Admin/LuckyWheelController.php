<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class LuckyWheelController extends Controller
{
    public function __construct() { AuthMiddleware::requireAdmin(); }

    public function index()
    {
        $prizeModel = $this->model('LuckyWheelPrize');
        $prizes = $prizeModel->getPrizesWithPercentages();
        $db = getDatabaseConnection();
        $settings = $db->query("SELECT * FROM settings WHERE name = 'wheel_spin_cost'")->fetch();
        $spinCost = $settings['value'] ?? 10000;

        $this->view('admin.lucky_wheel.index', [
            'pageTitle' => 'Quản lý Vòng Quay',
            'prizes' => $prizes,
            'spinCost' => $spinCost,
            'totalProbability' => $prizeModel->getTotalProbability()
        ], 'admin');
    }

    public function create()
    {
        $this->view('admin.lucky_wheel.form', [
            'pageTitle' => 'Thêm Giải Thưởng',
            'prize' => null
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) { setFlash('danger', 'Phiên hết hạn'); redirect('/admin/lucky-wheel'); }
        $db = getDatabaseConnection();

        $stmt = $db->prepare("INSERT INTO lucky_wheel_prizes (name, type, value, probability, color, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'] ?? 'Giải mới',
            $_POST['type'] ?? 'nothing',
            $_POST['value'] ?? 0,
            $_POST['probability'] ?? 10,
            $_POST['color'] ?? '#6c63ff',
            isset($_POST['status']) ? 1 : 0
        ]);

        setFlash('success', 'Thêm giải thưởng thành công');
        redirect('/admin/lucky-wheel');
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

    public function delete($id)
    {
        $db = getDatabaseConnection();
        $db->prepare("DELETE FROM lucky_wheel_prizes WHERE id = ?")->execute([$id]);
        setFlash('success', 'Đã xoá giải thưởng');
        redirect('/admin/lucky-wheel');
    }
}
