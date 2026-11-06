<?php
// app/client/home/index.php
$pageTitle = "Trang Chủ - Thế Giới Laptop & Next-Gen Computing";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

include __DIR__ . "/../../config/database.php";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

// Lọc tham số tìm kiếm, danh mục, thương hiệu, sắp xếp
$searchQuery = trim($_GET['q'] ?? '');
$categoryFilter = trim($_GET['dm'] ?? '');
$brandFilter = trim($_GET['brand'] ?? '');
$sortOption = trim($_GET['sort'] ?? '');

// 1. Lấy toàn bộ danh mục từ CSDL
$categories = [];
$totalAllProducts = 0;
$sqlCat = "SELECT * FROM danhmuc WHERE trangThai = '1' ORDER BY maDM ASC";
$resCat = $conn->query($sqlCat);
if ($resCat && $resCat->num_rows > 0) {
    while ($c = $resCat->fetch_assoc()) {
        $maDM = $c['maDM'];
        // Đếm số lượng sản phẩm của danh mục
        $cnt = 0;
        $resCount = $conn->query("SELECT COUNT(*) as cnt FROM sanpham WHERE maDM = '$maDM' AND isDeleted = 0");
        if ($resCount && $rCnt = $resCount->fetch_assoc()) {
            $cnt = (int)$rCnt['cnt'];
        }

        $icon = 'bi-laptop';
        $nameLower = mb_strtolower($c['tenDM'], 'UTF-8');
        if (str_contains($nameLower, 'gaming')) $icon = 'bi-controller';
        elseif (str_contains($nameLower, 'văn phòng') || str_contains($nameLower, 'học tập')) $icon = 'bi-briefcase';
        elseif (str_contains($nameLower, 'doanh nhân')) $icon = 'bi-laptop';
        elseif (str_contains($nameLower, 'đồ họa') || str_contains($nameLower, 'kỹ thuật')) $icon = 'bi-palette';
        elseif (str_contains($nameLower, 'macbook') || str_contains($nameLower, 'apple')) $icon = 'bi-apple';

        $categories[] = [
            'maDM' => $c['maDM'],
            'tenDM' => $c['tenDM'],
            'icon' => $icon,
            'totalSP' => $cnt
        ];
        $totalAllProducts += $cnt;
    }
}

// 2. Lấy danh sách thương hiệu từ CSDL (thương hiệu active hoặc có sản phẩm)
$brands = [];
$sqlBrand = "SELECT * FROM thuonghieu WHERE trangThai = '1'";
$resultSqlBrand = $conn->query($sqlBrand);
if ($resultSqlBrand->num_rows > 0) {
    while ($b = $resultSqlBrand->fetch_assoc()) {
        $brands[] = [
            'maTH' => $b['maTH'],
            'tenTH' => $b['tenTH'],
            'logo' => $b['logo']
        ];
    }
}

// 3. Lấy sản phẩm từ CSDL theo bộ lọc
$sqlProd = "SELECT * FROM sanpham WHERE isDeleted = 0";

$isSaleFilter = (isset($_GET['sale']) && $_GET['sale'] == '1') || strtolower($searchQuery) === 'sale';

if (!empty($searchQuery) && !$isSaleFilter) {
    $sqlProd .= " AND (tenSP LIKE '%$searchQuery%' OR maSP LIKE '%$searchQuery%' OR thongSoKyThuat LIKE '%$searchQuery%')";
}

if (!empty($categoryFilter)) {
    $sqlProd .= " AND maDM = '$categoryFilter'";
}

if (!empty($brandFilter)) {
    $sqlProd .= " AND maTH = '$brandFilter'";
}

switch ($sortOption) {
    case 'price_asc':
        $sqlProd .= " ORDER BY giaBan ASC";
        break;
    case 'price_desc':
        $sqlProd .= " ORDER BY giaBan DESC";
        break;
    case 'newest':
        $sqlProd .= " ORDER BY maSP DESC";
        break;
    default:
        $sqlProd .= " ORDER BY maSP ASC";
        break;
}

$resultProducts = $conn->query($sqlProd);
$totalProductsFound = $resultProducts ? $resultProducts->num_rows : 0;
?>

<div class="container pb-5">
    <!--  1. HERO SECTION WITH CATEGORY SIDEBAR  -->
    <div class="tz-hero-container">
        <div class="row g-3 align-items-stretch">
            <!-- Cột Trái: Category Sidebar linh hoạt -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="tz-category-sidebar">
                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small text-uppercase"><i class="bi bi-grid-fill text-primary me-2"></i>Danh mục máy tính</span>
                        <span class="badge bg-primary rounded-pill"><?= count($categories) ?></span>
                    </div>
                    <ul class="tz-cat-list">
                        <li class="tz-cat-item">
                            <a href="<?= BASE_URL ?>/client/index.php" class="<?= empty($categoryFilter) ? 'active' : '' ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-laptop cat-icon"></i>
                                    <span>Tất Cả Sản Phẩm</span>
                                </div>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"><?= $totalAllProducts ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $c): ?>
                            <li class="tz-cat-item">
                                <a href="<?= BASE_URL ?>/client/index.php?dm=<?= urlencode($c['maDM']) ?>" class="<?= ($categoryFilter === $c['maDM']) ? 'active' : '' ?>">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi <?= $c['icon'] ?> cat-icon"></i>
                                        <span><?= htmlspecialchars($c['tenDM']) ?></span>
                                    </div>
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"><?= $c['totalSP'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
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

    <!--  2. 4 COMMITMENT BOXES  -->
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
        <div class="tz-brand-item <?= empty($brandFilter) ? 'border-primary text-primary' : '' ?>" onclick="location.href='<?= BASE_URL ?>/client/index.php'">
            <i class="bi bi-grid text-primary"></i>
            <span>Tất Cả</span>
        </div>
        <?php foreach ($brands as $b): ?>
            <div class="tz-brand-item <?= ($brandFilter === $b['maTH']) ? 'border-primary text-primary' : '' ?>" onclick="location.href='<?= BASE_URL ?>/client/index.php?brand=<?= urlencode($b['maTH']) ?>'">
                <i class="bi bi-laptop text-primary"></i>
                <span><?= htmlspecialchars($b['tenTH']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!--  4. TIÊU ĐỀ LƯỚI SẢN PHẨM  -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4" id="productListArea">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">
                <?php if ($isSaleFilter): ?>
                    <i class="bi bi-fire text-danger me-2"></i>Chương Trình Khuyến Mãi Hot & Laptop Giảm Giá
                <?php elseif (!empty($searchQuery)): ?>
                    <i class="bi bi-search text-primary me-2"></i>Kết quả tìm kiếm cho: "<?= htmlspecialchars($searchQuery) ?>"
                <?php else: ?>
                    <i class="bi bi-laptop text-primary me-2"></i>Laptop Phân Phối Chính Hãng TechZone
                <?php endif; ?>
            </h2>
            <p class="text-secondary mb-0 small">
                Laptop chính hãng sẵn hàng tại hệ thống TechZone
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">Sắp xếp:</span>
            <select class="form-select form-select-sm" style="width: 170px;" onchange="location.href = updateQueryStringParameter(window.location.href, 'sort', this.value);">
                <option value="" <?= empty($sortOption) ? 'selected' : '' ?>>Mặc định</option>
                <option value="price_asc" <?= ($sortOption === 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến cao</option>
                <option value="price_desc" <?= ($sortOption === 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến thấp</option>
                <option value="newest" <?= ($sortOption === 'newest') ? 'selected' : '' ?>>Mới nhất 2026</option>
            </select>
        </div>
    </div>

    <!--  5. LƯỚI SẢN PHẨM LAPTOP (CLICK VÀO ĐỂ MỞ TRANG CHI TIẾT)  -->
    <div class="row g-4">
        <?php
        $countShown = 0;
        if ($resultProducts && $resultProducts->num_rows > 0) {
            while ($row = $resultProducts->fetch_assoc()) {
                $maSP = $row["maSP"];
                $tenSP = $row["tenSP"];
                $maDM = $row["maDM"] ?? "";
                $maTH = $row["maTH"] ?? "";
                $thongSo = $row["thongSoKyThuat"] ?? "";
                $giaBan = (float)$row["giaBan"];
                $soLuongTon = (int)($row["soLuongTon"] ?? 0);
                $logo = !empty($row["logo"]) ? $row["logo"] : "https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887";

                // 1. SELECT tên thương hiệu từ bảng thuonghieu
                $tenTH = "TECHZONE";
                if (!empty($maTH)) {
                    $sqlTH = "SELECT tenTH FROM thuonghieu WHERE maTH = '$maTH'";
                    $resTH = $conn->query($sqlTH);
                    if ($resTH && $resTH->num_rows > 0) {
                        $tenTH = $resTH->fetch_assoc()["tenTH"];
                    }
                }

                // 2. SELECT tên danh mục & maKM từ bảng danhmuc
                $tenDM = "";
                $maKM = "";
                if (!empty($maDM)) {
                    $sqlDM = "SELECT tenDM, maKM FROM danhmuc WHERE maDM = '$maDM'";
                    $resDM = $conn->query($sqlDM);
                    if ($resDM && $resDM->num_rows > 0) {
                        $rowDM = $resDM->fetch_assoc();
                        $tenDM = $rowDM["tenDM"];
                        $maKM = $rowDM["maKM"] ?? "";
                    }
                }

                // 3. SELECT khuyến mãi từ bảng khuyenmai
                $tenKM = "";
                $loaiKM = "";
                $giaTriGiam = 0;
                $mucGiamToiDa = 0;
                if (!empty($maKM)) {
                    $sqlKM = "SELECT * FROM khuyenmai WHERE maKM = '$maKM' AND trangThai = 1";
                    $resKM = $conn->query($sqlKM);
                    if ($resKM && $resKM->num_rows > 0) {
                        $rowKM = $resKM->fetch_assoc();
                        $tenKM = $rowKM["tenKM"];
                        $loaiKM = $rowKM["loaiKM"];
                        $giaTriGiam = (float)$rowKM["giaTriGiam"];
                        $mucGiamToiDa = (float)$rowKM["mucGiamToiDa"];
                    }
                }

                // Nếu người dùng đang lọc xem Khuyến mãi Hot (sale) mà máy này không có KM thì bỏ qua
                if ($isSaleFilter && empty($tenKM)) {
                    continue;
                }

                $countShown++;

                // Tính tiền giảm giá
                $giaGoc = $giaBan;
                $soTienGiam = 0;
                if ($loaiKM === 'PHAN_TRAM' && $giaTriGiam > 0) {
                    $soTienGiam = ($giaGoc * $giaTriGiam) / 100;
                    if ($mucGiamToiDa > 0 && $soTienGiam > $mucGiamToiDa) {
                        $soTienGiam = $mucGiamToiDa;
                    }
                } elseif ($loaiKM === 'TIEN_MAT' && $giaTriGiam > 0) {
                    $soTienGiam = $giaTriGiam;
                }

                $giaHienTai = $soTienGiam > 0 ? max(0, $giaGoc - $soTienGiam) : $giaGoc;
                $giaCu = $soTienGiam > 0 ? $giaGoc : 0;
        ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <!-- Click vào card để nhảy sang trang chi tiết sản phẩm: product-detail.php?id=... -->
                    <a href="<?= BASE_URL ?>/client/product-detail.php?id=<?= $maSP ?>" class="tz-product-card position-relative <?= $soLuongTon <= 0 ? 'tz-product-card-out' : '' ?>">
                        <div class="tz-card-image-box position-relative">
                            <span class="tz-card-tag tz-tag-hot">
                                <?= htmlspecialchars($tenTH) ?>
                            </span>

                            <?php if ($soLuongTon <= 0): ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-2.5 py-1.5 shadow-sm fw-bold" style="font-size: 11px; z-index: 3;">
                                    <i class="bi bi-x-circle me-1"></i>HẾT HÀNG
                                </span>
                            <?php elseif ($soLuongTon <= 3): ?>
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm fw-semibold" style="font-size: 10.5px; z-index: 3;">
                                    <i class="bi bi-lightning-fill me-1"></i>Chỉ còn <?= $soLuongTon ?>
                                </span>
                            <?php endif; ?>

                            <img src="<?= $logo ?>" alt="<?= htmlspecialchars($tenSP) ?>" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887';" style="<?= $soLuongTon <= 0 ? 'filter: grayscale(80%) opacity(0.85);' : '' ?>">
                        </div>

                        <div class="tz-card-content">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="tz-product-brand"><?= htmlspecialchars($tenTH) ?></span>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"><?= $maSP ?></span>
                            </div>

                            <h3 class="tz-product-title" title="<?= htmlspecialchars($tenSP) ?>">
                                <?= htmlspecialchars($tenSP) ?>
                            </h3>

                            <!-- Tình trạng tồn kho -->
                            <div class="mb-2" style="font-size: 11.5px;">
                                <?php if ($soLuongTon <= 0): ?>
                                    <span class="text-danger fw-semibold"><i class="bi bi-x-circle me-1"></i>Tạm hết hàng</span>
                                <?php elseif ($soLuongTon <= 5): ?>
                                    <span class="text-warning fw-semibold"><i class="bi bi-box-seam me-1"></i>Còn <?= $soLuongTon ?> máy sẵn</span>
                                <?php else: ?>
                                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Còn <?= $soLuongTon ?> máy</span>
                                <?php endif; ?>
                            </div>

                            <!-- Khung giá & Nút thêm giỏ hàng -->
                            <div class="tz-product-price-box" onclick="event.preventDefault(); event.stopPropagation();">
                                <div>
                                    <div class="tz-current-price"><?= number_format($giaHienTai, 0, ',', '.') ?> ₫</div>
                                    <?php if ($giaCu > $giaHienTai): ?>
                                        <div class="tz-old-price"><?= number_format($giaCu, 0, ',', '.') ?> ₫</div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($soLuongTon <= 0): ?>
                                    <button type="button" class="btn-tz-add-cart disabled" style="opacity: 0.5; cursor: not-allowed; background-color: #cbd5e1; border-color: #cbd5e1; color: #64748b;" title="Sản phẩm tạm hết hàng" disabled onclick="event.preventDefault(); event.stopPropagation();">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn-tz-add-cart" title="Thêm vào giỏ hàng (Kho còn <?= $soLuongTon ?>)" onclick="CartManager.themSanPham('<?= $maSP ?>', 1, this);">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
            }
        }

        if ($countShown === 0):
            ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h4 class="text-secondary">Không tìm thấy sản phẩm laptop phù hợp</h4>
                <p class="text-muted small">Vui lòng thử lại với từ khóa khác hoặc bấm nút bên dưới để xem toàn bộ danh mục.</p>
                <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Xem toàn bộ laptop
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + encodeURIComponent(value) + '$2');
        } else {
            return uri + separator + key + "=" + encodeURIComponent(value);
        }
    }
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>