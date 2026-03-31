<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <h1>Mua Acc Game & Dịch Vụ MXH</h1>
        <p>Nền tảng uy tín hàng đầu cung cấp tài khoản game chất lượng và dịch vụ tăng tương tác mạng xã hội.</p>
        <div class="hero-actions">
            <a href="<?= url('/shop/games') ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-gamepad"></i> Mua Acc Game
            </a>
            <a href="<?= url('/shop/services') ?>" class="btn btn-secondary btn-lg"
                style="border: 1px solid var(--border-color);">
                <i class="fas fa-share-nodes"></i> Dịch Vụ MXH
            </a>
        </div>
    </section>

    <!-- Daily Check-in -->
    <div class="home-checkin-card">
        <div class="home-checkin-left">
            <div class="home-checkin-title"><i class="fas fa-calendar-check"></i> Điểm Danh Hàng Ngày</div>
            <div class="home-checkin-days">
                <?php for ($d = 1; $d <= 7; $d++): ?>
                    <?php
                        $isChecked = $d <= $checkin['current_day'];
                        $isToday = $d == ($checkin['current_day'] + 1) && !$checkin['has_checked_in_today'];
                        $cls = $isChecked ? 'hc-checked' : ($isToday ? 'hc-today' : 'hc-locked');
                    ?>
                    <div class="hc-day <?= $cls ?>">
                        <span class="hc-day-num"><?= $d ?></span>
                        <?php if ($isChecked): ?><i class="fas fa-check"></i><?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <div class="home-checkin-right">
            <?php if (!isLoggedIn()): ?>
                <a href="<?= url('/login') ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
            <?php elseif ($checkin['has_checked_in_today']): ?>
                <button class="btn btn-secondary" disabled><i class="fas fa-check"></i> Đã điểm danh</button>
            <?php else: ?>
                <button class="btn btn-primary" id="btn-home-checkin"><i class="fas fa-hand-pointer"></i> Điểm Danh</button>
            <?php endif; ?>
            <div class="hc-spins"><i class="fas fa-ticket-alt"></i> <strong id="hc-free-spins"><?= $checkin['free_spins'] ?></strong> lượt free</div>
        </div>
    </div>

    <style>
    .home-checkin-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:20px 28px;margin-bottom:28px;background:var(--gradient-card);border:1px solid var(--border-color);border-radius:16px;flex-wrap:wrap}
    .home-checkin-title{font-size:.92rem;font-weight:700;color:var(--text-primary);margin-bottom:12px}
    .home-checkin-title i{color:var(--accent-primary);margin-right:6px}
    .home-checkin-days{display:flex;gap:8px}
    .hc-day{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;border:1px solid var(--border-color);position:relative}
    .hc-day .hc-day-num{display:block}
    .hc-checked{background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(56,189,248,.1));border-color:rgba(16,185,129,.3);color:#10b981}
    .hc-checked .hc-day-num{display:none}
    .hc-checked i{color:#10b981;font-size:.85rem}
    .hc-today{background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(168,85,247,.08));border-color:rgba(99,102,241,.4);color:var(--accent-primary);animation:todayPulse 2s ease-in-out infinite}
    @keyframes todayPulse{0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,.2)}50%{box-shadow:0 0 12px 3px rgba(99,102,241,.15)}}
    .hc-locked{opacity:.3;color:var(--text-muted)}
    .home-checkin-right{display:flex;flex-direction:column;align-items:center;gap:8px}
    .hc-spins{font-size:.82rem;color:var(--text-secondary)}
    .hc-spins strong{color:var(--accent-primary);font-size:1rem}
    @media(max-width:600px){.home-checkin-card{flex-direction:column;text-align:center}.home-checkin-days{justify-content:center}}
    </style>

    <script>
    document.addEventListener('DOMContentLoaded',function(){
        var btn=document.getElementById('btn-home-checkin');
        if(btn){
            var csrfToken='<?= $_SESSION['csrf_token'] ?? '' ?>';
            btn.addEventListener('click',function(){
                btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i>';
                fetch('<?= url('/mystery-bag/checkin') ?>',{
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded'},
                    body:'csrf_token='+csrfToken
                }).then(function(r){return r.json()}).then(function(data){
                    if(data.csrf_token) csrfToken=data.csrf_token;
                    if(data.status==='success'){
                        btn.innerHTML='<i class="fas fa-check"></i> '+data.message;
                        btn.className='btn btn-secondary';
                        var s=document.getElementById('hc-free-spins');if(s)s.textContent=data.total_spins;
                        var days=document.querySelectorAll('.hc-day');
                        if(days[data.day-1]){days[data.day-1].className='hc-day hc-checked';days[data.day-1].innerHTML='<i class="fas fa-check"></i>'}
                    }else{btn.innerHTML='<i class="fas fa-exclamation-triangle"></i> '+data.message;btn.disabled=false}
                }).catch(function(){btn.innerHTML='Lỗi!';btn.disabled=false});
            });
        }
    });
    </script>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-gamepad"></i></div>
            <div class="stat-info">
                <h3>
                    <?= count($products) ?>+
                </h3>
                <p>Tài khoản Game</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pink"><i class="fas fa-share-nodes"></i></div>
            <div class="stat-info">
                <h3>
                    <?= count($services) ?>+
                </h3>
                <p>Dịch vụ MXH</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-shield-halved"></i></div>
            <div class="stat-info">
                <h3>100%</h3>
                <p>Bảo hành uy tín</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
            <div class="stat-info">
                <h3>24/7</h3>
                <p>Hỗ trợ nhanh chóng</p>
            </div>
        </div>
    </div>

    <!-- Game Accounts Section -->
    <section class="section">
        <div class="section-title">
            <i class="fas fa-gamepad"></i>
            <h2>Tài Khoản Game Nổi Bật</h2>
            <a href="<?= url('/shop/games') ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Category Tags -->
        <div class="category-tags">
            <?php foreach ($gameCategories as $cat): ?>
                <a href="<?= url('/shop/games?category=' . $cat['id']) ?>" class="category-tag">
                    <i class="fas <?= e($cat['icon']) ?>"></i>
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>Chưa có sản phẩm nào</h3>
                <p>Hãy quay lại sau nhé!</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <a href="<?= url('/product/' . $product['id']) ?>" class="card">
                        <?php if ($product['image']): ?>
                            <img src="<?= asset('uploads/' . $product['image']) ?>" alt="<?= e($product['title']) ?>"
                                class="card-img">
                        <?php else: ?>
                            <div class="card-img-placeholder">
                                <i class="fas fa-gamepad"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="card-title">
                                <?= e($product['title']) ?>
                            </div>
                            <div class="card-text">
                                <?= e($product['description']) ?>
                            </div>
                            <div class="card-price">
                                <?= formatMoney($product['price']) ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <span class="badge badge-info">
                                <?= e($product['category_name']) ?>
                            </span>
                            <span class="badge badge-success">Còn hàng</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Social Services Section -->
    <section class="section">
        <div class="section-title">
            <i class="fas fa-share-nodes"></i>
            <h2>Dịch Vụ Mạng Xã Hội</h2>
            <a href="<?= url('/shop/services') ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Category Tags -->
        <div class="category-tags">
            <?php foreach ($socialCategories as $cat): ?>
                <a href="<?= url('/shop/services?category=' . $cat['id']) ?>" class="category-tag">
                    <i class="fab <?= e($cat['icon']) ?>"></i>
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($services)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>Chưa có dịch vụ nào</h3>
                <p>Hãy quay lại sau nhé!</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($services as $service): ?>
                    <a href="<?= url('/service/' . $service['id']) ?>" class="card">
                        <div class="card-img-placeholder">
                            <i
                                class="fab <?= e($service['category_name'] === 'Facebook' ? 'fa-facebook' : ($service['category_name'] === 'TikTok' ? 'fa-tiktok' : ($service['category_name'] === 'Instagram' ? 'fa-instagram' : ($service['category_name'] === 'YouTube' ? 'fa-youtube' : 'fa-share-nodes')))) ?>"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-title">
                                <?= e($service['name']) ?>
                            </div>
                            <div class="card-text">
                                <?= e($service['description']) ?>
                            </div>
                            <div class="card-price">
                                <?= formatMoney($service['price_per_1000']) ?>/1000
                            </div>
                        </div>
                        <div class="card-footer">
                            <span class="badge badge-primary">
                                <?= e($service['category_name']) ?>
                            </span>
                            <span class="badge badge-success">Hoạt động</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>