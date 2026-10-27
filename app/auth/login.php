<?php
/**
 * ==============================================================================
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: app/views/auth/login.php
 * MÔ TẢ: Giao diện ĐĂNG NHẬP / ĐĂNG KÝ DÙNG CHUNG cho cả Khách hàng & Quản trị viên
 * LƯU Ý: Giao diện thuần (HTML/CSS/JS) - Không logic, không session
 * ==============================================================================
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'TECHZONE - Quản Trị & Mua Sắm Laptop');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/computer-shop/public');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống | <?= APP_NAME ?></title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Giao diện đăng nhập dùng chung -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
    <!-- TechZone Global Notify & Confirm CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/techzone-notify.css">
</head>

<body>
    <div class="login-wrapper">
        <!-- CỘT TRÁI: FORM ĐĂNG NHẬP / ĐĂNG KÝ CHUNG -->
        <div class="login-left">
            <div class="login-box">
                
                <!-- 1. Header Đăng Nhập -->
                <header class="login-header">
                    <div class="icon-login">
                        <img class="admin-logo_login" alt="TechZone Logo" src="<?= BASE_URL ?>/img/login/logoHunre.jpg" />
                        <h1 class="logo-text" id="brandTitle">TECHZONE</h1>
                    </div>
                    <p class="subtitle" id="formSubtitle">Hệ Thống Quản Lý Cửa Hàng & Mua Sắm Laptop</p>
                </header>

                <!-- 2. Tab Chuyển Đổi Vai Trò: Khách Hàng (User) vs Quản Trị Viên (Admin) -->
                <div class="role-tab-container">
                    <button type="button" class="role-tab-btn active" id="btnRoleUser">
                        <i class="bi bi-person-heart"></i>
                        <span>Khách Hàng</span>
                    </button>
                    <button type="button" class="role-tab-btn" id="btnRoleAdmin">
                        <i class="bi bi-shield-lock"></i>
                        <span>Quản Trị Viên</span>
                    </button>
                </div>

                <!-- 3. Form Đăng Nhập / Đăng Ký -->
                <form class="login-form" id="mainAuthForm" method="POST" action="#">
                    
                    <!-- Trường Họ và Tên (Chỉ hiển thị khi ở chế độ Đăng Ký) -->
                    <div class="form-group-custom" id="fullNameGroup" style="display: none;">
                        <label class="form-label-custom">Họ và tên <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-card-heading input-icon-left"></i>
                            <input type="text" id="fullNameInput" name="fullname" placeholder="Nhập họ và tên đầy đủ..." autocomplete="name" />
                        </div>
                    </div>

                    <!-- Trường Tên Đăng Nhập / Email / Số điện thoại -->
                    <div class="form-group-custom">
                        <label class="form-label-custom" id="accountLabel">Email hoặc Số điện thoại <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-person input-icon-left"></i>
                            <input type="text" id="accountInput" name="username" placeholder="Nhập email hoặc số điện thoại..." required autocomplete="username" />
                        </div>
                    </div>

                    <!-- Trường Mật Khẩu -->
                    <div class="form-group-custom">
                        <label class="form-label-custom">Mật khẩu truy cập <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-shield-lock input-icon-left"></i>
                            <input type="password" id="passwordInput" name="password" placeholder="Nhập mật khẩu..." required autocomplete="current-password" />
                            <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Hiện mật khẩu">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Trường Xác Nhận Mật Khẩu (Chỉ hiển thị khi Đăng Ký) -->
                    <div class="form-group-custom" id="confirmPasswordGroup" style="display: none;">
                        <label class="form-label-custom">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-check2-circle input-icon-left"></i>
                            <input type="password" id="confirmPasswordInput" name="confirm_password" placeholder="Nhập lại mật khẩu..." autocomplete="new-password" />
                        </div>
                    </div>

                    <!-- Tùy Chọn Ghi Nhớ & Quên Mật Khẩu -->
                    <div class="login-options" id="loginOptionsArea">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" />
                            <span>Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="#" class="forgot-password-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Quên Mật Khẩu?</a>
                    </div>

                    <!-- Nút Bấm Thực Hiện (Submit) -->
                    <button type="submit" class="login-btn" id="submitActionBtn">
                        <i class="bi bi-box-arrow-in-right" id="submitIcon"></i>
                        <span id="submitText">Đăng Nhập Khách Hàng</span>
                    </button>
                </form>

                <!-- 4. Chuyển Đổi Nhanh Giữa Đăng Nhập & Đăng Ký (Dành cho Khách Hàng) -->
                <div class="auth-switch-box" id="authSwitchArea">
                    <span id="switchPromptText">Chưa có tài khoản TechZone?</span>
                    <a href="javascript:void(0)" class="auth-switch-link" id="authToggleLink">Đăng ký thành viên ngay</a>
                </div>

                <!-- 5. Footer Chân Trang -->
                <div class="login-footer">
                    <p class="mb-0">&copy; <?= date('Y') ?> TECHZONE Laptop Management. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: MOCKUP & GIỚI THIỆU HỆ THỐNG TECHZONE -->
        <div class="login-right">
            <div class="right-content">
                <!-- Logo Chữ TechZone -->
                <a href="<?= BASE_URL ?>" class="d-inline-block">
                    <img alt="Brand Logo" src="<?= BASE_URL ?>/img/login/Textlogo.png" class="right-brand-logo" />
                </a>

                <!-- Hình ảnh Mockup Dashboard Laptop -->
                <div class="mockup-area">
                    <img src="<?= BASE_URL ?>/img/login/imgright.png" alt="Laptop Shop Dashboard Mockup" />
                </div>

                <!-- Tiêu đề giới thiệu chuẩn mẫu -->
                <h2>Fast, Efficient and Productive</h2>
                <p>
                    Giải pháp quản lý kho laptop thông minh, theo dõi Serial/IMEI, xuất hóa đơn tài chính và mua sắm máy tính chính hãng thế hệ mới.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Hướng dẫn Quên Mật Khẩu -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-shield-lock text-primary me-2"></i>Khôi Phục Mật Khẩu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 28px;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Hỗ Trợ Khôi Phục Tài Khoản</h6>
                    <p class="text-secondary small mb-3">
                        Vui lòng nhập Email hoặc liên hệ Quản trị viên để nhận lại mã khôi phục mật khẩu truy cập hệ thống.
                    </p>
                    <input type="email" class="form-control mb-3" placeholder="Nhập địa chỉ email đăng ký..." />
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">
                        <i class="bi bi-send me-1"></i> Gửi Yêu Cầu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT TƯƠNG TÁC GIAO DIỆN (100% THUẦN UI) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Trạng thái giao diện
            let currentRole = 'user'; // 'user' hoặc 'admin'
            let isRegisterMode = false; // false = Đăng nhập, true = Đăng ký

            const btnRoleUser = document.getElementById('btnRoleUser');
            const btnRoleAdmin = document.getElementById('btnRoleAdmin');
            const accountLabel = document.getElementById('accountLabel');
            const accountInput = document.getElementById('accountInput');
            const submitText = document.getElementById('submitText');
            const submitIcon = document.getElementById('submitIcon');
            const authSwitchArea = document.getElementById('authSwitchArea');
            const switchPromptText = document.getElementById('switchPromptText');
            const authToggleLink = document.getElementById('authToggleLink');
            const fullNameGroup = document.getElementById('fullNameGroup');
            const confirmPasswordGroup = document.getElementById('confirmPasswordGroup');
            const loginOptionsArea = document.getElementById('loginOptionsArea');
            const brandTitle = document.getElementById('brandTitle');

            // 2. Chuyển sang vai trò Khách Hàng
            btnRoleUser.addEventListener('click', function () {
                currentRole = 'user';
                btnRoleUser.classList.add('active');
                btnRoleAdmin.classList.remove('active');
                brandTitle.textContent = 'TECHZONE';
                authSwitchArea.style.display = 'block';
                updateFormUI();
            });

            // 3. Chuyển sang vai trò Quản Trị Viên
            btnRoleAdmin.addEventListener('click', function () {
                currentRole = 'admin';
                btnRoleAdmin.classList.add('active');
                btnRoleUser.classList.remove('active');
                brandTitle.textContent = 'ADMIN';
                isRegisterMode = false; // Quản trị viên không có đăng ký tự do
                authSwitchArea.style.display = 'none';
                updateFormUI();
            });

            // 4. Chuyển qua lại giữa Đăng Nhập và Đăng Ký (Khách hàng)
            authToggleLink.addEventListener('click', function () {
                isRegisterMode = !isRegisterMode;
                updateFormUI();
            });

            // 5. Cập nhật nhãn và trường nhập dựa theo trạng thái
            function updateFormUI() {
                if (currentRole === 'admin') {
                    // Chế độ Admin
                    accountLabel.innerHTML = 'Tài khoản quản trị <span class="text-danger">*</span>';
                    accountInput.placeholder = 'Nhập tên đăng nhập hoặc email quản trị...';
                    fullNameGroup.style.display = 'none';
                    confirmPasswordGroup.style.display = 'none';
                    loginOptionsArea.style.display = 'flex';
                    submitText.textContent = 'Đăng Nhập Quản Trị';
                    submitIcon.className = 'bi bi-shield-check';
                } else {
                    // Chế độ Khách hàng
                    if (isRegisterMode) {
                        // Khách hàng -> Đăng ký
                        accountLabel.innerHTML = 'Email / Số điện thoại đăng ký <span class="text-danger">*</span>';
                        accountInput.placeholder = 'Nhập email hoặc số điện thoại...';
                        fullNameGroup.style.display = 'block';
                        confirmPasswordGroup.style.display = 'block';
                        loginOptionsArea.style.display = 'none';
                        submitText.textContent = 'Tạo Tài Khoản Khách Hàng';
                        submitIcon.className = 'bi bi-person-plus';
                        switchPromptText.textContent = 'Đã có tài khoản?';
                        authToggleLink.textContent = 'Đăng nhập ngay';
                    } else {
                        // Khách hàng -> Đăng nhập
                        accountLabel.innerHTML = 'Email hoặc Số điện thoại <span class="text-danger">*</span>';
                        accountInput.placeholder = 'Nhập email hoặc số điện thoại mua hàng...';
                        fullNameGroup.style.display = 'none';
                        confirmPasswordGroup.style.display = 'none';
                        loginOptionsArea.style.display = 'flex';
                        submitText.textContent = 'Đăng Nhập Khách Hàng';
                        submitIcon.className = 'bi bi-box-arrow-in-right';
                        switchPromptText.textContent = 'Chưa có tài khoản TechZone?';
                        authToggleLink.textContent = 'Đăng ký thành viên ngay';
                    }
                }
            }

            // 6. Toggle Hiện / Ẩn Mật Khẩu
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');

            togglePasswordBtn.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePasswordIcon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    togglePasswordIcon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    </script>
    <!-- TechZone Global Notify & Confirm JS -->
    <script src="<?= BASE_URL ?>/js/techzone-notify.js"></script>
</body>
</html>
