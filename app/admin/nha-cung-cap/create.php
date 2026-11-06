<?php
// app/admin/nha-cung-cap/create.php
$pageTitle = "Thêm nhà cung cấp mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maNCC = trim($_POST['maNCC'] ?? '');
    $tenCongTy = trim($_POST['tenCongTy'] ?? '');
    $soDienThoai = trim($_POST['soDienThoai'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $diaChiTruSo = trim($_POST['diaChiTruSo'] ?? '');
    $quocTich = trim($_POST['quocTich'] ?? 'Việt Nam');
    $maSoThue = trim($_POST['maSoThue'] ?? '');
    $trangThai = trim($_POST['trangThai'] ?? '1');

    if (empty($maNCC)) {
        $errors[] = 'Mã nhà cung cấp không được để trống';
    }
    if (empty($tenCongTy)) {
        $errors[] = 'Tên công ty doanh nghiệp không được để trống';
    }
    if (empty($soDienThoai)) {
        $errors[] = 'Số điện thoại liên hệ không được để trống';
    }

    // Kiểm tra trùng mã NCC
    $sql_check = "SELECT maNCC FROM nhacungcap WHERE maNCC = '$maNCC'";
    $result_check = $conn->query($sql_check);
    if ($result_check && $result_check->num_rows > 0) {
        $errors[] = 'Mã nhà cung cấp "' . htmlspecialchars($maNCC) . '" đã tồn tại trong hệ thống';
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO nhacungcap (maNCC, tenCongTy, soDienThoai, email, diaChiTruSo, quocTich, maSoThue, trangThai) 
                       VALUES ('$maNCC', '$tenCongTy', '$soDienThoai', '$email', '$diaChiTruSo', '$quocTich', '$maSoThue', '$trangThai')";
        if ($conn->query($sql_insert)) {
            echo "<script>window.location.href='nha-cung-cap.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi lưu nhà cung cấp: " . $conn->error;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm nhà cung cấp mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập thông tin đối tác cung ứng và đầu mối nhập kho laptop chính hãng</p>
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

        <form action="create.php" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-building me-2 text-primary"></i> Thông tin pháp nhân & Doanh nghiệp
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã nhà cung cấp (maNCC) <span class="text-danger">*</span></label>
                            <input type="text" name="maNCC" class="form-control font-monospace text-uppercase" 
                                   placeholder="VD: NCC_ASUS, NCC_DELL, NCC_FPT..." value="<?= htmlspecialchars($_POST['maNCC'] ?? '') ?>" required>
                            <small class="text-muted">Khóa chính (PK) viết tắt</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tên công ty doanh nghiệp (tenCongTy) <span class="text-danger">*</span></label>
                            <input type="text" name="tenCongTy" class="form-control" 
                                   placeholder="VD: Công ty Cổ phần Thế Giới Số (Digiworld)" value="<?= htmlspecialchars($_POST['tenCongTy'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mã số thuế (maSoThue)</label>
                            <input type="text" name="maSoThue" class="form-control font-monospace" 
                                   placeholder="VD: 0302861742" value="<?= htmlspecialchars($_POST['maSoThue'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quốc gia / Quốc tịch</label>
                            <input type="text" name="quocTich" class="form-control" value="<?= htmlspecialchars($_POST['quocTich'] ?? 'Việt Nam') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái hợp tác (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" <?= (!isset($_POST['trangThai']) || $_POST['trangThai'] === '1') ? 'selected' : '' ?>>1 - Đang hợp tác nhập hàng</option>
                                <option value="0" <?= (isset($_POST['trangThai']) && $_POST['trangThai'] === '0') ? 'selected' : '' ?>>0 - Tạm dừng giao dịch</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ (soDienThoai) <span class="text-danger">*</span></label>
                            <input type="text" name="soDienThoai" class="form-control" placeholder="VD: 028 3929 0059" value="<?= htmlspecialchars($_POST['soDienThoai'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email liên hệ / Đặt hàng</label>
                            <input type="email" name="email" class="form-control" placeholder="VD: contact@synnexfpt.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ trụ sở chính (diaChiTruSo)</label>
                            <input type="text" name="diaChiTruSo" class="form-control" 
                                   placeholder="Số nhà, đường, phường, quận, tỉnh/TP..." value="<?= htmlspecialchars($_POST['diaChiTruSo'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="nha-cung-cap.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu nhà cung cấp
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
