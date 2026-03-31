<div class="admin-header">
    <h1><i class="fas fa-cogs"></i> Cài Đặt Chung</h1>
</div>

<form action="<?= url('/admin/settings/update') ?>" method="POST">
    <?= csrfField() ?>

    <!-- Thanh toán / Ngân hàng -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-university"></i> Cấu hình Ngân hàng</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Thông tin ngân hàng dùng để tạo QR nạp tiền tự động. User sẽ thấy thông tin này khi nạp tiền.</p>

        <div class="form-group">
            <label>Tên ngân hàng</label>
            <input type="text" name="bank_name" class="form-control" value="<?= e($settings['bank_name'] ?? '') ?>" placeholder="Ví dụ: MB Bank, Vietcombank">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Tên viết tắt ngân hàng của bạn (dùng cho API tạo QR)</small>
        </div>
        <div class="form-group">
            <label>Số tài khoản</label>
            <input type="text" name="bank_acc_number" class="form-control" value="<?= e($settings['bank_acc_number'] ?? '') ?>" placeholder="Ví dụ: 0123456789">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Số tài khoản nhận tiền nạp từ user</small>
        </div>
        <div class="form-group">
            <label>Tên chủ tài khoản</label>
            <input type="text" name="bank_acc_name" class="form-control" value="<?= e($settings['bank_acc_name'] ?? '') ?>" placeholder="Ví dụ: NGUYEN VAN A">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Viết IN HOA không dấu, đúng như trên ngân hàng</small>
        </div>
        <div class="form-group">
            <label>Tiền tố nội dung chuyển khoản</label>
            <input type="text" name="bank_prefix" class="form-control" value="<?= e($settings['bank_prefix'] ?? 'NAP') ?>" placeholder="Ví dụ: NAP">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Nội dung CK sẽ là: [tiền tố][mã user]. Ví dụ: NAP12345</small>
        </div>
    </div>

    <!-- Thông báo -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-bullhorn"></i> Thông báo Website</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Nội dung sẽ hiển thị dưới dạng popup khi user truy cập website. Để trống nếu không muốn hiện.</p>

        <div class="form-group">
            <label>Nội dung thông báo</label>
            <textarea name="site_notice" class="form-control" rows="4" placeholder="Ví dụ: Chào mừng bạn đến với ShopAcc VN!"><?= e($settings['site_notice'] ?? '') ?></textarea>
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Hỗ trợ HTML cơ bản. Popup sẽ hiện 1 lần/ngày cho mỗi user</small>
        </div>
    </div>

    <!-- Vòng quay -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-dharmachakra"></i> Vòng Quay May Mắn</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Cấu hình giá lượt quay. Chi tiết giải thưởng quản lý tại mục <a href="<?= url('/admin/lucky-wheel') ?>">Vòng quay</a>.</p>

        <div class="form-group">
            <label>Giá mỗi lượt quay (VNĐ)</label>
            <input type="number" name="wheel_spin_cost" class="form-control" value="<?= e($settings['wheel_spin_cost'] ?? '10000') ?>">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Số tiền trừ từ tài khoản user khi quay. Đặt 0 = miễn phí</small>
        </div>
    </div>

    <!-- Điểm danh -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-calendar-check"></i> Điểm Danh Hàng Ngày</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Cấu hình phần thưởng điểm danh. User điểm danh mỗi ngày nhận lượt quay túi mù miễn phí. Sau 7 ngày chu kỳ tự reset.</p>

        <div class="form-group">
            <label>Lượt quay free mỗi ngày</label>
            <input type="number" name="checkin_spins_per_day" class="form-control" value="<?= e($settings['checkin_spins_per_day'] ?? '1') ?>">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Số lượt quay túi mù miễn phí khi điểm danh mỗi ngày (ngày 1-6)</small>
        </div>
        <div class="form-group">
            <label>Bonus lượt quay ngày thứ 7</label>
            <input type="number" name="checkin_bonus_day7" class="form-control" value="<?= e($settings['checkin_bonus_day7'] ?? '3') ?>">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Số lượt quay bonus khi hoàn thành đủ 7 ngày liên tiếp. Nhiều hơn = khuyến khích user điểm danh đủ</small>
        </div>
        <div class="form-group">
            <label>Điểm xanh mỗi lần điểm danh</label>
            <input type="number" name="checkin_green_points" class="form-control" value="<?= e($settings['checkin_green_points'] ?? '5') ?>">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Số điểm xanh (Green Points) cộng vào tài khoản user mỗi lần điểm danh. Dùng cho hệ thống xếp hạng điểm xanh</small>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="margin-bottom:40px"><i class="fas fa-save"></i> Lưu Cài Đặt</button>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-card h3{font-size:1.1rem;font-weight:700;color:var(--text-primary)}
.form-card h3 i{color:var(--accent-primary);margin-right:8px}
.form-section-note{font-size:.82rem;color:var(--text-secondary);margin-bottom:20px;padding:10px 14px;background:rgba(99,102,241,.06);border-left:3px solid var(--accent-primary);border-radius:0 8px 8px 0}
.form-section-note i{color:var(--accent-primary);margin-right:4px}
.form-section-note a{color:var(--accent-primary);text-decoration:underline}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>
