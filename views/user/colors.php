<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-palette"></i> Màu sắc giao diện</h1>
        <p class="page-subtitle">Tùy chỉnh giao diện trang web theo phong cách của bạn</p>
    </div>
</div>

<div class="theme-colors-grid">
    <!-- Light -->
    <button class="theme-card" data-theme-value="light" id="themeCardLight">
        <div class="theme-card-preview preview-light">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-light"></span>
            <div class="theme-card-text">
                <strong>Sáng</strong>
                <span>Giao diện sáng, nhẹ nhàng</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>

    <!-- Dark -->
    <button class="theme-card" data-theme-value="dark" id="themeCardDark">
        <div class="theme-card-preview preview-dark">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-dark"></span>
            <div class="theme-card-text">
                <strong>Tối</strong>
                <span>Bảo vệ mắt, tiết kiệm pin</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>

    <!-- Gray -->
    <button class="theme-card" data-theme-value="gray" id="themeCardGray">
        <div class="theme-card-preview preview-gray">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-gray"></span>
            <div class="theme-card-text">
                <strong>Xám</strong>
                <span>Hiện đại, tối giản</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>

    <!-- Blue -->
    <button class="theme-card" data-theme-value="blue" id="themeCardBlue">
        <div class="theme-card-preview preview-blue">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-blue"></span>
            <div class="theme-card-text">
                <strong>Xanh dương</strong>
                <span>Chuyên nghiệp, tin cậy</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>

    <!-- Green -->
    <button class="theme-card" data-theme-value="green" id="themeCardGreen">
        <div class="theme-card-preview preview-green">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-green"></span>
            <div class="theme-card-text">
                <strong>Xanh lá</strong>
                <span>Tươi mới, tự nhiên</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>

    <!-- Pink -->
    <button class="theme-card" data-theme-value="pink" id="themeCardPink">
        <div class="theme-card-preview preview-pink">
            <div class="preview-sidebar"></div>
            <div class="preview-content">
                <div class="preview-topbar"></div>
                <div class="preview-block"></div>
                <div class="preview-block short"></div>
            </div>
        </div>
        <div class="theme-card-info">
            <span class="theme-card-swatch swatch-pink"></span>
            <div class="theme-card-text">
                <strong>Hồng</strong>
                <span>Ngọt ngào, nữ tính</span>
            </div>
            <span class="theme-card-check"><i class="fas fa-check-circle"></i></span>
        </div>
    </button>
</div>

<script>
(function() {
    const cards = document.querySelectorAll('.theme-card');
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';

    // Mark current active
    function updateActive() {
        const active = document.documentElement.getAttribute('data-theme') || 'light';
        cards.forEach(function(card) {
            card.classList.toggle('active', card.getAttribute('data-theme-value') === active);
        });
    }

    // Handle click
    cards.forEach(function(card) {
        card.addEventListener('click', function() {
            const theme = this.getAttribute('data-theme-value');
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateActive();
        });
    });

    updateActive();
})();
</script>
