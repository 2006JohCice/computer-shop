<?php
// app/admin/serial/edit.php
$pageTitle = "Chỉnh sửa trạng thái Serial";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maSerial = htmlspecialchars($_GET['serial'] ?? $_GET['maSerial_IMEI'] ?? 'DELL-XPS-9320-001');
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật Serial: <?= htmlspecialchars($maSerial) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật trạng thái vòng đời thiết bị, xuất kho và thời hạn bảo hành.</p>
            </div>
            <a href="serial.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="serial.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="maSerial_IMEI" value="<?= htmlspecialchars($maSerial) ?>">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Trạng Thái Thiết Bị (SerialSanPham)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã Serial / IMEI (maSerial_IMEI)</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light" value="<?= htmlspecialchars($maSerial) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái máy (trangThai) <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="Trong kho">Trong kho (Chưa bán)</option>
                                <option value="Đã bán">Đã bán cho khách</option>
                                <option value="Đang bảo hành">Đang tiếp nhận bảo hành</option>
                                <option value="Lỗi đổi trả">Lỗi đổi trả nhà sản xuất</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày xuất kho (ngayXuatKho)</label>
                            <input type="datetime-local" name="ngayXuatKho" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạn bảo hành (hanBaoHanh)</label>
                            <input type="datetime-local" name="hanBaoHanh" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú cập nhật (ghiChu)</label>
                            <textarea name="ghiChu" rows="2" class="form-control" placeholder="Cập nhật lịch sử bảo hành, tình trạng thân vỏ..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="serial.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi Serial
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
