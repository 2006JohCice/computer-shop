<?php
// app/admin/tai-khoan/tai-khoan.php
$pageTitle = "Quản lý tài khoản hệ thống";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

// Xử lý thông báo giả lập Form Action POST
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $maTk = htmlspecialchars($_POST['maTk'] ?? '');
    if ($action === 'create') {
        $hoTen = htmlspecialchars($_POST['hoTen'] ?? '');
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check-circle-fill me-2'></i>Đã tạo tài khoản Quản trị viên mới: <strong>$hoTen</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'update') {
        $alertMsg = "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                        <i class='bi bi-info-circle-fill me-2'></i>Đã cập nhật thông tin tài khoản <strong>$maTk</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'toggle_status') {
        $status = $_POST['trangThai'] ?? 'active';
        $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='bi bi-shield-exclamation me-2'></i>Đã cập nhật trạng thái tài khoản <strong>$maTk</strong> sang: <strong>$status</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'delete') {
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-trash-fill me-2'></i>Đã khóa / xóa tài khoản <strong>$maTk</strong> khỏi hệ thống!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Danh sách tài khoản mẫu
$sampleAccounts = [
    [
        'maTk' => 'TK01',
        'hoTen' => 'Nguyễn Đăng Tài',
        'email' => 'admin.tai@techzone.vn',
        'tenTaiKhoan' => 'admin_tai',
        'role' => 'admin',
        'trangThai' => 'active',
        'ngayTao' => '2026-01-15 08:30:00'
    ],
    [
        'maTk' => 'TK02',
        'hoTen' => 'Phạm Minh Nhật',
        'email' => 'nhat.pm@techzone.vn',
        'tenTaiKhoan' => 'nhat_admin',
        'role' => 'admin',
        'trangThai' => 'active',
        'ngayTao' => '2026-01-18 09:15:00'
    ],
    [
        'maTk' => 'TK03',
        'hoTen' => 'Trần Thành',
        'email' => 'thanh.kho@techzone.vn',
        'tenTaiKhoan' => 'thanh_kho',
        'role' => 'admin',
        'trangThai' => 'active',
        'ngayTao' => '2026-02-01 10:00:00'
    ],
    [
        'maTk' => 'TK04',
        'hoTen' => 'Lê Hồng Sơn',
        'email' => 'son.marketing@techzone.vn',
        'tenTaiKhoan' => 'son_km',
        'role' => 'admin',
        'trangThai' => 'active',
        'ngayTao' => '2026-02-10 11:20:00'
    ],
    [
        'maTk' => 'TK05',
        'hoTen' => 'Nguyễn Văn An',
        'email' => 'an.nguyen@gmail.com',
        'tenTaiKhoan' => 'an_nguyen99',
        'role' => 'user',
        'trangThai' => 'active',
        'ngayTao' => '2026-03-05 14:45:00'
    ],
    [
        'maTk' => 'TK06',
        'hoTen' => 'Trần Thị Mai',
        'email' => 'mai.tran@yahoo.com',
        'tenTaiKhoan' => 'mai_tran',
        'role' => 'user',
        'trangThai' => 'active',
        'ngayTao' => '2026-03-12 16:20:00'
    ],
    [
        'maTk' => 'TK07',
        'hoTen' => 'Hoàng Khắc Nam',
        'email' => 'nam.hoang@outlook.com',
        'tenTaiKhoan' => 'nam_hk',
        'role' => 'user',
        'trangThai' => 'inactive',
        'ngayTao' => '2026-04-01 09:10:00'
    ]
];
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header trang & Action buttons -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Quản lý tài khoản</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Tài Khoản Hệ Thống</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản trị tài khoản đăng nhập (Admin/User), phân quyền vai trò và trạng thái kích hoạt tài khoản.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-person-plus-fill me-1"></i>Tạo tài khoản Admin
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG TÀI KHOẢN</div>
                        <div class="tz-metric-value">7</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">QUẢN TRỊ VIÊN (ADMIN)</div>
                        <div class="tz-metric-value text-danger">4</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">KHÁCH HÀNG (USER)</div>
                        <div class="tz-metric-value text-primary">3</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-person-heart"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG KHÓA (INACTIVE)</div>
                        <div class="tz-metric-value text-warning">1</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Tìm Kiếm & Bộ Lọc -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="" method="GET" class="row g-2 align-items-center">
                    <div class="col-xl-5 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Tìm theo Tên tài khoản, Họ tên hoặc Email...">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6">
                        <select name="role" class="form-select">
                            <option value="">Tất cả vai trò</option>
                            <option value="admin">Quản trị viên (admin)</option>
                            <option value="user">Khách hàng (user)</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select name="trangThai" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang hoạt động (active)</option>
                            <option value="inactive">Đã khóa (inactive)</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-12">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-funnel"></i>Lọc tài khoản
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách Tài Khoản -->
        <div class="tz-card">
            <div class="tz-card-header d-flex justify-content-between align-items-center">
                <h3 class="tz-card-title"><i class="bi bi-person-lines-fill text-primary"></i>Danh Sách Tài Khoản</h3>
                <span class="badge bg-light text-secondary border">Hiển thị <?= count($sampleAccounts) ?> tài khoản</span>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">MÃ TK</th>
                            <th>HỌ VÀ TÊN</th>
                            <th>TÊN TÀI KHOẢN</th>
                            <th>EMAIL LIÊN HỆ</th>
                            <th>VAI TRÒ (ROLE)</th>
                            <th>TRẠNG THÁI</th>
                            <th>NGÀY TẠO</th>
                            <th class="text-end" style="width: 140px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sampleAccounts as $acc): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($acc['maTk']) ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white <?= $acc['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>" style="width: 32px; height: 32px; font-size: 13px;">
                                        <?= strtoupper(substr($acc['hoTen'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($acc['hoTen']) ?></span>
                                </div>
                            </td>
                            <td><span class="font-monospace text-secondary fw-semibold">@<?= htmlspecialchars($acc['tenTaiKhoan']) ?></span></td>
                            <td><a href="mailto:<?= htmlspecialchars($acc['email']) ?>" class="text-decoration-none text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($acc['email']) ?></a></td>
                            <td>
                                <?php if ($acc['role'] === 'admin'): ?>
                                    <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-shield-check me-1"></i>admin</span>
                                    <small class="d-block text-muted" style="font-size: 11px;">Quản trị / Duyệt đơn / Nhập kho</small>
                                <?php else: ?>
                                    <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-person me-1"></i>user</span>
                                    <a href="../khach-hang/khach-hang.php" class="d-block text-primary small text-decoration-none mt-1" title="Liên kết Class KhachHang.maTk" style="font-size: 11.5px;">
                                        <i class="bi bi-person-badge me-1"></i>Hồ sơ KH
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($acc['trangThai'] === 'active'): ?>
                                    <span class="tz-badge tz-badge-success"><i class="bi bi-check2-circle"></i>active</span>
                                <?php else: ?>
                                    <span class="tz-badge tz-badge-danger"><i class="bi bi-lock-fill"></i>inactive (Khóa)</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($acc['ngayTao'])) ?></small></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="edit.php?id=<?= $acc['maTk'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa tài khoản"><i class="bi bi-pencil-square"></i></a>
                                    
                                    <!-- Form Khóa / Mở Khóa -->
                                    <form method="POST" action="tai-khoan.php" class="d-inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="maTk" value="<?= $acc['maTk'] ?>">
                                        <input type="hidden" name="trangThai" value="<?= $acc['trangThai'] === 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="btn btn-sm <?= $acc['trangThai'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $acc['trangThai'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' ?>" data-tz-confirm="Bạn có chắc chắn muốn đổi trạng thái tài khoản @<?= $acc['tenTaiKhoan'] ?>?" data-tz-title="Xác nhận trạng thái" data-tz-type="warning">
                                            <i class="bi <?= $acc['trangThai'] === 'active' ? 'bi-lock' : 'bi-unlock' ?>"></i>
                                        </button>
                                    </form>

                                    <!-- Form Xóa / Khóa vĩnh viễn -->
                                    <form method="POST" action="tai-khoan.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="maTk" value="<?= $acc['maTk'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa tài khoản" data-tz-confirm="Bạn có chắc muốn xóa tài khoản @<?= $acc['tenTaiKhoan'] ?> khỏi hệ thống?" data-tz-title="Xác nhận xóa tài khoản"><i class="bi bi-trash3"></i></button>
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
