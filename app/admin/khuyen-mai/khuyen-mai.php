<?php
// app/admin/khuyen-mai/khuyen-mai.php
$pageTitle = "Quản lý khuyến mãi Voucher";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê Metrics
$sqlTongKM = "SELECT COUNT(*) as total FROM khuyenmai";
$totalKM = $conn->query($sqlTongKM)->fetch_assoc()['total'] ?? 0;

$sqlDangChay = "SELECT COUNT(*) as total FROM khuyenmai WHERE trangThai = 1 AND NOW() BETWEEN ngayBatDau AND ngayKetThuc";
$totalDangChay = $conn->query($sqlDangChay)->fetch_assoc()['total'] ?? 0;

$sqlHetHan = "SELECT COUNT(*) as total FROM khuyenmai WHERE trangThai = 0 OR ngayKetThuc < NOW()";
$totalHetHan = $conn->query($sqlHetHan)->fetch_assoc()['total'] ?? 0;

$sqlTienGiam = "SELECT IFNULL(SUM(tienGiamGia), 0) as total FROM donhang WHERE maKM IS NOT NULL";
$totalTienGiam = $conn->query($sqlTienGiam)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý Form Action (Xóa khuyến mãi)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $maKM = trim($_POST['maKM'] ?? '');
    
    // Gỡ liên kết maKM khỏi danhmuc và donhang trước khi xóa
    $conn->query("UPDATE danhmuc SET maKM = NULL WHERE maKM = '$maKM'");
    $conn->query("UPDATE donhang SET maKM = NULL WHERE maKM = '$maKM'");

    $sqlDel = "DELETE FROM khuyenmai WHERE maKM = '$maKM'";
    if ($conn->query($sqlDel)) {
        echo "<script>window.location.href='khuyen-mai.php';</script>";
        exit;
    } else {
        $errors[] = "Xóa khuyến mãi thất bại: " . $conn->error;
    }
}

// 3. Tìm kiếm & Lọc dữ liệu
$search = trim($_GET['search'] ?? '');
$filterLoai = trim($_GET['loaiKM'] ?? '');
$filterTrangThai = trim($_GET['trangThai'] ?? '');

$sql = "SELECT * FROM khuyenmai WHERE 1 = 1";

if ($search !== "") {
    $sql .= " AND (maKM LIKE '%$search%' OR tenKM LIKE '%$search%')";
}
if ($filterLoai !== "") {
    $sql .= " AND loaiKM = '$filterLoai'";
}
if ($filterTrangThai !== "") {
    $sql .= " AND trangThai = '$filterTrangThai'";
}

$sql .= " ORDER BY trangThai DESC, ngayKetThuc DESC";
$resultKM = $conn->query($sql);
$totalRows = $resultKM ? $resultKM->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Khuyến mãi</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Chương Trình Khuyến Mãi & Voucher</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý mã giảm giá, trợ giá cho từng dòng sản phẩm và chiết khấu kích cầu đơn hàng.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-tag-fill me-1"></i>Tạo mã khuyến mãi mới
            </a>
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
                        <div class="tz-metric-title">TỔNG VOUCHER / KM</div>
                        <div class="tz-metric-value text-primary"><?= $totalKM ?></div>
                        <small class="text-muted"><i class="bi bi-ticket-perforated me-1"></i>Chiến dịch khuyến mãi</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG HIỆU LỰC</div>
                        <div class="tz-metric-value text-success"><?= $totalDangChay ?></div>
                        <small class="text-success"><i class="bi bi-lightning-charge me-1"></i>Khách đang áp dụng</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TẠM DỪNG / HẾT HẠN</div>
                        <div class="tz-metric-value text-secondary"><?= $totalHetHan ?></div>
                        <small class="text-muted"><i class="bi bi-clock-history me-1"></i>Đã quá thời gian</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG CHIẾT KHẤU ĐÃ GIẢM</div>
                        <div class="tz-metric-value text-danger"><?= number_format($totalTienGiam, 0, ',', '.') ?> ₫</div>
                        <small class="text-danger"><i class="bi bi-cash-coin me-1"></i>Tiết kiệm cho khách</small>
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
                        <i class="bi bi-tags text-primary"></i>Danh Sách Chương Trình Khuyến Mãi
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> mã
                    </span>
                </div>

                <form action="khuyen-mai.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 360px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Tìm theo mã KM, tên chiến dịch...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-percent text-success"></i>
                        </span>
                        <select name="loaiKM" class="form-select border-start-0 ps-2">
                            <option value="">Loại giảm</option>
                            <option value="PHAN_TRAM" <?= $filterLoai === 'PHAN_TRAM' ? 'selected' : '' ?>>Phần trăm (%)</option>
                            <option value="TIEN_MAT" <?= $filterLoai === 'TIEN_MAT' ? 'selected' : '' ?>>Tiền mặt (₫)</option>
                        </select>
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-toggle-on text-info"></i>
                        </span>
                        <select name="trangThai" class="form-select border-start-0 ps-2">
                            <option value="">Trạng thái</option>
                            <option value="1" <?= $filterTrangThai === '1' ? 'selected' : '' ?>>Đang diễn ra</option>
                            <option value="0" <?= $filterTrangThai === '0' ? 'selected' : '' ?>>Tạm dừng</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="khuyen-mai.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
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
                            <th style="width: 140px;">MÃ VOUCHER</th>
                            <th>TÊN CHIẾN DỊCH KHUYẾN MÃI</th>
                            <th>MỨC CHIẾT KHẤU</th>
                            <th>ĐƠN TỐI THIỂU</th>
                            <th>THỜI HẠN ÁP DỤNG</th>
                            <th class="text-center">ĐÃ DÙNG</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 100px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultKM && $resultKM->num_rows > 0) {
                            while ($row = $resultKM->fetch_assoc()) {
                                $maKM = $row["maKM"];
                                $tenKM = $row["tenKM"];
                                $loaiKM = $row["loaiKM"];
                                $giaTriGiam = $row["giaTriGiam"];
                                $mucGiamToiDa = $row["mucGiamToiDa"];
                                $donHangToiThieu = $row["donHangToiThieu"];
                                $ngayBatDau = $row["ngayBatDau"];
                                $ngayKetThuc = $row["ngayKetThuc"];
                                $trangThai = $row["trangThai"];

                                // Đếm số đơn hàng áp dụng mã khuyến mãi này từ bảng donhang
                                $sqlDon = "SELECT COUNT(*) AS soDonHangApDung FROM donhang WHERE maKM = '$maKM'";
                                $resDon = $conn->query($sqlDon);
                                $rowDon = ($resDon && $resDon->num_rows > 0) ? $resDon->fetch_assoc() : null;
                                $soDon = $rowDon["soDonHangApDung"] ?? 0;

                                $isExpired = (strtotime($ngayKetThuc) < time());
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace fw-bold">
                                            <?= htmlspecialchars($maKM) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenKM) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($loaiKM === 'PHAN_TRAM') { ?>
                                            <span class="fw-bold text-success">Giảm <?= $giaTriGiam ?>%</span>
                                            <?php if (!empty($mucGiamToiDa)) { ?>
                                                <small class="text-muted d-block">Tối đa: <?= number_format($mucGiamToiDa, 0, ',', '.') ?> ₫</small>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <span class="fw-bold text-primary">Giảm <?= number_format($giaTriGiam, 0, ',', '.') ?> ₫</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="text-secondary"><?= number_format($donHangToiThieu, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td>
                                        <small class="text-muted d-block"><i class="bi bi-calendar-event me-1"></i>Từ: <?= date('d/m/Y', strtotime($ngayBatDau)) ?></small>
                                        <small class="text-danger d-block"><i class="bi bi-clock me-1"></i>Đến: <?= date('d/m/Y', strtotime($ngayKetThuc)) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="../don-hang/don-hang.php?search=<?= urlencode($maKM) ?>" class="badge bg-light text-dark border text-decoration-none">
                                            <i class="bi bi-bag-check me-1 text-primary"></i><?= $soDon ?> đơn
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($trangThai == 1 && !$isExpired) { ?>
                                            <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đang chạy</span>
                                        <?php } elseif ($isExpired) { ?>
                                            <span class="tz-badge tz-badge-secondary"><i class="bi bi-clock-history"></i>Hết hạn</span>
                                        <?php } else { ?>
                                            <span class="tz-badge tz-badge-warning"><i class="bi bi-pause"></i>Tạm dừng</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?maKM=<?= urlencode($maKM) ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="khuyen-mai.php" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="maKM" value="<?= htmlspecialchars($maKM) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                data-tz-confirm="Bạn có chắc muốn xóa mã khuyến mãi <?= htmlspecialchars($maKM) ?>?"
                                                data-tz-title="Xác nhận xóa khuyến mãi">
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
                                            <i class="bi bi-ticket-perforated"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy mã khuyến mãi nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có khuyến mãi nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="khuyen-mai.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Tạo mã mới
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
