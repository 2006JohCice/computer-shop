<?php
// app/admin/serial/create.php
$pageTitle = "Thêm Serial / IMEI Laptop Mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$errorMsg = '';
$successMsg = '';

// 1. XỬ LÝ SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'single'; // 'single' hoặc 'bulk'
    $maSP = trim($_POST['maSP'] ?? '');
    $idChiTietNhap = (int)($_POST['idChiTietNhap'] ?? 0);
    $trangThai = trim($_POST['trangThai'] ?? 'Trong kho');
    $ghiChu = trim($_POST['ghiChu'] ?? 'Hàng mới 100% nguyên seal');

    if (empty($maSP)) {
        $errorMsg = 'Vui lòng chọn sản phẩm laptop!';
    } elseif ($idChiTietNhap <= 0) {
        $errorMsg = 'Vui lòng chọn phiếu / lô nhập kho hợp lệ!';
    } else {
        $maSPEsc = $conn->real_escape_string($maSP);
        $stEsc = $conn->real_escape_string($trangThai);
        $gcEsc = $conn->real_escape_string($ghiChu);

        if ($mode === 'single') {
            // Thêm 1 mã Serial đơn lẻ
            $maSerial = strtoupper(trim($_POST['maSerial_IMEI'] ?? ''));
            if (empty($maSerial)) {
                $errorMsg = 'Vui lòng nhập mã Serial / IMEI!';
            } else {
                $serEsc = $conn->real_escape_string($maSerial);
                $chk = $conn->query("SELECT maSerial_IMEI FROM serialsanpham WHERE maSerial_IMEI = '$serEsc'");
                if ($chk && $chk->num_rows > 0) {
                    $errorMsg = "Mã Serial <strong>$maSerial</strong> đã tồn tại trong hệ thống, không thể thêm trùng!";
                } else {
                    $sqlIns = "INSERT INTO serialsanpham (maSerial_IMEI, maSP, idChiTietNhap, trangThai, ghiChu) 
                               VALUES ('$serEsc', '$maSPEsc', $idChiTietNhap, '$stEsc', '$gcEsc')";
                    if ($conn->query($sqlIns)) {
                        $_SESSION['flash_success'] = "Đã thêm thành công Serial <strong>$maSerial</strong> cho sản phẩm <strong>$maSP</strong>!";
                        header("Location: serial.php");
                        exit();
                    } else {
                        $errorMsg = "Lỗi thêm Serial: " . $conn->error;
                    }
                }
            }
        } elseif ($mode === 'bulk') {
            // Thêm hàng loạt (Danh sách nhiều Serial hoặc tự sinh theo số lượng)
            $bulkType = $_POST['bulk_type'] ?? 'list'; // 'list' hoặc 'auto'
            $addedCount = 0;
            $skippedCount = 0;

            if ($bulkType === 'list') {
                $rawList = trim($_POST['serial_list'] ?? '');
                $lines = array_filter(array_map('trim', explode("\n", $rawList)));
                if (empty($lines)) {
                    $errorMsg = 'Vui lòng nhập danh sách mã Serial (mỗi mã 1 dòng)!';
                } else {
                    foreach ($lines as $lineSer) {
                        $lineSer = strtoupper(trim($lineSer));
                        if (empty($lineSer)) continue;
                        $sEsc = $conn->real_escape_string($lineSer);
                        $chk = $conn->query("SELECT maSerial_IMEI FROM serialsanpham WHERE maSerial_IMEI = '$sEsc'");
                        if ($chk && $chk->num_rows > 0) {
                            $skippedCount++;
                            continue;
                        }
                        $sqlIns = "INSERT INTO serialsanpham (maSerial_IMEI, maSP, idChiTietNhap, trangThai, ghiChu) 
                                   VALUES ('$sEsc', '$maSPEsc', $idChiTietNhap, '$stEsc', '$gcEsc')";
                        if ($conn->query($sqlIns)) {
                            $addedCount++;
                        }
                    }
                    $_SESSION['flash_success'] = "Đã thêm thành công <strong>$addedCount</strong> Serial mới! (Bỏ qua $skippedCount mã đã tồn tại)";
                    header("Location: serial.php");
                    exit();
                }
            } elseif ($bulkType === 'auto') {
                $autoQty = min(200, max(1, (int)($_POST['auto_qty'] ?? 10)));
                $prefix = strtoupper(trim($_POST['auto_prefix'] ?? 'SN-' . $maSP));
                for ($k = 1; $k <= $autoQty; $k++) {
                    $genSerial = $prefix . '-' . date('Y') . '-' . strtoupper(substr(md5(uniqid($k, true)), 0, 6));
                    $sEsc = $conn->real_escape_string($genSerial);
                    $sqlIns = "INSERT INTO serialsanpham (maSerial_IMEI, maSP, idChiTietNhap, trangThai, ghiChu) 
                               VALUES ('$sEsc', '$maSPEsc', $idChiTietNhap, '$stEsc', '$gcEsc')";
                    if ($conn->query($sqlIns)) {
                        $addedCount++;
                    }
                }
                $_SESSION['flash_success'] = "Đã tự động tạo và thêm thành công <strong>$addedCount</strong> Serial mới cho sản phẩm <strong>$maSP</strong>!";
                header("Location: serial.php");
                exit();
            }
        }
    }
}

// 2. LẤY DANH SÁCH SẢN PHẨM & CÁC LÔ NHẬP ĐỂ HIỂN THỊ DROPDOWN
$allSanpham = [];
$resSP = $conn->query("SELECT maSP, tenSP, soLuongTon FROM sanpham WHERE isDeleted = 0 ORDER BY tenSP ASC");
if ($resSP) {
    while ($r = $resSP->fetch_assoc()) {
        $allSanpham[] = $r;
    }
}

$allChiTietNhap = [];
$resCN = $conn->query("SELECT cn.id, cn.maPhieu, cn.maSP, sp.tenSP, cn.soLuongNhap 
                       FROM chitietnhap cn
                       LEFT JOIN sanpham sp ON cn.maSP = sp.maSP
                       ORDER BY cn.id DESC");
if ($resCN) {
    while ($r = $resCN->fetch_assoc()) {
        $allChiTietNhap[] = $r;
    }
}
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm Serial / IMEI Máy Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Đăng ký mã định danh vật lý, liên kết sản phẩm laptop và trạng thái lưu kho.</p>
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

        <!-- Tabs chọn phương thức thêm (Thêm 1 mã hoặc Thêm hàng loạt) -->
        <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="tab-single-btn" data-bs-toggle="pill" data-bs-target="#tab-single" type="button" role="tab">
                    <i class="bi bi-upc me-1"></i> Thêm Từng Mã Serial Đơn Lẻ
                </button>
            </li>
            <li class="nav-item ms-2" role="presentation">
                <button class="nav-link fw-semibold" id="tab-bulk-btn" data-bs-toggle="pill" data-bs-target="#tab-bulk" type="button" role="tab">
                    <i class="bi bi-stack me-1"></i> Thêm Hàng Loạt (Bulk Import)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- TAB 1: THÊM 1 SERIAL ĐƠN LẺ -->
            <div class="tab-pane fade show active" id="tab-single" role="tabpanel">
                <form action="create.php" method="POST">
                    <input type="hidden" name="mode" value="single">

                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="bi bi-upc-scan text-primary me-2"></i> Thông Tin Thiết Bị & Mã Serial
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mã Serial / IMEI <span class="text-danger">*</span></label>
                                    <input type="text" name="maSerial_IMEI" class="form-control font-monospace text-uppercase fw-bold" 
                                           placeholder="VD: SN-ASUS-TUF-2026-9901" required value="<?= htmlspecialchars($_POST['maSerial_IMEI'] ?? '') ?>">
                                    <small class="text-muted">Mã định danh vật lý duy nhất in trên thân máy hoặc vỏ hộp</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Thuộc sản phẩm laptop <span class="text-danger">*</span></label>
                                    <select name="maSP" class="form-select" required id="selectSingleSP" onchange="autoSyncChiTietNhap(this.value)">
                                        <option value="">-- Chọn sản phẩm laptop --</option>
                                        <?php foreach ($allSanpham as $sp): ?>
                                            <option value="<?= htmlspecialchars($sp['maSP']) ?>" <?= (($_POST['maSP'] ?? '') === $sp['maSP']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sp['maSP']) ?> - <?= htmlspecialchars($sp['tenSP']) ?> (Kho: <?= $sp['soLuongTon'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lô / Chi tiết nhập kho liên kết <span class="text-danger">*</span></label>
                                    <select name="idChiTietNhap" class="form-select" required id="selectSingleCTN">
                                        <?php foreach ($allChiTietNhap as $cn): ?>
                                            <option value="<?= $cn['id'] ?>" data-masp="<?= htmlspecialchars($cn['maSP']) ?>">
                                                Lô ID #<?= $cn['id'] ?> - <?= htmlspecialchars($cn['maPhieu']) ?> (<?= htmlspecialchars($cn['maSP']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Liên kết với phiếu nhập kho tương ứng</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Trạng thái máy ban đầu <span class="text-danger">*</span></label>
                                    <select name="trangThai" class="form-select" required>
                                        <option value="Trong kho" selected>Trong kho (Sẵn sàng bán)</option>
                                        <option value="Đã bán">Đã bán</option>
                                        <option value="Đang bảo hành">Đang bảo hành</option>
                                        <option value="Lỗi đổi trả">Lỗi đổi trả</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú thiết bị (ghiChu)</label>
                                    <textarea name="ghiChu" rows="2" class="form-control" placeholder="Tình trạng ngoại quan, tem phân phối chính hãng..."><?= htmlspecialchars($_POST['ghiChu'] ?? 'Hàng mới 100% nguyên seal tại kho') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-5">
                        <a href="serial.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-tz-primary px-4 py-2 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Lưu số Serial / IMEI
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: THÊM HÀNG LOẠT (BULK IMPORT) -->
            <div class="tab-pane fade" id="tab-bulk" role="tabpanel">
                <form action="create.php" method="POST">
                    <input type="hidden" name="mode" value="bulk">

                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="bi bi-layers text-primary me-2"></i> Thêm Hàng Loạt Nhiều Serial Cho Một Dòng Laptop
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Thuộc sản phẩm laptop <span class="text-danger">*</span></label>
                                    <select name="maSP" class="form-select" required id="selectBulkSP" onchange="autoSyncBulkCTN(this.value)">
                                        <option value="">-- Chọn sản phẩm laptop --</option>
                                        <?php foreach ($allSanpham as $sp): ?>
                                            <option value="<?= htmlspecialchars($sp['maSP']) ?>">
                                                <?= htmlspecialchars($sp['maSP']) ?> - <?= htmlspecialchars($sp['tenSP']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lô nhập kho liên kết <span class="text-danger">*</span></label>
                                    <select name="idChiTietNhap" class="form-select" required id="selectBulkCTN">
                                        <?php foreach ($allChiTietNhap as $cn): ?>
                                            <option value="<?= $cn['id'] ?>" data-masp="<?= htmlspecialchars($cn['maSP']) ?>">
                                                Lô ID #<?= $cn['id'] ?> - <?= htmlspecialchars($cn['maPhieu']) ?> (<?= htmlspecialchars($cn['maSP']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phương thức tạo hàng loạt</label>
                                    <div class="d-flex align-items-center gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bulk_type" id="bulkTypeList" value="list" checked onclick="toggleBulkMode('list')">
                                            <label class="form-check-label fw-medium" for="bulkTypeList">Dán danh sách mã Serial</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bulk_type" id="bulkTypeAuto" value="auto" onclick="toggleBulkMode('auto')">
                                            <label class="form-check-label fw-medium" for="bulkTypeAuto">Tự động sinh ngẫu nhiên</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Trạng thái máy ban đầu</label>
                                    <select name="trangThai" class="form-select" required>
                                        <option value="Trong kho" selected>Trong kho (Sẵn sàng bán)</option>
                                        <option value="Đã bán">Đã bán</option>
                                    </select>
                                </div>

                                <!-- Box 1: Dán danh sách -->
                                <div class="col-12" id="boxBulkList">
                                    <label class="form-label fw-semibold">Danh sách mã Serial (Mỗi mã trên 1 dòng)</label>
                                    <textarea name="serial_list" rows="5" class="form-control font-monospace text-uppercase" placeholder="VD:&#10;SN-ASUS-001&#10;SN-ASUS-002&#10;SN-ASUS-003"></textarea>
                                    <small class="text-muted">Các mã đã tồn tại trong CSDL sẽ được hệ thống tự động bỏ qua</small>
                                </div>

                                <!-- Box 2: Tự động sinh -->
                                <div class="col-12" id="boxBulkAuto" style="display: none;">
                                    <div class="row g-3 p-3 bg-light rounded-3 border">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tiền tố mã Serial (Prefix)</label>
                                            <input type="text" name="auto_prefix" class="form-control font-monospace text-uppercase" placeholder="VD: SN-DELL-XPS">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Số lượng máy cần sinh (Tối đa 200)</label>
                                            <input type="number" name="auto_qty" class="form-control font-monospace" value="10" min="1" max="200">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú chung</label>
                                    <textarea name="ghiChu" rows="2" class="form-control" placeholder="Ghi chú áp dụng cho toàn bộ lô máy vừa thêm...">Hàng mới 100% nguyên seal tại kho TechZone</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-5">
                        <a href="serial.php" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                            <i class="bi bi-stack me-1"></i> Tiến Hành Thêm Hàng Loạt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function autoSyncChiTietNhap(maSP) {
    if (!maSP) return;
    const select = document.getElementById('selectSingleCTN');
    for (let opt of select.options) {
        if (opt.getAttribute('data-masp') === maSP) {
            opt.selected = true;
            break;
        }
    }
}

function autoSyncBulkCTN(maSP) {
    if (!maSP) return;
    const select = document.getElementById('selectBulkCTN');
    for (let opt of select.options) {
        if (opt.getAttribute('data-masp') === maSP) {
            opt.selected = true;
            break;
        }
    }
}

function toggleBulkMode(mode) {
    document.getElementById('boxBulkList').style.display = (mode === 'list') ? 'block' : 'none';
    document.getElementById('boxBulkAuto').style.display = (mode === 'auto') ? 'block' : 'none';
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
