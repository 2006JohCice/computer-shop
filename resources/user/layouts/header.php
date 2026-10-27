<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechZone - Next-Gen Computing | Máy Tính, Laptop & Gaming Gear Chính Hãng</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../../css/layouts/header.css">
    <link rel="stylesheet" href="../../css/layouts/home.css">
    <link rel="stylesheet" href="../../css/layouts/footer.css">
</head>
<body>

<header class="site-header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container top-bar-inner">
            <div class="top-bar-left">
                <span class="top-bar-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    Giao hàng hỏa tốc trong 2 giờ
                </span>
                <span class="top-divider"></span>
                <span class="top-bar-item">
                    <i class="fa-solid fa-shield-halved"></i>
                    Cam kết chính hãng 100% - Đổi mới 30 ngày
                </span>
                <span class="top-divider"></span>
                <span class="top-bar-item">
                    <i class="fa-solid fa-headset"></i>
                    Hotline CSKH: <strong>1900 8888</strong>
                </span>
            </div>

            <div class="top-bar-right">
                <a href="#" class="top-bar-link">
                    <i class="fa-solid fa-location-dot"></i> Hệ thống 12 Showroom
                </a>
                <span class="top-divider"></span>
                <a href="#" class="top-bar-link">
                    <i class="fa-solid fa-screwdriver-wrench"></i> Tra cứu bảo hành
                </a>
                <span class="top-divider"></span>
                <a href="#" class="top-bar-link">
                    <i class="fa-solid fa-newspaper"></i> Tin tức công nghệ
                </a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container main-header-inner">
            <!-- Brand Logo -->
            <a href="home.php" class="header-brand">
                <div class="brand-icon-box">
                    <i class="fa-solid fa-cube"></i>
                </div>
                <div class="brand-meta">
                    <span class="brand-title">TECH<span>ZONE</span></span>
                    <span class="brand-subtitle">NEXT-GEN COMPUTING</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="header-search">
                <form class="search-form" onsubmit="return false;">
                    <select class="search-category-select" aria-label="Danh mục tìm kiếm">
                        <option value="all">Tất cả</option>
                        <option value="pc">PC Gaming</option>
                        <option value="laptop">Laptop</option>
                        <option value="components">Linh kiện</option>
                        <option value="gear">Gaming Gear</option>
                    </select>
                    <input type="text" class="search-input" placeholder="Tìm kiếm PC, Laptop, RTX 4090, Màn hình 144Hz..." aria-label="Tìm kiếm sản phẩm">
                    <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                <div class="quick-tags">
                    <span>Gợi ý:</span>
                    <a href="#">RTX 4070</a>
                    <a href="#">Core i9-14900K</a>
                    <a href="#">ROG Ally</a>
                    <a href="#">Màn OLED</a>
                    <a href="#">Bàn phím cơ</a>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Hotline Call -->
                <div class="action-item">
                    <div class="action-icon-wrap">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div class="action-text">
                        <span class="action-label">Tư vấn mua hàng</span>
                        <span class="action-value">1900 8888</span>
                    </div>
                </div>

                <!-- Account / Login -->
                <a href="../login.php" class="action-item">
                    <div class="action-icon-wrap">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="action-text">
                        <span class="action-label">Tài khoản</span>
                        <span class="action-value">Đăng nhập</span>
                    </div>
                </a>

                <!-- Cart -->
                <div class="action-item">
                    <div class="action-icon-wrap">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-badge">3</span>
                    </div>
                    <div class="action-text">
                        <span class="action-label">Giỏ hàng</span>
                        <span class="action-value">3 sản phẩm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Bar -->
    <div class="nav-bar">
        <div class="container nav-bar-inner">
            <!-- Catalog Dropdown Button -->
            <div class="catalog-menu">
                <button type="button" class="catalog-toggle-btn">
                    <i class="fa-solid fa-bars"></i>
                    <span>DANH MỤC SẢN PHẨM</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>

            <!-- Primary Nav -->
            <nav class="main-nav">
                <a href="home.php" class="nav-link active">
                    <i class="fa-solid fa-house"></i> Trang chủ
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-fire"></i> Khuyến mãi Hot
                    <span class="nav-badge-hot">SALE</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-desktop"></i> PC Gaming & Đồ họa
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-laptop"></i> Laptop Gaming & AI
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-microchip"></i> Linh kiện máy tính
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-gamepad"></i> Gaming Gear
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-newspaper"></i> Tin công nghệ
                </a>
            </nav>

            <!-- Nav Extra (Build PC Button) -->
            <div class="nav-extra">
                <a href="#" class="build-pc-btn">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span>XÂY DỰNG CẤU HÌNH</span>
                </a>
            </div>
        </div>
    </div>
</header>
