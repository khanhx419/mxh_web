<div class="admin-header">
    <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Khách hàng</th>
                <th>Loại</th>
                <th>Sản phẩm/Dịch vụ</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">Chưa có đơn hàng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#
                            <?= $o['id'] ?>
                        </td>
                        <td>
                            <?= e($o['username']) ?>
                        </td>
                        <td>
                            <?= $o['order_type'] === 'product'
                                ? '<span class="badge badge-info">Acc Game</span>'
                                : '<span class="badge badge-primary">Dịch vụ MXH</span>' ?>
                        </td>
                        <td>
                            <?= e($o['product_title'] ?? $o['service_name'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <?= number_format($o['quantity']) ?>
                        </td>
                        <td class="text-success">
                            <?= formatMoney($o['total_price']) ?>
                        </td>
                        <td>
                            <?= orderStatusLabel($o['status']) ?>
                        </td>
                        <td>
                            <?= formatDate($o['created_at']) ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>