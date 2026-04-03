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

<!-- Quick Probability Editor -->
<?php if (!empty($items)): ?>
<div style="margin-bottom:20px;padding:20px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:14px">
    <h3 style="margin-bottom:14px;font-size:.95rem"><i class="fas fa-sliders-h" style="color:var(--accent-primary)"></i> Chỉnh Xác Suất Nhanh</h3>
    
    <!-- Visual Bar -->
    <div style="height:12px;border-radius:6px;overflow:hidden;display:flex;background:var(--bg-input);margin-bottom:14px">
        <?php 
        $colors = ['#6c63ff','#e94560','#00d4aa','#ffa726','#29b6f6','#ab47bc','#ef5350','#66bb6a','#42a5f5','#ff7043'];
        foreach ($items as $i => $item): 
            $c = $colors[$i % count($colors)];
        ?>
            <div style="width:<?= $item['percentage'] ?>%;background:<?= $c ?>;transition:width .3s" title="<?= e($item['name']) ?>: <?= $item['percentage'] ?>%"></div>
        <?php endforeach; ?>
    </div>

    <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/probabilities') ?>" method="POST">
        <?= csrfField() ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
            <?php foreach ($items as $i => $item): 
                $c = $colors[$i % count($colors)];
                $pctColor = $item['percentage'] >= 40 ? 'var(--accent-success)' : ($item['percentage'] >= 15 ? 'var(--accent-warning)' : 'var(--accent-danger)');
            ?>
                <div style="padding:12px;background:var(--bg-body);border-radius:10px;border:1px solid var(--border-color)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:.82rem;font-weight:600;color:<?= $c ?>"><?= e($item['name']) ?></span>
                        <span style="font-size:.78rem;font-weight:700;color:<?= $pctColor ?>"><?= $item['percentage'] ?>%</span>
                    </div>
                    <input type="range" name="probability[<?= $item['id'] ?>]" value="<?= $item['probability'] ?>" min="0" max="100" 
                        style="width:100%;accent-color:<?= $c ?>" 
                        oninput="this.nextElementSibling.value=this.value">
                    <input type="number" value="<?= $item['probability'] ?>" min="0" class="form-control" 
                        style="margin-top:4px;font-size:.82rem;padding:4px 8px"
                        oninput="this.previousElementSibling.value=this.value"
                        name="probability[<?= $item['id'] ?>]">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:14px"><i class="fas fa-save"></i> Lưu xác suất</button>
    </form>
</div>
<?php endif; ?>

<!-- Bulk Add -->
<div style="margin-bottom:20px;padding:16px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px">
    <details>
        <summary style="cursor:pointer;font-weight:600;font-size:.9rem;color:var(--text-primary)">
            <i class="fas fa-layer-group" style="color:var(--accent-info)"></i> Thêm nhiều tài khoản cùng lúc
        </summary>
        <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/bulk-add') ?>" method="POST" style="margin-top:12px">
            <?= csrfField() ?>
            <textarea name="bulk_items" class="form-control" rows="5" placeholder="Tên|Giá trị|Nội dung|Xác suất&#10;VD: Acc VIP|150000|Tài khoản rank cao|20&#10;Acc Thường|30000|Tài khoản cơ bản|50"></textarea>
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)">Mỗi dòng 1 item. Format: Tên|Giá trị|Nội dung|Xác suất</small>
            <button type="submit" class="btn btn-primary" style="margin-top:8px"><i class="fas fa-plus"></i> Thêm tất cả</button>
        </form>
    </details>
</div>

<!-- Items Table -->
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên item</th>
                <th>Giá trị</th>
                <th>Nội dung / Tài khoản</th>
                <th>Xác suất</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center text-muted">Chưa có tài khoản nào. Hãy thêm mới!</td></tr>
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
                                <small style="color:var(--text-muted);margin-left:4px">(<?= $item['percentage'] ?>%)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $st = $item['status'] ?? 1; ?>
                            <?= $st ? '<span class="badge badge-success">Bật</span>' : '<span class="badge badge-danger">Tắt</span>' ?>
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
