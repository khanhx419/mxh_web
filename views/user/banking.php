<div class="container">
    <div class="section-title">
        <i class="fas fa-wallet"></i>
        <h2>Nạp Tiền Vào Tài Khoản</h2>
    </div>

    <?php if (!empty($depositNotice)): ?>
    <div class="alert alert-warning" style="border-left: 4px solid var(--accent-warning); background: rgba(245, 158, 11, 0.1); border-radius: 8px; margin-bottom: 20px; padding: 14px 18px; line-height: 1.6;">
        <i class="fas fa-exclamation-triangle" style="color: var(--accent-warning); margin-right: 6px;"></i>
        <strong style="color: var(--accent-warning);">LƯU Ý:</strong>
        <?php
            $notice = e($depositNotice);
            $highlight = '<strong style="color: var(--accent-danger); font-size: 1.05em; letter-spacing: 1px; background: rgba(239,68,68,.1); padding: 2px 8px; border-radius: 4px;">' . e($transferContent) . '</strong>';
            echo str_replace(['[nội dung của bạn]', '[nội dung]'], $highlight, $notice);
        ?>
    </div>
    <?php endif; ?>

    <div class="grid-2">
        <!-- Bank Info Card -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Thông tin chuyển khoản</h3>

                <div class="text-center mb-4">
                    <?php if (!empty($depositQrImage)): ?>
                        <img src="<?= asset($depositQrImage) ?>"
                            alt="QR Code"
                            style="max-width: 250px; border-radius: 10px; border: 1px solid var(--border-color); padding: 5px; background: white;">
                    <?php else: ?>
                        <img src="https://img.vietqr.io/image/<?= e($bankConfig['bank_name']) ?>-<?= e($bankConfig['bank_acc_number']) ?>-compact2.jpg?amount=0&addInfo=<?= e($transferContent) ?>"
                            alt="QR Code"
                            style="max-width: 250px; border-radius: 10px; border: 1px solid var(--border-color); padding: 5px; background: white;">
                    <?php endif; ?>
                    <p class="text-secondary mt-2"><small>Quét mã QR để tự động điền thông tin</small></p>
                    <?php if (!empty($depositTransferDetails)): ?>
                        <div style="margin-top: 10px; padding: 10px 16px; background: rgba(99,102,241,.06); border-left: 3px solid var(--accent-primary); border-radius: 0 8px 8px 0; text-align: left; font-size: 0.85rem; color: var(--text-secondary);">
                            <?= nl2br(e($depositTransferDetails)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bank-details"
                    style="background: var(--bg-input); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Ngân hàng:</span>
                        <strong class="text-primary">
                            <?= e($bankConfig['bank_name']) ?>
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Chủ tài khoản:</span>
                        <strong class="text-primary">
                            <?= e($bankConfig['bank_acc_name']) ?>
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Số tài khoản:</span>
                        <strong class="text-primary">
                            <?= e($bankConfig['bank_acc_number']) ?>
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Nội dung CK:</span>
                        <strong class="text-danger" style="font-size: 1.2rem; letter-spacing: 1px;">
                            <?= e($transferContent) ?>
                        </strong>
                    </div>
                </div>

                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>QUAN TRỌNG:</strong> Nội dung chuyển khoản BẮT BUỘC phải là <code
                        style="color: #fff; font-size: 1.1em;"><?= e($transferContent) ?></code>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    Sau khi chuyển khoản, bấm nút <strong>"Kiểm tra trạng thái"</strong> hoặc đợi 1-3 phút để hệ thống
                    tự động cộng tiền.
                </div>
            </div>
        </div>

        <!-- Create Invoice Form + Check Status -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Tạo yêu cầu nạp tiền</h3>

                <form id="deposit-form" action="<?= url('/banking/create') ?>" method="POST">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label>Chọn cổng thanh toán</label>
                        <select name="method" class="form-control" required>
                            <option value="MBBank">MBBank (Tự động 1-3p)</option>
                            <option value="Vietcombank">Vietcombank (Tự động 1-3p)</option>
                            <option value="Momo">Ví Momo (Thủ công)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Số tiền cần nạp (VNĐ)</label>
                        <input type="number" name="amount" class="form-control" placeholder="100000" min="10000"
                            required>
                        <small class="text-secondary mt-1 d-block">Tối thiểu: 10,000đ</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block p-3">
                        <i class="fas fa-plus-circle"></i> TẠO HOÁ ĐƠN NẠP
                    </button>
                </form>

                <div id="ajax-message" class="mt-3"></div>

                <!-- Nút Kiểm Tra Trạng Thái -->
                <hr style="border-color: var(--border-color); margin: 25px 0;">
                <h4 class="mb-3 text-info"><i class="fas fa-sync-alt"></i> Đã chuyển khoản rồi?</h4>
                <p class="text-secondary mb-3">Bấm nút bên dưới để hệ thống kiểm tra ngay lập tức.</p>
                <button id="check-status-btn" class="btn btn-success btn-block p-3" style="font-size: 1.1rem;">
                    <i class="fas fa-search-dollar"></i> KIỂM TRA TRẠNG THÁI NẠP TIỀN
                </button>
                <div id="check-result" class="mt-3"></div>

                <div class="mt-4 text-center">
                    <a href="<?= url('/banking/history') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-history"></i> Xem lịch sử nạp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <?php if (count($invoices) > 0): ?>
        <div class="card mt-5">
            <div class="card-body">
                <h3 class="card-title mb-4">Giao dịch gần đây</h3>
                <div class="table-wrapper" style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã GD</th>
                                <th>Thời gian</th>
                                <th>Số tiền</th>
                                <th>Phương thức</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($invoices, 0, 10) as $invoice): ?>
                                <tr>
                                    <td>#<?= e($invoice['trans_id']) ?></td>
                                    <td><?= formatDate($invoice['created_at']) ?></td>
                                    <td class="text-success font-weight-bold">+<?= formatMoney($invoice['amount']) ?></td>
                                    <td><?= e($invoice['method']) ?></td>
                                    <td>
                                        <?php if ($invoice['status'] == 1): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Thành công</span>
                                        <?php elseif ($invoice['status'] == 2): ?>
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Đã hủy</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Đang chờ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card Deposit Section -->
    <div class="card mt-5">
        <div class="card-body">
            <h3 class="card-title mb-4"><i class="fas fa-sim-card" style="color: var(--accent-info);"></i> Nạp Thẻ Cào</h3>
            <p class="text-secondary mb-3">Nạp tiền bằng thẻ điện thoại / thẻ game. Phí chiết khấu áp dụng theo từng nhà mạng.</p>

            <form id="card-deposit-form" action="<?= url('/banking/card-deposit') ?>" method="POST">
                <?= csrfField() ?>

                <div class="form-group">
                    <label>Loại thẻ</label>
                    <select name="card_type" class="form-control" required>
                        <option value="">-- Chọn nhà mạng --</option>
                        <option value="VIETTEL">Viettel</option>
                        <option value="MOBIFONE">Mobifone</option>
                        <option value="VINAPHONE">Vinaphone</option>
                        <option value="VIETNAMOBILE">Vietnamobile</option>
                        <option value="ZING">Zing</option>
                        <option value="GARENA">Garena</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mệnh giá</label>
                    <select name="card_amount" class="form-control" required>
                        <option value="">-- Chọn mệnh giá --</option>
                        <option value="10000">10,000đ</option>
                        <option value="20000">20,000đ</option>
                        <option value="30000">30,000đ</option>
                        <option value="50000">50,000đ</option>
                        <option value="100000">100,000đ</option>
                        <option value="200000">200,000đ</option>
                        <option value="300000">300,000đ</option>
                        <option value="500000">500,000đ</option>
                        <option value="1000000">1,000,000đ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Số Serial</label>
                    <input type="text" name="card_serial" class="form-control" placeholder="Nhập số serial thẻ" required>
                </div>

                <div class="form-group">
                    <label>Mã thẻ</label>
                    <input type="text" name="card_code" class="form-control" placeholder="Nhập mã thẻ cào" required>
                </div>

                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i>
                    Thẻ sẽ được xử lý tự động trong 1-3 phút. Nếu khai sai mệnh giá, thẻ vẫn nạp theo giá trị thực tế.
                </div>

                <button type="submit" class="btn btn-primary btn-block p-3">
                    <i class="fas fa-paper-plane"></i> GỬI THẺ NẠP
                </button>
            </form>

            <div id="card-ajax-message" class="mt-3"></div>
        </div>
    </div>
</div>


<script>
    // Form tạo hoá đơn
    document.getElementById('deposit-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const msgDiv = document.getElementById('ajax-message');

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG XỬ LÝ...';
        btn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.json())
            .catch(() => ({ status: 'error', message: 'Lỗi mạng hoặc server không phản hồi' }))
            .then(data => {
                msgDiv.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                if (data.status === 'success' && data.redirect) {
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                } else {
                    btn.innerHTML = '<i class="fas fa-plus-circle"></i> TẠO HOÁ ĐƠN NẠP';
                    btn.disabled = false;
                }
            });
    });

    // Nút kiểm tra trạng thái
    document.getElementById('check-status-btn').addEventListener('click', function () {
        const btn = this;
        const resultDiv = document.getElementById('check-result');

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG KIỂM TRA...';
        btn.disabled = true;

        fetch('<?= url("/banking/check-status") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: '_csrf=<?= $_SESSION["csrf_token"] ?>'
        })
            .then(res => res.json())
            .catch(() => ({ status: 'error', message: 'Lỗi kết nối' }))
            .then(data => {
                let alertClass = 'info';
                if (data.status === 'success') alertClass = 'success';
                else if (data.status === 'error') alertClass = 'danger';
                else if (data.status === 'warning') alertClass = 'warning';
                else if (data.status === 'pending') alertClass = 'warning';

                resultDiv.innerHTML = `<div class="alert alert-${alertClass}">${data.message}</div>`;

                if (data.status === 'success') {
                    // Cập nhật balance trên navbar
                    if (data.balance) {
                        const balanceEls = document.querySelectorAll('.user-balance');
                        balanceEls.forEach(el => el.innerHTML = `<i class="fas fa-wallet"></i> ${data.balance}`);
                    }
                    // Reload 2s sau
                    setTimeout(() => location.reload(), 2000);
                }

                btn.innerHTML = '<i class="fas fa-search-dollar"></i> KIỂM TRA LẠI';
                btn.disabled = false;
            });
    });

    // Form nạp thẻ cào
    document.getElementById('card-deposit-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const msgDiv = document.getElementById('card-ajax-message');

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG GỬI THẺ...';
        btn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.json())
            .catch(() => ({ status: 'error', message: 'Lỗi mạng hoặc server không phản hồi' }))
            .then(data => {
                msgDiv.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                if (data.status === 'success' && data.redirect) {
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                } else {
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> GỬI THẺ NẠP';
                    btn.disabled = false;
                }
            });
    });
</script>