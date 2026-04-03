<div class="shop-landing-page">
    <div class="section-title" style="margin-bottom: 24px;">
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
                    <span class="shop-folder-count"><i class="fas fa-box"></i> Khám phá ngay</span>
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
                    <span class="shop-folder-count"><i class="fas fa-cogs"></i> Khám phá ngay</span>
                </div>
            </div>
            <div class="shop-folder-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
    </div>

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
