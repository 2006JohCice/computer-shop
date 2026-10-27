<?php
// app/admin/nha-cung-cap/nha-cung-cap.php
$pageTitle = "Quản lý nhà cung cấp";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Xử lý Form Action (Xóa NCC)
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maNCC = htmlspecialchars($_POST['maNCC'] ?? '');
    if ($action === 'delete') {
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-trash3-fill me-2'></i>Đã xóa nhà cung cấp <strong>$maNCC</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Dữ liệu mẫu Nhà cung cấp
$sampleSuppliers = [
    [
        'maNCC' => 'NCC_ASUS',
        'tenCongTy' => 'Công Ty TNHH ASUS Technology Việt Nam',
        'soDienThoai' => '1800 6588',
        'email' => 'distribution@asus.com.vn',
        'diaChiTruSo' => 'Tầng 5, Tòa nhà Viettel, 285 Cách Mạng Tháng 8, P.12, Q.10, TP.HCM',
        'quocTich' => 'Đài Loan',
        'maSoThue' => '0304912845',
        'trangThai' => 1
    ],
    [
        'maNCC' => 'NCC_DELL',
        'tenCongTy' => 'Dell Global B.V Việt Nam',
        'soDienThoai' => '1800 545455',
        'email' => 'partner_sales@dell.com',
        'diaChiTruSo' => 'Tầng 26, Keangnam Hanoi Landmark Tower, Nam Từ Liêm, Hà Nội',
        'quocTich' => 'Mỹ',
        'maSoThue' => '0102849182',
        'trangThai' => 1
    ],
    [
        'maNCC' => 'NCC_APPLE',
        'tenCongTy' => 'Công Ty TNHH Apple Việt Nam',
        'soDienThoai' => '1800 1127',
        'email' => 'apac_reseller@apple.com',
        'diaChiTruSo' => 'Phòng 901, Tầng 9, Tòa nhà MPlaza Saigon, 39 Lê Duẩn, Q.1, TP.HCM',
        'quocTich' => 'Mỹ',
        'maSoThue' => '0313504829',
        'trangThai' => 1
    ],
    [
        'maNCC' => 'NCC_LENOVO',
        'tenCongTy' => 'Lenovo Pte Ltd Việt Nam',
        'soDienThoai' => '1800 9284',
        'email' => 'support_vn@lenovo.com',
        'diaChiTruSo' => 'Tòa nhà Landmark 81, Vinhomes Central Park, Bình Thạnh, TP.HCM',
        'quocTich' => 'Trung Quốc',
        'maSoThue' => '0310294819',
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Nhà cung cấp</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Nhà Cung Cấp Đối Tác</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý danh bạ hãng cung ứng phần cứng, tra cứu chứng từ và đối chiếu phiếu nhập.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-building-add me-1"></i>Thêm nhà cung cấp mới
            </a>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG NHÀ CUNG CẤP</div>
                        <div class="tz-metric-value"><?= count($sampleSuppliers) ?></div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">QUỐC GIA ĐỐI TÁC</div>
                        <div class="tz-metric-value text-success">3 Quốc Gia</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG LÔ HÀNG ĐÃ NHẬP</div>
                        <div class="tz-metric-value text-primary">18 Lô</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu NCC -->
        <div class="tz-card">
            <div class="tz-card-header d-flex justify-content-between align-items-center">
                <h3 class="tz-card-title"><i class="bi bi-buildings text-primary"></i>Danh Sách Nhà Cung Cấp (xemDanhSachNCC)</h3>
                <span class="badge bg-light text-secondary border">Hiển thị <?= count($sampleSuppliers) ?> đối tác</span>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 110px;">MÃ NCC</th>
                            <th>TÊN CÔNG TY</th>
                            <th>LIÊN HỆ (SĐT / EMAIL)</th>
                            <th>ĐỊA CHỈ TRỤ SỞ</th>
                            <th>QUỐC TỊCH</th>
                            <th>MÃ SỐ THUẾ</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 160px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sampleSuppliers as $ncc): ?>
                        <tr>
                            <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold"><?= htmlspecialchars($ncc['maNCC']) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($ncc['tenCongTy']) ?></div>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone text-primary me-1"></i><?= htmlspecialchars($ncc['soDienThoai']) ?></div>
                                <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($ncc['email']) ?></small>
                            </td>
                            <td><small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($ncc['diaChiTruSo']) ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($ncc['quocTich']) ?></span></td>
                            <td><span class="font-monospace text-secondary fw-semibold"><?= htmlspecialchars($ncc['maSoThue']) ?></span></td>
                            <td>
                                <?php if ($ncc['trangThai'] === 1): ?>
                                    <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Hợp tác</span>
                                <?php else: ?>
                                    <span class="tz-badge tz-badge-danger"><i class="bi bi-pause-circle"></i>Tạm dừng</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Nút Xem các phiếu nhập của NCC này -->
                                    <a href="../phieu-nhap/phieu-nhap.php?maNCC=<?= $ncc['maNCC'] ?>" class="btn btn-sm btn-outline-info" title="Xem các phiếu nhập của NCC này">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </a>

                                    <a href="edit.php?id=<?= $ncc['maNCC'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil-square"></i></a>
                                    
                                    <!-- Form Xóa NCC -->
                                    <form method="POST" action="nha-cung-cap.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="maNCC" value="<?= $ncc['maNCC'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" data-tz-confirm="Bạn có chắc chắn muốn xóa nhà cung cấp <?= $ncc['tenCongTy'] ?>?" data-tz-title="Xác nhận xóa nhà cung cấp"><i class="bi bi-trash3"></i></button>
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
