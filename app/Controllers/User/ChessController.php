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

        // Đảm bảo user có cột chess_score
        $this->ensureChessScoreColumn();

        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT chess_score FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $chessScore = intval($row['chess_score'] ?? 0);

        $this->view('user.chess', [
            'pageTitle' => 'Cờ Vua AI — Stockfish',
            'chessScore' => $chessScore,
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ]);
    }

    /**
     * API ghi nhận thắng cờ — cộng điểm
     * POST /chess/record-win
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
        $stmt = $db->prepare("UPDATE users SET chess_score = chess_score + ? WHERE id = ?");
        $stmt->execute([$points, $userId]);

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
