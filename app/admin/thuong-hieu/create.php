<?php
// app/admin/thuong-hieu/create.php
$pageTitle = "Thêm thương hiệu đối tác mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="thuong-hieu.php" class="text-decoration-none text-muted">Thương hiệu</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm thương hiệu mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Đăng ký hãng sản xuất hoặc đối tác ủy quyền phân phối vào hệ thống TECHZONE</p>
            </div>
            <a href="thuong-hieu.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form action="thuong-hieu.php" method="POST">
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Thông tin cơ bản thương hiệu (ThuongHieu)
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã thương hiệu (maTH) <span class="text-danger">*</span></label>
                            <input type="text" name="maTH" class="form-control font-monospace text-uppercase" 
                                   placeholder="VD: DELL, ASUS, MSI..." required>
                            <small class="text-muted">Khóa chính (PK) viết tắt</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Tên thương hiệu (tenTH) <span class="text-danger">*</span></label>
                            <input type="text" name="tenTH" class="form-control" 
                                   placeholder="VD: Dell Technologies, ASUStek Inc." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Xuất xứ / Quốc gia (xuatXu) <span class="text-danger">*</span></label>
                            <input type="text" name="xuatXu" class="form-control" 
                                   placeholder="VD: Hoa Kỳ, Đài Loan, Nhật Bản..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đường dẫn Logo / Biểu tượng (logo)</label>
                            <input type="text" name="logo" class="form-control" 
                                   placeholder="VD: assets/logo/dell.png hoặc bi-laptop">
                            <small class="text-muted">Đường dẫn tệp hình ảnh hoặc tên icon Bootstrap</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái hợp tác (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>1 - Đang hợp tác phân phối</option>
                                <option value="0">0 - Ngừng hợp tác</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-file-text me-2 text-primary"></i> Giới thiệu & Mô tả hãng (moTa)
                </div>
                <div class="card-body-modern">
                    <div class="mb-0">
                        <label class="form-label">Mô tả tóm tắt thương hiệu (moTa)</label>
                        <textarea name="moTa" rows="3" class="form-control" 
                                  placeholder="Nhập thông tin giới thiệu, lịch sử phát triển hoặc điểm nổi bật của dải sản phẩm laptop..."></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="thuong-hieu.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thương hiệu
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
