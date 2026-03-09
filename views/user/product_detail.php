<div class="container">
    <div class="page-header">
        <ul class="breadcrumb">
            <li><a href="<?= url('/') ?>">Trang chủ</a></li>
            <span class="separator">/</span>
            <li><a href="<?= url('/shop/games') ?>">Tài khoản Game</a></li>
            <span class="separator">/</span>
            <li>
                <?= e($product['title']) ?>
            </li>
        </ul>
    </div>

    <div class="product-detail">
        <!-- Image -->
        <div class="product-image-box">
            <?php if ($product['image']): ?>
                <img src="<?= asset('uploads/' . $product['image']) ?>" alt="<?= e($product['title']) ?>" class="card-img"
                    style="height: 400px;">
            <?php else: ?>
                <div class="card-img-placeholder" style="height: 400px; font-size: 5rem;">
                    <i class="fas fa-gamepad"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="product-info-box">
            <span class="badge badge-info mb-2">
                <?= e($product['category_name']) ?>
            </span>
            <h1>
                <?= e($product['title']) ?>
            </h1>
            <div class="price">
                <?= formatMoney($product['price']) ?>
            </div>

            <div class="description">
                <h3 style="color: var(--text-primary); margin-bottom: 8px;">Mô tả</h3>
                <?= nl2br(e($product['description'])) ?>
            </div>

            <div
                style="padding: 16px; background: rgba(0,212,170,0.05); border: 1px solid rgba(0,212,170,0.15); border-radius: 10px; margin-bottom: 20px;">
                <p style="color: var(--accent-success); font-size: 0.9rem;">
                    <i class="fas fa-shield-halved"></i> Thông tin tài khoản sẽ được gửi ngay sau khi thanh toán thành
                    công.
                </p>
            </div>

            <?php if (isLoggedIn()): ?>
                <div style="margin-bottom: 12px; color: var(--text-secondary); font-size: 0.9rem;">
                    Số dư hiện tại: <strong class="text-success">
                        <?= formatMoney($_SESSION['user_balance'] ?? 0) ?>
                    </strong>
                </div>
                <form method="POST" action="<?= url('/order/product/' . $product['id']) ?>">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-primary btn-lg btn-block"
                        onclick="return confirm('Xác nhận mua tài khoản này với giá <?= formatMoney($product['price']) ?>?')">
                        <i class="fas fa-shopping-cart"></i> Mua ngay -
                        <?= formatMoney($product['price']) ?>
                    </button>
                </form>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để mua
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>