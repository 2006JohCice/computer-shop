<?php
// app/admin/nha-cung-cap/edit.php
$pageTitle = "Chỉnh sửa nhà cung cấp";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maNCC = $_GET['maNCC'] ?? 'NCC01';
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật đối tác: <?= htmlspecialchars($maNCC) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin pháp nhân theo biểu đồ thực thể NhaCungCap</p>
            </div>
            <a href="nha-cung-cap.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form action="nha-cung-cap.php" method="POST">
            <input type="hidden" name="action" value="update">

            <!-- Box 1: Pháp nhân doanh nghiệp (NhaCungCap) -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-building me-2 text-primary"></i> Thông tin pháp nhân & Liên hệ (NhaCungCap)
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <input type="text" name="maNCC" class="form-control font-monospace text-uppercase" 
                                   value="<?= htmlspecialchars($maNCC) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK) cố định</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tên công ty doanh nghiệp (tenCongTy) <span class="text-danger">*</span></label>
                            <input type="text" name="tenCongTy" class="form-control" 
                                   value="Công ty Cổ phần Thế Giới Số (Digiworld Corporation)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã số thuế doanh nghiệp (maSoThue) <span class="text-danger">*</span></label>
                            <input type="text" name="maSoThue" class="form-control font-monospace" 
                                   value="0302861742" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quốc gia / Quốc tịch (quocTich)</label>
                            <input type="text" name="quocTich" class="form-control" value="Việt Nam">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="text" name="soDienThoai" class="form-control" value="028 3929 0059" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ trụ sở chính (diaChiTruSo) <span class="text-danger">*</span></label>
                            <input type="text" name="diaChiTruSo" class="form-control" 
                                   value="195 Nguyễn Thái Bình, Phường 4, Tân Bình, TP.HCM" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái quan hệ cung ứng (trangThai) <span class="text-danger">*</span></label>
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
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
