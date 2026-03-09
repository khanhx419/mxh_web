<div class="admin-header">
    <h1><i class="fas fa-share-nodes"></i>
        <?= $service ? 'Sửa dịch vụ MXH' : 'Thêm dịch vụ MXH' ?>
    </h1>
    <a href="<?= url('/admin/services') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST"
            action="<?= $service ? url('/admin/services/update/' . $service['id']) : url('/admin/services/store') ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label>Nền tảng *</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn nền tảng --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($service['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tên dịch vụ *</label>
                <input type="text" name="name" class="form-control" value="<?= e($service['name'] ?? '') ?>" required
                    placeholder="VD: Tăng Like Facebook Post">
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="3"
                    placeholder="Mô tả dịch vụ..."><?= e($service['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Giá / 1000 lượt (VNĐ) *</label>
                <input type="number" name="price_per_1000" class="form-control"
                    value="<?= $service['price_per_1000'] ?? '' ?>" required min="0" placeholder="VD: 25000">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Số lượng tối thiểu</label>
                    <input type="number" name="min_quantity" class="form-control"
                        value="<?= $service['min_quantity'] ?? 100 ?>" min="1">
                </div>
                <div class="form-group">
                    <label>Số lượng tối đa</label>
                    <input type="number" name="max_quantity" class="form-control"
                        value="<?= $service['max_quantity'] ?? 100000 ?>" min="1">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="status" <?= ($service['status'] ?? 1) ? 'checked' : '' ?>>
                    Kích hoạt
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <?= $service ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
        </form>
    </div>
</div>