<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ShopAcc VN - Mua bán tài khoản game & dịch vụ mạng xã hội uy tín">
    <title>
        <?= e($pageTitle ?? APP_NAME) ?> |
        <?= e(APP_NAME) ?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="<?= url('/') ?>" class="navbar-brand">
                <i class="fas fa-bolt"></i>
                <?= e(APP_NAME) ?>
            </a>

            <ul class="navbar-menu">
                <li><a href="<?= url('/') ?>"
                        class="<?= ($_SERVER['REQUEST_URI'] == '/' || strpos($_SERVER['REQUEST_URI'], '/public') !== false && strlen($_SERVER['REQUEST_URI']) <= strlen(parse_url(APP_URL, PHP_URL_PATH) ?: '') + 1) ? 'active' : '' ?>"><i
                            class="fas fa-home"></i> Trang chủ</a></li>
                <li><a href="<?= url('/shop/games') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/shop/games') !== false ? 'active' : '' ?>"><i
                            class="fas fa-gamepad"></i> Tài khoản Game</a></li>
                <li><a href="<?= url('/shop/services') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/shop/services') !== false ? 'active' : '' ?>"><i
                            class="fas fa-share-nodes"></i> Dịch vụ MXH</a></li>
            </ul>

            <div class="navbar-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="user-balance">
                        <i class="fas fa-wallet"></i>
                        <?= formatMoney($_SESSION['user_balance'] ?? 0) ?>
                    </div>
                    <a href="<?= url('/my-orders') ?>" class="user-info">
                        <i class="fas fa-receipt"></i> Đơn hàng
                    </a>
                    <?php if (isAdmin()): ?>
                        <a href="<?= url('/admin') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('/profile') ?>" class="user-info">
                        <i class="fas fa-user"></i>
                        <?= e($_SESSION['username']) ?>
                    </a>
                    <a href="<?= url('/logout') ?>" class="btn btn-sm btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>
                    <a href="<?= url('/register') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus"></i> Đăng ký
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="container" style="margin-top: 20px;">
            <div class="alert alert-<?= e($flash['type']) ?>">
                <i
                    class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <?= e($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy;
                <?= date('Y') ?>
                <?= e(APP_NAME) ?>. All Rights Reserved. | Được xây dựng với <i class="fas fa-heart text-danger"></i>
            </p>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>

</html>