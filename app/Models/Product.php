<?php

require_once BASE_PATH . '/core/Model.php';

class Product extends Model
{
    protected $table = 'products';

    /**
     * Lấy sản phẩm còn hàng với thông tin danh mục
     */
    public function getAvailable($categoryId = null)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'available'";
        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết sản phẩm với danh mục
     */
    public function getDetail($id)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả sản phẩm (admin)
     */
    public function getAllWithCategory()
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Đếm sản phẩm còn hàng
     */
    public function countAvailable()
    {
        return $this->count(['status' => 'available']);
    }
}
