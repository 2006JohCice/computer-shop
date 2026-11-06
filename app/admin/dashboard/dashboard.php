<?php
// app/admin/dashboard/dashboard.php
$pageTitle = "Tổng Quan Hệ Thống";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// 1. Thống kê KPI hệ thống
// Doanh thu hoàn thành
$sqlDoanhThu = "SELECT IFNULL(SUM(tongThanhToan), 0) AS total FROM donhang WHERE trangThaiDon = 'HOAN_THANH'";
$tongDoanhThu = $conn->query($sqlDoanhThu)->fetch_assoc()['total'] ?? 0;

// Tổng số đơn hàng
$sqlTongDon = "SELECT COUNT(*) AS total FROM donhang";
$tongDon = $conn->query($sqlTongDon)->fetch_assoc()['total'] ?? 0;

// Đơn chờ duyệt
$sqlChoDuyet = "SELECT COUNT(*) AS total FROM donhang WHERE trangThaiDon = 'CHO_DUYET'";
$donChoDuyet = $conn->query($sqlChoDuyet)->fetch_assoc()['total'] ?? 0;

// Máy tồn kho
$sqlTonKho = "SELECT IFNULL(SUM(soLuongTon), 0) AS total FROM sanpham WHERE isDeleted = 0";
$tongTonKho = $conn->query($sqlTonKho)->fetch_assoc()['total'] ?? 0;

// Số ca bảo hành đang xử lý
$sqlBaoHanh = "SELECT COUNT(*) AS total FROM phieubaohanh WHERE trangThai LIKE '%tiếp nhận%' OR trangThai LIKE '%sửa chữa%'";
$caBaoHanh = $conn->query($sqlBaoHanh)->fetch_assoc()['total'] ?? 0;

// Tổng khách hàng
$sqlTongKH = "SELECT COUNT(*) AS total FROM khachhang";
$tongKH = $conn->query($sqlTongKH)->fetch_assoc()['total'] ?? 0;

// 2. Top Thương hiệu bán chạy
$topTHList = [];
$resTH = $conn->query("SELECT * FROM thuonghieu WHERE trangThai = 1");
if ($resTH && $resTH->num_rows > 0) {
    while ($th = $resTH->fetch_assoc()) {
        $maTH = $th['maTH'];
        // Lấy số lượng máy và tổng tiền bán của thương hiệu này từ sanpham và chitietdonhang
        $totalBan = 0;
        $totalTien = 0;
        $resSP = $conn->query("SELECT maSP FROM sanpham WHERE maTH = '$maTH'");
        if ($resSP && $resSP->num_rows > 0) {
            while ($sp = $resSP->fetch_assoc()) {
                $maSP = $sp['maSP'];
                $resCT = $conn->query("SELECT SUM(soLuong) AS sl, SUM(thanhTien) AS tt FROM chitietdonhang WHERE maSP = '$maSP'");
                if ($resCT && $rowCT = $resCT->fetch_assoc()) {
                    $totalBan += (int)($rowCT['sl'] ?? 0);
                    $totalTien += (float)($rowCT['tt'] ?? 0);
                }
            }
        }
        $topTHList[] = [
            'maTH' => $maTH,
            'tenTH' => $th['tenTH'],
            'soLuongBan' => $totalBan,
            'tongTienBan' => $totalTien
        ];
    }
    // Sắp xếp thương hiệu bán chạy nhất lên đầu
    usort($topTHList, function($a, $b) {
        return $b['soLuongBan'] - $a['soLuongBan'];
    });
    $topTHList = array_slice($topTHList, 0, 4);
}

// 3. Đơn hàng gần đây cần xử lý
$sqlRecentDH = "SELECT * FROM donhang ORDER BY ngayTao DESC LIMIT 5";
$resRecentDH = $conn->query($sqlRecentDH);
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Tiêu đề trang & Bộ lọc thời gian -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tổng Quan Hệ Thống</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Báo cáo số liệu kinh doanh, đơn hàng và hoạt động kho hàng TechZone theo thời gian thực.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small fw-medium"><i class="bi bi-clock-history me-1"></i>Hệ thống:</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5"><i class="bi bi-broadcast me-1"></i>Đang trực tuyến</span>
            </div>
        </div>

        <!-- 6 Thẻ Metric Thống kê -->
        <div class="row g-3 mb-4">
            <!-- Doanh thu -->
            <div class="col-xl-4 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG DOANH THU THỰC TẾ</div>
                        <div class="tz-metric-value text-primary"><?= number_format($tongDoanhThu, 0, ',', '.') ?> ₫</div>
                        <small class="text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i>Từ đơn hàng đã hoàn tất</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>

            <!-- Tổng Đơn -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG ĐƠN HÀNG</div>
                        <div class="tz-metric-value"><?= $tongDon ?></div>
                        <small class="text-muted"><i class="bi bi-bag-check me-1"></i>Đã khởi tạo</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-bag"></i>
                    </div>
                </div>
            </div>

            <!-- Đơn chờ xử lý -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN CHỜ DUYỆT</div>
                        <div class="tz-metric-value text-warning"><?= $donChoDuyet ?></div>
                        <small class="text-danger fw-semibold"><i class="bi bi-clock me-1"></i>Cần xử lý ngay</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <!-- Laptop trong kho -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">MÁY TỒN KHO</div>
                        <div class="tz-metric-value"><?= $tongTonKho ?></div>
                        <small class="text-primary"><i class="bi bi-laptop me-1"></i>Hàng sẵn bán</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-info-light); color: var(--tz-info);">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div>

            <!-- Bảo hành -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG BẢO HÀNH</div>
                        <div class="tz-metric-value text-danger"><?= $caBaoHanh ?></div>
                        <small class="text-muted"><i class="bi bi-tools me-1"></i>Máy tại xưởng</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f3e8ff; color: #9333ea;">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hàng 2: Bảng Đơn Hàng Mới Nhất & Top Hãng Bán Chạy -->
        <div class="row g-4 mb-4">
            <!-- Đơn hàng mới nhất -->
            <div class="col-xl-8">
                <div class="tz-card h-100">
                    <div class="tz-card-header d-flex justify-content-between align-items-center">
                        <h3 class="tz-card-title mb-0"><i class="bi bi-clock-history text-primary"></i>Đơn Hàng Gần Đây Cần Xử Lý</h3>
                        <a href="../don-hang/don-hang.php" class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold">Xem tất cả đơn hàng <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="tz-table">
                            <thead>
                                <tr>
                                    <th>MÃ ĐƠN</th>
                                    <th>KHÁCH HÀNG</th>
                                    <th>THỜI GIAN</th>
                                    <th>KÊNH & TT</th>
                                    <th class="text-end">TỔNG TIỀN</th>
                                    <th class="text-center">TRẠNG THÁI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resRecentDH && $resRecentDH->num_rows > 0) {
                                    while ($d = $resRecentDH->fetch_assoc()) {
                                        $maKH = $d['maKH'];
                                        $tenKhachHang = "Khách vãng lai";
                                        $sdtKhach = "";
                                        if (!empty($maKH)) {
                                            $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '$maKH'");
                                            if ($resKH && $resKH->num_rows > 0) {
                                                $rowKH = $resKH->fetch_assoc();
                                                $tenKhachHang = $rowKH['hoVaTen'];
                                                $sdtKhach = $rowKH['soDienThoai'];
                                            }
                                        }
                                ?>
                                        <tr>
                                            <td>
                                                <a href="../don-hang/detail.php?id=<?= urlencode($d['maDH']) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold text-decoration-none">
                                                    <?= htmlspecialchars($d['maDH']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($tenKhachHang) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($sdtKhach) ?></small>
                                            </td>
                                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($d['ngayTao'])) ?></small></td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?= $d['kenhBan'] === 'TAI_QUAY' ? 'Tại quầy' : 'Online' ?></span>
                                                <small class="text-secondary d-block font-monospace"><?= htmlspecialchars($d['phuongThucThanhToan']) ?></small>
                                            </td>
                                            <td class="text-end font-monospace fw-bold text-danger"><?= number_format($d['tongThanhToan'], 0, ',', '.') ?> ₫</td>
                                            <td class="text-center">
                                                <?php if ($d['trangThaiDon'] === 'HOAN_THANH'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Hoàn thành</span>
                                                <?php elseif ($d['trangThaiDon'] === 'DANG_GIAO'): ?>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle">Đang giao</span>
                                                <?php elseif ($d['trangThaiDon'] === 'CHO_DUYET'): ?>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Chờ duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border">Đã hủy</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Chưa có đơn hàng nào trong hệ thống</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Hãng Laptop Bán Chạy -->
            <div class="col-xl-4">
                <div class="tz-card h-100">
                    <div class="tz-card-header">
                        <h3 class="tz-card-title"><i class="bi bi-trophy text-warning"></i>Doanh Số Theo Thương Hiệu</h3>
                    </div>
                    <div class="tz-card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php
                            if (!empty($topTHList)) {
                                foreach ($topTHList as $th) {
                            ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold font-monospace" style="width: 42px; height: 42px;">
                                                <?= htmlspecialchars(substr($th['maTH'], 0, 4)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="font-size: 13.5px;"><?= htmlspecialchars($th['tenTH']) ?></div>
                                                <small class="text-muted"><i class="bi bi-laptop me-1"></i><?= $th['soLuongBan'] ?> máy đã bán</small>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-danger"><?= number_format($th['tongTienBan'], 0, ',', '.') ?> ₫</span>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Các liên kết nhanh quản trị -->
        <div class="row g-3">
            <div class="col-md-3">
                <a href="../phieu-nhap/create.php" class="card p-3 border-0 shadow-sm text-decoration-none text-dark hover-shadow d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary fs-4"><i class="bi bi-box-arrow-in-down"></i></div>
                    <div>
                        <div class="fw-bold">Lập Phiếu Nhập Kho</div>
                        <small class="text-muted">Nhập thêm máy từ NCC</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="../don-hang/create.php" class="card p-3 border-0 shadow-sm text-decoration-none text-dark hover-shadow d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-success-subtle text-success fs-4"><i class="bi bi-cart-plus"></i></div>
                    <div>
                        <div class="fw-bold">Tạo Đơn Bán Hàng</div>
                        <small class="text-muted">Bán tại quầy & Online</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="../bao-hanh/create.php" class="card p-3 border-0 shadow-sm text-decoration-none text-dark hover-shadow d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning fs-4"><i class="bi bi-shield-plus"></i></div>
                    <div>
                        <div class="fw-bold">Tiếp Nhận Bảo Hành</div>
                        <small class="text-muted">Tra cứu Serial máy lỗi</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="../khach-hang/create.php" class="card p-3 border-0 shadow-sm text-decoration-none text-dark hover-shadow d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-info-subtle text-info fs-4"><i class="bi bi-person-plus"></i></div>
                    <div>
                        <div class="fw-bold">Thêm Khách Hàng</div>
                        <small class="text-muted">Đăng ký thành viên VIP</small>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
