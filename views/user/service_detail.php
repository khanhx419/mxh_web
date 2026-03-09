<div class="container">
    <div class="page-header">
        <ul class="breadcrumb">
            <li><a href="<?= url('/') ?>">Trang chủ</a></li>
            <span class="separator">/</span>
            <li><a href="<?= url('/shop/services') ?>">Dịch vụ MXH</a></li>
            <span class="separator">/</span>
            <li>
                <?= e($service['name']) ?>
            </li>
        </ul>
    </div>

    <div class="product-detail">
        <!-- Service Icon -->
        <div class="product-image-box">
            <div class="card-img-placeholder" style="height: 400px; font-size: 5rem;">
                <i
                    class="fab <?= e($service['category_name'] === 'Facebook' ? 'fa-facebook' : ($service['category_name'] === 'TikTok' ? 'fa-tiktok' : ($service['category_name'] === 'Instagram' ? 'fa-instagram' : ($service['category_name'] === 'YouTube' ? 'fa-youtube' : 'fa-share-nodes')))) ?>"></i>
            </div>
        </div>

        <!-- Info & Order Form -->
        <div class="product-info-box">
            <span class="badge badge-primary mb-2">
                <?= e($service['category_name']) ?>
            </span>
            <h1>
                <?= e($service['name']) ?>
            </h1>
            <div class="price">
                <?= formatMoney($service['price_per_1000']) ?> / 1000 lượt
            </div>

            <div class="description">
                <h3 style="color: var(--text-primary); margin-bottom: 8px;">Mô tả</h3>
                <?= nl2br(e($service['description'])) ?>
            </div>

            <div
                style="padding: 12px; background: rgba(108,99,255,0.05); border: 1px solid rgba(108,99,255,0.15); border-radius: 10px; margin-bottom: 16px;">
                <p style="color: var(--accent-info); font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i>
                    Số lượng: <strong>
                        <?= number_format($service['min_quantity']) ?>
                    </strong> - <strong>
                        <?= number_format($service['max_quantity']) ?>
                    </strong>
                </p>
            </div>

            <?php if (isLoggedIn()): ?>
                <div style="margin-bottom: 12px; color: var(--text-secondary); font-size: 0.9rem;">
                    Số dư hiện tại: <strong class="text-success">
                        <?= formatMoney($_SESSION['user_balance'] ?? 0) ?>
                    </strong>
                </div>

                <form method="POST" action="<?= url('/order/service/' . $service['id']) ?>">
                    <?= csrfField() ?>
                    <input type="hidden" id="pricePerUnit" value="<?= $service['price_per_1000'] ?>">

                    <div class="form-group">
                        <label>Link mục tiêu *</label>
                        <input type="url" name="target_link" class="form-control" required
                            placeholder="https://www.facebook.com/photo/...">
                    </div>

                    <div class="form-group">
                        <label>Số lượng *</label>
                        <input type="number" name="quantity" id="quantity" class="form-control"
                            min="<?= $service['min_quantity'] ?>" max="<?= $service['max_quantity'] ?>"
                            value="<?= $service['min_quantity'] ?>" required>
                    </div>

                    <div
                        style="padding: 16px; background: rgba(0,212,170,0.05); border: 1px solid rgba(0,212,170,0.15); border-radius: 10px; margin-bottom: 20px; text-align: center;">
                        <span style="color: var(--text-secondary);">Thành tiền:</span>
                        <span id="totalPrice"
                            style="font-size: 1.5rem; font-weight: 700; color: var(--accent-success); margin-left: 8px;">
                            <?= formatMoney(($service['min_quantity'] / 1000) * $service['price_per_1000']) ?>
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-paper-plane"></i> Đặt dịch vụ
                    </button>
                </form>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để đặt dịch vụ
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>