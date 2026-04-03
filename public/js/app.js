/**
 * ShopAcc VN - Main JavaScript v2.0
 * Sidebar + Theme Toggle + Utilities
 */

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // THEME (applied via localStorage in head)
    // ==========================================

    // ==========================================
    // SIDEBAR TOGGLE (Mobile)
    // ==========================================
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on nav link click (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
    }

    // ==========================================
    // PERSISTENT ALERTS (Manual Close)
    // ==========================================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Add close button if not already present
        if (!alert.querySelector('.alert-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'alert-close';
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.setAttribute('aria-label', 'Đóng');
            alert.appendChild(closeBtn);
        }
        // Close on click
        alert.querySelector('.alert-close').addEventListener('click', function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    });

    // ==========================================
    // CONFIRM DELETE ACTIONS
    // ==========================================
    const deleteLinks = document.querySelectorAll('[data-confirm]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn xóa?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ==========================================
    // IMAGE PREVIEW ON FILE INPUT
    // ==========================================
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ==========================================
    // FORMAT MONEY INPUTS
    // ==========================================
    const moneyInputs = document.querySelectorAll('.money-input');
    moneyInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = value;
        });
    });

    // ==========================================
    // SERVICE ORDER TOTAL CALCULATOR
    // ==========================================
    const quantityInput = document.getElementById('quantity');
    const priceDisplay = document.getElementById('totalPrice');
    const pricePerUnit = document.getElementById('pricePerUnit');
    
    if (quantityInput && priceDisplay && pricePerUnit) {
        quantityInput.addEventListener('input', function() {
            const qty = parseInt(this.value) || 0;
            const price = parseFloat(pricePerUnit.value) || 0;
            const total = (qty / 1000) * price;
            priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        });
    }

    // ==========================================
    // LEADERBOARD TABS
    // ==========================================
    const lbTabs = document.querySelectorAll('.leaderboard-tab');
    const lbPanels = document.querySelectorAll('.leaderboard-panel');
    
    lbTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            lbTabs.forEach(t => t.classList.remove('active'));
            lbPanels.forEach(p => p.style.display = 'none');
            
            this.classList.add('active');
            const panel = document.getElementById(target);
            if (panel) panel.style.display = 'block';
        });
    });

    // ==========================================
    // GLOBAL POPUP NOTIFICATION
    // ==========================================
    const globalPopupOverlay = document.getElementById('globalPopupOverlay');
    const globalPopupCloseBtn = document.getElementById('globalPopupCloseBtn');
    const globalPopupCloseBtn2 = document.getElementById('globalPopupCloseBtn2');
    const globalPopupHide1hBtn = document.getElementById('globalPopupHide1hBtn');

    if (globalPopupOverlay) {
        const popupHideTime = localStorage.getItem('globalPopupHideTime');
        const now = new Date().getTime();

        // Check if popup should be shown
        if (!popupHideTime || now > parseInt(popupHideTime)) {
            setTimeout(() => {
                globalPopupOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }, 600);
        }

        const closePopup = () => {
            globalPopupOverlay.classList.remove('show');
            document.body.style.overflow = '';
        };

        const hidePopup1h = () => {
            const oneHour = 60 * 60 * 1000;
            localStorage.setItem('globalPopupHideTime', new Date().getTime() + oneHour);
            closePopup();
        };

        if (globalPopupCloseBtn) globalPopupCloseBtn.addEventListener('click', closePopup);
        if (globalPopupCloseBtn2) globalPopupCloseBtn2.addEventListener('click', closePopup);
        if (globalPopupHide1hBtn) globalPopupHide1hBtn.addEventListener('click', hidePopup1h);
        
        // Close when clicking outside
        globalPopupOverlay.addEventListener('click', (e) => {
            if (e.target === globalPopupOverlay) {
                closePopup();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && globalPopupOverlay.classList.contains('show')) {
                closePopup();
            }
        });
    }

    // ==========================================
    // MYSTERY BAG MODULE
    // ==========================================
    const mbConfig = document.getElementById('mb-config');
    if (mbConfig) {
        var csrfToken = mbConfig.dataset.csrf || '';
        var checkinUrl = mbConfig.dataset.checkinUrl || '';
        var openUrl = mbConfig.dataset.openUrl || '';

        // Particles
        var canvas = document.getElementById('particles-canvas');
        if (canvas) {
            var ctx = canvas.getContext('2d'), particles = [];
            function resizeCanvas() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
            resizeCanvas(); window.addEventListener('resize', resizeCanvas);
            var colors = ['#fbbf24', '#a78bfa', '#38bdf8', '#f472b6', '#6366f1'];
            for (var i = 0; i < 25; i++) {
                particles.push({
                    x: Math.random() * canvas.width, y: Math.random() * canvas.height,
                    size: Math.random() * 4 + 1, sx: (Math.random() - .5) * .5, sy: Math.random() * -.8 - .2,
                    op: Math.random() * .5 + .1, rot: Math.random() * Math.PI * 2, rs: (Math.random() - .5) * .02,
                    color: colors[Math.floor(Math.random() * colors.length)]
                });
            }
            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(function (p) {
                    p.x += p.sx; p.y += p.sy; p.rot += p.rs;
                    if (p.y < -20) { p.y = canvas.height + 20; p.x = Math.random() * canvas.width; }
                    ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot);
                    ctx.globalAlpha = Math.max(0, Math.min(1, p.op + Math.sin(Date.now() * .001 + p.x) * .1));
                    ctx.fillStyle = p.color; ctx.shadowColor = p.color; ctx.shadowBlur = 6;
                    ctx.beginPath(); ctx.moveTo(0, -p.size); ctx.lineTo(p.size * .6, 0);
                    ctx.lineTo(0, p.size); ctx.lineTo(-p.size * .6, 0); ctx.closePath(); ctx.fill();
                    ctx.restore();
                });
                requestAnimationFrame(animateParticles);
            }
            animateParticles();
        }

        // Check-in
        var btnCheckin = document.getElementById('btn-checkin');
        if (btnCheckin) {
            btnCheckin.addEventListener('click', function () {
                btnCheckin.disabled = true; btnCheckin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                fetch(checkinUrl, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'csrf_token=' + csrfToken
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (data.csrf_token) csrfToken = data.csrf_token;
                    if (data.status === 'success') {
                        btnCheckin.innerHTML = '<i class="fas fa-check"></i> ' + data.message;
                        btnCheckin.className = 'mb-btn-checkin mb-btn-done';
                        var sd = document.getElementById('free-spins-display'); if (sd) sd.textContent = data.total_spins;
                        var sc = document.getElementById('free-spins-count'); if (sc) sc.textContent = data.total_spins;
                        var mf = document.getElementById('modal-free-spins'); if (mf) mf.textContent = data.free_spins;
                        var days = document.querySelectorAll('.mb-day-item');
                        if (days[data.day - 1]) {
                            days[data.day - 1].className = 'mb-day-item mb-day-checked';
                            days[data.day - 1].querySelector('.mb-day-icon').innerHTML = '<i class="fas fa-check-circle"></i>';
                        }
                        if (data.day < 7 && days[data.day]) { days[data.day].className = 'mb-day-item mb-day-locked'; }
                    } else {
                        btnCheckin.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
                        btnCheckin.disabled = false;
                    }
                }).catch(function () { btnCheckin.innerHTML = 'Lỗi, thử lại!'; btnCheckin.disabled = false; });
            });
        }

        // Purchase Modal
        var currentBagId = null, currentBagPrice = 0;
        window.showPurchaseModal = function (id, name, price) {
            currentBagId = id; currentBagPrice = price;
            document.getElementById('modal-bag-name').textContent = name;
            document.getElementById('modal-price').textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
            var freeEl = document.getElementById('modal-free-spins');
            var freeCount = freeEl ? (parseInt(freeEl.textContent) || 0) : 0;
            var useFreeEl = document.getElementById('modal-use-free');
            if (useFreeEl) useFreeEl.checked = freeCount > 0;
            updatePaymentInfo();
            document.getElementById('purchaseModal').classList.add('active');
        };
        window.closePurchaseModal = function () { document.getElementById('purchaseModal').classList.remove('active'); };
        window.updatePaymentInfo = function () {
            var useFreeEl = document.getElementById('modal-use-free');
            var useFree = useFreeEl ? useFreeEl.checked : false;
            document.getElementById('modal-total').textContent = useFree ? 'Miễn phí (1 lượt)' : new Intl.NumberFormat('vi-VN').format(currentBagPrice) + 'đ';
            document.getElementById('modal-total').className = useFree ? 'mb-text-success' : 'mb-text-warning';
        };

        var confirmBtn = document.getElementById('btn-confirm-purchase');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                var useFreeEl = document.getElementById('modal-use-free');
                var useFree = useFreeEl ? useFreeEl.checked : false;
                closePurchaseModal(); openBag(currentBagId, useFree);
            });
        }

        function openBag(bagId, useFree) {
            var loading = document.getElementById('loading-animation'),
                resultBox = document.getElementById('result-content'),
                errorBox = document.getElementById('error-content');
            loading.style.display = 'block'; resultBox.style.display = 'none'; errorBox.style.display = 'none';
            document.getElementById('resultModal').classList.add('active');
            setTimeout(function () {
                var body = 'csrf_token=' + csrfToken;
                if (useFree) body += '&use_free_spin=1';
                fetch(openUrl + bagId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (data.csrf_token) csrfToken = data.csrf_token;
                    loading.style.display = 'none';
                    if (data.status === 'error') {
                        document.getElementById('error-desc').innerText = data.message;
                        errorBox.style.display = 'block';
                    } else {
                        var icon = document.querySelector('.mb-result-success-icon i');
                        var title = document.getElementById('result-title');
                        if (data.is_lucky) {
                            title.textContent = '🎉 Chúc mừng!'; title.style.color = 'var(--accent-success)';
                            if (icon) { icon.className = 'fas fa-coins'; icon.style.cssText = 'font-size:2rem;color:#10b981'; }
                        } else {
                            title.textContent = '😢 Tiếc quá!'; title.style.color = 'var(--accent-warning)';
                            if (icon) { icon.className = 'fas fa-sad-tear'; icon.style.cssText = 'font-size:2rem;color:#f59e0b'; }
                        }
                        document.getElementById('result-desc').innerText = data.item_name;
                        document.getElementById('result-detail').innerText = data.item_content;
                        document.querySelectorAll('.user-balance').forEach(function (el) { el.innerHTML = '<i class="fas fa-wallet"></i> ' + data.balance; });
                        if (data.free_spins !== undefined) {
                            var sd = document.getElementById('free-spins-display'); if (sd) sd.textContent = data.free_spins;
                            var sc = document.getElementById('free-spins-count'); if (sc) sc.textContent = data.free_spins;
                            var mf = document.getElementById('modal-free-spins'); if (mf) mf.textContent = data.free_spins;
                        }
                        resultBox.style.display = 'block';
                    }
                }).catch(function () {
                    loading.style.display = 'none';
                    document.getElementById('error-desc').innerText = 'Lỗi mạng, thử lại!';
                    errorBox.style.display = 'block';
                });
            }, 1200);
        }

        window.closeResultModal = function () { document.getElementById('resultModal').classList.remove('active'); };
        window.toggleItems = function (id) { var p = document.getElementById('items-panel-' + id); if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none'; };
        document.querySelectorAll('.mb-modal-overlay').forEach(function (o) {
            o.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });
        });
        document.querySelectorAll('.mb-bag-card').forEach(function (c) {
            c.addEventListener('click', function () { var b = this.querySelector('.mb-btn-open'); if (b) b.click(); });
        });
    }
});

