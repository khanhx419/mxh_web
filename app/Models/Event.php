<?php

require_once BASE_PATH . '/core/Model.php';

class Event extends Model
{
    protected $table = 'events';

    /**
     * Lấy sự kiện đang hoạt động
     */
    public function getActive()
    {
        $db = $this->getDb();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            SELECT * FROM {$this->table} 
            WHERE status = 1 AND start_date <= ? AND end_date >= ?
            ORDER BY start_date ASC
        ");
        $stmt->execute([$now, $now]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả sự kiện (kể cả hết hạn)
     */
    public function getAll()
    {
        $db = $this->getDb();
        $stmt = $db->query("SELECT * FROM {$this->table} ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }

    /**
     * Lấy sự kiện sắp tới
     */
    public function getUpcoming()
    {
        $db = $this->getDb();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            SELECT * FROM {$this->table} 
            WHERE status = 1 AND start_date > ?
            ORDER BY start_date ASC LIMIT 5
        ");
        $stmt->execute([$now]);
        return $stmt->fetchAll();
    }
}
