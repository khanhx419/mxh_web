<?php

require_once BASE_PATH . '/core/Model.php';

class GreenPoint extends Model
{
    protected $table = 'green_points';

    /**
     * Thêm điểm xanh cho user
     */
    public function add($userId, $points, $reason, $refType = null, $refId = null)
    {
        $id = $this->create([
            'user_id' => $userId,
            'points' => $points,
            'reason' => $reason,
            'reference_type' => $refType,
            'reference_id' => $refId
        ]);

        // Update user total
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE users SET green_points_total = green_points_total + ? WHERE id = ?");
        $stmt->execute([$points, $userId]);

        return $id;
    }

    /**
     * Lấy tổng điểm xanh của user
     */
    public function getUserTotal($userId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT green_points_total FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return intval($row['green_points_total'] ?? 0);
    }

    /**
     * Lấy lịch sử điểm xanh của user
     */
    public function getHistory($userId, $limit = 20)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Top users theo điểm xanh
     */
    public function getTopUsers($limit = 10)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.green_points_total 
            FROM users u 
            WHERE u.green_points_total > 0
            ORDER BY u.green_points_total DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
