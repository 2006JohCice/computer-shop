<?php
// app/admin/bao-hanh/bao-hanh.php
$pageTitle = "Quản lý bảo hành điện tử laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// Tự động đồng bộ / kích hoạt bảo hành cho tất cả các máy đã bán nhưng chưa tạo phiếu
require_once __DIR__ . "/../../helpers/warranty_helper.php";
dongBoBaoHanhMayDaBan($conn);

// 1. Thống kê Metrics
$sqlTongBH = "SELECT COUNT(*) as total FROM phieubaohanh";
$totalBH = $conn->query($sqlTongBH)->fetch_assoc()['total'] ?? 0;

$sqlDangXuLy = "SELECT COUNT(*) as total FROM phieubaohanh WHERE trangThai LIKE '%tiếp nhận%' OR trangThai LIKE '%sửa chữa%' OR trangThai LIKE '%bảo hành%'";
$totalDangXuLy = $conn->query($sqlDangXuLy)->fetch_assoc()['total'] ?? 0;

$sqlHieuLuc = "SELECT COUNT(*) as total FROM phieubaohanh WHERE ngayKetThuc >= NOW() AND trangThai NOT LIKE '%Hết hạn%'";
$totalHieuLuc = $conn->query($sqlHieuLuc)->fetch_assoc()['total'] ?? 0;

$sqlHetHan = "SELECT COUNT(*) as total FROM phieubaohanh WHERE ngayKetThuc < NOW() OR trangThai LIKE '%Hết hạn%'";
$totalHetHan = $conn->query($sqlHetHan)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý Form Action (Xóa phiếu, Bàn giao máy)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maPBH = trim($_POST['maPhieuBH'] ?? '');

    if ($action === 'delete') {
        if ($conn->query("DELETE FROM phieubaohanh WHERE maPhieuBH = '$maPBH'")) {
            echo "<script>window.location.href='bao-hanh.php';</script>";
            exit;
        } else {
            $errors[] = "Xóa phiếu bảo hành thất bại: " . $conn->error;
        }
    } elseif ($action === 'traKhach') {
        $conn->query("UPDATE phieubaohanh SET trangThai = 'Đã bàn giao khách' WHERE maPhieuBH = '$maPBH'");
        echo "<script>window.location.href='bao-hanh.php';</script>";
        exit;
    }
}

// 3. Tìm kiếm & Lọc dữ liệu
$search = trim($_GET['search'] ?? '');
$filterTT = trim($_GET['trangThai'] ?? '');

$sql = "SELECT * FROM phieubaohanh WHERE 1 = 1";

if ($search !== "") {
    $sql .= " AND (maPhieuBH LIKE '%$search%' OR maSerial_IMEI LIKE '%$search%' OR maKH IN (SELECT maKH FROM khachhang WHERE hoVaTen LIKE '%$search%' OR soDienThoai LIKE '%$search%'))";
}
if ($filterTT !== "") {
    $sql .= " AND trangThai LIKE '%$filterTT%'";
}

$sql .= " ORDER BY ngayBatDau DESC";
$resultBH = $conn->query($sql);
$totalRows = $resultBH ? $resultBH->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Bảo hành điện tử</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Bảo Hành Điện Tử</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Bảo hành chính hãng tự động kích hoạt ngay khi bán máy. Tra cứu hạn bảo hành theo Serial/IMEI và quản lý chính sách hậu mãi.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-shield-plus me-1"></i>Kích hoạt bảo hành (Cấp bù)
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
                        <div class="tz-metric-title">TỔNG MÁY ĐÃ KÍCH HOẠT BH</div>
                        <div class="tz-metric-value text-primary"><?= $totalBH ?></div>
                        <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Tự động kích hoạt khi bán</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">CÒN HẠN CHÍNH HÃNG</div>
                        <div class="tz-metric-value text-success"><?= $totalHieuLuc ?></div>
                        <small class="text-success"><i class="bi bi-patch-check me-1"></i>Trong thời hạn BH</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-patch-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG XỬ LÝ BẢO HÀNH</div>
                        <div class="tz-metric-value text-warning"><?= $totalDangXuLy ?></div>
                        <small class="text-warning"><i class="bi bi-tools me-1"></i>Máy đang gửi bảo hành</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">HẾT HẠN BẢO HÀNH</div>
                        <div class="tz-metric-value text-secondary"><?= $totalHetHan ?></div>
                        <small class="text-muted"><i class="bi bi-clock-history me-1"></i>Đã quá thời hạn</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Danh Sách & Bộ Lọc -->
        <div class="tz-card">
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-shield-check text-primary"></i>Danh Sách Phiếu Bảo Hành
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> phiếu
                    </span>
                </div>

                <form action="bao-hanh.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 380px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Mã phiếu, Serial/IMEI, tên khách...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 180px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-filter text-info"></i>
                        </span>
                        <select name="trangThai" class="form-select border-start-0 ps-2">
                            <option value="">Trạng thái</option>
                            <option value="tiếp nhận" <?= $filterTT === 'tiếp nhận' ? 'selected' : '' ?>>Đang tiếp nhận</option>
                            <option value="Đang hiệu lực" <?= $filterTT === 'Đang hiệu lực' ? 'selected' : '' ?>>Đang hiệu lực</option>
                            <option value="bàn giao" <?= $filterTT === 'bàn giao' ? 'selected' : '' ?>>Đã bàn giao</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="bao-hanh.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
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
                            <th style="width: 130px;">MÃ PHIẾU BH</th>
                            <th>MÃ SERIAL / IMEI</th>
                            <th>SẢN PHẨM LAPTOP</th>
                            <th>KHÁCH HÀNG</th>
                            <th>THỜI HẠN BẢO HÀNH</th>
                            <th>CHÍNH SÁCH / TIẾN ĐỘ</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 130px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultBH && $resultBH->num_rows > 0) {
                            while ($row = $resultBH->fetch_assoc()) {
                                $maPBH = $row["maPhieuBH"];
                                $serial = $row["maSerial_IMEI"];
                                $maKH = $row["maKH"];
                                $ngayBD = $row["ngayBatDau"];
                                $ngayKT = $row["ngayKetThuc"];
                                $chinhSach = $row["moTaChinhSach"];
                                $trangThai = $row["trangThai"];
                                $isExpired = (strtotime($ngayKT) < time());

                                // Lấy thông tin khách hàng từ bảng khachhang
                                $tenKH = "Khách hàng";
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

                                // Lấy thông tin laptop từ bảng serialsanpham và sanpham
                                $tenSP = "Laptop chính hãng";
                                if (!empty($serial)) {
                                    $sqlSer = "SELECT * FROM serialsanpham WHERE maSerial_IMEI = '$serial'";
                                    $resSer = $conn->query($sqlSer);
                                    if ($resSer && $resSer->num_rows > 0) {
                                        $rowSer = $resSer->fetch_assoc();
                                        $maSP = $rowSer["maSP"];
                                        if (!empty($maSP)) {
                                            $sqlSP = "SELECT * FROM sanpham WHERE maSP = '$maSP'";
                                            $resSP = $conn->query($sqlSP);
                                            if ($resSP && $resSP->num_rows > 0) {
                                                $rowSP = $resSP->fetch_assoc();
                                                $tenSP = $rowSP["tenSP"];
                                            }
                                        }
                                    }
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">
                                            <?= htmlspecialchars($maPBH) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border font-monospace fw-semibold">
                                            <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($serial) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($tenSP) ?>">
                                            <?= htmlspecialchars($tenSP) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenKH) ?></div>
                                        <?php if (!empty($sdtKH)): ?>
                                            <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($sdtKH) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted d-block"><i class="bi bi-calendar-check me-1"></i>Từ: <?= date('d/m/Y', strtotime($ngayBD)) ?></small>
                                        <small class="d-block <?= $isExpired ? 'text-danger fw-semibold' : 'text-success fw-semibold' ?>"><i class="bi bi-shield me-1"></i>Đến: <?= date('d/m/Y', strtotime($ngayKT)) ?></small>
                                        <?php
                                        $diffDays = (int)round((strtotime($ngayKT) - time()) / 86400);
                                        if ($diffDays > 0):
                                        ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 11px;">Còn <?= $diffDays ?> ngày</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1" style="font-size: 11px;">Hết hạn <?= abs($diffDays) ?> ngày</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 240px;" title="<?= htmlspecialchars($chinhSach ?? '') ?>">
                                            <?= htmlspecialchars($chinhSach ?? 'Bảo hành tiêu chuẩn') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (stripos($trangThai, 'tiếp nhận') !== false): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-tools me-1"></i>Đang tiếp nhận</span>
                                        <?php elseif (stripos($trangThai, 'bàn giao') !== false): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-check2-all me-1"></i>Đã bàn giao</span>
                                        <?php elseif ($isExpired): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">Hết hạn</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-patch-check me-1"></i>Đang hiệu lực</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if (stripos($trangThai, 'tiếp nhận') !== false): ?>
                                            <form method="POST" action="bao-hanh.php" class="d-inline">
                                                <input type="hidden" name="action" value="traKhach">
                                                <input type="hidden" name="maPhieuBH" value="<?= htmlspecialchars($maPBH) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Đã bàn giao máy lại cho khách">
                                                    <i class="bi bi-check2"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a href="edit.php?maPhieuBH=<?= urlencode($maPBH) ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil-square"></i></a>

                                        <form method="POST" action="bao-hanh.php" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="maPhieuBH" value="<?= htmlspecialchars($maPBH) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                data-tz-confirm="Bạn có chắc muốn xóa phiếu bảo hành <?= htmlspecialchars($maPBH) ?>?"
                                                data-tz-title="Xác nhận xóa phiếu bảo hành">
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
                                            <i class="bi bi-shield-x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy phiếu bảo hành nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có phiếu bảo hành nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="bao-hanh.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-shield-plus me-1"></i>Tiếp nhận mới
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
