<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class ChessController extends Controller
{
    public function __construct()
    {
        AuthMiddleware::requireLogin();
    }

    /**
     * Hiển thị trang chơi cờ vua
     */
    public function index()
    {
        $userId = $_SESSION['user_id'];
        $this->ensureChessScoreColumn();

        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT chess_score FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $chessScore = intval($row['chess_score'] ?? 0);

        // Lấy stats per difficulty
        $userStats = ['easy' => 0, 'medium' => 0, 'hard' => 0, 'hell' => 0];
        try {
            $stmt = $db->prepare("SELECT difficulty, COUNT(*) as wins, SUM(points) as total_points FROM chess_wins WHERE user_id = ? GROUP BY difficulty");
            $stmt->execute([$userId]);
            $stats = $stmt->fetchAll();
            foreach ($stats as $s) {
                $userStats[$s['difficulty']] = [
                    'wins' => intval($s['wins']),
                    'points' => intval($s['total_points'])
                ];
            }
        } catch (Exception $e) {}

        // Mini leaderboard 
        $miniLeaderboard = [];
        try {
            foreach (['easy', 'medium', 'hard', 'hell'] as $diff) {
                $stmt = $db->prepare("
                    SELECT u.username, COUNT(cw.id) as wins, SUM(cw.points) as total_points
                    FROM chess_wins cw
                    JOIN users u ON cw.user_id = u.id
                    WHERE cw.difficulty = ?
                    GROUP BY cw.user_id
                    ORDER BY wins DESC
                    LIMIT 5
                ");
                $stmt->execute([$diff]);
                $miniLeaderboard[$diff] = $stmt->fetchAll();
            }
        } catch (Exception $e) {}

        $this->view('user.chess', [
            'pageTitle' => 'Cờ Vua AI — Stockfish',
            'chessScore' => $chessScore,
            'userStats' => $userStats,
            'miniLeaderboard' => $miniLeaderboard,
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ]);
    }

    /**
     * API ghi nhận thắng cờ — cộng điểm + log vào chess_wins
     */
    public function recordWin()
    {
        header('Content-Type: application/json');

        if (!verifyCsrf()) {
            echo json_encode(['status' => 'error', 'message' => 'Phiên hết hạn']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $difficulty = trim($_POST['difficulty'] ?? '');

        $pointsMap = [
            'easy'   => 1,
            'medium' => 3,
            'hard'   => 5,
            'hell'   => 10
        ];

        if (!isset($pointsMap[$difficulty])) {
            echo json_encode(['status' => 'error', 'message' => 'Mức độ không hợp lệ']);
            return;
        }

        $points = $pointsMap[$difficulty];

        $this->ensureChessScoreColumn();

        $db = getDatabaseConnection();

        // Update total score
        $stmt = $db->prepare("UPDATE users SET chess_score = chess_score + ? WHERE id = ?");
        $stmt->execute([$points, $userId]);

        // Log win to chess_wins table
        try {
            $stmt = $db->prepare("INSERT INTO chess_wins (user_id, difficulty, points) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $difficulty, $points]);
        } catch (Exception $e) {
            // Table might not exist yet
        }

        $stmt2 = $db->prepare("SELECT chess_score FROM users WHERE id = ?");
        $stmt2->execute([$userId]);
        $row = $stmt2->fetch();
        $newScore = intval($row['chess_score'] ?? 0);

        echo json_encode([
            'status'  => 'success',
            'message' => "Chúc mừng! Bạn được +{$points} điểm cờ vua!",
            'points'  => $points,
            'total'   => $newScore
        ]);
    }

    /**
     * Tự động thêm cột chess_score nếu chưa có
     */
    private function ensureChessScoreColumn()
    {
        static $checked = false;
        if ($checked) return;

        try {
            $db = getDatabaseConnection();
            $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'chess_score'");
            if ($stmt->rowCount() === 0) {
                $db->exec("ALTER TABLE users ADD COLUMN chess_score INT DEFAULT 0");
            }
        } catch (Exception $e) {
            // Ignore
        }

        $checked = true;
    }
}
