<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> Tài khoản: <?= e($bag['name']) ?></h1>
    <div style="display:flex;gap:8px">
        <a href="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/add') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm tài khoản</a>
        <a href="<?= url('/admin/mystery-bag') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</div>

<div style="margin-bottom:16px;padding:14px 18px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <div style="font-size:.85rem;color:var(--text-secondary)">
        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px"></i>
        <strong>Túi:</strong> <?= e($bag['name']) ?> &bull;
        <strong>Giá:</strong> <?= formatMoney($bag['price']) ?> &bull;
        <strong>Tổng items:</strong> <?= count($items) ?> &bull;
        <strong>Tổng xác suất:</strong> <?= array_sum(array_column($items, 'probability')) ?>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên item</th>
                <th>Giá trị</th>
                <th>Nội dung / Tài khoản</th>
                <th>Xác suất</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" class="text-center text-muted">Chưa có tài khoản nào. Hãy thêm mới!</td></tr>
            <?php else: ?>
                <?php $totalProb = array_sum(array_column($items, 'probability')); ?>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($item['name']) ?></strong></td>
                        <td class="text-success"><?= formatMoney($item['value']) ?></td>
                        <td>
                            <div style="max-width:300px;font-size:.82rem;color:var(--text-secondary);white-space:pre-wrap;word-break:break-all"><?= e(mb_substr($item['content'], 0, 120)) ?><?= mb_strlen($item['content']) > 120 ? '...' : '' ?></div>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= $item['probability'] ?></span>
                            <?php if ($totalProb > 0): ?>
                                <small style="color:var(--text-muted);margin-left:4px">(<?= round($item['probability'] / $totalProb * 100, 1) ?>%)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/mystery-bag/items/edit/' . $item['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('/admin/mystery-bag/items/delete/' . $item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá item này?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
