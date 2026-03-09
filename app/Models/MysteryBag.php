<?php

require_once BASE_PATH . '/core/Model.php';

class MysteryBag extends Model
{
    protected $table = 'mystery_bags';

    /**
     * Lấy các túi mù đang hoạt động
     */
    public function getActiveBags()
    {
        return $this->findWhere(['status' => 1]);
    }

    /**
     * Lấy danh sách item bên trong một túi mù
     */
    public function getItems($bagId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM mystery_bag_items WHERE bag_id = ? ORDER BY probability DESC");
        $stmt->execute([$bagId]);
        return $stmt->fetchAll();
    }

    /**
     * Thuật toán mở túi mù
     */
    public function open($bagId)
    {
        $items = $this->getItems($bagId);
        if (empty($items))
            return null;

        $totalProbability = array_sum(array_column($items, 'probability'));
        $randomPoint = mt_rand(1, intval($totalProbability));

        $currentWeight = 0;
        foreach ($items as $item) {
            $currentWeight += $item['probability'];
            if ($randomPoint <= $currentWeight) {
                return $item;
            }
        }

        return $items[0]; // Fallback
    }

    /**
     * Lịch sử mở túi
     */
    public function logHistory($userId, $bagId, $item)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("
            INSERT INTO mystery_bag_history (user_id, bag_id, item_id, item_name, item_content) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $bagId,
            $item['id'],
            $item['name'],
            $item['content']
        ]);
        return $db->lastInsertId();
    }
}
