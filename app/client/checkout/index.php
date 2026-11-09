<?php
// app/views/client/checkout/index.php
$pageTitle = "Thanh Toán & Đặt Hàng COD";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BẮT BUỘC ĐĂNG NHẬP: Chưa đăng nhập không được phép vào trang thanh toán đặt hàng
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || empty($_SESSION['maTk'])) {
    header("Location: /computer-shop/public/login.php?redirect=checkout");
    exit();
}

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

// Nạp kết nối CSDL và đọc thông tin khách hàng từ DB nếu có ?kh=
include_once dirname(__DIR__, 2) . '/config/database.php';
$conn = @new mysqli($servername, $username, $password, $dbname);
if ($conn && !$conn->connect_error) {
    $conn->set_charset("utf8mb4");
}

// Xác định thông tin khách hàng từ Session đăng nhập
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk']);
$sessionMaTk = $_SESSION['maTk'] ?? '';
$sessionUserName = $_SESSION['user_name'] ?? '';

$activeMaKH = '';
$hoTenNhan = $sessionUserName ?: 'Nguyễn Văn An';
$sdtNhan = '0912345678';
$diaChiNhan = '123 Đường Láng, Đống Đa, Hà Nội';

if ($isLoggedIn && $conn && !$conn->connect_error) {
    $maTkEsc = $conn->real_escape_string($sessionMaTk);
    $resKH = $conn->query("SELECT * FROM khachhang WHERE maTk = '$maTkEsc' LIMIT 1");
    if ($resKH && $rowKH = $resKH->fetch_assoc()) {
        $activeMaKH = $rowKH['maKH'];
        if (!empty($rowKH['hoVaTen'])) $hoTenNhan = $rowKH['hoVaTen'];
        if (!empty($rowKH['soDienThoai'])) $sdtNhan = $rowKH['soDienThoai'];
        if (!empty($rowKH['diaChi'])) $diaChiNhan = $rowKH['diaChi'];
    } else {
        $resAcc = $conn->query("SELECT email, hoTen FROM accounts WHERE maTk = '$maTkEsc' LIMIT 1");
        if ($resAcc && $rAcc = $resAcc->fetch_assoc()) {
            $emailAcc = $conn->real_escape_string($rAcc['email'] ?? '');
            if (!empty($emailAcc)) {
                $resKHEmail = $conn->query("SELECT * FROM khachhang WHERE email = '$emailAcc' LIMIT 1");
                if ($resKHEmail && $rowKHEmail = $resKHEmail->fetch_assoc()) {
                    $activeMaKH = $rowKHEmail['maKH'];
                    if (!empty($rowKHEmail['hoVaTen'])) $hoTenNhan = $rowKHEmail['hoVaTen'];
                    if (!empty($rowKHEmail['soDienThoai'])) $sdtNhan = $rowKHEmail['soDienThoai'];
                    if (!empty($rowKHEmail['diaChi'])) $diaChiNhan = $rowKHEmail['diaChi'];
                    $conn->query("UPDATE khachhang SET maTk = '$maTkEsc' WHERE maKH = '$activeMaKH'");
                }
            }
        }
    }
}

if (empty($activeMaKH)) {
    $requestedKh = trim($_GET['kh'] ?? '');
    $activeMaKH = !empty($requestedKh) ? $requestedKh : 'KH001';
    if ($conn && !$conn->connect_error) {
        $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '$activeMaKH' LIMIT 1");
        if ($resKH && $rowKH = $resKH->fetch_assoc()) {
            if (!empty($rowKH['hoVaTen'])) $hoTenNhan = $rowKH['hoVaTen'];
            if (!empty($rowKH['soDienThoai'])) $sdtNhan = $rowKH['soDienThoai'];
            if (!empty($rowKH['diaChi'])) $diaChiNhan = $rowKH['diaChi'];
        }
    }
}

// Lấy sẵn giỏ hàng trong CSDL (nếu khách hàng có trong DB)
$dbCartMap = [];
if ($conn && !$conn->connect_error) {
    $resGH = $conn->query("SELECT maSP, soLuong FROM giohang WHERE maKH = '$activeMaKH'");
    if ($resGH && $resGH->num_rows > 0) {
        while ($rGH = $resGH->fetch_assoc()) {
            $dbCartMap[$rGH['maSP']] = (int)$rGH['soLuong'];
        }
    }
}
?>

<style>
/* ==========================================================================
   TECHZONE COD PAYMENT & MODAL - UI/UX PRO MAX DESIGN SYSTEM
   ========================================================================== */
.tz-cod-card {
    position: relative;
    padding: 22px;
    border: 2px solid #10b981;
    border-radius: var(--tz-radius-md, 12px);
    background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    box-shadow: 0 4px 18px rgba(16, 185, 129, 0.08);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.tz-cod-card:hover {
    border-color: #059669;
    box-shadow: 0 6px 24px rgba(16, 185, 129, 0.14);
}
.tz-cod-feature-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    height: 100%;
    transition: all 0.2s ease;
}
.tz-cod-feature-box:hover {
    border-color: #10b981;
    background: #fcfdfd;
    transform: translateY(-1px);
}
.tz-cod-modal .modal-content {
    border: 0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(6, 95, 70, 0.25);
    background: #ffffff;
}
.tz-cod-modal-header {
    background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
    color: #ffffff;
    padding: 18px 24px;
    position: relative;
}
.tz-cod-modal-header .btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.85;
    transition: opacity 0.2s;
}
.tz-cod-modal-header .btn-close-white:hover {
    opacity: 1;
}
</style>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php<?= !empty($requestedKh) ? '?kh=' . urlencode($requestedKh) : '' ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/cart.php<?= !empty($requestedKh) ? '?kh=' . urlencode($requestedKh) : '' ?>" class="text-decoration-none text-muted">Giỏ hàng</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Thanh toán đặt hàng</li>
        </ol>
    </nav>

    <!-- Stepper 3 Bước Mua Hàng Chuẩn E-Commerce -->
    <div class="tz-checkout-stepper d-none d-md-flex">
        <a href="<?= BASE_URL ?>/client/cart.php<?= !empty($requestedKh) ? '?kh=' . urlencode($requestedKh) : '' ?>" class="tz-step-node completed text-decoration-none">
            <span class="tz-step-node-num"><i class="bi bi-check-lg"></i></span>
            <span>1. Giỏ Hàng</span>
        </a>
        <span class="tz-step-divider"></span>
        <div class="tz-step-node active">
            <span class="tz-step-node-num">2</span>
            <span>2. Giao Hàng & Thanh Toán COD</span>
        </div>
        <span class="tz-step-divider"></span>
        <div class="tz-step-node">
            <span class="tz-step-node-num">3</span>
            <span>3. Hoàn Tất & Kích Hoạt Bảo Hành</span>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1" style="font-size: 24px;">
            <i class="bi bi-credit-card-2-front text-primary me-2"></i>Thanh Toán & Đặt Hàng Laptop
        </h2>
        <p class="text-secondary mb-0 small">Hoàn tất thông tin nhận hàng và thanh toán tiền mặt an toàn khi kiểm tra hàng.</p>
    </div>

    <form id="checkoutForm" onsubmit="handlePlaceOrder(event);">
        <div class="row g-4">
            <!-- CỘT TRÁI: THÔNG TIN NGƯỜI NHẬN & PHƯƠNG THỨC -->
            <div class="col-lg-7">
                <!-- 1. Thông tin giao nhận -->
                <div class="tz-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h4 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>1. Thông Tin Người Nhận Hàng
                        </h4>
                        <span class="badge bg-light text-secondary border">Mã KH: <?= htmlspecialchars($activeMaKH) ?></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="tenNguoiNhan" id="tenNguoiNhan" class="form-control" value="<?= htmlspecialchars($hoTenNhan) ?>" placeholder="Nhập tên người nhận..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                            <input type="tel" name="sdtNhanHang" id="sdtNhanHang" class="form-control" value="<?= htmlspecialchars($sdtNhan) ?>" placeholder="Nhập SĐT..." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
                            <textarea name="diaChiGiaoHang" id="diaChiGiaoHang" class="form-control" rows="2" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành..." required><?= htmlspecialchars($diaChiNhan) ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea name="ghiChuKhachHang" id="ghiChuKhachHang" class="form-control" rows="2" placeholder="VD: Giao giờ hành chính, gọi trước 15 phút, cài sẵn Windows 11 + Office..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Phương thức giao hàng (phuongThucGiaoHang) -->
                <div class="tz-card p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-truck text-primary me-2"></i>2. Phương Thức Vận Chuyển
                    </h4>

                    <div class="d-flex flex-column gap-2">
                        <label class="tz-select-card active">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="15000" class="form-check-input mt-0" checked onchange="updateShippingFee(15000)">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-truck fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Giao Hàng Tiêu Chuẩn</div>
                                    <small class="text-muted">Nhận hàng trong 2-3 ngày làm việc toàn quốc</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary fs-6">15.000 ₫</span>
                        </label>

                        <label class="tz-select-card">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="30000" class="form-check-input mt-0" onchange="updateShippingFee(30000)">
                                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-lightning-charge-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        Giao Hàng Nhanh 24H
                                        <span class="badge bg-warning text-dark font-monospace" style="font-size: 10px;">ƯU TIÊN</span>
                                    </div>
                                    <small class="text-muted">Giao trong ngày hoặc sáng ngày làm việc kế tiếp</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary fs-6">30.000 ₫</span>
                        </label>

                        <label class="tz-select-card">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="50000" class="form-check-input mt-0" onchange="updateShippingFee(50000)">
                                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        Giao Hỏa Tốc Nội Thành 2H
                                        <span class="badge bg-danger text-white font-monospace" style="font-size: 10px;">HỎA TỐC</span>
                                    </div>
                                    <small class="text-muted">Kỹ thuật viên giao tận tay & hướng dẫn cài đặt máy</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary fs-6">50.000 ₫</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Phương thức thanh toán (COD - UI/UX PRO MAX) -->
                <div class="tz-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h4 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                            <i class="bi bi-shield-check text-success me-2"></i>3. Phương Thức Thanh Toán
                        </h4>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                            <i class="bi bi-patch-check-fill text-success"></i> ĐỒNG KIỂM 100% • AN TÂM TUYỆT ĐỐI
                        </span>
                    </div>

                    <!-- THẺ THANH TOÁN COD CHÍNH THỨC -->
                    <div class="tz-cod-card">
                        <input type="hidden" name="phuongThucThanhToan" value="COD">

                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; font-size: 22px;">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                                        Thanh toán tiền mặt khi nhận hàng (COD)
                                    </h5>
                                    <span class="badge bg-success text-white px-2.5 py-1 fw-bold font-monospace" style="font-size: 11px;">
                                        <i class="bi bi-check2-circle me-1"></i>PHƯƠNG THỨC CHÍNH
                                    </span>
                                </div>
                                <p class="text-secondary small mb-3">
                                    Quý khách chỉ thanh toán khi đơn hàng đã được giao tận tay, kiểm tra đúng sản phẩm, linh phụ kiện và số Serial/IMEI nguyên seal.
                                </p>

                                <!-- 3 Cam Kết Đồng Kiểm Vàng -->
                                <div class="row g-2 pt-2 border-top">
                                    <div class="col-sm-4">
                                        <div class="tz-cod-feature-box">
                                            <i class="bi bi-box-seam-fill text-success fs-5 flex-shrink-0"></i>
                                            <span class="text-dark small" style="font-size: 11.5px; line-height: 1.35;">
                                                <strong>Đồng kiểm</strong> mở hộp kiểm tra ngoại quan máy
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="tz-cod-feature-box">
                                            <i class="bi bi-cpu-fill text-primary fs-5 flex-shrink-0"></i>
                                            <span class="text-dark small" style="font-size: 11.5px; line-height: 1.35;">
                                                <strong>Cắm sạc bật máy</strong> test màn hình & IMEI
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="tz-cod-feature-box">
                                            <i class="bi bi-receipt-cutoff text-danger fs-5 flex-shrink-0"></i>
                                            <span class="text-dark small" style="font-size: 11.5px; line-height: 1.35;">
                                                <strong>Phiếu xuất kho</strong> kèm bảo hành chính hãng
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lưu ý tiền mặt & chuyển khoản linh hoạt -->
                                <div class="mt-3 p-2.5 rounded-2 d-flex align-items-center gap-2" style="background: #f0fdf4; border: 1px dashed #86efac; font-size: 12px; color: #166534;">
                                    <i class="bi bi-info-circle-fill text-success flex-shrink-0"></i>
                                    <span>
                                        <strong>Linh hoạt:</strong> Quý khách có thể trả tiền mặt hoặc chuyển khoản trực tiếp cho shipper khi nhận hàng.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & MÃ KHUYẾN MÃI -->
            <div class="col-lg-5">
                <div class="tz-card p-4 mb-4 sticky-top" style="top: 80px; z-index: 10;">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-bag-check text-primary me-2"></i>Sản Phẩm Trong Đơn Hàng
                    </h4>

                    <!-- Danh sách item từ giỏ hàng -->
                    <div id="checkoutItemsList" class="mb-3" style="max-height: 280px; overflow-y: auto;">
                        <!-- Render JS / Database -->
                    </div>

                    <!-- Ô NHẬP MÃ GIẢM GIÁ -->
                    <div class="mb-3 pt-3 border-top">
                        <label class="form-label small fw-semibold"><i class="bi bi-tag text-primary me-1"></i>Mã Khuyến Mãi (Voucher)</label>
                        <div class="input-group mb-2">
                            <input type="text" id="voucherCodeInput" class="form-control text-uppercase font-monospace" placeholder="VD: BACK2SCHOOL">
                            <button type="button" class="btn btn-outline-primary fw-semibold" onclick="applyVoucher()">Áp dụng</button>
                        </div>
                        <div id="voucherFeedback" class="small mt-1"></div>

                        <!-- Clickable Voucher Chips (1 chạm để áp dụng) -->
                        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                            <span class="text-muted small" style="font-size: 11px;">Mã gợi ý:</span>
                            <span class="tz-voucher-chip" onclick="applyQuickVoucher('BACK2SCHOOL')" title="Nhấp để áp dụng mã giảm 10%">
                                <i class="bi bi-tag-fill"></i> BACK2SCHOOL (-10%)
                            </span>
                            <span class="tz-voucher-chip" onclick="applyQuickVoucher('TECHZONE500K')" title="Nhấp để áp dụng mã giảm 500k">
                                <i class="bi bi-tag-fill"></i> TECHZONE500K (-500k)
                            </span>
                        </div>
                    </div>

                    <!-- Bảng tính chi phí -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small">Tổng tiền hàng:</span>
                            <span class="fw-semibold text-dark" id="checkoutSubtotal">0 ₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small">Phí vận chuyển:</span>
                            <span class="fw-semibold text-dark" id="checkoutShipping">15.000 ₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none !important;">
                            <span class="text-success small fw-semibold">Chiết khấu Voucher:</span>
                            <span class="text-success fw-bold" id="checkoutDiscount">-0 ₫</span>
                        </div>

                        <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">TỔNG CỘNG (COD):</span>
                            <span class="fw-bold text-danger fs-4" id="checkoutFinalTotal">0 ₫</span>
                        </div>
                    </div>

                    <!-- NÚT HOÀN TẤT ĐẶT HÀNG COD -->
                    <button type="submit" class="btn btn-tz-cta w-100 py-3 mt-4 text-center justify-content-center fs-6 shadow">
                        <i class="bi bi-box-seam me-2"></i>ĐẶT HÀNG THANH TOÁN KHI NHẬN (COD)
                    </button>

                    <!-- Trust & Guarantee Badges -->
                    <div class="row g-2 mt-3 pt-2 text-center text-muted" style="font-size: 11px;">
                        <div class="col-6 d-flex align-items-center gap-1 justify-content-center">
                            <i class="bi bi-shield-check text-success"></i> 100% Chính hãng
                        </div>
                        <div class="col-6 d-flex align-items-center gap-1 justify-content-center">
                            <i class="bi bi-cpu text-primary"></i> Bảo hành Serial/IMEI
                        </div>
                        <div class="col-6 d-flex align-items-center gap-1 justify-content-center">
                            <i class="bi bi-box-seam text-success"></i> Đồng kiểm trước khi nhận
                        </div>
                        <div class="col-6 d-flex align-items-center gap-1 justify-content-center">
                            <i class="bi bi-arrow-repeat text-primary"></i> 1 đổi 1 trong 30 ngày
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ==============================================================================
     MODAL XÁC NHẬN ĐẶT HÀNG COD - UI/UX PRO MAX
     ============================================================================== -->
<div class="modal fade tz-cod-modal" id="modalConfirmCOD" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="tz-cod-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 42px; height: 42px; font-size: 20px;">
                        <i class="bi bi-box2-heart-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" style="font-size: 18px;">
                            Xác Nhận Đặt Hàng COD
                        </h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-white text-success fw-bold px-2 py-0.5" style="font-size: 11px;">
                                <i class="bi bi-cash-coin me-1"></i>THANH TOÁN KHI NHẬN HÀNG
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 bg-white">
                <!-- Thẻ tóm tắt thông tin đơn hàng -->
                <div class="rounded-3 p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                        <span class="text-secondary small">Mã đơn hàng:</span>
                        <strong class="font-monospace text-primary fs-6" id="codModalOrderId">DH8557</strong>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-secondary small">Người nhận:</span>
                        <span class="fw-semibold text-dark small" id="codModalReceiver">Nguyễn Văn An</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-secondary small">Số điện thoại:</span>
                        <span class="fw-semibold text-dark small font-monospace" id="codModalPhone">0912345678</span>
                    </div>

                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-secondary small flex-shrink-0 me-2">Giao đến:</span>
                        <span class="text-dark small text-end fw-medium" id="codModalAddress">123 Đường Láng, Đống Đa, Hà Nội</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-secondary small">Gói vận chuyển:</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="codModalShipping">Giao Hàng Tiêu Chuẩn (15.000 ₫)</span>
                    </div>
                </div>

                <!-- Box nổi bật tổng tiền mặt cần thanh toán -->
                <div class="p-3 rounded-3 text-center mb-3" style="background: #fff7ed; border: 1.5px solid #fdba74;">
                    <span class="text-secondary small d-block mb-1">Tổng tiền mặt cần chuẩn bị cho shipper:</span>
                    <div class="fw-bold text-danger fs-3 font-monospace" id="codModalFinalTotal">24.020.000 ₫</div>
                    <span class="text-muted small" style="font-size: 11px;">(Đã bao gồm tiền hàng, phí ship và giảm giá voucher)</span>
                </div>

                <!-- Box lưu ý giao nhận -->
                <div class="p-2.5 rounded-3 d-flex align-items-start gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 12px; color: #1e40af;">
                    <i class="bi bi-info-circle-fill text-primary fs-6 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <strong>Lưu ý giao nhận:</strong> Shipper sẽ gọi điện trước 15-30 phút khi giao. Quý khách vui lòng để ý điện thoại và được quyền đồng kiểm tra máy trước khi thanh toán.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light border-top px-4 py-3 d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                    <i class="bi bi-pencil me-1"></i>Kiểm tra lại
                </button>
                <button type="button" id="btnConfirmSubmitCOD" class="btn btn-success fw-bold px-4 shadow-sm" onclick="confirmAndSubmitCOD()">
                    <i class="bi bi-check2-circle me-1"></i>Xác Nhận Đặt Hàng Ngay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentShippingFee = 15000;
    let appliedDiscount = 0;
    let appliedVoucherCode = '';
    window.currentFinalTotalNum = 0;
    let generatedOrderId = '';
    let currentOrderPayload = null;
    const dbCartData = <?= json_encode($dbCartMap, JSON_UNESCAPED_UNICODE) ?>;

    // Bộ mã voucher mẫu
    const TECHZONE_VOUCHERS = {
        'BACK2SCHOOL': {
            loai: 'percent',
            giam: 10,
            max: 1500000,
            desc: 'Giảm 10% tối đa 1.5 triệu cho học sinh sinh viên'
        },
        'TECHZONE500K': {
            loai: 'amount',
            giam: 500000,
            max: 500000,
            desc: 'Giảm trực tiếp 500.000đ cho đơn từ 15 triệu'
        }
    };

    function updateShippingFee(fee) {
        currentShippingFee = Number(fee);
        document.getElementById('checkoutShipping').textContent = Number(fee).toLocaleString('vi-VN') + ' ₫';
        recalcTotal();
    }

    function getActiveCartItems() {
        let cart = {};
        if (typeof CartManager !== 'undefined' && CartManager.getCart) {
            cart = CartManager.getCart();
        }
        // Nếu trong localStorage không có nhưng trong CSDL có, dùng CSDL
        if (!cart || Object.keys(cart).length === 0) {
            if (dbCartData && Object.keys(dbCartData).length > 0) {
                cart = dbCartData;
            }
        }
        return cart;
    }

    function getCartSubtotal() {
        const cart = getActiveCartItems();
        let subtotal = 0;
        for (const [maSP, qty] of Object.entries(cart)) {
            const item = window.TECHZONE_CATALOG ? window.TECHZONE_CATALOG[maSP] : null;
            if (item && item.giaBan) {
                subtotal += (item.giaBan * Number(qty));
            }
        }
        return subtotal;
    }

    function recalcTotal() {
        const subtotal = getCartSubtotal();
        const finalTotal = Math.max(0, subtotal + currentShippingFee - appliedDiscount);
        window.currentFinalTotalNum = finalTotal;

        document.getElementById('checkoutSubtotal').textContent = Number(subtotal).toLocaleString('vi-VN') + ' ₫';
        document.getElementById('checkoutFinalTotal').textContent = Number(finalTotal).toLocaleString('vi-VN') + ' ₫';
    }

    function applyQuickVoucher(code) {
        document.getElementById('voucherCodeInput').value = code;
        applyVoucher();
    }

    function applyVoucher() {
        const code = document.getElementById('voucherCodeInput').value.trim().toUpperCase();
        const feedback = document.getElementById('voucherFeedback');
        const voucher = TECHZONE_VOUCHERS[code];
        const subtotal = getCartSubtotal();

        if (!code) {
            feedback.className = 'small mt-1 text-danger';
            feedback.textContent = 'Vui lòng nhập mã khuyến mãi!';
            return;
        }

        if (voucher) {
            if (voucher.loai === 'percent') {
                appliedDiscount = Math.min((subtotal * voucher.giam) / 100, voucher.max);
            } else {
                appliedDiscount = voucher.giam;
            }
            appliedVoucherCode = code;

            feedback.className = 'small mt-1 text-success fw-semibold';
            feedback.innerHTML = `<i class="bi bi-check-circle me-1"></i>Áp dụng mã <strong>${code}</strong> thành công! Giảm: -${Number(appliedDiscount).toLocaleString('vi-VN')} ₫`;

            document.getElementById('discountRow').style.setProperty('display', 'flex', 'important');
            document.getElementById('checkoutDiscount').textContent = `-${Number(appliedDiscount).toLocaleString('vi-VN')} ₫`;
            recalcTotal();
            if (window.TechZoneNotify && TechZoneNotify.success) {
                TechZoneNotify.success(`Áp dụng mã giảm giá ${code} thành công!`, 'Khuyến mãi');
            }
        } else {
            feedback.className = 'small mt-1 text-danger';
            feedback.textContent = 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn!';
            appliedDiscount = 0;
            appliedVoucherCode = '';
            document.getElementById('discountRow').style.setProperty('display', 'none', 'important');
            recalcTotal();
            if (window.TechZoneNotify && TechZoneNotify.error) {
                TechZoneNotify.error('Mã khuyến mãi không tồn tại hoặc đã hết hạn!', 'Khuyến mãi');
            }
        }
    }

    function renderCheckoutPage() {
        const cart = getActiveCartItems();
        const listContainer = document.getElementById('checkoutItemsList');
        listContainer.innerHTML = '';

        const keys = Object.keys(cart);
        if (keys.length === 0) {
            listContainer.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary"></i>
                <span>Giỏ hàng đang trống!</span>
                <div class="mt-2">
                    <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-sm btn-outline-primary">Mua sắm ngay</a>
                </div>
            </div>
        `;
            recalcTotal();
            return;
        }

        let hasOutOfStock = false;
        for (const [maSP, qty] of Object.entries(cart)) {
            const item = window.TECHZONE_CATALOG ? window.TECHZONE_CATALOG[maSP] : null;
            if (!item) continue;
            const tonKho = (typeof item.soLuongTon !== 'undefined') ? Number(item.soLuongTon) : 999;
            const isOut = tonKho <= 0;
            if (isOut) hasOutOfStock = true;

            const div = document.createElement('div');
            div.className = `d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom ${isOut ? 'bg-danger-subtle p-2 rounded' : ''}`;
            div.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <img src="${item.img}" style="width: 46px; height: 46px; object-fit: contain;" class="rounded border p-1 bg-white">
                <div>
                    <div class="fw-bold text-dark small" style="line-height: 1.3;">${item.tenSP}</div>
                    <small class="text-muted">SL: <strong>${qty}</strong> x ${Number(item.giaBan).toLocaleString('vi-VN')} ₫</small>
                    ${isOut ? '<span class="badge bg-danger text-white ms-1"><i class="bi bi-x-circle me-1"></i>HẾT HÀNG</span>' : `<span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 10px;">Kho: ${tonKho}</span>`}
                </div>
            </div>
            <div class="fw-bold ${isOut ? 'text-secondary text-decoration-line-through' : 'text-dark'} small text-end">${Number(item.giaBan * qty).toLocaleString('vi-VN')} ₫</div>
        `;
            listContainer.appendChild(div);
        }

        const submitBtn = document.querySelector('#checkoutForm button[type="submit"]');
        if (submitBtn) {
            if (hasOutOfStock) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                submitBtn.style.opacity = '0.65';
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>CÓ SẢN PHẨM HẾT HÀNG (KHÔNG THỂ ĐẶT)';
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled');
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                submitBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>TIẾN HÀNH ĐẶT HÀNG COD';
            }
        }

        recalcTotal();
    }

    // Xử lý tạo đơn hàng COD
    function handlePlaceOrder(event) {
        event.preventDefault();
        const cart = getActiveCartItems();
        if (Object.keys(cart).length === 0) {
            if (window.TechZoneNotify) {
                TechZoneNotify.warning("Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước.", "Thông báo");
            } else {
                alert("Giỏ hàng của bạn đang trống!");
            }
            return;
        }

        // KIỂM TRA TỒN KHO: Chặn ngay nếu có sản phẩm hết hàng hoặc vượt tồn kho
        for (const [maSP, qty] of Object.entries(cart)) {
            const item = window.TECHZONE_CATALOG ? window.TECHZONE_CATALOG[maSP] : null;
            const tonKho = (item && typeof item.soLuongTon !== 'undefined') ? Number(item.soLuongTon) : 999;
            const itemName = item?.tenSP || maSP;
            if (tonKho <= 0) {
                if (window.TechZoneNotify) {
                    TechZoneNotify.error(`Sản phẩm <strong>${itemName}</strong> hiện đã hết hàng trong kho. Vui lòng quay lại giỏ hàng xóa sản phẩm này trước khi đặt hàng!`, 'Hết Hàng');
                } else {
                    alert(`Sản phẩm ${itemName} hiện đã hết hàng trong kho!`);
                }
                return;
            }
            if (Number(qty) > tonKho) {
                if (window.TechZoneNotify) {
                    TechZoneNotify.warning(`Sản phẩm <strong>${itemName}</strong> chỉ còn ${tonKho} máy trong kho (bạn đang đặt ${qty} máy). Vui lòng điều chỉnh lại số lượng!`, 'Vượt Quá Tồn Kho');
                } else {
                    alert(`Sản phẩm ${itemName} chỉ còn ${tonKho} máy trong kho!`);
                }
                return;
            }
        }

        const hoTen = document.getElementById('tenNguoiNhan').value.trim();
        const sdt = document.getElementById('sdtNhanHang').value.trim();
        const diaChi = document.getElementById('diaChiGiaoHang').value.trim();
        const ghiChu = document.getElementById('ghiChuKhachHang').value.trim();

        if (!hoTen) {
            document.getElementById('tenNguoiNhan').focus();
            if (window.TechZoneNotify) TechZoneNotify.error("Vui lòng nhập họ tên người nhận!", "Thiếu thông tin");
            return;
        }
        if (!sdt) {
            document.getElementById('sdtNhanHang').focus();
            if (window.TechZoneNotify) TechZoneNotify.error("Vui lòng nhập số điện thoại nhận hàng!", "Thiếu thông tin");
            return;
        }
        if (!diaChi) {
            document.getElementById('diaChiGiaoHang').focus();
            if (window.TechZoneNotify) TechZoneNotify.error("Vui lòng nhập địa chỉ giao hàng!", "Thiếu thông tin");
            return;
        }

        // Tự sinh mã đơn hàng
        generatedOrderId = 'DH' + Math.floor(1000 + Math.random() * 9000);
        const subtotal = getCartSubtotal();
        const finalTotalNum = Math.max(0, subtotal + currentShippingFee - appliedDiscount);
        const finalTotalStr = Number(finalTotalNum).toLocaleString('vi-VN') + ' ₫';

        // Lấy tên gói vận chuyển
        let shippingName = 'Giao Hàng Tiêu Chuẩn (15.000 ₫)';
        if (currentShippingFee === 30000) {
            shippingName = 'Giao Hàng Nhanh 24H (30.000 ₫)';
        } else if (currentShippingFee === 50000) {
            shippingName = 'Giao Hỏa Tốc Nội Thành 2H (50.000 ₫)';
        }

        currentOrderPayload = {
            maDH: generatedOrderId,
            hoTen: hoTen,
            sdt: sdt,
            diaChi: diaChi,
            ghiChu: ghiChu,
            phuongThuc: 'COD',
            phiShip: currentShippingFee,
            tongTienHang: subtotal,
            tienGiamGia: appliedDiscount,
            tongThanhToan: finalTotalNum,
            cartItems: cart,
            maKH: '<?= htmlspecialchars($activeMaKH) ?>'
        };

        // Điền dữ liệu vào Modal Xác Nhận Đặt Hàng COD
        document.getElementById('codModalOrderId').textContent = generatedOrderId;
        document.getElementById('codModalReceiver').textContent = hoTen;
        document.getElementById('codModalPhone').textContent = sdt;
        document.getElementById('codModalAddress').textContent = diaChi;
        document.getElementById('codModalShipping').textContent = shippingName;
        document.getElementById('codModalFinalTotal').textContent = finalTotalStr;

        // Reset trạng thái nút bấm
        const btn = document.getElementById('btnConfirmSubmitCOD');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Xác Nhận Đặt Hàng Ngay';
        }

        // Mở Modal Xác Nhận COD UI/UX Pro Max
        const codModal = new bootstrap.Modal(document.getElementById('modalConfirmCOD'));
        codModal.show();
    }

    function confirmAndSubmitCOD() {
        if (!currentOrderPayload) return;
        const btn = document.getElementById('btnConfirmSubmitCOD');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Đang tạo đơn hàng...';
        }

        submitOrderPayload(currentOrderPayload, generatedOrderId);
    }

    function submitOrderPayload(payload, orderId) {
        fetch(window.BASE_URL + '/client/api-create-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                if (typeof CartManager !== 'undefined' && CartManager.xoaToanBoGio) {
                    CartManager.xoaToanBoGio();
                }
                if (window.TechZoneNotify && TechZoneNotify.success) {
                    TechZoneNotify.success(`Đặt hàng COD thành công! Mã đơn: ${orderId}`, 'Hoàn tất đơn hàng');
                }
                setTimeout(() => {
                    location.href = `<?= BASE_URL ?>/client/tra-cuu.php?id=${orderId}&kh=<?= urlencode($activeMaKH) ?>`;
                }, 1200);
            })
            .catch(() => {
                if (typeof CartManager !== 'undefined' && CartManager.xoaToanBoGio) {
                    CartManager.xoaToanBoGio();
                }
                location.href = `<?= BASE_URL ?>/client/tra-cuu.php?id=${orderId}&kh=<?= urlencode($activeMaKH) ?>`;
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCheckoutPage();

        // Đồng bộ trạng thái active của các thẻ lựa chọn Vận chuyển
        document.querySelectorAll('.tz-select-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const name = this.name;
                document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    const card = r.closest('.tz-select-card');
                    if (card) card.classList.remove('active');
                });
                const parentCard = this.closest('.tz-select-card');
                if (parentCard) parentCard.classList.add('active');
            });
        });
    });
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
