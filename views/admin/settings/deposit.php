<div class="admin-header">
    <h1><i class="fas fa-money-check-alt"></i> Cấu Hình Nạp Tiền</h1>
    <p class="text-secondary">Quản lý API ngân hàng, nạp thẻ cào và chiết khấu nạp tiền</p>
</div>

<div style="margin-bottom:16px;">
    <a href="<?= url('/admin/settings') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại Cài Đặt Chung</a>
</div>

<form action="<?= url('/admin/settings/deposit/update') ?>" method="POST">
    <?= csrfField() ?>

    <!-- Cấu hình API chung -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-plug"></i> Cấu Hình API Chung</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> URL và Token API dùng cho hệ thống tự động kiểm tra giao dịch ngân hàng (cron deposit). Đây là cấu hình master, nếu để trống sẽ fallback về giá trị trong file <code>.env</code>.</p>

        <div class="form-group">
            <label>Bank API URL</label>
            <input type="text" name="bank_api_url" class="form-control" value="<?= e($settings['bank_api_url'] ?? '') ?>" placeholder="https://thueapibank.vn">
            <small class="form-hint"><i class="fas fa-lightbulb"></i> URL gốc của nhà cung cấp API ngân hàng (VD: thueapibank.vn hoặc web2m.com)</small>
        </div>
        <div class="form-group">
            <label>Bank API Token</label>
            <div style="position:relative">
                <input type="password" name="bank_api_token" id="bank_api_token_input" class="form-control" value="<?= e($settings['bank_api_token'] ?? '') ?>" placeholder="Token API xác thực" style="padding-right:40px">
                <button type="button" onclick="toggleTokenVisibility(this, 'bank_api_token_input')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.9rem;padding:4px" title="Hiện/ẩn token">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <small class="form-hint"><i class="fas fa-lightbulb"></i> Token xác thực từ nhà cung cấp API. Giữ bí mật, không chia sẻ.</small>
        </div>
    </div>

    <!-- Chiết khấu nạp tiền -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-percent"></i> Chiết Khấu Nạp Tiền</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Phần trăm bonus thêm khi user nạp tiền qua ngân hàng. VD: 10 = nạp 100k nhận 110k.</p>

        <div class="form-group">
            <label>Chiết khấu (%)</label>
            <input type="number" name="deposit_discount" class="form-control" value="<?= e($settings['deposit_discount'] ?? '0') ?>" min="0" max="100">
        </div>
    </div>

    <!-- MBBank -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-university"></i> MBBank API</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Token API từ Web2M / ThueAPIBank cho MBBank.</p>

        <div class="form-group">
            <label>API Token</label>
            <input type="text" name="bank_mb_api_token" class="form-control" value="<?= e($settings['bank_mb_api_token'] ?? '') ?>" placeholder="Token API MBBank">
        </div>
        <div class="form-group">
            <label>Số tài khoản</label>
            <input type="text" name="bank_mb_account_number" class="form-control" value="<?= e($settings['bank_mb_account_number'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu tài khoản</label>
            <input type="password" name="bank_mb_account_password" class="form-control" value="<?= e($settings['bank_mb_account_password'] ?? '') ?>">
        </div>
    </div>

    <!-- Vietcombank -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-university"></i> Vietcombank API</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Token API từ Web2M / ThueAPIBank cho Vietcombank.</p>

        <div class="form-group">
            <label>API Token</label>
            <input type="text" name="bank_vcb_api_token" class="form-control" value="<?= e($settings['bank_vcb_api_token'] ?? '') ?>" placeholder="Token API VCB">
        </div>
        <div class="form-group">
            <label>Số tài khoản</label>
            <input type="text" name="bank_vcb_account_number" class="form-control" value="<?= e($settings['bank_vcb_account_number'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu tài khoản</label>
            <input type="password" name="bank_vcb_account_password" class="form-control" value="<?= e($settings['bank_vcb_account_password'] ?? '') ?>">
        </div>
    </div>

    <!-- ACB -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-university"></i> ACB API</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Token API từ Web2M / ThueAPIBank cho ACB.</p>

        <div class="form-group">
            <label>API Token</label>
            <input type="text" name="bank_acb_api_token" class="form-control" value="<?= e($settings['bank_acb_api_token'] ?? '') ?>" placeholder="Token API ACB">
        </div>
        <div class="form-group">
            <label>Số tài khoản</label>
            <input type="text" name="bank_acb_account_number" class="form-control" value="<?= e($settings['bank_acb_account_number'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu tài khoản</label>
            <input type="password" name="bank_acb_account_password" class="form-control" value="<?= e($settings['bank_acb_account_password'] ?? '') ?>">
        </div>
    </div>

    <!-- Momo -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-mobile-alt"></i> Momo API</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Token API cho ví Momo.</p>

        <div class="form-group">
            <label>API Token</label>
            <input type="text" name="bank_momo_api_token" class="form-control" value="<?= e($settings['bank_momo_api_token'] ?? '') ?>" placeholder="Token API Momo">
        </div>
    </div>

    <!-- TheSieuRe -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-wallet"></i> TheSieuRe API</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Token API cho TheSieuRe.</p>

        <div class="form-group">
            <label>API Token</label>
            <input type="text" name="bank_thesieure_api_token" class="form-control" value="<?= e($settings['bank_thesieure_api_token'] ?? '') ?>" placeholder="Token API TheSieuRe">
        </div>
    </div>

    <!-- Card Charging -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-sim-card"></i> Nạp Thẻ Cào (Card Charging API)</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Cấu hình API nạp thẻ cào tự động (thegioithe, trumthe, v.v.).</p>

        <div class="form-group">
            <label>API URL</label>
            <input type="text" name="card_api_url" class="form-control" value="<?= e($settings['card_api_url'] ?? '') ?>" placeholder="https://thegioithe.com/chargingws/v2">
        </div>
        <div class="form-group">
            <label>Partner ID</label>
            <input type="text" name="card_partner_id" class="form-control" value="<?= e($settings['card_partner_id'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Partner Key</label>
            <input type="text" name="card_partner_key" class="form-control" value="<?= e($settings['card_partner_key'] ?? '') ?>">
        </div>
    </div>

    <!-- Card Fees -->
    <div class="form-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:4px"><i class="fas fa-hand-holding-usd"></i> Phí Chiết Khấu Thẻ Cào (%)</h3>
        <p class="form-section-note"><i class="fas fa-info-circle"></i> Phần trăm phí tính trên mệnh giá thẻ. VD: 20 = nạp thẻ 100k nhận 80k.</p>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
            <div class="form-group">
                <label>Viettel (%)</label>
                <input type="number" name="card_fees_viettel" class="form-control" value="<?= e($settings['card_fees_viettel'] ?? '20') ?>" min="0" max="100">
            </div>
            <div class="form-group">
                <label>Mobifone (%)</label>
                <input type="number" name="card_fees_mobifone" class="form-control" value="<?= e($settings['card_fees_mobifone'] ?? '20') ?>" min="0" max="100">
            </div>
            <div class="form-group">
                <label>Vinaphone (%)</label>
                <input type="number" name="card_fees_vinaphone" class="form-control" value="<?= e($settings['card_fees_vinaphone'] ?? '20') ?>" min="0" max="100">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="margin-bottom:40px"><i class="fas fa-save"></i> Lưu Cấu Hình Nạp Tiền</button>
</form>

<style>
.form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:24px}
.form-card h3{font-size:1.1rem;font-weight:700;color:var(--text-primary)}
.form-card h3 i{color:var(--accent-primary);margin-right:8px}
.form-section-note{font-size:.82rem;color:var(--text-secondary);margin-bottom:20px;padding:10px 14px;background:rgba(99,102,241,.06);border-left:3px solid var(--accent-primary);border-radius:0 8px 8px 0}
.form-section-note i{color:var(--accent-primary);margin-right:4px}
.form-section-note code{background:rgba(99,102,241,.15);padding:2px 6px;border-radius:4px;font-size:.85em}
.form-hint{display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)}
.form-hint i{margin-right:4px;color:var(--accent-warning)}
</style>

<script>
function toggleTokenVisibility(btn, inputId) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
