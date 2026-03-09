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