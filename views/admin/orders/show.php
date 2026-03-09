<div class="admin-header">
    <h1><i class="fas fa-receipt"></i> Chi tiết đơn hàng #<?= $order['id'] ?></h1>
    <a href="<?= url('/admin/orders') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-2">Thông tin đơn hàng</h3>
            <table class="table">
                <tr><td class="text-muted">Mã đơn:</td><td><strong>#<?= $order['id'] ?></strong></td></tr>
                <tr><td class="text-muted">Loại:</td><td>
                    <?= $order['order_type'] === 'product' 
                        ? '<span class="badge badge-info">Acc Game</span>' 
                        : '<span class="badge badge-primary">Dịch vụ MXH</span>' ?>
                </td></tr>
                <tr><td class="text-muted">Sản phẩm/DV:</td><td><?= e($order['product_title'] ?? $order['service_name'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Số lượng:</td><td><?= number_format($order['quantity']) ?></td></tr>
                <tr><td class="text-muted">Tổng tiền:</td><td class="text-success" style="font-size: 1.2rem; font-weight: 700;"><?= formatMoney($order['total_price']) ?></td></tr>
                <tr><td class="text-muted">Trạng thái:</td><td><?= orderStatusLabel($order['status']) ?></td></tr>
                <tr><td class="text-muted">Ngày tạo:</td><td><?= formatDate($order['created_at']) ?></td></tr>
                <?php if ($order['target_link']): ?>
                    <tr><td class="text-muted">Link mục tiêu:</td><td><a href="<?= e($order['target_link']) ?>" target="_blank"><?= e($order['target_link']) ?></a></td></tr>
                <?php endif; ?>
                <?php if ($order['account_data']): ?>
                    <tr><td class="text-muted">Thông tin ACC:</td><td><code><?= e($order['account_data']) ?></code></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div>
        <!-- Customer Info -->
        <div class="card mb-2">
            <div class="card-body">
                <h3 class="mb-2">Thông tin khách hàng</h3>
                <table class="table">
                    <tr><td class="text-muted">Username:</td><td><?= e($order['username']) ?></td></tr>
                    <tr><td class="text-muted">Email:</td><td><?= e($order['email']) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Update Status -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2">Cập nhật trạng thái</h3>
                <form method="POST" action="<?= url('/admin/orders/update-status/' . $order['id']) ?>">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Hủy (hoàn tiền)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
