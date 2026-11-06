<?php
// app/admin/bao-hanh/edit.php
$pageTitle = "Chi tiết phiếu bảo hành điện tử";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPBH = trim($_GET['maPhieuBH'] ?? $_GET['id'] ?? '');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$errors = [];

$sql = "SELECT * FROM phieubaohanh WHERE maPhieuBH = '$maPBH'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy phiếu bảo hành $maPBH!'); window.location.href='bao-hanh.php';</script>";
    exit;
}
$pbh = $res->fetch_assoc();

// Lấy tên khách hàng từ bảng khachhang
$pbh['hoVaTen'] = 'Khách hàng';
if (!empty($pbh['maKH'])) {
    $resKH = $conn->query("SELECT hoVaTen FROM khachhang WHERE maKH = '" . $pbh['maKH'] . "'");
    if ($resKH && $resKH->num_rows > 0) {
        $pbh['hoVaTen'] = $resKH->fetch_assoc()['hoVaTen'];
    }
}

// Lấy tên sản phẩm từ serialsanpham và sanpham
$pbh['tenSP'] = 'Laptop chính hãng';
if (!empty($pbh['maSerial_IMEI'])) {
    $resSer = $conn->query("SELECT maSP FROM serialsanpham WHERE maSerial_IMEI = '" . $pbh['maSerial_IMEI'] . "'");
    if ($resSer && $resSer->num_rows > 0) {
        $maSP = $resSer->fetch_assoc()['maSP'];
        if (!empty($maSP)) {
            $resSP = $conn->query("SELECT tenSP FROM sanpham WHERE maSP = '$maSP'");
            if ($resSP && $resSP->num_rows > 0) {
                $pbh['tenSP'] = $resSP->fetch_assoc()['tenSP'];
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $trangThai = trim($_POST['trangThai'] ?? '');
    $moTaChinhSach = trim($_POST['moTaChinhSach'] ?? '');
    $ngayKetThuc = trim($_POST['ngayKetThuc'] ?? '');

    if (empty($trangThai)) {
        $errors[] = "Trạng thái không được để trống.";
    }

    if (empty($errors)) {
        $sqlUpdate = "UPDATE phieubaohanh SET 
                        trangThai = '$trangThai',
                        moTaChinhSach = '$moTaChinhSach',
                        ngayKetThuc = '$ngayKetThuc'
                      WHERE maPhieuBH = '$maPBH'";
        if ($conn->query($sqlUpdate)) {
            // Nếu đã bàn giao, chuyển serial về lại trạng thái đã bán
            if (stripos($trangThai, 'bàn giao') !== false) {
                $ser = $pbh['maSerial_IMEI'];
                $conn->query("UPDATE serialsanpham SET trangThai = 'Đã bán' WHERE maSerial_IMEI = '$ser'");
            }
            echo "<script>window.location.href='bao-hanh.php';</script>";
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
                        <li class="breadcrumb-item"><a href="bao-hanh.php" class="text-decoration-none text-muted">Bảo hành điện tử</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chi tiết (<?= htmlspecialchars($maPBH) ?>)</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Chi Tiết Phiếu Bảo Hành: <?= htmlspecialchars($maPBH) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Xem thông tin máy laptop, thời hạn bảo hành điện tử chính hãng và cập nhật trạng thái khi khách gửi máy bảo hành.</p>
            </div>
            <a href="bao-hanh.php" class="btn-back-pill">
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
                    <i class="bi bi-shield-check text-primary me-2"></i> Thông tin phiếu bảo hành
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu BH</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light fw-bold" 
                                   value="<?= htmlspecialchars($pbh['maPhieuBH']) ?>" readonly>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Mã Serial / IMEI máy</label>
                            <input type="text" class="form-control font-monospace bg-light fw-bold text-primary" 
                                   value="<?= htmlspecialchars($pbh['maSerial_IMEI']) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sản phẩm laptop</label>
                            <input type="text" class="form-control bg-light" 
                                   value="<?= htmlspecialchars($pbh['tenSP'] ?? 'Laptop') ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Khách hàng sở hữu</label>
                            <input type="text" class="form-control bg-light" 
                                   value="<?= htmlspecialchars($pbh['hoVaTen'] ?? $pbh['maKH']) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="text" class="form-control bg-light" 
                                   value="<?= date('d/m/Y H:i', strtotime($pbh['ngayBatDau'])) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày kết thúc hiệu lực</label>
                            <input type="datetime-local" name="ngayKetThuc" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($pbh['ngayKetThuc'])) ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Trạng thái hiện tại <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select">
                                <option value="Đang tiếp nhận bảo hành" <?= $pbh['trangThai'] === 'Đang tiếp nhận bảo hành' ? 'selected' : '' ?>>Đang tiếp nhận bảo hành (Máy tại xưởng)</option>
                                <option value="Đang sửa chữa / Chờ linh kiện" <?= $pbh['trangThai'] === 'Đang sửa chữa / Chờ linh kiện' ? 'selected' : '' ?>>Đang sửa chữa / Chờ linh kiện thay thế</option>
                                <option value="Đã bàn giao khách" <?= $pbh['trangThai'] === 'Đã bàn giao khách' ? 'selected' : '' ?>>Đã bàn giao máy lại cho khách (Hoàn tất)</option>
                                <option value="Đang hiệu lực" <?= $pbh['trangThai'] === 'Đang hiệu lực' ? 'selected' : '' ?>>Đang hiệu lực (Chưa có phát sinh lỗi)</option>
                                <option value="Hết hạn bảo hành" <?= $pbh['trangThai'] === 'Hết hạn bảo hành' ? 'selected' : '' ?>>Hết hạn bảo hành</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả hiện trạng lỗi / Linh kiện thay thế</label>
                            <textarea name="moTaChinhSach" rows="3" class="form-control"><?= htmlspecialchars($pbh['moTaChinhSach'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="bao-hanh.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi phiếu BH
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
