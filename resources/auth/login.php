<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - TECHZONE</title>
    <!-- CSS dùng chung -->
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/login.css">
    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Tab chuyển đổi vai trò (Khách hàng / Quản trị viên) */
        .role-switcher {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 20px;
            gap: 6px;
            width: 100%;
            max-width: 360px;
        }
        .role-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .role-btn.active {
            background: #ffffff;
            color: #1d4ed8;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .register-switch {
            margin-top: 16px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
        }
        .register-switch a {
            color: #1d4ed8;
            font-weight: 600;
            text-decoration: none;
        }
        .register-switch a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-split">
        <!-- Phần Bên Trái: Form Đăng Nhập / Đăng Ký Chung -->
        <div class="login-left">
            <div class="login-form-container">
                <div class="brand-header">
                    <i class="bi bi-box-seam text-primary" style="font-size: 36px; color: #1d4ed8;"></i>
                    <h1 id="brandRoleTitle" style="margin-top: 8px; font-weight: 800; font-size: 26px;">TECHZONE</h1>
                    <p style="color: #64748b; font-size: 13.5px; margin-top: 4px;">Hệ Thống Mua Sắm & Quản Trị Laptop</p>
                </div>

                <!-- Tab chọn vai trò trực quan (Khách hàng / Quản trị viên) -->
                <div class="role-switcher">
                    <button type="button" class="role-btn active" id="btnRoleCustomer">
                        <i class="bi bi-person"></i> Khách Hàng
                    </button>
                    <button type="button" class="role-btn" id="btnRoleAdmin">
                        <i class="bi bi-shield-lock"></i> Quản Trị Viên
                    </button>
                </div>

                <!-- Form Giao Diện Thuần (Không logic, không session) -->
                <form action="#" method="POST" id="authForm">
                    <!-- Ô Nhập Họ Tên (Chỉ hiện khi bấm Đăng ký) -->
                    <div class="form-group" id="fullNameGroup" style="display: none; margin-bottom: 14px;">
                        <input type="text" id="fullname" name="fullname" class="form-input" placeholder="Họ và tên của bạn...">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <input type="text" id="username" name="username" class="form-input" placeholder="Email hoặc Tên đăng nhập..." required autofocus>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Mật khẩu..." required>
                    </div>

                    <!-- Ô Nhập Lại Mật Khẩu (Chỉ hiện khi bấm Đăng ký) -->
                    <div class="form-group" id="confirmPassGroup" style="display: none; margin-bottom: 14px;">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Nhập lại mật khẩu...">
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        Đăng nhập Khách Hàng
                    </button>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;" id="loginOptions">
                        <label style="font-size: 12.5px; color: #64748b; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="checkbox" name="remember"> Ghi nhớ
                        </label>
                        <a href="#" class="forgot-password" style="font-size: 12.5px; margin: 0;">Quên mật khẩu?</a>
                    </div>
                </form>

                <div class="register-switch" id="registerSwitchBox">
                    <span id="promptText">Chưa có tài khoản?</span> 
                    <a href="javascript:void(0)" id="toggleRegisterBtn">Đăng ký thành viên</a>
                </div>
            </div>
        </div>

        <!-- Phần Bên Phải: Graphic & Text Dashboard (Được vẽ bằng CSS) -->
        <div class="login-right">
            <div class="glow glow-1"></div>
            <div class="glow glow-2"></div>

            <div class="dashboard-graphic">
                <div class="float-card fc-tl">
                    <div class="ui-line" style="width: 40px; margin-bottom: 12px;"></div>
                    <div class="ui-circle" style="width: 40px; height: 40px;"></div>
                </div>

                <div class="float-card fc-r">
                    <div class="ui-text-p">Statistics</div>
                    <div class="ui-text-h">570k</div>
                    <div class="ui-line" style="width: 100%; margin-top: 12px;"></div>
                    <div class="ui-line" style="width: 80%; margin-top: 8px;"></div>
                    <div class="ui-line" style="width: 90%; margin-top: 8px;"></div>
                    <div style="display: flex; gap: 8px; margin-top: 15px;">
                        <div class="ui-avatar"></div>
                        <div class="ui-avatar"></div>
                        <div class="ui-avatar"></div>
                    </div>
                </div>

                <div class="float-card fc-b">
                    <div class="ui-circle"></div>
                    <div style="flex: 1;">
                        <div class="ui-line" style="width: 80%; margin-bottom: 6px;"></div>
                        <div class="ui-line" style="width: 50%;"></div>
                    </div>
                </div>

                <div class="float-card fc-main">
                    <div>
                        <div style="display: flex; justify-content: space-between;">
                            <div class="ui-line" style="width: 60px;"></div>
                            <div class="ui-line" style="width: 20px;"></div>
                        </div>
                        <div class="ui-text-h">69,700</div>
                    </div>
                    
                    <div class="chart-bars">
                        <div class="bar" style="height: 30%;"></div>
                        <div class="bar" style="height: 50%;"></div>
                        <div class="bar" style="height: 80%;"></div>
                        <div class="bar" style="height: 40%;"></div>
                        <div class="bar" style="height: 100%;"></div>
                        <div class="bar" style="height: 60%;"></div>
                        <div class="bar" style="height: 90%;"></div>
                    </div>
                    
                    <div class="ui-button"></div>
                </div>
            </div>

            <div class="hero-content">
                <h2>Fast, Efficient and Productive</h2>
                <p>Nền tảng quản lý cửa hàng laptop thông minh và trải nghiệm mua sắm công nghệ đỉnh cao.</p>
            </div>
        </div>
    </div>

    <!-- Script tương tác giao diện thuần (Không logic) -->
    <script>
        const btnRoleCustomer = document.getElementById('btnRoleCustomer');
        const btnRoleAdmin = document.getElementById('btnRoleAdmin');
        const brandRoleTitle = document.getElementById('brandRoleTitle');
        const submitBtn = document.getElementById('submitBtn');
        const registerSwitchBox = document.getElementById('registerSwitchBox');
        const toggleRegisterBtn = document.getElementById('toggleRegisterBtn');
        const promptText = document.getElementById('promptText');
        const fullNameGroup = document.getElementById('fullNameGroup');
        const confirmPassGroup = document.getElementById('confirmPassGroup');
        const usernameInput = document.getElementById('username');

        let currentRole = 'customer';
        let isRegister = false;

        btnRoleCustomer.addEventListener('click', () => {
            currentRole = 'customer';
            btnRoleCustomer.classList.add('active');
            btnRoleAdmin.classList.remove('active');
            brandRoleTitle.textContent = 'TECHZONE';
            registerSwitchBox.style.display = 'block';
            updateUI();
        });

        btnRoleAdmin.addEventListener('click', () => {
            currentRole = 'admin';
            btnRoleAdmin.classList.add('active');
            btnRoleCustomer.classList.remove('active');
            brandRoleTitle.textContent = 'ADMIN';
            isRegister = false;
            registerSwitchBox.style.display = 'none';
            updateUI();
        });

        toggleRegisterBtn.addEventListener('click', () => {
            isRegister = !isRegister;
            updateUI();
        });

        function updateUI() {
            if (currentRole === 'admin') {
                usernameInput.placeholder = 'Tài khoản quản trị (Admin)...';
                submitBtn.textContent = 'Đăng nhập Quản Trị';
                fullNameGroup.style.display = 'none';
                confirmPassGroup.style.display = 'none';
            } else {
                if (isRegister) {
                    usernameInput.placeholder = 'Email đăng ký...';
                    submitBtn.textContent = 'Tạo Tài Khoản Khách Hàng';
                    fullNameGroup.style.display = 'block';
                    confirmPassGroup.style.display = 'block';
                    promptText.textContent = 'Đã có tài khoản?';
                    toggleRegisterBtn.textContent = 'Đăng nhập ngay';
                } else {
                    usernameInput.placeholder = 'Email hoặc Tên đăng nhập...';
                    submitBtn.textContent = 'Đăng nhập Khách Hàng';
                    fullNameGroup.style.display = 'none';
                    confirmPassGroup.style.display = 'none';
                    promptText.textContent = 'Chưa có tài khoản?';
                    toggleRegisterBtn.textContent = 'Đăng ký thành viên';
                }
            }
        }
    </script>
</body>
</html>
