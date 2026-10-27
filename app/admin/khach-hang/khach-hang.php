<?php
// app/admin/khach-hang/khach-hang.php
$pageTitle = "Quản lý khách hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Xử lý Form Action (Gạt VIP, Xóa khách hàng)
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maKH = htmlspecialchars($_POST['maKH'] ?? '');
    if ($action === 'toggle_vip') {
        $vipStatus = intval($_POST['doiTuongUuTien'] ?? 0);
        $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='bi bi-star-fill text-warning me-2'></i>" . ($vipStatus == 1 ? "Đã nâng cấp khách hàng <strong>$maKH</strong> thành VIP!" : "Đã chuyển khách hàng <strong>$maKH</strong> về hạng tiêu chuẩn.") . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'delete') {
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-trash3-fill me-2'></i>Đã xóa hồ sơ khách hàng <strong>$maKH</strong> khỏi hệ thống!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Dữ liệu mẫu khách hàng
$sampleCustomers = [
    [
        'maKH' => 'KH01',
        'maTk' => 'TK001', // FK -> Accounts.maTk
        'hoVaTen' => 'Nguyễn Văn An',
        'soDienThoai' => '0912 345 678',
        'email' => 'an.nguyen@gmail.com',
        'diaChi' => '123 Đường Láng, Đống Đa, Hà Nội',
        'doiTuongUuTien' => 1, // Khách VIP
        'donHang' => [
            [
                'maDH' => 'DH01',
                'ngayTao' => '2026-10-23 14:15:00',
                'tongThanhToan' => 36140500,
                'trangThaiDon' => 'Hoàn thành',
                'chiTietMay' => 'Dell XPS 13 Plus 9320',
                'serial' => 'SN-DELL-XPS-99201'
            ]
        ]
    ],
    [
        'maKH' => 'KH02',
        'maTk' => 'TK004',
        'hoVaTen' => 'Trần Thị Mai',
        'soDienThoai' => '0987 654 321',
        'email' => 'mai.tran@yahoo.com',
        'diaChi' => '45 Hai Bà Trưng, Quận 1, TP. HCM',
        'doiTuongUuTien' => 1, // Khách VIP
        'donHang' => [
            [
                'maDH' => 'DH02',
                'ngayTao' => '2026-10-23 10:30:00',
                'tongThanhToan' => 45071000,
                'trangThaiDon' => 'Đã duyệt',
                'chiTietMay' => 'MacBook Pro 14 M3 Pro',
                'serial' => 'SN-APP-MBP-38102'
            ]
        ]
    ],
    [
        'maKH' => 'KH03',
        'maTk' => null, // Khách vãng lai mua tại quầy không tạo tài khoản
        'hoVaTen' => 'Lê Hoàng Nam',
        'soDienThoai' => '0903 112 233',
        'email' => 'nam.le@outlook.com',
        'diaChi' => '78 Cầu Giấy, Cầu Giấy, Hà Nội',
        'doiTuongUuTien' => 0, // Khách thường
        'donHang' => [
            [
                'maDH' => 'DH03',
                'ngayTao' => '2026-10-24 09:00:00',
                'tongThanhToan' => 43040000,
                'trangThaiDon' => 'Tiếp nhận',
                'chiTietMay' => 'ASUS ROG Strix G16',
                'serial' => 'Chưa gán (Chờ duyệt)'
            ]
        ]
    ],
    [
        'maKH' => 'KH04',
        'maTk' => 'TK008',
        'hoVaTen' => 'Phạm Quỳnh Anh',
        'soDienThoai' => '0934 889 900',
        'email' => 'quynhanh@gmail.com',
        'diaChi' => '12 Nguyễn Huệ, Quận 1, TP. HCM',
        'doiTuongUuTien' => 0, // Khách thường
        'donHang' => []
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Khách hàng</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Khách Hàng & Chương Trình VIP</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý hồ sơ khách mua, tra cứu đơn hàng và phân cấp thành viên VIP.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-person-plus-fill me-1"></i>Thêm khách hàng mới
            </a>
        </div>

        <!-- 3 Thẻ Metric -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG KHÁCH HÀNG</div>
                        <div class="tz-metric-value">4</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">KHÁCH HÀNG VIP / ƯU TIÊN</div>
                        <div class="tz-metric-value text-warning">2</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fef3c7; color: #d97706;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỶ LỆ QUAY LẠI MUA</div>
                        <div class="tz-metric-value text-success">75%</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Tìm Kiếm: timKiemKhachHang(sdt, hoVaTen) -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="sdt" class="form-control border-start-0 ps-0" placeholder="Số điện thoại khách hàng (sdt)...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="hoVaTen" class="form-control border-start-0 ps-0" placeholder="Họ và tên khách hàng (hoVaTen)...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-search me-1"></i>Tìm khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Khách Hàng -->
        <div class="tz-card">
            <div class="tz-card-header d-flex justify-content-between align-items-center">
                <h3 class="tz-card-title"><i class="bi bi-person-lines-fill text-primary"></i>Danh Sách Khách Hàng</h3>
                <span class="badge bg-light text-secondary border">Hiển thị <?= count($sampleCustomers) ?> khách hàng</span>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">MÃ KH</th>
                            <th>TÀI KHOẢN (maTk)</th>
                            <th>HỌ VÀ TÊN</th>
                            <th>SỐ ĐIỆN THOẠI</th>
                            <th>EMAIL</th>
                            <th>ĐỊA CHỈ</th>
                            <th>ĐỐI TƯỢNG ƯU TIÊN (VIP)</th>
                            <th class="text-end" style="width: 170px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sampleCustomers as $kh): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($kh['maKH']) ?></span></td>
                            <td>
                                <?php if (!empty($kh['maTk'])): ?>
                                    <a href="../tai-khoan/tai-khoan.php?search=<?= $kh['maTk'] ?>" class="badge bg-info-subtle text-dark border text-decoration-none" title="Xem tài khoản liên kết">
                                        <i class="bi bi-person-check-fill text-primary me-1"></i><?= $kh['maTk'] ?>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border"><i class="bi bi-shop me-1"></i>Tại quầy (Null)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($kh['hoVaTen']) ?></div>
                            </td>
                            <td>
                                <a href="tel:<?= $kh['soDienThoai'] ?>" class="text-decoration-none text-primary fw-semibold">
                                    <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($kh['soDienThoai']) ?>
                                </a>
                            </td>
                            <td><a href="mailto:<?= htmlspecialchars($kh['email']) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($kh['email']) ?></a></td>
                            <td><small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($kh['diaChi']) ?></small></td>
                            <td>
                                <!-- Nút gạt Switch VIP: Gọi nangCapUuTien(maKH, trangThai) -->
                                <form method="POST" action="khach-hang.php" class="d-inline-flex align-items-center gap-2">
                                    <input type="hidden" name="action" value="toggle_vip">
                                    <input type="hidden" name="maKH" value="<?= $kh['maKH'] ?>">
                                    <input type="hidden" name="doiTuongUuTien" value="<?= $kh['doiTuongUuTien'] == 1 ? '0' : '1' ?>">
                                    
                                    <button type="submit" class="border-0 bg-transparent p-0" title="<?= $kh['doiTuongUuTien'] == 1 ? 'Bấm để hủy VIP' : 'Bấm để nâng cấp VIP' ?>">
                                        <div class="form-check form-switch m-0 pointer-events-none">
                                            <input class="form-check-input" type="checkbox" role="switch" <?= $kh['doiTuongUuTien'] == 1 ? 'checked' : '' ?> style="cursor: pointer;">
                                        </div>
                                    </button>

                                    <?php if ($kh['doiTuongUuTien'] == 1): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-star-fill text-dark me-1"></i>KHÁCH VIP</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1">Khách thường</span>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Nút Xem Lịch Sử Giao Dịch Mua Máy: xemLichSuGiaoDich(sdt, hoVaTen, maKH) -->
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalHistory_<?= $kh['maKH'] ?>" title="Lịch sử mua máy">
                                        <i class="bi bi-clock-history"></i>
                                    </button>

                                    <a href="edit.php?id=<?= $kh['maKH'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil-square"></i></a>
                                    
                                    <!-- Form Xóa -->
                                    <form method="POST" action="khach-hang.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="maKH" value="<?= $kh['maKH'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" data-tz-confirm="Bạn có chắc chắn muốn xóa khách hàng <?= $kh['hoVaTen'] ?> khỏi hệ thống?" data-tz-title="Xác nhận xóa khách hàng"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>

                                <!-- Modal Lịch Sử Giao Dịch Mua Máy -->
                                <div class="modal fade" id="modalHistory_<?= $kh['maKH'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light border-bottom">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="bi bi-bag-check-fill text-primary me-2"></i>Lịch Sử Mua Hàng: <?= htmlspecialchars($kh['hoVaTen']) ?> (<?= $kh['maKH'] ?>)
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <p class="text-secondary small mb-3">Lịch sử các đơn đặt hàng và bảo hành đã phát sinh của khách hàng:</p>
                                                
                                                <?php if (!empty($kh['donHang'])): ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>MÃ ĐƠN HÀNG</th>
                                                                    <th>NGÀY MUA</th>
                                                                    <th>SẢN PHẨM & SERIAL/IMEI</th>
                                                                    <th>TỔNG THANH TOÁN</th>
                                                                    <th>TRẠNG THÁI</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($kh['donHang'] as $dh): ?>
                                                                <tr>
                                                                    <td><span class="badge bg-primary-subtle text-primary border font-monospace fw-bold"><?= $dh['maDH'] ?></span></td>
                                                                    <td><small><?= date('d/m/Y H:i', strtotime($dh['ngayTao'])) ?></small></td>
                                                                    <td>
                                                                        <div class="fw-bold"><?= $dh['chiTietMay'] ?></div>
                                                                        <small class="text-danger font-monospace"><i class="bi bi-upc-scan me-1"></i><?= $dh['serial'] ?></small>
                                                                    </td>
                                                                    <td class="fw-bold text-danger"><?= number_format($dh['tongThanhToan'], 0, ',', '.') ?> ₫</td>
                                                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle"><?= $dh['trangThaiDon'] ?></span></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="p-4 text-center text-muted bg-light rounded">
                                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                                        Khách hàng này chưa phát sinh giao dịch mua máy nào tại TechZone.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer bg-light border-top">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            </div>
                                        </div>
                                    </div>
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
