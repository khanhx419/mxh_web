<div class="mystery-bag-page">
<canvas id="particles-canvas"></canvas>

<!-- Hero -->
<div class="mb-hero-section">
    <div class="mb-hero-bg"></div>
    <div class="mb-hero-content">
        <div class="mb-hero-badge"><i class="fas fa-gem"></i> MINIGAME</div>
        <h1 class="mb-hero-title"><span class="mb-title-gradient">Túi Mù</span> Nhân Phẩm</h1>
        <p class="mb-hero-desc">Mở túi mù, thử vận may nhận quà hấp dẫn!</p>
        <div class="mb-hero-stats">
            <div class="mb-stat-item"><i class="fas fa-box-open"></i><span><?= count($bags) ?></span><small>Loại túi</small></div>
            <div class="mb-stat-divider"></div>
            <div class="mb-stat-item"><i class="fas fa-wallet"></i><span class="user-balance"><?= isset($_SESSION['user_balance']) ? formatMoney($_SESSION['user_balance']) : '0đ' ?></span><small>Số dư</small></div>
            <div class="mb-stat-divider"></div>
            <div class="mb-stat-item"><i class="fas fa-trophy"></i><span>100%</span><small>Có thưởng</small></div>
        </div>
    </div>
</div>


<!-- Bag Grid -->
<div class="mb-section">
    <div class="mb-section-header">
        <h2><i class="fas fa-shopping-bag"></i> Chọn Túi Mù</h2>
        <p>Click vào túi để mở thử vận may!</p>
    </div>
    <div class="mb-bag-grid">
        <?php foreach ($bags as $bag): ?>
        <div class="mb-bag-card" data-bag-id="<?= $bag['id'] ?>">
            <div class="mb-card-glow"></div>
            <?php if ($bag['price'] > 50000): ?>
                <div class="mb-card-badge mb-badge-hot"><i class="fas fa-fire"></i> HOT</div>
            <?php else: ?>
                <div class="mb-card-badge mb-badge-new"><i class="fas fa-bolt"></i> PHỔ BIẾN</div>
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
                    <span class="mb-price-label">/ lần mở</span>
                </div>
            </div>
            <div class="mb-card-actions">
                <button class="mb-btn-open" style="flex:1" onclick="event.stopPropagation(); showPurchaseModal(<?= $bag['id'] ?>, '<?= e($bag['name']) ?>', <?= $bag['price'] ?>)"><i class="fas fa-unlock-alt"></i> Mở Ngay</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</div>


<!-- Purchase Modal (NO FREE SPIN) -->
<div class="mb-modal-overlay" id="purchaseModal">
<div class="mb-modal">
    <button class="mb-modal-close" onclick="closePurchaseModal()"><i class="fas fa-times"></i></button>
    <div class="mb-modal-header">
        <div class="mb-modal-icon"><i class="fas fa-shopping-bag"></i></div>
        <h3 id="modal-bag-name">Túi Mù</h3>
    </div>
    <div class="mb-modal-body">
        <div class="mb-modal-row"><span>Giá tiền</span><strong id="modal-price" class="mb-text-accent">0đ</strong></div>
        <div class="mb-modal-row"><span>Số dư hiện tại</span><strong class="mb-text-info user-balance"><?= isset($_SESSION['user_balance']) ? formatMoney($_SESSION['user_balance']) : '0đ' ?></strong></div>
        <div class="mb-modal-divider"></div>
        <div class="mb-modal-row mb-modal-total"><span>Tổng thanh toán</span><strong id="modal-total" class="mb-text-warning">0đ</strong></div>
    </div>
    <div class="mb-modal-footer">
        <button class="mb-modal-btn mb-modal-btn-cancel" onclick="closePurchaseModal()">Đóng</button>
        <button class="mb-modal-btn mb-modal-btn-confirm" id="btn-confirm-purchase"><i class="fas fa-unlock-alt"></i> Mở Túi</button>
    </div>
</div>
</div>

<!-- Result Modal -->
<div class="mb-modal-overlay" id="resultModal">
<div class="mb-modal mb-modal-result">
    <div class="mb-modal-body text-center">
        <div id="loading-animation">
            <div class="mb-loading-spinner"><div class="mb-spinner-ring"></div><i class="fas fa-box-open"></i></div>
            <h4>Đang mở túi...</h4>
        </div>
        <div id="result-content" style="display:none;">
            <div class="mb-result-icon mb-result-success-icon"><i class="fas fa-gift"></i></div>
            <h3 class="mb-result-title" id="result-title">Chúc mừng!</h3>
            <p class="mb-result-desc" id="result-desc"></p>
            <div class="mb-result-reward"><small>Kết quả</small><strong id="result-detail"></strong></div>
            <button class="mb-modal-btn mb-modal-btn-confirm" onclick="closeResultModal();location.reload();"><i class="fas fa-check"></i> Đóng</button>
        </div>
        <div id="error-content" style="display:none;">
            <div class="mb-result-icon mb-result-error-icon"><i class="fas fa-times-circle"></i></div>
            <h4 class="mb-text-danger">Thất bại</h4>
            <p id="error-desc"></p>
            <button class="mb-modal-btn mb-modal-btn-cancel" onclick="closeResultModal()">Đóng</button>
        </div>
    </div>
</div>
</div>

<div id="mb-config" 
     data-csrf="<?= e($_SESSION['csrf_token'] ?? '') ?>" 
     data-checkin-url="<?= url('/mystery-bag/checkin') ?>" 
     data-open-url="<?= url('/mystery-bag/open/') ?>" 
     style="display:none;"></div>