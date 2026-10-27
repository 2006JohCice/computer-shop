<?php
// app/admin/khach-hang/edit.php
$pageTitle = "Chỉnh sửa khách hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maKH = htmlspecialchars($_GET['maKH'] ?? 'KH01');
// Dữ liệu mẫu tương ứng mã khách hàng
$customers = [
    'KH01' => [
        'maKH' => 'KH01',
        'hoVaTen' => 'Nguyễn Văn An',
        'soDienThoai' => '0912 345 678',
        'email' => 'an.nguyen@gmail.com',
        'diaChi' => '123 Đường Láng, Đống Đa, Hà Nội',
        'doiTuongUuTien' => 0
    ],
    'KH02' => [
        'maKH' => 'KH02',
        'hoVaTen' => 'Trần Thị Mai',
        'soDienThoai' => '0987 654 321',
        'email' => 'mai.tran@gmail.com',
        'diaChi' => '45 Hai Bà Trưng, Quận 1, TP. HCM',
        'doiTuongUuTien' => 3
    ]
];
$data = $customers[$maKH] ?? [
    'maKH' => $maKH,
    'hoVaTen' => 'Khách hàng TechZone',
    'soDienThoai' => '0900 000 000',
    'email' => 'khachhang@techzone.vn',
    'diaChi' => 'Hà Nội',
    'doiTuongUuTien' => 0
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
                        <li class="breadcrumb-item"><a href="khach-hang.php" class="text-decoration-none text-muted">Khách hàng</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Chỉnh sửa khách hàng: <?= $data['hoVaTen'] ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật hồ sơ khách hàng theo phương thức capNhapKhachHang() & nangCapUuTien()</p>
            </div>
            <a href="khach-hang.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- FORM SUBMIT TRUYỀN THỐNG -->
        <form action="khach-hang.php" method="POST">
            <input type="hidden" name="action" value="update">

            <!-- Box 1: Thông tin khách hàng -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-person-circle me-2 text-primary"></i> Thông tin khách hàng & Liên hệ
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã khách hàng (maKH)</label>
                            <input type="text" name="maKH" class="form-control font-monospace text-uppercase bg-light" 
                                   value="<?= htmlspecialchars($data['maKH']) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK) cố định</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Họ và tên (hoVaTen) <span class="text-danger">*</span></label>
                            <input type="text" name="hoVaTen" class="form-control" 
                                   value="<?= htmlspecialchars($data['hoVaTen']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" class="form-control font-monospace" 
                                   value="<?= htmlspecialchars($data['soDienThoai']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email (email)</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($data['email']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Địa chỉ liên hệ (diaChi) <span class="text-danger">*</span></label>
                            <input type="text" name="diaChi" class="form-control" 
                                   value="<?= htmlspecialchars($data['diaChi']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Phân hạng ưu tiên thành viên -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-award-fill me-2 text-warning"></i> Đối tượng ưu tiên & Hạng mức
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạng ưu tiên (doiTuongUuTien)</label>
                            <select name="doiTuongUuTien" class="form-select">
                                <option value="0" <?= $data['doiTuongUuTien'] == 0 ? 'selected' : '' ?>>0 - Tiêu chuẩn (Khách vãng lai, mua lẻ thông thường)</option>
                                <option value="1" <?= $data['doiTuongUuTien'] == 1 ? 'selected' : '' ?>>1 - Thành viên Thân thiết (Bạc - Giảm 2%)</option>
                                <option value="2" <?= $data['doiTuongUuTien'] == 2 ? 'selected' : '' ?>>2 - Thành viên Thân thiết (Vàng - Giảm 5%)</option>
                                <option value="3" <?= $data['doiTuongUuTien'] == 3 ? 'selected' : '' ?>>3 - Khách hàng VIP đặc quyền (Kim Cương - Giảm 8% + Ưu tiên 1-đổi-1)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="fw-semibold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i> Trạng thái khách hàng:</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Đang hoạt động bình thường</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khach-hang.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2">
                    <i class="bi bi-check-lg me-1"></i> Cập nhật khách hàng
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
