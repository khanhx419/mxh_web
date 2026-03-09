<div class="container py-4">
    <div class="section-title">
        <i class="fas fa-search"></i>
        <h2>Kết quả tìm kiếm: "
            <?= e($keyword) ?>"
        </h2>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= url('/search') ?>" method="GET" class="d-flex">
                <input type="text" name="q" class="form-control" value="<?= e($keyword) ?>"
                    placeholder="Nhập tên sản phẩm, dịch vụ..." required>
                <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-search"></i> Tìm kiếm</button>
            </form>
        </div>
    </div>

    <?php if (empty($keyword)): ?>
        <div class="alert alert-info">Vui lòng nhập từ khóa để tìm kiếm.</div>
    <?php elseif (empty($products) && empty($services)): ?>
        <div class="empty-state text-center py-5">
            <i class="fas fa-box-open mb-3" style="font-size: 3rem; color: var(--text-muted);"></i>
            <h3>Không tìm thấy kết quả nào</h3>
            <p class="text-secondary">Thử tìm kiếm với một từ khóa khác hoặc duyệt qua danh mục của chúng tôi.</p>
            <a href="<?= url('/shop') ?>" class="btn btn-primary mt-3">Tất cả sản phẩm</a>
        </div>
    <?php else: ?>

        <!-- Products Results -->
        <?php if (!empty($products)): ?>
            <h3 class="mb-4 mt-5"><i class="fas fa-gamepad text-primary"></i> Tài khoản Game (
                <?= count($products) ?>)
            </h3>
            <div class="grid-4">
                <?php foreach ($products as $product): ?>
                    <div class="card product-card">
                        <?php if ($product['image']): ?>
                            <img src="<?= asset('uploads/products/' . $product['image']) ?>" alt="<?= e($product['title']) ?>"
                                class="product-img">
                        <?php else: ?>
                            <div class="product-img d-flex align-items-center justify-content-center bg-dark text-muted">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h4 class="product-title">
                                <?= e($product['title']) ?>
                            </h4>
                            <div class="product-meta">
                                <span class="badge badge-info">
                                    <?= e($product['category_name']) ?>
                                </span>
                                <span class="badge badge-secondary">ID:
                                    <?= e($product['id']) ?>
                                </span>
                            </div>
                            <div class="product-price">
                                <?= formatMoney($product['price']) ?>
                            </div>
                            <a href="<?= url('/product/' . $product['id']) ?>" class="btn btn-primary btn-block">XEM CHI TIẾT</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Services Results -->
        <?php if (!empty($services)): ?>
            <h3 class="mb-4 mt-5"><i class="fas fa-share-nodes text-success"></i> Dịch vụ Mạng Xã Hội (
                <?= count($services) ?>)
            </h3>
            <div class="grid-4">
                <?php foreach ($services as $service): ?>
                    <div class="card product-card">
                        <?php if ($service['image']): ?>
                            <img src="<?= asset('uploads/services/' . $service['image']) ?>" alt="<?= e($service['name']) ?>"
                                class="product-img">
                        <?php else: ?>
                            <div class="product-img d-flex align-items-center justify-content-center bg-dark text-muted">
                                <i class="fas fa-concierge-bell fa-3x"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h4 class="product-title">
                                <?= e($service['name']) ?>
                            </h4>
                            <div class="product-meta">
                                <span class="badge badge-success">
                                    <?= e($service['category_name']) ?>
                                </span>
                            </div>
                            <div class="product-price">
                                <?= formatMoney($service['price']) ?> /
                                <?= e($service['unit']) ?>
                            </div>
                            <a href="<?= url('/service/' . $service['id']) ?>" class="btn btn-success btn-block">XEM CHI TIẾT</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>