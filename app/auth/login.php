<?php
session_start();
/**
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: app/auth/login.php
 */

// 1. Cấu hình 
if (!defined('APP_NAME')) {
    define('APP_NAME', 'TECHZONE - Quản Trị & Mua Sắm Laptop');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/computer-shop/public');
}

// 2. Kết nối Database
if (!isset($conn) || !($conn instanceof mysqli)) {
    if (file_exists(dirname(__DIR__) . '/config/database.php')) {
        include dirname(__DIR__) . '/config/database.php';
    }
    // Đảm bảo các biến $servername, $username, $password, $dbname đã có trong file database.php
    $conn = new mysqli($servername ?? 'localhost', $username ?? 'root', $password ?? '', $dbname ?? 'techzone_db');
    if ($conn->connect_error) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $conn->connect_error);
    }
}

// 3. Khởi tạo biến trạng thái
$login_error = '';
$register_error = '';
$register_success = '';
$active_tab = (isset($_GET['mode']) && $_GET['mode'] === 'register') ? 'register' : 'login';

// 4. Xử lý khi Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    // A. XỬ LÝ ĐĂNG NHẬP
    if ($action === 'login') {
        $active_tab = 'login';
        $user_input = trim($_POST['username'] ?? '');
        $pass_input = trim($_POST['password'] ?? '');

        if (empty($user_input) || empty($pass_input)) {
            $login_error = 'Vui lòng nhập tên tài khoản / email và mật khẩu!';
        } else {
            $userEsc = $conn->real_escape_string($user_input);
            $sql = "SELECT * FROM accounts WHERE (tenTaiKhoan = '$userEsc' OR email = '$userEsc') LIMIT 1";
            $res = $conn->query($sql);

            if ($res && $res->num_rows > 0) {
                $acc = $res->fetch_assoc();
                $status = strtolower(trim($acc['trangThai'] ?? 'active'));

                if ($status !== 'active' && $status !== '1') {
                    $login_error = 'Tài khoản của bạn hiện đang bị khóa hoặc ngừng hoạt động!';
                } else {
                    $passHash = md5($pass_input);
                    // Kiểm tra mật khẩu
                    if ($acc['matKhau'] === $passHash || $acc['matKhau'] === $pass_input) {

                        $role = strtolower(trim($acc['role'] ?? 'user'));

                        // FIX 2: Lưu đầy đủ Session để các trang sau dùng
                        $_SESSION['login'] = true;
                        $_SESSION['maTk'] = $acc['maTk'];
                        $_SESSION['user_name'] = $acc['hoTen'] ?? $user_input;
                        $_SESSION['role'] = $role;

                        $redirectParam = trim($_GET['redirect'] ?? $_POST['redirect'] ?? '');
                        if ($role === 'user') {
                            if ($redirectParam === 'checkout') {
                                header("Location: /computer-shop/public/client/checkout.php");
                                exit();
                            }
                            header("Location: /computer-shop/public/client/index.php");
                            exit();
                        } else {
                            // Chuyển hướng Admin
                            header("Location: /computer-shop/app/admin/dashboard/dashboard.php");
                            exit();
                        }
                    } else {
                        $login_error = 'Mật khẩu truy cập không chính xác!';
                    }
                }
            } else {
                $login_error = 'Tên tài khoản hoặc email không tồn tại trên hệ thống!';
            }
        }
    }

    // B. XỬ LÝ ĐĂNG KÝ
    if ($action === 'register') {
        $active_tab = 'register';
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $regUser = trim($_POST['username'] ?? '');
        $regPass = trim($_POST['password'] ?? '');
        $confirmPass = trim($_POST['confirm_password'] ?? '');

        if (empty($fullname) || empty($email) || empty($regUser) || empty($regPass) || empty($confirmPass)) {
            $register_error = 'Vui lòng điền đầy đủ tất cả các trường thông tin!';
        } elseif ($regPass !== $confirmPass) {
            $register_error = 'Mật khẩu xác nhận không khớp, vui lòng kiểm tra lại!';
        } elseif (strlen($regPass) < 6) {
            $register_error = 'Mật khẩu phải chứa ít nhất 6 ký tự!';
        } else {
            $escFullname = $conn->real_escape_string($fullname);
            $escEmail = $conn->real_escape_string($email);
            $escUser = $conn->real_escape_string($regUser);

            $chkSql = "SELECT maTk FROM accounts WHERE tenTaiKhoan = '$escUser' OR email = '$escEmail' LIMIT 1";
            $chkRes = $conn->query($chkSql);

            if ($chkRes && $chkRes->num_rows > 0) {
                $register_error = 'Tên tài khoản hoặc email đã được sử dụng!';
            } else {
                // Sinh mã TK tự động
                $resMaxTk = $conn->query("SELECT maTk FROM accounts WHERE maTk LIKE 'TK%' ORDER BY LENGTH(maTk) DESC, maTk DESC LIMIT 1");
                $newMaTk = 'TK001';
                if ($resMaxTk && $resMaxTk->num_rows > 0) {
                    $rowMax = $resMaxTk->fetch_assoc();
                    if (preg_match('/^TK(\d+)$/', $rowMax['maTk'], $matches)) {
                        $num = intval($matches[1]) + 1;
                        $newMaTk = 'TK' . str_pad($num, 3, '0', STR_PAD_LEFT);
                    }
                }

                $passMd5 = md5($regPass);
                $sqlInsert = "INSERT INTO accounts (maTk, hoTen, email, tenTaiKhoan, matKhau, ngayTao, role, trangThai) 
                              VALUES ('$newMaTk', '$escFullname', '$escEmail', '$escUser', '$passMd5', NOW(), 'user', 'active')";

                if ($conn->query($sqlInsert)) {
                    // Kiểm tra và insert bảng khachhang
                    $checkKhTable = $conn->query("SHOW TABLES LIKE 'khachhang'");
                    if ($checkKhTable && $checkKhTable->num_rows > 0) {
                        $resMaxKh = $conn->query("SELECT maKH FROM khachhang WHERE maKH LIKE 'KH%' ORDER BY LENGTH(maKH) DESC, maKH DESC LIMIT 1");
                        $newMaKh = 'KH001';
                        if ($resMaxKh && $resMaxKh->num_rows > 0) {
                            $rowKh = $resMaxKh->fetch_assoc();
                            if (preg_match('/^KH(\d+)$/', $rowKh['maKH'], $mKh)) {
                                $numKh = intval($mKh[1]) + 1;
                                $newMaKh = 'KH' . str_pad($numKh, 3, '0', STR_PAD_LEFT);
                            }
                        }
                        $conn->query("INSERT INTO khachhang (maKH, maTk, hoVaTen, email, doiTuongUuTien) 
                                      VALUES ('$newMaKh', '$newMaTk', '$escFullname', '$escEmail', 0)");
                    }

                    // Tự động đăng nhập luôn sau khi đăng ký thành công (Nâng cao trải nghiệm)
                    $_SESSION['login'] = true;
                    $_SESSION['maTk'] = $newMaTk;
                    $_SESSION['user_name'] = $fullname;
                    $_SESSION['role'] = 'user';

                    // FIX 1: Dùng header() thay cho JS
                    header("Location: /computer-shop/app/client/home/index.php");
                    exit();
                } else {
                    $register_error = 'Lỗi tạo tài khoản: ' . $conn->error;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký Hệ Thống | <?= htmlspecialchars(APP_NAME) ?></title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Giao diện đăng nhập dùng chung (Đường dẫn tuyệt đối - Không Shadow) -->
    <link rel="stylesheet" href="/computer-shop/public/css/login.css?v=<?= time() ?>">
</head>

<body>

    <div class="login-wrapper">
        <!-- CỘT TRÁI: FORM ĐĂNG NHẬP / ĐĂNG KÝ DUY NHẤT -->
        <div class="login-left">
            <div class="login-box">

                <!-- 1. Header Đăng Nhập -->
                <header class="login-header">
                    <div class="icon-login">
                        <img class="admin-logo_login" alt="TechZone Logo" src="/computer-shop/public/img/login/logoHunre.jpg" />
                        <h1 class="logo-text">TECHZONE</h1>
                    </div>
                    <p class="subtitle">Hệ Thống Quản Lý Cửa Hàng & Mua Sắm Laptop</p>
                </header>

                <!-- 2. Form ĐĂNG NHẬP -->
                <div id="loginFormContainer" style="<?= $active_tab === 'login' ? 'display: block;' : 'display: none;' ?>">
                    <?php if (!empty($login_error)): ?>
                        <div class="login-alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span><?= htmlspecialchars($login_error) ?></span>
                        </div>
                    <?php endif; ?>

                    <form class="login-form" method="POST" action="">
                        <input type="hidden" name="action" value="login">
                        <?php if (!empty($_GET['redirect'])): ?>
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
                        <?php endif; ?>

                        <!-- Tài khoản hoặc Email -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tên đăng nhập hoặc Email <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-person input-icon-left"></i>
                                <input type="text" name="username" placeholder="Nhập tên đăng nhập hoặc email..." value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="username" />
                            </div>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Mật khẩu truy cập <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-shield-lock input-icon-left"></i>
                                <input type="password" id="loginPasswordInput" name="password" placeholder="Nhập mật khẩu..." required autocomplete="current-password" />
                                <button type="button" class="toggle-password-btn" data-target="loginPasswordInput" title="Hiện/ẩn mật khẩu">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tùy Chọn Ghi Nhớ & Quên Mật Khẩu -->
                        <div class="login-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" />
                                <span>Ghi nhớ đăng nhập</span>
                            </label>
                            <a href="#" class="forgot-password-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Quên Mật Khẩu?</a>
                        </div>

                        <!-- Nút Bấm Đăng Nhập -->
                        <button type="submit" class="login-btn">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Đăng Nhập</span>
                        </button>
                    </form>

                    <!-- Chuyển sang Đăng ký -->
                    <div class="auth-switch-box">
                        <span>Chưa có tài khoản TechZone?</span>
                        <a href="?mode=register" onclick="showRegister(event)" class="auth-switch-link" id="linkToRegister">Đăng ký thành viên ngay</a>
                    </div>
                </div>

                <!-- 4. Form ĐĂNG KÝ -->
                <div id="registerFormContainer" style="<?= $active_tab === 'register' ? 'display: block;' : 'display: none;' ?>">
                    <?php if (!empty($register_error)): ?>
                        <div class="login-alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span><?= htmlspecialchars($register_error) ?></span>
                        </div>
                    <?php endif; ?>

                    <form class="login-form" method="POST" action="">
                        <input type="hidden" name="action" value="register">

                        <!-- Họ và Tên -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Họ và tên <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-card-heading input-icon-left"></i>
                                <input type="text" name="fullname" placeholder="Nhập họ và tên đầy đủ..." value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required autocomplete="name" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Địa chỉ Email <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-envelope input-icon-left"></i>
                                <input type="email" name="email" placeholder="Nhập địa chỉ email..." value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email" />
                            </div>
                        </div>

                        <!-- Tên Tài Khoản -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tên tài khoản (Username) <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-person input-icon-left"></i>
                                <input type="text" name="username" placeholder="Nhập tên tài khoản mong muốn..." value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="username" />
                            </div>
                        </div>

                        <!-- Mật Khẩu -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Mật khẩu truy cập <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-shield-lock input-icon-left"></i>
                                <input type="password" id="regPasswordInput" name="password" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)..." required autocomplete="new-password" />
                                <button type="button" class="toggle-password-btn" data-target="regPasswordInput" title="Hiện/ẩn mật khẩu">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Xác Nhận Mật Khẩu -->
                        <div class="form-group-custom">
                            <label class="form-label-custom">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-check2-circle input-icon-left"></i>
                                <input type="password" id="regConfirmPasswordInput" name="confirm_password" placeholder="Nhập lại mật khẩu vừa nhập..." required autocomplete="new-password" />
                                <button type="button" class="toggle-password-btn" data-target="regConfirmPasswordInput" title="Hiện/ẩn mật khẩu">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Nút Bấm Đăng Ký -->
                        <button type="submit" class="login-btn">
                            <i class="bi bi-person-plus"></i>
                            <span>Tạo Tài Khoản</span>
                        </button>
                    </form>

                    <!-- Chuyển sang Đăng nhập -->
                    <div class="auth-switch-box">
                        <span>Đã có tài khoản TechZone?</span>
                        <a href="?mode=login" onclick="showLogin(event)" class="auth-switch-link" id="linkToLogin">Đăng nhập ngay</a>
                    </div>
                </div>

                <!-- 5. Footer Chân Trang -->
                <div class="login-footer">
                    <p class="mb-0">&copy; <?= date('Y') ?> TECHZONE Laptop Management. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: MOCKUP & GIỚI THIỆU HỆ THỐNG (KHÔNG SHADOW) -->
        <div class="login-right">
            <div class="right-content">
                <!-- Logo Chữ TechZone (Đường dẫn tuyệt đối) -->
                <a href="/computer-shop/public/index.php" class="d-inline-block">
                    <img alt="Brand Logo" src="/computer-shop/public/img/login/Textlogo.png" class="right-brand-logo" />
                </a>

                <!-- Hình ảnh Mockup Dashboard Laptop (Đường dẫn tuyệt đối - Không Shadow) -->
                <div class="mockup-area">
                    <img src="/computer-shop/public/img/login/imgright.png" alt="Laptop Shop Dashboard Mockup" />
                </div>

                <!-- Tiêu đề giới thiệu -->
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

    <!-- SCRIPT CHUYỂN TAB ĐĂNG NHẬP / ĐĂNG KÝ & HIỆN MẬT KHẨU -->
    <script>
        function showLogin(e) {
            if (e && e.preventDefault) e.preventDefault();
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');
            const loginFormContainer = document.getElementById('loginFormContainer');
            const registerFormContainer = document.getElementById('registerFormContainer');

            if (tabLoginBtn) tabLoginBtn.classList.add('active');
            if (tabRegisterBtn) tabRegisterBtn.classList.remove('active');
            if (loginFormContainer) loginFormContainer.style.display = 'block';
            if (registerFormContainer) registerFormContainer.style.display = 'none';
        }

        function showRegister(e) {
            if (e && e.preventDefault) e.preventDefault();
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');
            const loginFormContainer = document.getElementById('loginFormContainer');
            const registerFormContainer = document.getElementById('registerFormContainer');

            if (tabRegisterBtn) tabRegisterBtn.classList.add('active');
            if (tabLoginBtn) tabLoginBtn.classList.remove('active');
            if (loginFormContainer) loginFormContainer.style.display = 'none';
            if (registerFormContainer) registerFormContainer.style.display = 'block';
        }

        window.showLogin = showLogin;
        window.showRegister = showRegister;

        document.addEventListener('DOMContentLoaded', function() {
            // Nút Toggle Hiện / Ẩn Mật khẩu
            document.querySelectorAll('.toggle-password-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (targetInput) {
                        if (targetInput.type === 'password') {
                            targetInput.type = 'text';
                            icon.classList.replace('bi-eye', 'bi-eye-slash');
                        } else {
                            targetInput.type = 'password';
                            icon.classList.replace('bi-eye-slash', 'bi-eye');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>