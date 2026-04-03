<?php

require_once BASE_PATH . '/core/Model.php';

class Invoice extends Model
{
    protected $table = 'invoices';

    /**
     * Lấy danh sách giao dịch nạp tiền của user
     */
    public function getUserInvoices($userId)
    {
        return $this->findWhere(['user_id' => $userId], 'created_at DESC');
    }

    /**
     * Lấy danh sách đang chờ nạp (status = 0)
     */
    public function getPending()
    {
        return $this->findWhere(['status' => 0], 'created_at ASC');
    }

    /**
     * Tạo hóa đơn nạp tiền mới
     */
    public function createInvoice($userId, $amount, $method)
    {
        $settingModel = new Setting();
        $prefix = strtoupper($settingModel->get('bank_prefix', 'NAP'));
        $transId = $prefix . $userId;

        $existingPending = $this->findOneWhere([
            'trans_id' => $transId,
            'status' => 0
        ]);

        if ($existingPending) {
            $this->update($existingPending['id'], [
                'amount' => $amount,
                'pay' => $amount,
                'method' => $method
            ]);
            return $this->findById($existingPending['id']);
        }

        $id = $this->create([
            'user_id' => $userId,
            'trans_id' => $transId,
            'amount' => $amount,
            'pay' => $amount,
            'method' => $method,
            'status' => 0,
            'description' => 'Nạp tiền vào tài khoản'
        ]);

        return $this->findById($id);
    }

    /**
     * Tìm invoice theo mã giao dịch nội bộ (trans_id)
     */
    public function findByTransId($transId)
    {
        return $this->findOneWhere(['trans_id' => $transId]);
    }

    /**
     * Tìm invoice theo request_id (FPayment, PerfectMoney)
     */
    public function findByRequestId($requestId, $type = null)
    {
        $conditions = ['request_id' => $requestId, 'status' => 0];
        if ($type) {
            $conditions['type'] = $type;
        }
        return $this->findOneWhere($conditions);
    }

    /**
     * Kiểm tra bank transaction ID đã được xử lý chưa (chống nạp trùng)
     */
    public function isTransactionProcessed($bankTxId)
    {
        $result = $this->findOneWhere(['tid' => $bankTxId]);
        return $result !== false && $result !== null;
    }

    /**
     * Đếm số invoice theo điều kiện
     */
    public function count($conditions = [])
    {
        $db = $this->getDb();
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = ?";
            $params[] = $value;
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return intval($row['total'] ?? 0);
    }
}
