<?php

require_once BASE_PATH . '/core/Controller.php';

class PageController extends Controller
{
    /**
     * Trang tìm kiếm
     */
    public function search()
    {
        $keyword = trim($_GET['q'] ?? '');

        $products = [];
        $services = [];

        if (!empty($keyword)) {
            $productModel = $this->model('Product');
            $serviceModel = $this->model('Service');

            // Tìm kiếm products
            $stmtP = $productModel->getDb()->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.title LIKE ? AND p.status = 'available'
            ");
            $stmtP->execute(["%$keyword%"]);
            $products = $stmtP->fetchAll();

            // Tìm kiếm services
            $stmtS = $serviceModel->getDb()->prepare("
                SELECT s.*, c.name as category_name 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                WHERE s.name LIKE ? AND s.status = 1
            ");
            $stmtS->execute(["%$keyword%"]);
            $services = $stmtS->fetchAll();
        }

        $this->view('user.search', [
            'pageTitle' => 'Tìm kiếm: ' . ($keyword ?: 'Tất cả'),
            'keyword' => $keyword,
            'products' => $products,
            'services' => $services
        ]);
    }

    /**
     * Bảng xếp hạng - Enhanced with tabs
     */
    public function leaderboard()
    {
        $userModel = $this->model('User');
        $db = $userModel->getDb();

        // Top Nạp (deposit)
        $stmt = $db->query("
            SELECT u.id, u.username, SUM(t.amount) as total_deposit 
            FROM users u 
            JOIN transactions t ON u.id = t.user_id 
            WHERE t.type = 'deposit' 
            GROUP BY u.id 
            ORDER BY total_deposit DESC 
            LIMIT 10
        ");
        $topDeposit = $stmt->fetchAll();

        // Top Chi Tiêu (spending)
        $stmt2 = $db->query("
            SELECT u.id, u.username, SUM(o.total_price) as total_spending
            FROM users u 
            JOIN orders o ON u.id = o.user_id 
            WHERE o.status = 'completed'
            GROUP BY u.id 
            ORDER BY total_spending DESC 
            LIMIT 10
        ");
        $topSpending = $stmt2->fetchAll();

        // Top Điểm Xanh
        $topPoints = [];
        try {
            $greenPointModel = $this->model('GreenPoint');
            $topPoints = $greenPointModel->getTopUsers(10);
        } catch (Exception $e) {
            // Table may not exist yet
        }

        // Top Cờ Vua (per difficulty)
        $topChess = ['easy' => [], 'medium' => [], 'hard' => [], 'hell' => []];
        try {
            foreach (['easy', 'medium', 'hard', 'hell'] as $diff) {
                $stmtC = $db->prepare("
                    SELECT u.username, COUNT(cw.id) as wins, SUM(cw.points) as total_points
                    FROM chess_wins cw
                    JOIN users u ON cw.user_id = u.id
                    WHERE cw.difficulty = ?
                    GROUP BY cw.user_id
                    ORDER BY wins DESC
                    LIMIT 10
                ");
                $stmtC->execute([$diff]);
                $topChess[$diff] = $stmtC->fetchAll();
            }
        } catch (Exception $e) {
            // Table may not exist yet
        }

        $this->view('user.leaderboard', [
            'pageTitle' => 'Bảng Xếp Hạng',
            'topDeposit' => $topDeposit,
            'topSpending' => $topSpending,
            'topPoints' => $topPoints,
            'topChess' => $topChess
        ]);
    }

    /**
     * Trang Sự kiện
     */
    public function events()
    {
        $activeEvents = [];
        $upcomingEvents = [];

        try {
            $eventModel = $this->model('Event');
            $activeEvents = $eventModel->getActive();
            $upcomingEvents = $eventModel->getUpcoming();
        } catch (Exception $e) {
            // Table may not exist yet
        }

        $this->view('user.events', [
            'pageTitle' => 'Sự Kiện',
            'activeEvents' => $activeEvents,
            'upcomingEvents' => $upcomingEvents
        ]);
    }

    /**
     * Trang Điểm xanh
     */
    public function greenPoints()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Vui lòng đăng nhập để xem điểm xanh.');
            redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $totalPoints = 0;
        $history = [];

        try {
            $greenPointModel = $this->model('GreenPoint');
            $totalPoints = $greenPointModel->getUserTotal($userId);
            $history = $greenPointModel->getHistory($userId);
        } catch (Exception $e) {
            // Table may not exist yet
        }

        $this->view('user.green_points', [
            'pageTitle' => 'Điểm Xanh',
            'totalPoints' => $totalPoints,
            'history' => $history
        ]);
    }

    /**
     * Trang Hướng dẫn
     */
    public function guide()
    {
        $this->view('user.guide', [
            'pageTitle' => 'Hướng dẫn sử dụng'
        ]);
    }

    /**
     * Trang Liên hệ
     */
    public function contact()
    {
        $this->view('user.contact', [
            'pageTitle' => 'Liên Hệ Chúng Tôi'
        ]);
    }

    /**
     * Trang Màu sắc (Theme colors)
     */
    public function colors()
    {
        $this->view('user.colors', [
            'pageTitle' => 'Màu sắc giao diện'
        ]);
    }

    /**
     * Xử lý form liên hệ
     */
    public function submitContact()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn.');
            redirect('/contact');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            setFlash('danger', 'Vui lòng nhập đầy đủ thông tin bắt buộc.');
            redirect('/contact');
        }

        // Lưu vào CSDL
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $db = $this->model('User')->getDb();
        $stmt = $db->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $name, $email, $subject, $message]);

        setFlash('success', 'Gửi lời nhắn thành công. Chúng tôi sẽ phản hồi sớm nhất!');
        redirect('/contact');
    }
}
