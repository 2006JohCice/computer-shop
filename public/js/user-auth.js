/**
 * ==============================================================================
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: public/js/user-auth.js
 * MÔ TẢ: Tập lệnh tương tác cho trang Đăng nhập & Đăng ký Khách hàng (User Client)
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. HIỆN / ẨN MẬT KHẨU CHO CÁC Ô PASSWORD
    const toggleBtns = document.querySelectorAll('.user-password-toggle');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target') || 'userPassword';
            const input = document.getElementById(targetId);
            if (input) {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');

                const icon = this.querySelector('i');
                if (icon) {
                    if (isPassword) {
                        icon.classList.replace('bi-lock', 'bi-unlock');
                    } else {
                        icon.classList.replace('bi-unlock', 'bi-lock');
                    }
                }
            }
        });
    });

    // 2. HIỆU ỨNG LOADING KHI SUBMIT FORM
    const authForms = document.querySelectorAll('.user-auth-form');
    authForms.forEach(form => {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('.user-auth-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...`;
            }
        });
    });

    // 3. NÚT CHAT HỖ TRỢ NỔI
    const chatBtn = document.querySelector('.user-floating-chat');
    if (chatBtn) {
        chatBtn.addEventListener('click', function () {
            if (typeof TechZoneNotify !== 'undefined') {
                TechZoneNotify.info('Bộ phận Chăm sóc khách hàng TechZone đang sẵn sàng hỗ trợ bạn qua Hotline: 1900 6868!', 'Hỗ Trợ Trực Tuyến');
            } else {
                window.location.href = 'tel:19006868';
            }
        });
    }
});
