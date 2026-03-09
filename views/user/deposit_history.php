<div class="container">
    <div class="section-title">
        <i class="fas fa-history"></i>
        <h2>Lịch Sử Nạp Tiền</h2>
        <a href="<?= url('/banking') ?>" class="view-all"><i class="fas fa-arrow-left"></i> Quay lại Nạp Tiền</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($invoices)): ?>
                <div class="empty-state text-center py-5">
                    <i class="fas fa-receipt mb-3" style="font-size: 3rem; color: var(--text-muted);"></i>
                    <h3>Chưa có giao dịch nạp tiền nào</h3>
                    <p class="text-secondary">Bạn chưa thực hiện giao dịch nạp tiền nào trên hệ thống.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper" style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Nội dung chuyển khoản</th>
                                <th>Số tiền nạp</th>
                                <th>Thực nhận</th>
                                <th>Phương thức</th>
                                <th>Thời gian tạo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><strong>#
                                            <?= e($invoice['trans_id']) ?>
                                        </strong></td>
                                    <td><code
                                            style="background: var(--bg-primary); padding: 4px 8px; border-radius: 4px;"><?= e($invoice['description']) ?></code>
                                    </td>
                                    <td class="text-primary font-weight-bold">
                                        <?= formatMoney($invoice['amount']) ?>
                                    </td>
                                    <td class="text-success font-weight-bold">+
                                        <?= formatMoney($invoice['pay']) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><i class="fas fa-university"></i>
                                            <?= e($invoice['method']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= formatDate($invoice['created_at']) ?>
                                    </td>
                                    <td>
                                        <?php if ($invoice['status'] == 1): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Hoàn thành</span>
                                        <?php elseif ($invoice['status'] == 2): ?>
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Đã hủy</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Chờ thanh
                                                toán</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>