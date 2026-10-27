<?php
// app/admin/phieu-nhap/edit.php
$pageTitle = "Chỉnh sửa phiếu nhập kho";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPhieu = htmlspecialchars($_GET['id'] ?? $_GET['maPhieu'] ?? 'PN20261001');
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
                        <li class="breadcrumb-item"><a href="phieu-nhap.php" class="text-decoration-none text-muted">Phiếu nhập</a></li>
                        <li class="breadcrumb-item"><a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="text-decoration-none text-muted"><?= $maPhieu ?></a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Sửa phiếu nhập: <?= htmlspecialchars($maPhieu) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin phiếu nhập, đối chiếu chứng từ và ghi chú giải trình.</p>
            </div>
            <a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="phieu-nhap.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="maPhieu" value="<?= htmlspecialchars($maPhieu) ?>">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Chỉnh sửa thông tin phiếu nhập kho
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã phiếu (maPhieu)</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light" value="<?= htmlspecialchars($maPhieu) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <select name="maNCC" class="form-select" required>
                                <option value="NCC01" selected>NCC01 - Digiworld (DGW)</option>
                                <option value="NCC02">NCC02 - Synnex FPT</option>
                                <option value="NCC03">NCC03 - Công ty Công Nghệ Viễn Sơn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Người nhập (nguoiNhap) <span class="text-danger">*</span></label>
                            <input type="text" name="nguoiNhap" class="form-control" value="Quản lý Nguyễn Tuấn" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số hóa đơn NCC (soHoaDonNCC)</label>
                            <input type="text" name="soHoaDonNCC" class="form-control font-monospace" value="HD-DGW-9821">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tiền trả trước NCC (tongTienTraTruocNCC) (₫)</label>
                            <input type="number" step="1000" name="tongTienTraTruocNCC" class="form-control font-monospace" value="100000000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Trạng thái thanh toán (trangThaiThanhToan)</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="Đã thanh toán" selected>Đã thanh toán</option>
                                <option value="Thanh toán một phần">Thanh toán một phần</option>
                                <option value="Chưa thanh toán">Chưa thanh toán</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái nhập kho (trangThai) <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="Đã nhập kho" selected>Đã nhập kho</option>
                                <option value="Chờ duyệt kho">Chờ duyệt kho</option>
                                <option value="Đang kiểm tra lỗi">Đang kiểm tra lỗi</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lý do điều chỉnh (lyDo)</label>
                            <input type="text" name="lyDo" class="form-control" placeholder="VD: Cập nhật lại số seri hoặc điều chỉnh số lượng...">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú phiếu nhập (ghiChu)</label>
                            <textarea name="ghiChu" rows="2" class="form-control">Lô laptop văn phòng và gaming nhập khẩu nguyên chiếc từ Digiworld quý 4.</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi phiếu nhập
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
