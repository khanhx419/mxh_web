<div class="admin-header">
    <h1><i class="fas fa-users"></i> Quản lý người dùng</h1>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Số dư</th>
                <th>Ngày tạo</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <?= $u['id'] ?>
                    </td>
                    <td><strong>
                            <?= e($u['username']) ?>
                        </strong></td>
                    <td>
                        <?= e($u['email']) ?>
                    </td>
                    <td>
                        <?= $u['role'] === 'admin'
                            ? '<span class="badge badge-danger">Admin</span>'
                            : '<span class="badge badge-info">User</span>' ?>
                    </td>
                    <td class="text-success">
                        <?= formatMoney($u['balance']) ?>
                    </td>
                    <td>
                        <?= formatDate($u['created_at']) ?>
                    </td>
                    <td>
                        <a href="<?= url('/admin/users/' . $u['id']) ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>