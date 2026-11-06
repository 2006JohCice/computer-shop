<?php
// app/admin/phieu-nhap/detail.php
$pageTitle = "Chi tiết phiếu nhập kho";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPhieu = htmlspecialchars($_GET['id'] ?? $_GET['maPhieu'] ?? 'PN2026_001');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$alertMsg = '';

// Xử lý các Form Actions gửi lên từ chi tiết
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_invoice_ncc') {
        $soHD = trim($_POST['soHoaDonNCC'] ?? '');
        $conn->query("UPDATE phieunhap SET soHoaDonNCC = '$soHD' WHERE maPhieu = '$maPhieu'");
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Đã cập nhật số hóa đơn NCC <strong>" . htmlspecialchars($soHD) . "</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'confirm_import') {
        // Kiểm tra xem đã nhập kho chưa
        $checkStatus = $conn->query("SELECT trangThaiNhapKho FROM phieunhap WHERE maPhieu = '$maPhieu'")->fetch_assoc();
        if ($checkStatus && $checkStatus['trangThaiNhapKho'] !== 'DA_NHAP_KHO') {
            // Cộng tồn kho cho từng sản phẩm trong chi tiết phiếu
            $sqlItems = "SELECT maSP, soLuongNhap FROM chitietnhap WHERE maPhieu = '$maPhieu'";
            $resItems = $conn->query($sqlItems);
            if ($resItems && $resItems->num_rows > 0) {
                while ($it = $resItems->fetch_assoc()) {
                    $spID = $it['maSP'];
                    $spQty = $it['soLuongNhap'];
                    $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $spQty WHERE maSP = '$spID'");
                }
            }

            $conn->query("UPDATE phieunhap SET trangThaiNhapKho = 'DA_NHAP_KHO' WHERE maPhieu = '$maPhieu'");
            $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='bi bi-check-all me-2'></i>Phiếu <strong>$maPhieu</strong> đã hoàn tất chốt nhập kho và cộng dồn số lượng tồn vào hệ thống!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                         </div>";
        }
    } elseif ($action === 'edit_draft') {
        $maNCC = trim($_POST['maNCC'] ?? '');
        $tongTienTraTruocNCC = floatval($_POST['tongTienTraTruocNCC'] ?? 0);
        $trangThaiThanhToan = trim($_POST['trangThaiThanhToan'] ?? 'CHUA_THANH_TOAN');
        $soHoaDonNCC = trim($_POST['soHoaDonNCC'] ?? '');
        $ghiChu = trim($_POST['ghiChu'] ?? '');

        $conn->query("UPDATE phieunhap SET 
                        maNCC = '$maNCC',
                        tongTienTraTruocNCC = '$tongTienTraTruocNCC',
                        trangThaiThanhToan = '$trangThaiThanhToan',
                        soHoaDonNCC = '$soHoaDonNCC',
                        ghiChu = '$ghiChu'
                      WHERE maPhieu = '$maPhieu'");

        $alertMsg = "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                        <i class='bi bi-pencil-square me-2'></i>Cập nhật thông tin phiếu nhập <strong>$maPhieu</strong> thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'add_item') {
        $maSP = trim($_POST['maSP'] ?? '');
        $soLuongNhap = intval($_POST['soLuongNhap'] ?? 1);
        $giaVonNhap = floatval($_POST['giaVonNhap'] ?? 0);
        $ghiChuItem = trim($_POST['ghiChuChiTiet'] ?? '');

        if (!empty($maSP) && $soLuongNhap > 0) {
            $thanhTienNhap = $soLuongNhap * $giaVonNhap;
            $sqlAdd = "INSERT INTO chitietnhap (maPhieu, maSP, soLuongNhap, giaVonNhap, thanhTienNhap, ghiChu) 
                       VALUES ('$maPhieu', '$maSP', '$soLuongNhap', '$giaVonNhap', '$thanhTienNhap', '$ghiChuItem')
                       ON DUPLICATE KEY UPDATE 
                       soLuongNhap = soLuongNhap + $soLuongNhap,
                       giaVonNhap = '$giaVonNhap',
                       thanhTienNhap = (soLuongNhap + $soLuongNhap) * '$giaVonNhap'";
            $conn->query($sqlAdd);

            // Cập nhật lại tổng tiền phiếu nhập
            $resSum = $conn->query("SELECT SUM(thanhTienNhap) AS sumTotal FROM chitietnhap WHERE maPhieu = '$maPhieu'")->fetch_assoc();
            $newTotal = $resSum['sumTotal'] ?? 0;
            $conn->query("UPDATE phieunhap SET tongTienNhap = '$newTotal' WHERE maPhieu = '$maPhieu'");

            // Nếu phiếu đã nhập kho, cộng thêm tồn
            $chk = $conn->query("SELECT trangThaiNhapKho FROM phieunhap WHERE maPhieu = '$maPhieu'")->fetch_assoc();
            if ($chk && $chk['trangThaiNhapKho'] === 'DA_NHAP_KHO') {
                $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $soLuongNhap WHERE maSP = '$maSP'");
            }

            $alertMsg = "<div class='alert alert-primary alert-dismissible fade show' role='alert'>
                            <i class='bi bi-plus-circle me-2'></i>Đã thêm $soLuongNhap máy sản phẩm <strong>$maSP</strong> vào phiếu!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                         </div>";
        }
    } elseif ($action === 'delete_item') {
        $idItem = intval($_POST['idItem'] ?? 0);
        if ($idItem > 0) {
            // Xóa serial liên kết trước
            $conn->query("DELETE FROM serialsanpham WHERE idChiTietNhap = $idItem");
            $conn->query("DELETE FROM chitietnhap WHERE id = $idItem AND maPhieu = '$maPhieu'");

            // Cập nhật lại tổng tiền phiếu nhập
            $resSum = $conn->query("SELECT SUM(thanhTienNhap) AS sumTotal FROM chitietnhap WHERE maPhieu = '$maPhieu'")->fetch_assoc();
            $newTotal = $resSum['sumTotal'] ?? 0;
            $conn->query("UPDATE phieunhap SET tongTienNhap = '$newTotal' WHERE maPhieu = '$maPhieu'");

            $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                            <i class='bi bi-trash3 me-2'></i>Đã xóa dòng mặt hàng khỏi phiếu nhập!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                         </div>";
        }
    } elseif ($action === 'save_serial') {
        $idChiTietNhap = intval($_POST['idChiTietNhap'] ?? 0);
        $maSP = trim($_POST['maSP'] ?? '');
        $serialVal = trim($_POST['maSerial_IMEI'] ?? '');
        $ghiChuSerial = trim($_POST['ghiChuSerial'] ?? 'Hàng nhập kho');

        if (!empty($serialVal) && $idChiTietNhap > 0) {
            // Kiểm tra trùng mã serial trong toàn hệ thống
            $chkSer = $conn->query("SELECT maSerial_IMEI FROM serialsanpham WHERE maSerial_IMEI = '$serialVal'");
            if ($chkSer && $chkSer->num_rows > 0) {
                $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                <i class='bi bi-exclamation-triangle me-2'></i>Mã Serial <strong>$serialVal</strong> đã tồn tại trong hệ thống! Vui lòng kiểm tra lại tem nhãn.
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                             </div>";
            } else {
                $sqlAddSer = "INSERT INTO serialsanpham (maSerial_IMEI, maSP, idChiTietNhap, trangThai, ghiChu) 
                              VALUES ('$serialVal', '$maSP', $idChiTietNhap, 'Trong kho', '$ghiChuSerial')";
                if ($conn->query($sqlAddSer)) {
                    $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                    <i class='bi bi-check2-circle me-2'></i>Đã lưu mã Serial <strong>$serialVal</strong> vào kho thành công!
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 </div>";
                } else {
                    $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <i class='bi bi-exclamation-triangle me-2'></i>Lỗi khi lưu Serial: " . $conn->error . "
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 </div>";
                }
            }
        }
    }
}

// Truy vấn thông tin phiếu nhập từ database
$sqlPhieu = "SELECT * FROM phieunhap WHERE maPhieu = '$maPhieu'";
$resPhieu = $conn->query($sqlPhieu);

if (!$resPhieu || $resPhieu->num_rows === 0) {
    echo "<script>alert('Không tìm thấy phiếu nhập $maPhieu!'); window.location.href='phieu-nhap.php';</script>";
    exit;
}

$phieu = $resPhieu->fetch_assoc();
$isDraft = ($phieu['trangThaiNhapKho'] !== 'DA_NHAP_KHO');

// Lấy tên nhà cung cấp từ bảng nhacungcap
$phieu['tenCongTy'] = $phieu['maNCC'];
if (!empty($phieu['maNCC'])) {
    $resNCC = $conn->query("SELECT tenCongTy FROM nhacungcap WHERE maNCC = '" . $phieu['maNCC'] . "'");
    if ($resNCC && $resNCC->num_rows > 0) {
        $phieu['tenCongTy'] = $resNCC->fetch_assoc()['tenCongTy'];
    }
}

// Lấy tên người nhập từ bảng accounts
$phieu['tenNguoiNhap'] = $phieu['nguoiNhap'];
if (!empty($phieu['nguoiNhap'])) {
    $resAcc = $conn->query("SELECT hoTen FROM accounts WHERE maTk = '" . $phieu['nguoiNhap'] . "'");
    if ($resAcc && $resAcc->num_rows > 0) {
        $phieu['tenNguoiNhap'] = $resAcc->fetch_assoc()['hoTen'];
    }
}

// Lấy danh sách mặt hàng thuộc phiếu nhập từ bảng chitietnhap
$sqlDetails = "SELECT * FROM chitietnhap WHERE maPhieu = '$maPhieu' ORDER BY id ASC";
$resDetails = $conn->query($sqlDetails);
$detailsList = [];
if ($resDetails && $resDetails->num_rows > 0) {
    while ($rowD = $resDetails->fetch_assoc()) {
        $detailsList[] = $rowD;
    }
}
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="phieu-nhap.php" class="text-decoration-none text-muted">Phiếu nhập</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= htmlspecialchars($maPhieu) ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Phiếu Nhập: <?= htmlspecialchars($maPhieu) ?></h2>
                    <?php if ($isDraft): ?>
                        <span class="tz-badge tz-badge-warning"><i class="bi bi-pencil-fill"></i>BẢN NHÁP (ĐANG MỞ CHỈNH SỬA)</span>
                    <?php else: ?>
                        <span class="tz-badge tz-badge-success"><i class="bi bi-lock-fill"></i>ĐÃ NHẬP KHO (ĐÃ CỘNG TỒN KHO)</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="edit.php?maPhieu=<?= urlencode($maPhieu) ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa phiếu
                </a>
                <a href="phieu-nhap.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
        </div>

        <?php if (!$isDraft): ?>
            <!-- Thông báo khi phiếu đã hoàn tất nhập kho -->
            <div class="alert alert-secondary border d-flex align-items-center justify-content-between p-3 mb-4">
                <div>
                    <i class="bi bi-shield-lock-fill text-success fs-4 me-2 align-middle"></i>
                    <span class="fw-semibold text-dark">Phiếu nhập này đã hoàn tất nhập kho.</span> Toàn bộ các dòng sản phẩm, số lượng và giá vốn đã được cố định. Có thể cập nhật bổ sung số Hóa Đơn NCC nếu cần.
                </div>
                <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="action" value="update_invoice_ncc">
                    <input type="text" name="soHoaDonNCC" class="form-control form-control-sm font-monospace" placeholder="Nhập số hóa đơn..." value="<?= htmlspecialchars($phieu['soHoaDonNCC'] ?? '') ?>" required style="width: 200px;">
                    <button type="submit" class="btn btn-sm btn-tz-primary text-nowrap"><i class="bi bi-check2 me-1"></i>Cập nhật HĐ</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cột Trái: Thông tin chung phiếu nhập -->
            <div class="col-lg-4">
                <div class="tz-card p-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-info-circle text-primary me-2"></i>Thông Tin Phiếu Nhập
                    </h4>

                    <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST">
                        <input type="hidden" name="action" value="edit_draft">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Mã phiếu (maPhieu)</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace fw-bold" value="<?= htmlspecialchars($phieu['maPhieu']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Thời gian nhập (ngayNhap)</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="<?= date('d/m/Y H:i', strtotime($phieu['ngayNhap'])) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Người lập phiếu (nguoiNhap)</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($phieu['tenNguoiNhap'] ?? $phieu['nguoiNhap']) ?> [<?= htmlspecialchars($phieu['nguoiNhap']) ?>]" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nhà cung cấp (maNCC)</label>
                            <select name="maNCC" class="form-select form-select-sm" <?= !$isDraft ? 'disabled' : '' ?>>
                                <?php
                                $sqlAllNCC = "SELECT maNCC, tenCongTy FROM nhacungcap WHERE trangThai = 1 ORDER BY tenCongTy ASC";
                                $resAllNCC = $conn->query($sqlAllNCC);
                                if ($resAllNCC && $resAllNCC->num_rows > 0) {
                                    while ($nccRow = $resAllNCC->fetch_assoc()) {
                                        $sel = ($phieu['maNCC'] === $nccRow['maNCC']) ? 'selected' : '';
                                        echo "<option value='{$nccRow['maNCC']}' $sel>" . htmlspecialchars($nccRow['tenCongTy']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Số hóa đơn NCC (soHoaDonNCC)</label>
                            <input type="text" name="soHoaDonNCC" class="form-control form-control-sm font-monospace" value="<?= htmlspecialchars($phieu['soHoaDonNCC'] ?? '') ?>" <?= !$isDraft ? 'readonly' : '' ?>>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Đã trả trước / cọc (₫)</label>
                            <input type="number" step="1000" name="tongTienTraTruocNCC" class="form-control form-control-sm" value="<?= htmlspecialchars($phieu['tongTienTraTruocNCC']) ?>" <?= !$isDraft ? 'readonly' : '' ?>>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Trạng thái thanh toán</label>
                            <select name="trangThaiThanhToan" class="form-select form-select-sm" <?= !$isDraft ? 'disabled' : '' ?>>
                                <option value="DA_THANH_TOAN" <?= $phieu['trangThaiThanhToan'] === 'DA_THANH_TOAN' ? 'selected' : '' ?>>Đã thanh toán toàn bộ</option>
                                <option value="THANH_TOAN_MOT_PHAN" <?= $phieu['trangThaiThanhToan'] === 'THANH_TOAN_MOT_PHAN' ? 'selected' : '' ?>>Thanh toán một phần (Đặt cọc)</option>
                                <option value="CHUA_THANH_TOAN" <?= $phieu['trangThaiThanhToan'] === 'CHUA_THANH_TOAN' ? 'selected' : '' ?>>Chưa thanh toán</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Ghi chú phiếu nhập</label>
                            <textarea name="ghiChu" class="form-control form-control-sm" rows="2" <?= !$isDraft ? 'readonly' : '' ?>><?= htmlspecialchars($phieu['ghiChu'] ?? '') ?></textarea>
                        </div>

                        <?php if ($isDraft): ?>
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <i class="bi bi-save me-1"></i>Lưu Thay Đổi Thông Tin Phiếu
                            </button>
                        <?php endif; ?>
                    </form>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="text-muted small">Tổng tiền nhập hàng:</span>
                            <span class="fw-bold text-danger fs-5"><?= number_format($phieu['tongTienNhap'], 0, ',', '.') ?> ₫</span>
                        </div>

                        <?php if ($isDraft): ?>
                            <!-- NÚT CHỐT: XÁC NHẬN NHẬP KHO -->
                            <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST">
                                <input type="hidden" name="action" value="confirm_import">
                                <button type="submit" class="btn btn-success w-100 py-2 fw-bold"
                                    data-tz-confirm="Bạn có chắc chắn muốn chốt nhập kho? Sau khi chốt, số lượng tồn kho sản phẩm sẽ được tự động cộng dồn và phiếu sẽ khóa chỉnh sửa dòng hàng."
                                    data-tz-title="Xác nhận chốt nhập kho">
                                    <i class="bi bi-check2-all me-1"></i>XÁC NHẬN NHẬP KHO
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Bảng Mặt hàng nhập & Nhập Serial/IMEI -->
            <div class="col-lg-8">
                <?php if ($isDraft): ?>
                    <!-- Form Thêm Mặt Hàng Vào Phiếu Nháp -->
                    <div class="tz-card p-3 mb-4 bg-light border">
                        <h5 class="fw-bold text-dark mb-2" style="font-size: 15px;">
                            <i class="bi bi-plus-circle text-primary me-2"></i>Thêm Mặt Hàng Vào Phiếu
                        </h5>
                        <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST" class="row g-2 align-items-end">
                            <input type="hidden" name="action" value="add_item">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Chọn Sản Phẩm <span class="text-danger">*</span></label>
                                <select name="maSP" class="form-select form-select-sm" required>
                                    <option value="">-- Chọn sản phẩm laptop --</option>
                                    <?php
                                    $sqlListSP = "SELECT maSP, tenSP, giaBan FROM sanpham WHERE isDeleted = 0 ORDER BY tenSP ASC";
                                    $resListSP = $conn->query($sqlListSP);
                                    if ($resListSP && $resListSP->num_rows > 0) {
                                        while ($spRow = $resListSP->fetch_assoc()) {
                                            echo "<option value='{$spRow['maSP']}'>" . htmlspecialchars($spRow['tenSP']) . " [{$spRow['maSP']}]</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="soLuongNhap" class="form-control form-control-sm" min="1" value="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Giá vốn nhập (₫) <span class="text-danger">*</span></label>
                                <input type="number" step="1000" name="giaVonNhap" class="form-control form-control-sm font-monospace" placeholder="VD: 18000000" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-tz-primary w-100">
                                    <i class="bi bi-plus-lg me-1"></i>Thêm
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Bảng Danh Sách Mặt Hàng Trong Phiếu -->
                <div class="tz-card">
                    <div class="tz-card-header d-flex justify-content-between align-items-center">
                        <h4 class="tz-card-title mb-0"><i class="bi bi-list-check text-primary"></i>Chi Tiết Mặt Hàng Nhập Kho</h4>
                        <span class="badge bg-light text-dark border">
                            <?= count($detailsList) ?> dòng sản phẩm
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="tz-table">
                            <thead>
                                <tr>
                                    <th>MÃ SP</th>
                                    <th>TÊN SẢN PHẨM</th>
                                    <th class="text-center">SỐ LƯỢNG</th>
                                    <th class="text-end">GIÁ VỐN NHẬP</th>
                                    <th class="text-end">THÀNH TIỀN</th>
                                    <th class="text-center">SERIAL / IMEI</th>
                                    <?php if ($isDraft): ?>
                                        <th class="text-end" style="width: 60px;">XÓA</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($detailsList)) {
                                    foreach ($detailsList as $item) {
                                        $idItem = $item['id'];
                                        $maSPItem = $item['maSP'];
                                        $slNhap = $item['soLuongNhap'];
                                        $giaVon = $item['giaVonNhap'];
                                        $thanhTien = $item['thanhTienNhap'];

                                        // Lấy tên sản phẩm và thông số từ bảng sanpham
                                        $tenSPItem = $maSPItem;
                                        $thongSoKyThuat = '';
                                        if (!empty($maSPItem)) {
                                            $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSPItem'");
                                            if ($resSP && $resSP->num_rows > 0) {
                                                $rowSP = $resSP->fetch_assoc();
                                                $tenSPItem = $rowSP['tenSP'];
                                                $thongSoKyThuat = $rowSP['thongSoKyThuat'];
                                            }
                                        }

                                        // Đếm số Serial đã nhập cho dòng này
                                        $sqlCntSer = "SELECT count(*) AS total FROM serialsanpham WHERE idChiTietNhap = $idItem";
                                        $cntSer = $conn->query($sqlCntSer)->fetch_assoc()['total'] ?? 0;
                                        $isFullSer = ($cntSer >= $slNhap);
                                ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($maSPItem) ?></span></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($tenSPItem) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($thongSoKyThuat) ?></small>
                                            </td>
                                            <td class="text-center"><span class="fw-bold text-primary"><?= $slNhap ?> chiếc</span></td>
                                            <td class="text-end"><?= number_format($giaVon, 0, ',', '.') ?> ₫</td>
                                            <td class="text-end fw-bold text-dark"><?= number_format($thanhTien, 0, ',', '.') ?> ₫</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm <?= $isFullSer ? 'btn-outline-success' : 'btn-outline-warning' ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalSerial_<?= $idItem ?>">
                                                    <i class="bi bi-upc-scan me-1"></i>Serial (<?= $cntSer ?> / <?= $slNhap ?>)
                                                </button>
                                            </td>
                                            <?php if ($isDraft): ?>
                                                <td class="text-end">
                                                    <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="delete_item">
                                                        <input type="hidden" name="idItem" value="<?= $idItem ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa dòng mặt hàng"
                                                            data-tz-confirm="Bạn có chắc muốn xóa mặt hàng này khỏi phiếu nhập?"
                                                            data-tz-title="Xác nhận xóa dòng">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Chưa có mặt hàng nào trong phiếu nhập này. Vui lòng thêm mặt hàng ở trên.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Quản Lý Serial cho từng dòng mặt hàng (Đặt ngoài thẻ table để giao diện chuẩn và không bị lỗi vỡ bảng) -->
        <?php
        if (!empty($detailsList)) {
            foreach ($detailsList as $item) {
                $idItem = $item['id'];
                $maSPItem = $item['maSP'];
                $slNhap = $item['soLuongNhap'];

                $tenSPItem = $maSPItem;
                if (!empty($maSPItem)) {
                    $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSPItem'");
                    if ($resSP && $resSP->num_rows > 0) {
                        $tenSPItem = $resSP->fetch_assoc()['tenSP'];
                    }
                }

                $sqlCntSer = "SELECT count(*) AS total FROM serialsanpham WHERE idChiTietNhap = $idItem";
                $cntSer = $conn->query($sqlCntSer)->fetch_assoc()['total'] ?? 0;
        ?>
        <div class="modal fade" id="modalSerial_<?= $idItem ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-upc-scan text-primary me-2"></i>Danh Sách Serial / IMEI: <?= htmlspecialchars($tenSPItem) ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Cần nhập: <strong><?= $slNhap ?></strong> máy | Đã nhập: <strong class="text-success"><?= $cntSer ?></strong> mã</span>
                            <span class="badge bg-primary-subtle text-primary border font-monospace"><?= htmlspecialchars($maSPItem) ?></span>
                        </div>

                        <!-- Danh sách serial đã có -->
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>MÃ SERIAL / IMEI</th>
                                        <th>TRẠNG THÁI</th>
                                        <th>GHI CHÚ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sqlSers = "SELECT * FROM serialsanpham WHERE idChiTietNhap = $idItem ORDER BY maSerial_IMEI ASC";
                                    $resSers = $conn->query($sqlSers);
                                    if ($resSers && $resSers->num_rows > 0) {
                                        $idx = 1;
                                        while ($s = $resSers->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>" . $idx++ . "</td>
                                                    <td><span class='font-monospace fw-bold text-primary'>" . htmlspecialchars($s['maSerial_IMEI']) . "</span></td>
                                                    <td><span class='badge bg-success-subtle text-success border border-success-subtle'>" . htmlspecialchars($s['trangThai']) . "</span></td>
                                                    <td><small class='text-muted'>" . htmlspecialchars($s['ghiChu'] ?? '') . "</small></td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center text-muted py-3'>Chưa có mã Serial nào được lưu</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Thêm Serial Mới nếu chưa đủ số lượng -->
                        <?php if ($cntSer < $slNhap): ?>
                            <div class="bg-light p-3 rounded border">
                                <h6 class="fw-bold text-dark mb-2">Thêm mã Serial/IMEI máy tiếp theo:</h6>
                                <form action="detail.php?id=<?= urlencode($maPhieu) ?>" method="POST" class="row g-2 align-items-end">
                                    <input type="hidden" name="action" value="save_serial">
                                    <input type="hidden" name="idChiTietNhap" value="<?= $idItem ?>">
                                    <input type="hidden" name="maSP" value="<?= htmlspecialchars($maSPItem) ?>">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Mã Serial/IMEI tem máy <span class="text-danger">*</span></label>
                                        <input type="text" name="maSerial_IMEI" class="form-control font-monospace" placeholder="VD: SN_ASUS_F15_0003..." required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Vị trí / Ghi chú</label>
                                        <input type="text" name="ghiChuSerial" class="form-control" placeholder="VD: Kệ A1, nguyên seal...">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-tz-primary w-100">
                                            <i class="bi bi-plus-lg me-1"></i>Lưu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success mb-0 py-2">
                                <i class="bi bi-check2-circle me-1"></i>Đã kiểm đếm đầy đủ <strong><?= $slNhap ?> / <?= $slNhap ?></strong> mã Serial cho dòng sản phẩm này!
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        }
        ?>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
