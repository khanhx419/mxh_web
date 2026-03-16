<div class="container">
    <div class="page-header text-center">
        <h1 style="background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <i class="fas fa-dharmachakra" style="-webkit-text-fill-color: initial; color: var(--accent-secondary);"></i>
            Vòng Quay May Mắn
        </h1>
        <p class="text-secondary mt-1">Thử vận may của bạn ngay hôm nay! Mỗi lượt quay chỉ <?= formatMoney($spinCost) ?></p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 28px; align-items: start;">
        <!-- Wheel Area -->
        <div style="text-align: center;">
            <div class="wheel-wrapper">
                <div class="wheel-glow"></div>
                <div class="wheel-outer-ring">
                    <canvas id="wheelCanvas" width="420" height="420"></canvas>
                    <div class="wheel-pointer-wrap">
                        <div class="wheel-pointer"></div>
                    </div>
                    <div class="wheel-center-circle">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <button id="spin-btn" class="btn btn-lg mt-3" style="background: var(--gradient-primary); color: #fff; border-radius: 50px; padding: 14px 48px; font-size: 1.05rem; box-shadow: var(--shadow-glow);">
                <i class="fas fa-play"></i> QUAY NGAY (<?= formatMoney($spinCost) ?>)
            </button>

            <div id="spin-result" class="mt-2" style="min-height: 50px;"></div>
        </div>

        <!-- Prizes & History Column -->
        <div>
            <!-- Prize List -->
            <div class="card mb-2" style="border-top: 3px solid var(--accent-warning);">
                <div class="card-body">
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 14px; color: var(--accent-warning);">
                        <i class="fas fa-gift"></i> Danh sách phần thưởng
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <?php foreach ($prizes as $prize): ?>
                            <div style="padding: 8px 12px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-align: center; color: #fff; background: <?= e($prize['color']) ?>;">
                                <?= e($prize['name']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <h4 style="font-size: 1rem; font-weight: 700; padding: 16px; margin: 0; border-bottom: 1px solid var(--border-color); color: var(--accent-info);">
                        <i class="fas fa-clock-rotate-left"></i> Biến động trúng thưởng
                    </h4>
                    <div style="max-height: 340px; overflow-y: auto;">
                        <?php if (empty($history)): ?>
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fas fa-inbox"></i>
                                <p class="text-muted">Chưa có ai quay thưởng</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($history as $h): ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid var(--border-color-light);">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user-circle" style="color: var(--text-muted);"></i>
                                        <span style="font-weight: 600; font-size: 0.85rem;"><?= e($h['username']) ?></span>
                                    </div>
                                    <span class="badge badge-success"><?= e($h['prize_name']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confetti container -->
    <div id="confetti-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>
</div>

<style>
    .wheel-wrapper {
        position: relative;
        display: inline-block;
        margin: 0 auto;
    }

    .wheel-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 460px;
        height: 460px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        animation: glowPulse 3s ease-in-out infinite;
        z-index: 0;
    }

    .wheel-outer-ring {
        position: relative;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        padding: 10px;
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        box-shadow: 0 0 40px rgba(99, 102, 241, 0.3), 0 0 80px rgba(99, 102, 241, 0.15);
        z-index: 1;
    }

    #wheelCanvas {
        display: block;
        border-radius: 50%;
        width: 400px;
        height: 400px;
    }

    .wheel-pointer-wrap {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }

    .wheel-pointer {
        width: 0;
        height: 0;
        border-left: 16px solid transparent;
        border-right: 16px solid transparent;
        border-top: 32px solid var(--accent-secondary);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.4));
        animation: pointerBounce 1.5s ease-in-out infinite;
    }

    .wheel-center-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        z-index: 5;
    }

    @keyframes glowPulse {
        0%, 100% { opacity: 0.6; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 1; transform: translate(-50%, -50%) scale(1.05); }
    }

    @keyframes pointerBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(4px); }
    }

    .confetti-piece {
        position: fixed;
        width: 10px;
        height: 10px;
        border-radius: 2px;
        animation: confettiFall linear forwards;
        z-index: 9999;
    }

    @keyframes confettiFall {
        0% { opacity: 1; transform: translateY(0) rotate(0deg); }
        100% { opacity: 0; transform: translateY(100vh) rotate(720deg); }
    }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: 1fr 380px"] {
            display: block !important;
        }
        .wheel-outer-ring {
            width: 320px;
            height: 320px;
        }
        #wheelCanvas {
            width: 300px;
            height: 300px;
        }
        .wheel-glow {
            width: 360px;
            height: 360px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prizes = <?= json_encode($prizes) ?>;
    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');
    const spinBtn = document.getElementById('spin-btn');
    const resultDiv = document.getElementById('spin-result');
    const numSlices = prizes.length;
    const sliceAngle = (2 * Math.PI) / numSlices;
    const size = canvas.width;
    const center = size / 2;
    const radius = center - 5;

    let currentAngle = 0;
    let isSpinning = false;

    // Draw the wheel
    function drawWheel(rotation) {
        ctx.clearRect(0, 0, size, size);
        ctx.save();
        ctx.translate(center, center);
        ctx.rotate(rotation);

        for (let i = 0; i < numSlices; i++) {
            const startAngle = i * sliceAngle;
            const endAngle = startAngle + sliceAngle;

            // Draw slice
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.arc(0, 0, radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = prizes[i].color;
            ctx.fill();

            // Slice border
            ctx.strokeStyle = 'rgba(255,255,255,0.15)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Draw text
            ctx.save();
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 13px Inter, sans-serif';
            ctx.shadowColor = 'rgba(0,0,0,0.5)';
            ctx.shadowBlur = 3;

            const text = prizes[i].name;
            const maxWidth = radius - 30;
            let displayText = text;
            if (ctx.measureText(text).width > maxWidth) {
                displayText = text.substring(0, 10) + '...';
            }
            ctx.fillText(displayText, radius - 18, 5);
            ctx.restore();
        }

        ctx.restore();
    }

    drawWheel(0);

    function createConfetti() {
        const container = document.getElementById('confetti-container');
        const colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#38bdf8', '#a855f7'];
        for (let i = 0; i < 60; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + '%';
            piece.style.top = '-10px';
            piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            piece.style.width = (Math.random() * 8 + 4) + 'px';
            piece.style.height = (Math.random() * 8 + 4) + 'px';
            piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
            piece.style.animationDelay = (Math.random() * 1) + 's';
            container.appendChild(piece);
        }
        setTimeout(() => container.innerHTML = '', 4000);
    }

    spinBtn.addEventListener('click', function() {
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
            body: `csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message}</div>`;
                spinBtn.disabled = false;
                spinBtn.innerHTML = '<i class="fas fa-play"></i> QUAY LẠI';
                return;
            }

            isSpinning = true;

            let prizeIndex = prizes.findIndex(p => p.id == data.prize_id);
            if (prizeIndex === -1) prizeIndex = 0;

            // Calculate target: the pointer is at top (270deg / -PI/2 in canvas coords)
            // We need the prize slice center to end up at the top
            const prizeCenterAngle = prizeIndex * sliceAngle + sliceAngle / 2;
            const fullSpins = 6;
            const targetRotation = fullSpins * 2 * Math.PI + (2 * Math.PI - prizeCenterAngle) - Math.PI / 2;

            const startAngle = currentAngle;
            const totalRotation = targetRotation - (currentAngle % (2 * Math.PI)) + 2 * Math.PI;
            const duration = 5000;
            const startTime = performance.now();

            function animate(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease out cubic
                const ease = 1 - Math.pow(1 - progress, 3);
                const angle = startAngle + totalRotation * ease;
                drawWheel(angle);
                currentAngle = angle;

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    isSpinning = false;
                    spinBtn.disabled = false;
                    spinBtn.innerHTML = '<i class="fas fa-play"></i> QUAY TIẾP (<?= formatMoney($spinCost) ?>)';

                    // Update balance in topbar
                    document.querySelectorAll('.user-balance-amount').forEach(el => {
                        el.textContent = data.balance;
                    });

                    // Confetti effect
                    createConfetti();

                    resultDiv.innerHTML = `
                        <div class="alert alert-success" style="font-size: 1rem; font-weight: 700; justify-content: center;">
                            <i class="fas fa-gift"></i> ${data.message}
                        </div>
                    `;
                }
            }

            requestAnimationFrame(animate);
        })
        .catch(err => {
            console.error(err);
            resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Lỗi kết nối máy chủ!</div>`;
            spinBtn.disabled = false;
            spinBtn.innerHTML = '<i class="fas fa-play"></i> THỬ LẠI';
        });
    });
});
</script>