<div class="container py-4">
    <div class="section-title text-center mb-5">
        <i class="fas fa-headset text-primary" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
        <h2>Liên Hệ Hỗ Trợ</h2>
        <p class="text-secondary mt-2">Đội ngũ ShopAcc VN luôn sẵn sàng hỗ trợ bạn 24/7</p>
    </div>

    <div class="row">
        <!-- Contact Info -->
        <div class="col-md-5 mb-4">
            <div class="card h-100" style="background: linear-gradient(145deg, var(--bg-card), rgba(30, 30, 45, 0.9));">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-primary">Thông Tin Liên Hệ</h3>

                    <div class="contact-info-item mb-4 d-flex align-items-center">
                        <div class="icon-box"
                            style="background: rgba(0, 212, 170, 0.1); color: var(--accent-success); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                            <i class="fab fa-facebook-messenger"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Fanpage Facebook</h5>
                            <a href="#" class="text-secondary text-decoration-none">m.me/shopaccvn</a>
                        </div>
                    </div>

                    <div class="contact-info-item mb-4 d-flex align-items-center">
                        <div class="icon-box"
                            style="background: rgba(108, 99, 255, 0.1); color: var(--accent-primary); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Email Hỗ Trợ</h5>
                            <a href="mailto:hotro@shopacc.vn"
                                class="text-secondary text-decoration-none">hotro@shopacc.vn</a>
                        </div>
                    </div>

                    <div class="contact-info-item mb-4 d-flex align-items-center">
                        <div class="icon-box"
                            style="background: rgba(255, 107, 107, 0.1); color: var(--accent-danger); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Hotline CSKH</h5>
                            <a href="tel:0123456789" class="text-secondary text-decoration-none">012.345.6789</a>
                            <small class="d-block text-muted">(08:00 - 22:00 hàng ngày)</small>
                        </div>
                    </div>

                    <hr class="border-secondary my-4">

                    <h5 class="mb-3">Về chúng tôi</h5>
                    <p class="text-secondary">ShopAcc VN - Hệ thống uy tín chuyên cung cấp tài khoản game Liên Quân,
                        Free Fire chất lượng và các dịch vụ mạng xã hội với giá tốt nhất thị trường.</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h3 class="mb-4">Gửi Lời Nhắn</h3>

                    <form action="<?= url('/contact/send') ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Tên của bạn <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" required
                                    value="<?= isset($_SESSION['user_id']) ? e($_SESSION['username']) : '' ?>"
                                    placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email">Email liên hệ <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" required
                                    placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Tiêu đề (Tùy chọn)</label>
                            <input type="text" id="subject" name="subject" class="form-control"
                                placeholder="Lỗi nạp thẻ, Cần hỗ trợ mua nick...">
                        </div>

                        <div class="form-group">
                            <label for="message">Nội dung chi tiết <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="5" required
                                placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block pt-3 pb-3 mt-4"
                            style="font-size: 1.1rem;">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi Tin Nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>