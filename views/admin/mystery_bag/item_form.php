<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> <?= $item ? 'Sửa' : 'Thêm' ?> Tài Khoản - <?= e($bag['name']) ?></h1>
    <a href="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="<?= $item ? url('/admin/mystery-bag/items/update/' . $item['id']) : url('/admin/mystery-bag/' . $bag['id'] . '/items/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="form-card" style="margin-bottom:24px">
        <div class="form-group">
            <label>Tên item / tài khoản *</label>
            <input type="text" name="name" class="form-control" value="<?= e($item['name'] ?? '') ?>" required placeholder="VD: Acc VIP Rank Cao Thủ">
        </div>

        <div class="form-group">
            <label>Giá trị (VNĐ)</label>
            <input type="number" name="value" class="form-control" value="<?= e($item['value'] ?? '0') ?>" step="any">
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)"><i class="fas fa-info-circle" style="margin-right:4px;color:var(--accent-warning)"></i> Giá trị ước tính của tài khoản này. Dùng để hiển thị cho user.</small>
        </div>

        <div class="form-group">
            <label>Nội dung / Thông tin tài khoản</label>
            <textarea name="content" class="form-control" rows="4" placeholder="VD: Tên đăng nhập: abc123&#10;Mật khẩu: ****&#10;Server: Việt Nam"><?= e($item['content'] ?? '') ?></textarea>
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)"><i class="fas fa-info-circle" style="margin-right:4px;color:var(--accent-warning)"></i> Thông tin chi tiết sẽ hiện cho user khi trúng item này.</small>
        </div>

        <div class="form-group">
            <label>Xác suất (trọng số)</label>
            <input type="number" name="probability" class="form-control" value="<?= e($item['probability'] ?? '10') ?>" min="0">
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)"><i class="fas fa-lightbulb" style="margin-right:4px;color:var(--accent-warning)"></i> Trọng số xác suất. VD: item A=60, item B=30, item C=10 → 60%, 30%, 10%</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?= ($item['status'] ?? 1) ? 'checked' : '' ?>> Kích hoạt (Cho phép rơi item này)
            </label>
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)"><i class="fas fa-info-circle" style="margin-right:4px;color:var(--accent-warning)"></i> Tắt để tạm ẩn item khỏi pool xác suất mà không cần xoá.</small>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $item ? 'Cập nhật' : 'Thêm mới' ?></button>
    </div>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
</style>
