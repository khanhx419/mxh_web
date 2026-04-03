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
    // MYSTERY BAG MODULE (Stock-based Account Shop)
    // ==========================================
    const mbConfig = document.getElementById('mb-config');
    if (mbConfig) {
        var csrfToken = mbConfig.dataset.csrf || '';
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

        // Purchase Modal
        var currentBagId = null, currentBagPrice = 0;
        window.showPurchaseModal = function (id, name, price, stock) {
            currentBagId = id; currentBagPrice = price;
            document.getElementById('modal-bag-name').textContent = name;
            document.getElementById('modal-price').textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
            document.getElementById('modal-total').textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
            document.getElementById('modal-stock').textContent = stock + ' tài khoản';
            document.getElementById('purchaseModal').classList.add('active');
        };
        window.closePurchaseModal = function () { document.getElementById('purchaseModal').classList.remove('active'); };

        var confirmBtn = document.getElementById('btn-confirm-purchase');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                closePurchaseModal();
                buyBag(currentBagId);
            });
        }

        function buyBag(bagId) {
            var loading = document.getElementById('loading-animation'),
                resultBox = document.getElementById('result-content'),
                errorBox = document.getElementById('error-content');
            loading.style.display = 'block'; resultBox.style.display = 'none'; errorBox.style.display = 'none';
            document.getElementById('resultModal').classList.add('active');

            setTimeout(function () {
                fetch(openUrl + bagId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'csrf_token=' + csrfToken
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (data.csrf_token) csrfToken = data.csrf_token;
                    loading.style.display = 'none';

                    if (data.status === 'error') {
                        document.getElementById('error-desc').innerText = data.message;
                        errorBox.style.display = 'block';
                    } else {
                        // Success — show account info
                        document.getElementById('result-desc').innerText = data.message;

                        var acc = data.account || {};
                        
                        // Username
                        var usernameEl = document.getElementById('acc-username');
                        var usernameRow = document.getElementById('acc-username-row');
                        if (acc.username) { usernameEl.textContent = acc.username; usernameRow.style.display = 'flex'; }
                        else { usernameRow.style.display = 'none'; }

                        // Password
                        var passwordEl = document.getElementById('acc-password');
                        var passwordRow = document.getElementById('acc-password-row');
                        if (acc.password) { passwordEl.textContent = acc.password; passwordRow.style.display = 'flex'; }
                        else { passwordRow.style.display = 'none'; }

                        // Email
                        var emailEl = document.getElementById('acc-email');
                        var emailRow = document.getElementById('acc-email-row');
                        if (acc.email) { emailEl.textContent = acc.email; emailRow.style.display = 'flex'; }
                        else { emailRow.style.display = 'none'; }

                        // Extra
                        var extraEl = document.getElementById('acc-extra');
                        var extraRow = document.getElementById('acc-extra-row');
                        if (acc.extra && acc.extra.trim()) { extraEl.textContent = acc.extra; extraRow.style.display = 'flex'; }
                        else { extraRow.style.display = 'none'; }

                        // Update balance
                        document.querySelectorAll('.user-balance').forEach(function (el) { el.textContent = data.balance; });

                        resultBox.style.display = 'block';
                    }
                }).catch(function () {
                    loading.style.display = 'none';
                    document.getElementById('error-desc').innerText = 'Lỗi kết nối mạng. Vui lòng thử lại!';
                    errorBox.style.display = 'block';
                });
            }, 800);
        }

        // Copy helper
        window.copyText = function (elementId) {
            var text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text).then(function () {
                var btn = document.getElementById(elementId).closest('.mb-account-row').querySelector('.mb-copy-btn');
                if (btn) {
                    var orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.style.background = 'var(--accent-success)';
                    btn.style.color = '#fff';
                    setTimeout(function () {
                        btn.innerHTML = orig;
                        btn.style.background = '';
                        btn.style.color = '';
                    }, 1500);
                }
            });
        };

        window.closeResultModal = function () { document.getElementById('resultModal').classList.remove('active'); };
        document.querySelectorAll('.mb-modal-overlay').forEach(function (o) {
            o.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });
        });
        document.querySelectorAll('.mb-bag-card').forEach(function (c) {
            c.addEventListener('click', function () { var b = this.querySelector('.mb-btn-open:not([disabled])'); if (b) b.click(); });
        });
    }
});

