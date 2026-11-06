<?php
// app/admin/bao-hanh/create.php
$pageTitle = "Kích hoạt bảo hành điện tử";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

// Gợi ý mã bảo hành tiếp theo (VD: BH2026_004)
$resMax = $conn->query("SELECT maPhieuBH FROM phieubaohanh ORDER BY maPhieuBH DESC LIMIT 1");
$autoMaPBH = 'BH' . date('Y') . '_001';
if ($resMax && $resMax->num_rows > 0) {
    $rowMax = $resMax->fetch_assoc();
    $lastMa = $rowMax['maPhieuBH'];
    if (preg_match('/^BH(\d+)_(\d+)$/', $lastMa, $m)) {
        $year = $m[1];
        $num = intval($m[2]) + 1;
        $autoMaPBH = 'BH' . $year . '_' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $autoMaPBH = 'BH' . date('Ymd_His');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maPhieuBH = trim($_POST['maPhieuBH'] ?? '');
    $maSerial_IMEI = trim($_POST['maSerial_IMEI'] ?? '');
    $maKH = trim($_POST['maKH'] ?? '');
    $ngayBatDau = trim($_POST['ngayBatDau'] ?? date('Y-m-d H:i:s'));
    $ngayKetThuc = trim($_POST['ngayKetThuc'] ?? date('Y-m-d H:i:s', strtotime('+24 months')));
    $moTaChinhSach = trim($_POST['moTaChinhSach'] ?? '');
    $trangThai = trim($_POST['trangThai'] ?? 'Đang hiệu lực');

    if (empty($maPhieuBH)) $errors[] = "Mã phiếu bảo hành không được để trống";
    if (empty($maSerial_IMEI)) $errors[] = "Vui lòng chọn mã Serial/IMEI máy bảo hành";
    if (empty($maKH)) $errors[] = "Vui lòng chọn khách hàng sở hữu";

    // Kiểm tra trùng mã phiếu BH
    $chk = $conn->query("SELECT maPhieuBH FROM phieubaohanh WHERE maPhieuBH = '$maPhieuBH'");
    if ($chk && $chk->num_rows > 0) $errors[] = "Mã phiếu bảo hành đã tồn tại trong hệ thống";

    // Nếu mô tả chính sách trống, tự động tạo theo thông tin laptop
    if (empty($moTaChinhSach) && !empty($maSerial_IMEI)) {
        $resSer = $conn->query("SELECT s.tenSP, s.thoiGianBaoHanh FROM serialsanpham ser JOIN sanpham s ON ser.maSP = s.maSP WHERE ser.maSerial_IMEI = '$maSerial_IMEI'");
        if ($resSer && $resSer->num_rows > 0) {
            $rowS = $resSer->fetch_assoc();
            $moTaChinhSach = "Bảo hành chính hãng {$rowS['tenSP']} {$rowS['thoiGianBaoHanh']} tháng theo Serial/IMEI, 1 đổi 1 trong 30 ngày nếu lỗi NSX";
        }
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO phieubaohanh (maPhieuBH, maSerial_IMEI, maKH, ngayBatDau, ngayKetThuc, moTaChinhSach, trangThai) 
                       VALUES ('$maPhieuBH', '$maSerial_IMEI', '$maKH', '$ngayBatDau', '$ngayKetThuc', '$moTaChinhSach', '$trangThai')";
        if ($conn->query($sql_insert)) {
            // Cập nhật trạng thái máy trong serialsanpham nếu đang còn là Trong kho
            $conn->query("UPDATE serialsanpham SET trangThai = 'Đã bán', ngayXuatKho = COALESCE(ngayXuatKho, '$ngayBatDau') WHERE maSerial_IMEI = '$maSerial_IMEI' AND trangThai = 'Trong kho'");
            echo "<script>window.location.href='bao-hanh.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi lưu phiếu bảo hành: " . $conn->error;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Kích hoạt bảo hành</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Kích Hoạt Bảo Hành Điện Tử (Cấp Bù)</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Kích hoạt bảo hành chính hãng theo mã Serial/IMEI của máy laptop đã bán cho khách hàng.</p>
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

        <form action="create.php" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-shield-check text-primary me-2"></i> Thông tin kích hoạt bảo hành điện tử
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu BH <span class="text-danger">*</span></label>
                            <input type="text" name="maPhieuBH" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= htmlspecialchars($_POST['maPhieuBH'] ?? $autoMaPBH) ?>" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Khách hàng sở hữu <span class="text-danger">*</span></label>
                            <select name="maKH" class="form-select" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php
                                $sqlKH = "SELECT maKH, hoVaTen, soDienThoai FROM khachhang ORDER BY hoVaTen ASC";
                                $resKH = $conn->query($sqlKH);
                                if ($resKH && $resKH->num_rows > 0) {
                                    while ($k = $resKH->fetch_assoc()) {
                                        $sel = (isset($_POST['maKH']) && $_POST['maKH'] === $k['maKH']) ? 'selected' : '';
                                        echo "<option value='{$k['maKH']}' $sel>" . htmlspecialchars($k['hoVaTen']) . " - " . htmlspecialchars($k['soDienThoai']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Trạng thái bảo hành</label>
                            <select name="trangThai" class="form-select">
                                <option value="Đang hiệu lực" selected>Đang hiệu lực (Kích hoạt)</option>
                                <option value="Đang tiếp nhận bảo hành">Đang tiếp nhận bảo hành</option>
                                <option value="Đã bàn giao khách">Đã bàn giao khách</option>
                                <option value="Hết hạn">Hết hạn</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Máy Serial / IMEI cần kích hoạt bảo hành <span class="text-danger">*</span></label>
                            <select name="maSerial_IMEI" class="form-select font-monospace" required>
                                <option value="">-- Chọn máy theo Serial/IMEI & Model Laptop --</option>
                                <?php
                                $sqlSer = "SELECT * FROM serialsanpham ORDER BY maSerial_IMEI ASC";
                                $resSer = $conn->query($sqlSer);
                                if ($resSer && $resSer->num_rows > 0) {
                                    while ($s = $resSer->fetch_assoc()) {
                                        $maSP = $s['maSP'];
                                        $tenSP = 'Laptop chính hãng';
                                        $bhThang = 24;
                                        if (!empty($maSP)) {
                                            $resSP = $conn->query("SELECT tenSP, thoiGianBaoHanh FROM sanpham WHERE maSP = '$maSP'");
                                            if ($resSP && $resSP->num_rows > 0) {
                                                $rSP = $resSP->fetch_assoc();
                                                $tenSP = $rSP['tenSP'];
                                                $bhThang = $rSP['thoiGianBaoHanh'] ?? 24;
                                            }
                                        }
                                        $sel = (isset($_POST['maSerial_IMEI']) && $_POST['maSerial_IMEI'] === $s['maSerial_IMEI']) ? 'selected' : '';
                                        echo "<option value='{$s['maSerial_IMEI']}' $sel>{$s['maSerial_IMEI']} - " . htmlspecialchars($tenSP) . " ({$bhThang} tháng) [{$s['trangThai']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu kích hoạt</label>
                            <input type="datetime-local" name="ngayBatDau" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày hết hạn bảo hành</label>
                            <input type="datetime-local" name="ngayKetThuc" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime('+24 months')) ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Chính sách bảo hành / Quyền lợi khách hàng</label>
                            <textarea name="moTaChinhSach" rows="3" class="form-control" 
                                      placeholder="VD: Bảo hành chính hãng 24 tháng theo số Serial/IMEI, 1 đổi 1 trong 30 ngày nếu lỗi NSX..."><?= htmlspecialchars($_POST['moTaChinhSach'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="bao-hanh.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-shield-check me-1"></i> Kích hoạt phiếu bảo hành
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
