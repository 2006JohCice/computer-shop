<?php
// app/admin/khuyen-mai/create.php
$pageTitle = "Tạo mã khuyến mãi mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maKM = trim($_POST['maKM'] ?? '');
    $tenKM = trim($_POST['tenKM'] ?? '');
    $loaiKM = trim($_POST['loaiKM'] ?? 'PHAN_TRAM');
    $giaTriGiam = floatval($_POST['giaTriGiam'] ?? 0);
    $mucGiamToiDa = !empty($_POST['mucGiamToiDa']) ? floatval($_POST['mucGiamToiDa']) : 'NULL';
    $donHangToiThieu = floatval($_POST['donHangToiThieu'] ?? 0);
    $ngayBatDau = trim($_POST['ngayBatDau'] ?? date('Y-m-d H:i:s'));
    $ngayKetThuc = trim($_POST['ngayKetThuc'] ?? date('Y-m-d 23:59:59', strtotime('+30 days')));
    $trangThai = intval($_POST['trangThai'] ?? 1);

    if (empty($maKM)) {
        $errors[] = 'Mã khuyến mãi không được để trống';
    }
    if (empty($tenKM)) {
        $errors[] = 'Tên chiến dịch khuyến mãi không được để trống';
    }
    if ($giaTriGiam <= 0) {
        $errors[] = 'Giá trị giảm phải lớn hơn 0';
    }

    // Kiểm tra trùng mã
    $sql_check = "SELECT maKM FROM khuyenmai WHERE maKM = '$maKM'";
    if ($conn->query($sql_check)->num_rows > 0) {
        $errors[] = 'Mã khuyến mãi đã tồn tại trong hệ thống';
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO khuyenmai (maKM, tenKM, loaiKM, giaTriGiam, mucGiamToiDa, donHangToiThieu, ngayBatDau, ngayKetThuc, trangThai) 
                       VALUES ('$maKM', '$tenKM', '$loaiKM', $giaTriGiam, $mucGiamToiDa, $donHangToiThieu, '$ngayBatDau', '$ngayKetThuc', $trangThai)";
        if ($conn->query($sql_insert)) {
            echo "<script>window.location.href='khuyen-mai.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi lưu khuyến mãi: " . $conn->error;
        }
    }
}
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="khuyen-mai.php" class="text-decoration-none text-muted">Khuyến mãi</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tạo mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tạo Chiến Dịch Khuyến Mãi Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập mã coupon voucher, tỷ lệ chiết khấu và thời hạn hiệu lực.</p>
            </div>
            <a href="khuyen-mai.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger border-danger-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">Vui lòng kiểm tra lại:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <form action="create.php" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-tag-fill me-2 text-primary"></i> Thông tin khuyến mãi Voucher
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã khuyến mãi (maKM) <span class="text-danger">*</span></label>
                            <input type="text" name="maKM" class="form-control font-monospace text-uppercase fw-bold" 
                                   placeholder="VD: KM_CHAOMUNG, BACK2SCHOOL..." value="<?= htmlspecialchars($_POST['maKM'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tên chương trình / Chiến dịch <span class="text-danger">*</span></label>
                            <input type="text" name="tenKM" class="form-control" 
                                   placeholder="VD: Ưu đãi tân sinh viên mùa tựu trường" value="<?= htmlspecialchars($_POST['tenKM'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Hình thức giảm giá <span class="text-danger">*</span></label>
                            <select name="loaiKM" class="form-select" id="selectLoaiKM">
                                <option value="PHAN_TRAM" selected>Giảm theo phần trăm (%)</option>
                                <option value="TIEN_MAT">Giảm trừ trực tiếp tiền mặt (₫)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Mức giảm (% hoặc ₫) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="giaTriGiam" class="form-control font-monospace" 
                                   placeholder="VD: 5, 10 hoặc 500000" value="<?= htmlspecialchars($_POST['giaTriGiam'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Mức giảm tối đa (₫)</label>
                            <input type="number" step="1000" min="0" name="mucGiamToiDa" class="form-control font-monospace" 
                                   placeholder="VD: 1500000" value="<?= htmlspecialchars($_POST['mucGiamToiDa'] ?? '') ?>">
                            <small class="text-muted">Áp dụng cho giảm theo %</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Đơn hàng tối thiểu (₫)</label>
                            <input type="number" step="1000" min="0" name="donHangToiThieu" class="form-control font-monospace" 
                                   placeholder="VD: 15000000" value="<?= htmlspecialchars($_POST['donHangToiThieu'] ?? '0') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ngày bắt đầu hiệu lực <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayBatDau" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ngày kết thúc chiến dịch <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayKetThuc" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime('+30 days')) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Trạng thái kích hoạt</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>1 - Đang diễn ra (áp dụng ngay)</option>
                                <option value="0">0 - Tạm dừng / Nháp</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khuyen-mai.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu mã khuyến mãi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
