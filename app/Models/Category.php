<?php

require_once BASE_PATH . '/core/Model.php';

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Lấy danh mục game
     */
    public function getGameCategories()
    {
        return $this->findWhere(['type' => 'game', 'status' => 1], 'name ASC');
    }

    /**
     * Lấy danh mục MXH
     */
    public function getSocialCategories()
    {
        return $this->findWhere(['type' => 'social', 'status' => 1], 'name ASC');
    }

    /**
     * Lấy tất cả danh mục active
     */
    public function getActive()
    {
        return $this->findWhere(['status' => 1], 'type ASC, name ASC');
    }
}
