<!DOCTYPE html>
<html lang="vi" data-theme="dark">

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
    <script>
        // Apply saved theme immediately to prevent flash
        (function() {
            const t = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>

<body>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="sidebar">
        <a href="<?= url('/') ?>" class="sidebar-brand">
            <i class="fas fa-bolt"></i>
            <?= e(APP_NAME) ?>
        </a>

        <nav class="sidebar-nav">
            <div class="sidebar-label">KHÁM PHÁ</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/') ?>"
                        class="<?= ($_SERVER['REQUEST_URI'] == '/' || strpos($_SERVER['REQUEST_URI'], '/public') !== false && strlen($_SERVER['REQUEST_URI']) <= strlen(parse_url(APP_URL, PHP_URL_PATH) ?: '') + 1) ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li>
                    <a href="<?= url('/shop/games') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/shop/games') !== false ? 'active' : '' ?>">
                        <i class="fas fa-gamepad"></i> Tài khoản Game
                    </a>
                </li>
                <li>
                    <a href="<?= url('/shop/services') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/shop/services') !== false ? 'active' : '' ?>">
                        <i class="fas fa-share-nodes"></i> Dịch vụ MXH
                    </a>
                </li>
                <li>
                    <a href="<?= url('/search') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/search') !== false ? 'active' : '' ?>">
                        <i class="fas fa-magnifying-glass"></i> Tìm kiếm
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">SỰ KIỆN & GIẢI TRÍ</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/events') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/events') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-star"></i> Sự kiện
                    </a>
                </li>
                <li>
                    <a href="<?= url('/lucky-wheel') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/lucky-wheel') !== false ? 'active' : '' ?>">
                        <i class="fas fa-dharmachakra"></i> Vòng quay
                    </a>
                </li>
                <li>
                    <a href="<?= url('/mystery-bag') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/mystery-bag') !== false ? 'active' : '' ?>">
                        <i class="fas fa-box-open"></i> Túi mù
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">XẾP HẠNG & ĐIỂM</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/leaderboard') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/leaderboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-trophy"></i> Bảng xếp hạng
                    </a>
                </li>
                <li>
                    <a href="<?= url('/green-points') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/green-points') !== false ? 'active' : '' ?>">
                        <i class="fas fa-leaf"></i> Điểm xanh
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">HỖ TRỢ</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('/guide') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/guide') !== false ? 'active' : '' ?>">
                        <i class="fas fa-book"></i> Hướng dẫn
                    </a>
                </li>
                <li>
                    <a href="<?= url('/contact') ?>"
                        class="<?= strpos($_SERVER['REQUEST_URI'], '/contact') !== false ? 'active' : '' ?>">
                        <i class="fas fa-headset"></i> Liên hệ
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <?php if (isLoggedIn()): ?>
                <a href="<?= url('/banking') ?>" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus-circle"></i> Nạp tiền
                </a>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="btn btn-primary btn-block btn-sm">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <form action="<?= url('/search') ?>" method="GET" class="topbar-search">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Tìm kiếm sản phẩm, dịch vụ..."
                    value="<?= e($_GET['q'] ?? '') ?>">
            </form>
        </div>

        <div class="topbar-actions">
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggle" title="Chuyển đổi giao diện">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon"></i>
            </button>

            <?php if (isLoggedIn()): ?>
                <div class="user-balance-pill">
                    <i class="fas fa-wallet"></i>
                    <span class="user-balance-amount"><?= formatMoney($_SESSION['user_balance'] ?? 0) ?></span>
                </div>
                <a href="<?= url('/my-orders') ?>" class="topbar-user" title="Đơn hàng">
                    <i class="fas fa-receipt"></i>
                    <span class="hide-mobile">Đơn hàng</span>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="<?= url('/admin') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog"></i>
                    </a>
                <?php endif; ?>
                <a href="<?= url('/profile') ?>" class="topbar-user" title="Tài khoản">
                    <i class="fas fa-user"></i>
                    <span class="hide-mobile"><?= e($_SESSION['username']) ?></span>
                </a>
                <a href="<?= url('/logout') ?>" class="btn btn-sm btn-danger" title="Đăng xuất">
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
    </header>

    <!-- Main Wrapper -->
    <div class="app-wrapper">
        <!-- Flash Messages -->
        <?php $flash = getFlash(); ?>
        <?php if ($flash): ?>
            <div class="app-content" style="padding-bottom: 0;">
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                    <?= e($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="app-content">
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
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>

</html>