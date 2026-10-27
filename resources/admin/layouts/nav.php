<?php
// Nav template

$current_path = $_SERVER['PHP_SELF'];
$is_products = (strpos($current_path, 'products/') !== false);
$is_categories = (strpos($current_path, 'categories/') !== false);
$is_brands = (strpos($current_path, 'brands/') !== false);
$is_recycle_bin = (strpos($current_path, 'recycle-bin.php') !== false);
$is_home = (strpos($current_path, 'home.php') !== false);

?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-box-seam text-primary fs-3"></i>
        </div>
        <div class="brand-text">
            <div class="brand-name fw-bold">TECHZONE</div>
            <div class="brand-subtext text-muted small">Quản trị hệ thống</div>
        </div>
    </div>

    <!-- <div class="sidebar-user mx-3 mb-3 p-2 rounded d-flex align-items-center bg-light">
        <div class="user-avatar bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="user-info ms-2">
            <div class="user-name fw-semibold small">Trần Minh Quang</div>
            <div class="user-role text-muted" style="font-size: 0.75rem;">Quản trị viên</div>
        </div>
        <i class="bi bi-chevron-down ms-auto text-muted" style="font-size: 0.8rem;"></i>
    </div> -->

    <div class="sidebar-menu">
        <div class="menu-section">
            <div class="menu-title">TỔNG QUAN</div>
            <a href="/computer-shop/resources/admin/home.php" class="menu-item <?= $is_home ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i>
                <span>Tổng quan</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">QUẢN LÝ KINH DOANH</div>
            <a href="/computer-shop/resources/admin/products/products.php" class="menu-item <?= $is_products ? 'active' : '' ?>">
                <i class="bi bi-box-fill"></i>
                <span>Sản phẩm</span>
            </a>
            <a href="/computer-shop/resources/admin/categories/categories.php" class="menu-item <?= $is_categories ? 'active' : '' ?>">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Danh mục</span>
            </a>
            <a href="/computer-shop/resources/admin/brands/brands.php" class="menu-item <?= $is_brands ? 'active' : '' ?>">
                <i class="bi bi-award-fill"></i>
                <span>Thương hiệu</span>
            </a>
            <a href="/computer-shop/resources/admin/recycle-bin.php" class="menu-item <?= $is_recycle_bin ? 'active' : '' ?>">
                <i class="bi bi-trash"></i>
                <span>Thùng rác</span>
            </a>
        </div>

        <!-- <div class="menu-section">
            <div class="menu-title">MARKETING</div>
            <a href="#" class="menu-item">
                <i class="bi bi-megaphone"></i>
                <span>Khuyến mãi</span>
            </a>
            <a href="#" class="menu-item">
                <i class="bi bi-ticket-perforated"></i>
                <span>Mã giảm giá</span>
            </a>
            <a href="#" class="menu-item">
                <i class="bi bi-image"></i>
                <span>Banner</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">HỆ THỐNG</div>
            <a href="#" class="menu-item">
                <i class="bi bi-person-gear"></i>
                <span>Người dùng</span>
            </a>
            <a href="#" class="menu-item">
                <i class="bi bi-shield-check"></i>
                <span>Vai trò & Phân quyền</span>
            </a>
            <a href="#" class="menu-item">
                <i class="bi bi-clock-history"></i>
                <span>Nhật ký hoạt động</span>
            </a>
            <a href="#" class="menu-item">
                <i class="bi bi-gear"></i>
                <span>Cài đặt</span>
            </a>
        </div> -->
    </div>
    
    <!-- <div class="sidebar-footer mt-auto p-3">
        <div class="version-badge d-flex align-items-center justify-content-between p-2 rounded bg-light">
            <div class="d-flex align-items-center">
                <span class="status-dot bg-info rounded-circle me-2" style="width: 8px; height: 8px;"></span>
                <span class="small text-muted">v2.6 Cloud SaaS</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary">VN</span>
        </div>
    </div> -->
</aside>
