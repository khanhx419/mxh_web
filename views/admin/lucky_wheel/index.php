<div class="admin-header">
    <h1><i class="fas fa-dharmachakra"></i> Quản lý Vòng Quay May Mắn</h1>
    <a href="<?= url('/admin/lucky-wheel/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm giải</a>
</div>

<form action="<?= url('/admin/lucky-wheel/update') ?>" method="POST">
    <?= csrfField() ?>

    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:16px"><i class="fas fa-cog"></i> Cấu hình chung</h3>
        <div class="form-group">
            <label>Giá mỗi lượt quay (VNĐ)</label>
            <input type="number" name="spin_cost" value="<?= e($spinCost) ?>" class="form-control">
        </div>
    </div>

    <!-- Probability Overview -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:16px"><i class="fas fa-chart-pie"></i> Phân bố xác suất</h3>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
            <?php foreach ($prizes as $p): ?>
                <div style="flex:1;min-width:120px;background:<?= e($p['color']) ?>20;border:1px solid <?= e($p['color']) ?>40;border-radius:10px;padding:10px 14px;text-align:center">
                    <div style="font-size:.78rem;color:var(--text-secondary)"><?= e($p['name']) ?></div>
                    <div style="font-size:1.3rem;font-weight:800;color:<?= e($p['color']) ?>"><?= $p['percentage'] ?>%</div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="height:8px;border-radius:4px;overflow:hidden;display:flex;background:var(--bg-input)">
            <?php foreach ($prizes as $p): ?>
                <div style="width:<?= $p['percentage'] ?>%;background:<?= e($p['color']) ?>;transition:width .3s" title="<?= e($p['name']) ?>: <?= $p['percentage'] ?>%"></div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:right;margin-top:8px;font-size:.82rem;color:var(--text-muted)">
            Tổng trọng số: <strong style="color:<?= $totalProbability == 100 ? 'var(--accent-success)' : 'var(--accent-warning)' ?>"><?= $totalProbability ?></strong>
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
                        <th>Trọng số</th>
                        <th>%</th>
                        <th>Màu</th>
                        <th>Bật</th>
                        <th>Xoá</th>
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
                                    <option value="product" <?= $p['type']=='product'?'selected':'' ?>>Vật phẩm</option>
                                </select>
                            </td>
                            <td><input type="number" name="prize_value[<?= $p['id'] ?>]" value="<?= $p['value'] ?>" class="form-control"></td>
                            <td><input type="number" name="prize_probability[<?= $p['id'] ?>]" value="<?= $p['probability'] ?>" class="form-control" step="0.1" min="0"></td>
                            <td>
                                <span class="badge" style="background:<?= e($p['color']) ?>30;color:<?= e($p['color']) ?>"><?= $p['percentage'] ?>%</span>
                            </td>
                            <td><input type="color" name="prize_color[<?= $p['id'] ?>]" value="<?= e($p['color']) ?>" style="width:40px;height:32px;border:none;cursor:pointer;border-radius:4px"></td>
                            <td><input type="checkbox" name="prize_status[<?= $p['id'] ?>]" <?= $p['status']?'checked':'' ?>></td>
                            <td>
                                <a href="<?= url('/admin/lucky-wheel/delete/' . $p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá giải thưởng này?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <small class="form-hint"><i class="fas fa-info-circle"></i> Tổng trọng số nên bằng 100. Loại "Không trúng" sẽ hiển thị "Chúc may mắn lần sau"</small>
    </div>

    <div style="margin-top:20px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
    </div>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-hint{display:block;margin-top:8px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>
