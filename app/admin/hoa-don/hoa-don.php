<?php
// app/admin/hoa-don/hoa-don.php
$pageTitle = "Quản lý hóa đơn xuất bán";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê Metrics
$sqlTongHD = "SELECT COUNT(*) as total FROM hoadon";
$totalHD = $conn->query($sqlTongHD)->fetch_assoc()['total'] ?? 0;

$sqlDaTT = "SELECT COUNT(*) as total FROM hoadon WHERE trangThaiThanhToan = 'DA_THANH_TOAN'";
$totalDaTT = $conn->query($sqlDaTT)->fetch_assoc()['total'] ?? 0;

$sqlChuaTT = "SELECT COUNT(*) as total FROM hoadon WHERE trangThaiThanhToan != 'DA_THANH_TOAN'";
$totalChuaTT = $conn->query($sqlChuaTT)->fetch_assoc()['total'] ?? 0;

$sqlTongTien = "SELECT IFNULL(SUM(soTienThanhToan), 0) as total FROM hoadon";
$totalTien = $conn->query($sqlTongTien)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý xóa hóa đơn (POST Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $maHDDel = trim($_POST['maHD'] ?? '');
    if ($conn->query("DELETE FROM hoadon WHERE maHD = '$maHDDel'")) {
        echo "<script>window.location.href='hoa-don.php';</script>";
        exit;
    } else {
        $errors[] = "Xóa hóa đơn thất bại: " . $conn->error;
    }
}

// 3. Tìm kiếm & Lọc dữ liệu
$search = trim($_GET['search'] ?? '');
$filterTT = trim($_GET['trangThaiThanhToan'] ?? '');

$sql = "SELECT * FROM hoadon WHERE 1 = 1";

if ($search !== "") {
    $sql .= " AND (maHD LIKE '%$search%' OR maDH LIKE '%$search%' OR tenKhachHang LIKE '%$search%' OR soDienThoai LIKE '%$search%' OR maSoThueKH LIKE '%$search%')";
}
if ($filterTT !== "") {
    $sql .= " AND trangThaiThanhToan = '$filterTT'";
}

$sql .= " ORDER BY ngayXuat DESC";
$resultHD = $conn->query($sql);
$totalRows = $resultHD ? $resultHD->num_rows : 0;
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header trang -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Hóa đơn</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Hóa Đơn Tài Chính & Bán Hàng</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý hóa đơn xuất bán lẻ, hóa đơn VAT điện tử và đối soát chứng từ thanh toán.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-receipt-cutoff me-1"></i>Xuất hóa đơn mới
                </a>
            </div>
        </div>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger border-danger-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">Vui lòng kiểm tra lại:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <!-- 4 Thẻ Metric -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG HÓA ĐƠN XUẤT</div>
                        <div class="tz-metric-value text-primary"><?= $totalHD ?></div>
                        <small class="text-muted"><i class="bi bi-receipt me-1"></i>Chứng từ tài chính</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ THANH TOÁN</div>
                        <div class="tz-metric-value text-success"><?= $totalDaTT ?></div>
                        <small class="text-success"><i class="bi bi-check2-circle me-1"></i>Tiền đã vào tài khoản/két</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">CHƯA THANH TOÁN</div>
                        <div class="tz-metric-value text-warning"><?= $totalChuaTT ?></div>
                        <small class="text-warning"><i class="bi bi-clock-history me-1"></i>Đang chờ đối chiếu</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG GIÁ TRỊ HÓA ĐƠN</div>
                        <div class="tz-metric-value text-danger"><?= number_format($totalTien, 0, ',', '.') ?> ₫</div>
                        <small class="text-danger"><i class="bi bi-cash-stack me-1"></i>Tổng doanh thu xuất</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Danh Sách & Bộ Lọc -->
        <div class="tz-card">
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-receipt-cutoff text-primary"></i>Danh Sách Hóa Đơn Bán Hàng
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> hóa đơn
                    </span>
                </div>

                <form action="hoa-don.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 380px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Mã HĐ, mã ĐH, tên khách, MST...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 180px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-credit-card text-info"></i>
                        </span>
                        <select name="trangThaiThanhToan" class="form-select border-start-0 ps-2">
                            <option value="">Thanh toán</option>
                            <option value="DA_THANH_TOAN" <?= $filterTT === 'DA_THANH_TOAN' ? 'selected' : '' ?>>Đã thanh toán</option>
                            <option value="CHUA_THANH_TOAN" <?= $filterTT === 'CHUA_THANH_TOAN' ? 'selected' : '' ?>>Chưa thanh toán</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="hoa-don.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th style="width: 140px;">MÃ HÓA ĐƠN</th>
                            <th style="width: 120px;">ĐƠN HÀNG</th>
                            <th>NGÀY XUẤT</th>
                            <th>KHÁCH HÀNG</th>
                            <th>SỐ ĐIỆN THOẠI & MST</th>
                            <th class="text-end">SỐ TIỀN</th>
                            <th class="text-center">THANH TOÁN</th>
                            <th class="text-end" style="width: 110px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultHD && $resultHD->num_rows > 0) {
                            while ($row = $resultHD->fetch_assoc()) {
                                $maHD = $row["maHD"];
                                $maDH = $row["maDH"];
                                $ngayXuat = $row["ngayXuat"];
                                $tenKH = $row["tenKhachHang"];
                                $sdt = $row["soDienThoai"];
                                $mst = $row["maSoThueKH"];
                                $soTien = $row["soTienThanhToan"];
                                $trangThai = $row["trangThaiThanhToan"];
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <a href="detail.php?id=<?= urlencode($maHD) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold text-decoration-none">
                                            <?= htmlspecialchars($maHD) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="../don-hang/detail.php?id=<?= urlencode($maDH) ?>" class="badge bg-light text-dark border font-monospace text-decoration-none" title="Xem đơn hàng gốc">
                                            <i class="bi bi-box-arrow-up-right me-1 text-primary"></i><?= htmlspecialchars($maDH) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($ngayXuat)) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenKH) ?></div>
                                    </td>
                                    <td>
                                        <div><span class="font-monospace text-primary"><?= htmlspecialchars($sdt) ?></span></div>
                                        <?php if (!empty($mst)): ?>
                                            <small class="text-muted">MST: <?= htmlspecialchars($mst) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger"><?= number_format($soTien, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($trangThai === 'DA_THANH_TOAN'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2-circle me-1"></i>Đã thanh toán</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-clock-history me-1"></i>Chưa thanh toán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="detail.php?id=<?= urlencode($maHD) ?>" class="btn btn-sm btn-outline-info" title="Xem & In hóa đơn"><i class="bi bi-printer"></i></a>
                                        <form method="POST" action="hoa-don.php" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="maHD" value="<?= htmlspecialchars($maHD) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                data-tz-confirm="Bạn có chắc muốn xóa chứng từ hóa đơn <?= htmlspecialchars($maHD) ?>?"
                                                data-tz-title="Xác nhận xóa hóa đơn">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy hóa đơn nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có hóa đơn nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="hoa-don.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Xuất hóa đơn mới
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
