<?php

require_once BASE_PATH . '/core/Model.php';

class User extends Model
{
    protected $table = 'users';

    /**
     * Tìm user theo username
     */
    public function findByUsername($username)
    {
        return $this->findOneWhere(['username' => $username]);
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail($email)
    {
        return $this->findOneWhere(['email' => $email]);
    }

    /**
     * Tạo user mới với password hash
     */
    public function register($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }

    /**
     * Cập nhật số dư
     */
    public function updateBalance($userId, $amount)
    {
        $stmt = $this->db->prepare("UPDATE `users` SET `balance` = `balance` + ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }

    /**
     * Lấy số dư hiện tại
     */
    public function getBalance($userId)
    {
        $user = $this->findById($userId);
        return $user ? $user['balance'] : 0;
    }
}
