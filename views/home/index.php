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

    <!-- Cửa Hàng (Shop Folder) -->
    <section class="section">
        <div class="section-title">
            <i class="fas fa-store"></i>
            <h2>Cửa Hàng</h2>
        </div>

        <div class="shop-folder-grid">
            <!-- Game Accounts Folder -->
            <a href="<?= url('/shop/games') ?>" class="shop-folder-card shop-folder-games">
                <div class="shop-folder-glow"></div>
                <div class="shop-folder-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div class="shop-folder-info">
                    <h3>Tài Khoản Game</h3>
                    <p>Mua acc game chất lượng cao với giá ưu đãi</p>
                    <div class="shop-folder-meta">
                        <span class="shop-folder-count"><i class="fas fa-box"></i> <?= count($products) ?>+ sản phẩm</span>
                        <span class="shop-folder-cats"><i class="fas fa-tags"></i> <?= count($gameCategories) ?> danh mục</span>
                    </div>
                </div>
                <div class="shop-folder-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>

            <!-- Social Media Folder -->
            <a href="<?= url('/shop/services') ?>" class="shop-folder-card shop-folder-social">
                <div class="shop-folder-glow"></div>
                <div class="shop-folder-icon">
                    <i class="fas fa-share-nodes"></i>
                </div>
                <div class="shop-folder-info">
                    <h3>Dịch Vụ Mạng Xã Hội</h3>
                    <p>Tăng tương tác Facebook, TikTok, Instagram, YouTube</p>
                    <div class="shop-folder-meta">
                        <span class="shop-folder-count"><i class="fas fa-cogs"></i> <?= count($services) ?>+ dịch vụ</span>
                        <span class="shop-folder-cats"><i class="fas fa-tags"></i> <?= count($socialCategories) ?> danh mục</span>
                    </div>
                </div>
                <div class="shop-folder-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>
    </section>

    <style>
    .shop-folder-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px}
    .shop-folder-card{display:flex;align-items:center;gap:18px;padding:24px 28px;background:var(--gradient-card);border:1px solid var(--border-color);border-radius:16px;text-decoration:none;color:var(--text-primary);position:relative;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1)}
    .shop-folder-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--accent-primary)}
    .shop-folder-card:hover .shop-folder-arrow{transform:translateX(4px);color:var(--accent-primary)}
    .shop-folder-card:hover .shop-folder-glow{opacity:1}
    .shop-folder-glow{position:absolute;top:-50%;right:-50%;width:100%;height:100%;border-radius:50%;opacity:0;transition:opacity .4s ease;pointer-events:none}
    .shop-folder-games .shop-folder-glow{background:radial-gradient(circle,rgba(99,102,241,.08) 0%,transparent 70%)}
    .shop-folder-social .shop-folder-glow{background:radial-gradient(circle,rgba(236,72,153,.08) 0%,transparent 70%)}
    .shop-folder-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;flex-shrink:0}
    .shop-folder-games .shop-folder-icon{background:linear-gradient(135deg,#6366f1,#a855f7)}
    .shop-folder-social .shop-folder-icon{background:linear-gradient(135deg,#ec4899,#f472b6)}
    .shop-folder-info{flex:1;min-width:0}
    .shop-folder-info h3{font-size:1.08rem;font-weight:700;margin-bottom:4px}
    .shop-folder-info p{font-size:.82rem;color:var(--text-secondary);margin-bottom:8px;line-height:1.4}
    .shop-folder-meta{display:flex;gap:14px;flex-wrap:wrap}
    .shop-folder-meta span{font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:4px}
    .shop-folder-meta i{font-size:.7rem;color:var(--accent-primary)}
    .shop-folder-arrow{color:var(--text-muted);font-size:1.1rem;flex-shrink:0;transition:all .3s ease}
    @media(max-width:600px){.shop-folder-grid{grid-template-columns:1fr}.shop-folder-card{padding:18px 20px}}
    </style>
</div>