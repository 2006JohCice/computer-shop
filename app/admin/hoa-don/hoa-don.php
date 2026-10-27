<?php
// app/admin/hoa-don/hoa-don.php
$pageTitle = "Quản lý hóa đơn xuất";

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $maHD = htmlspecialchars($_POST['maHD'] ?? '');
        $successMsg = "Đã xuất hóa đơn thành công: <strong>$maHD</strong>";
    } elseif ($action === 'delete') {
        $maHD = htmlspecialchars($_POST['maHD'] ?? '');
        $successMsg = "Đã xóa hóa đơn: <strong>$maHD</strong>";
    }
}

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div><?= $successMsg ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Header trang -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Hóa Đơn Tài Chính & Bán Hàng</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý hóa đơn xuất bán lẻ, chiết khấu voucher khuyến mãi và in hóa đơn VAT điện tử.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-tz-primary"><i class="bi bi-plus-lg me-1"></i> Xuất hóa đơn mới</a>
                <button onclick="window.print()" class="btn btn-tz-outline"><i class="bi bi-printer me-1"></i> In danh sách</button>
            </div>
        </div>

        <!-- 4 Thẻ Metric -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG HÓA ĐƠN</div>
                        <div class="tz-metric-value">92</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ THANH TOÁN</div>
                        <div class="tz-metric-value text-success">89</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">DOANH THU THỰC NHẬN</div>
                        <div class="tz-metric-value text-primary" style="font-size: 20px;">2.080.000.000 ₫</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-info-light); color: var(--tz-info);">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG GIẢM GIÁ VOUCHER</div>
                        <div class="tz-metric-value text-danger" style="font-size: 20px;">52.170.000 ₫</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Hóa Đơn -->
        <div class="tz-card">
            <div class="tz-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h3 class="tz-card-title mb-0"><i class="bi bi-receipt text-primary me-2"></i>Danh Sách Hóa Đơn Xuất Bán</h3>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 form-control-sm" placeholder="Mã HĐ, tên khách, SĐT...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ HÓA ĐƠN (maHD)</th>
                            <th>MÃ ĐƠN HÀNG (maDH)</th>
                            <th>KHÁCH HÀNG</th>
                            <th>TIỀN HÀNG (tongTienHang)</th>
                            <th>GIẢM GIÁ (donGiamGia)</th>
                            <th>THỰC THANH TOÁN (soTienThanhToan)</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">HD01</span></td>
                            <td><a href="../don-hang/detail.php?id=DH01" class="badge bg-light text-dark border font-monospace">DH01</a></td>
                            <td>
                                <div class="fw-bold text-dark">Nguyễn Văn An</div>
                                <small class="text-muted">0912 345 678</small>
                            </td>
                            <td class="font-monospace">37.990.000 ₫</td>
                            <td><span class="text-danger fw-semibold font-monospace">-1.899.500 ₫ (5%)</span></td>
                            <td><span class="fw-bold text-primary font-monospace">36.140.500 ₫</span></td>
                            <td><span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đã thanh toán</span></td>
                            <td class="text-end">
                                <a href="detail.php?id=HD01" class="btn btn-sm btn-tz-outline" title="Xem chi tiết"><i class="bi bi-eye"></i> Chi tiết</a>
                                <a href="detail.php?id=HD01" class="btn btn-sm btn-outline-primary" title="In hóa đơn"><i class="bi bi-printer"></i> In</a>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">HD02</span></td>
                            <td><a href="../don-hang/detail.php?id=DH02" class="badge bg-light text-dark border font-monospace">DH02</a></td>
                            <td>
                                <div class="fw-bold text-dark">Trần Thị Mai <span class="badge bg-warning text-dark ms-1">VIP</span></div>
                                <small class="text-muted">0987 654 321</small>
                            </td>
                            <td class="font-monospace">52.480.000 ₫</td>
                            <td><span class="text-danger fw-semibold font-monospace">-5.248.000 ₫ (10%)</span></td>
                            <td><span class="fw-bold text-primary font-monospace">47.312.000 ₫</span></td>
                            <td><span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đã thanh toán</span></td>
                            <td class="text-end">
                                <a href="detail.php?id=HD02" class="btn btn-sm btn-tz-outline" title="Xem chi tiết"><i class="bi bi-eye"></i> Chi tiết</a>
                                <a href="detail.php?id=HD02" class="btn btn-sm btn-outline-primary" title="In hóa đơn"><i class="bi bi-printer"></i> In</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
