<?php
// app/admin/khuyen-mai/edit.php
$pageTitle = "Chỉnh sửa mã khuyến mãi";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maKM = htmlspecialchars($_GET['maKM'] ?? 'TET2026');
// Dữ liệu mẫu tương ứng
$vouchers = [
    'TET2026' => [
        'maKM' => 'TET2026',
        'tenKM' => 'Đón Tết Bính Ngọ - Giảm Giá Sốc',
        'phanTramGiam' => 10.0,
        'ngayBatDau' => '2026-01-01T00:00',
        'ngayKetThuc' => '2026-02-15T23:59'
    ],
    'BACKTOSCHOOL' => [
        'maKM' => 'BACKTOSCHOOL',
        'tenKM' => 'Tựu trường hứng khởi cho sinh viên',
        'phanTramGiam' => 5.5,
        'ngayBatDau' => '2026-08-01T00:00',
        'ngayKetThuc' => '2026-09-30T23:59'
    ]
];
$data = $vouchers[$maKM] ?? [
    'maKM' => $maKM,
    'tenKM' => 'Chiến dịch khuyến mãi TechZone',
    'phanTramGiam' => 8.0,
    'ngayBatDau' => '2026-10-01T00:00',
    'ngayKetThuc' => '2026-12-31T23:59'
];
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="khuyen-mai.php" class="text-decoration-none text-muted">Khuyến mãi</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Chỉnh sửa khuyến mãi: <?= htmlspecialchars($data['maKM']) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật chiết khấu và thời gian có hiệu lực áp dụng voucher.</p>
            </div>
            <a href="khuyen-mai.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="khuyen-mai.php" method="POST">
            <input type="hidden" name="action" value="update">

            <!-- Box 1: Thông tin mã Khuyến Mãi -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-tag-fill me-2 text-primary"></i> Thông tin khuyến mãi Voucher
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khuyến mãi (maKM)</label>
                            <input type="text" name="maKM" class="form-control font-monospace text-uppercase fw-bold bg-light" 
                                   value="<?= htmlspecialchars($data['maKM']) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK) cố định</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tên khuyến mãi (tenKM) <span class="text-danger">*</span></label>
                            <input type="text" name="tenKM" class="form-control" 
                                   value="<?= htmlspecialchars($data['tenKM']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phần trăm giảm (phanTramGiam) (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="phanTramGiam" class="form-control" 
                                       value="<?= htmlspecialchars($data['phanTramGiam']) ?>" required>
                                <span class="input-group-text bg-light font-monospace fw-bold">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái áp dụng</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>Đang chạy (Kích hoạt)</option>
                                <option value="0">Tạm dừng áp dụng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Thời gian hiệu lực -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-range me-2 text-primary"></i> Thời gian áp dụng
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu (ngayBatDau) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayBatDau" class="form-control" 
                                   value="<?= htmlspecialchars($data['ngayBatDau']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc (ngayKetThuc) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayKetThuc" class="form-control" 
                                   value="<?= htmlspecialchars($data['ngayKetThuc']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khuyen-mai.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Cập nhật khuyến mãi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
