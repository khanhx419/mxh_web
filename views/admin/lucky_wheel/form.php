<div class="admin-header">
    <h1><i class="fas fa-dharmachakra"></i> Thêm Giải Thưởng</h1>
    <a href="<?= url('/admin/lucky-wheel') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="<?= url('/admin/lucky-wheel/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="form-card">
        <div class="form-group">
            <label>Tên giải thưởng *</label>
            <input type="text" name="name" class="form-control" required placeholder="VD: 50,000đ">
        </div>
        <div class="form-group">
            <label>Loại giải</label>
            <select name="type" class="form-control">
                <option value="money">Tiền</option>
                <option value="nothing">Không trúng</option>
                <option value="product">Vật phẩm</option>
            </select>
        </div>
        <div class="form-group">
            <label>Giá trị (VNĐ)</label>
            <input type="number" name="value" class="form-control" value="0" step="any">
            <small class="form-hint"><i class="fas fa-info-circle"></i> Số tiền cộng vào tài khoản user khi trúng. Loại "Không trúng" để 0.</small>
        </div>
        <div class="form-group">
            <label>Trọng số xác suất</label>
            <input type="number" name="probability" class="form-control" value="10" min="0" step="0.1">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> VD: Item A=60, Item B=30, Item C=10 → 60%, 30%, 10%</small>
        </div>
        <div class="form-group">
            <label>Màu hiển thị</label>
            <input type="color" name="color" value="#6c63ff" style="width:60px;height:36px;border:none;cursor:pointer;border-radius:6px">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="status" checked> Kích hoạt
            </label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Thêm giải thưởng</button>
    </div>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>
