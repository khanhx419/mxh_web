<div class="admin-header">
    <h1><i class="fas fa-file-invoice-dollar"></i> Quản lý Nạp tiền</h1>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Người nạp</th>
                <th>Số tiền</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr><td colspan="6" class="text-center text-muted">Chưa có giao dịch nạp tiền nào.</td></tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>#<?= $inv['id'] ?></td>
                        <td><?= e($inv['username']) ?></td>
                        <td class="text-success"><?= formatMoney($inv['amount']) ?></td>
                        <td><?= e($inv['content'] ?? '') ?></td>
                        <td>
                            <?php if ($inv['status'] == 'completed'): ?>
                                <span class="badge badge-success">Thành công</span>
                            <?php elseif ($inv['status'] == 'pending'): ?>
                                <span class="badge badge-warning">Chờ xử lý</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Thất bại</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($inv['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
