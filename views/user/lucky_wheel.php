<div class="container py-4">
    <div class="section-title text-center mb-5">
        <i class="fas fa-dharmachakra text-danger" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
        <h2>Vòng Quay May Mắn</h2>
        <p class="text-secondary mt-2">Thử vận may của bạn ngay hôm nay! Mỗi lượt quay chỉ
            <?= formatMoney($spinCost) ?>
        </p>
    </div>

    <div class="row">
        <!-- Wheel Area -->
        <div class="col-md-7 mb-4">
            <div class="card h-100" style="background: transparent; border: none; box-shadow: none;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">

                    <div class="wheel-container">
                        <div class="wheel-pointer"></div>
                        <div class="wheel" id="wheel">
                            <!-- JS sẽ vẽ các phần thưởng vào đây -->
                        </div>
                    </div>

                    <button id="spin-btn" class="btn btn-danger btn-lg mt-5 px-5"
                        style="border-radius: 50px; font-weight: bold; font-size: 1.2rem; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.5);">
                        <i class="fas fa-play mr-2"></i> QUAY NGAY (
                        <?= formatMoney($spinCost) ?>)
                    </button>

                    <div id="spin-result" class="mt-4 text-center" style="min-height: 60px;"></div>
                </div>
            </div>
        </div>

        <!-- Prizes & History -->
        <div class="col-md-5 mb-4">
            <div class="card mb-4" style="border-top: 4px solid var(--accent-warning);">
                <div class="card-body">
                    <h4 class="card-title text-warning mb-3"><i class="fas fa-gift"></i> Danh sách phần thưởng</h4>
                    <div class="row">
                        <?php foreach ($prizes as $prize): ?>
                            <div class="col-6 mb-2">
                                <span class="badge w-100 py-2 text-dark"
                                    style="background-color: <?= e($prize['color']) ?>; font-size: 0.9rem;">
                                    <?= e($prize['name']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <h4 class="card-title p-3 m-0 border-bottom border-dark text-info"><i class="fas fa-history"></i>
                        Biến động trúng thưởng</h4>
                    <div class="history-list" style="max-height: 300px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($history)): ?>
                                <li class="list-group-item bg-transparent text-center text-muted py-4">Chưa có ai quay
                                    thưởng</li>
                            <?php else: ?>
                                <?php foreach ($history as $h): ?>
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <i class="fas fa-user-circle text-secondary mr-2"></i>
                                            <strong>
                                                <?= e($h['username']) ?>
                                            </strong>
                                        </div>
                                        <span class="badge badge-success">
                                            <?= e($h['prize_name']) ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS cho Vòng quay */
    .wheel-container {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 0 auto;
    }

    .wheel {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5), 0 0 0 10px var(--accent-warning);
        position: relative;
        overflow: hidden;
        transition: transform 5s cubic-bezier(0.25, 0.1, 0.25, 1);
    }

    .wheel-pointer {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 30px solid var(--accent-danger);
        z-index: 10;
        filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.5));
    }

    .wheel-slice {
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 50%;
        transform-origin: 0% 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .wheel-text {
        position: absolute;
        left: 5px;
        bottom: 20px;
        transform: rotate(45deg);
        transform-origin: 0% 0%;
        color: #fff;
        font-weight: bold;
        font-size: 12px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        width: 120px;
        text-align: right;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 768px) {
        .wheel-container {
            width: 400px;
            height: 400px;
        }

        .wheel-text {
            font-size: 14px;
            width: 170px;
            bottom: 30px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const prizes = <?= json_encode($prizes) ?>;
        const wheel = document.getElementById('wheel');
        const spinBtn = document.getElementById('spin-btn');
        const resultDiv = document.getElementById('spin-result');
        const numSlices = prizes.length;
        const sliceAngle = 360 / numSlices;

        // Vẽ CSS vòng quay
        // Dùng background conic-gradient cho dễ chia phần
        let gradientParts = [];
        prizes.forEach((prize, index) => {
            let startAngle = index * sliceAngle;
            let endAngle = (index + 1) * sliceAngle;
            gradientParts.push(`${prize.color} ${startAngle}deg ${endAngle}deg`);

            // Thêm text overlay
            let slice = document.createElement('div');
            slice.className = 'wheel-slice';
            slice.style.transform = `rotate(${startAngle}deg) skewY(${90 - sliceAngle}deg)`;

            let text = document.createElement('div');
            text.className = 'wheel-text';
            // Tính góc xoay ngược lại để text dễ đọc thay vì nằm ngang
            let textRotation = sliceAngle / 2;
            text.style.transform = `skewY(${-(90 - sliceAngle)}deg) rotate(${textRotation}deg)`;
            text.innerText = prize.name;

            slice.appendChild(text);
            wheel.appendChild(slice);
        });

        // Fallback conic-gradient (không hỗ trợ border dễ dàng nên dùng text đè lên)
        wheel.style.background = `conic-gradient(${gradientParts.join(', ')})`;

        let currentRotation = 0;
        let isSpinning = false;

        spinBtn.addEventListener('click', function () {
            if (isSpinning) return;

            resultDiv.innerHTML = '';
            spinBtn.disabled = true;
            spinBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG QUAY...';

            fetch('<?= url("/lucky-wheel/spin") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `_csrf=<?= $_SESSION['csrf_token'] ?>` // Simplified CSRF for JS
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        spinBtn.disabled = false;
                        spinBtn.innerHTML = '<i class="fas fa-play mr-2"></i> QUAY LẠI';
                        return;
                    }

                    isSpinning = true;

                    // Tìm index của giải thưởng trúng
                    let prizeIndex = prizes.findIndex(p => p.id == data.prize_id);
                    if (prizeIndex === -1) prizeIndex = 0;

                    // Tính góc quay (thêm nhiều vòng + góc đỗ vảo giữa giải thưởng)
                    let extraSpins = 5; // Quay thêm 5 vòng

                    // Góc của mũi tên (Kim chỉ ở trên cùng = 270deg hoặc -90deg so với conic-gradient bắt đầu từ 0 ở trên cùng)
                    // Fix đơn giản: Mũi tên ở top (0 độ của vòng quay CSS)
                    // Ta cần xoay vòng quay sao cho phần thưởng (startAngle -> endAngle) rơi vào góc 360/0 độ.

                    let centerAngle = (prizeIndex * sliceAngle) + (sliceAngle / 2);
                    let targetRotation = (360 - centerAngle); // Xoay ngược lại để phần bù đưa item lên top

                    currentRotation += (360 * extraSpins) + targetRotation - (currentRotation % 360);

                    // Play sound effect (optional)

                    // Xoay
                    wheel.style.transform = `rotate(${currentRotation}deg)`;

                    // Đợi CSS transition kết thúc (5s)
                    setTimeout(() => {
                        isSpinning = false;
                        spinBtn.disabled = false;
                        spinBtn.innerHTML = '<i class="fas fa-play mr-2"></i> QUAY TIẾP (<?= formatMoney($spinCost) ?>)';

                        // Show trúng thưởng
                        // update user balance in navbar
                        const balanceEls = document.querySelectorAll('.user-balance');
                        balanceEls.forEach(el => el.innerHTML = `<i class="fas fa-wallet"></i> ${data.balance}`);

                        resultDiv.innerHTML = `
                    <div class="alert alert-success mt-3 animated bounceIn" style="font-size: 1.2rem; font-weight: bold;">
                        <i class="fas fa-gift mr-2"></i> ${data.message}
                    </div>
                `;
                    }, 5000);
                })
                .catch(err => {
                    console.error(err);
                    resultDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối máy chủ!</div>`;
                    spinBtn.disabled = false;
                    spinBtn.innerHTML = '<i class="fas fa-play mr-2"></i> THỬ LẠI';
                });
        });
    });
</script>