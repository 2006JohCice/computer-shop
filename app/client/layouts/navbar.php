<?php
// app/views/client/layouts/navbar.php
$activeCategory = $_GET['dm'] ?? '';
$searchKeyword = htmlspecialchars($_GET['q'] ?? '');

// Danh mục linh hoạt (hỗ trợ bất kỳ số lượng danh mục nào)
$allCategories = [
    ['slug' => 'gaming', 'name' => 'Laptop Gaming & AI', 'icon' => 'bi-controller', 'badge' => 'HOT', 'badgeClass' => 'bg-danger'],
    ['slug' => 'van-phong', 'name' => 'Laptop Văn Phòng & Học Tập', 'icon' => 'bi-briefcase', 'badge' => '', 'badgeClass' => ''],
    ['slug' => 'do-hoa', 'name' => 'Laptop Đồ Họa & Render 3D', 'icon' => 'bi-palette', 'badge' => 'OLED', 'badgeClass' => 'bg-primary'],
    ['slug' => 'doanh-nhan', 'name' => 'Laptop Doanh Nhân Cao Cấp', 'icon' => 'bi-laptop', 'badge' => 'VIP', 'badgeClass' => 'bg-warning text-dark'],
    ['slug' => 'pc-gaming', 'name' => 'Dàn Máy PC Gaming & Đồ Họa', 'icon' => 'bi-pc-display', 'badge' => 'GIẢM 25%', 'badgeClass' => 'bg-success'],
    ['slug' => 'linh-kien', 'name' => 'Linh Kiện: CPU, VGA, RAM, SSD', 'icon' => 'bi-cpu', 'badge' => '', 'badgeClass' => ''],
    ['slug' => 'man-hinh', 'name' => 'Màn Hình Máy Tính 144Hz - 240Hz', 'icon' => 'bi-display', 'badge' => '2K/4K', 'badgeClass' => 'bg-info text-white'],
    ['slug' => 'gaming-gear', 'name' => 'Bàn Phím Cơ, Chuột & Tai Nghe', 'icon' => 'bi-headset', 'badge' => '', 'badgeClass' => '']
];
?>

<!-- MAIN NAVBAR -->
<nav class="tz-client-navbar">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <!-- Logo Brand -->
        <a href="<?= BASE_URL ?>/client/index.php" class="tz-client-brand">
            <div class="tz-brand-icon-box">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <div>
                <div class="tz-brand-text">TECH<span>ZONE</span></div>
                <div class="tz-brand-sub">NEXT-GEN COMPUTING</div>
            </div>
        </a>

        <!-- Header Search Bar with Quick Tags (Học hỏi từ Unnamed) -->
        <div class="tz-header-search-wrap d-none d-lg-block">
            <div class="tz-header-search">
                <form action="<?= BASE_URL ?>/client/index.php" method="GET">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="q" placeholder="Tìm kiếm PC, Laptop, RTX 4070, Core i9, Màn OLED..." value="<?= $searchKeyword ?>">
                    <button type="submit" class="search-submit-btn" title="Tìm kiếm">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
            <div class="tz-quick-tags">
                <span class="fw-semibold">Gợi ý:</span>
                <a href="<?= BASE_URL ?>/client/index.php?q=RTX+4060">RTX 4060</a>
                <span>•</span>
                <a href="<?= BASE_URL ?>/client/index.php?q=Core+i7">Core i7</a>
                <span>•</span>
                <a href="<?= BASE_URL ?>/client/index.php?q=M3+Pro">MacBook M3</a>
                <span>•</span>
                <a href="<?= BASE_URL ?>/client/index.php?q=OLED">Màn OLED</a>
                <span>•</span>
                <a href="<?= BASE_URL ?>/client/index.php?q=ThinkPad">ThinkPad</a>
            </div>
        </div>

        <!-- Header Actions: Hotline, Tra cứu, Giỏ hàng, Tài khoản -->
        <div class="d-flex align-items-center gap-3">
            <!-- Hotline tư vấn -->
            <a href="tel:19008888" class="tz-header-hotline d-none d-xl-flex">
                <div class="tz-hotline-icon">
                    <i class="bi bi-telephone-inbound-fill"></i>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 11px;">Tư vấn mua hàng</small>
                    <strong class="text-dark" style="font-size: 14px;">1900 8888</strong>
                </div>
            </a>

            <!-- Nút Giỏ Hàng Nổi Bật -->
            <a href="<?= BASE_URL ?>/client/cart.php" class="btn-tz-cart">
                <i class="bi bi-bag"></i>
                <span class="d-none d-sm-inline">Giỏ hàng</span>
                <span class="cart-badge cart-badge-count">1</span>
            </a>

            <!-- Tài khoản / Đăng nhập -->
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Tài khoản cá nhân">
                <i class="bi bi-person fs-5 text-dark"></i>
            </a>
        </div>
    </div>
</nav>

<!-- NAVIGATION MENU BAR & CATALOG MEGA DROPDOWN (Giao diện rộng rãi, thoáng đãng, KHÔNG thanh lăn) -->
<div class="tz-nav-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Nhóm bên trái: Nút Danh Mục + Menu Các Liên Kết Điều Hướng -->
        <div class="d-flex align-items-center flex-grow-1">
            <!-- Nút Bật Catalog Menu Danh Mục -->
            <div class="tz-catalog-box position-relative flex-shrink-0">
                <div class="dropdown">
                    <button class="tz-catalog-btn dropdown-toggle" type="button" id="catalogDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill"></i>
                        <span>DANH MỤC SẢN PHẨM</span>
                    </button>
                    <!-- Mega Menu Dropdown chứa tất cả danh mục -->
                    <ul class="dropdown-menu shadow-lg border-0 p-2" aria-labelledby="catalogDropdownBtn" style="border-radius: 0 0 12px 12px; min-width: 250px; max-height: 480px; overflow-y: auto;">
                        <?php foreach ($allCategories as $c): ?>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded d-flex align-items-center justify-content-between" href="<?= BASE_URL ?>/client/index.php?dm=<?= $c['slug'] ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi <?= $c['icon'] ?> text-primary fs-6" style="width: 20px;"></i>
                                    <span class="fw-semibold text-dark" style="font-size: 13px;"><?= $c['name'] ?></span>
                                </div>
                                <?php if (!empty($c['badge'])): ?>
                                    <span class="badge <?= $c['badgeClass'] ?>" style="font-size: 10px;"><?= $c['badge'] ?></span>
                                <?php else: ?>
                                    <i class="bi bi-chevron-right text-muted" style="font-size: 11px;"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 text-primary fw-bold small text-center" href="<?= BASE_URL ?>/client/index.php">
                                <i class="bi bi-grid me-1"></i>Xem tất cả sản phẩm
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Các Liên Kết Điều Hướng Chính (Rộng rãi, trải dài không bị ép hẹp, KHÔNG thanh lăn) -->
            <div class="tz-nav-links-wrap ps-3 d-none d-md-block">
                <ul class="tz-nav-links">
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/index.php" class="<?= empty($activeCategory) ? 'active' : '' ?>">
                            <i class="bi bi-house"></i>Trang chủ
                        </a>
                    </li>
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/index.php?q=sale">
                            <i class="bi bi-fire text-danger"></i>Khuyến mãi Hot
                            <span class="nav-badge-hot">SALE</span>
                        </a>
                    </li>
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/index.php?dm=gaming" class="<?= $activeCategory === 'gaming' ? 'active' : '' ?>">
                            <i class="bi bi-controller text-danger"></i>Laptop Gaming
                        </a>
                    </li>
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/index.php?dm=van-phong" class="<?= $activeCategory === 'van-phong' ? 'active' : '' ?>">
                            <i class="bi bi-briefcase text-primary"></i>Laptop Văn Phòng
                        </a>
                    </li>
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/index.php?dm=do-hoa" class="<?= $activeCategory === 'do-hoa' ? 'active' : '' ?>">
                            <i class="bi bi-palette text-warning"></i>Laptop Đồ Họa
                        </a>
                    </li>
                    <li class="tz-nav-item">
                        <a href="<?= BASE_URL ?>/client/tra-cuu.php">
                            <i class="bi bi-shield-check text-success"></i>Tra cứu bảo hành
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Nút phụ Admin Portal bên phải -->
        <div class="d-none d-xl-flex align-items-center gap-2 flex-shrink-0">
            <a href="/computer-shop/app/admin/dashboard/dashboard.php" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 12px; font-weight: 600;">
                <i class="bi bi-speedometer2 me-1"></i>Vào Admin Portal
            </a>
        </div>
    </div>
</div>
