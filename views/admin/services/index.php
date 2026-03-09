<div class="admin-header">
    <h1><i class="fas fa-share-nodes"></i> Quản lý dịch vụ MXH</h1>
    <a href="<?= url('/admin/services/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm dịch vụ
    </a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Tên dịch vụ</th>
                <th>Nền tảng</th>
                <th>Giá/1000</th>
                <th>Min/Max</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">Chưa có dịch vụ nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $s): ?>
                    <tr>
                        <td>
                            <?= $s['id'] ?>
                        </td>
                        <td><strong>
                                <?= e($s['name']) ?>
                            </strong></td>
                        <td><span class="badge badge-primary">
                                <?= e($s['category_name']) ?>
                            </span></td>
                        <td class="text-success">
                            <?= formatMoney($s['price_per_1000']) ?>
                        </td>
                        <td>
                            <?= number_format($s['min_quantity']) ?> /
                            <?= number_format($s['max_quantity']) ?>
                        </td>
                        <td>
                            <?= $s['status'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= url('/admin/services/edit/' . $s['id']) ?>" class="btn btn-sm btn-warning"><i
                                        class="fas fa-edit"></i></a>
                                <a href="<?= url('/admin/services/delete/' . $s['id']) ?>" class="btn btn-sm btn-danger"
                                    data-confirm="Xóa dịch vụ này?"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>