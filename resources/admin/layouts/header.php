<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TECHZONE Admin Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/bootstrap-shim.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/app.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/nav.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/header.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/product.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/category.css">
    <link rel="stylesheet" href="/computer-shop/resources/css/admin/brand.css">
    <!-- Style thanh cuộn siêu mỏng đẹp tinh tế -->
    <style>
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
            transition: background-color 0.2s ease;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
    </style>
</head>
<script>
function toggleUserDropdown() {
    document.getElementById("userDropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.closest('.header-user')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            if (dropdowns[i].classList.contains('show')) {
                dropdowns[i].classList.remove('show');
            }
        }
    }
}
</script>

<style>
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #ffffff;
    min-width: 200px;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    right: 0;
    top: calc(100% + 10px);
    z-index: 1000;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s ease;
}
.dropdown-content.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}
.dropdown-content a {
    color: #475569;
    padding: 12px 20px;
    text-decoration: none;
    display: flex;
    align-items: center;
    font-weight: 500;
    font-size: 0.9rem;
    transition: background 0.2s;
}
.dropdown-content a i {
    margin-right: 10px;
    font-size: 1.1rem;
    color: #94a3b8;
}
.dropdown-content a:hover {
    background-color: #f8fafc;
    color: #0f172a;
}
.dropdown-content a:hover i {
    color: #3b82f6;
}
.dropdown-divider {
    height: 1px;
    background-color: #f1f5f9;
    margin: 4px 0;
}
</style>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/nav.php'; ?>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header d-flex align-items-center justify-content-between px-4 bg-white border-bottom shadow-sm">
                <div class="header-left d-flex align-items-center">
                    <!-- Optional toggler for mobile -->
                    <button class="btn btn-light d-md-none me-3"><i class="bi bi-list"></i></button>
                    
                    <div class="breadcrumb-area d-none d-md-flex align-items-center me-4">
                        <div class="fw-semibold me-2">Quản trị</div>
                        <i class="bi bi-chevron-right text-muted small me-2"></i>
                        <div class="text-muted small">Hệ thống 2026</div>
                    </div>

                   
                </div>

                <div class="header-right d-flex align-items-center gap-3">
                   
                    <button class="btn btn-light rounded-circle icon-btn">
                        <i class="bi bi-moon"></i>
                    </button>

                  

                    <div class="vr mx-1 text-muted"></div>

                 

                    <div class="dropdown header-user cursor-pointer" onclick="toggleUserDropdown()">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="user-info d-none d-md-block me-2">
                                <div class="fw-semibold small text-dark">Đang Tải</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Admin</div>
                            </div>
                            <i class="bi bi-chevron-down text-muted small d-none d-md-block"></i>
                        </div>
                        
                        <div id="userDropdown" class="dropdown-content">
                            <div class="px-4 py-3 d-md-none border-bottom mb-1">
                                <div class="fw-bold text-dark">Đang Tải</div>
                                <div class="text-muted small">Admin</div>
                            </div>
                            <a href="/computer-shop/resources/admin/profile.php">
                                <i class="bi bi-person-circle"></i> Tài khoản của tôi
                            </a>
                            <a href="#">
                                <i class="bi bi-gear"></i> Cài đặt hệ thống
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/computer-shop/resources/auth/login.php" class="text-danger">
                                <i class="bi bi-box-arrow-right text-danger"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="admin-content p-4">
