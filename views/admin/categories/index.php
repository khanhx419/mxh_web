<div class="admin-header">
    <h1><i class="fas fa-tags"></i> Quản lý danh mục</h1>
    <a href="<?= url('/admin/categories/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm danh mục
    </a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Tên danh mục</th>
                <th>Loại</th>
                <th>Icon</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">Chưa có danh mục nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <?= $cat['id'] ?>
                        </td>
                        <td><strong>
                                <?= e($cat['name']) ?>
                            </strong></td>
                        <td>
                            <?php if ($cat['type'] === 'game'): ?>
                                <span class="badge badge-info">Game</span>
                            <?php else: ?>
                                <span class="badge badge-primary">MXH</span>
                            <?php endif; ?>
                        </td>
                        <td><i class="fas <?= e($cat['icon']) ?>"></i>
                            <?= e($cat['icon']) ?>
                        </td>
                        <td>
                            <?php if ($cat['status']): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= formatDate($cat['created_at']) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= url('/admin/categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= url('/admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-danger"
                                    data-confirm="Bạn có chắc muốn xóa danh mục này?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>