<?php
// app/admin/khach-hang/create.php
$pageTitle = "Thêm khách hàng mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

// Gợi ý mã KH tiếp theo (VD: KH006)
$resMax = $conn->query("SELECT maKH FROM khachhang ORDER BY maKH DESC LIMIT 1");
$autoMaKH = 'KH001';
if ($resMax && $resMax->num_rows > 0) {
    $rowMax = $resMax->fetch_assoc();
    $lastMa = $rowMax['maKH'];
    if (preg_match('/^KH(\d+)$/', $lastMa, $m)) {
        $num = intval($m[1]) + 1;
        $autoMaKH = 'KH' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maKH = trim($_POST['maKH'] ?? '');
    $hoVaTen = trim($_POST['hoVaTen'] ?? '');
    $soDienThoai = trim($_POST['soDienThoai'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $diaChi = trim($_POST['diaChi'] ?? '');
    $doiTuongUuTien = intval($_POST['doiTuongUuTien'] ?? 0);
    $maTk = trim($_POST['maTk'] ?? '');
    if (empty($maTk)) {
        $maTk = 'NULL';
    } else {
        $maTk = "'$maTk'";
    }

    if (empty($maKH)) {
        $errors[] = 'Mã khách hàng không được để trống';
    }
    if (empty($hoVaTen)) {
        $errors[] = 'Họ và tên khách hàng không được để trống';
    }
    if (empty($soDienThoai)) {
        $errors[] = 'Số điện thoại không được để trống';
    }

    // Kiểm tra trùng mã KH
    $sql_check = "SELECT maKH FROM khachhang WHERE maKH = '$maKH'";
    if ($conn->query($sql_check)->num_rows > 0) {
        $errors[] = 'Mã khách hàng đã tồn tại';
    }

    // Kiểm tra trùng SĐT
    $sql_check_phone = "SELECT maKH FROM khachhang WHERE soDienThoai = '$soDienThoai'";
    if ($conn->query($sql_check_phone)->num_rows > 0) {
        $errors[] = 'Số điện thoại này đã được đăng ký cho khách hàng khác';
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO khachhang (maKH, maTk, hoVaTen, soDienThoai, email, diaChi, doiTuongUuTien) 
                       VALUES ('$maKH', $maTk, '$hoVaTen', '$soDienThoai', '$email', '$diaChi', $doiTuongUuTien)";
        if ($conn->query($sql_insert)) {
            echo "<script>window.location.href='khach-hang.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi lưu khách hàng: " . $conn->error;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm Khách Hàng Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Tạo hồ sơ khách hàng mới, lưu trữ thông tin liên hệ và phân hạng ưu đãi.</p>
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

        <form action="create.php" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-person-circle me-2 text-primary"></i> Thông tin khách hàng & Liên hệ
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã khách hàng (maKH) <span class="text-danger">*</span></label>
                            <input type="text" name="maKH" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= htmlspecialchars($_POST['maKH'] ?? $autoMaKH) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Họ và tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" name="hoVaTen" class="form-control" 
                                   placeholder="VD: Lê Thị Kim Ngân" value="<?= htmlspecialchars($_POST['hoVaTen'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tài khoản đăng nhập hệ thống</label>
                            <select name="maTk" class="form-select">
                                <option value="">-- Khách mua vãng lai (không gắn tài khoản) --</option>
                                <?php
                                $sqlAcc = "SELECT * FROM accounts WHERE role = 'user' ORDER BY tenTaiKhoan ASC";
                                $resAcc = $conn->query($sqlAcc);
                                if ($resAcc && $resAcc->num_rows > 0) {
                                    while ($acc = $resAcc->fetch_assoc()) {
                                        $curTk = $acc['maTk'];
                                        $chkKH = $conn->query("SELECT maKH FROM khachhang WHERE maTk = '$curTk'");
                                        if ($chkKH && $chkKH->num_rows > 0) {
                                            continue; // Đã liên kết với khách hàng khác rồi
                                        }
                                        echo "<option value='{$acc['maTk']}'>" . htmlspecialchars($acc['tenTaiKhoan']) . " (" . htmlspecialchars($acc['hoTen']) . ")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" class="form-control font-monospace" 
                                   placeholder="VD: 0912 888 999" value="<?= htmlspecialchars($_POST['soDienThoai'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="VD: kimngan@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phân hạng khách hàng (doiTuongUuTien)</label>
                            <select name="doiTuongUuTien" class="form-select">
                                <option value="0" selected>0 - Khách hàng tiêu chuẩn</option>
                                <option value="1">1 - Khách hàng VIP thân thiết</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ nhận hàng / Nơi cư trú</label>
                            <input type="text" name="diaChi" class="form-control" 
                                   placeholder="Số nhà, đường, phường, quận/huyện, tỉnh/TP..." value="<?= htmlspecialchars($_POST['diaChi'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="khach-hang.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu khách hàng
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
