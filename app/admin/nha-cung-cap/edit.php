<?php
// app/admin/nha-cung-cap/edit.php
$pageTitle = "Chỉnh sửa nhà cung cấp";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maNCC = trim($_GET['maNCC'] ?? '');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$errors = [];

// Truy vấn thông tin nhà cung cấp hiện tại
$sql = "SELECT * FROM nhacungcap WHERE maNCC = '$maNCC'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy nhà cung cấp $maNCC!'); window.location.href='nha-cung-cap.php';</script>";
    exit;
}
$ncc = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $tenCongTy = trim($_POST['tenCongTy'] ?? '');
    $soDienThoai = trim($_POST['soDienThoai'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $diaChiTruSo = trim($_POST['diaChiTruSo'] ?? '');
    $quocTich = trim($_POST['quocTich'] ?? 'Việt Nam');
    $maSoThue = trim($_POST['maSoThue'] ?? '');
    $trangThai = trim($_POST['trangThai'] ?? '1');

    if (empty($tenCongTy)) {
        $errors[] = "Tên công ty doanh nghiệp không được để trống.";
    }
    if (empty($soDienThoai)) {
        $errors[] = "Số điện thoại không được để trống.";
    }

    if (empty($errors)) {
        $sqlUpdate = "UPDATE nhacungcap SET 
                        tenCongTy = '$tenCongTy',
                        soDienThoai = '$soDienThoai',
                        email = '$email',
                        diaChiTruSo = '$diaChiTruSo',
                        quocTich = '$quocTich',
                        maSoThue = '$maSoThue',
                        trangThai = '$trangThai'
                      WHERE maNCC = '$maNCC'";
        if ($conn->query($sqlUpdate)) {
            echo "<script>window.location.href='nha-cung-cap.php';</script>";
            exit;
        } else {
            $errors[] = "Cập nhật thất bại: " . $conn->error;
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
                        <li class="breadcrumb-item"><a href="nha-cung-cap.php" class="text-decoration-none text-muted">Nhà cung cấp</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa (<?= htmlspecialchars($maNCC) ?>)</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật đối tác: <?= htmlspecialchars($ncc['tenCongTy']) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin pháp nhân và hồ sơ liên hệ nhà cung cấp</p>
            </div>
            <a href="nha-cung-cap.php" class="btn-back-pill">
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

        <form action="" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-building me-2 text-primary"></i> Thông tin pháp nhân & Doanh nghiệp
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <input type="text" name="maNCC" class="form-control font-monospace text-uppercase bg-light fw-bold" 
                                   value="<?= htmlspecialchars($ncc['maNCC']) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK) cố định</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tên công ty doanh nghiệp (tenCongTy) <span class="text-danger">*</span></label>
                            <input type="text" name="tenCongTy" class="form-control" 
                                   value="<?= htmlspecialchars($ncc['tenCongTy']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mã số thuế doanh nghiệp (maSoThue)</label>
                            <input type="text" name="maSoThue" class="form-control font-monospace" 
                                   value="<?= htmlspecialchars($ncc['maSoThue'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quốc gia / Quốc tịch</label>
                            <input type="text" name="quocTich" class="form-control" value="<?= htmlspecialchars($ncc['quocTich'] ?? 'Việt Nam') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái quan hệ cung ứng</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" <?= $ncc['trangThai'] == 1 ? 'selected' : '' ?>>1 - Đang hợp tác nhập hàng</option>
                                <option value="0" <?= $ncc['trangThai'] == 0 ? 'selected' : '' ?>>0 - Tạm dừng giao dịch</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="text" name="soDienThoai" class="form-control" value="<?= htmlspecialchars($ncc['soDienThoai'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email liên hệ / Đặt hàng</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($ncc['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ trụ sở chính (diaChiTruSo)</label>
                            <input type="text" name="diaChiTruSo" class="form-control" 
                                   value="<?= htmlspecialchars($ncc['diaChiTruSo'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="nha-cung-cap.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
