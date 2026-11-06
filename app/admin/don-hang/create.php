<?php
// app/admin/don-hang/create.php
$pageTitle = "Tạo đơn bán hàng mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

// Gợi ý mã đơn hàng tiếp theo (VD: DH007)
$resMax = $conn->query("SELECT maDH FROM donhang ORDER BY maDH DESC LIMIT 1");
$autoMaDH = 'DH001';
if ($resMax && $resMax->num_rows > 0) {
    $rowMax = $resMax->fetch_assoc();
    $lastMa = $rowMax['maDH'];
    if (preg_match('/^DH(\d+)$/', $lastMa, $m)) {
        $num = intval($m[1]) + 1;
        $autoMaDH = 'DH' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $autoMaDH = 'DH' . date('ymd') . rand(10, 99);
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maDH = trim($_POST['maDH'] ?? '');
    $maKH = trim($_POST['maKH'] ?? '');
    $maKM = trim($_POST['maKM'] ?? '');
    $nguoiPhuTrach = trim($_POST['nguoiPhuTrach'] ?? '');
    $kenhBan = trim($_POST['kenhBan'] ?? 'TAI_QUAY');
    $phuongThucThanhToan = trim($_POST['phuongThucThanhToan'] ?? 'TIEN_MAT');
    $phuongThucGiaoHang = trim($_POST['phuongThucGiaoHang'] ?? '');
    $phiVanChuyen = floatval($_POST['phiVanChuyen'] ?? 0);
    $trangThaiDon = trim($_POST['trangThaiDon'] ?? 'HOAN_THANH');

    // Mặt hàng ban đầu
    $maSP = trim($_POST['maSP'] ?? '');
    $soLuong = intval($_POST['soLuong'] ?? 1);
    $donGia = floatval($_POST['donGia'] ?? 0);

    if (empty($maDH)) $errors[] = "Mã đơn hàng không được để trống";
    if (empty($maKH)) $errors[] = "Vui lòng chọn khách hàng";
    if (empty($maSP)) $errors[] = "Vui lòng chọn sản phẩm laptop";

    // Kiểm tra trùng mã đơn
    $chk = $conn->query("SELECT maDH FROM donhang WHERE maDH = '$maDH'");
    if ($chk && $chk->num_rows > 0) $errors[] = "Mã đơn hàng đã tồn tại";

    if (empty($errors)) {
        $tongTienHang = $soLuong * $donGia;
        $tienGiamGia = 0;

        // Tính tiền giảm giá nếu có voucher
        if (!empty($maKM)) {
            $kmRow = $conn->query("SELECT * FROM khuyenmai WHERE maKM = '$maKM'")->fetch_assoc();
            if ($kmRow) {
                if ($kmRow['loaiKM'] === 'PHAN_TRAM') {
                    $tienGiamGia = ($tongTienHang * $kmRow['giaTriGiam']) / 100;
                    if (!empty($kmRow['mucGiamToiDa']) && $tienGiamGia > $kmRow['mucGiamToiDa']) {
                        $tienGiamGia = $kmRow['mucGiamToiDa'];
                    }
                } else {
                    $tienGiamGia = $kmRow['giaTriGiam'];
                }
            }
            $maKMVal = "'$maKM'";
        } else {
            $maKMVal = "NULL";
        }

        $nguoiVal = !empty($nguoiPhuTrach) ? "'$nguoiPhuTrach'" : "NULL";
        $tongThanhToan = max(0, $tongTienHang - $tienGiamGia + $phiVanChuyen);

        $sql_insert = "INSERT INTO donhang (maDH, maKH, maKM, nguoiPhuTrach, ngayTao, kenhBan, phuongThucThanhToan, phuongThucGiaoHang, phiVanChuyen, tongTienHang, tienGiamGia, tongThanhToan, trangThaiDon) 
                       VALUES ('$maDH', '$maKH', $maKMVal, $nguoiVal, NOW(), '$kenhBan', '$phuongThucThanhToan', '$phuongThucGiaoHang', $phiVanChuyen, $tongTienHang, $tienGiamGia, $tongThanhToan, '$trangThaiDon')";

        if ($conn->query($sql_insert)) {
            // Chèn chi tiết đơn hàng
            $thanhTienItem = $soLuong * $donGia;
            $conn->query("INSERT INTO chitietdonhang (maDH, maSP, soLuong, donGia, thanhTien) 
                          VALUES ('$maDH', '$maSP', $soLuong, $donGia, $thanhTienItem)");

            // Trừ kho nếu hoàn thành
            if ($trangThaiDon === 'HOAN_THANH') {
                $conn->query("UPDATE sanpham SET soLuongTon = GREATEST(0, soLuongTon - $soLuong) WHERE maSP = '$maSP'");
            }

            echo "<script>window.location.href='don-hang.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi tạo đơn hàng: " . $conn->error;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tạo mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tạo Đơn Bán Hàng Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Thiết lập thông tin khách mua, mặt hàng laptop, chi phí vận chuyển và phương thức giao nhận.</p>
            </div>
            <a href="don-hang.php" class="btn-back-pill">
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
                    <i class="bi bi-receipt-cutoff me-2 text-primary"></i> Thông tin cơ bản đơn hàng
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã đơn hàng <span class="text-danger">*</span></label>
                            <input type="text" name="maDH" class="form-control font-monospace text-uppercase fw-bold" 
                                   value="<?= htmlspecialchars($_POST['maDH'] ?? $autoMaDH) ?>" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Khách hàng đặt mua <span class="text-danger">*</span></label>
                            <select name="maKH" class="form-select" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php
                                $sqlKH = "SELECT maKH, hoVaTen, soDienThoai, doiTuongUuTien FROM khachhang ORDER BY hoVaTen ASC";
                                $resKH = $conn->query($sqlKH);
                                if ($resKH && $resKH->num_rows > 0) {
                                    while ($k = $resKH->fetch_assoc()) {
                                        $vipText = ($k['doiTuongUuTien'] == 1) ? " [VIP]" : "";
                                        $sel = (isset($_POST['maKH']) && $_POST['maKH'] === $k['maKH']) ? 'selected' : '';
                                        echo "<option value='{$k['maKH']}' $sel>" . htmlspecialchars($k['hoVaTen']) . "$vipText - " . htmlspecialchars($k['soDienThoai']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Mã voucher khuyến mãi</label>
                            <select name="maKM" class="form-select">
                                <option value="">-- Không áp dụng voucher --</option>
                                <?php
                                $sqlKM = "SELECT maKM, tenKM, loaiKM, giaTriGiam FROM khuyenmai WHERE trangThai = 1 ORDER BY tenKM ASC";
                                $resKM = $conn->query($sqlKM);
                                if ($resKM && $resKM->num_rows > 0) {
                                    while ($m = $resKM->fetch_assoc()) {
                                        $valStr = ($m['loaiKM'] === 'PHAN_TRAM') ? "-{$m['giaTriGiam']}%" : "-" . number_format($m['giaTriGiam'], 0, ',', '.') . "₫";
                                        $sel = (isset($_POST['maKM']) && $_POST['maKM'] === $m['maKM']) ? 'selected' : '';
                                        echo "<option value='{$m['maKM']}' $sel>" . htmlspecialchars($m['tenKM']) . " ($valStr)</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kênh bán hàng</label>
                            <select name="kenhBan" class="form-select">
                                <option value="TAI_QUAY" selected>Tại quầy Showroom</option>
                                <option value="ONLINE">Đặt mua Online qua Web/SĐT</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="phuongThucThanhToan" class="form-select">
                                <option value="TIEN_MAT" selected>Tiền mặt</option>
                                <option value="CHUYEN_KHOAN">Chuyển khoản ngân hàng</option>
                                <option value="VNPay">Cổng thanh toán VNPay</option>
                                <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nhân viên phụ trách</label>
                            <select name="nguoiPhuTrach" class="form-select">
                                <option value="">-- Chọn nhân viên --</option>
                                <?php
                                $sqlNV = "SELECT maTk, hoTen, role FROM accounts WHERE role IN ('admin', 'nv_ban_hang', 'thu_ngan') ORDER BY hoTen ASC";
                                $resNV = $conn->query($sqlNV);
                                if ($resNV && $resNV->num_rows > 0) {
                                    while ($nv = $resNV->fetch_assoc()) {
                                        echo "<option value='{$nv['maTk']}'>" . htmlspecialchars($nv['hoTen']) . " [{$nv['role']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phí vận chuyển giao hàng (₫)</label>
                            <input type="number" step="1000" name="phiVanChuyen" class="form-control font-monospace" value="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Đơn vị vận chuyển (nếu giao hàng)</label>
                            <input type="text" name="phuongThucGiaoHang" class="form-control" placeholder="VD: Giao hàng nhanh, Viettel Post...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Trạng thái đơn hàng</label>
                            <select name="trangThaiDon" class="form-select">
                                <option value="HOAN_THANH" selected>Hoàn thành (Đã thanh toán)</option>
                                <option value="CHO_DUYET">Chờ duyệt xử lý</option>
                                <option value="DANG_GIAO">Đang giao hàng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mặt hàng bán -->
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-laptop me-2 text-primary"></i> Mặt hàng laptop xuất bán
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Chọn sản phẩm laptop <span class="text-danger">*</span></label>
                            <select name="maSP" id="selectLaptop" class="form-select" required>
                                <option value="">-- Chọn máy trong kho --</option>
                                <?php
                                $sqlLaptops = "SELECT maSP, tenSP, giaBan, soLuongTon FROM sanpham WHERE isDeleted = 0 ORDER BY tenSP ASC";
                                $resL = $conn->query($sqlLaptops);
                                if ($resL && $resL->num_rows > 0) {
                                    while ($lp = $resL->fetch_assoc()) {
                                        echo "<option value='{$lp['maSP']}' data-gia='{$lp['giaBan']}'>" . htmlspecialchars($lp['tenSP']) . " [Tồn: {$lp['soLuongTon']}] - " . number_format($lp['giaBan'], 0, ',', '.') . " ₫</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Số lượng mua</label>
                            <input type="number" step="1" min="1" name="soLuong" id="inputSL" value="1" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Đơn giá bán thực tế (₫)</label>
                            <input type="number" step="1000" name="donGia" id="inputDonGia" class="form-control font-monospace" placeholder="VD: 21990000" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="don-hang.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Tạo đơn bán hàng
                </button>
            </div>
        </form>
    </main>
</div>

<script>
    document.getElementById('selectLaptop').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const gia = opt.getAttribute('data-gia') || 0;
        document.getElementById('inputDonGia').value = gia;
    });
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
