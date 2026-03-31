<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> <?= $bag ? 'Sửa' : 'Thêm' ?> Túi Mù</h1>
    <a href="<?= url('/admin/mystery-bag') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="<?= $bag ? url('/admin/mystery-bag/update/'.$bag['id']) : url('/admin/mystery-bag/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="form-card">
        <div class="form-group">
            <label>Tên túi mù *</label>
            <input type="text" name="name" class="form-control" value="<?= e($bag['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Giá (VNĐ) *</label>
            <input type="number" name="price" class="form-control" value="<?= e($bag['price'] ?? '') ?>" required>
            <small class="form-hint"><i class="fas fa-info-circle"></i> Số tiền user trả để mở 1 lần. Nếu trúng sẽ nhận lại đúng số tiền này (tỉ lệ 20%)</small>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= e($bag['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Hình ảnh túi</label>
            <?php if ($bag && !empty($bag['image'])): ?>
                <div style="margin-bottom:8px">
                    <img src="<?= url('/uploads/mystery_bags/'.$bag['image']) ?>" style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:1px solid var(--border-color)">
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="form-hint"><i class="fas fa-info-circle"></i> Nên dùng ảnh vuông. Để trống sẽ dùng hình mặc định chung</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?= ($bag['status'] ?? 1) ? 'checked' : '' ?>> Kích hoạt
            </label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $bag ? 'Cập nhật' : 'Thêm mới' ?></button>
    </div>
</form>
