<?php
// app/admin/don-hang/edit.php
$pageTitle = "Chỉnh sửa đơn hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDH = htmlspecialchars($_GET['id'] ?? $_GET['maDH'] ?? 'DH01');
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
                        <li class="breadcrumb-item"><a href="don-hang.php" class="text-decoration-none text-muted">Đơn hàng</a></li>
                        <li class="breadcrumb-item"><a href="detail.php?id=<?= urlencode($maDH) ?>" class="text-decoration-none text-muted"><?= $maDH ?></a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Chỉnh sửa đơn hàng #<?= htmlspecialchars($maDH) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật phương thức giao nhận, chi phí vận chuyển và trạng thái xử lý đơn hàng.</p>
            </div>
            <a href="detail.php?id=<?= urlencode($maDH) ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="don-hang.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="maDH" value="<?= htmlspecialchars($maDH) ?>">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Thông tin đơn hàng (DonHang)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã đơn hàng (maDH)</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light" value="<?= htmlspecialchars($maDH) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khách hàng (maKH) <span class="text-danger">*</span></label>
                            <input type="text" name="maKH" class="form-control font-monospace" value="KH01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khuyến mãi áp dụng</label>
                            <input type="text" name="maKM" class="form-control font-monospace" value="KM_CHAOMUNG">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="phuongThucThanhToan" class="form-select">
                                <option value="Chuyển khoản VNPay / Ngân hàng" selected>Chuyển khoản VNPay / Ngân hàng</option>
                                <option value="Thanh toán khi nhận hàng (COD)">Thanh toán khi nhận hàng (COD)</option>
                                <option value="Thẻ tín dụng / Ghi nợ quốc tế">Thẻ tín dụng / Ghi nợ quốc tế</option>
                                <option value="Tiền mặt tại quầy TechZone">Tiền mặt tại quầy TechZone</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phương thức giao hàng <span class="text-danger">*</span></label>
                            <select name="phuongThucGiaoHang" class="form-select">
                                <option value="Giao hàng tiêu chuẩn tận nơi" selected>Giao hàng tiêu chuẩn tận nơi</option>
                                <option value="Giao hàng hỏa tốc 2H">Giao hàng hỏa tốc 2H</option>
                                <option value="Nhận trực tiếp tại showroom">Nhận trực tiếp tại showroom</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phí vận chuyển (phiVanChuyen) (₫)</label>
                            <input type="number" step="1000" name="phiVanChuyen" class="form-control font-monospace" value="50000" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tổng tiền hàng (tongTienHang) (₫)</label>
                            <input type="number" step="1000" name="tongTienHang" class="form-control font-monospace" value="37990000" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Trạng thái đơn (trangThaiDon)</label>
                            <select name="trangThaiDon" class="form-select">
                                <option value="Chờ duyệt" selected>Chờ duyệt</option>
                                <option value="Đã duyệt">Đã duyệt</option>
                                <option value="Đang giao">Đang giao</option>
                                <option value="Hoàn thành">Hoàn thành</option>
                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="detail.php?id=<?= urlencode($maDH) ?>" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi đơn hàng
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
