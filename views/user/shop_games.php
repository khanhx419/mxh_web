<div class="container">
    <div class="page-header">
        <ul class="breadcrumb">
            <li><a href="<?= url('/') ?>">Trang chủ</a></li>
            <span class="separator">/</span>
            <li>Tài khoản Game</li>
        </ul>
        <h1><i class="fas fa-gamepad"></i> Mua Tài Khoản Game</h1>
        <p>Chọn tài khoản game ưng ý với giá cả hợp lý nhất</p>
    </div>

    <!-- Category Filter -->
    <div class="category-tags">
        <a href="<?= url('/shop/games') ?>" class="category-tag <?= !$currentCategory ? 'active' : '' ?>">
            <i class="fas fa-th"></i> Tất cả
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= url('/shop/games?category=' . $cat['id']) ?>"
                class="category-tag <?= $currentCategory == $cat['id'] ? 'active' : '' ?>">
                <i class="fas <?= e($cat['icon']) ?>"></i>
                <?= e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>Không có sản phẩm nào</h3>
            <p>Danh mục này hiện chưa có sản phẩm. Hãy quay lại sau!</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <a href="<?= url('/product/' . $product['id']) ?>" class="card">
                    <?php if ($product['image']): ?>
                        <img src="<?= asset('uploads/' . $product['image']) ?>" alt="<?= e($product['title']) ?>" class="card-img">
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
</div>