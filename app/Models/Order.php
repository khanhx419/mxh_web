<?php

require_once BASE_PATH . '/core/Model.php';

class Order extends Model
{
    protected $table = 'orders';

    /**
     * Tạo đơn mua acc game
     */
    public function createProductOrder($userId, $productId, $totalPrice)
    {
        return $this->create([
            'user_id' => $userId,
            'order_type' => 'product',
            'product_id' => $productId,
            'quantity' => 1,
            'total_price' => $totalPrice,
            'status' => 'completed'
        ]);
    }

    /**
     * Tạo đơn dịch vụ MXH
     */
    public function createServiceOrder($userId, $serviceId, $quantity, $targetLink, $totalPrice, $smmOrderId = null)
    {
        return $this->create([
            'user_id' => $userId,
            'order_type' => 'service',
            'service_id' => $serviceId,
            'quantity' => $quantity,
            'target_link' => $targetLink,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'smm_order_id' => $smmOrderId
        ]);
    }

    /**
     * Lấy đơn SMM đang chờ xử lý (chưa completed/cancelled)
     */
    public function getPendingSmmOrders()
    {
        $sql = "SELECT * FROM orders
                WHERE order_type = 'service'
                  AND smm_order_id IS NOT NULL
                  AND status NOT IN ('completed', 'cancelled')
                ORDER BY created_at ASC LIMIT 50";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật thông tin SMM
     */
    public function updateSmmStatus($id, $smmStatus, $startCount = null, $remains = null, $status = null)
    {
        $data = ['smm_status' => $smmStatus];
        if ($startCount !== null)
            $data['start_count'] = $startCount;
        if ($remains !== null)
            $data['remains'] = $remains;
        if ($status !== null)
            $data['status'] = $status;
        return $this->update($id, $data);
    }

    /**
     * Lấy đơn hàng của user
     */
    public function getUserOrders($userId)
    {
        $sql = "SELECT o.*, 
                    p.title as product_title, 
                    s.name as service_name,
                    c1.name as product_category,
                    c2.name as service_category
                FROM orders o 
                LEFT JOIN products p ON o.product_id = p.id
                LEFT JOIN services s ON o.service_id = s.id
                LEFT JOIN categories c1 ON p.category_id = c1.id
                LEFT JOIN categories c2 ON s.category_id = c2.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả đơn hàng (admin)
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT o.*, 
                    u.username,
                    p.title as product_title, 
                    s.name as service_name
                FROM orders o 
                JOIN users u ON o.user_id = u.id
                LEFT JOIN products p ON o.product_id = p.id
                LEFT JOIN services s ON o.service_id = s.id
                ORDER BY o.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function getDetail($id)
    {
        $sql = "SELECT o.*, 
                    u.username, u.email,
                    p.title as product_title, p.account_info,
                    s.name as service_name
                FROM orders o 
                JOIN users u ON o.user_id = u.id
                LEFT JOIN products p ON o.product_id = p.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE o.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Tính tổng doanh thu
     */
    public function totalRevenue()
    {
        $sql = "SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE status != 'cancelled'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
}
