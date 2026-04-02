<div class="admin-header">
    <h1><i class="fas fa-user"></i> Chi tiết người dùng:
        <?= e($user['username']) ?>
    </h1>
    <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="grid-2">
    <!-- User Info -->
    <div>
        <div class="card mb-2">
            <div class="card-body">
                <h3 class="mb-2">Thông tin tài khoản</h3>
                <table class="table">
                    <tr>
                        <td class="text-muted">ID:</td>
                        <td>
                            <?= $user['id'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Username:</td>
                        <td><strong>
                                <?= e($user['username']) ?>
                            </strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td>
                            <?= e($user['email']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Vai trò:</td>
                        <td>
                            <?= $user['role'] === 'admin'
                                ? '<span class="badge badge-danger">Admin</span>'
                                : '<span class="badge badge-info">User</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Số dư:</td>
                        <td class="text-success" style="font-size: 1.3rem; font-weight: 700;">
                            <?= formatMoney($user['balance']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày đăng ký:</td>
                        <td>
                            <?= formatDate($user['created_at']) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Add Balance -->
        <div class="card mb-2">
            <div class="card-body">
                <h3 class="mb-2"><i class="fas fa-wallet"></i> Nạp tiền</h3>
                <form method="POST" action="<?= url('/admin/users/update-balance/' . $user['id']) ?>">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Số tiền nạp (VNĐ)</label>
                        <input type="number" name="amount" class="form-control" min="1000" placeholder="VD: 100000"
                            required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-plus-circle"></i> Nạp tiền
                    </button>
                </form>
            </div>
        </div>

        <!-- Add Spins -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2" style="color: var(--accent-warning);"><i class="fas fa-dharmachakra"></i> Thêm lượt quay</h3>
                <p class="text-secondary" style="font-size: 0.85rem; margin-bottom: 12px;">
                    Lượt quay hiện tại: <strong style="color: var(--accent-warning); font-size: 1.1rem;"><?= $freeSpins ?? 0 ?></strong>
                </p>
                <form method="POST" action="<?= url('/admin/users/add-spins/' . $user['id']) ?>">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Số lượt quay thêm</label>
                        <input type="number" name="spins" class="form-control" min="1" placeholder="VD: 5" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-block" style="color: #fff;">
                        <i class="fas fa-plus"></i> Cộng lượt quay
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div>
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2">Lịch sử giao dịch</h3>
                <?php if (empty($transactions)): ?>
                    <p class="text-muted">Chưa có giao dịch nào.</p>
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
                                            <span class="badge badge-success">Nạp tiền</span>
                                        <?php elseif ($t['type'] === 'purchase'): ?>
                                            <span class="badge badge-warning">Mua hàng</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Hoàn tiền</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="<?= $t['type'] === 'purchase' ? 'text-danger' : 'text-success' ?>">
                                        <?= $t['type'] === 'purchase' ? '-' : '+' ?>
                                        <?= formatMoney($t['amount']) ?>
                                    </td>
                                    <td>
                                        <?= formatMoney($t['balance_after']) ?>
                                    </td>
                                    <td>
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