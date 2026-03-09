<div class="admin-header">
    <h1><i class="fas fa-tags"></i>
        <?= $category ? 'Sửa danh mục' : 'Thêm danh mục' ?>
    </h1>
    <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST"
            action="<?= $category ? url('/admin/categories/update/' . $category['id']) : url('/admin/categories/store') ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label>Tên danh mục *</label>
                <input type="text" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" required
                    placeholder="VD: Liên Quân Mobile, Facebook...">
            </div>

            <div class="form-group">
                <label>Loại *</label>
                <select name="type" class="form-control">
                    <option value="game" <?= ($category['type'] ?? '') === 'game' ? 'selected' : '' ?>>Game</option>
                    <option value="social" <?= ($category['type'] ?? '') === 'social' ? 'selected' : '' ?>>Mạng xã hội
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Icon (FontAwesome class)</label>
                <input type="text" name="icon" class="form-control" value="<?= e($category['icon'] ?? 'fa-folder') ?>"
                    placeholder="VD: fa-gamepad, fa-facebook...">
                <small class="text-muted">Xem thêm tại <a href="https://fontawesome.com/icons"
                        target="_blank">fontawesome.com</a></small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="status" <?= ($category['status'] ?? 1) ? 'checked' : '' ?>>
                    Kích hoạt
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <?= $category ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
        </form>
    </div>
</div>