<div class="container py-4">
    <div class="section-title">
        <i class="fas fa-book-open"></i>
        <h2>Hướng dẫn sử dụng ShopAcc VN</h2>
    </div>

    <div class="row">
        <div class="col-md-3">
            <!-- Table of Contents -->
            <div class="card position-sticky" style="top: 100px;">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="guide-menu">
                        <li class="list-group-item active"><a href="#guide-register"
                                class="text-white text-decoration-none d-block"><i class="fas fa-user-plus mr-2"></i>
                                Đăng ký & Đăng nhập</a></li>
                        <li class="list-group-item"><a href="#guide-deposit"
                                class="text-white text-decoration-none d-block"><i class="fas fa-wallet mr-2"></i> Cách
                                Nạp Tiền</a></li>
                        <li class="list-group-item"><a href="#guide-buy"
                                class="text-white text-decoration-none d-block"><i
                                    class="fas fa-shopping-cart mr-2"></i> Mua Tài Khoản</a></li>
                        <li class="list-group-item"><a href="#guide-service"
                                class="text-white text-decoration-none d-block"><i class="fas fa-share-nodes mr-2"></i>
                                Thuê Dịch Vụ</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Guide Content -->

            <div id="guide-register" class="card mb-4 mt-0">
                <div class="card-body">
                    <h3 class="card-title text-primary"><i class="fas fa-user-plus"></i> 1. Đăng ký và Đăng nhập</h3>
                    <div class="guide-content mt-3">
                        <p>Để sử dụng các dịch vụ tại ShopAcc VN, bạn cần có một tài khoản.</p>
                        <ul>
                            <li><strong>Đăng ký:</strong> Click vào nút <code>Đăng ký</code> ở góc phải màn hình. Điền
                                đầy đủ thông tin Tên đăng nhập, Email (để khôi phục MK nếu quên) và Mật khẩu.</li>
                            <li><strong>Đăng nhập:</strong> Nếu đã có tài khoản, chọn <code>Đăng nhập</code> và nhập
                                thông tin của bạn.</li>
                        </ul>
                        <div class="alert alert-info">
                            <i class="fas fa-shield-alt"></i> Tuyệt đối không chia sẻ mật khẩu của bạn cho bất kỳ ai, kể
                            cả Admin!
                        </div>
                    </div>
                </div>
            </div>

            <div id="guide-deposit" class="card mb-4 pb-2 pt-2 mt-4"
                style="border-left: 4px solid var(--accent-success)">
                <div class="card-body">
                    <h3 class="card-title text-success"><i class="fas fa-wallet"></i> 2. Cách Nạp Tiền (Tự Động)</h3>
                    <div class="guide-content mt-3">
                        <p>Hệ thống nạp tiền của ShopAcc vận hành <strong>tự động 100%</strong> 24/7. Để nạp tiền, bạn
                            làm theo các bước sau:</p>
                        <ol>
                            <li>Truy cập mục <strong><a href="<?= url('/banking') ?>" class="text-success">Nạp
                                        tiền</a></strong> trên thanh công cụ.</li>
                            <li>Tại form Tạo Yêu Cầu, chọn phương thức nạp (VD: MBBank) và nhập số tiền muốn nạp (Tối
                                thiểu 10k).</li>
                            <li>Nhấn <strong>Tạo Hoá Đơn Nạp</strong>. Hệ thống sẽ hiển thị mã QR và thông tin chuyển
                                khoản.</li>
                            <li>Mở App Ngân hàng của bạn, <strong>Quét mã QR</strong> hoặc copy chính xác Số TKK và Nội
                                Dung CK.</li>
                            <li>Sau khi chuyển khoản thành công, đợi 1-3 phút hệ thống sẽ tự động cộng tiền cho bạn.
                            </li>
                        </ol>
                        <div class="alert alert-warning">
                            <strong>Quét mã QR là cách an toàn nhất!</strong> Nếu nhập thủ công, BẮT BUỘC phải điền đúng
                            <strong>Cú Pháp Chuyển Khoản</strong> thì tiền mới được cộng tự động.
                        </div>
                    </div>
                </div>
            </div>

            <div id="guide-buy" class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title text-warning"><i class="fas fa-shopping-cart"></i> 3. Cách Mua Tài Khoản Game
                    </h3>
                    <div class="guide-content mt-3">
                        <p>Sau khi có số dư trong tài khoản, bạn có thể mua các tài khoản game:</p>
                        <ol>
                            <li>Vào mục <strong>Tài khoản Game</strong>, lọc tài khoản theo game mong muốn (Liên Quân,
                                Free Fire...).</li>
                            <li>Click <strong>Xem Chi Tiết</strong> để xem hình ảnh và thông tin nick.</li>
                            <li>Nhấn nút <strong>Mua Ngay</strong>. Tiền sẽ tự động trừ đi theo giá của tài khoản.</li>
                            <li>Sau khi mua thành công, bạn vào mục <strong><a href="<?= url('/my-orders') ?>"
                                        class="text-primary">Đơn hàng của tôi</a></strong> để lấy Tài khoản và Mật khẩu.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div id="guide-service" class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title text-info"><i class="fas fa-share-nodes"></i> 4. Cách Thuê Dịch Vụ Cày
                        Thuê/Follow</h3>
                    <div class="guide-content mt-3">
                        <p>Bên cạnh bán nick, chúng tôi cung cấp dịch vụ Cày thuê, Tăng Follow, Tăng Like:</p>
                        <ol>
                            <li>Vào mục <strong>Dịch vụ MXH</strong>. Chọn dịch vụ bạn cần.</li>
                            <li>Tại trang chi tiết dịch vụ, nhập <strong>Số lượng</strong> bạn muốn mua. Ghi chú ID nick
                                hoặc Link bài viết vào ô Ghi chú.</li>
                            <li>Nhấn <strong>Mua Dịch Vụ</strong>.</li>
                            <li>Đơn hàng sẽ chuyển sang trạng thái <code>Đang Xử Lý</code>. Admin sẽ thực hiện yêu cầu
                                của bạn nhanh nhất có thể.</li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Simple Scrollspy for Guide Menu
    document.querySelectorAll('#guide-menu a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#guide-menu li').forEach(li => li.classList.remove('active'));
            this.parentElement.classList.add('active');
            const sectionId = this.getAttribute('href');
            document.querySelector(sectionId).scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

<style>
    #guide-menu li.active {
        background-color: var(--accent-primary);
    }

    #guide-menu li:hover:not(.active) {
        background-color: var(--bg-input);
    }

    .guide-content p,
    .guide-content li {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #e0e0e0;
    }

    .guide-content ol li {
        margin-bottom: 10px;
    }
</style>