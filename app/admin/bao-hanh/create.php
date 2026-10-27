<?php
// app/admin/bao-hanh/create.php
$pageTitle = "Tiếp nhận phiếu bảo hành mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$autoMaPBH = 'PBH' . date('Ym') . '-' . rand(100, 999);
$defaultSerial = htmlspecialchars($_GET['serial'] ?? '');
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tiếp nhận mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tiếp Nhận Phiếu Bảo Hành Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Ghi nhận yêu cầu sửa chữa, mô tả lỗi phần cứng và phân loại bảo hành.</p>
            </div>
            <a href="bao-hanh.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="bao-hanh.php" method="POST">
            <input type="hidden" name="action" value="create">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-shield-plus text-primary me-2"></i> Thông Tin Phiếu Bảo Hành (PhieuBaoHanh)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã phiếu BH <span class="text-danger">*</span></label>
                            <input type="text" name="maPhieuBH" class="form-control font-monospace text-uppercase fw-bold" value="<?= $autoMaPBH ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Khách hàng <span class="text-danger">*</span></label>
                            <select name="maKH" class="form-select" required>
                                <option value="KH01">KH01 - Nguyễn Văn An (0912 345 678)</option>
                                <option value="KH02">KH02 - Trần Thị Mai (VIP - 0987 654 321)</option>
                            </select>
                            <small class="text-muted">Tìm kiếm theo tên hoặc SĐT khách hàng</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sản phẩm laptop <span class="text-danger">*</span></label>
                            <select name="maSP" class="form-select" required>
                                <option value="SP01">SP01 - Dell XPS 13 Plus 9320</option>
                                <option value="SP02" selected>SP02 - Asus ROG Zephyrus G14</option>
                                <option value="SP03">SP03 - MacBook Pro 14 M3 Pro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã Serial / IMEI máy</label>
                            <input type="text" name="serial" class="form-control font-monospace" value="<?= $defaultSerial ?: 'ASUS-G14-2024-001' ?>" placeholder="VD: ASUS-G14-2024-001">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày tiếp nhận (ngayTiepNhan) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayTiepNhan" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả hiện tượng lỗi (moTaLoi) <span class="text-danger">*</span></label>
                            <textarea name="moTaLoi" rows="3" class="form-control" placeholder="Mô tả chi tiết triệu chứng lỗi: quạt tản nhiệt kêu, màn hình chớp tắt, không lên nguồn..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái tiếp nhận (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="Đang kiểm tra lỗi" selected>Đang kiểm tra lỗi kỹ thuật</option>
                                <option value="Chờ linh kiện hãng">Chờ gửi hãng / linh kiện thay thế</option>
                                <option value="Đang sửa chữa">Đang xử lý sửa chữa</option>
                                <option value="Đã sửa xong">Đã hoàn thành sửa chữa</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kỹ thuật viên phụ trách tiếp nhận</label>
                            <input type="text" name="kyThuatVien" class="form-control" value="KTV Phạm Tiến Đạt">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="bao-hanh.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu phiếu tiếp nhận bảo hành
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
