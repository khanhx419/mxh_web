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
        $stmt = $db->prepare("SELECT * FROM mystery_bag_items WHERE bag_id = ? ORDER BY id DESC");
        $stmt->execute([$bagId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy items đang active (status=1) — chưa được phát
     */
    public function getAvailableItems($bagId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM mystery_bag_items WHERE bag_id = ? AND (status = 1 OR status IS NULL) ORDER BY id DESC");
        $stmt->execute([$bagId]);
        return $stmt->fetchAll();
    }

    /**
     * Mở túi mù — Random thuần (không dùng xác suất)
     * Chọn ngẫu nhiên 1 tài khoản từ danh sách available, rồi tắt nó (đã phát)
     */
    public function open($bagId)
    {
        $items = $this->getAvailableItems($bagId);
        if (empty($items))
            return null;

        // Random thuần — chọn bất kỳ 1 item
        $index = array_rand($items);
        $wonItem = $items[$index];

        // Tắt item đã phát (để không phát lại)
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE mystery_bag_items SET status = 0 WHERE id = ?");
        $stmt->execute([$wonItem['id']]);

        return $wonItem;
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
