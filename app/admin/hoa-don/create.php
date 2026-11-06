<?php
// app/admin/hoa-don/create.php
$pageTitle = "Xuất hóa đơn bán hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

$maDHFromURL = htmlspecialchars($_GET['maDH'] ?? $_GET['dh'] ?? '');
$autoMaHD = 'HD' . date('ymd') . rand(100, 999);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maHD = trim($_POST['maHD'] ?? '');
    $maDH = trim($_POST['maDH'] ?? '');
    $tenKhachHang = trim($_POST['tenKhachHang'] ?? '');
    $soDienThoai = trim($_POST['soDienThoai'] ?? '');
    $diaChiKhachHang = trim($_POST['diaChiKhachHang'] ?? '');
    $maSoThueKH = trim($_POST['maSoThueKH'] ?? '');
    $soTienThanhToan = floatval($_POST['soTienThanhToan'] ?? 0);
    $trangThaiThanhToan = trim($_POST['trangThaiThanhToan'] ?? 'DA_THANH_TOAN');
    $ghiChu = trim($_POST['ghiChu'] ?? '');

    if (empty($maHD)) $errors[] = "Mã hóa đơn không được để trống";
    if (empty($maDH)) $errors[] = "Vui lòng chọn đơn hàng gốc";
    if (empty($tenKhachHang)) $errors[] = "Tên khách hàng không được để trống";
    if (empty($soDienThoai)) $errors[] = "Số điện thoại khách hàng không được để trống";

    // Kiểm tra trùng mã HD
    $chkHD = $conn->query("SELECT maHD FROM hoadon WHERE maHD = '$maHD'");
    if ($chkHD && $chkHD->num_rows > 0) $errors[] = "Mã hóa đơn đã tồn tại trong hệ thống";

    // Kiểm tra đơn hàng này đã xuất hóa đơn chưa 
    $chkDH = $conn->query("SELECT maHD FROM hoadon WHERE maDH = '$maDH'");
    if ($chkDH && $chkDH->num_rows > 0) $errors[] = "Đơn hàng $maDH đã được xuất hóa đơn trước đó";

    if (empty($errors)) {
        $sql_insert = "INSERT INTO hoadon (maHD, maDH, ngayXuat, tenKhachHang, soDienThoai, diaChiKhachHang, maSoThueKH, soTienThanhToan, trangThaiThanhToan, ghiChu) 
                       VALUES ('$maHD', '$maDH', NOW(), '$tenKhachHang', '$soDienThoai', '$diaChiKhachHang', '$maSoThueKH', $soTienThanhToan, '$trangThaiThanhToan', '$ghiChu')";
        if ($conn->query($sql_insert)) {
            echo "<script>window.location.href='detail.php?id=" . urlencode($maHD) . "';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi xuất hóa đơn: " . $conn->error;
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
                        <li class="breadcrumb-item"><a href="hoa-don.php" class="text-decoration-none text-muted">Hóa đơn</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Xuất mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Xuất Hóa Đơn Bán Hàng Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Khởi tạo chứng từ hóa đơn VAT điện tử dựa trên đơn bán hàng trong hệ thống.</p>
            </div>
            <a href="hoa-don.php" class="btn-back-pill">
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
                    <i class="bi bi-receipt text-primary me-2"></i> Thông tin chứng từ hóa đơn
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mã hóa đơn (maHD) <span class="text-danger">*</span></label>
                            <input type="text" name="maHD" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= htmlspecialchars($_POST['maHD'] ?? $autoMaHD) ?>" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Đơn hàng gốc cần xuất hóa đơn <span class="text-danger">*</span></label>
                            <select name="maDH" id="selectDonHang" class="form-select" required>
                                <option value="">-- Chọn đơn hàng trong hệ thống --</option>
                                <?php
                                $sqlDHList = "SELECT * FROM donhang ORDER BY ngayTao DESC";
                                $resDHList = $conn->query($sqlDHList);
                                if ($resDHList && $resDHList->num_rows > 0) {
                                    while ($d = $resDHList->fetch_assoc()) {
                                        $curMaDH = $d['maDH'];

                                        // Kiểm tra xem đơn hàng đã xuất hóa đơn chưa
                                        $chkHD = $conn->query("SELECT maHD FROM hoadon WHERE maDH = '$curMaDH'");
                                        if ($chkHD && $chkHD->num_rows > 0 && $curMaDH !== $maDHFromURL) {
                                            continue; // Đã xuất hóa đơn rồi thì bỏ qua
                                        }

                                        // Lấy thông tin khách hàng từ bảng khachhang
                                        $hoVaTen = "Khách vãng lai";
                                        $soDienThoai = "";
                                        $diaChi = "";
                                        if (!empty($d['maKH'])) {
                                            $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '" . $d['maKH'] . "'");
                                            if ($resKH && $resKH->num_rows > 0) {
                                                $rowKH = $resKH->fetch_assoc();
                                                $hoVaTen = $rowKH['hoVaTen'];
                                                $soDienThoai = $rowKH['soDienThoai'];
                                                $diaChi = $rowKH['diaChi'];
                                            }
                                        }

                                        $cntMay = 0;
                                        $resCntM = $conn->query("SELECT SUM(soLuong) as totalM FROM chitietdonhang WHERE maDH = '{$d['maDH']}'");
                                        if ($resCntM && $rM = $resCntM->fetch_assoc()) {
                                            $cntMay = (int)($rM['totalM'] ?? 0);
                                        }

                                        $sel = ($maDHFromURL === $d['maDH']) ? 'selected' : '';
                                        echo "<option value='{$d['maDH']}' 
                                                     data-ten='" . htmlspecialchars($hoVaTen) . "' 
                                                     data-sdt='" . htmlspecialchars($soDienThoai) . "' 
                                                     data-diachi='" . htmlspecialchars($diaChi) . "' 
                                                     data-tien='{$d['tongThanhToan']}' $sel>
                                                #{$d['maDH']} - " . htmlspecialchars($hoVaTen) . " ({$cntMay} máy - " . number_format($d['tongThanhToan'], 0, ',', '.') . " ₫)
                                              </option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tên khách hàng / Đơn vị mua <span class="text-danger">*</span></label>
                            <input type="text" name="tenKhachHang" id="inputTenKH" class="form-control" 
                                   placeholder="Họ tên người mua hoặc tên công ty" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoai" id="inputSDT" class="form-control font-monospace" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Địa chỉ giao dịch</label>
                            <input type="text" name="diaChiKhachHang" id="inputDiaChi" class="form-control" 
                                   placeholder="Địa chỉ trụ sở hoặc nơi nhận hàng">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Mã số thuế doanh nghiệp (nếu có)</label>
                            <input type="text" name="maSoThueKH" class="form-control font-monospace" placeholder="VD: 0109876543">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số tiền thanh toán hóa đơn (₫) <span class="text-danger">*</span></label>
                            <input type="number" step="1000" name="soTienThanhToan" id="inputSoTien" class="form-control font-monospace fw-bold text-danger" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trạng thái thanh toán</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="DA_THANH_TOAN" selected>Đã thanh toán đủ</option>
                                <option value="CHUA_THANH_TOAN">Chưa thanh toán</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ghi chú chứng từ</label>
                            <textarea name="ghiChu" rows="2" class="form-control" placeholder="Ghi chú thêm về hình thức thanh toán hoặc xuất hóa đơn điện tử..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="hoa-don.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-printer me-1"></i> Lưu và xem hóa đơn
                </button>
            </div>
        </form>
    </main>
</div>

<script>
    function updateFromOrder() {
        const sel = document.getElementById('selectDonHang');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('inputTenKH').value = opt.getAttribute('data-ten') || '';
            document.getElementById('inputSDT').value = opt.getAttribute('data-sdt') || '';
            document.getElementById('inputDiaChi').value = opt.getAttribute('data-diachi') || '';
            document.getElementById('inputSoTien').value = opt.getAttribute('data-tien') || 0;
        }
    }
    document.getElementById('selectDonHang').addEventListener('change', updateFromOrder);
    updateFromOrder();
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
