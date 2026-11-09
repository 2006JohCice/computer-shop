<?php
// app/views/admin/layouts/sidebar.php
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
function isActive($keyword, $currentUri) {
    return strpos($currentUri, $keyword) !== false ? 'active' : '';
}

// Đường dẫn gốc trực tiếp tới các trang Admin View trong dự án
$adminUrl = '/computer-shop/app/admin';
?>
<!-- Sidebar Admin -->
<aside class="tz-sidebar">
    <div class="tz-sidebar-brand">
        <div class="tz-brand-logo">
            <i class="bi bi-cpu-fill"></i>
        </div>
        <div>
            <h1 class="tz-brand-title">TECHZONE</h1>
            <span class="tz-brand-sub">Quản trị hệ thống</span>
        </div>
    </div>

    <div class="py-2 tz-sidebar-menu">
        <!-- Nhóm: Tổng quan -->
        <div class="tz-menu-group">Tổng quan</div>
        <a href="<?= $adminUrl ?>/dashboard/dashboard.php" class="tz-nav-link <?= isActive('dashboard', $currentUri) ?: (strpos($currentUri, 'admin') === false ? 'active' : '') ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Tổng quan</span>
        </a>

        <!-- Nhóm: Sản phẩm & Kho -->
        <div class="tz-menu-group">Quản lý Kho & Laptop</div>
        <a href="<?= $adminUrl ?>/san-pham/san-pham.php" class="tz-nav-link <?= isActive('san-pham', $currentUri) ?>">
            <i class="bi bi-laptop"></i>
            <span>Sản phẩm Laptop</span>
        </a>
        <a href="<?= $adminUrl ?>/serial/serial.php" class="tz-nav-link <?= isActive('serial', $currentUri) ?>">
            <i class="bi bi-upc-scan"></i>
            <span>Quản lý Serial / IMEI</span>
        </a>
        <a href="<?= $adminUrl ?>/danh-muc/danh-muc.php" class="tz-nav-link <?= isActive('danh-muc', $currentUri) ?>">
            <i class="bi bi-folder2-open"></i>
            <span>Danh mục máy</span>
        </a>
        <a href="<?= $adminUrl ?>/thuong-hieu/thuong-hieu.php" class="tz-nav-link <?= isActive('thuong-hieu', $currentUri) ?>">
            <i class="bi bi-patch-check"></i>
            <span>Thương hiệu</span>
        </a>

        <!-- Nhóm: Nhập kho & Đối tác -->
        <div class="tz-menu-group">Nhập kho & Cung ứng</div>
        <a href="<?= $adminUrl ?>/phieu-nhap/phieu-nhap.php" class="tz-nav-link <?= isActive('phieu-nhap', $currentUri) ?>">
            <i class="bi bi-box-arrow-in-down"></i>
            <span>Phiếu nhập kho</span>
        </a>
        <a href="<?= $adminUrl ?>/nha-cung-cap/nha-cung-cap.php" class="tz-nav-link <?= isActive('nha-cung-cap', $currentUri) ?>">
            <i class="bi bi-truck"></i>
            <span>Nhà cung cấp</span>
        </a>

        <!-- Nhóm: Bán hàng -->
        <div class="tz-menu-group">Kinh doanh & Đơn hàng</div>
        <a href="<?= $adminUrl ?>/don-hang/don-hang.php" class="tz-nav-link <?= isActive('don-hang', $currentUri) ?>">
            <i class="bi bi-bag-check"></i>
            <span>Đơn bán hàng</span>
        </a>
        <a href="<?= $adminUrl ?>/hoa-don/hoa-don.php" class="tz-nav-link <?= isActive('hoa-don', $currentUri) ?>">
            <i class="bi bi-receipt"></i>
            <span>Hóa đơn xuất</span>
        </a>
        <a href="<?= $adminUrl ?>/khuyen-mai/khuyen-mai.php" class="tz-nav-link <?= isActive('khuyen-mai', $currentUri) ?>">
            <i class="bi bi-percent"></i>
            <span>Khuyến mãi Voucher</span>
        </a>

        <!-- Nhóm: Khách hàng & Bảo hành -->
        <div class="tz-menu-group">Khách hàng & Hậu mãi</div>
        <a href="<?= $adminUrl ?>/khach-hang/khach-hang.php" class="tz-nav-link <?= isActive('khach-hang', $currentUri) ?>">
            <i class="bi bi-people"></i>
            <span>Khách hàng & VIP</span>
        </a>
        <a href="<?= $adminUrl ?>/bao-hanh/bao-hanh.php" class="tz-nav-link <?= isActive('bao-hanh', $currentUri) ?>">
            <i class="bi bi-shield-check"></i>
            <span>Phiếu bảo hành</span>
        </a>

        <!-- Nhóm: Tài khoản & Hệ thống -->
        <div class="tz-menu-group">Hệ thống & Phân quyền</div>
        <a href="<?= $adminUrl ?>/tai-khoan/tai-khoan.php" class="tz-nav-link <?= isActive('tai-khoan', $currentUri) ?>">
            <i class="bi bi-person-badge"></i>
            <span>Tài khoản hệ thống</span>
        </a>
        <a href="/computer-shop/public/client/index.php" target="_blank" class="tz-nav-link text-info">
            <i class="bi bi-shop text-info"></i>
            <span class="text-info">Xem Cửa hàng (User)</span>
        </a>

        <!-- Đăng xuất -->
        <div class="tz-menu-group">Đăng xuất</div>
        <a href="/computer-shop/app/auth/logout.php" class="tz-nav-link text-danger">
            <i class="bi bi-box-arrow-right text-danger"></i>
            <span class="text-danger">Đăng xuất</span>
        </a>
    </div>
</aside>
