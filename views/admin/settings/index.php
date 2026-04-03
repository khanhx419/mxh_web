<div class="admin-header">
    <h1><i class="fas fa-cogs"></i> Cài Đặt Chung</h1>
</div>

<form id="settings-form" action="<?= url('/admin/settings/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="site_logo_data" id="site_logo_data">

    <!-- Ngân hàng -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-university"></i> Cấu hình Ngân hàng</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Thông tin ngân hàng dùng để tạo QR nạp tiền tự động.</p>

        <div class="form-group">
            <label>Tên ngân hàng</label>
            <input type="text" name="bank_name" class="form-control" value="<?= e($settings['bank_name'] ?? '') ?>" placeholder="Ví dụ: MBBank">
        </div>
        <div class="form-group">
            <label>Số tài khoản</label>
            <input type="text" name="bank_acc_number" class="form-control" value="<?= e($settings['bank_acc_number'] ?? '') ?>" placeholder="Ví dụ: 0123456789">
        </div>
        <div class="form-group">
            <label>Tên chủ tài khoản</label>
            <input type="text" name="bank_acc_name" class="form-control" value="<?= e($settings['bank_acc_name'] ?? '') ?>" placeholder="Ví dụ: NGUYEN VAN A">
        </div>
        <div class="form-group">
            <label>Tiền tố nội dung chuyển khoản</label>
            <input type="text" name="bank_prefix" class="form-control" value="<?= e($settings['bank_prefix'] ?? 'NAP') ?>" placeholder="Ví dụ: NAP">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Nội dung CK: [tiền tố][mã user]. VD: NAP12345</small>
        </div>
    </div>

    <!-- Thông báo Nạp tiền -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-money-bill-wave"></i> Thông báo Nạp tiền</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Hiển thị cảnh báo ở <strong>trang Nạp tiền</strong>. Dùng <code>[nội dung]</code> để chèn mã CK của user.</p>

        <div class="form-group">
            <label>Nội dung cảnh báo</label>
            <textarea name="deposit_notice" class="form-control" rows="3"><?= e($settings['deposit_notice'] ?? 'Vui lòng nạp theo nội dung sau: [nội dung]. Nếu sau 10p tiền không vào tài khoản thì liên hệ admin.') ?></textarea>
            <small class="form-hint"><i class="fas fa-lightbulb"></i> <code>[nội dung]</code> sẽ tự động được thay bằng mã CK của từng user</small>
        </div>
    </div>

    <!-- Cấu hình trang Nạp tiền -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-qrcode"></i> Cấu hình trang Nạp tiền</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Tùy chỉnh hình QR Code và nội dung chuyển khoản hiển thị cho người dùng trên trang Nạp tiền.</p>

        <div class="form-group">
            <label>Hình QR Code tùy chỉnh</label>
            <?php if (!empty($settings['deposit_qr_image'])): ?>
                <div style="margin-bottom:8px">
                    <img src="<?= asset($settings['deposit_qr_image']) ?>" style="max-width:200px;border-radius:12px;border:1px solid var(--border-color);padding:4px;background:#fff">
                    <p style="font-size:.78rem;color:var(--text-muted);margin-top:4px"><i class="fas fa-check-circle" style="color:var(--accent-success)"></i> Đang sử dụng QR tùy chỉnh</p>
                </div>
            <?php endif; ?>
            <input type="file" name="deposit_qr_image" class="form-control" accept="image/*">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Upload ảnh QR Code tùy chỉnh. Để trống sẽ dùng QR tự động từ VietQR.</small>
        </div>

        <div class="form-group">
            <label>Nội dung chuyển khoản hiển thị</label>
            <textarea name="deposit_transfer_details" class="form-control" rows="3" placeholder="Ví dụ: Chuyển khoản theo cú pháp: NAP [mã user]..."><?= e($settings['deposit_transfer_details'] ?? '') ?></textarea>
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Nội dung bổ sung hiển thị bên dưới QR code. Để trống sẽ dùng mặc định.</small>
        </div>
    </div>

    <!-- Thông báo Popup (Floating) -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-bullhorn"></i> Thông báo Popup (Floating)</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Popup liên hệ hiển thị khi user truy cập website.</p>

        <div class="form-group">
            <label>Trạng thái Popup</label>
            <select name="popup_enabled" class="form-control">
                <option value="1" <?= ($settings['popup_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>Bật hiển thị</option>
                <option value="0" <?= ($settings['popup_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>Tắt hiển thị</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tên chủ shop</label>
            <input type="text" name="popup_owner_name" class="form-control" value="<?= e($settings['popup_owner_name'] ?? 'Bùi Đình Bình') ?>">
        </div>
        <div class="form-group">
            <label>Số điện thoại / Zalo</label>
            <input type="text" name="popup_phone" class="form-control" value="<?= e($settings['popup_phone'] ?? '0377994308') ?>">
        </div>
        <div class="form-group">
            <label>Cam kết dịch vụ</label>
            <textarea name="popup_notice_text" class="form-control" rows="3"><?= e($settings['popup_notice_text'] ?? 'Cam kết chất lượng dịch vụ tốt nhất, giá cả hợp lý và bảo đảm quyền lợi cho khách hàng. Mọi giao dịch thông qua các kênh khác đều không thuộc trách nhiệm của shop.') ?></textarea>
        </div>
    </div>

    <!-- Thông báo Website chung -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-bell"></i> Thông báo Website</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Nội dung popup chung khi user truy cập. Để trống = không hiện.</p>

        <div class="form-group">
            <label>Nội dung thông báo</label>
            <textarea name="site_notice" class="form-control" rows="4" placeholder="Ví dụ: Chào mừng bạn..."><?= e($settings['site_notice'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Vòng quay -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-dharmachakra"></i> Vòng Quay May Mắn</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Chi tiết giải thưởng tại <a href="<?= url('/admin/lucky-wheel') ?>">Vòng quay</a>.</p>

        <div class="form-group">
            <label>Giá mỗi lượt quay (VNĐ)</label>
            <input type="number" name="wheel_spin_cost" class="form-control" value="<?= e($settings['wheel_spin_cost'] ?? '10000') ?>">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Đặt 0 = miễn phí</small>
        </div>
    </div>

    <!-- Điểm danh -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-calendar-check"></i> Điểm Danh Hàng Ngày</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Cấu hình phần thưởng điểm danh. Sau 7 ngày chu kỳ tự reset.</p>

        <div class="form-group">
            <label>Lượt quay free mỗi ngày</label>
            <input type="number" name="checkin_spins_per_day" class="form-control" value="<?= e($settings['checkin_spins_per_day'] ?? '1') ?>">
        </div>
        <div class="form-group">
            <label>Bonus lượt quay ngày thứ 7</label>
            <input type="number" name="checkin_bonus_day7" class="form-control" value="<?= e($settings['checkin_bonus_day7'] ?? '3') ?>">
        </div>
        <div class="form-group">
            <label>Điểm xanh mỗi lần điểm danh</label>
            <input type="number" name="checkin_green_points" class="form-control" value="<?= e($settings['checkin_green_points'] ?? '5') ?>">
        </div>
    </div>

    <!-- Logo Website -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-image"></i> Logo Website</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Upload & crop logo giống Facebook. Kéo/thu phóng để chỉnh vị trí trước khi lưu.</p>

        <?php if (!empty($settings['site_logo'])): ?>
            <div class="form-group">
                <label>Logo hiện tại</label>
                <div style="background: var(--bg-input); padding: 12px; border-radius: 8px; display: inline-block;">
                    <img src="<?= asset($settings['site_logo']) ?>" alt="Logo" style="max-height: 50px; border-radius: 4px;">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Chọn ảnh logo mới</label>
            <input type="file" id="logo-input" class="form-control" accept="image/png,image/jpeg,image/webp">
        </div>

        <!-- Cropper Modal -->
        <div id="cropper-modal" style="display:none; background:rgba(0,0,0,0.85); position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:var(--bg-card,#1a1a2e); border-radius:16px; padding:24px; max-width:600px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.5);">
                <h4 style="margin-bottom:16px; color:var(--text-primary,#fff);"><i class="fas fa-crop-alt"></i> Cắt & Chỉnh Logo</h4>
                <div style="max-height:400px; overflow:hidden; border-radius:8px; background:#000;">
                    <img id="cropper-image" style="display:block; max-width:100%;">
                </div>
                <div style="margin-top:16px; display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" id="cropper-cancel" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</button>
                    <button type="button" id="cropper-save" class="btn btn-primary"><i class="fas fa-check"></i> Áp dụng</button>
                </div>
            </div>
        </div>

        <div id="logo-preview-container" style="display:none; margin-top:10px;">
            <label>Xem trước logo đã crop:</label>
            <div style="background:var(--bg-input); padding:12px; border-radius:8px; display:inline-block;">
                <img id="logo-preview" style="max-height:50px; border-radius:4px;">
            </div>
            <span style="color:var(--accent-success); font-size:0.85rem; margin-left:8px;"><i class="fas fa-check-circle"></i> Sẵn sàng lưu</span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="margin-bottom:40px"><i class="fas fa-save"></i> Lưu Cài Đặt</button>
</form>

<!-- Cropper.js CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logo-input');
    const cropperModal = document.getElementById('cropper-modal');
    const cropperImage = document.getElementById('cropper-image');
    let cropper = null;

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            cropperImage.src = ev.target.result;
            cropperModal.style.display = 'block';
            if (cropper) cropper.destroy();
            cropper = new Cropper(cropperImage, {
                viewMode: 1, dragMode: 'move', autoCropArea: 0.9,
                restore: false, guides: true, center: true, highlight: false,
                cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false
            });
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('cropper-cancel').addEventListener('click', function() {
        cropperModal.style.display = 'none';
        if (cropper) { cropper.destroy(); cropper = null; }
        logoInput.value = '';
    });

    document.getElementById('cropper-save').addEventListener('click', function() {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({ maxWidth: 400, maxHeight: 120 });
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('site_logo_data').value = dataUrl;
        document.getElementById('logo-preview').src = dataUrl;
        document.getElementById('logo-preview-container').style.display = 'block';
        cropperModal.style.display = 'none';
        cropper.destroy(); cropper = null;
    });
});
</script>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-card h3{font-size:1.1rem;font-weight:700;color:var(--text-primary)}
.form-card h3 i{color:var(--accent-primary);margin-right:8px}
.form-section-note{font-size:.82rem;color:var(--text-secondary);margin-bottom:20px;padding:10px 14px;background:rgba(99,102,241,.06);border-left:3px solid var(--accent-primary);border-radius:0 8px 8px 0}
.form-section-note i{color:var(--accent-primary);margin-right:4px}
.form-section-note a{color:var(--accent-primary);text-decoration:underline}
.form-section-note code{background:rgba(99,102,241,.15);padding:2px 6px;border-radius:4px;font-size:.85em}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
.form-hint code{background:rgba(99,102,241,.15);padding:1px 5px;border-radius:3px;font-size:.9em}
</style>