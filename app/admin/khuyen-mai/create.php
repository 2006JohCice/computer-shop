<?php
// app/admin/khuyen-mai/create.php
$pageTitle = "Tạo mã khuyến mãi mới";
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
                        <li class="breadcrumb-item"><a href="khuyen-mai.php" class="text-decoration-none text-muted">Khuyến mãi</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tạo mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tạo Chiến Dịch Khuyến Mãi Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập mã coupon voucher, tỷ lệ chiết khấu và thời hạn hiệu lực.</p>
            </div>
            <a href="khuyen-mai.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="khuyen-mai.php" method="POST">
            <input type="hidden" name="action" value="create">

            <!-- Box 1: Thông tin mã Khuyến Mãi -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-tag-fill me-2 text-primary"></i> Thông tin khuyến mãi Voucher
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khuyến mãi (maKM) <span class="text-danger">*</span></label>
                            <input type="text" name="maKM" class="form-control font-monospace text-uppercase fw-bold" 
                                   placeholder="VD: KM2026, TETBINGNGO" required>
                            <small class="text-muted">Mã áp dụng trên hóa đơn hoặc đơn hàng</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tên khuyến mãi (tenKM) <span class="text-danger">*</span></label>
                            <input type="text" name="tenKM" class="form-control" 
                                   placeholder="VD: Khuyến Mãi Đầu Xuân - Giảm Giá Laptop Gaming" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phần trăm giảm (phanTramGiam) (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="phanTramGiam" class="form-control" 
                                       placeholder="VD: 10 hoặc 15.5" required>
                                <span class="input-group-text bg-light font-monospace fw-bold">%</span>
                            </div>
                            <small class="text-muted">Nhập phần trăm từ 1% đến 100%</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú đối tượng áp dụng</label>
                            <input type="text" name="ghiChu" class="form-control" 
                                   placeholder="Áp dụng cho toàn bộ đơn hàng thanh toán trực tuyến">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Thời gian hiệu lực -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-range me-2 text-primary"></i> Thời gian áp dụng
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayBatDau" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayKetThuc" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khuyen-mai.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu chương trình khuyến mãi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
