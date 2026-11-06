<?php
// app/admin/khach-hang/edit.php
$pageTitle = "Chỉnh sửa khách hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maKH = trim($_GET['maKH'] ?? '');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$errors = [];

$sql = "SELECT * FROM khachhang WHERE maKH = '$maKH'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy khách hàng $maKH!'); window.location.href='khach-hang.php';</script>";
    exit;
}
$kh = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $hoVaTen = trim($_POST['hoVaTen'] ?? '');
    $soDienThoai = trim($_POST['soDienThoai'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $diaChi = trim($_POST['diaChi'] ?? '');
    $doiTuongUuTien = intval($_POST['doiTuongUuTien'] ?? 0);
    $maTk = trim($_POST['maTk'] ?? '');
    if (empty($maTk)) {
        $maTkVal = 'NULL';
    } else {
        $maTkVal = "'$maTk'";
    }

    if (empty($hoVaTen)) {
        $errors[] = "Họ và tên không được để trống.";
    }
    if (empty($soDienThoai)) {
        $errors[] = "Số điện thoại không được để trống.";
    }

    // Kiểm tra trùng SĐT với người khác
    $sql_check_phone = "SELECT maKH FROM khachhang WHERE soDienThoai = '$soDienThoai' AND maKH != '$maKH'";
    if ($conn->query($sql_check_phone)->num_rows > 0) {
        $errors[] = "Số điện thoại này đã được sử dụng bởi khách hàng khác.";
    }

    if (empty($errors)) {
        $sqlUpdate = "UPDATE khachhang SET 
                        hoVaTen = '$hoVaTen',
                        soDienThoai = '$soDienThoai',
                        email = '$email',
                        diaChi = '$diaChi',
                        doiTuongUuTien = $doiTuongUuTien,
                        maTk = $maTkVal
                      WHERE maKH = '$maKH'";
        if ($conn->query($sqlUpdate)) {
            echo "<script>window.location.href='khach-hang.php';</script>";
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
                        <li class="breadcrumb-item"><a href="khach-hang.php" class="text-decoration-none text-muted">Khách hàng</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa (<?= htmlspecialchars($maKH) ?>)</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật hồ sơ: <?= htmlspecialchars($kh['hoVaTen']) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin khách hàng, số điện thoại liên hệ và hạng ưu đãi</p>
            </div>
            <a href="khach-hang.php" class="btn-back-pill">
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
                    <i class="bi bi-person-circle me-2 text-primary"></i> Thông tin khách hàng & Liên hệ
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã khách hàng (maKH) <span class="text-danger">*</span></label>
                            <input type="text" name="maKH" class="form-control font-monospace text-uppercase bg-light fw-bold" 
                                   value="<?= htmlspecialchars($kh['maKH']) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK)</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Họ và tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" name="hoVaTen" class="form-control" 
                                   value="<?= htmlspecialchars($kh['hoVaTen']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tài khoản đăng nhập hệ thống</label>
                            <select name="maTk" class="form-select">
                                <option value="">-- Khách vãng lai (không gắn tài khoản) --</option>
                                <?php
                                $currentTk = $kh['maTk'] ?? '';
                                $sqlAcc = "SELECT * FROM accounts WHERE role = 'user' ORDER BY tenTaiKhoan ASC";
                                $resAcc = $conn->query($sqlAcc);
                                if ($resAcc && $resAcc->num_rows > 0) {
                                    while ($acc = $resAcc->fetch_assoc()) {
                                        $curTk = $acc['maTk'];
                                        if ($curTk !== $currentTk) {
                                            $chkKH = $conn->query("SELECT maKH FROM khachhang WHERE maTk = '$curTk'");
                                            if ($chkKH && $chkKH->num_rows > 0) {
                                                continue; // Đã liên kết với khách hàng khác rồi
                                            }
                                        }
                                        $sel = ($currentTk === $acc['maTk']) ? 'selected' : '';
                                        echo "<option value='{$acc['maTk']}' $sel>" . htmlspecialchars($acc['tenTaiKhoan']) . " (" . htmlspecialchars($acc['hoTen']) . ")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" class="form-control font-monospace" 
                                   value="<?= htmlspecialchars($kh['soDienThoai']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($kh['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phân hạng ưu tiên</label>
                            <select name="doiTuongUuTien" class="form-select">
                                <option value="0" <?= $kh['doiTuongUuTien'] == 0 ? 'selected' : '' ?>>0 - Khách hàng tiêu chuẩn</option>
                                <option value="1" <?= $kh['doiTuongUuTien'] == 1 ? 'selected' : '' ?>>1 - Khách hàng VIP thân thiết</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ nhận hàng</label>
                            <input type="text" name="diaChi" class="form-control" 
                                   value="<?= htmlspecialchars($kh['diaChi'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khach-hang.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
