<?php
// app/admin/phieu-nhap/phieu-nhap.php
$pageTitle = "Quản lý nhập kho & Serial/IMEI";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Tính toán các chỉ số thống kê (Metrics) từ Database
// Tổng tiền nhập kho (chỉ tính phiếu đã nhập kho)
$sqlTongTien = "SELECT SUM(tongTienNhap) AS total FROM phieunhap WHERE trangThaiNhapKho = 'DA_NHAP_KHO'";
$resultTongTien = $conn->query($sqlTongTien);
$rowTongTien = $resultTongTien->fetch_assoc();
$tongTienNhapKho = $rowTongTien['total'] ?? 0;

// Phiếu đã nhập kho
$sqlDaNhap = "SELECT count(*) AS total FROM phieunhap WHERE trangThaiNhapKho = 'DA_NHAP_KHO'";
$resultDaNhap = $conn->query($sqlDaNhap);
$rowDaNhap = $resultDaNhap->fetch_assoc();
$totalDaNhap = $rowDaNhap['total'] ?? 0;

// Phiếu bản nháp (chưa nhập kho / đang đếm serial)
$sqlBanNhap = "SELECT count(*) AS total FROM phieunhap WHERE trangThaiNhapKho != 'DA_NHAP_KHO' OR trangThaiNhapKho IS NULL";
$resultBanNhap = $conn->query($sqlBanNhap);
$rowBanNhap = $resultBanNhap->fetch_assoc();
$totalBanNhap = $rowBanNhap['total'] ?? 0;

// Tiền đã trả trước / đặt cọc cho NCC
$sqlTraTruoc = "SELECT SUM(tongTienTraTruocNCC) AS total FROM phieunhap";
$resultTraTruoc = $conn->query($sqlTraTruoc);
$rowTraTruoc = $resultTraTruoc->fetch_assoc();
$totalTraTruoc = $rowTraTruoc['total'] ?? 0;

$errors = [];

// 2. Xử lý xóa phiếu nhập (POST Request)
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $maPhieuDelete = $_POST['maPhieu'] ?? '';

    if (!empty($maPhieuDelete)) {
        // Xóa các mã serial gắn liền với chi tiết nhập của phiếu này trước (tránh vướng khóa ngoại)
        $sqlDeleteSerials = "DELETE FROM serialsanpham WHERE idChiTietNhap IN (SELECT id FROM chitietnhap WHERE maPhieu = '$maPhieuDelete')";
        $conn->query($sqlDeleteSerials);

        // Xóa phiếu nhập (các dòng chi tiết nhập sẽ tự động xóa theo ràng buộc ON DELETE CASCADE)
        $sqlDelete = "DELETE FROM phieunhap WHERE maPhieu = '$maPhieuDelete'";
        if ($conn->query($sqlDelete)) {
            echo "<script>window.location.href='phieu-nhap.php';</script>";
            exit;
        } else {
            $errors[] = "Xóa phiếu nhập thất bại: " . $conn->error;
        }
    }
}

// 3. Lấy thông tin tìm kiếm & bộ lọc từ GET
$search = trim($_GET['search'] ?? '');
$filterNCC = trim($_GET['maNCC'] ?? '');
$filterNhapKho = trim($_GET['trangThaiNhapKho'] ?? '');
$filterThanhToan = trim($_GET['trangThaiThanhToan'] ?? '');

// Lấy danh sách phiếu nhập ra
$sqlPhieuNhap = "SELECT * FROM phieunhap WHERE 1 = 1";

if ($search !== "") {
    $sqlPhieuNhap .= " AND (maPhieu LIKE '%$search%' OR soHoaDonNCC LIKE '%$search%' OR ghiChu LIKE '%$search%')";
}
if ($filterNCC !== "") {
    $sqlPhieuNhap .= " AND maNCC = '$filterNCC'";
}
if ($filterNhapKho !== "") {
    $sqlPhieuNhap .= " AND trangThaiNhapKho = '$filterNhapKho'";
}
if ($filterThanhToan !== "") {
    $sqlPhieuNhap .= " AND trangThaiThanhToan = '$filterThanhToan'";
}

$sqlPhieuNhap .= " ORDER BY ngayNhap DESC";
$resultPhieuNhap = $conn->query($sqlPhieuNhap);
$totalRows = $resultPhieuNhap ? $resultPhieuNhap->num_rows : 0;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Phiếu nhập kho</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Nhập Kho & Serial/IMEI</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý chứng từ nhập hàng, đối chiếu công nợ nhà cung cấp và kiểm đếm Serial vật lý.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Nút Mở Modal Báo cáo Lịch sử Nhập - Xuất Kho -->
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalBaoCaoNhapXuat">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i>Báo cáo Nhập - Xuất kho
                </button>
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-patch-plus me-1"></i>Tạo phiếu nhập mới
                </a>
            </div>
        </div>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger border-danger-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">Vui lòng kiểm tra lại các thông tin sau:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $err): ?>
                            <li><?= ($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <!-- 4 Thẻ Metric Thống Kê -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG TIỀN NHẬP KHO</div>
                        <div class="tz-metric-value text-primary"><?= number_format($tongTienNhapKho, 0, ',', '.') ?> ₫</div>
                        <small class="text-muted"><i class="bi bi-check2-circle me-1"></i>Đã nhập kho chính thức</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">PHIẾU ĐÃ NHẬP KHO</div>
                        <div class="tz-metric-value text-success"><?= $totalDaNhap ?> Phiếu</div>
                        <small class="text-success"><i class="bi bi-shield-check me-1"></i>Hàng đã vào kho bán</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">PHIẾU BẢN NHÁP (ĐANG ĐẾM)</div>
                        <div class="tz-metric-value text-warning"><?= $totalBanNhap ?> Phiếu</div>
                        <small class="text-warning"><i class="bi bi-pencil-square me-1"></i>Chờ duyệt / quét serial</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TIỀN ĐÃ TRẢ / CỌC NCC</div>
                        <div class="tz-metric-value text-danger"><?= number_format($totalTraTruoc, 0, ',', '.') ?> ₫</div>
                        <small class="text-muted"><i class="bi bi-cash-coin me-1"></i>Thanh toán trước cho hãng</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Danh Sách & Bộ Lọc Dữ Liệu -->
        <div class="tz-card">
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-box-arrow-in-down text-primary"></i>Danh Sách Phiếu Nhập Kho
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalRows ?> phiếu
                    </span>
                </div>

                <!-- Form Lọc Dữ Liệu Tương Tự Module Thương Hiệu -->
                <form action="phieu-nhap.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <!-- Ô Tìm Kiếm -->
                    <div class="input-group flex-grow-1" style="min-width: 200px; max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= ($search) ?>" class="form-control border-start-0 ps-2" placeholder="Mã phiếu, số HĐ NCC, ghi chú...">
                    </div>

                    <!-- Dropdown Nhà Cung Cấp  -->
                    <div class="input-group flex-shrink-0" style="width: 220px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-building text-primary"></i>
                        </span>
                        <select name="maNCC" class="form-select border-start-0 ps-2">
                            <option value="">-- Nhà cung cấp --</option>
                            <?php
                            $sqlNCC = "SELECT maNCC, tenCongTy FROM nhacungcap ORDER BY tenCongTy ASC";
                            $resultNCC = $conn->query($sqlNCC);
                            if ($resultNCC && $resultNCC->num_rows > 0) {
                                while ($rowNCC = $resultNCC->fetch_assoc()) {
                                    $selected = ($filterNCC === $rowNCC['maNCC']) ? 'selected' : '';
                                    echo "<option value='{$rowNCC['maNCC']}' $selected>" . ($rowNCC['tenCongTy']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dropdown Trạng Thái Nhập Kho -->
                    <div class="input-group flex-shrink-0" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-box-seam text-success"></i>
                        </span>
                        <select name="trangThaiNhapKho" class="form-select border-start-0 ps-2">
                            <option value="">Trạng thái kho</option>
                            <option value="DA_NHAP_KHO" <?= $filterNhapKho === 'DA_NHAP_KHO' ? 'selected' : '' ?>>Đã nhập kho</option>
                            <option value="BAN_NHAP" <?= $filterNhapKho === 'BAN_NHAP' ? 'selected' : '' ?>>Bản nháp</option>
                        </select>
                    </div>

                    <!-- Dropdown Trạng Thái Thanh Toán -->
                    <div class="input-group flex-shrink-0" style="width: 185px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-credit-card text-info"></i>
                        </span>
                        <select name="trangThaiThanhToan" class="form-select border-start-0 ps-2">
                            <option value="">Thanh toán</option>
                            <option value="DA_THANH_TOAN" <?= $filterThanhToan === 'DA_THANH_TOAN' ? 'selected' : '' ?>>Đã thanh toán</option>
                            <option value="THANH_TOAN_MOT_PHAN" <?= $filterThanhToan === 'THANH_TOAN_MOT_PHAN' ? 'selected' : '' ?>>Thanh toán 1 phần</option>
                            <option value="CHUA_THANH_TOAN" <?= $filterThanhToan === 'CHUA_THANH_TOAN' ? 'selected' : '' ?>>Chưa thanh toán</option>
                        </select>
                    </div>

                    <!-- Nút Thao Tác: Lọc & Đặt lại -->
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="phieu-nhap.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Bảng Dữ Liệu Phiếu Nhập -->
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th style="width: 130px; white-space: nowrap;">MÃ PHIẾU</th>
                            <th style="white-space: nowrap;">NGÀY NHẬP</th>
                            <th>NHÀ CUNG CẤP</th>
                            <th style="white-space: nowrap;">NGƯỜI LẬP</th>
                            <th style="white-space: nowrap;">SỐ HÓA ĐƠN NCC</th>
                            <th class="text-end" style="white-space: nowrap;">TRẢ TRƯỚC</th>
                            <th class="text-end" style="white-space: nowrap;">TỔNG TIỀN NHẬP</th>
                            <th class="text-center" style="white-space: nowrap;">THANH TOÁN</th>
                            <th class="text-center" style="white-space: nowrap;">KHO</th>
                            <th class="text-end" style="width: 140px; min-width: 130px; white-space: nowrap;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultPhieuNhap && $resultPhieuNhap->num_rows > 0) {
                            while ($row = $resultPhieuNhap->fetch_assoc()) {
                                $maPhieu = $row["maPhieu"];
                                $ngayNhap = $row["ngayNhap"];
                                $maNCC = $row["maNCC"];
                                $nguoiNhap = $row["nguoiNhap"];
                                $soHoaDonNCC = $row["soHoaDonNCC"];
                                $tongTienTraTruocNCC = $row["tongTienTraTruocNCC"];
                                $tongTienNhap = $row["tongTienNhap"];
                                $trangThaiThanhToan = $row["trangThaiThanhToan"];
                                $trangThaiNhapKho = $row["trangThaiNhapKho"];
                                $ghiChu = $row["ghiChu"];

                                // Lấy tên nhà cung cấp từ bảng nhacungcap
                                $tenCongTy = $maNCC;
                                if (!empty($maNCC)) {
                                    $sqlNCC = "SELECT * FROM nhacungcap WHERE maNCC = '$maNCC'";
                                    $resNCC = $conn->query($sqlNCC);
                                    if ($resNCC && $resNCC->num_rows > 0) {
                                        $rowNCC = $resNCC->fetch_assoc();
                                        $tenCongTy = $rowNCC["tenCongTy"];
                                    }
                                }

                                // Lấy tên người nhập từ bảng accounts
                                $tenNguoiNhap = $nguoiNhap;
                                if (!empty($nguoiNhap)) {
                                    $sqlAcc = "SELECT * FROM accounts WHERE maTk = '$nguoiNhap'";
                                    $resAcc = $conn->query($sqlAcc);
                                    if ($resAcc && $resAcc->num_rows > 0) {
                                        $rowAcc = $resAcc->fetch_assoc();
                                        $tenNguoiNhap = $rowAcc["hoTen"];
                                    }
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold text-decoration-none" title="Xem chi tiết phiếu nhập">
                                            <?= ($maPhieu) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted text-nowrap">
                                            <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($ngayNhap)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="<?= ($tenCongTy) ?>">
                                            <?= ($tenCongTy) ?>
                                        </div>
                                        <small class="text-primary font-monospace"><?= ($maNCC) ?></small>
                                    </td>
                                    <td>
                                        <span class="text-secondary small text-nowrap">
                                            <i class="bi bi-person me-1"></i><?= ($tenNguoiNhap) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($soHoaDonNCC)) { ?>
                                            <span class="font-monospace text-dark fw-semibold text-nowrap"><?= ($soHoaDonNCC) ?></span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-nowrap">Chưa bổ sung</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <span class="text-secondary"><?= number_format($tongTienTraTruocNCC, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <span class="fw-bold text-danger"><?= number_format($tongTienNhap, 0, ',', '.') ?> ₫</span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php if ($trangThaiThanhToan === "DA_THANH_TOAN") { ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Đã thanh toán</span>
                                        <?php } elseif ($trangThaiThanhToan === "THANH_TOAN_MOT_PHAN") { ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-clock-history me-1"></i>Đặt cọc</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-hourglass-split me-1"></i>Chưa thanh toán</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php if ($trangThaiNhapKho === "DA_NHAP_KHO") { ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-box-seam me-1"></i>Đã nhập kho</span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-pencil-fill me-1"></i>Bản nháp</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                            <!-- Xem Chi Tiết -->
                                            <a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết & Serial">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <!-- Chỉnh Sửa Thông Tin Phiếu -->
                                            <a href="edit.php?maPhieu=<?= urlencode($maPhieu) ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin phiếu">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <!-- Xóa Phiếu Nhập (Form POST xác nhận Modal) -->
                                            <form method="POST" action="phieu-nhap.php" class="d-inline m-0 p-0">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="maPhieu" value="<?= ($maPhieu) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa phiếu nhập"
                                                    data-tz-confirm="Bạn có chắc muốn xóa phiếu nhập <?= ($maPhieu) ?> và toàn bộ chi tiết kiểm đếm liên quan?"
                                                    data-tz-title="Xác nhận xóa phiếu nhập">
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
                                <td colspan="11" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-inboxes"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy phiếu nhập nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có phiếu nhập nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="phieu-nhap.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Tạo phiếu nhập mới
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

        <!-- Modal Báo Cáo Lịch Sử Nhập - Xuất Kho -->
        <div class="modal fade" id="modalBaoCaoNhapXuat" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Báo Cáo Đối Chiếu Lịch Sử Nhập - Xuất Kho
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">Trích xuất biến động nhập kho từ nhà cung cấp và đối chiếu dữ liệu theo thời gian thực:</p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>MÃ PHIẾU / HÓA ĐƠN</th>
                                        <th>LOẠI GIAO DỊCH</th>
                                        <th>THỜI GIAN</th>
                                        <th>ĐỐI TÁC / NHÀ CUNG CẤP</th>
                                        <th>SỐ MẶT HÀNG</th>
                                        <th>TỔNG GIÁ TRỊ</th>
                                        <th>TRẠNG THÁI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sqlReport = "SELECT * FROM phieunhap ORDER BY ngayNhap DESC LIMIT 5";
                                    $resReport = $conn->query($sqlReport);
                                    if ($resReport && $resReport->num_rows > 0) {
                                        while ($rp = $resReport->fetch_assoc()) {
                                            $rpMaPhieu = $rp['maPhieu'];
                                            $rpMaNCC = $rp['maNCC'];
                                            $tenNCC = $rpMaNCC;
                                            $resNCC = $conn->query("SELECT tenCongTy FROM nhacungcap WHERE maNCC = '$rpMaNCC'");
                                            if ($resNCC && $resNCC->num_rows > 0) {
                                                $tenNCC = $resNCC->fetch_assoc()['tenCongTy'];
                                            }
                                            $resCount = $conn->query("SELECT COUNT(*) AS soMatHang FROM chitietnhap WHERE maPhieu = '$rpMaPhieu'");
                                            $soMatHang = ($resCount && $resCount->num_rows > 0) ? $resCount->fetch_assoc()['soMatHang'] : 0;
                                    ?>
                                            <tr>
                                                <td><span class="badge bg-primary-subtle text-primary border font-monospace"><?= ($rp['maPhieu']) ?></span></td>
                                                <td><span class="badge bg-success text-white"><i class="bi bi-arrow-down-circle me-1"></i>Nhập Kho</span></td>
                                                <td><?= date('d/m/Y H:i', strtotime($rp['ngayNhap'])) ?></td>
                                                <td><?= ($tenNCC) ?></td>
                                                <td><strong><?= $soMatHang ?> mặt hàng</strong></td>
                                                <td class="fw-bold text-danger"><?= number_format($rp['tongTienNhap'], 0, ',', '.') ?> ₫</td>
                                                <td>
                                                    <?php if ($rp['trangThaiNhapKho'] === 'DA_NHAP_KHO'): ?>
                                                        <span class="text-success fw-semibold"><i class="bi bi-check2 me-1"></i>Đã vào kho</span>
                                                    <?php else: ?>
                                                        <span class="text-warning fw-semibold"><i class="bi bi-pencil me-1"></i>Bản nháp</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>