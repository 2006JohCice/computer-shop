<?php
// app/admin/nha-cung-cap/nha-cung-cap.php
$pageTitle = "Quản lý nhà cung cấp";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê metrics từ database
// Tổng nhà cung cấp
$sqlTongNCC = "SELECT COUNT(*) as total FROM nhacungcap";
$totalNCC = $conn->query($sqlTongNCC)->fetch_assoc()['total'] ?? 0;

// Đang hợp tác
$sqlHopTac = "SELECT COUNT(*) as total FROM nhacungcap WHERE trangThai = 1";
$totalHopTac = $conn->query($sqlHopTac)->fetch_assoc()['total'] ?? 0;

// Ngừng hợp tác
$sqlNgung = "SELECT COUNT(*) as total FROM nhacungcap WHERE trangThai = 0";
$totalNgung = $conn->query($sqlNgung)->fetch_assoc()['total'] ?? 0;

// Tổng tiền hàng đã giao dịch nhập kho
$sqlTongGiaoDich = "SELECT IFNULL(SUM(tongTienNhap), 0) as total FROM phieunhap";
$totalGiaoDich = $conn->query($sqlTongGiaoDich)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý xóa nhà cung cấp (POST Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $maNCCDelete = trim($_POST['maNCC'] ?? '');

    // Kiểm tra xem NCC đã có phiếu nhập hàng chưa (ràng buộc khóa ngoại)
    $sqlCheckPN = "SELECT COUNT(*) as total FROM phieunhap WHERE maNCC = '$maNCCDelete'";
    $countPN = $conn->query($sqlCheckPN)->fetch_assoc()['total'] ?? 0;

    if ($countPN > 0) {
        $errors[] = "Không thể xóa nhà cung cấp $maNCCDelete vì đã có $countPN phiếu nhập hàng liên kết trong hệ thống!";
    } else {
        $sqlDelete = "DELETE FROM nhacungcap WHERE maNCC = '$maNCCDelete'";
        if ($conn->query($sqlDelete)) {
            echo "<script>window.location.href='nha-cung-cap.php';</script>";
            exit;
        } else {
            $errors[] = "Xóa thất bại: " . $conn->error;
        }
    }
}

// 3. Lấy thông tin tìm kiếm & bộ lọc từ GET
$search = trim($_GET['search'] ?? '');
$quocTichFilter = trim($_GET['quocTich'] ?? '');
$trangThaiFilter = trim($_GET['trangThai'] ?? '');

$sqlNCC = "SELECT * FROM nhacungcap WHERE 1 = 1";

if ($search !== "") {
    $sqlNCC .= " AND (maNCC LIKE '%$search%' OR tenCongTy LIKE '%$search%' OR soDienThoai LIKE '%$search%' OR maSoThue LIKE '%$search%')";
}
if ($quocTichFilter !== "") {
    $sqlNCC .= " AND quocTich = '$quocTichFilter'";
}
if ($trangThaiFilter !== "") {
    $sqlNCC .= " AND trangThai = '$trangThaiFilter'";
}

$sqlNCC .= " ORDER BY trangThai DESC, tenCongTy ASC";
$resultNCC = $conn->query($sqlNCC);
$totalRows = $resultNCC ? $resultNCC->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Nhà cung cấp</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Hồ Sơ Nhà Cung Cấp Đối Tác</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý danh sách các hãng sản xuất và tổng kho phân phối laptop chính hãng.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-patch-plus me-1"></i>Thêm nhà cung cấp mới
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

        <!-- 4 Thẻ Metric Thống Kê -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG NHÀ CUNG CẤP</div>
                        <div class="tz-metric-value text-primary"><?= $totalNCC ?></div>
                        <small class="text-muted"><i class="bi bi-building me-1"></i>Doanh nghiệp đối tác</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG HỢP TÁC</div>
                        <div class="tz-metric-value text-success"><?= $totalHopTac ?></div>
                        <small class="text-success"><i class="bi bi-check-circle me-1"></i>Đang nhập hàng đều đặn</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TẠM DỪNG GIAO DỊCH</div>
                        <div class="tz-metric-value text-secondary"><?= $totalNgung ?></div>
                        <small class="text-muted"><i class="bi bi-pause-circle me-1"></i>Ngừng nhập kho</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG TIỀN ĐÃ NHẬP HÀNG</div>
                        <div class="tz-metric-value text-danger"><?= number_format($totalGiaoDich, 0, ',', '.') ?> ₫</div>
                        <small class="text-danger"><i class="bi bi-cash-stack me-1"></i>Giá trị nhập kho</small>
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
                        <i class="bi bi-building text-primary"></i>Danh Sách Nhà Cung Cấp
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> NCC
                    </span>
                </div>

                <form action="nha-cung-cap.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 380px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Nhập tên, mã NCC, SĐT, MST...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-geo-alt text-danger"></i>
                        </span>
                        <select name="quocTich" class="form-select border-start-0 ps-2">
                            <option value="">Quốc tịch</option>
                            <?php
                            $sqlQT = "SELECT DISTINCT quocTich FROM nhacungcap WHERE quocTich IS NOT NULL AND quocTich != ''";
                            $resQT = $conn->query($sqlQT);
                            if ($resQT && $resQT->num_rows > 0) {
                                while ($qt = $resQT->fetch_assoc()) {
                                    $sel = ($quocTichFilter === $qt['quocTich']) ? 'selected' : '';
                                    echo "<option value='{$qt['quocTich']}' $sel>" . htmlspecialchars($qt['quocTich']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 180px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-toggle-on text-info"></i>
                        </span>
                        <select name="trangThai" class="form-select border-start-0 ps-2">
                            <option value="">Trạng thái</option>
                            <option value="1" <?= $trangThaiFilter === '1' ? 'selected' : '' ?>>Đang hợp tác</option>
                            <option value="0" <?= $trangThaiFilter === '0' ? 'selected' : '' ?>>Ngừng hợp tác</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="nha-cung-cap.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
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
                            <th style="width: 130px;">MÃ NCC</th>
                            <th>TÊN CÔNG TY DOANH NGHIỆP</th>
                            <th>LIÊN HỆ (SĐT / EMAIL)</th>
                            <th>ĐỊA CHỈ TRỤ SỞ & MST</th>
                            <th class="text-center">PHIẾU NHẬP</th>
                            <th class="text-end">TỔNG NHẬP</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 100px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultNCC && $resultNCC->num_rows > 0) {
                            while ($row = $resultNCC->fetch_assoc()) {
                                $maNCC = $row["maNCC"];
                                $tenCongTy = $row["tenCongTy"];
                                $sdt = $row["soDienThoai"];
                                $email = $row["email"];
                                $diaChi = $row["diaChiTruSo"];
                                $quocTich = $row["quocTich"];
                                $mst = $row["maSoThue"];
                                $trangThai = $row["trangThai"];

                                // Lấy số phiếu nhập và tổng tiền nhập từ bảng phieunhap
                                $sqlPN = "SELECT COUNT(*) AS soPhieu, IFNULL(SUM(tongTienNhap), 0) AS tongTien FROM phieunhap WHERE maNCC = '$maNCC'";
                                $resPN = $conn->query($sqlPN);
                                $rowPN = ($resPN && $resPN->num_rows > 0) ? $resPN->fetch_assoc() : null;
                                $soPhieu = $rowPN["soPhieu"] ?? 0;
                                $tongTien = $rowPN["tongTien"] ?? 0;
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">
                                            <?= htmlspecialchars($maNCC) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenCongTy) ?></div>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($quocTich ?? 'Việt Nam') ?></small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone me-1 text-primary"></i><span class="font-monospace text-dark"><?= htmlspecialchars($sdt) ?></span></div>
                                        <?php if (!empty($email)) { ?>
                                            <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($email) ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($diaChi) ?>">
                                            <?= htmlspecialchars($diaChi) ?>
                                        </div>
                                        <small class="text-secondary font-monospace">MST: <?= htmlspecialchars($mst) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="../phieu-nhap/phieu-nhap.php?maNCC=<?= urlencode($maNCC) ?>" class="badge bg-light text-dark border text-decoration-none" title="Xem các phiếu nhập của NCC này">
                                            <i class="bi bi-box-arrow-in-down me-1 text-primary"></i><?= $soPhieu ?> phiếu
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger"><?= number_format($tongTien, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td>
                                        <?php if ($trangThai == 1) { ?>
                                            <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đang hợp tác</span>
                                        <?php } else { ?>
                                            <span class="tz-badge tz-badge-secondary"><i class="bi bi-pause"></i>Ngừng hợp tác</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                            <a href="edit.php?maNCC=<?= urlencode($maNCC) ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                            <form method="POST" action="nha-cung-cap.php" class="d-inline m-0 p-0">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="maNCC" value="<?= htmlspecialchars($maNCC) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                    data-tz-confirm="Bạn có chắc muốn xóa nhà cung cấp <?= htmlspecialchars($tenCongTy) ?>?"
                                                    data-tz-title="Xác nhận xóa nhà cung cấp">
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
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy nhà cung cấp nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có nhà cung cấp nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="nha-cung-cap.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm nhà cung cấp mới
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
