<?php

require_once BASE_PATH . '/core/Model.php';

class DailyCheckin extends Model
{
    protected $table = 'daily_checkin';

    /**
     * Lấy thông tin lượt quay free của user
     */
    public function getUserSpinInfo($userId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM user_free_spins WHERE user_id = ?");
        $stmt->execute([$userId]);
        $info = $stmt->fetch();

        if (!$info) {
            // Tạo record mới
            $db->prepare("INSERT INTO user_free_spins (user_id, free_spins, current_day) VALUES (?, 0, 0)")
                ->execute([$userId]);
            return [
                'user_id' => $userId,
                'free_spins' => 0,
                'last_checkin_date' => null,
                'cycle_start' => null,
                'current_day' => 0
            ];
        }

        return $info;
    }

    /**
     * Kiểm tra hôm nay đã điểm danh chưa
     */
    public function hasCheckedInToday($userId)
    {
        $info = $this->getUserSpinInfo($userId);
        $today = date('Y-m-d');
        return $info['last_checkin_date'] === $today;
    }

    /**
     * Lấy lịch sử điểm danh trong chu kỳ hiện tại
     */
    public function getCurrentCycleCheckins($userId)
    {
        $info = $this->getUserSpinInfo($userId);
        
        if (!$info['cycle_start']) {
            return [];
        }

        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT * FROM daily_checkin 
            WHERE user_id = ? AND cycle_start = ?
            ORDER BY day_number ASC
        ");
        $stmt->execute([$userId, $info['cycle_start']]);
        return $stmt->fetchAll();
    }

    /**
     * Thực hiện điểm danh
     */
    public function checkin($userId)
    {
        $info = $this->getUserSpinInfo($userId);
        $today = date('Y-m-d');
        $db = $this->getDb();

        // Đã điểm danh hôm nay rồi
        if ($info['last_checkin_date'] === $today) {
            return ['status' => 'error', 'message' => 'Bạn đã điểm danh hôm nay rồi!'];
        }

        // Lấy settings từ DB
        $settings = [];
        $rows = $db->query("SELECT name, value FROM settings WHERE name IN ('checkin_spins_per_day','checkin_bonus_day7','checkin_green_points')")->fetchAll();
        foreach ($rows as $r) { $settings[$r['name']] = $r['value']; }
        $spinsPerDay = intval($settings['checkin_spins_per_day'] ?? 1);
        $bonusDay7 = intval($settings['checkin_bonus_day7'] ?? 3);
        $greenPoints = intval($settings['checkin_green_points'] ?? 5);

        $currentDay = intval($info['current_day']);
        $cycleStart = $info['cycle_start'];

        // Reset chu kỳ nếu đủ 7 ngày hoặc chưa bắt đầu
        if ($currentDay >= 7 || !$cycleStart) {
            $currentDay = 0;
            $cycleStart = $today;
        }

        $currentDay++;
        $spinsEarned = $spinsPerDay;

        // Ngày thứ 7 nhận bonus
        if ($currentDay == 7) {
            $spinsEarned = $bonusDay7;
        }

        // Ghi điểm danh
        $stmt = $db->prepare("INSERT INTO daily_checkin (user_id, day_number, cycle_start, free_spins_earned) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $currentDay, $cycleStart, $spinsEarned]);

        // Cập nhật lượt quay
        $newSpins = intval($info['free_spins']) + $spinsEarned;
        $stmt = $db->prepare("UPDATE user_free_spins SET free_spins=?, last_checkin_date=?, cycle_start=?, current_day=? WHERE user_id=?");
        $stmt->execute([$newSpins, $today, $cycleStart, $currentDay, $userId]);

        // Cộng điểm xanh (Green Points)
        if ($greenPoints > 0) {
            $db->prepare("UPDATE users SET green_points_total = green_points_total + ? WHERE id = ?")
                ->execute([$greenPoints, $userId]);
            $db->prepare("INSERT INTO green_points (user_id, points, reason, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$userId, $greenPoints, 'Điểm danh ngày ' . $currentDay . '/7']);
        }

        $msg = "Ngày {$currentDay}/7 ✓ +{$spinsEarned} lượt quay";
        if ($greenPoints > 0) $msg .= " +{$greenPoints} điểm xanh";

        return [
            'status' => 'success',
            'message' => $msg,
            'day' => $currentDay,
            'spins_earned' => $spinsEarned,
            'total_spins' => $newSpins,
            'green_points' => $greenPoints
        ];
    }

    /**
     * Sử dụng 1 lượt quay miễn phí
     */
    public function useFreeSpin($userId)
    {
        $info = $this->getUserSpinInfo($userId);
        
        if (intval($info['free_spins']) <= 0) {
            return false;
        }

        $db = $this->getDb();
        $stmt = $db->prepare("
            UPDATE user_free_spins 
            SET free_spins = free_spins - 1 
            WHERE user_id = ? AND free_spins > 0
        ");
        $stmt->execute([$userId]);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Lấy số lượt quay free còn lại
     */
    public function getFreeSpins($userId)
    {
        $info = $this->getUserSpinInfo($userId);
        return intval($info['free_spins']);
    }
}
