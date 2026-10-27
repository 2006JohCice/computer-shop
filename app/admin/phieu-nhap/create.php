<?php
// app/admin/phieu-nhap/create.php
$pageTitle = "Lập phiếu nhập kho mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$autoMaPhieu = 'PN' . date('Ymd') . rand(10, 99);
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
                        <li class="breadcrumb-item"><a href="phieu-nhap.php" class="text-decoration-none text-muted">Phiếu nhập kho</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Lập phiếu</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Lập Phiếu Nhập Kho Laptop</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Khởi tạo chứng từ nhập hàng, đối chiếu thông tin nhà cung cấp và hóa đơn vận chuyển.</p>
            </div>
            <a href="phieu-nhap.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="phieu-nhap.php" method="POST">
            <input type="hidden" name="action" value="create">

            <!-- Box 1: Thông tin chung phiếu nhập -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-file-earmark-arrow-down-fill text-primary me-2"></i> Thông tin phiếu nhập kho (PhieuNhap)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã phiếu nhập (maPhieu) <span class="text-danger">*</span></label>
                            <input type="text" name="maPhieu" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= $autoMaPhieu ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <select name="maNCC" class="form-select" required>
                                <option value="NCC01">NCC01 - Digiworld (DGW)</option>
                                <option value="NCC02">NCC02 - Synnex FPT</option>
                                <option value="NCC03">NCC03 - Công ty Công Nghệ Viễn Sơn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Người nhập (nguoiNhap) <span class="text-danger">*</span></label>
                            <input type="text" name="nguoiNhap" class="form-control" value="Quản lý Nguyễn Tuấn" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày nhập (ngayNhap) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayNhap" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số hóa đơn NCC (soHoaDonNCC)</label>
                            <input type="text" name="soHoaDonNCC" class="form-control font-monospace" placeholder="VD: HD-DGW-9821">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tiền trả trước NCC (tongTienTraTruocNCC) (₫)</label>
                            <input type="number" step="1000" name="tongTienTraTruocNCC" class="form-control font-monospace" value="50000000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái thanh toán (trangThaiThanhToan)</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="Chưa thanh toán">Chưa thanh toán</option>
                                <option value="Thanh toán một phần">Thanh toán một phần</option>
                                <option value="Đã thanh toán" selected>Đã thanh toán toàn bộ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái nhập kho (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="Chờ kiểm hàng">Chờ kiểm hàng</option>
                                <option value="Đã nhập kho" selected>Đã nhập kho (xacNhanNhapKho)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú phiếu nhập (ghiChu)</label>
                            <textarea name="ghiChu" rows="2" class="form-control" placeholder="Nhập lô laptop bảo hành chính hãng, đợt hàng quý 4..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Mặt hàng nhập kho (ChiTietNhap) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-box-seam-fill text-primary me-2"></i> Chi Tiết Mặt Hàng (ChiTietNhap)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Sản phẩm laptop (maSP) <span class="text-danger">*</span></label>
                            <select name="maSP" class="form-select" required>
                                <option value="SP01">SP01 - Dell XPS 13 Plus 9320</option>
                                <option value="SP02">SP02 - MacBook Pro 14 M3 Pro</option>
                                <option value="SP03">SP03 - ASUS ROG Zephyrus G16</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số lượng nhập (soLuongNhap) <span class="text-danger">*</span></label>
                            <input type="number" step="1" min="1" name="soLuongNhap" value="5" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ghi chú dòng nhập (ghiChu)</label>
                            <input type="text" name="ghiChuChiTiet" class="form-control" placeholder="Nguyên seal thùng, mới 100%">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="phieu-nhap.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu và tạo phiếu nhập
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
