<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-leaf" style="color: var(--accent-success);"></i> Điểm Xanh</h1>
        <p class="text-secondary">Tích điểm xanh khi mua hàng, nạp tiền và tham gia sự kiện</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
        <!-- Left: Points Summary -->
        <div>
            <div class="points-card mb-2">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">🍀</div>
                <div class="points-value" id="gp-total"><?= number_format($totalPoints) ?></div>
                <div class="points-label">Điểm xanh hiện tại</div>
            </div>

            <!-- Exchange Card -->
            <div class="card" style="margin-bottom: 16px;">
                <div class="card-body" style="text-align: center;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 14px;">
                        <i class="fas fa-exchange-alt" style="color: var(--accent-primary);"></i> Đổi Điểm Xanh
                    </h4>
                    <div style="background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(99,102,241,0.08)); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 16px; margin-bottom: 14px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap;">
                            <div style="text-align: center;">
                                <div style="font-size: 1.6rem; font-weight: 800; color: var(--accent-success);">100</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">điểm xanh</div>
                            </div>
                            <i class="fas fa-arrow-right" style="color: var(--accent-primary); font-size: 1.2rem;"></i>
                            <div style="text-align: center;">
                                <div style="font-size: 1.6rem; font-weight: 800; color: var(--accent-warning);">10,000đ</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">số dư tài khoản</div>
                            </div>
                        </div>
                    </div>
                    <?php if (isLoggedIn()): ?>
                        <?php if ($totalPoints >= 100): ?>
                            <button class="btn btn-primary" id="btn-exchange-gp" style="width: 100%;">
                                <i class="fas fa-exchange-alt"></i> Đổi 100 Điểm → 10,000đ
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled style="width: 100%; opacity: 0.5;">
                                <i class="fas fa-lock"></i> Cần thêm <?= 100 - $totalPoints ?> điểm nữa
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập để đổi
                        </a>
                    <?php endif; ?>
                    <div id="exchange-result" style="margin-top: 10px; font-size: 0.85rem; display: none;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 14px;">
                        <i class="fas fa-seedling" style="color: var(--accent-success);"></i> Cách kiếm điểm
                    </h4>

                    <div class="points-earn-item">
                        <div class="points-earn-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.88rem;">Nạp tiền</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">+1 điểm / 10.000đ nạp</div>
                        </div>
                    </div>

                    <div class="points-earn-item">
                        <div class="points-earn-icon" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.88rem;">Mua hàng</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">+2 điểm / đơn hàng</div>
                        </div>
                    </div>

                    <div class="points-earn-item">
                        <div class="points-earn-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.88rem;">Điểm danh</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">+5 điểm / lần điểm danh</div>
                        </div>
                    </div>

                    <div class="points-earn-item">
                        <div class="points-earn-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                            <i class="fas fa-calendar-star"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.88rem;">Sự kiện</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">Nhân đôi/ba trong sự kiện</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: History -->
        <div>
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; padding: 16px; margin: 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-clock-rotate-left" style="color: var(--accent-info);"></i> Lịch sử điểm xanh
                    </h4>

                    <?php if (empty($history)): ?>
                        <div class="empty-state">
                            <i class="fas fa-leaf"></i>
                            <h3>Chưa có điểm xanh nào</h3>
                            <p>Hãy nạp tiền hoặc mua hàng để bắt đầu tích điểm!</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Lịch sử nhận</th>
                                    <th class="text-right">Điểm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td style="font-size: 0.82rem; color: var(--text-muted);">
                                            <?= formatDate($h['created_at']) ?>
                                        </td>
                                        <td style="font-size: 0.88rem;"><?= e($h['reason']) ?></td>
                                        <td class="text-right">
                                            <?php if ($h['points'] > 0): ?>
                                                <span style="color: var(--accent-success); font-weight:700;">
                                                    +<?= $h['points'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--accent-danger); font-weight:700;">
                                                    <?= $h['points'] ?>
                                                </span>
                                            <?php endif; ?>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('btn-exchange-gp');
    if (btn) {
        var csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
        btn.addEventListener('click', function() {
            if (!confirm('Bạn muốn đổi 100 điểm xanh lấy 10,000đ?')) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            var resultEl = document.getElementById('exchange-result');

            fetch('<?= url('/green-points/exchange') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + csrfToken
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.csrf_token) csrfToken = data.csrf_token;
                resultEl.style.display = 'block';
                if (data.status === 'success') {
                    resultEl.innerHTML = '<span style="color:var(--accent-success);"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
                    document.getElementById('gp-total').textContent = Number(data.new_points).toLocaleString();
                    // Update balance in topbar
                    var balEl = document.querySelector('.user-balance-amount');
                    if (balEl) balEl.textContent = data.new_balance;
                    // Disable button if not enough points
                    if (data.new_points < 100) {
                        btn.innerHTML = '<i class="fas fa-lock"></i> Cần thêm ' + (100 - data.new_points) + ' điểm nữa';
                        btn.className = 'btn btn-secondary';
                        btn.style.opacity = '0.5';
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-exchange-alt"></i> Đổi 100 Điểm → 10,000đ';
                    }
                } else {
                    resultEl.innerHTML = '<span style="color:var(--accent-danger);"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</span>';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-exchange-alt"></i> Đổi 100 Điểm → 10,000đ';
                }
            }).catch(function() {
                resultEl.style.display = 'block';
                resultEl.innerHTML = '<span style="color:var(--accent-danger);">Lỗi kết nối!</span>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-exchange-alt"></i> Đổi 100 Điểm → 10,000đ';
            });
        });
    }
});
</script>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 2fr"] {
            display: block !important;
        }
        div[style*="grid-template-columns: 1fr 2fr"] > div {
            margin-bottom: 16px;
        }
    }
</style>
