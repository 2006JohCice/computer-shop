<?php
// app/client/products/detail.php
$pageTitle = "Chi Tiết Sản Phẩm Laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

include __DIR__ . "/../../config/database.php";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$maSP = $_GET['id'] ?? 'SP001';

// Lấy thông tin chi tiết sản phẩm hehehehheheheheheheheh
$sql = "SELECT * FROM sanpham WHERE maSP = '$maSP' AND isDeleted = 0";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    $sqlFallback = "SELECT * FROM sanpham WHERE isDeleted = 0 ORDER BY maSP ASC LIMIT 1";
    $result = $conn->query($sqlFallback);
}

$tenSP = "";
$maDM = "";
$maTH = "";
$giaBan = 0;
$giaGoc = 0;
$tietKiem = 0;
$soLuongTon = 0;
$thoiGianBaoHanh = 12;
$thongSo = "";
$moTa = "";
$viTriKho = "Kho Trung Tâm TechZone";
$logo = "https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887";
$tenTH = "TECHZONE";
$tenDM = "Laptop";
$specs = [];

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $maSP = $row["maSP"];
    $tenSP = $row["tenSP"];
    $maDM = $row["maDM"] ?? "";
    $maTH = $row["maTH"] ?? "";
    $giaBan = (float)$row["giaBan"];
    $giaGoc = $giaBan > 0 ? (float)round($giaBan * 1.15) : 0;
    $tietKiem = $giaGoc - $giaBan;
    $soLuongTon = (int)($row["soLuongTon"] ?? 0);
    $thoiGianBaoHanh = (int)($row["thoiGianBaoHanh"] ?? 12);
    $thongSo = $row["thongSoKyThuat"] ?? "";
    $moTa = $row["moTa"] ?? "";
    $viTriKho = $row["viTriKho"] ?? "Kho Trung Tâm TechZone";
    if (!empty($row["logo"])) {
        $logo = $row["logo"];
    }

    // Lấy tên thương hiệu từ bảng thuonghieu
    if (!empty($maTH)) {
        $resTH = $conn->query("SELECT tenTH FROM thuonghieu WHERE maTH = '$maTH'");
        if ($resTH && $rowTH = $resTH->fetch_assoc()) {
            $tenTH = $rowTH["tenTH"];
        }
    }

    // Lấy tên danh mục từ bảng danhmuc
    if (!empty($maDM)) {
        $resDM = $conn->query("SELECT tenDM FROM danhmuc WHERE maDM = '$maDM'");
        if ($resDM && $rowDM = $resDM->fetch_assoc()) {
            $tenDM = $rowDM["tenDM"];
        }
    }

    // Bảng thông số kỹ thuật chuẩn hóa trực tiếp 100% từ CSDL db_computer
    $specs = [];
    $specs['Thương hiệu'] = $tenTH;
    $specs['Phân loại danh mục'] = $tenDM;
    $specs['Mã sản phẩm (SKU)'] = $maSP;

    // Tách các thông số phần cứng từ chuỗi thongSoKyThuat trong CSDL
    if (!empty($thongSo)) {
        $parts = explode('|', $thongSo);
        foreach ($parts as $p) {
            $p = trim($p);
            if (strpos($p, ':') !== false) {
                list($k, $v) = explode(':', $p, 2);
                $specs[trim($k)] = trim($v);
            } elseif (!empty($p)) {
                $specs['Cấu hình chi tiết'] = $p;
            }
        }
    }

    $specs['Thời gian bảo hành'] = $thoiGianBaoHanh . ' tháng chính hãng';
    if (!empty($viTriKho)) {
        $specs['Vị trí kho hàng'] = $viTriKho;
    }
    $specs['Tình trạng tồn kho'] = ($soLuongTon > 0) ? 'Còn hàng (' . $soLuongTon . ' máy sẵn sàng giao)' : '<span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Tạm hết hàng (0 chiếc)</span>';
}
?>

<div class="container py-4">
    <!-- ==================== BREADCRUMB ==================== -->
    <nav class="sp-breadcrumb">
        <a href="<?= BASE_URL ?>/client/index.php"><i class="bi bi-house me-1"></i>Trang chủ</a>
        <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
        <a href="<?= BASE_URL ?>/client/index.php?dm=<?= urlencode($maDM) ?>"><?= htmlspecialchars($tenDM) ?></a>
        <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
        <span class="current text-truncate" style="max-width: 450px;"><?= htmlspecialchars($tenSP) ?></span>
    </nav>

    <!-- ==================== KHỐI TỔNG QUAN SẢN PHẨM ==================== -->
    <section class="sp-overview-card">
        <div class="row g-4">
            <!-- 1. CỘT TRÁI: ẢNH SẢN PHẨM TỪ CSDL -->
            <div class="col-lg-5">
                <div class="sp-main-image-wrap">
                    <div class="sp-img-badges">
                        <?php if ($giaGoc > 0): ?>
                            <span class="sp-badge-discount">-<?= round(($tietKiem / $giaGoc) * 100) ?>% GIẢM</span>
                        <?php endif; ?>
                        <span class="sp-badge-installment">TRẢ GÓP 0%</span>
                    </div>
                    <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($tenSP) ?>" id="mainProductImg" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887';">
                </div>
            </div>

            <!-- 2. CỘT PHẢI: THÔNG TIN MÁY, GIÁ TIỀN & ĐẶT HÀNG -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white font-monospace"><?= htmlspecialchars($tenTH) ?></span>
                    <span class="badge bg-light text-secondary border font-monospace">MÃ SP: <?= htmlspecialchars($maSP) ?></span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        <i class="bi bi-check2-circle me-1"></i>Sẵn hàng tại kho (<?= $soLuongTon ?> máy)
                    </span>
                </div>

                <h1 class="sp-product-title"><?= htmlspecialchars($tenSP) ?></h1>

                <!-- Đánh giá sao & Vị trí kho & Trạng thái tồn kho -->
                <div class="sp-rating-bar d-flex align-items-center gap-2 flex-wrap">
                    <div class="sp-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="text-muted">•</span>
                    <span class="text-secondary"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Kho: <?= htmlspecialchars($viTriKho) ?></span>
                    <span class="text-muted">•</span>
                    <?php if ($soLuongTon > 0): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i>Còn hàng (<?= $soLuongTon ?> máy)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger text-white px-2.5 py-1 fw-bold">
                            <i class="bi bi-x-circle me-1"></i>TẠM HẾT HÀNG
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Khung Giá & Khuyến Mãi -->
                <div class="sp-price-box">
                    <div>
                        <div class="sp-price-current"><?= number_format($giaBan, 0, ',', '.') ?> ₫</div>
                        <small class="text-muted">Đã bao gồm thuế VAT 10% & Xuất Hóa Đơn Điện Tử</small>
                    </div>
                    <?php if ($giaGoc > $giaBan): ?>
                        <div class="sp-price-old"><?= number_format($giaGoc, 0, ',', '.') ?> ₫</div>
                        <span class="sp-price-saving">Tiết kiệm: <?= number_format($tietKiem, 0, ',', '.') ?> ₫</span>
                    <?php endif; ?>
                </div>

                <!-- Khung Quà Tặng Độc Quyền -->
                <div class="sp-gift-box">
                    <div class="sp-gift-header">
                        <i class="bi bi-gift-fill text-primary"></i>
                        <span>ƯU ĐÃI & QUÀ TẶNG KÈM THEO MÁY</span>
                    </div>
                    <ul class="sp-gift-list">
                        <li><i class="bi bi-check-circle-fill"></i><span>Tặng <strong>Balo Laptop Cao Cấp Chống Nước TechZone</strong> trị giá 850.000₫</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Tặng <strong>Chuột Không Dây Gaming Silent RGB</strong> trị giá 350.000₫</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Giao hàng toàn quốc, được <strong>đồng kiểm mở hộp & kiểm tra máy</strong> trước khi thanh toán COD</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Miễn phí lắp đặt, vệ sinh máy trọn đời & cài sẵn <strong>Windows 11 Pro bản quyền</strong></span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Hỗ trợ kỹ thuật từ xa qua Ultraviewer / Teamviewer 24/7 trọn đời máy</span></li>
                    </ul>
                </div>

                <!-- Bộ chọn số lượng & 2 Nút Mua Hàng -->
                <?php if ($soLuongTon > 0): ?>
                    <div class="sp-buy-actions">
                        <div class="sp-qty-select">
                            <button type="button" class="sp-qty-btn" onclick="adjustQty(-1)">-</button>
                            <input type="number" id="buyQtyInput" class="sp-qty-input" value="1" min="1" max="<?= $soLuongTon ?>">
                            <button type="button" class="sp-qty-btn" onclick="adjustQty(1)">+</button>
                        </div>

                        <!-- Nút Thêm vào giỏ -->
                        <button type="button" class="btn-sp-add-cart" onclick="addCurrentProductToCart(this)">
                            <i class="bi bi-cart-plus me-1"></i>THÊM VÀO GIỎ
                        </button>

                        <!-- Nút Mua ngay (Chuyển checkout) -->
                        <button type="button" class="btn-sp-buy-now" onclick="buyCurrentProductNow(this)">
                            <i class="bi bi-lightning-charge-fill me-1"></i>MUA NGAY (TRẢ GÓP 0%)
                        </button>
                    </div>
                <?php else: ?>
                    <div class="sp-buy-actions">
                        <div class="sp-qty-select" style="opacity: 0.5;">
                            <button type="button" class="sp-qty-btn" disabled>-</button>
                            <input type="number" id="buyQtyInput" class="sp-qty-input" value="0" disabled>
                            <button type="button" class="sp-qty-btn" disabled>+</button>
                        </div>

                        <!-- Nút Thêm vào giỏ (Disabled) -->
                        <button type="button" class="btn-sp-add-cart disabled" style="opacity: 0.6; cursor: not-allowed; background: #94a3b8; border-color: #94a3b8;" disabled>
                            <i class="bi bi-cart-x me-1"></i>TẠM HẾT HÀNG
                        </button>

                        <!-- Nút Mua ngay (Disabled) -->
                        <button type="button" class="btn-sp-buy-now disabled" style="opacity: 0.6; cursor: not-allowed; background: #64748b; border-color: #64748b;" disabled>
                            <i class="bi bi-x-circle me-1"></i>HẾT HÀNG
                        </button>
                    </div>
                    <div class="alert alert-warning d-flex align-items-center gap-2 mt-3 mb-0 py-2.5 px-3 small rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
                        <div>Sản phẩm này hiện đang tạm hết hàng tại kho TechZone. Quý khách vui lòng liên hệ Hotline <strong>1900 8888</strong> để đặt trước đợt hàng mới!</div>
                    </div>
                <?php endif; ?>

                <!-- Cam Kết Dịch Vụ -->
                <div class="sp-policy-box">
                    <div class="sp-policy-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Bảo hành <strong><?= $thoiGianBaoHanh ?> Tháng</strong> chính hãng kích hoạt theo Serial/IMEI</span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Lỗi phần cứng NSX đổi mới trong <strong>30 ngày</strong></span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-truck"></i>
                        <span>Giao hỏa tốc trong <strong>2 giờ</strong> nội thành Hà Nội & TP.HCM</span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-credit-card"></i>
                        <span>Trả góp 0% lãi suất xét duyệt nhanh trong 5 phút</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== BẢNG THÔNG SỐ KỸ THUẬT & ĐÁNH GIÁ ==================== -->
    <div class="row g-4">
        <!-- Cột Trái: Đánh Giá Chi Tiết Máy & Review Khách Hàng -->
        <div class="col-lg-7">
            <div class="tz-card p-4 mb-4">
                <h3 class="fw-bold text-dark mb-3 fs-5">
                    <i class="bi bi-file-text text-primary me-2"></i>Đặc Điểm Nổi Bật & Trải Nghiệm Thực Tế
                </h3>
                <div class="text-secondary" style="font-size: 14.5px; line-height: 1.8;">
                    <?php if (!empty($moTa)): ?>
                        <p><?= nl2br(htmlspecialchars($moTa)) ?></p>
                    <?php else: ?>
                        <p>
                            <strong><?= htmlspecialchars($tenSP) ?></strong> là dòng sản phẩm đại diện cho xu hướng điện toán cá nhân thế hệ mới của hãng <strong><?= htmlspecialchars($tenTH) ?></strong>. Được trang bị kiến trúc vi xử lý tiên tiến cùng công nghệ tăng tốc trí tuệ nhân tạo (NPU AI Engine), chiếc máy sẵn sàng xử lý mượt mà từ các tác vụ văn phòng đa nhiệm, xử lý đồ họa kiến trúc nặng cho tới trải nghiệm các tựa game bom tấn.
                        </p>
                        <h5 class="fw-bold text-dark mt-3 mb-2">1. Hiệu năng vượt bậc với cấu hình thế hệ mới</h5>
                        <p>
                            Nhờ tối ưu hóa điện năng và xung nhịp boost mạnh mẽ, thiết bị có khả năng giải phóng toàn bộ hiệu năng mà vẫn duy trì nhiệt độ mát mẻ suốt nhiều giờ tải nặng liên tục. Bộ nhớ RAM thế hệ mới cho phép mở hàng chục tab trình duyệt và ứng dụng mà không lo giật lag.
                        </p>
                        <h5 class="fw-bold text-dark mt-3 mb-2">2. Màn hình chuẩn màu sắc nét & Chống mỏi mắt</h5>
                        <p>
                            Trang bị tấm nền công nghệ cao với dải màu đạt chuẩn chuyên nghiệp, độ tương phản sâu và độ sáng vượt trội giúp hiển thị chi tiết hình ảnh chân thực ngay cả trong điều kiện ánh sáng ngoài trời.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Khung Review Khách Hàng -->
            <!-- <div class="tz-review-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0 fs-5"><i class="bi bi-chat-heart text-danger me-2"></i>Khách Hàng Đánh Giá</h4>
                    <span class="badge bg-primary">4.9 / 5 Sao</span>
                </div>

                <div class="tz-review-item">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-dark">Trần Hoàng Long</strong>
                        <span class="text-muted small">18/10/2026</span>
                    </div>
                    <div class="sp-stars mb-2" style="font-size: 12px;">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary small mb-0">
                        Máy đóng gói rất cẩn thận, nguyên seal hộp. Mình kiểm tra mã Serial trên hệ thống TechZone đúng hạn bảo hành <?= $thoiGianBaoHanh ?> tháng chính hãng. Dùng rất mượt, pin trâu, đáng giá từng đồng!
                    </p>
                </div>

                <div class="tz-review-item">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-dark">Nguyễn Thị Thu Hà</strong>
                        <span class="text-muted small">15/10/2026</span>
                    </div>
                    <div class="sp-stars mb-2" style="font-size: 12px;">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary small mb-0">
                        Giao hàng hỏa tốc trong 2 giờ tại Hà Nội cực kỳ nhanh. Kỹ thuật viên qua bàn giao tận nhà, hướng dẫn cài đặt phần mềm rất nhiệt tình. 10 điểm cho dịch vụ!
                    </p>
                </div>
            </div> -->
        </div>

        <!-- Cột Phải: BẢNG THÔNG SỐ KỸ THUẬT CHI TIẾT -->
        <div class="col-lg-5">
            <div class="tz-specs-card">
                <h3 class="fw-bold text-dark mb-3 pb-2 border-bottom fs-5">
                    <i class="bi bi-cpu text-primary me-2"></i>Bảng Thông Số Kỹ Thuật
                </h3>

                <table class="tz-specs-table">
                    <tbody>
                        <?php foreach ($specs as $name => $val): ?>
                            <tr>
                                <td class="spec-name"><?= htmlspecialchars($name) ?></td>
                                <td class="spec-val"><?= htmlspecialchars($val) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Showroom còn hàng -->
            <!-- <div class="tz-card p-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 15px;">
                    <i class="bi bi-shop text-primary me-2"></i>Showroom Sẵn Hàng Trải Nghiệm
                </h5>
                <div class="mb-3">
                    <div class="fw-bold text-dark small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Showroom Quận 1, TP.HCM</div>
                    <div class="text-muted small ps-3">123 Đường Công Nghệ, P. Bến Nghé • <span class="text-success fw-bold">Sẵn hàng</span></div>
                </div>
                <div>
                    <div class="fw-bold text-dark small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Showroom Đống Đa, Hà Nội</div>
                    <div class="text-muted small ps-3">456 Phố Thái Hà, P. Trung Liệt • <span class="text-success fw-bold">Sẵn hàng</span></div>
                </div>
            </div> -->
        </div>
    </div>

    <!-- ==================== SẢN PHẨM LAPTOP GỢI Ý / CÙNG HÃNG TỪ DATABASE ==================== -->
    <section class="mt-5 pt-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1 fs-4">
                    <i class="bi bi-laptop text-primary me-2"></i>Gợi Ý Laptop Cùng Hãng & Nổi Bật
                </h3>
                <p class="text-secondary small mb-0">Các mẫu laptop chính hãng sẵn hàng tại kho TechZone</p>
            </div>
            <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                Xem tất cả laptop <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
            // Lấy 4 sản phẩm liên quan từ CSDL theo logic của bạn
            $sqlRel = "SELECT * FROM sanpham WHERE isDeleted = 0 AND maSP != '$maSP'";
            if (!empty($maTH)) {
                $sqlRel .= " ORDER BY (maTH = '$maTH') DESC, maSP ASC LIMIT 4";
            } else {
                $sqlRel .= " ORDER BY maSP ASC LIMIT 4";
            }
            $resRel = $conn->query($sqlRel);

            if ($resRel && $resRel->num_rows > 0) {
                while ($row = $resRel->fetch_assoc()) {
                    $maSP_rel = $row["maSP"];
                    $tenSP_rel = $row["tenSP"];
                    $maDM_rel = $row["maDM"] ?? "";
                    $maTH_rel = $row["maTH"] ?? "";
                    $giaBan_rel = (float)$row["giaBan"];
                    $giaGoc_rel = $giaBan_rel > 0 ? (float)round($giaBan_rel * 1.15) : 0;
                    $thongSo_rel = $row["thongSoKyThuat"] ?? "";
                    $logo_rel = !empty($row["logo"]) ? $row["logo"] : "https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887";

                    // Lấy tên thương hiệu từ bảng thuonghieu
                    $tenTH_rel = "TECHZONE";
                    if (!empty($maTH_rel)) {
                        $resTH_rel = $conn->query("SELECT tenTH FROM thuonghieu WHERE maTH = '$maTH_rel'");
                        if ($resTH_rel && $rowTH_rel = $resTH_rel->fetch_assoc()) {
                            $tenTH_rel = $rowTH_rel["tenTH"];
                        }
                    }

                    // Tách thông số nhanh CPU, RAM, GPU
                    $cpu_rel = 'Đang cập nhật';
                    $ram_rel = 'Đang cập nhật';
                    $gpu_rel = 'Đang cập nhật';
                    if (!empty($thongSo_rel)) {
                        $parts_rel = explode('|', $thongSo_rel);
                        foreach ($parts_rel as $pr) {
                            $pr = trim($pr);
                            if (stripos($pr, 'CPU:') === 0 || stripos($pr, 'Chip:') === 0) {
                                $cpu_rel = trim(preg_replace('/^(CPU|Chip):\s*/i', '', $pr));
                            } elseif (stripos($pr, 'RAM:') === 0) {
                                $ram_rel = trim(preg_replace('/^RAM:\s*/i', '', $pr));
                            } elseif (stripos($pr, 'VGA:') === 0 || stripos($pr, 'GPU:') === 0) {
                                $gpu_rel = trim(preg_replace('/^(VGA|GPU):\s*/i', '', $pr));
                            }
                        }
                    }
                    if ($gpu_rel === 'Đang cập nhật') {
                        if (stripos($thongSo_rel, 'Intel') !== false) $gpu_rel = 'Intel Iris Xe';
                        elseif (stripos($thongSo_rel, 'Apple') !== false) $gpu_rel = 'Apple GPU';
                        else $gpu_rel = 'Đồ họa Onboard';
                    }
            ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="<?= BASE_URL ?>/client/product-detail.php?id=<?= $maSP_rel ?>" class="tz-product-card">
                            <div class="tz-card-image-box">
                                <span class="tz-card-tag tz-tag-hot">
                                    <?= htmlspecialchars($tenTH_rel ?: $maTH_rel) ?>
                                </span>
                                <img src="<?= $logo_rel ?>" alt="<?= htmlspecialchars($tenSP_rel) ?>" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://banhanggiasi.com.vn/storage/laptop/dell-latitude-7410-2in1-i7-10610u-16gb-512gb-tuoch-gap.jpg?v=1713513887';">
                            </div>

                            <div class="tz-card-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="tz-product-brand"><?= htmlspecialchars($tenTH_rel) ?></span>
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;"><?= $maSP_rel ?></span>
                                </div>

                                <h3 class="tz-product-title" title="<?= htmlspecialchars($tenSP_rel) ?>">
                                    <?= htmlspecialchars($tenSP_rel) ?>
                                </h3>

                                <div class="tz-spec-chips">
                                    <span class="tz-spec-chip"><i class="bi bi-cpu me-1"></i><?= htmlspecialchars($cpu_rel) ?></span>
                                    <span class="tz-spec-chip"><i class="bi bi-memory me-1"></i><?= htmlspecialchars($ram_rel) ?></span>
                                    <span class="tz-spec-chip"><i class="bi bi-gpu-card me-1"></i><?= htmlspecialchars($gpu_rel) ?></span>
                                </div>

                                <div class="tz-product-price-box" onclick="event.preventDefault(); event.stopPropagation();">
                                    <div>
                                        <div class="tz-current-price"><?= number_format($giaBan_rel, 0, ',', '.') ?> ₫</div>
                                        <?php if ($giaGoc_rel > $giaBan_rel): ?>
                                            <div class="tz-old-price"><?= number_format($giaGoc_rel, 0, ',', '.') ?> ₫</div>
                                        <?php endif; ?>
                                    </div>

                                    <button type="button" class="btn-tz-add-cart" title="Thêm vào giỏ hàng" onclick="CartManager.themSanPham('<?= $maSP_rel ?>', 1);">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </section>
</div>

<script>
    const maxTonKho = <?= $soLuongTon ?>;

    function adjustQty(delta) {
        const input = document.getElementById('buyQtyInput');
        if (!input || input.disabled || maxTonKho <= 0) return;
        let val = parseInt(input.value) || 1;
        val = Math.max(1, Math.min(val + delta, maxTonKho));
        input.value = val;
    }

    function addCurrentProductToCart(btn) {
        if (maxTonKho <= 0) {
            if (window.TechZoneNotify) {
                TechZoneNotify.error('Sản phẩm này hiện đã hết hàng, không thể đặt!', 'Hết hàng');
            } else {
                alert('Sản phẩm đã hết hàng!');
            }
            return;
        }
        const qty = parseInt(document.getElementById('buyQtyInput').value) || 1;
        CartManager.themSanPham('<?= $maSP ?>', qty, btn);
    }

    function buyCurrentProductNow(btn) {
        if (maxTonKho <= 0) {
            if (window.TechZoneNotify) {
                TechZoneNotify.error('Sản phẩm này hiện đã hết hàng, không thể mua ngay!', 'Hết hàng');
            } else {
                alert('Sản phẩm đã hết hàng!');
            }
            return;
        }
        const qty = parseInt(document.getElementById('buyQtyInput').value) || 1;
        CartManager.themSanPham('<?= $maSP ?>', qty, btn);
        setTimeout(() => {
            location.href = '<?= BASE_URL ?>/client/checkout.php';
        }, 600);
    }
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>