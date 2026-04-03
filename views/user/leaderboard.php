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
            <button class="leaderboard-tab" data-tab="tab-chess-easy">
                🟢 Cờ Vua Easy
            </button>
            <button class="leaderboard-tab" data-tab="tab-chess-medium">
                🟡 Cờ Vua Medium
            </button>
            <button class="leaderboard-tab" data-tab="tab-chess-hard">
                🟠 Cờ Vua Hard
            </button>
            <button class="leaderboard-tab" data-tab="tab-chess-hell">
                🔴 Cờ Vua Hell
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

        <!-- Chess Tabs (4 difficulties) -->
        <?php
        $chessLabels = [
            'easy' => ['🟢 Easy', '+1 điểm/thắng'],
            'medium' => ['🟡 Medium', '+3 điểm/thắng'],
            'hard' => ['🟠 Hard', '+5 điểm/thắng'],
            'hell' => ['🔴 Hell', '+10 điểm/thắng']
        ];
        foreach ($chessLabels as $diff => $meta):
            $players = $topChess[$diff] ?? [];
        ?>
        <div class="leaderboard-panel" id="tab-chess-<?= $diff ?>" style="display: none;">
            <div style="text-align: center; margin-bottom: 12px;">
                <span style="font-size: 0.82rem; color: var(--text-muted);"><?= $meta[1] ?></span>
            </div>
            <?php if (empty($players)): ?>
                <div class="empty-state">
                    <i class="fas fa-chess-knight"></i>
                    <h3>Chưa có dữ liệu</h3>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Người dùng</th>
                            <th class="text-right">Số trận thắng</th>
                            <th class="text-right">Tổng điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $i => $player): ?>
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
                                        <i class="fas fa-chess-knight" style="color: var(--accent-primary); margin-right: 4px;"></i>
                                        <?= e($player['username']) ?>
                                    </span>
                                </td>
                                <td class="text-right" style="font-weight: 700; color: var(--accent-info);">
                                    <?= number_format($player['wins']) ?> trận
                                </td>
                                <td class="text-right" style="font-weight: 700; color: var(--accent-warning);">
                                    ⭐ <?= number_format($player['total_points']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
