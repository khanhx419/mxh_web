<div class="admin-header">
    <h1><i class="fas fa-dharmachakra"></i> Quản lý Vòng Quay</h1>
</div>

<form action="<?= url('/admin/lucky-wheel/update') ?>" method="POST">
    <?= csrfField() ?>

    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:16px"><i class="fas fa-cog"></i> Cấu hình chung</h3>
        <div class="form-group">
            <label>Giá mỗi lượt quay (VNĐ)</label>
            <input type="number" name="spin_cost" value="<?= e($spinCost) ?>" class="form-control">
            <small class="form-hint"><i class="fas fa-info-circle"></i> Số tiền user cần trả cho mỗi lần quay vòng quay may mắn</small>
        </div>
    </div>

    <div class="form-card">
        <h3 style="margin-bottom:16px"><i class="fas fa-gift"></i> Danh sách giải thưởng</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên giải</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Tỉ lệ (%)</th>
                        <th>Màu</th>
                        <th>Bật</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prizes as $p): ?>
                        <tr>
                            <td><input type="text" name="prize_name[<?= $p['id'] ?>]" value="<?= e($p['name']) ?>" class="form-control"></td>
                            <td>
                                <select name="prize_type[<?= $p['id'] ?>]" class="form-control">
                                    <option value="money" <?= $p['type']=='money'?'selected':'' ?>>Tiền</option>
                                    <option value="nothing" <?= $p['type']=='nothing'?'selected':'' ?>>Không trúng</option>
                                    <option value="item" <?= $p['type']=='item'?'selected':'' ?>>Vật phẩm</option>
                                </select>
                            </td>
                            <td><input type="number" name="prize_value[<?= $p['id'] ?>]" value="<?= $p['value'] ?>" class="form-control"></td>
                            <td><input type="number" name="prize_probability[<?= $p['id'] ?>]" value="<?= $p['probability'] ?>" class="form-control" step="0.1"></td>
                            <td><input type="color" name="prize_color[<?= $p['id'] ?>]" value="<?= e($p['color']) ?>" style="width:40px;height:32px;border:none;cursor:pointer"></td>
                            <td><input type="checkbox" name="prize_status[<?= $p['id'] ?>]" <?= $p['status']?'checked':'' ?>></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <small class="form-hint"><i class="fas fa-info-circle"></i> Tổng tỉ lệ nên bằng 100%. Loại "Không trúng" sẽ hiển thị "Chúc may mắn lần sau"</small>
    </div>

    <div style="margin-top:20px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
    </div>
</form>
