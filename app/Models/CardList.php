<?php

require_once BASE_PATH . '/core/Model.php';

class CardList extends Model
{
    protected $table = 'card_lists';

    /**
     * Lấy danh sách thẻ đang xử lý
     */
    public function getProcessing()
    {
        return $this->findWhere(['status' => 'Processing']);
    }

    /**
     * Tạo yêu cầu nạp thẻ
     */
    public function createCard($userId, $type, $serial, $code, $amount)
    {
        $requestId = 'CARD-' . time() . '-' . mt_rand(1000, 9999);
        return $this->create([
            'user_id' => $userId,
            'type' => strtoupper($type),
            'serial' => $serial,
            'code' => $code,
            'amount' => $amount,
            'request_id' => $requestId,
            'status' => 'Processing'
        ]);
    }

    /**
     * Tìm thẻ theo request_id và order_id
     */
    public function findByRequestAndOrder($requestId, $orderId)
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE request_id = ? AND order_id = ? AND status = 'Processing'");
        $stmt->execute([$requestId, $orderId]);
        return $stmt->fetch();
    }

    /**
     * Lấy lịch sử nạp thẻ của user
     */
    public function getUserCards($userId, $limit = 20)
    {
        return $this->findWhere(['user_id' => $userId], 'created_at DESC', $limit);
    }
}
