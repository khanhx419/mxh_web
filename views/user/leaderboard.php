<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-trophy" style="color: var(--accent-warning);"></i> Bảng Xếp Hạng</h1>
        <p class="text-secondary">Top người dùng nổi bật trên hệ thống</p>
    </div>

    <div class="leaderboard-card">
        <!-- Tabs -->
        <div class="leaderboard-tabs">
            <button class="leaderboard-tab active" data-tab="tab-deposit">
                <i class="fas fa-coins"></i> Top Nạp
            </button>
            <button class="leaderboard-tab" data-tab="tab-spending">
                <i class="fas fa-shopping-cart"></i> Top Chi Tiêu
            </button>
            <button class="leaderboard-tab" data-tab="tab-points">
                <i class="fas fa-leaf"></i> Top Điểm Xanh
            </button>
        </div>

        <!-- Tab: Top Nạp -->
        <div class="leaderboard-panel" id="tab-deposit">
            <?php if (empty($topDeposit)): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>Chưa có dữ liệu</h3>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Người dùng</th>
                            <th class="text-right">Tổng nạp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topDeposit as $i => $user): ?>
                            <tr class="rank-row">
                                <td>
                                    <?php if ($i < 3): ?>
                                        <span class="rank-badge rank-<?= $i + 1 ?>"><?= $i + 1 ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-weight: 600; padding-left: 8px;"><?= $i + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">
                                        <i class="fas fa-user-circle" style="color: var(--text-muted); margin-right: 4px;"></i>
                                        <?= e($user['username']) ?>
                                    </span>
                                </td>
                                <td class="text-right" style="font-weight: 700; color: var(--accent-success);">
                                    <?= formatMoney($user['total_deposit']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Tab: Top Chi Tiêu -->
        <div class="leaderboard-panel" id="tab-spending" style="display: none;">
            <?php if (empty($topSpending)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Chưa có dữ liệu</h3>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Người dùng</th>
                            <th class="text-right">Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSpending as $i => $user): ?>
                            <tr class="rank-row">
                                <td>
                                    <?php if ($i < 3): ?>
                                        <span class="rank-badge rank-<?= $i + 1 ?>"><?= $i + 1 ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-weight: 600; padding-left: 8px;"><?= $i + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">
                                        <i class="fas fa-user-circle" style="color: var(--text-muted); margin-right: 4px;"></i>
                                        <?= e($user['username']) ?>
                                    </span>
                                </td>
                                <td class="text-right" style="font-weight: 700; color: var(--accent-info);">
                                    <?= formatMoney($user['total_spending']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Tab: Top Điểm Xanh -->
        <div class="leaderboard-panel" id="tab-points" style="display: none;">
            <?php if (empty($topPoints)): ?>
                <div class="empty-state">
                    <i class="fas fa-leaf"></i>
                    <h3>Chưa có dữ liệu</h3>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Người dùng</th>
                            <th class="text-right">Điểm xanh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPoints as $i => $user): ?>
                            <tr class="rank-row">
                                <td>
                                    <?php if ($i < 3): ?>
                                        <span class="rank-badge rank-<?= $i + 1 ?>"><?= $i + 1 ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-weight: 600; padding-left: 8px;"><?= $i + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">
                                        <i class="fas fa-user-circle" style="color: var(--text-muted); margin-right: 4px;"></i>
                                        <?= e($user['username']) ?>
                                    </span>
                                </td>
                                <td class="text-right" style="font-weight: 700; color: var(--accent-success);">
                                    🍀 <?= number_format($user['green_points_total']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="text-align:center;margin-top:16px;">
            <a href="<?= url('/chess') ?>" style="font-size:0.85rem;color:var(--accent-primary);">
                <i class="fas fa-chess-knight"></i> Xem Bảng xếp hạng Cờ Vua tại trang Cờ Vua AI
            </a>
        </div>
    </div>
</div>
