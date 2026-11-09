<?php
// app/admin/serial/serial.php
$pageTitle = "Quản lý Serial / IMEI Laptop";

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 1. XỬ LÝ XÓA SERIAL (DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $delSerial = trim($_GET['serial'] ?? '');
    if (!empty($delSerial)) {
        $delEsc = $conn->real_escape_string($delSerial);
        // Kiểm tra xem serial đã liên kết với phiếu bảo hành chưa
        $chkBH = $conn->query("SELECT maPhieuBH FROM phieubaohanh WHERE maSerial_IMEI = '$delEsc'");
        if ($chkBH && $chkBH->num_rows > 0) {
            $_SESSION['flash_error'] = "Không thể xóa Serial <strong>$delSerial</strong> vì đã kích hoạt phiếu bảo hành!";
        } else {
            $conn->query("DELETE FROM serialsanpham WHERE maSerial_IMEI = '$delEsc'");
            $_SESSION['flash_success'] = "Đã xóa thành công mã Serial <strong>$delSerial</strong> khỏi hệ thống!";
        }
        header("Location: serial.php");
        exit();
    }
}

// Lấy thông báo Flash
$successMsg = $_SESSION['flash_success'] ?? '';
$errorMsg = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// 2. TÍNH TOÁN METRIC SỐ LIỆU TỔNG QUAN THEO CSDL
$cntTrongKho = (int)$conn->query("SELECT COUNT(*) as c FROM serialsanpham WHERE trangThai = 'Trong kho'")->fetch_assoc()['c'];
$cntDaBan = (int)$conn->query("SELECT COUNT(*) as c FROM serialsanpham WHERE trangThai = 'Đã bán'")->fetch_assoc()['c'];
$cntDangBH = (int)$conn->query("SELECT COUNT(*) as c FROM serialsanpham WHERE trangThai = 'Đang bảo hành'")->fetch_assoc()['c'];
$cntLoi = (int)$conn->query("SELECT COUNT(*) as c FROM serialsanpham WHERE trangThai = 'Lỗi đổi trả' OR trangThai LIKE '%lỗi%' OR trangThai LIKE '%hỏng%'")->fetch_assoc()['c'];
$totalSerialCount = (int)$conn->query("SELECT COUNT(*) as c FROM serialsanpham")->fetch_assoc()['c'];

// 3. XỬ LÝ BỘ LỌC VÀ TÌM KIẾM
$searchKeyword = trim($_GET['serial'] ?? '');
$filterSP = trim($_GET['san_pham'] ?? '');
$filterStatus = trim($_GET['trang_thai'] ?? '');

$where = ["1=1"];
if (!empty($searchKeyword)) {
    $kEsc = $conn->real_escape_string($searchKeyword);
    $where[] = "(s.maSerial_IMEI LIKE '%$kEsc%' OR s.ghiChu LIKE '%$kEsc%')";
}
if (!empty($filterSP)) {
    $spEsc = $conn->real_escape_string($filterSP);
    $where[] = "s.maSP = '$spEsc'";
}
if (!empty($filterStatus)) {
    $stEsc = $conn->real_escape_string($filterStatus);
    $where[] = "s.trangThai = '$stEsc'";
}
$whereSql = implode(" AND ", $where);

// 4. PHÂN TRANG (PAGINATION)
$perPage = 25;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$cntQuery = $conn->query("SELECT COUNT(*) as c FROM serialsanpham s WHERE $whereSql");
$totalFilteredRows = (int)($cntQuery ? $cntQuery->fetch_assoc()['c'] : 0);
$totalPages = max(1, (int)ceil($totalFilteredRows / $perPage));
if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}
$offset = ($currentPage - 1) * $perPage;

// 5. TRUY VẤN DANH SÁCH SERIAL CÓ KÈM THÔNG TIN LAPTOP & PHIẾU NHẬP / ĐƠN HÀNG
$sqlSerials = "SELECT s.*, 
                      sp.tenSP, sp.thoiGianBaoHanh, sp.logo,
                      cn.maPhieu,
                      dh.maDH, dh.trangThaiDon
               FROM serialsanpham s
               LEFT JOIN sanpham sp ON s.maSP = sp.maSP
               LEFT JOIN chitietnhap cn ON s.idChiTietNhap = cn.id
               LEFT JOIN chitietdonhang ctdh ON s.idChiTietDonHang = ctdh.id
               LEFT JOIN donhang dh ON ctdh.maDH = dh.maDH
               WHERE $whereSql
               ORDER BY s.ngayXuatKho DESC, s.maSerial_IMEI DESC
               LIMIT $offset, $perPage";
$resSerials = $conn->query($sqlSerials);

// Danh sách sản phẩm cho dropdown lọc
$allSanpham = [];
$resAllSP = $conn->query("SELECT maSP, tenSP FROM sanpham WHERE isDeleted = 0 ORDER BY tenSP ASC");
if ($resAllSP) {
    while ($r = $resAllSP->fetch_assoc()) {
        $allSanpham[] = $r;
    }
}
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

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div><?= $errorMsg ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Header & Action -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản lý Serial / IMEI (SerialSanPham)</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">
                    Theo dõi chính xác từng chiếc laptop theo số Serial/IMEI duy nhất từ khi nhập kho đến xuất bán và bảo hành.
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2 font-monospace">Tổng: <?= number_format($totalSerialCount) ?> máy</span>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Serial máy mới
                </a>
            </div>
        </div>

        <!-- Metric tóm tắt Serial -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">MÁY TRONG KHO</div>
                        <div class="tz-metric-value text-primary"><?= number_format($cntTrongKho) ?></div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ BÁN CHO KHÁCH</div>
                        <div class="tz-metric-value text-success"><?= number_format($cntDaBan) ?></div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG BẢO HÀNH</div>
                        <div class="tz-metric-value text-warning"><?= number_format($cntDangBH) ?></div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">LỖI ĐỔI TRẢ / HỎNG</div>
                        <div class="tz-metric-value text-danger"><?= number_format($cntLoi) ?></div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Tìm Kiếm & Bộ Lọc Serial -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="serial.php" method="GET" class="row g-2 align-items-center">
                    <div class="col-xl-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="serial" class="form-control border-start-0 ps-0" placeholder="Nhập số Serial / IMEI cần tra cứu..." value="<?= htmlspecialchars($searchKeyword) ?>">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <select name="san_pham" class="form-select">
                            <option value="">-- Tất cả laptop --</option>
                            <?php foreach ($allSanpham as $sp): ?>
                                <option value="<?= htmlspecialchars($sp['maSP']) ?>" <?= ($filterSP === $sp['maSP']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sp['maSP']) ?> - <?= htmlspecialchars($sp['tenSP']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <select name="trang_thai" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="Trong kho" <?= ($filterStatus === 'Trong kho') ? 'selected' : '' ?>>Trong kho (Chưa bán)</option>
                            <option value="Đã bán" <?= ($filterStatus === 'Đã bán') ? 'selected' : '' ?>>Đã bán</option>
                            <option value="Đang bảo hành" <?= ($filterStatus === 'Đang bảo hành') ? 'selected' : '' ?>>Đang bảo hành</option>
                            <option value="Lỗi đổi trả" <?= ($filterStatus === 'Lỗi đổi trả') ? 'selected' : '' ?>>Lỗi đổi trả</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-funnel me-1"></i> Tra cứu
                        </button>
                        <?php if (!empty($searchKeyword) || !empty($filterSP) || !empty($filterStatus)): ?>
                            <a href="serial.php" class="btn btn-outline-secondary" title="Đặt lại bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách Serial / IMEI -->
        <div class="tz-card">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2">
                <span class="small text-muted">
                    Hiển thị <strong><?= min($totalFilteredRows, $offset + 1) ?></strong> - <strong><?= min($totalFilteredRows, $offset + $perPage) ?></strong> 
                    trong tổng số <strong><?= number_format($totalFilteredRows) ?></strong> Serial
                </span>
                <span class="badge bg-light text-dark border">Trang <?= $currentPage ?> / <?= $totalPages ?></span>
            </div>

            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ SERIAL / IMEI</th>
                            <th>MÃ SP</th>
                            <th>MODEL SẢN PHẨM LAPTOP</th>
                            <th>PHIẾU NHẬP</th>
                            <th>ĐƠN HÀNG XUẤT</th>
                            <th>NGÀY XUẤT KHO</th>
                            <th>HẠN BẢO HÀNH</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="min-width: 140px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resSerials && $resSerials->num_rows > 0): ?>
                            <?php while ($row = $resSerials->fetch_assoc()): 
                                $status = $row['trangThai'];
                                $badgeClass = 'tz-badge-primary';
                                $badgeIcon = 'bi-box';

                                if ($status === 'Đã bán') {
                                    $badgeClass = 'tz-badge-success';
                                    $badgeIcon = 'bi-check2';
                                } elseif ($status === 'Đang bảo hành') {
                                    $badgeClass = 'tz-badge-warning';
                                    $badgeIcon = 'bi-tools';
                                } elseif ($status === 'Lỗi đổi trả' || strpos($status, 'Lỗi') !== false) {
                                    $badgeClass = 'tz-badge-danger';
                                    $badgeIcon = 'bi-exclamation-triangle';
                                }

                                // Tính hạn bảo hành nếu máy đã bán
                                $hanBH = '— (' . (int)($row['thoiGianBaoHanh'] ?? 24) . 'T khi bán)';
                                if (!empty($row['ngayXuatKho'])) {
                                    $thangBH = (int)($row['thoiGianBaoHanh'] ?? 24);
                                    $hanTime = strtotime("+{$thangBH} months", strtotime($row['ngayXuatKho']));
                                    $isExpired = ($hanTime < time());
                                    $hanBH = "<span class='" . ($isExpired ? 'text-danger' : 'text-success fw-semibold') . "'>" . date('d/m/Y', $hanTime) . "</span>";
                                }
                            ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary fs-6"><?= htmlspecialchars($row['maSerial_IMEI']) ?></span>
                                        <?php if (!empty($row['ghiChu'])): ?>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($row['ghiChu']) ?>">
                                                <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($row['ghiChu']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($row['maSP']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($row['tenSP'] ?? 'Laptop Chính Hãng') ?></div>
                                        <small class="text-muted font-monospace">Mã: <?= htmlspecialchars($row['maSP']) ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['maPhieu'])): ?>
                                            <a href="../phieu-nhap/detail.php?id=<?= urlencode($row['maPhieu']) ?>" class="text-decoration-none font-monospace small">
                                                <?= htmlspecialchars($row['maPhieu']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted font-monospace small">PN#<?= (int)$row['idChiTietNhap'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['maDH'])): ?>
                                            <a href="../don-hang/detail.php?id=<?= urlencode($row['maDH']) ?>" class="text-decoration-none text-success fw-bold font-monospace small">
                                                #<?= htmlspecialchars($row['maDH']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">— (Chưa xuất)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="small <?= !empty($row['ngayXuatKho']) ? 'text-dark font-monospace' : 'text-muted' ?>">
                                            <?= !empty($row['ngayXuatKho']) ? date('d/m/Y', strtotime($row['ngayXuatKho'])) : '—' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small"><?= $hanBH ?></span>
                                    </td>
                                    <td>
                                        <span class="tz-badge <?= $badgeClass ?>">
                                            <i class="bi <?= $badgeIcon ?>"></i> <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit.php?serial=<?= urlencode($row['maSerial_IMEI']) ?>" class="btn btn-outline-primary" title="Chỉnh sửa trạng thái">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if ($status === 'Đã bán'): ?>
                                                <a href="../bao-hanh/create.php?serial=<?= urlencode($row['maSerial_IMEI']) ?>" class="btn btn-outline-warning" title="Tạo / Xem phiếu bảo hành">
                                                    <i class="bi bi-shield-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="serial.php?action=delete&serial=<?= urlencode($row['maSerial_IMEI']) ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa mã Serial <?= htmlspecialchars($row['maSerial_IMEI']) ?> không?')" 
                                               title="Xóa Serial">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <div>Không tìm thấy số Serial / IMEI nào phù hợp với điều kiện lọc!</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang (Pagination) -->
            <?php if ($totalPages > 1): ?>
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Trang <strong><?= $currentPage ?></strong> trên tổng số <strong><?= $totalPages ?></strong> trang
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <?php
                            $queryParams = $_GET;
                            $buildPageUrl = function($p) use ($queryParams) {
                                $queryParams['page'] = $p;
                                return 'serial.php?' . http_build_query($queryParams);
                            };
                            ?>
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildPageUrl(1) ?>" title="Trang đầu"><i class="bi bi-chevron-double-left"></i></a>
                            </li>
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildPageUrl($currentPage - 1) ?>" title="Trang trước"><i class="bi bi-chevron-left"></i></a>
                            </li>

                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            for ($p = $startPage; $p <= $endPage; $p++):
                            ?>
                                <li class="page-item <?= ($p === $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $buildPageUrl($p) ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildPageUrl($currentPage + 1) ?>" title="Trang sau"><i class="bi bi-chevron-right"></i></a>
                            </li>
                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildPageUrl($totalPages) ?>" title="Trang cuối"><i class="bi bi-chevron-double-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
