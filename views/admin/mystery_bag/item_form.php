<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> <?= $item ? 'Sửa' : 'Thêm' ?> Tài Khoản - <?= e($bag['name']) ?></h1>
    <a href="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<?php
    // Parse existing content into fields
    $acct_username = '';
    $acct_password = '';
    $acct_email = '';
    $acct_extra = '';
    if ($item && !empty($item['content'])) {
        $lines = explode("\n", $item['content']);
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'Tài khoản:') === 0) {
                $acct_username = trim(substr($line, strlen('Tài khoản:')));
            } elseif (stripos($line, 'Mật khẩu:') === 0) {
                $acct_password = trim(substr($line, strlen('Mật khẩu:')));
            } elseif (stripos($line, 'Email:') === 0) {
                $acct_email = trim(substr($line, strlen('Email:')));
            } else {
                $acct_extra .= $line . "\n";
            }
        }
        $acct_extra = trim($acct_extra);
    }
?>

<form action="<?= $item ? url('/admin/mystery-bag/items/update/' . $item['id']) : url('/admin/mystery-bag/' . $bag['id'] . '/items/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:16px;font-size:.95rem"><i class="fas fa-user-circle" style="color:var(--accent-info)"></i> Thông tin tài khoản</h3>

        <div class="form-group">
            <label>Tên hiển thị / Nhãn item *</label>
            <input type="text" name="name" class="form-control" value="<?= e($item['name'] ?? '') ?>" required placeholder="VD: Acc VIP Rank Cao Thủ">
            <small class="form-hint"><i class="fas fa-info-circle"></i> Tên hiển thị cho user khi nhận thưởng.</small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label><i class="fas fa-user" style="color:var(--accent-primary);margin-right:4px"></i> Username / Tài khoản</label>
                <input type="text" name="acct_username" id="acct_username" class="form-control" value="<?= e($acct_username) ?>" placeholder="VD: player123">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock" style="color:var(--accent-warning);margin-right:4px"></i> Password / Mật khẩu</label>
                <div style="position:relative">
                    <input type="text" name="acct_password" id="acct_password" class="form-control" value="<?= e($acct_password) ?>" placeholder="VD: abc@123" style="padding-right:36px">
                    <button type="button" onclick="var i=document.getElementById('acct_password');i.type=i.type==='password'?'text':'password'" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-envelope" style="color:var(--accent-success);margin-right:4px"></i> Email</label>
            <input type="text" name="acct_email" id="acct_email" class="form-control" value="<?= e($acct_email) ?>" placeholder="VD: player123@gmail.com">
        </div>

        <div class="form-group">
            <label>Thông tin bổ sung (tuỳ chọn)</label>
            <textarea name="acct_extra" id="acct_extra" class="form-control" rows="3" placeholder="VD: Server: Việt Nam&#10;Rank: Kim Cương&#10;Skin: 50+ skins"><?= e($acct_extra) ?></textarea>
            <small class="form-hint"><i class="fas fa-info-circle"></i> Thông tin thêm sẽ hiện cho user khi trúng item này.</small>
        </div>

        <!-- Hidden content field built from JS -->
        <input type="hidden" name="content" id="content_combined" value="<?= e($item['content'] ?? '') ?>">
    </div>

    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:16px;font-size:.95rem"><i class="fas fa-sliders-h" style="color:var(--accent-warning)"></i> Cấu hình xác suất</h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label>Giá trị (VNĐ)</label>
                <input type="number" name="value" class="form-control" value="<?= e($item['value'] ?? '0') ?>" step="any">
                <small class="form-hint"><i class="fas fa-info-circle"></i> Giá trị ước tính. Nếu > 0, user sẽ nhận được số tiền này khi trúng.</small>
            </div>
            <div class="form-group">
                <label>Xác suất (trọng số)</label>
                <input type="number" name="probability" class="form-control" value="<?= e($item['probability'] ?? '10') ?>" min="0">
                <small class="form-hint"><i class="fas fa-lightbulb"></i> VD: A=60, B=30, C=10 → 60%, 30%, 10%</small>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?= ($item['status'] ?? 1) ? 'checked' : '' ?>> Kích hoạt (Cho phép rơi item này)
            </label>
            <small class="form-hint"><i class="fas fa-info-circle"></i> Tắt để tạm ẩn item khỏi pool xác suất.</small>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $item ? 'Cập nhật' : 'Thêm mới' ?></button>
    </div>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-card h3 i{margin-right:6px}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>

<script>
document.querySelector('form').addEventListener('submit', function() {
    var u = document.getElementById('acct_username').value.trim();
    var p = document.getElementById('acct_password').value.trim();
    var e = document.getElementById('acct_email').value.trim();
    var x = document.getElementById('acct_extra').value.trim();
    var content = '';
    if (u) content += 'Tài khoản: ' + u + '\n';
    if (p) content += 'Mật khẩu: ' + p + '\n';
    if (e) content += 'Email: ' + e + '\n';
    if (x) content += x;
    document.getElementById('content_combined').value = content.trim();
});
</script>
