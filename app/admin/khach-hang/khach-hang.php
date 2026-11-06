<?php
// app/admin/khach-hang/khach-hang.php
$pageTitle = "Quản lý khách hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê Metrics
$sqlTongKH = "SELECT COUNT(*) as total FROM khachhang";
$totalKH = $conn->query($sqlTongKH)->fetch_assoc()['total'] ?? 0;

$sqlVIP = "SELECT COUNT(*) as total FROM khachhang WHERE doiTuongUuTien = 1";
$totalVIP = $conn->query($sqlVIP)->fetch_assoc()['total'] ?? 0;

$sqlThuong = "SELECT COUNT(*) as total FROM khachhang WHERE doiTuongUuTien = 0";
$totalThuong = $conn->query($sqlThuong)->fetch_assoc()['total'] ?? 0;

$sqlTongChiTieu = "SELECT IFNULL(SUM(tongThanhToan), 0) as total FROM donhang WHERE trangThaiDon = 'HOAN_THANH'";
$totalChiTieu = $conn->query($sqlTongChiTieu)->fetch_assoc()['total'] ?? 0;

$errors = [];

// 2. Xử lý các Form Action (Xóa khách hàng / Nâng cấp VIP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maKH = trim($_POST['maKH'] ?? '');

    if ($action === 'toggle_vip') {
        $newVip = intval($_POST['doiTuongUuTien'] ?? 0);
        $conn->query("UPDATE khachhang SET doiTuongUuTien = $newVip WHERE maKH = '$maKH'");
        echo "<script>window.location.href='khach-hang.php';</script>";
        exit;
    } elseif ($action === 'delete') {
        // Kiểm tra xem khách có đơn hàng chưa
        $chkDon = $conn->query("SELECT COUNT(*) as total FROM donhang WHERE maKH = '$maKH'")->fetch_assoc()['total'] ?? 0;
        if ($chkDon > 0) {
            $errors[] = "Không thể xóa khách hàng $maKH vì đã có $chkDon đơn hàng trong hệ thống!";
        } else {
            $sqlDel = "DELETE FROM khachhang WHERE maKH = '$maKH'";
            if ($conn->query($sqlDel)) {
                echo "<script>window.location.href='khach-hang.php';</script>";
                exit;
            } else {
                $errors[] = "Xóa khách hàng thất bại: " . $conn->error;
            }
        }
    }
}

// 3. Tìm kiếm & Lọc dữ liệu
$search = trim($_GET['search'] ?? '');
$filterHang = trim($_GET['doiTuongUuTien'] ?? '');

$sql = "SELECT * FROM khachhang WHERE 1 = 1";

if ($search !== "") {
    $sql .= " AND (maKH LIKE '%$search%' OR hoVaTen LIKE '%$search%' OR soDienThoai LIKE '%$search%' OR email LIKE '%$search%')";
}
if ($filterHang !== "") {
    $sql .= " AND doiTuongUuTien = '$filterHang'";
}

$sql .= " ORDER BY maKH DESC";
$resultKH = $conn->query($sql);
$totalRows = $resultKH ? $resultKH->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Khách hàng</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Hồ Sơ Khách Hàng</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý danh sách khách hàng, theo dõi lịch sử mua sắm và phân hạng khách hàng VIP.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-person-plus me-1"></i>Thêm khách hàng mới
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
                        <div class="tz-metric-title">TỔNG KHÁCH HÀNG</div>
                        <div class="tz-metric-value text-primary"><?= $totalKH ?></div>
                        <small class="text-muted"><i class="bi bi-people me-1"></i>Khách hàng trong hệ thống</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">THÀNH VIÊN VIP</div>
                        <div class="tz-metric-value text-warning"><?= $totalVIP ?></div>
                        <small class="text-warning"><i class="bi bi-star-fill me-1"></i>Khách hàng thân thiết</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-star"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">KHÁCH TIÊU CHUẨN</div>
                        <div class="tz-metric-value text-secondary"><?= $totalThuong ?></div>
                        <small class="text-muted"><i class="bi bi-person-check me-1"></i>Hạng thông thường</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG DOANH SỐ ĐÃ MUA</div>
                        <div class="tz-metric-value text-success"><?= number_format($totalChiTieu, 0, ',', '.') ?> ₫</div>
                        <small class="text-success"><i class="bi bi-bag-check me-1"></i>Đơn hoàn thành</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
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
                        <i class="bi bi-people text-primary"></i>Danh Sách Khách Hàng
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> khách
                    </span>
                </div>

                <form action="khach-hang.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 380px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-2" placeholder="Tên, mã KH, SĐT, Email...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 175px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-star text-warning"></i>
                        </span>
                        <select name="doiTuongUuTien" class="form-select border-start-0 ps-2">
                            <option value="">Phân hạng</option>
                            <option value="1" <?= $filterHang === '1' ? 'selected' : '' ?>>Khách VIP</option>
                            <option value="0" <?= $filterHang === '0' ? 'selected' : '' ?>>Tiêu chuẩn</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="khach-hang.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
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
                            <th style="width: 110px;">MÃ KH</th>
                            <th>HỌ VÀ TÊN</th>
                            <th>LIÊN HỆ (SĐT / EMAIL)</th>
                            <th>ĐỊA CHỈ</th>
                            <th class="text-center">ĐƠN MUA</th>
                            <th class="text-end">TỔNG CHI TIÊU</th>
                            <th class="text-center">PHÂN HẠNG</th>
                            <th class="text-end" style="width: 130px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultKH && $resultKH->num_rows > 0) {
                            while ($row = $resultKH->fetch_assoc()) {
                                $maKH = $row["maKH"];
                                $hoTen = $row["hoVaTen"];
                                $sdt = $row["soDienThoai"];
                                $email = $row["email"];
                                $diaChi = $row["diaChi"];
                                $vip = $row["doiTuongUuTien"];
                                $maTk = $row["maTk"] ?? "";

                                // Lấy tên tài khoản từ bảng accounts
                                $tk = "";
                                if (!empty($maTk)) {
                                    $sqlAcc = "SELECT * FROM accounts WHERE maTk = '$maTk'";
                                    $resAcc = $conn->query($sqlAcc);
                                    if ($resAcc && $resAcc->num_rows > 0) {
                                        $rowAcc = $resAcc->fetch_assoc();
                                        $tk = $rowAcc["tenTaiKhoan"];
                                    }
                                }

                                // Lấy số đơn hàng và tổng chi tiêu từ bảng donhang
                                $sqlDon = "SELECT COUNT(*) AS soDonHang, IFNULL(SUM(tongThanhToan), 0) AS tongChiTieu FROM donhang WHERE maKH = '$maKH' AND trangThaiDon = 'HOAN_THANH'";
                                $resDon = $conn->query($sqlDon);
                                $rowDon = ($resDon && $resDon->num_rows > 0) ? $resDon->fetch_assoc() : null;
                                $soDon = $rowDon["soDonHang"] ?? 0;
                                $tongTien = $rowDon["tongChiTieu"] ?? 0;
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">
                                            <?= htmlspecialchars($maKH) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($hoTen) ?></div>
                                        <?php if (!empty($tk)) { ?>
                                            <small class="text-muted"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($tk) ?></small>
                                        <?php } else { ?>
                                            <small class="text-secondary">Khách vãng lai</small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone me-1 text-primary"></i><span class="font-monospace text-dark"><?= htmlspecialchars($sdt) ?></span></div>
                                        <?php if (!empty($email)) { ?>
                                            <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($email) ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($diaChi) ?>">
                                            <?= htmlspecialchars($diaChi) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="../don-hang/don-hang.php?search=<?= urlencode($maKH) ?>" class="badge bg-light text-dark border text-decoration-none">
                                            <i class="bi bi-bag me-1 text-primary"></i><?= $soDon ?> đơn
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger"><?= number_format($tongTien, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($vip == 1) { ?>
                                            <span class="badge bg-warning text-dark border border-warning"><i class="bi bi-star-fill me-1"></i>VIP</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">Tiêu chuẩn</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                            <!-- Đổi hạng VIP -->
                                            <form method="POST" action="khach-hang.php" class="d-inline m-0 p-0">
                                                <input type="hidden" name="action" value="toggle_vip">
                                                <input type="hidden" name="maKH" value="<?= htmlspecialchars($maKH) ?>">
                                                <input type="hidden" name="doiTuongUuTien" value="<?= $vip == 1 ? '0' : '1' ?>">
                                                <button type="submit" class="btn btn-sm <?= $vip == 1 ? 'btn-outline-warning' : 'btn-outline-secondary' ?>" title="<?= $vip == 1 ? 'Hạ xuống khách thường' : 'Nâng cấp VIP' ?>">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            </form>

                                            <a href="edit.php?maKH=<?= urlencode($maKH) ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>

                                            <form method="POST" action="khach-hang.php" class="d-inline m-0 p-0">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="maKH" value="<?= htmlspecialchars($maKH) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                    data-tz-confirm="Bạn có chắc muốn xóa hồ sơ khách hàng <?= htmlspecialchars($hoTen) ?>?"
                                                    data-tz-title="Xác nhận xóa khách hàng">
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
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy khách hàng nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có khách hàng nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="khach-hang.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm khách hàng mới
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
