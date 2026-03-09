<div class="container py-4">
    <div class="section-title text-center mb-5">
        <i class="fas fa-trophy text-warning" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
        <h2>Top Đại Gia Nạp Tiền</h2>
        <p class="text-secondary mt-2">Bảng vinh danh những người dùng có tổng tiền nạp cao nhất hệ thống</p>
    </div>

    <div class="card leaderboard-card mx-auto" style="max-width: 800px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div class="card-body p-0">
            <table class="table mb-0 leaderboard-table">
                <thead style="background: var(--bg-card); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th class="text-center py-4" style="width: 15%">Hạng</th>
                        <th class="py-4">Người dùng</th>
                        <th class="text-right py-4" style="width: 35%">Tổng nạp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topUsers)): ?>
                        <tr><td colspan="3" class="text-center py-5">Chưa có dữ liệu nạp tiền</td></tr>
                    <?php else: ?>
                        <?php foreach ($topUsers as $index => $user): ?>
                            <tr class="rank-row <?= $index < 3 ? 'top-3 bg-dark-gradient' : '' ?>" style="transition: all 0.3s; <?= $index < 3 ? 'border-left: 4px solid '.($index==0?'#ffd700':($index==1?'#c0c0c0':'#cd7f32')).';' : '' ?>">
                                <td class="text-center py-3">
                                    <?php if ($index == 0): ?>
                                        <i class="fas fa-crown text-warning" style="font-size: 1.5rem;"></i>
                                    <?php elseif ($index == 1): ?>
                                        <i class="fas fa-medal text-light" style="font-size: 1.5rem;"></i>
                                    <?php elseif ($index == 2): ?>
                                        <i class="fas fa-award" style="color: #cd7f32; font-size: 1.5rem;"></i>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="font-size: 1.1rem;"><?= $index + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="py-3 font-weight-bold" style="font-size: 1.1rem; <?= $index < 3 ? 'color: var(--accent-primary);' : '' ?>">
                                    <i class="fas fa-user-circle mr-2 text-secondary"></i>
                                    <?= e($user['username']) ?>
                                </td>
                                
                                <td class="text-right py-3 font-weight-bold" style="font-size: 1.2rem; color: var(--accent-success);">
                                    <?= formatMoney($user['total_deposit']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.leaderboard-table tr:hover {
    background: rgba(255,255,255,0.05);
    transform: scale(1.01);
}
.bg-dark-gradient {
    background: linear-gradient(90deg, rgba(20,20,30,1) 0%, rgba(30,30,45,1) 100%);
}
</style>
