<?php
// app/admin/don-hang/don-hang.php
$pageTitle = "Quản lý đơn bán hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê Metrics
$sqlDoanhThu = "SELECT IFNULL(SUM(tongThanhToan), 0) as total FROM donhang WHERE trangThaiDon = 'HOAN_THANH'";
$totalDoanhThu = $conn->query($sqlDoanhThu)->fetch_assoc()['total'] ?? 0;

$sqlHoanThanh = "SELECT COUNT(*) as total FROM donhang WHERE trangThaiDon = 'HOAN_THANH'";
$totalHoanThanh = $conn->query($sqlHoanThanh)->fetch_assoc()['total'] ?? 0;

$sqlDangGiao = "SELECT COUNT(*) as total FROM donhang WHERE trangThaiDon = 'DANG_GIAO'";
$totalDangGiao = $conn->query($sqlDangGiao)->fetch_assoc()['total'] ?? 0;

$sqlChoDuyet = "SELECT COUNT(*) as total FROM donhang WHERE trangThaiDon = 'CHO_DUYET'";
$totalChoDuyet = $conn->query($sqlChoDuyet)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý Form Action (Xóa đơn hàng, Duyệt đơn)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maDHDong = trim($_POST['maDH'] ?? '');

    if ($action === 'delete') {
        // Trả lại các serial đã gán về trạng thái 'Trong kho'
        $conn->query("UPDATE serialsanpham SET idChiTietDonHang = NULL, trangThai = 'Trong kho' WHERE idChiTietDonHang IN (SELECT id FROM chitietdonhang WHERE maDH = '$maDHDong')");

        // Xóa hóa đơn liên quan nếu có
        $conn->query("DELETE FROM hoadon WHERE maDH = '$maDHDong'");

        // Xóa đơn hàng (chi tiết đơn hàng sẽ tự động xóa theo ON DELETE CASCADE)
        if ($conn->query("DELETE FROM donhang WHERE maDH = '$maDHDong'")) {
            echo "<script>window.location.href='don-hang.php';</script>";
            exit;
        } else {
            $errors[] = "Xóa đơn hàng thất bại: " . $conn->error;
        }
    } elseif ($action === 'duyet') {
        $conn->query("UPDATE donhang SET trangThaiDon = 'DANG_GIAO' WHERE maDH = '$maDHDong'");
        echo "<script>window.location.href='don-hang.php';</script>";
        exit;
    }
}

// 3. Tìm kiếm & Lọc dữ liệu
$search = trim($_GET['search'] ?? '');
$filterTrangThai = trim($_GET['trangThaiDon'] ?? '');
$filterKenh = trim($_GET['kenhBan'] ?? '');

$sql = "SELECT * FROM donhang WHERE 1 = 1";

if ($search !== "") {
    $sql .= " AND (maDH LIKE '%$search%' OR maKH IN (SELECT maKH FROM khachhang WHERE hoVaTen LIKE '%$search%' OR soDienThoai LIKE '%$search%'))";
}
if ($filterTrangThai !== "") {
    $sql .= " AND trangThaiDon = '$filterTrangThai'";
}
if ($filterKenh !== "") {
    $sql .= " AND kenhBan = '$filterKenh'";
}

$sql .= " ORDER BY ngayTao DESC";
$resultDH = $conn->query($sql);
$totalRows = $resultDH ? $resultDH->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Đơn hàng</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Đơn Bán Hàng</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Theo dõi đơn hàng tại quầy và online, phân công nhân viên xử lý và đối soát doanh thu.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-cart-plus me-1"></i>Tạo đơn bán hàng mới
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
                        <div class="tz-metric-title">TỔNG DOANH THU</div>
                        <div class="tz-metric-value text-success"><?= number_format($totalDoanhThu, 0, ',', '.') ?> ₫</div>
                        <small class="text-success"><i class="bi bi-check2-circle me-1"></i>Đơn đã hoàn thành</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN HOÀN THÀNH</div>
                        <div class="tz-metric-value text-primary"><?= $totalHoanThanh ?></div>
                        <small class="text-muted"><i class="bi bi-bag-check me-1"></i>Đã giao & thu tiền</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG GIAO HÀNG</div>
                        <div class="tz-metric-value text-info"><?= $totalDangGiao ?></div>
                        <small class="text-info"><i class="bi bi-truck me-1"></i>Đang trên đường giao</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN CHỜ DUYỆT</div>
                        <div class="tz-metric-value text-warning"><?= $totalChoDuyet ?></div>
                        <small class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Cần xử lý ngay</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Danh Sách & Bộ Lọc -->
        <div class="tz-card">
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-bag text-primary"></i>Danh Sách Đơn Hàng
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> đơn
                    </span>
                </div>

                <form action="don-hang.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 360px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Tìm theo mã đơn, tên khách, SĐT...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-shop text-primary"></i>
                        </span>
                        <select name="kenhBan" class="form-select border-start-0 ps-2">
                            <option value="">Kênh bán</option>
                            <option value="TAI_QUAY" <?= $filterKenh === 'TAI_QUAY' ? 'selected' : '' ?>>Tại quầy</option>
                            <option value="ONLINE" <?= $filterKenh === 'ONLINE' ? 'selected' : '' ?>>Online</option>
                        </select>
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 180px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-filter text-info"></i>
                        </span>
                        <select name="trangThaiDon" class="form-select border-start-0 ps-2">
                            <option value="">Trạng thái</option>
                            <option value="CHO_DUYET" <?= $filterTrangThai === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="DANG_GIAO" <?= $filterTrangThai === 'DANG_GIAO' ? 'selected' : '' ?>>Đang giao</option>
                            <option value="HOAN_THANH" <?= $filterTrangThai === 'HOAN_THANH' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="DA_HUY" <?= $filterTrangThai === 'DA_HUY' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="don-hang.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
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
                            <th style="width: 110px;">MÃ ĐƠN</th>
                            <th>KHÁCH HÀNG</th>
                            <th class="text-center">SỐ LƯỢNG MÁY</th>
                            <th>THỜI GIAN</th>
                            <th>KÊNH & THANH TOÁN</th>
                            <th>NHÂN VIÊN DUYỆT</th>
                            <th>VOUCHER GIẢM</th>
                            <th class="text-end">TỔNG THANH TOÁN</th>
                            <th class="text-center">TRẠNG THÁI</th>
                            <th class="text-end" style="width: 130px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultDH && $resultDH->num_rows > 0) {
                            while ($row = $resultDH->fetch_assoc()) {
                                $maDH = $row["maDH"];
                                $maKH = $row["maKH"];
                                $maKM = $row["maKM"];
                                $nguoiPhuTrach = $row["nguoiPhuTrach"];
                                $ngayTao = $row["ngayTao"];
                                $kenhBan = $row["kenhBan"];
                                $pttt = $row["phuongThucThanhToan"];
                                $tienGiam = $row["tienGiamGia"];
                                $tongThanhToan = $row["tongThanhToan"];
                                $trangThai = $row["trangThaiDon"];

                                // Lấy thông tin khách hàng từ bảng khachhang
                                $tenKH = "Khách vãng lai";
                                $sdtKH = "";
                                if (!empty($maKH)) {
                                    $sqlKH = "SELECT * FROM khachhang WHERE maKH = '$maKH'";
                                    $resKH = $conn->query($sqlKH);
                                    if ($resKH && $resKH->num_rows > 0) {
                                        $rowKH = $resKH->fetch_assoc();
                                        $tenKH = $rowKH["hoVaTen"];
                                        $sdtKH = $rowKH["soDienThoai"];
                                    }
                                }

                                // Lấy thông tin nhân viên từ bảng accounts
                                $tenNV = "Hệ thống / Chưa giao";
                                if (!empty($nguoiPhuTrach)) {
                                    $sqlNV = "SELECT * FROM accounts WHERE maTk = '$nguoiPhuTrach'";
                                    $resNV = $conn->query($sqlNV);
                                    if ($resNV && $resNV->num_rows > 0) {
                                        $rowNV = $resNV->fetch_assoc();
                                        $tenNV = $rowNV["hoTen"];
                                    }
                                }

                                // Lấy thông tin voucher từ bảng khuyenmai
                                $tenKM = "";
                                if (!empty($maKM)) {
                                    $sqlKM = "SELECT * FROM khuyenmai WHERE maKM = '$maKM'";
                                    $resKM = $conn->query($sqlKM);
                                    if ($resKM && $resKM->num_rows > 0) {
                                        $rowKM = $resKM->fetch_assoc();
                                        $tenKM = $rowKM["tenKM"];
                                    }
                                }
                                // Tính tổng số lượng máy trong đơn
                                $totalQtyMay = 0;
                                $resCntMay = $conn->query("SELECT SUM(soLuong) as totalSL FROM chitietdonhang WHERE maDH = '$maDH'");
                                if ($resCntMay && $rMay = $resCntMay->fetch_assoc()) {
                                    $totalQtyMay = (int)($rMay['totalSL'] ?? 0);
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <a href="detail.php?id=<?= urlencode($maDH) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold text-decoration-none">
                                            <?= htmlspecialchars($maDH) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenKH) ?></div>
                                        <?php if (!empty($sdtKH)) { ?>
                                            <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($sdtKH) ?></small>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border font-monospace fw-bold" style="font-size: 11.5px;">
                                            <i class="bi bi-laptop me-1 text-primary"></i><?= $totalQtyMay ?> máy
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($ngayTao)) ?></small>
                                    </td>
                                    <td>
                                        <div>
                                            <?php if ($kenhBan === 'TAI_QUAY') { ?>
                                                <span class="badge bg-light text-dark border"><i class="bi bi-shop me-1 text-primary"></i>Tại quầy</span>
                                            <?php } else { ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-globe me-1"></i>Online</span>
                                            <?php } ?>
                                        </div>
                                        <small class="text-secondary font-monospace"><?= htmlspecialchars($pttt) ?></small>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><i class="bi bi-person me-1"></i><?= htmlspecialchars($tenNV) ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($tenKM)) { ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" title="<?= htmlspecialchars($tenKM) ?>">
                                                -<?= number_format($tienGiam, 0, ',', '.') ?> ₫
                                            </span>
                                        <?php } else { ?>
                                            <span class="text-muted small">Không</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger"><?= number_format($tongThanhToan, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($trangThai === 'HOAN_THANH') { ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2-circle me-1"></i>Hoàn thành</span>
                                        <?php } elseif ($trangThai === 'DANG_GIAO') { ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-truck me-1"></i>Đang giao</span>
                                        <?php } elseif ($trangThai === 'CHO_DUYET') { ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-hourglass-split me-1"></i>Chờ duyệt</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">Đã hủy</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                            <a href="detail.php?id=<?= urlencode($maDH) ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết & Serial"><i class="bi bi-eye"></i></a>
                                            <a href="edit.php?maDH=<?= urlencode($maDH) ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                            <form method="POST" action="don-hang.php" class="d-inline m-0 p-0">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="maDH" value="<?= htmlspecialchars($maDH) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                    data-tz-confirm="Bạn có chắc muốn xóa đơn hàng <?= htmlspecialchars($maDH) ?>?"
                                                    data-tz-title="Xác nhận xóa đơn hàng">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-bag"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy đơn hàng nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có đơn hàng nào hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="don-hang.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-cart-plus me-1"></i>Tạo đơn hàng mới
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
