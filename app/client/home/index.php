<?php
// app/views/client/home/index.php
$pageTitle = "Trang Chủ - Thế Giới Laptop & Next-Gen Computing";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

?>

<div class="container pb-5">
    <!--  1. HERO SECTION WITH CATEGORY SIDEBAR  -->
    <div class="tz-hero-container">
        <div class="row g-3 align-items-stretch">
            <!-- Cột Trái: Category Sidebar linh hoạt (Học hỏi kiến trúc từ Unnamed) -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="tz-category-sidebar">
                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small text-uppercase"><i class="bi bi-grid-fill text-primary me-2"></i>Danh mục máy tính</span>
                        <span class="badge bg-primary rounded-pill"></span>
                    </div>
                    <ul class="tz-cat-list">
                       
                        <li class="tz-cat-item">
                            <a href="<?= BASE_URL ?>/client/index.php?dm=" class="">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi  cat-icon"></i>
                                    <span></span>
                                </div>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"></span>
                            </a>
                        </li>
                       
                    </ul>
                </div>
            </div>

            <!-- Cột Phải: Big Banner Khuyến Mãi AI 2026 -->
            <div class="col-lg-9 col-12">
                <div class="tz-hero-banner-main">
                    <div class="tz-hero-badge">
                        <i class="bi bi-stars"></i>CÔNG NGHỆ THẾ HỆ MỚI 2026
                    </div>
                    <h1 class="tz-hero-title">
                        Đỉnh Cao Laptop AI &<br>Hiệu Năng Gaming Vượt Trội
                    </h1>
                    <p class="tz-hero-desc">
                        Trải nghiệm sức mạnh xử lý đỉnh cao với vi xử lý AI NPU tích hợp, card đồ họa RTX 40-Series và chế độ kích hoạt bảo hành điện tử theo Serial/IMEI tức thì.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#productListArea" class="btn-tz-cta">
                            <i class="bi bi-bag-fill"></i>Khám Phá Danh Sách Máy
                        </a>
                        <a href="<?= BASE_URL ?>/client/tra-cuu.php" class="btn btn-outline-light px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                            <i class="bi bi-shield-check"></i>Tra cứu bảo hành Serial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  2. 4 COMMITMENT BOXES (Học hỏi từ Unnamed)  -->
    <div class="tz-commit-strip">
        <div class="tz-commit-box">
            <div class="tz-commit-icon bg-primary-subtle text-primary">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="fw-bold text-dark small">100% Hàng Chính Hãng</div>
                <small class="text-muted" style="font-size: 11.5px;">Bảo hành theo Serial/IMEI gốc</small>
            </div>
        </div>

        <div class="tz-commit-box">
            <div class="tz-commit-icon bg-danger-subtle text-danger">
                <i class="bi bi-truck-front-fill"></i>
            </div>
            <div>
                <div class="fw-bold text-dark small">Giao Hỏa Tốc 2 Giờ</div>
                <small class="text-muted" style="font-size: 11.5px;">Miễn phí nội thành từ 10 triệu</small>
            </div>
        </div>

        <div class="tz-commit-box">
            <div class="tz-commit-icon bg-success-subtle text-success">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div>
                <div class="fw-bold text-dark small">Đổi Mới Trong 30 Ngày</div>
                <small class="text-muted" style="font-size: 11.5px;">Lỗi phần cứng do NSX đổi 1-1</small>
            </div>
        </div>

        <div class="tz-commit-box">
            <div class="tz-commit-icon bg-warning-subtle text-warning">
                <i class="bi bi-credit-card"></i>
            </div>
            <div>
                <div class="fw-bold text-dark small">Trả Góp 0% Lãi Suất</div>
                <small class="text-muted" style="font-size: 11.5px;">Xét duyệt online trong 5 phút</small>
            </div>
        </div>
    </div>

    <!--  3. BRAND STRIP  -->
    <div class="tz-brand-strip">
        <span class="text-secondary small fw-bold text-uppercase"><i class="bi bi-patch-check-fill text-primary me-1"></i>Hãng đối tác:</span>
        
        <div class="tz-brand-item" onclick="location.href='<?= BASE_URL ?>/client/index.php?q='">
            <i class="bi  text-primary"></i>
            <span></span>
        </div>
       
    </div>

    <!--  4. TIÊU ĐỀ LƯỚI SẢN PHẨM  -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4" id="productListArea">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">
                <i class="bi bi-laptop text-primary me-2"></i>Laptop Phân Phối Chính Hãng TechZone
            </h2>
            <p class="text-secondary mb-0 small">
                Hiển thị  mẫu máy chính hãng sẵn hàng tại hệ thống TechZone
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">Sắp xếp:</span>
            <select class="form-select form-select-sm" style="width: 170px;">
                <option selected>Giá: Tốt nhất</option>
                <option>Giá: Từ cao đến thấp</option>
                <option>Mới nhất 2026</option>
                <option>Bán chạy nhất</option>
            </select>
        </div>
    </div>

    <!--  5. LƯỚI SẢN PHẨM LAPTOP (CLICK VÀO ĐỂ MỞ TRANG CHI TIẾT)  -->
    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <!-- Click vào card để nhảy sang trang chi tiết sẩn phẩm: product-detail.php?id=... -->
                <a href="<?= BASE_URL ?>/client/product-detail.php?id=<?= $p['maSP'] ?>" class="tz-product-card">
                    <div class="tz-card-image-box">
                        <span class="tz-card-tag <?= $p['tag'] === 'gaming' ? 'tz-tag-gaming' : ($p['tag'] === 'hot' ? 'tz-tag-hot' : 'tz-tag-office') ?>">
                            <?= $p['tagLabel'] ?>
                        </span>
                        <img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['tenSP']) ?>">
                    </div>

                    <div class="tz-card-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="tz-product-brand"><?= $p['maTH'] ?></span>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"><?= $p['maSP'] ?></span>
                        </div>
                        
                        <h3 class="tz-product-title" title="<?= htmlspecialchars($p['tenSP']) ?>">
                            <?= htmlspecialchars($p['tenSP']) ?>
                        </h3>

                        <!-- Thông số kỹ thuật nhanh -->
                        <div class="tz-spec-chips">
                            <span class="tz-spec-chip"><i class="bi bi-cpu me-1"></i><?= $p['cpu'] ?></span>
                            <span class="tz-spec-chip"><i class="bi bi-memory me-1"></i><?= $p['ram'] ?></span>
                            <span class="tz-spec-chip"><i class="bi bi-gpu-card me-1"></i><?= $p['gpu'] ?></span>
                        </div>

                        <!-- Khung giá & Nút thêm giỏ hàng -->
                        <div class="tz-product-price-box" onclick="event.preventDefault(); event.stopPropagation();">
                            <div>
                                <div class="tz-current-price"><?= number_format($p['giaBan'], 0, ',', '.') ?> ₫</div>
                                <div class="tz-old-price"><?= number_format($p['giaGoc'], 0, ',', '.') ?> ₫</div>
                            </div>
                            
                            <button type="button" class="btn-tz-add-cart" title="Thêm vào giỏ hàng" onclick="CartManager.themSanPham('<?= $p['maSP'] ?>', 1);">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h4 class="text-secondary">Không tìm thấy sản phẩm laptop phù hợp</h4>
                <p class="text-muted small">Vui lòng thử lại với từ khóa khác hoặc bấm nút bên dưới để xem toàn bộ danh mục.</p>
                <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-outline-primary btn-sm">Xem toàn bộ laptop</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
