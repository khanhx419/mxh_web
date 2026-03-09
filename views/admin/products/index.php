<div class="admin-header">
    <h1><i class="fas fa-gamepad"></i> Quản lý tài khoản Game</h1>
    <a href="<?= url('/admin/products/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm sản phẩm
    </a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Chưa có sản phẩm nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <?= $p['id'] ?>
                        </td>
                        <td>
                            <?php if ($p['image']): ?>
                                <img src="<?= asset('uploads/' . $p['image']) ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                                <div
                                    style="width: 50px; height: 50px; background: var(--bg-secondary); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><strong>
                                <?= e($p['title']) ?>
                            </strong></td>
                        <td><span class="badge badge-info">
                                <?= e($p['category_name']) ?>
                            </span></td>
                        <td class="text-success">
                            <?= formatMoney($p['price']) ?>
                        </td>
                        <td>
                            <?php if ($p['status'] === 'available'): ?>
                                <span class="badge badge-success">Còn hàng</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Đã bán</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= formatDate($p['created_at']) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= url('/admin/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-warning"><i
                                        class="fas fa-edit"></i></a>
                                <a href="<?= url('/admin/products/delete/' . $p['id']) ?>" class="btn btn-sm btn-danger"
                                    data-confirm="Xóa sản phẩm này?"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>