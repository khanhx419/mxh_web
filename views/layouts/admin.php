<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trang quản trị - ShopAcc VN">
    <title>
        <?= e($pageTitle ?? 'Admin') ?> |
        <?= e(APP_NAME) ?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="<?= url('/admin') ?>" class="navbar-brand">
                <i class="fas fa-bolt"></i>
                <?= e(APP_NAME) ?> <span style="font-size: 0.7em; opacity: 0.7;">ADMIN</span>
            </a>

            <div class="navbar-actions">
                <a href="<?= url('/') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-globe"></i> Xem Website
                </a>
                <div class="user-info">
                    <i class="fas fa-user-shield"></i>
                    <?= e($_SESSION['username']) ?>
                </div>
                <a href="<?= url('/logout') ?>" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-label">TỔNG QUAN</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/admin') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && substr_count($_SERVER['REQUEST_URI'], '/') <= 2 ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">QUẢN LÝ NỘI DUNG</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/admin/categories') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tags"></i> Danh mục
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/products') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'active' : '' ?>">
                        <i class="fas fa-gamepad"></i> Tài khoản Game
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/services') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/services') !== false ? 'active' : '' ?>">
                        <i class="fas fa-share-nodes"></i> Dịch vụ MXH
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">QUẢN LÝ BÁN HÀNG</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/admin/orders') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i> Đơn hàng
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/users') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Người dùng
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Content -->
        <div class="admin-content">
            <!-- Flash Messages -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <i
                        class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>

</html>