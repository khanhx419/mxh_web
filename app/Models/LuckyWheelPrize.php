<?php

require_once BASE_PATH . '/core/Model.php';

class LuckyWheelPrize extends Model
{
    protected $table = 'lucky_wheel_prizes';

    /**
     * Lấy tất cả phần thưởng đang hoạt động
     */
    public function getActivePrizes()
    {
        return $this->findWhere(['status' => 1]);
    }

    /**
     * Thuật toán quay thưởng theo tỷ lệ phần trăm (probability)
     */
    public function spin()
    {
        $prizes = $this->getActivePrizes();
        if (empty($prizes))
            return null;

        $totalProbability = array_sum(array_column($prizes, 'probability'));
        $randomPoint = mt_rand(1, intval($totalProbability));

        $currentWeight = 0;
        foreach ($prizes as $prize) {
            $currentWeight += $prize['probability'];
            if ($randomPoint <= $currentWeight) {
                return $prize;
            }
        }

        return $prizes[0]; // Fallback
    }

    /**
     * Lưu lịch sử quay
     */
    public function logHistory($userId, $prize)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("
            INSERT INTO lucky_wheel_history (user_id, prize_id, prize_name, prize_value) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $prize['id'],
            $prize['name'],
            $prize['value']
        ]);
        return $db->lastInsertId();
    }
}
