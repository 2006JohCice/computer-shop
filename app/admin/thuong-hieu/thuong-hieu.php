<?php
// app/admin/thuong-hieu/thuong-hieu.php
$pageTitle = "Quản lý thương hiệu laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Xử lý Form Action (Xóa thương hiệu) - UI Only
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maTH = htmlspecialchars($_POST['maTH'] ?? '');
    if ($action === 'delete') {
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-trash3-fill me-2'></i>Đã xóa thương hiệu <strong>$maTH</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Dữ liệu mẫu chuẩn Class Diagram: maTH, tenTH, logo, xuatXu, moTa, trangThai
$brands = [
    [
        'maTH' => 'DELL',
        'tenTH' => 'Dell Technologies',
        'logo' => 'bi-pc-display',
        'xuatXu' => 'Hoa Kỳ (USA)',
        'moTa' => 'Tập đoàn công nghệ Mỹ, chuyên dòng laptop doanh nhân XPS, Inspiron, Latitude và gaming Alienware',
        'soLuongMay' => 8,
        'trangThai' => 1
    ],
    [
        'maTH' => 'ASUS',
        'tenTH' => 'ASUStek Computer Inc.',
        'logo' => 'bi-controller',
        'xuatXu' => 'Đài Loan (Taiwan)',
        'moTa' => 'Hãng công nghệ Đài Loan, dẫn đầu phân khúc laptop gaming với dải ROG, TUF Gaming và ZenBook mỏng nhẹ',
        'soLuongMay' => 6,
        'trangThai' => 1
    ],
    [
        'maTH' => 'APPLE',
        'tenTH' => 'Apple Inc.',
        'logo' => 'bi-apple',
        'xuatXu' => 'Hoa Kỳ (USA)',
        'moTa' => 'Dòng MacBook Air và MacBook Pro chạy chip Apple Silicon M-Series hiệu năng mạnh mẽ, pin đỉnh cao',
        'soLuongMay' => 4,
        'trangThai' => 1
    ],
    [
        'maTH' => 'LENOVO',
        'tenTH' => 'Lenovo Group Limited',
        'logo' => 'bi-laptop',
        'xuatXu' => 'Trung Quốc (China)',
        'moTa' => 'Nổi tiếng thế giới với bàn phím ThinkPad trứ danh, laptop văn phòng Yoga, IdeaPad và dòng LOQ gaming',
        'soLuongMay' => 5,
        'trangThai' => 1
    ],
    [
        'maTH' => 'MSI',
        'tenTH' => 'Micro-Star International',
        'logo' => 'bi-lightning-charge-fill',
        'xuatXu' => 'Đài Loan (Taiwan)',
        'moTa' => 'Thương hiệu chuyên biệt phần cứng cao cấp, laptop đồ họa Creator và laptop gaming mạnh mẽ tản nhiệt khủng',
        'soLuongMay' => 3,
        'trangThai' => 1
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thương hiệu</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thương Hiệu Laptop Đối Tác</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý các hãng sản xuất chính hãng được phân phối tại hệ thống TECHZONE.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-patch-plus"></i>Thêm thương hiệu mới
            </a>
        </div>

        <!-- 3 Thẻ Metric -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG THƯƠNG HIỆU (maTH)</div>
                        <div class="tz-metric-value"><?= count($brands) ?></div>
                        <small class="text-muted"><i class="bi bi-patch-check me-1"></i>Hãng sản xuất</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-patch-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG HỢP TÁC (trangThai = 1)</div>
                        <div class="tz-metric-value text-success">5</div>
                        <small class="text-success"><i class="bi bi-handshake me-1"></i>Đại lý ủy quyền</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-handshake"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">HÃNG NHIỀU MẪU MÁY NHẤT</div>
                        <div class="tz-metric-value text-primary">DELL</div>
                        <small class="text-muted"><i class="bi bi-award me-1"></i>8 mẫu laptop trong kho</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-award"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Thương Hiệu (Khớp 100% Class Diagram: maTH, tenTH, logo, xuatXu, moTa, trangThai) -->
        <div class="tz-card">
            <div class="tz-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h3 class="tz-card-title mb-0"><i class="bi bi-tags text-primary"></i>Danh Sách Thương Hiệu Laptop</h3>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 form-control-sm" placeholder="Tìm thương hiệu...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th style="width: 90px;">MÃ HÃNG</th>
                            <th style="width: 70px;">LOGO</th>
                            <th>TÊN THƯƠNG HIỆU</th>
                            <th>XUẤT XỨ (xuatXu)</th>
                            <th>MÔ TẢ HÃNG (moTa)</th>
                            <th>SẢN PHẨM LIÊN KẾT</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $th): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold"><?= $th['maTH'] ?></span></td>
                            <td>
                                <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 18px; color: var(--tz-primary);">
                                    <i class="bi <?= $th['logo'] ?>"></i>
                                </div>
                            </td>
                            <td>
                                <a href="../san-pham/san-pham.php?thuongHieu=<?= $th['maTH'] ?>" class="fw-bold text-dark text-decoration-none" title="Xem các laptop thuộc hãng <?= $th['tenTH'] ?>">
                                    <?= htmlspecialchars($th['tenTH']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($th['xuatXu']) ?></span>
                            </td>
                            <td>
                                <div class="text-muted text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($th['moTa']) ?>">
                                    <?= htmlspecialchars($th['moTa']) ?>
                                </div>
                            </td>
                            <td>
                                <a href="../san-pham/san-pham.php?thuongHieu=<?= $th['maTH'] ?>" class="badge bg-light text-dark border text-decoration-none" title="Xem danh sách laptop của hãng">
                                    <i class="bi bi-laptop me-1 text-primary"></i><?= $th['soLuongMay'] ?> mẫu máy
                                </a>
                            </td>
                            <td>
                                <?php if ($th['trangThai'] == 1): ?>
                                    <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đang hợp tác</span>
                                <?php else: ?>
                                    <span class="tz-badge tz-badge-secondary"><i class="bi bi-eye-slash"></i>Ngừng hợp tác</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="edit.php?maTH=<?= $th['maTH'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="thuong-hieu.php" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="maTH" value="<?= $th['maTH'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i></button>
                                </form>
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
