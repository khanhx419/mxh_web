<?php

/**
 * Base Model
 * PDO wrapper cho tất cả các model
 */
class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = getDatabaseConnection();
    }

    /**
     * Lấy tất cả record
     */
    public function findAll($orderBy = 'id DESC')
    {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    /**
     * Lấy record theo ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Lấy record theo điều kiện
     */
    public function findWhere($conditions = [], $orderBy = 'id DESC', $limit = null)
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $wheres = [];
            foreach ($conditions as $key => $value) {
                $wheres[] = "`{$key}` = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy 1 record theo điều kiện
     */
    public function findOneWhere($conditions = [])
    {
        $result = $this->findWhere($conditions, 'id DESC', 1);
        return $result[0] ?? null;
    }

    /**
     * Tạo record mới
     */
    public function create($data)
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    /**
     * Cập nhật record
     */
    public function update($id, $data)
    {
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = ?";
            $params[] = $value;
        }
        $params[] = $id;

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa record
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Đếm tổng record
     */
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $wheres = [];
            foreach ($conditions as $key => $value) {
                $wheres[] = "`{$key}` = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    /**
     * Thực thi raw query
     */
    public function raw($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy PDO instance
     */
    public function getDb()
    {
        return $this->db;
    }
}
