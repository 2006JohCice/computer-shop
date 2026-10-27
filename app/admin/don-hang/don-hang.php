<?php
// app/admin/don-hang/don-hang.php
$pageTitle = "Quản lý đơn hàng";

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $maDH = htmlspecialchars($_POST['maDH'] ?? '');
        $successMsg = "Đã tạo thành công đơn hàng: <strong>$maDH</strong>";
    } elseif ($action === 'update') {
        $maDH = htmlspecialchars($_POST['maDH'] ?? '');
        $successMsg = "Đã cập nhật thông tin đơn hàng: <strong>$maDH</strong>";
    } elseif ($action === 'duyet') {
        $maDH = htmlspecialchars($_POST['maDH'] ?? '');
        $successMsg = "Đã duyệt đơn hàng: <strong>$maDH</strong> thành công!";
    }
}

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Dữ liệu mẫu đơn hàng chuẩn Class Diagram:
// maDH, maKH, maKM, nguoiPhuTrach, ngayTao, kenhBan ("TAI_QUAY" / "ONLINE"), phuongThucThanhToan, phuongThucGiaoHang, phiVanChuyen, tongTienHang, tienGiamGia, tongThanhToan, trangThaiDon
$orders = [
    [
        'maDH' => 'DH01',
        'maKH' => 'KH01',
        'tenKH' => 'Nguyễn Văn An',
        'sdt' => '0912 345 678',
        'maKM' => 'KM2026-T10',
        'nguoiPhuTrach' => 'TK002', // Admin / Thu ngân
        'tenNguoiPhuTrach' => 'Trần Văn Quản Trị (TK002)',
        'ngayTao' => '2026-10-23 14:15:00',
        'kenhBan' => 'ONLINE',
        'phuongThucThanhToan' => 'VNPay',
        'phuongThucGiaoHang' => 'Giao hàng hỏa tốc',
        'phiVanChuyen' => 50000,
        'tongTienHang' => 37990000,
        'tienGiamGia' => 3799000,
        'tongThanhToan' => 34241000,
        'trangThaiDon' => 'CHO_DUYET'
    ],
    [
        'maDH' => 'DH02',
        'maKH' => 'KH02',
        'tenKH' => 'Trần Thị Mai',
        'sdt' => '0987 654 321',
        'maKM' => 'VIP2026',
        'nguoiPhuTrach' => 'TK002',
        'tenNguoiPhuTrach' => 'Trần Văn Quản Trị (TK002)',
        'ngayTao' => '2026-10-23 13:40:00',
        'kenhBan' => 'ONLINE',
        'phuongThucThanhToan' => 'COD',
        'phuongThucGiaoHang' => 'Chuyển phát nhanh',
        'phiVanChuyen' => 40000,
        'tongTienHang' => 52480000,
        'tienGiamGia' => 2624000,
        'tongThanhToan' => 49896000,
        'trangThaiDon' => 'DANG_GIAO'
    ],
    [
        'maDH' => 'DH03',
        'maKH' => 'KH03',
        'tenKH' => 'Lê Hoàng Nam',
        'sdt' => '0903 112 233',
        'maKM' => null,
        'nguoiPhuTrach' => 'TK003', // Thu ngân tại quầy
        'tenNguoiPhuTrach' => 'Lê Thu Ngân (TK003)',
        'ngayTao' => '2026-10-24 09:15:00',
        'kenhBan' => 'TAI_QUAY',
        'phuongThucThanhToan' => 'TIEN_MAT',
        'phuongThucGiaoHang' => null, // Mua tại quầy nhận trực tiếp
        'phiVanChuyen' => 0,
        'tongTienHang' => 21990000,
        'tienGiamGia' => 0,
        'tongThanhToan' => 21990000,
        'trangThaiDon' => 'HOAN_THANH'
    ]
];
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Đơn hàng</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Đơn Bán Hàng</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Phân tách rõ ràng giữa kênh Bán Tại Quầy (TAI_QUAY) và Bán Trực Tuyến (ONLINE), kết nối xuất Hóa Đơn và bảo hành Serial.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="../hoa-don/hoa-don.php" class="btn btn-tz-outline">
                    <i class="bi bi-receipt"></i>Danh Sách Hóa Đơn
                </a>
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tạo đơn bán hàng mới
                </a>
            </div>
        </div>

        <!-- 4 Thẻ Metric Bán Hàng -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG ĐƠN HÀNG (maDH)</div>
                        <div class="tz-metric-value"><?= count($orders) ?></div>
                        <small class="text-muted"><i class="bi bi-cart-check me-1"></i>Cả Online & Tại quầy</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN ONLINE CHỜ DUYỆT</div>
                        <div class="tz-metric-value text-warning">1</div>
                        <small class="text-warning"><i class="bi bi-globe me-1"></i>CHO_DUYET (UserDatHang)</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN TẠI QUẦY (HOÀN THÀNH)</div>
                        <div class="tz-metric-value text-success">1</div>
                        <small class="text-success"><i class="bi bi-shop me-1"></i>TAI_QUAY (Xuất kho ngay)</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-shop"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">DOANH THU ĐƠN (tongThanhToan)</div>
                        <div class="tz-metric-value text-dark" style="font-size: 21px;">106.1M ₫</div>
                        <small class="text-secondary"><i class="bi bi-cash-stack me-1"></i>Đã trừ voucher KM</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #ecfdf5; color: #10b981;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Danh Sách Đơn Hàng (Đầy đủ thuộc tính Class Diagram) -->
        <div class="tz-card">
            <div class="tz-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h3 class="tz-card-title mb-0"><i class="bi bi-receipt text-primary me-2"></i>Danh Sách Đơn Đặt Hàng</h3>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 form-control-sm" placeholder="Mã đơn hàng, tên khách, SĐT...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th>MÃ ĐƠN (maDH)</th>
                            <th>KÊNH BÁN (kenhBan)</th>
                            <th>KHÁCH HÀNG (maKH)</th>
                            <th>NGƯỜI PHỤ TRÁCH</th>
                            <th>MÃ KM (maKM)</th>
                            <th>NGÀY TẠO</th>
                            <th>TỔNG THANH TOÁN</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $dh): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td><span class="fw-bold font-monospace text-primary"><?= $dh['maDH'] ?></span></td>
                            <td>
                                <?php if ($dh['kenhBan'] === 'TAI_QUAY'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                                        <i class="bi bi-shop me-1"></i>Tại Quầy
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                        <i class="bi bi-globe me-1"></i>Online
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../khach-hang/khach-hang.php?search=<?= $dh['maKH'] ?>" class="text-decoration-none fw-bold text-dark" title="Xem hồ sơ khách hàng">
                                    <?= htmlspecialchars($dh['tenKH']) ?>
                                </a>
                                <small class="text-muted d-block"><?= htmlspecialchars($dh['sdt']) ?> • <?= $dh['maKH'] ?></small>
                            </td>
                            <td>
                                <span class="text-secondary small" title="Tài khoản nhân viên phụ trách đơn"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($dh['tenNguoiPhuTrach']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($dh['maKM'])): ?>
                                    <a href="../khuyen-mai/khuyen-mai.php?search=<?= $dh['maKM'] ?>" class="badge bg-warning-subtle text-dark border text-decoration-none" title="Xem chương trình khuyến mãi">
                                        <i class="bi bi-tag-fill text-danger me-1"></i><?= $dh['maKM'] ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Không</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($dh['ngayTao'])) ?></small></td>
                            <td>
                                <div class="fw-bold text-danger font-monospace"><?= number_format($dh['tongThanhToan'], 0, ',', '.') ?> ₫</div>
                                <?php if ($dh['tienGiamGia'] > 0): ?>
                                    <small class="text-muted" style="font-size: 11px;">(Đã giảm <?= number_format($dh['tienGiamGia'], 0, ',', '.') ?>₫)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($dh['trangThaiDon'] === 'CHO_DUYET'): ?>
                                    <span class="tz-badge tz-badge-warning"><i class="bi bi-clock"></i>Chờ duyệt</span>
                                <?php elseif ($dh['trangThaiDon'] === 'DANG_GIAO'): ?>
                                    <span class="tz-badge tz-badge-info"><i class="bi bi-truck"></i>Đang giao</span>
                                <?php elseif ($dh['trangThaiDon'] === 'HOAN_THANH'): ?>
                                    <span class="tz-badge tz-badge-success"><i class="bi bi-check2-all"></i>Hoàn thành</span>
                                <?php else: ?>
                                    <span class="tz-badge tz-badge-danger"><i class="bi bi-x-circle"></i>Đã hủy</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="detail.php?id=<?= $dh['maDH'] ?>" class="btn btn-sm btn-tz-outline"><i class="bi bi-eye"></i> Chi tiết</a>
                                <a href="../hoa-don/detail.php?maDH=<?= $dh['maDH'] ?>" class="btn btn-sm btn-outline-success" title="Xem Hóa Đơn liên kết (HoaDon.maDH)"><i class="bi bi-receipt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
