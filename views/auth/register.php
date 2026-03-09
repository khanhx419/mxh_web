<div class="auth-container">
    <div class="auth-card fade-in">
        <h2><i class="fas fa-user-plus"></i> Đăng ký</h2>
        <p class="subtitle">Tạo tài khoản mới để bắt đầu!</p>

        <form method="POST" action="<?= url('/register') ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="form-control"
                    placeholder="Nhập tên đăng nhập (ít nhất 3 ký tự)..." required minlength="3" maxlength="50">
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email..." required>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control"
                    placeholder="Nhập mật khẩu (ít nhất 6 ký tự)..." required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Xác nhận mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                    placeholder="Nhập lại mật khẩu..." required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-user-plus"></i> Đăng ký
            </button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="<?= url('/login') ?>">Đăng nhập</a>
        </div>
    </div>
</div>