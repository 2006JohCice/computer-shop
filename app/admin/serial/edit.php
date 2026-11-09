<?php
// app/admin/serial/edit.php
$pageTitle = "Chỉnh sửa trạng thái Serial";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$maSerial = trim($_GET['serial'] ?? $_POST['maSerial_IMEI'] ?? '');
if (empty($maSerial)) {
    header("Location: serial.php");
    exit();
}

$serEsc = $conn->real_escape_string($maSerial);
$errorMsg = '';

// 1. XỬ LÝ SUBMIT CẬP NHẬT (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trangThai = trim($_POST['trangThai'] ?? 'Trong kho');
    $ngayXuatKho = trim($_POST['ngayXuatKho'] ?? '');
    $ghiChu = trim($_POST['ghiChu'] ?? '');

    $stEsc = $conn->real_escape_string($trangThai);
    $gcEsc = $conn->real_escape_string($ghiChu);

    $xuatVal = "NULL";
    if (!empty($ngayXuatKho)) {
        $xuatEsc = $conn->real_escape_string(date('Y-m-d H:i:s', strtotime($ngayXuatKho)));
        $xuatVal = "'$xuatEsc'";
    } elseif ($trangThai === 'Đã bán') {
        $xuatVal = "NOW()";
    }

    $sqlUpdate = "UPDATE serialsanpham 
                  SET trangThai = '$stEsc', 
                      ngayXuatKho = $xuatVal, 
                      ghiChu = '$gcEsc' 
                  WHERE maSerial_IMEI = '$serEsc'";

    if ($conn->query($sqlUpdate)) {
        $_SESSION['flash_success'] = "Đã cập nhật thành công trạng thái mã Serial <strong>$maSerial</strong>!";
        header("Location: serial.php");
        exit();
    } else {
        $errorMsg = "Lỗi cập nhật CSDL: " . $conn->error;
    }
}

// 2. LẤY DỮ LIỆU SERIAL HIỆN TẠI
$sqlGet = "SELECT s.*, sp.tenSP, sp.thoiGianBaoHanh, cn.maPhieu, dh.maDH 
           FROM serialsanpham s
           LEFT JOIN sanpham sp ON s.maSP = sp.maSP
           LEFT JOIN chitietnhap cn ON s.idChiTietNhap = cn.id
           LEFT JOIN chitietdonhang ctdh ON s.idChiTietDonHang = ctdh.id
           LEFT JOIN donhang dh ON ctdh.maDH = dh.maDH
           WHERE s.maSerial_IMEI = '$serEsc' LIMIT 1";
$resGet = $conn->query($sqlGet);
if (!$resGet || $resGet->num_rows === 0) {
    $_SESSION['flash_error'] = "Không tìm thấy mã Serial $maSerial trong CSDL!";
    header("Location: serial.php");
    exit();
}
$serialRow = $resGet->fetch_assoc();
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
                        <li class="breadcrumb-item"><a href="serial.php" class="text-decoration-none text-muted">Serial / IMEI</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật Serial: <?= htmlspecialchars($maSerial) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Cập nhật trạng thái vòng đời thiết bị, thông tin xuất kho và ghi chú bảo trì.</p>
            </div>
            <a href="serial.php" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div><?= $errorMsg ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form Cập Nhật Trạng Thái Serial -->
        <form action="edit.php?serial=<?= urlencode($maSerial) ?>" method="POST">
            <input type="hidden" name="maSerial_IMEI" value="<?= htmlspecialchars($maSerial) ?>">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Trạng Thái & Thông Tin Thiết Bị (SerialSanPham)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã Serial / IMEI</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light fw-bold text-primary" value="<?= htmlspecialchars($serialRow['maSerial_IMEI']) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sản phẩm laptop</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($serialRow['maSP']) ?> - <?= htmlspecialchars($serialRow['tenSP'] ?? 'Laptop Chính Hãng') ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phiếu nhập kho ban đầu</label>
                            <input type="text" class="form-control bg-light font-monospace" value="<?= htmlspecialchars($serialRow['maPhieu'] ?? ('Lô #' . $serialRow['idChiTietNhap'])) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn hàng xuất bán</label>
                            <input type="text" class="form-control bg-light font-monospace text-success fw-bold" value="<?= !empty($serialRow['maDH']) ? ('#' . htmlspecialchars($serialRow['maDH'])) : '— Chưa xuất bán' ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái thiết bị <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="Trong kho" <?= ($serialRow['trangThai'] === 'Trong kho') ? 'selected' : '' ?>>Trong kho (Sẵn sàng bán)</option>
                                <option value="Đã bán" <?= ($serialRow['trangThai'] === 'Đã bán') ? 'selected' : '' ?>>Đã bán cho khách</option>
                                <option value="Đang bảo hành" <?= ($serialRow['trangThai'] === 'Đang bảo hành') ? 'selected' : '' ?>>Đang bảo hành</option>
                                <option value="Lỗi đổi trả" <?= ($serialRow['trangThai'] === 'Lỗi đổi trả') ? 'selected' : '' ?>>Lỗi đổi trả nhà sản xuất</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thời gian xuất kho</label>
                            <input type="datetime-local" name="ngayXuatKho" class="form-control font-monospace" 
                                   value="<?= !empty($serialRow['ngayXuatKho']) ? date('Y-m-d\TH:i', strtotime($serialRow['ngayXuatKho'])) : '' ?>">
                            <small class="text-muted">Để trống nếu máy đang lưu kho chưa xuất bán</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú thiết bị (ghiChu)</label>
                            <textarea name="ghiChu" rows="3" class="form-control" placeholder="Cập nhật tình trạng máy, kiểm định chất lượng, lịch sử sửa chữa..."><?= htmlspecialchars($serialRow['ghiChu'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="serial.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi Serial
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
