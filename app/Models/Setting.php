<?php

require_once BASE_PATH . '/core/Model.php';

class Setting extends Model
{
    protected $table = 'settings';

    /**
     * Lấy giá trị cấu hình
     */
    public function get($name, $default = null)
    {
        $setting = $this->findOneWhere(['name' => $name]);
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Cập nhật cấu hình
     */
    public function set($name, $value)
    {
        $setting = $this->findOneWhere(['name' => $name]);
        if ($setting) {
            return $this->update($setting['id'], ['value' => $value]);
        } else {
            return $this->create([
                'name' => $name,
                'value' => $value
            ]);
        }
    }
}
