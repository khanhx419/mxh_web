<div class="container">
    <div class="page-header">
        <ul class="breadcrumb">
            <li><a href="<?= url('/') ?>">Trang chủ</a></li>
            <span class="separator">/</span>
            <li>Dịch vụ MXH</li>
        </ul>
        <h1><i class="fas fa-share-nodes"></i> Dịch Vụ Mạng Xã Hội</h1>
        <p>Tăng tương tác mạng xã hội nhanh chóng, uy tín</p>
    </div>

    <!-- Category Filter -->
    <div class="category-tags">
        <a href="<?= url('/shop/services') ?>" class="category-tag <?= !$currentCategory ? 'active' : '' ?>">
            <i class="fas fa-th"></i> Tất cả
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= url('/shop/services?category=' . $cat['id']) ?>"
                class="category-tag <?= $currentCategory == $cat['id'] ? 'active' : '' ?>">
                <i class="fab <?= e($cat['icon']) ?>"></i>
                <?= e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Services Grid -->
    <?php if (empty($services)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>Không có dịch vụ nào</h3>
            <p>Danh mục này hiện chưa có dịch vụ. Hãy quay lại sau!</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($services as $service): ?>
                <a href="<?= url('/service/' . $service['id']) ?>" class="card">
                    <div class="card-img-placeholder">
                        <i class="fab <?= e($service['category_name'] === 'Facebook' ? 'fa-facebook' : ($service['category_name'] === 'TikTok' ? 'fa-tiktok' : ($service['category_name'] === 'Instagram' ? 'fa-instagram' : ($service['category_name'] === 'YouTube' ? 'fa-youtube' : 'fa-share-nodes')))) ?>"
                            style="font-size: 3rem;"></i>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            <?= e($service['name']) ?>
                        </div>
                        <div class="card-text">
                            <?= e($service['description']) ?>
                        </div>
                        <div class="card-price">
                            <?= formatMoney($service['price_per_1000']) ?> / 1000
                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="badge badge-primary">
                            <?= e($service['category_name']) ?>
                        </span>
                        <span class="text-muted" style="font-size: 0.8rem;">Min:
                            <?= number_format($service['min_quantity']) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>