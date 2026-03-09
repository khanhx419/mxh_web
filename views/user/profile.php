<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-user-circle"></i> Tài khoản của tôi</h1>
    </div>

    <div class="grid-2">
        <!-- Profile Info -->
        <div class="card">
            <div class="card-body">
                <div style="text-align: center; padding: 20px 0;">
                    <div
                        style="width: 80px; height: 80px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem; color: #fff;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 style="margin-bottom: 4px;">
                        <?= e($user['username']) ?>
                    </h2>
                    <p class="text-muted">
                        <?= e($user['email']) ?>
                    </p>
                </div>

                <table class="table">
                    <tr>
                        <td class="text-muted">Vai trò:</td>
                        <td>
                            <?= $user['role'] === 'admin' ? '<span class="badge badge-danger">Admin</span>' : '<span class="badge badge-info">Thành viên</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Số dư:</td>
                        <td style="font-size: 1.4rem; font-weight: 700;" class="text-success">
                            <?= formatMoney($user['balance']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày tham gia:</td>
                        <td>
                            <?= formatDate($user['created_at']) ?>
                        </td>
                    </tr>
                </table>

                <div class="mt-2">
                    <a href="<?= url('/my-orders') ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-receipt"></i> Xem đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2"><i class="fas fa-history"></i> Lịch sử giao dịch gần đây</h3>

                <?php if (empty($transactions)): ?>
                    <div class="empty-state">
                        <i class="fas fa-exchange-alt"></i>
                        <h3>Chưa có giao dịch</h3>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Loại</th>
                                <th>Số tiền</th>
                                <th>Số dư</th>
                                <th>Mô tả</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td>
                                        <?php if ($t['type'] === 'deposit'): ?>
                                            <span class="badge badge-success">Nạp</span>
                                        <?php elseif ($t['type'] === 'purchase'): ?>
                                            <span class="badge badge-warning">Mua</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Hoàn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="<?= $t['type'] === 'purchase' ? 'text-danger' : 'text-success' ?>">
                                        <?= $t['type'] === 'purchase' ? '-' : '+' ?>
                                        <?= formatMoney($t['amount']) ?>
                                    </td>
                                    <td>
                                        <?= formatMoney($t['balance_after']) ?>
                                    </td>
                                    <td
                                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($t['description']) ?>
                                    </td>
                                    <td>
                                        <?= formatDate($t['created_at']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>