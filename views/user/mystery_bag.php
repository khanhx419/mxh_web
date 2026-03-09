<div class="container py-4">
    <div class="section-title text-center mb-5">
        <i class="fas fa-box-open text-primary" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
        <h2>Túi Mù Nhân Phẩm</h2>
        <p class="text-secondary mt-2">Mở túi mù, nhận ngay những tài khoản Game Cực Khủng hoặc hoàn trả bằng tiền thật!
        </p>
    </div>

    <!-- Túi mù list -->
    <div class="row mb-5">
        <?php foreach ($bags as $bag): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 mystery-bag-card"
                    style="border: 2px solid var(--border-color); border-radius: 15px; overflow: hidden; transition: all 0.3s; background: linear-gradient(145deg, var(--bg-card), rgba(20,20,30,0.8));">
                    <div class="card-body text-center p-4">
                        <div class="bag-icon mb-4"
                            style="font-size: 6rem; color: <?= $bag['price'] > 50000 ? 'var(--accent-warning)' : 'var(--accent-primary)' ?>;">
                            <i class="fas fa-shopping-bag shadow-icon"></i>
                        </div>

                        <h3 class="mb-3" style="color: var(--accent-info);">
                            <?= e($bag['name']) ?>
                        </h3>
                        <p class="text-secondary mb-4">
                            <?= e($bag['description']) ?>
                        </p>

                        <div class="price-tag mb-4">
                            <span style="font-size: 2rem; font-weight: bold; color: var(--accent-success);">
                                <?= formatMoney($bag['price']) ?>
                            </span>
                        </div>

                        <!-- Drop Rate Details (Collapsible) -->
                        <div class="mb-4 text-left">
                            <a class="btn btn-sm btn-outline-info w-100 mb-2" data-toggle="collapse"
                                href="#items-<?= $bag['id'] ?>" role="button" aria-expanded="false"
                                aria-controls="items-<?= $bag['id'] ?>">
                                <i class="fas fa-list-ul"></i> Xem tỉ lệ ra đồ
                            </a>
                            <div class="collapse" id="items-<?= $bag['id'] ?>">
                                <ul class="list-group list-group-flush small" style="background: transparent;">
                                    <?php foreach ($bag['items'] as $item): ?>
                                        <li
                                            class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-secondary py-1 px-0 text-light">
                                            <?= e($item['name']) ?>
                                            <span class="badge badge-primary badge-pill">
                                                <?= $item['probability'] ?>%
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg btn-block open-bag-btn" data-id="<?= $bag['id'] ?>"
                            style="font-weight: bold; font-size: 1.2rem; border-radius: 50px; text-transform: uppercase;">
                            <i class="fas fa-unlock-alt mr-2"></i> Mở Túi Ngay
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- History -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-info mb-4"><i class="fas fa-history"></i> Lịch Sử Mở Gần Đây</h4>
            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Người Mở</th>
                            <th>Túi Mù</th>
                            <th>Vật Phẩm Nhận Được</th>
                            <th>Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có ai mở túi</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history as $h): ?>
                                <tr>
                                    <td><i class="fas fa-user-circle text-secondary mr-2"></i><strong>
                                            <?= e($h['username']) ?>
                                        </strong></td>
                                    <td><span class="badge badge-info">
                                            <?= e($h['bag_name']) ?>
                                        </span></td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            <?= e($h['item_name']) ?>
                                        </span>
                                        <small class="d-block text-muted">
                                            <?= e($h['item_content']) ?>
                                        </small>
                                    </td>
                                    <td><small>
                                            <?= formatDate($h['created_at']) ?>
                                        </small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kết quả Mở Túi -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content"
            style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
            <div class="modal-body text-center py-5">
                <div id="loading-animation">
                    <i class="fas fa-box-open fa-3x fa-spin text-warning mb-4" style="animation-duration: 0.5s;"></i>
                    <h4 class="text-white">Đang mở túi...</h4>
                </div>

                <div id="result-content" style="display: none;">
                    <i class="fas fa-gift fa-4x text-success mb-3 bounce-anim"></i>
                    <h3 class="text-success mb-2" id="result-title">Chúc mừng!</h3>
                    <p class="text-light" id="result-desc" style="font-size: 1.1rem;"></p>

                    <div class="mt-4 p-3 rounded" style="background: rgba(0,0,0,0.3);">
                        <small class="text-muted d-block mb-1">Nội dung quà:</small>
                        <strong class="text-warning" id="result-detail" style="font-size: 1.2rem;"></strong>
                    </div>

                    <button type="button" class="btn btn-outline-light mt-4 px-4" data-dismiss="modal"
                        onclick="location.reload()">Đóng & Cập nhật</button>
                </div>

                <div id="error-content" style="display: none;">
                    <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                    <h4 class="text-danger mb-2">Thất bại</h4>
                    <p class="text-light" id="error-desc"></p>
                    <button type="button" class="btn btn-outline-light mt-4 px-4" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mystery-bag-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 212, 170, 0.2) !important;
        border-color: var(--accent-success) !important;
    }

    .shadow-icon {
        filter: drop-shadow(0 10px 10px rgba(0, 0, 0, 0.5));
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-30px);
        }

        60% {
            transform: translateY(-15px);
        }
    }

    .bounce-anim {
        animation: bounce 1s ease;
    }
</style>

<!-- Custom JS for Bootstrap toggles directly without heavy jQuery -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Basic collapse functionality for items
        const collapseToggles = document.querySelectorAll('[data-toggle="collapse"]');
        collapseToggles.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    target.style.display = 'none';
                } else {
                    target.classList.add('show');
                    target.style.display = 'block';
                }
            });
        });

        // Handle Bag Opening
        const openBtns = document.querySelectorAll('.open-bag-btn');
        const modalWrap = document.createElement('div'); // Fake modal overlay if BS JS missing
        const resultModal = document.getElementById('resultModal');

        // JS helpers for minimal modal
        function showModal() {
            resultModal.classList.add('show');
            resultModal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            let backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop';
            document.body.appendChild(backdrop);
        }

        function hideModal() {
            resultModal.classList.remove('show');
            resultModal.style.display = 'none';
            document.body.style.overflow = '';
            let backdrop = document.getElementById('modal-backdrop');
            if (backdrop) backdrop.remove();
        }

        document.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', hideModal);
        });

        openBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const bagId = this.getAttribute('data-id');
                const loading = document.getElementById('loading-animation');
                const resultBox = document.getElementById('result-content');
                const errorBox = document.getElementById('error-content');

                // UI Reset
                loading.style.display = 'block';
                resultBox.style.display = 'none';
                errorBox.style.display = 'none';
                showModal();

                // Fake loading delay for suspense
                setTimeout(() => {
                    fetch(`<?= url('/mystery-bag/open/') ?>${bagId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `_csrf=<?= $_SESSION['csrf_token'] ?>`
                    })
                        .then(res => res.json())
                        .then(data => {
                            loading.style.display = 'none';
                            if (data.status === 'error') {
                                document.getElementById('error-desc').innerText = data.message;
                                errorBox.style.display = 'block';
                            } else {
                                document.getElementById('result-desc').innerText = `Nhận: ${data.item_name}`;
                                document.getElementById('result-detail').innerText = data.item_content;

                                // Update balance
                                const balanceEls = document.querySelectorAll('.user-balance');
                                balanceEls.forEach(el => el.innerHTML = `<i class="fas fa-wallet"></i> ${data.balance}`);

                                resultBox.style.display = 'block';
                            }
                        })
                        .catch(err => {
                            loading.style.display = 'none';
                            document.getElementById('error-desc').innerText = "Rớt mạng rồi, vui lòng thử lại!";
                            errorBox.style.display = 'block';
                        });
                }, 1000); // 1s delay
            });
        });
    });
</script>