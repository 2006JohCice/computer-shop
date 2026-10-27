<?php
// app/admin/hoa-don/create.php
$pageTitle = "Xuất hóa đơn bán hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDH = htmlspecialchars($_GET['maDH'] ?? $_GET['dh'] ?? 'DH01');
$autoMaHD = 'HD' . date('ymd') . rand(100, 999);
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
                        <li class="breadcrumb-item"><a href="hoa-don.php" class="text-decoration-none text-muted">Hóa đơn</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Xuất mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Xuất Hóa Đơn Bán Hàng Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Khởi tạo hóa đơn bán lẻ, áp dụng mã chiết khấu voucher và thông tin thanh toán.</p>
            </div>
            <a href="hoa-don.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="hoa-don.php" method="POST">
            <input type="hidden" name="action" value="create">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-receipt text-primary me-2"></i> Thông Tin Hóa Đơn Xuất
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã hóa đơn (maHD) <span class="text-danger">*</span></label>
                            <input type="text" name="maHD" class="form-control font-monospace text-uppercase fw-bold" value="<?= $autoMaHD ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã đơn hàng gốc (maDH) <span class="text-danger">*</span></label>
                            <input type="text" name="maDH" class="form-control font-monospace text-uppercase" value="<?= $maDH ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã voucher khuyến mãi (maKM)</label>
                            <input type="text" name="maKM" class="form-control font-monospace" value="KM_CHAOMUNG">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên khách hàng (tenKhachHang) <span class="text-danger">*</span></label>
                            <input type="text" name="tenKhachHang" class="form-control" value="Nguyễn Văn An" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" class="form-control font-monospace" value="0912 345 678" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Địa chỉ khách hàng (diaChiKhachHang) <span class="text-danger">*</span></label>
                            <input type="text" name="diaChiKhachHang" class="form-control" value="123 Đường Láng, Đống Đa, Hà Nội" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã số thuế KH (maSoThueKH - Nullable)</label>
                            <input type="text" name="maSoThueKH" class="form-control font-monospace" placeholder="VD: 0108928412-001">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tổng tiền hàng (tongTienHang) (₫) <span class="text-danger">*</span></label>
                            <input type="number" step="1000" name="tongTienHang" class="form-control font-monospace" value="37990000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giảm giá (donGiamGia) (₫)</label>
                            <input type="number" step="1000" name="donGiamGia" class="form-control font-monospace" value="1899500">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tiền thanh toán (soTienThanhToan) <span class="text-danger">*</span></label>
                            <input type="text" name="soTienThanhToan" class="form-control font-monospace fw-bold text-danger" value="36.090.500" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái thanh toán (trangThaiThanhToan)</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="Đã thanh toán" selected>Đã thanh toán (kiemTraThanhToanHopLe = TRUE)</option>
                                <option value="Chờ thanh toán">Chờ thanh toán</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú hóa đơn (ghiChu)</label>
                            <input type="text" name="ghiChu" class="form-control" placeholder="Ghi chú hóa đơn...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="hoa-don.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-printer me-1"></i> Xuất hóa đơn
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
