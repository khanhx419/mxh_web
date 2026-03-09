<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-receipt"></i> Đơn hàng của tôi</h1>
        <p>Lịch sử mua hàng và trạng thái đơn hàng</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h3>Chưa có đơn hàng nào</h3>
            <p>Bạn chưa mua gì cả. <a href="<?= url('/shop/games') ?>">Mua sắm ngay!</a></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Loại</th>
                        <th>Sản phẩm/Dịch vụ</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày mua</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#
                                <?= $o['id'] ?>
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
                                <?php if ($o['order_type'] === 'product' && $o['account_data'] && $o['status'] === 'completed'): ?>
                                    <button class="btn btn-sm btn-success"
                                        onclick="alert('Thông tin tài khoản:\n<?= addslashes(e($o['account_data'])) ?>')">
                                        <i class="fas fa-key"></i> Xem ACC
                                    </button>
                                <?php elseif ($o['target_link']): ?>
                                    <a href="<?= e($o['target_link']) ?>" target="_blank" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-external-link-alt"></i> Link
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>