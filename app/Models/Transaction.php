<?php

require_once BASE_PATH . '/core/Model.php';

class Transaction extends Model
{
    protected $table = 'transactions';

    /**
     * Ghi nhận giao dịch
     */
    public function log($userId, $type, $amount, $balanceAfter, $description)
    {
        return $this->create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $description
        ]);
    }

    /**
     * Lấy lịch sử giao dịch của user
     */
    public function getUserTransactions($userId)
    {
        return $this->findWhere(['user_id' => $userId], 'created_at DESC');
    }
}
