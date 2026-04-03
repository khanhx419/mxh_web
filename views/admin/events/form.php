<div class="admin-header">
    <h1><i class="fas fa-calendar-star"></i> <?= $event ? 'Sửa' : 'Thêm' ?> Sự Kiện</h1>
    <a href="<?= url('/admin/events') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="<?= $event ? url('/admin/events/update/' . $event['id']) : url('/admin/events/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="form-card" style="margin-bottom:24px">
        <div class="form-group">
            <label>Tiêu đề sự kiện *</label>
            <input type="text" name="title" class="form-control" value="<?= e($event['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="4"><?= e($event['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Hình ảnh sự kiện</label>
            <?php if ($event && !empty($event['image'])): ?>
                <div style="margin-bottom:8px">
                    <img src="<?= asset('uploads/events/' . $event['image']) ?>" style="max-width:200px;border-radius:12px;border:1px solid var(--border-color)">
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="form-hint"><i class="fas fa-info-circle"></i> Hình banner sự kiện. Để trống giữ hình cũ.</small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label>Ngày bắt đầu *</label>
                <input type="datetime-local" name="start_date" class="form-control" 
                    value="<?= $event ? date('Y-m-d\TH:i', strtotime($event['start_date'])) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Ngày kết thúc *</label>
                <input type="datetime-local" name="end_date" class="form-control" 
                    value="<?= $event ? date('Y-m-d\TH:i', strtotime($event['end_date'])) : '' ?>" required>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label>Loại phần thưởng</label>
                <select name="reward_type" class="form-control">
                    <option value="money" <?= ($event['reward_type'] ?? '') === 'money' ? 'selected' : '' ?>>Tiền (VNĐ)</option>
                    <option value="points" <?= ($event['reward_type'] ?? 'points') === 'points' ? 'selected' : '' ?>>Điểm xanh</option>
                    <option value="discount" <?= ($event['reward_type'] ?? '') === 'discount' ? 'selected' : '' ?>>Giảm giá (%)</option>
                    <option value="item" <?= ($event['reward_type'] ?? '') === 'item' ? 'selected' : '' ?>>Đặc biệt</option>
                </select>
            </div>
            <div class="form-group">
                <label>Giá trị phần thưởng</label>
                <input type="number" name="reward_value" class="form-control" value="<?= e($event['reward_value'] ?? '0') ?>" step="any">
                <small class="form-hint"><i class="fas fa-lightbulb"></i> Tuỳ loại: số tiền, hệ số nhân, % giảm giá</small>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?= ($event['status'] ?? 1) ? 'checked' : '' ?>> Kích hoạt sự kiện
            </label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $event ? 'Cập nhật' : 'Thêm mới' ?></button>
    </div>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>
