/**
 * ShopAcc VN - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('[data-confirm]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn xóa?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Image preview on file input
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

    // Format money inputs
    const moneyInputs = document.querySelectorAll('.money-input');
    moneyInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = value;
        });
    });

    // Calculate total for service orders
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
});
