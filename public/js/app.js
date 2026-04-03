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
});
