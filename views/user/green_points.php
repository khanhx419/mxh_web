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
                <div class="points-value"><?= number_format($totalPoints) ?></div>
                <div class="points-label">Điểm xanh hiện tại</div>
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
                                    <th>Lý do</th>
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
                                            <span style="color: var(--accent-success); font-weight:700;">
                                                +<?= $h['points'] ?>
                                            </span>
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
