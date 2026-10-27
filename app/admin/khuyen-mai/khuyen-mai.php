<?php
// app/admin/khuyen-mai/khuyen-mai.php
$pageTitle = "Quản lý khuyến mãi Voucher";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Xử lý Form Action (Xóa khuyến mãi & Kiểm tra hợp lệ: kiemTraHopLe) - UI Only
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maKM = htmlspecialchars($_POST['maKM'] ?? '');
    if ($action === 'delete') {
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-trash3-fill me-2'></i>Đã xóa mã voucher <strong>$maKM</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'check_valid') {
        $maDM = htmlspecialchars($_POST['testDM'] ?? '1');
        $tongTien = floatval($_POST['testTongTien'] ?? 25000000);
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i><strong>[kiemTraHopLe]</strong> Mã khuyến mãi <strong>$maKM</strong> HỢP LỆ! Đủ điều kiện áp dụng cho Danh mục #$maDM với đơn hàng " . number_format($tongTien, 0, ',', '.') . "₫!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Dữ liệu mẫu chuẩn Class Diagram KhuyenMai:
// maKM, tenKM, loaiKM ("PHAN_TRAM" / "TIEN_MAT"), giaTriGiam, mucGiamToiDa, donHangToiThieu, ngayBatDau, ngayKetThuc, trangThai (1: Đang diễn ra, 0: Tạm dừng / Hết hạn)
$samplePromos = [
    [
        'maKM' => 'KM2026-T10',
        'tenKM' => 'Flash Sale Mùa Thu Laptop Gaming',
        'loaiKM' => 'PHAN_TRAM',
        'giaTriGiam' => 10,
        'mucGiamToiDa' => 3000000,
        'donHangToiThieu' => 15000000,
        'ngayBatDau' => '2026-10-01 00:00:00',
        'ngayKetThuc' => '2026-11-15 23:59:59',
        'danhMucApDung' => ['Laptop Gaming'],
        'trangThai' => 1
    ],
    [
        'maKM' => 'BACK2SCHOOL',
        'tenKM' => 'Tựu Trường Giảm Giá Trực Tiếp',
        'loaiKM' => 'TIEN_MAT',
        'giaTriGiam' => 1500000,
        'mucGiamToiDa' => 1500000,
        'donHangToiThieu' => 12000000,
        'ngayBatDau' => '2026-08-15 00:00:00',
        'ngayKetThuc' => '2026-11-30 23:59:59',
        'danhMucApDung' => ['Laptop Văn Phòng & Học Tập'],
        'trangThai' => 1
    ],
    [
        'maKM' => 'VIP2026',
        'tenKM' => 'Đặc Quyền Thành Viên VIP Khách Quen',
        'loaiKM' => 'PHAN_TRAM',
        'giaTriGiam' => 5,
        'mucGiamToiDa' => 5000000,
        'donHangToiThieu' => 20000000,
        'ngayBatDau' => '2026-01-01 00:00:00',
        'ngayKetThuc' => '2026-12-31 23:59:59',
        'danhMucApDung' => ['Laptop Cao Cấp - Doanh Nhân', 'Laptop Đồ Họa & Kỹ Thuật'],
        'trangThai' => 1
    ],
    [
        'maKM' => 'EXPIRED2025',
        'tenKM' => 'Xả Kho Cuối Năm Cũ',
        'loaiKM' => 'TIEN_MAT',
        'giaTriGiam' => 500000,
        'mucGiamToiDa' => 500000,
        'donHangToiThieu' => 8000000,
        'ngayBatDau' => '2025-01-01 00:00:00',
        'ngayKetThuc' => '2025-12-31 23:59:59',
        'danhMucApDung' => ['Laptop Văn Phòng & Học Tập'],
        'trangThai' => 0
    ]
];
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header trang -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Khuyến mãi</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Khuyến Mãi & Voucher Giảm Giá</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý mã coupon chiết khấu phần trăm / tiền mặt, liên kết trực tiếp với Danh Mục và Đơn Hàng.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="../danh-muc/danh-muc.php" class="btn btn-tz-outline">
                    <i class="bi bi-folder2-open"></i>Xem Danh Mục Áp Dụng
                </a>
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-tag-fill me-1"></i>Tạo mã khuyến mãi mới
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG MÃ KHUYẾN MÃI (maKM)</div>
                        <div class="tz-metric-value"><?= count($samplePromos) ?> Mã</div>
                        <small class="text-muted"><i class="bi bi-tag me-1"></i>Chương trình chiết khấu</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG HIỆU LỰC (trangThai = 1)</div>
                        <div class="tz-metric-value text-success">3 Mã</div>
                        <small class="text-success"><i class="bi bi-check-circle me-1"></i>Sẵn sàng áp dụng cho đơn</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ HẾT HẠN (trangThai = 0)</div>
                        <div class="tz-metric-value text-secondary">1 Mã</div>
                        <small class="text-muted"><i class="bi bi-clock-history me-1"></i>Tạm dừng lưu trữ</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Khuyến Mãi (Khớp 100% Class Diagram: maKM, tenKM, loaiKM, giaTriGiam, mucGiamToiDa, donHangToiThieu, ngayBatDau, ngayKetThuc, trangThai) -->
        <div class="tz-card">
            <div class="tz-card-header d-flex justify-content-between align-items-center">
                <h3 class="tz-card-title"><i class="bi bi-ticket-perforated text-primary"></i>Danh Sách Mã Khuyến Mãi</h3>
                <span class="badge bg-light text-secondary border">Hiển thị <?= count($samplePromos) ?> voucher</span>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ VOUCHER</th>
                            <th>TÊN CHƯƠNG TRÌNH (tenKM)</th>
                            <th>LOẠI KM</th>
                            <th>MỨC GIẢM (giaTriGiam)</th>
                            <th>ĐƠN TỐI THIỂU & TỐI ĐA</th>
                            <th>THỜI GIAN ÁP DỤNG</th>
                            <th>DANH MỤC ÁP DỤNG (maDM)</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 170px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($samplePromos as $km): ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold fs-6">
                                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($km['maKM']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($km['tenKM']) ?></div>
                            </td>
                            <td>
                                <?php if ($km['loaiKM'] === 'PHAN_TRAM'): ?>
                                    <span class="badge bg-warning-subtle text-dark border"><i class="bi bi-percent me-1"></i>Phần trăm</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-dark border"><i class="bi bi-cash me-1"></i>Tiền mặt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($km['loaiKM'] === 'PHAN_TRAM'): ?>
                                    <span class="fw-bold text-danger fs-6"><?= $km['giaTriGiam'] ?>%</span>
                                <?php else: ?>
                                    <span class="fw-bold text-danger fs-6"><?= number_format($km['giaTriGiam'], 0, ',', '.') ?> ₫</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted d-block">Đơn tối thiểu: <strong><?= number_format($km['donHangToiThieu'], 0, ',', '.') ?> ₫</strong></small>
                                <small class="text-muted d-block">Giảm tối đa: <strong><?= number_format($km['mucGiamToiDa'], 0, ',', '.') ?> ₫</strong></small>
                            </td>
                            <td>
                                <small class="text-muted d-block">Từ: <?= date('d/m/Y', strtotime($km['ngayBatDau'])) ?></small>
                                <small class="text-muted d-block">Đến: <?= date('d/m/Y', strtotime($km['ngayKetThuc'])) ?></small>
                            </td>
                            <td>
                                <?php foreach ($km['danhMucApDung'] as $dm): ?>
                                    <a href="../danh-muc/danh-muc.php" class="badge bg-light text-primary border text-decoration-none me-1 mb-1" title="Xem danh mục">
                                        <i class="bi bi-folder-fill me-1"></i><?= $dm ?>
                                    </a>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php if ($km['trangThai'] == 1): ?>
                                    <span class="tz-badge tz-badge-success"><i class="bi bi-check-circle"></i>Đang diễn ra</span>
                                <?php else: ?>
                                    <span class="tz-badge tz-badge-secondary"><i class="bi bi-x-circle"></i>Hết hạn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Nút kiểm tra hợp lệ: kiemTraHopLe(maKM, maDM, tongTienDon, ngayHienTai) -->
                                    <form method="POST" action="khuyen-mai.php" class="d-inline">
                                        <input type="hidden" name="action" value="check_valid">
                                        <input type="hidden" name="maKM" value="<?= $km['maKM'] ?>">
                                        <input type="hidden" name="testDM" value="1">
                                        <input type="hidden" name="testTongTien" value="25000000">
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Kiểm tra hợp lệ đơn hàng (kiemTraHopLe)"><i class="bi bi-patch-check"></i></button>
                                    </form>

                                    <a href="edit.php?id=<?= $km['maKM'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                    
                                    <form method="POST" action="khuyen-mai.php" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã voucher <?= $km['maKM'] ?>?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="maKM" value="<?= $km['maKM'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
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
