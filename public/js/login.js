/**
 * ==============================================================================
 * HỆ THỐNG QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: public/js/login.js
 * MÔ TẢ: Tập lệnh tương tác cho trang Đăng nhập Admin
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. HIỆN / ẨN MẬT KHẨU (SHOW / HIDE PASSWORD)
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            const icon = this.querySelector('i');
            if (isPassword) {
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                this.setAttribute('title', 'Ẩn mật khẩu');
            } else {
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                this.setAttribute('title', 'Hiện mật khẩu');
            }
        });
    }

    // 2. XỬ LÝ QUÊN MẬT KHẨU (NÂNG CẤP TỪ ALERT() NGUYÊN BẢN SANG MODAL/SWEET ALERT)
    const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
    if (forgotPasswordBtn) {
        forgotPasswordBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const modalEl = document.getElementById('forgotPasswordModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                if (typeof TechZoneNotify !== 'undefined') {
                    TechZoneNotify.info("Vui lòng liên hệ Trưởng bộ phận IT hoặc Quản trị viên cấp cao để được cấp lại mật khẩu đăng nhập!", "Quên Mật Khẩu");
                }
            }
        });
    }

    // 3. HIỆU ỨNG LOADING KHI SUBMIT FORM
    const loginForm = document.getElementById('adminLoginForm');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');

    if (loginForm && loginSubmitBtn) {
        loginForm.addEventListener('submit', function () {
            loginSubmitBtn.disabled = true;
            loginSubmitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xác thực...`;
        });
    }
});
