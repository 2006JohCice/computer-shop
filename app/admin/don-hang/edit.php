<?php
// app/admin/don-hang/edit.php
$pageTitle = "Chỉnh sửa đơn hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDH = trim($_GET['maDH'] ?? $_GET['id'] ?? '');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$errors = [];

$sql = "SELECT * FROM donhang WHERE maDH = '$maDH'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy đơn hàng $maDH!'); window.location.href='don-hang.php';</script>";
    exit;
}
$dh = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $maKH = trim($_POST['maKH'] ?? '');
    $maKM = trim($_POST['maKM'] ?? '');
    $nguoiPhuTrach = trim($_POST['nguoiPhuTrach'] ?? '');
    $kenhBan = trim($_POST['kenhBan'] ?? 'TAI_QUAY');
    $phuongThucThanhToan = trim($_POST['phuongThucThanhToan'] ?? 'TIEN_MAT');
    $phuongThucGiaoHang = trim($_POST['phuongThucGiaoHang'] ?? '');
    $phiVanChuyen = floatval($_POST['phiVanChuyen'] ?? 0);
    $trangThaiDon = trim($_POST['trangThaiDon'] ?? 'CHO_DUYET');

    if (empty($maKH)) {
        $errors[] = "Vui lòng chọn khách hàng.";
    }

    if (empty($errors)) {
        $maKMVal = !empty($maKM) ? "'$maKM'" : "NULL";
        $nguoiVal = !empty($nguoiPhuTrach) ? "'$nguoiPhuTrach'" : "NULL";

        // Tính lại tổng thanh toán = tongTienHang - tienGiamGia + phiVanChuyen
        $tongMoi = max(0, $dh['tongTienHang'] - $dh['tienGiamGia'] + $phiVanChuyen);

        $sqlUpdate = "UPDATE donhang SET 
                        maKH = '$maKH',
                        maKM = $maKMVal,
                        nguoiPhuTrach = $nguoiVal,
                        kenhBan = '$kenhBan',
                        phuongThucThanhToan = '$phuongThucThanhToan',
                        phuongThucGiaoHang = '$phuongThucGiaoHang',
                        phiVanChuyen = $phiVanChuyen,
                        tongThanhToan = $tongMoi,
                        trangThaiDon = '$trangThaiDon'
                      WHERE maDH = '$maDH'";
        if ($conn->query($sqlUpdate)) {
            echo "<script>window.location.href='don-hang.php';</script>";
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
                        <li class="breadcrumb-item"><a href="don-hang.php" class="text-decoration-none text-muted">Đơn hàng</a></li>
                        <li class="breadcrumb-item"><a href="detail.php?id=<?= urlencode($maDH) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($maDH) ?></a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập Nhật Đơn Hàng #<?= htmlspecialchars($maDH) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin thanh toán, phương thức giao nhận và phân công phụ trách đơn.</p>
            </div>
            <a href="detail.php?id=<?= urlencode($maDH) ?>" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại chi tiết
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
                    <i class="bi bi-pencil-square me-2 text-primary"></i> Thông tin cơ bản đơn hàng
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã đơn hàng</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light fw-bold" 
                                   value="<?= htmlspecialchars($dh['maDH']) ?>" readonly>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Khách hàng đặt mua <span class="text-danger">*</span></label>
                            <select name="maKH" class="form-select" required>
                                <?php
                                $sqlKH = "SELECT maKH, hoVaTen, soDienThoai FROM khachhang ORDER BY hoVaTen ASC";
                                $resKH = $conn->query($sqlKH);
                                if ($resKH && $resKH->num_rows > 0) {
                                    while ($k = $resKH->fetch_assoc()) {
                                        $sel = ($dh['maKH'] === $k['maKH']) ? 'selected' : '';
                                        echo "<option value='{$k['maKH']}' $sel>" . htmlspecialchars($k['hoVaTen']) . " - " . htmlspecialchars($k['soDienThoai']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mã khuyến mãi áp dụng</label>
                            <select name="maKM" class="form-select">
                                <option value="">-- Không áp dụng voucher --</option>
                                <?php
                                $sqlKM = "SELECT maKM, tenKM FROM khuyenmai ORDER BY tenKM ASC";
                                $resKM = $conn->query($sqlKM);
                                if ($resKM && $resKM->num_rows > 0) {
                                    while ($m = $resKM->fetch_assoc()) {
                                        $sel = ($dh['maKM'] === $m['maKM']) ? 'selected' : '';
                                        echo "<option value='{$m['maKM']}' $sel>" . htmlspecialchars($m['tenKM']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kênh bán hàng</label>
                            <select name="kenhBan" class="form-select">
                                <option value="TAI_QUAY" <?= $dh['kenhBan'] === 'TAI_QUAY' ? 'selected' : '' ?>>Tại quầy Showroom</option>
                                <option value="ONLINE" <?= $dh['kenhBan'] === 'ONLINE' ? 'selected' : '' ?>>Đặt mua Online</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="phuongThucThanhToan" class="form-select">
                                <option value="TIEN_MAT" <?= $dh['phuongThucThanhToan'] === 'TIEN_MAT' ? 'selected' : '' ?>>Tiền mặt</option>
                                <option value="CHUYEN_KHOAN" <?= $dh['phuongThucThanhToan'] === 'CHUYEN_KHOAN' ? 'selected' : '' ?>>Chuyển khoản</option>
                                <option value="VNPay" <?= $dh['phuongThucThanhToan'] === 'VNPay' ? 'selected' : '' ?>>VNPay</option>
                                <option value="COD" <?= $dh['phuongThucThanhToan'] === 'COD' ? 'selected' : '' ?>>COD</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nhân viên phụ trách</label>
                            <select name="nguoiPhuTrach" class="form-select">
                                <option value="">-- Chưa gán --</option>
                                <?php
                                $sqlNV = "SELECT maTk, hoTen, role FROM accounts ORDER BY hoTen ASC";
                                $resNV = $conn->query($sqlNV);
                                if ($resNV && $resNV->num_rows > 0) {
                                    while ($nv = $resNV->fetch_assoc()) {
                                        $sel = ($dh['nguoiPhuTrach'] === $nv['maTk']) ? 'selected' : '';
                                        echo "<option value='{$nv['maTk']}' $sel>" . htmlspecialchars($nv['hoTen']) . " [{$nv['role']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phí vận chuyển (₫)</label>
                            <input type="number" step="1000" name="phiVanChuyen" class="form-control font-monospace" 
                                   value="<?= htmlspecialchars($dh['phiVanChuyen']) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Đơn vị vận chuyển</label>
                            <input type="text" name="phuongThucGiaoHang" class="form-control" 
                                   value="<?= htmlspecialchars($dh['phuongThucGiaoHang'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Trạng thái đơn hàng</label>
                            <select name="trangThaiDon" class="form-select">
                                <option value="CHO_DUYET" <?= $dh['trangThaiDon'] === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
                                <option value="DANG_GIAO" <?= $dh['trangThaiDon'] === 'DANG_GIAO' ? 'selected' : '' ?>>Đang giao hàng</option>
                                <option value="HOAN_THANH" <?= $dh['trangThaiDon'] === 'HOAN_THANH' ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="DA_HUY" <?= $dh['trangThaiDon'] === 'DA_HUY' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="detail.php?id=<?= urlencode($maDH) ?>" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi đơn hàng
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
