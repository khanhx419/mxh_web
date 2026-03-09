<div class="admin-header">
    <h1><i class="fas fa-gamepad"></i> <?= $product ? 'Sửa tài khoản Game' : 'Thêm tài khoản Game' ?></h1>
    <a href="<?= url('/admin/products') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="<?= $product ? url('/admin/products/update/' . $product['id']) : url('/admin/products/store') ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="form-group">
                <label>Danh mục *</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tiêu đề *</label>
                <input type="text" name="title" class="form-control" value="<?= e($product['title'] ?? '') ?>" required placeholder="VD: Acc Liên Quân 50 Tướng Full Ngọc">
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Mô tả chi tiết sản phẩm..."><?= e($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Giá bán (VNĐ) *</label>
                <input type="number" name="price" class="form-control" value="<?= $product['price'] ?? '' ?>" required min="0" placeholder="VD: 150000">
            </div>

            <div class="form-group">
                <label>Thông tin tài khoản (Username/Pass) *</label>
                <textarea name="account_info" class="form-control" rows="3" required placeholder="VD: Username: abc123 | Password: xyz456"><?= e($product['account_info'] ?? '') ?></textarea>
                <small class="text-muted">Thông tin này chỉ hiển thị sau khi khách mua thành công.</small>
            </div>

            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="image" class="form-control" accept="image/*" data-preview="imagePreview">
                <?php if ($product && $product['image']): ?>
                    <img id="imagePreview" src="<?= asset('uploads/' . $product['image']) ?>" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">
                <?php else: ?>
                    <img id="imagePreview" style="max-width: 200px; margin-top: 10px; border-radius: 8px; display: none;">
                <?php endif; ?>
            </div>

            <?php if ($product): ?>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="available" <?= $product['status'] === 'available' ? 'selected' : '' ?>>Còn hàng</option>
                        <option value="sold" <?= $product['status'] === 'sold' ? 'selected' : '' ?>>Đã bán</option>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $product ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
        </form>
    </div>
</div>
