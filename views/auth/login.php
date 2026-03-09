<div class="auth-container">
    <div class="auth-card fade-in">
        <h2><i class="fas fa-bolt"></i> Đăng nhập</h2>
        <p class="subtitle">Chào mừng bạn quay trở lại!</p>

        <form method="POST" action="<?= url('/login') ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="form-control"
                    placeholder="Nhập tên đăng nhập..." required autofocus>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu..."
                    required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
            </button>
        </form>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="<?= url('/register') ?>">Đăng ký ngay</a>
        </div>
    </div>
</div>