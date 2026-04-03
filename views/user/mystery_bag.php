<div class="mystery-bag-page">
<canvas id="particles-canvas"></canvas>

<!-- Hero -->
<div class="mb-hero-section">
    <div class="mb-hero-bg"></div>
    <div class="mb-hero-content">
        <div class="mb-hero-badge"><i class="fas fa-box-open"></i> SHOP</div>
        <h1 class="mb-hero-title"><span class="mb-title-gradient">Túi Mù</span> Tài Khoản</h1>
        <p class="mb-hero-desc">Mua túi mù — nhận ngẫu nhiên 1 tài khoản từ kho hàng!</p>
        <div class="mb-hero-stats">
            <div class="mb-stat-item"><i class="fas fa-box-open"></i><span><?= count($bags) ?></span><small>Loại túi</small></div>
            <div class="mb-stat-divider"></div>
            <div class="mb-stat-item"><i class="fas fa-wallet"></i><span class="user-balance"><?= isset($_SESSION['user_balance']) ? formatMoney($_SESSION['user_balance']) : '0đ' ?></span><small>Số dư</small></div>
            <div class="mb-stat-divider"></div>
            <div class="mb-stat-item"><i class="fas fa-check-circle"></i><span>100%</span><small>Nhận acc</small></div>
        </div>
    </div>
</div>


<!-- Bag Grid -->
<div class="mb-section">
    <div class="mb-section-header">
        <h2><i class="fas fa-shopping-bag"></i> Chọn Túi Mù</h2>
        <p>Mỗi túi chứa các tài khoản khác nhau. Bấm mua để nhận 1 acc ngẫu nhiên!</p>
    </div>
    <div class="mb-bag-grid">
        <?php foreach ($bags as $bag): ?>
        <div class="mb-bag-card" data-bag-id="<?= $bag['id'] ?>">
            <div class="mb-card-glow"></div>
            <?php if ($bag['stock'] > 0): ?>
                <div class="mb-card-badge mb-badge-new"><i class="fas fa-check"></i> Còn <?= $bag['stock'] ?> acc</div>
            <?php else: ?>
                <div class="mb-card-badge mb-badge-sold"><i class="fas fa-times"></i> Hết hàng</div>
            <?php endif; ?>
            <div class="mb-card-visual">
                <?php
                    $bagImage = !empty($bag['image']) 
                        ? url('/uploads/mystery_bags/' . $bag['image']) 
                        : url('/uploads/mystery_bags/default.png');
                ?>
                <div class="mb-bag-img-wrap">
                    <img src="<?= $bagImage ?>" alt="<?= e($bag['name']) ?>" class="mb-bag-img">
                </div>
            </div>
            <div class="mb-card-info">
                <h3 class="mb-card-name"><?= e($bag['name']) ?></h3>
                <p class="mb-card-desc"><?= e($bag['description']) ?></p>
                <div class="mb-card-price">
                    <span class="mb-price-value"><?= formatMoney($bag['price']) ?></span>
                    <span class="mb-price-label">/ 1 tài khoản</span>
                </div>
            </div>
            <div class="mb-card-actions">
                <?php if ($bag['stock'] > 0): ?>
                <button class="mb-btn-open" style="flex:1" onclick="event.stopPropagation(); showPurchaseModal(<?= $bag['id'] ?>, '<?= e($bag['name']) ?>', <?= $bag['price'] ?>, <?= $bag['stock'] ?>)"><i class="fas fa-shopping-cart"></i> Mua Ngay</button>
                <?php else: ?>
                <button class="mb-btn-open mb-btn-disabled" style="flex:1" disabled><i class="fas fa-ban"></i> Hết Hàng</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</div>


<!-- Purchase Confirm Modal -->
<div class="mb-modal-overlay" id="purchaseModal">
<div class="mb-modal">
    <button class="mb-modal-close" onclick="closePurchaseModal()"><i class="fas fa-times"></i></button>
    <div class="mb-modal-header">
        <div class="mb-modal-icon"><i class="fas fa-shopping-cart"></i></div>
        <h3 id="modal-bag-name">Túi Mù</h3>
    </div>
    <div class="mb-modal-body">
        <div class="mb-modal-row"><span>Giá tiền</span><strong id="modal-price" class="mb-text-accent">0đ</strong></div>
        <div class="mb-modal-row"><span>Số dư hiện tại</span><strong class="mb-text-info user-balance"><?= isset($_SESSION['user_balance']) ? formatMoney($_SESSION['user_balance']) : '0đ' ?></strong></div>
        <div class="mb-modal-row"><span>Kho hàng</span><strong id="modal-stock" class="mb-text-success">0 acc</strong></div>
        <div class="mb-modal-divider"></div>
        <div class="mb-modal-row mb-modal-total"><span>Tổng thanh toán</span><strong id="modal-total" class="mb-text-warning">0đ</strong></div>
        <div style="margin-top:8px;padding:8px 12px;background:rgba(0,212,170,.06);border-radius:8px;border-left:3px solid var(--accent-success,#00d4aa)">
            <small style="font-size:.78rem;color:var(--text-secondary)"><i class="fas fa-info-circle" style="color:var(--accent-success);margin-right:4px"></i> Bạn sẽ nhận ngẫu nhiên 1 tài khoản từ kho. Thông tin acc sẽ hiện ngay sau khi mua.</small>
        </div>
    </div>
    <div class="mb-modal-footer">
        <button class="mb-modal-btn mb-modal-btn-cancel" onclick="closePurchaseModal()">Huỷ</button>
        <button class="mb-modal-btn mb-modal-btn-confirm" id="btn-confirm-purchase"><i class="fas fa-shopping-cart"></i> Xác Nhận Mua</button>
    </div>
</div>
</div>

<!-- Result Modal (Account Info) -->
<div class="mb-modal-overlay" id="resultModal">
<div class="mb-modal mb-modal-result">
    <div class="mb-modal-body text-center">
        <div id="loading-animation">
            <div class="mb-loading-spinner"><div class="mb-spinner-ring"></div><i class="fas fa-box-open"></i></div>
            <h4>Đang xử lý đơn hàng...</h4>
        </div>
        <div id="result-content" style="display:none;">
            <div class="mb-result-icon mb-result-success-icon"><i class="fas fa-check-circle"></i></div>
            <h3 class="mb-result-title" style="color:var(--accent-success)">🎉 Mua Thành Công!</h3>
            <p class="mb-result-desc" id="result-desc"></p>
            
            <!-- Account Info Card -->
            <div class="mb-account-card">
                <div class="mb-account-header"><i class="fas fa-user-shield"></i> Thông Tin Tài Khoản</div>
                <div class="mb-account-row" id="acc-username-row">
                    <span class="mb-account-label"><i class="fas fa-user"></i> Tài khoản</span>
                    <div class="mb-account-value-wrap">
                        <span class="mb-account-value" id="acc-username"></span>
                        <button class="mb-copy-btn" onclick="copyText('acc-username')" title="Copy"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                <div class="mb-account-row" id="acc-password-row">
                    <span class="mb-account-label"><i class="fas fa-lock"></i> Mật khẩu</span>
                    <div class="mb-account-value-wrap">
                        <span class="mb-account-value" id="acc-password"></span>
                        <button class="mb-copy-btn" onclick="copyText('acc-password')" title="Copy"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                <div class="mb-account-row" id="acc-email-row">
                    <span class="mb-account-label"><i class="fas fa-envelope"></i> Email</span>
                    <div class="mb-account-value-wrap">
                        <span class="mb-account-value" id="acc-email"></span>
                        <button class="mb-copy-btn" onclick="copyText('acc-email')" title="Copy"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                <div class="mb-account-row" id="acc-extra-row" style="display:none">
                    <span class="mb-account-label"><i class="fas fa-info-circle"></i> Ghi chú</span>
                    <span class="mb-account-value" id="acc-extra" style="white-space:pre-wrap"></span>
                </div>
            </div>

            <div class="mb-account-warning">
                <i class="fas fa-exclamation-triangle"></i> Hãy đổi mật khẩu ngay sau khi nhận! Chúng tôi không chịu trách nhiệm nếu bạn không đổi mật khẩu.
            </div>

            <button class="mb-modal-btn mb-modal-btn-confirm" onclick="closeResultModal();location.reload();"><i class="fas fa-check"></i> Đã Ghi Nhận</button>
        </div>
        <div id="error-content" style="display:none;">
            <div class="mb-result-icon mb-result-error-icon"><i class="fas fa-times-circle"></i></div>
            <h4 class="mb-text-danger">Không Thành Công</h4>
            <p id="error-desc"></p>
            <button class="mb-modal-btn mb-modal-btn-cancel" onclick="closeResultModal()">Đóng</button>
        </div>
    </div>
</div>
</div>

<div id="mb-config" 
     data-csrf="<?= e($_SESSION['csrf_token'] ?? '') ?>" 
     data-open-url="<?= url('/mystery-bag/open/') ?>" 
     style="display:none;"></div>

<style>
/* Account Info Card in Result Modal */
.mb-account-card {
    background: var(--bg-body);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    margin: 16px 0 12px;
    text-align: left;
}
.mb-account-header {
    padding: 10px 16px;
    font-size: .84rem;
    font-weight: 700;
    color: var(--accent-primary);
    background: rgba(99,102,241,.06);
    border-bottom: 1px solid var(--border-color);
}
.mb-account-header i { margin-right: 6px; }
.mb-account-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(255,255,255,.04);
    gap: 8px;
}
.mb-account-row:last-child { border-bottom: none; }
.mb-account-label {
    font-size: .8rem;
    color: var(--text-muted);
    white-space: nowrap;
    min-width: 90px;
}
.mb-account-label i { margin-right: 4px; width: 14px; text-align: center; }
.mb-account-value-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    justify-content: flex-end;
}
.mb-account-value {
    font-size: .86rem;
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-all;
    font-family: 'Courier New', monospace;
}
.mb-copy-btn {
    background: rgba(99,102,241,.1);
    border: none;
    color: var(--accent-primary);
    padding: 4px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: .78rem;
    transition: all .2s;
    flex-shrink: 0;
}
.mb-copy-btn:hover { background: var(--accent-primary); color: #fff; }
.mb-account-warning {
    font-size: .78rem;
    color: var(--accent-warning);
    padding: 8px 12px;
    background: rgba(255,167,38,.06);
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid rgba(255,167,38,.12);
}
.mb-account-warning i { margin-right: 4px; }
.mb-btn-disabled {
    opacity: .5;
    cursor: not-allowed !important;
    background: var(--bg-input) !important;
    color: var(--text-muted) !important;
}
.mb-badge-sold {
    background: rgba(239,68,68,.15) !important;
    color: #ef4444 !important;
}
</style>