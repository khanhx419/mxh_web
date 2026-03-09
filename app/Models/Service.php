<?php

require_once BASE_PATH . '/core/Model.php';

class Service extends Model
{
    protected $table = 'services';

    /**
     * Lấy dịch vụ hoạt động với danh mục
     */
    public function getActive($categoryId = null)
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                WHERE s.status = 1";
        $params = [];

        if ($categoryId) {
            $sql .= " AND s.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết dịch vụ
     */
    public function getDetail($id)
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả (admin)
     */
    public function getAllWithCategory()
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
