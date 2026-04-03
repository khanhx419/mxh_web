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
     * Lấy items kèm phần trăm đã tính
     */
    public function getItemsWithPercentages($bagId)
    {
        $items = $this->getItems($bagId);
        $total = array_sum(array_column($items, 'probability'));
        foreach ($items as &$item) {
            $item['percentage'] = $total > 0 ? round($item['probability'] / $total * 100, 1) : 0;
        }
        return $items;
    }

    /**
     * Lấy items đang active (status=1)
     */
    public function getAvailableItems($bagId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM mystery_bag_items WHERE bag_id = ? AND (status = 1 OR status IS NULL) ORDER BY probability DESC");
        $stmt->execute([$bagId]);
        return $stmt->fetchAll();
    }

    /**
     * Thuật toán mở túi mù (chỉ dùng items đang active)
     */
    public function open($bagId)
    {
        $items = $this->getAvailableItems($bagId);
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
     * Update probability cho 1 item
     */
    public function updateItemProbability($itemId, $probability)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE mystery_bag_items SET probability = ? WHERE id = ?");
        return $stmt->execute([intval($probability), $itemId]);
    }

    /**
     * Bulk update probabilities cho tất cả items của 1 túi
     */
    public function bulkUpdateProbabilities($bagId, $probabilities)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE mystery_bag_items SET probability = ? WHERE id = ? AND bag_id = ?");
        foreach ($probabilities as $itemId => $prob) {
            $stmt->execute([intval($prob), $itemId, $bagId]);
        }
        return true;
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

    /**
     * Ghi lịch sử mở túi (custom - không cần item từ DB)
     */
    public function logHistoryCustom($userId, $bagId, $itemName, $itemContent)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("
            INSERT INTO mystery_bag_history (user_id, bag_id, item_id, item_name, item_content) 
            VALUES (?, ?, 0, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $bagId,
            $itemName,
            $itemContent
        ]);
        return $db->lastInsertId();
    }
}
