<?php
// app/admin/serial/create.php
$pageTitle = "Thêm Serial / IMEI Laptop mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
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
                        <li class="breadcrumb-item"><a href="serial.php" class="text-decoration-none text-muted">Serial / IMEI</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm Serial / IMEI Máy Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Đăng ký mã định danh vật lý, liên kết sản phẩm, thời hạn bảo hành và trạng thái lưu kho.</p>
            </div>
            <a href="serial.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="serial.php" method="POST">
            <input type="hidden" name="action" value="create">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-upc-scan text-primary me-2"></i> Thông Tin Định Danh Thiết Bị (Serial / IMEI)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã Serial / IMEI <span class="text-danger">*</span></label>
                            <input type="text" name="maSerial_IMEI" class="form-control font-monospace text-uppercase fw-bold" 
                                   placeholder="VD: SN-DELL-9320-2026-088" required>
                            <small class="text-muted">Hệ thống sẽ tự động kiểm tra tính hợp lệ của mã Serial này</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thuộc sản phẩm laptop <span class="text-danger">*</span></label>
                            <select name="maSP" class="form-select" required>
                                <option value="SP01">SP01 - Dell XPS 13 Plus 9320</option>
                                <option value="SP02">SP02 - MacBook Pro 14 M3 Pro</option>
                                <option value="SP03">SP03 - ASUS ROG Zephyrus G16</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ID Dòng chi tiết nhập kho (idChiTietNhap) <span class="text-danger">*</span></label>
                            <input type="number" name="idChiTietNhap" class="form-control font-monospace" value="1" required>
                            <small class="text-muted">Khóa ngoại liên kết tới bảng ChiTietNhap</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái máy (trangThai) <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="Trong kho" selected>Trong kho (Sẵn sàng bán)</option>
                                <option value="Đã bán">Đã bán</option>
                                <option value="Đang bảo hành">Đang bảo hành</option>
                                <option value="Lỗi đổi trả">Lỗi đổi trả</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ID Dòng chi tiết đơn hàng</label>
                            <input type="number" name="idChiTietDonHang" class="form-control font-monospace" placeholder="Bỏ trống nếu máy chưa xuất bán">
                            <small class="text-muted">Để trống nếu máy đang trong kho chưa xuất bán</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạn bảo hành (hanBaoHanh)</label>
                            <input type="datetime-local" name="hanBaoHanh" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú máy (ghiChu)</label>
                            <textarea name="ghiChu" rows="2" class="form-control" placeholder="Tình trạng ngoại quan, tem nhà phân phối..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="serial.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu số Serial / IMEI
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
