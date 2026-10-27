<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - TechZone</title>
    <!-- Use correct path according to directory structure -->
    <link rel="stylesheet" href="../css/user/signup.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="signup-container">
        <!-- Left Panel -->
        <div class="signup-left">
            <div class="branding">
                <div class="logo-box">
                    <i class="fa-solid fa-cube logo-icon"></i>
                </div>
                <div class="brand-text">
                    <span class="brand-name">TECHZONE</span>
                    <span class="brand-sub">NEXT-GEN COMPUTING</span>
                </div>
            </div>
            
            <div class="left-content">
                <h1>Tham gia cùng TECHZONE.</h1>
                <div class="image-wrapper">
                    <!-- Using a gamer/PC setup image similar to the mockup -->
                    <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=800" alt="TechZone Setup" class="tech-image">
                </div>
            </div>
            
            <div class="copyright">
                &copy; 2026 TECHZONE VIETNAM
            </div>
        </div>

        <!-- Right Panel -->
        <div class="signup-right">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Tạo tài khoản</h2>
                </div>
                
                <form action="sloveSignup.php" method="POST" class="signup-form">
                    <div class="input-group">
                        <label for="username">TÊN ĐĂNG NHẬP <span class="required">*</span></label>
                        <div class="input-field">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">EMAIL <span class="required">*</span></label>
                        <div class="input-field">
                            <i class="fa-solid fa-at input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">MẬT KHẨU <span class="required">*</span></label>
                        <div class="input-field">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Tạo mật khẩu" required>
                            <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        
                        <div class="password-strength">
                            <div class="strength-header">
                                <span>Độ an toàn mật khẩu:</span>
                                <span class="strength-status">Chưa nhập</span>
                            </div>
                            <div class="strength-criteria">
                                <span class="criterion default"><i class="fa-solid fa-check"></i> Ít nhất 8 ký tự</span>
                                <span class="criterion default"><i class="fa-solid fa-check"></i> Có chữ & số</span>
                                <span class="criterion default"><i class="fa-solid fa-check"></i> Ký tự đặc biệt</span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirm-password">XÁC NHẬN MẬT KHẨU <span class="required">*</span></label>
                        <div class="input-field">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Nhập lại mật khẩu" required>
                            <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    

                    
                    <button type="submit" class="btn-primary">Đăng ký</button>
                    

                </form>
                
                <div class="login-prompt">
                    Đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for toggle password visibility -->
    <script>
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
