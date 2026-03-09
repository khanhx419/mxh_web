<div class="admin-header">
    <h1><i class="fas fa-chart-pie"></i> Dashboard</h1>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-info">
            <h3>
                <?= formatMoney($totalRevenue) ?>
            </h3>
            <p>Tổng doanh thu</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3>
                <?= $totalOrders ?>
            </h3>
            <p>Tổng đơn hàng</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-gamepad"></i></div>
        <div class="stat-info">
            <h3>
                <?= $totalProducts ?>
            </h3>
            <p>Acc Game còn hàng</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-share-nodes"></i></div>
        <div class="stat-info">
            <h3>
                <?= $totalServices ?>
            </h3>
            <p>Dịch vụ MXH</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>
                <?= $totalUsers ?>
            </h3>
            <p>Khách hàng</p>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="section-title">
    <i class="fas fa-clock"></i>
    <h2>Đơn hàng gần đây</h2>
    <a href="<?= url('/admin/orders') ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Khách hàng</th>
                <th>Loại</th>
                <th>Sản phẩm/Dịch vụ</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentOrders)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">Chưa có đơn hàng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><a href="<?= url('/admin/orders/' . $order['id']) ?>">#
                                <?= $order['id'] ?>
                            </a></td>
                        <td>
                            <?= e($order['username']) ?>
                        </td>
                        <td>
                            <?php if ($order['order_type'] === 'product'): ?>
                                <span class="badge badge-info">Acc Game</span>
                            <?php else: ?>
                                <span class="badge badge-primary">Dịch vụ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= e($order['product_title'] ?? $order['service_name'] ?? 'N/A') ?>
                        </td>
                        <td class="text-success">
                            <?= formatMoney($order['total_price']) ?>
                        </td>
                        <td>
                            <?= orderStatusLabel($order['status']) ?>
                        </td>
                        <td>
                            <?= formatDate($order['created_at']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>