<?php
// app/admin/don-hang/create.php
$pageTitle = "Tạo đơn bán hàng mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Tự động tạo mã đơn theo phương thức taoMaDonHang()
$autoMaDH = 'DH' . date('ymd') . rand(10, 99);
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="don-hang.php" class="text-decoration-none text-muted">Đơn hàng</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tạo mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tạo Đơn Bán Hàng Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập thông tin khách mua, mặt hàng laptop, chi phí vận chuyển và phương thức giao nhận.</p>
            </div>
            <a href="don-hang.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="don-hang.php" method="POST">
            <input type="hidden" name="action" value="create">

            <!-- Box 1: Thông tin cơ bản đơn hàng -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-receipt-cutoff text-primary me-2"></i> Thông tin đơn hàng (DonHang)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Mã đơn hàng <span class="text-danger">*</span></label>
                            <input type="text" name="maDH" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= $autoMaDH ?>" required>
                            <small class="text-muted">Mã được tạo tự động</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Khách hàng <span class="text-danger">*</span></label>
                            <select name="maKH" class="form-select font-monospace" required>
                                <option value="KH01">KH01 - Nguyễn Văn An (0912 345 678)</option>
                                <option value="KH02">KH02 - Trần Thị Mai (VIP - 0987 654 321)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Mã khuyến mãi</label>
                            <select name="maKM" class="form-select font-monospace">
                                <option value="">-- Không áp dụng voucher --</option>
                                <option value="KM_CHAOMUNG">KM_CHAOMUNG (Giảm 5%)</option>
                                <option value="KM_BACKTOSCHOOL">KM_BACKTOSCHOOL (Giảm 10%)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ngày tạo <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayTao" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="phuongThucThanhToan" class="form-select" required>
                                <option value="Chuyển khoản VNPay / Ngân hàng" selected>Chuyển khoản VNPay / Ngân hàng</option>
                                <option value="Thanh toán khi nhận hàng (COD)">Thanh toán khi nhận hàng (COD)</option>
                                <option value="Thẻ tín dụng / Ghi nợ quốc tế">Thẻ tín dụng / Ghi nợ quốc tế</option>
                                <option value="Tiền mặt tại quầy TechZone">Tiền mặt tại quầy TechZone</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phương thức giao hàng <span class="text-danger">*</span></label>
                            <select name="phuongThucGiaoHang" class="form-select" required>
                                <option value="Giao hàng tiêu chuẩn tận nơi" selected>Giao hàng tiêu chuẩn tận nơi</option>
                                <option value="Giao hàng hỏa tốc 2H">Giao hàng hỏa tốc 2H</option>
                                <option value="Nhận trực tiếp tại showroom">Nhận trực tiếp tại showroom</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phí vận chuyển (phiVanChuyen) (₫)</label>
                            <input type="number" step="1000" name="phiVanChuyen" class="form-control font-monospace" value="50000" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tổng tiền hàng (tongTienHang) (₫)</label>
                            <input type="number" step="1000" name="tongTienHang" class="form-control font-monospace" value="35990000" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Trạng thái ban đầu (trangThaiDon)</label>
                            <select name="trangThaiDon" class="form-select">
                                <option value="Chờ duyệt" selected>Chờ duyệt</option>
                                <option value="Đã duyệt">Đã duyệt (duyetDon)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Thêm sản phẩm vào đơn (ChiTietDonHang) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-laptop text-primary me-2"></i> Sản Phẩm (themSanPhamVaoDon)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sản phẩm laptop (maSP) <span class="text-danger">*</span></label>
                            <select name="maSP" class="form-select" required>
                                <option value="SP01">SP01 - Dell XPS 13 Plus 9320 (37.990.000 ₫)</option>
                                <option value="SP02">SP02 - MacBook Pro 14 M3 Pro (49.990.000 ₫)</option>
                                <option value="SP03">SP03 - ASUS ROG Zephyrus G16 (44.990.000 ₫)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số lượng (soLuong) <span class="text-danger">*</span></label>
                            <input type="number" name="soLuong" min="1" value="1" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Đơn giá (donGia) (₫) <span class="text-danger">*</span></label>
                            <input type="number" name="donGia" step="1000" value="35990000" class="form-control font-monospace" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="don-hang.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu và tạo đơn hàng
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
