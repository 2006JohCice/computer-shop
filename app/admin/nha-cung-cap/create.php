<?php
// app/views/admin/nha-cung-cap/create.php
$pageTitle = "Thêm nhà cung cấp mới";
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
                        <li class="breadcrumb-item"><a href="nha-cung-cap.php" class="text-decoration-none text-muted">Nhà cung cấp</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm nhà cung cấp mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập thông tin đối tác cung ứng và đầu mối nhập kho laptop chính hãng</p>
            </div>
            <a href="nha-cung-cap.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form action="nha-cung-cap.php" method="POST">
            <!-- Box 1: Pháp nhân doanh nghiệp -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-building me-2 text-primary"></i> Thông tin pháp nhân & Doanh nghiệp
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <input type="text" name="maNCC" class="form-control font-monospace text-uppercase" 
                                   placeholder="VD: NCC01, NCC-DGW" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tên công ty doanh nghiệp (tenCongTy) <span class="text-danger">*</span></label>
                            <input type="text" name="tenCongTy" class="form-control" 
                                   placeholder="VD: Công ty Cổ phần Thế Giới Số (Digiworld)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã số thuế doanh nghiệp (maSoThue) <span class="text-danger">*</span></label>
                            <input type="text" name="maSoThue" class="form-control font-monospace" 
                                   placeholder="VD: 0302861742" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quốc gia / Quốc tịch (quocTich)</label>
                            <input type="text" name="quocTich" class="form-control" value="Việt Nam">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="text" name="soDienThoai" class="form-control" placeholder="VD: 028 3929 0059" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ trụ sở chính (diaChiTruSo) <span class="text-danger">*</span></label>
                            <input type="text" name="diaChiTruSo" class="form-control" 
                                   placeholder="Số nhà, đường, phường, quận, tỉnh/TP" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 3: Hạn mức công nợ & Trạng thái -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-shield-check me-2 text-primary"></i> Chính sách công nợ & Trạng thái
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Thời hạn thanh toán công nợ</label>
                            <select name="payment_term" class="form-select">
                                <option value="immediate">Thanh toán ngay khi giao hàng (COD/CK)</option>
                                <option value="15days" selected>Gối đầu 15 ngày</option>
                                <option value="30days">Gối đầu 30 ngày</option>
                                <option value="45days">Gối đầu 45 ngày</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hạn mức nợ tối đa (VNĐ)</label>
                            <input type="text" name="credit_limit" class="form-control" placeholder="VD: 500.000.000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái quan hệ cung ứng (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>Đang hợp tác nhập hàng</option>
                                <option value="0">Tạm dừng giao dịch</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu / Hủy cuối trang -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="nha-cung-cap.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu nhà cung cấp
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
