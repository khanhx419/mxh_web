<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class EventController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireAdmin();
    }

    public function index()
    {
        $eventModel = $this->model('Event');
        $events = $eventModel->getAll();

        $this->view('admin.events.index', [
            'pageTitle' => 'Quản lý Sự kiện',
            'events' => $events
        ], 'admin');
    }

    public function create()
    {
        $this->view('admin.events.form', [
            'pageTitle' => 'Thêm Sự kiện',
            'event' => null
        ], 'admin');
    }

    public function store()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên hết hạn');
            redirect('/admin/events');
        }

        $image = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public/uploads/events/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $image = uniqid('event_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        }

        $eventModel = $this->model('Event');
        $eventModel->create([
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'image' => $image,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'reward_type' => $_POST['reward_type'] ?? 'points',
            'reward_value' => $_POST['reward_value'] ?? 0,
            'status' => isset($_POST['status']) ? 1 : 0
        ]);

        setFlash('success', 'Thêm sự kiện thành công');
        redirect('/admin/events');
    }

    public function edit($id)
    {
        $eventModel = $this->model('Event');
        $event = $eventModel->findById($id);
        if (!$event) {
            setFlash('danger', 'Không tìm thấy sự kiện');
            redirect('/admin/events');
        }

        $this->view('admin.events.form', [
            'pageTitle' => 'Sửa Sự kiện',
            'event' => $event
        ], 'admin');
    }

    public function update($id)
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên hết hạn');
            redirect('/admin/events');
        }

        $eventModel = $this->model('Event');
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'reward_type' => $_POST['reward_type'] ?? 'points',
            'reward_value' => $_POST['reward_value'] ?? 0,
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public/uploads/events/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $data['image'] = uniqid('event_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $data['image']);
        }

        $eventModel->update($id, $data);
        setFlash('success', 'Cập nhật sự kiện thành công');
        redirect('/admin/events');
    }

    public function delete($id)
    {
        $eventModel = $this->model('Event');
        $eventModel->delete($id);
        setFlash('success', 'Đã xoá sự kiện');
        redirect('/admin/events');
    }
}
