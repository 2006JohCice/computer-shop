<?php
// app/admin/bao-hanh/edit.php
$pageTitle = "Cập nhật phiếu bảo hành";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPBH = htmlspecialchars($_GET['maPhieuBH'] ?? $_GET['id'] ?? 'PBH202610-001');
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
                        <li class="breadcrumb-item"><a href="bao-hanh.php" class="text-decoration-none text-muted">Bảo hành</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Cập nhật tiến độ</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật phiếu bảo hành: <?= htmlspecialchars($maPBH) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật tiến độ xử lý kỹ thuật, linh kiện thay thế và thông báo cho khách hàng.</p>
            </div>
            <a href="bao-hanh.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="bao-hanh.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="maPhieuBH" value="<?= htmlspecialchars($maPBH) ?>">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Tiến Độ & Hiện Trạng Xử Lý (PhieuBaoHanh)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã phiếu BH (maPhieuBH)</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light" value="<?= htmlspecialchars($maPBH) ?>" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khách hàng (maKH)</label>
                            <input type="text" name="maKH" class="form-control font-monospace bg-light" value="KH01" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã sản phẩm (maSP)</label>
                            <input type="text" name="maSP" class="form-control font-monospace bg-light" value="SP01" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái bảo hành <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="Đang kiểm tra lỗi">Đang kiểm tra lỗi kỹ thuật</option>
                                <option value="Chờ linh kiện hãng">Chờ linh kiện chính hãng</option>
                                <option value="Đang sửa chữa">Đang xử lý sửa chữa</option>
                                <option value="Đã sửa xong">Đã sửa xong (Sẵn sàng trả khách)</option>
                                <option value="Đã trả khách">Đã hoàn tất trả máy cho khách</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chi phí phát sinh (nếu ngoài bảo hành) (₫)</label>
                            <input type="number" step="1000" name="chiPhiPhatSinh" class="form-control font-monospace" value="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Mô tả hiện tượng lỗi / Biện pháp xử lý (moTaLoi) <span class="text-danger">*</span></label>
                            <textarea name="moTaLoi" rows="3" class="form-control" required>Quạt tản nhiệt kêu to, máy nóng tự sập nguồn khi chạy đồ họa. Đã tiến hành vệ sinh cụm tản nhiệt, tra lại keo tản nhiệt gốm và test tải 100% qua FurMark.</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="bao-hanh.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu cập nhật phiếu bảo hành
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
