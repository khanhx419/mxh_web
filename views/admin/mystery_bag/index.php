<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> Quản lý Túi Mù</h1>
    <a href="<?= url('/admin/mystery-bag/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Hình</th>
                <th>Tên túi</th>
                <th>Giá</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bags)): ?>
                <tr><td colspan="6" class="text-center text-muted">Chưa có túi mù nào.</td></tr>
            <?php else: ?>
                <?php foreach ($bags as $bag): ?>
                    <tr>
                        <td>
                            <?php $img = !empty($bag['image']) ? url('/uploads/mystery_bags/'.$bag['image']) : url('/uploads/mystery_bags/default.png'); ?>
                            <img src="<?= $img ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid var(--border-color)">
                        </td>
                        <td><strong><?= e($bag['name']) ?></strong></td>
                        <td class="text-success"><?= formatMoney($bag['price']) ?></td>
                        <td><?= e(mb_substr($bag['description'], 0, 50)) ?></td>
                        <td>
                            <?= $bag['status'] ? '<span class="badge badge-success">Bật</span>' : '<span class="badge badge-danger">Tắt</span>' ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/mystery-bag/edit/'.$bag['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('/admin/mystery-bag/delete/'.$bag['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá túi này?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
