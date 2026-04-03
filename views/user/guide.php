<div class="guide-page">
    <div class="guide-header">
        <i class="fas fa-book-open"></i>
        <h2>Hướng dẫn sử dụng ShopAcc VN</h2>
    </div>

    <div class="guide-layout">
        <!-- Table of Contents (Sidebar) -->
        <div class="guide-sidebar">
            <div class="guide-toc">
                <ul id="guide-menu">
                    <li><a href="#guide-register"><i class="fas fa-user-plus"></i> Đăng ký & Đăng nhập</a></li>
                    <li><a href="#guide-deposit"><i class="fas fa-wallet"></i> Cách Nạp Tiền</a></li>
                    <li><a href="#guide-buy"><i class="fas fa-shopping-cart"></i> Mua Tài Khoản</a></li>
                    <li><a href="#guide-service"><i class="fas fa-share-nodes"></i> Thuê Dịch Vụ</a></li>
                </ul>
            </div>
        </div>

        <!-- Guide Content -->
        <div class="guide-content-area">

            <div id="guide-register" class="guide-card">
                <div class="guide-card-header" style="--accent:#6c63ff">
                    <i class="fas fa-user-plus"></i>
                    <h3>1. Đăng ký và Đăng nhập</h3>
                </div>
                <div class="guide-card-body">
                    <p>Để sử dụng các dịch vụ tại ShopAcc VN, bạn cần có một tài khoản.</p>
                    <ul>
                        <li><strong>Đăng ký:</strong> Click vào nút <code>Đăng ký</code> ở góc phải màn hình. Điền đầy đủ thông tin Tên đăng nhập, Email và Mật khẩu.</li>
                        <li><strong>Đăng nhập:</strong> Nếu đã có tài khoản, chọn <code>Đăng nhập</code> và nhập thông tin của bạn.</li>
                    </ul>
                    <div class="guide-alert guide-alert-info">
                        <i class="fas fa-shield-alt"></i> Tuyệt đối không chia sẻ mật khẩu của bạn cho bất kỳ ai, kể cả Admin!
                    </div>
                </div>
            </div>

            <div id="guide-deposit" class="guide-card">
                <div class="guide-card-header" style="--accent:#00d4aa">
                    <i class="fas fa-wallet"></i>
                    <h3>2. Cách Nạp Tiền (Tự Động)</h3>
                </div>
                <div class="guide-card-body">
                    <p>Hệ thống nạp tiền vận hành <strong>tự động 100%</strong> 24/7. Để nạp tiền, bạn làm theo các bước sau:</p>
                    <ol>
                        <li>Truy cập mục <strong><a href="<?= url('/banking') ?>">Nạp tiền</a></strong> trên thanh công cụ.</li>
                        <li>Chọn phương thức nạp (VD: MBBank) và nhập số tiền muốn nạp (Tối thiểu 10k).</li>
                        <li>Nhấn <strong>Tạo Hoá Đơn Nạp</strong>. Hệ thống sẽ hiển thị mã QR và thông tin chuyển khoản.</li>
                        <li>Mở App Ngân hàng, <strong>Quét mã QR</strong> hoặc copy chính xác Số TK và Nội Dung CK.</li>
                        <li>Sau khi chuyển khoản thành công, đợi 1-3 phút hệ thống sẽ tự động cộng tiền.</li>
                    </ol>
                    <div class="guide-alert guide-alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Quét mã QR là cách an toàn nhất!</strong> Nếu nhập thủ công, BẮT BUỘC phải điền đúng <strong>Cú Pháp Chuyển Khoản</strong>.
                    </div>
                </div>
            </div>

            <div id="guide-buy" class="guide-card">
                <div class="guide-card-header" style="--accent:#ffa726">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>3. Cách Mua Tài Khoản Game</h3>
                </div>
                <div class="guide-card-body">
                    <p>Sau khi có số dư trong tài khoản, bạn có thể mua các tài khoản game:</p>
                    <ol>
                        <li>Vào mục <strong>Tài khoản Game</strong>, lọc tài khoản theo game mong muốn.</li>
                        <li>Click <strong>Xem Chi Tiết</strong> để xem hình ảnh và thông tin nick.</li>
                        <li>Nhấn nút <strong>Mua Ngay</strong>. Tiền sẽ tự động trừ theo giá của tài khoản.</li>
                        <li>Sau khi mua thành công, vào mục <strong><a href="<?= url('/my-orders') ?>">Đơn hàng của tôi</a></strong> để lấy Tài khoản và Mật khẩu.</li>
                    </ol>
                </div>
            </div>

            <div id="guide-service" class="guide-card">
                <div class="guide-card-header" style="--accent:#29b6f6">
                    <i class="fas fa-share-nodes"></i>
                    <h3>4. Cách Thuê Dịch Vụ Cày Thuê/Follow</h3>
                </div>
                <div class="guide-card-body">
                    <p>Bên cạnh bán nick, chúng tôi cung cấp dịch vụ Cày thuê, Tăng Follow, Tăng Like:</p>
                    <ol>
                        <li>Vào mục <strong>Dịch vụ MXH</strong>. Chọn dịch vụ bạn cần.</li>
                        <li>Nhập <strong>Số lượng</strong> bạn muốn mua. Ghi chú ID nick hoặc Link bài viết.</li>
                        <li>Nhấn <strong>Mua Dịch Vụ</strong>.</li>
                        <li>Đơn hàng sẽ chuyển sang trạng thái <code>Đang Xử Lý</code>. Admin sẽ thực hiện yêu cầu nhanh nhất có thể.</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
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
.guide-page {
    max-width: 960px;
    margin: 0 auto;
    padding: 16px;
}
.guide-header {
    text-align: center;
    margin-bottom: 24px;
}
.guide-header i {
    font-size: 1.6rem;
    color: var(--accent-primary);
    margin-bottom: 6px;
    display: block;
}
.guide-header h2 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}
.guide-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.guide-sidebar {
    flex: 0 0 200px;
    position: sticky;
    top: 90px;
}
.guide-toc {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
}
.guide-toc ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.guide-toc li {
    border-bottom: 1px solid var(--border-color);
}
.guide-toc li:last-child {
    border-bottom: none;
}
.guide-toc li a {
    display: block;
    padding: 10px 14px;
    font-size: .82rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all .2s;
}
.guide-toc li a i {
    margin-right: 6px;
    width: 16px;
    text-align: center;
    font-size: .78rem;
}
.guide-toc li a:hover,
.guide-toc li.active a {
    background: var(--accent-primary);
    color: #fff;
}
.guide-content-area {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.guide-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    overflow: hidden;
}
.guide-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-color);
    background: rgba(99,102,241,.03);
}
.guide-card-header i {
    font-size: .95rem;
    color: var(--accent, var(--accent-primary));
}
.guide-card-header h3 {
    font-size: .92rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}
.guide-card-body {
    padding: 16px 18px;
}
.guide-card-body p {
    font-size: .86rem;
    line-height: 1.55;
    color: var(--text-secondary);
    margin: 0 0 10px 0;
}
.guide-card-body ul,
.guide-card-body ol {
    padding-left: 18px;
    margin: 0 0 10px 0;
}
.guide-card-body li {
    font-size: .84rem;
    line-height: 1.5;
    color: var(--text-secondary);
    margin-bottom: 6px;
}
.guide-card-body code {
    background: rgba(99,102,241,.1);
    padding: 1px 5px;
    border-radius: 4px;
    font-size: .8rem;
}
.guide-card-body a {
    color: var(--accent-primary);
    text-decoration: none;
    font-weight: 600;
}
.guide-card-body a:hover {
    text-decoration: underline;
}
.guide-alert {
    font-size: .82rem;
    line-height: 1.5;
    padding: 10px 14px;
    border-radius: 8px;
    margin-top: 8px;
}
.guide-alert i {
    margin-right: 6px;
}
.guide-alert-info {
    background: rgba(41,182,246,.08);
    border: 1px solid rgba(41,182,246,.15);
    color: var(--accent-info, #29b6f6);
}
.guide-alert-warning {
    background: rgba(255,167,38,.08);
    border: 1px solid rgba(255,167,38,.15);
    color: var(--accent-warning, #ffa726);
}

/* Responsive */
@media (max-width: 700px) {
    .guide-layout {
        flex-direction: column;
    }
    .guide-sidebar {
        flex: none;
        width: 100%;
        position: static;
    }
    .guide-toc ul {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        padding: 6px;
    }
    .guide-toc li {
        border-bottom: none;
    }
    .guide-toc li a {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: .78rem;
        white-space: nowrap;
    }
}
</style>