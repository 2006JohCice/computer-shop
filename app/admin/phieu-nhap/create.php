<?php
// app/admin/phieu-nhap/create.php
$pageTitle = "Lập phiếu nhập kho mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối database: " . $conn->connect_error);
}

// Tự động gợi ý mã phiếu nhập tiếp theo (VD: PN2026_003)
$resMax = $conn->query("SELECT maPhieu FROM phieunhap ORDER BY maPhieu DESC LIMIT 1");
$autoMaPhieu = 'PN' . date('Y') . '_001';
if ($resMax && $resMax->num_rows > 0) {
    $rowMax = $resMax->fetch_assoc();
    $lastMa = $rowMax['maPhieu'];
    if (preg_match('/^PN(\d+)_(\d+)$/', $lastMa, $matches)) {
        $year = $matches[1];
        $num = intval($matches[2]) + 1;
        $autoMaPhieu = 'PN' . $year . '_' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $autoMaPhieu = 'PN' . date('Ymd_His');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maPhieu = trim($_POST['maPhieu'] ?? '');
    $ngayNhap = trim($_POST['ngayNhap'] ?? date('Y-m-d H:i:s'));
    $maNCC = trim($_POST['maNCC'] ?? '');
    $nguoiNhap = trim($_POST['nguoiNhap'] ?? '');
    $soHoaDonNCC = trim($_POST['soHoaDonNCC'] ?? '');
    $tongTienTraTruocNCC = floatval($_POST['tongTienTraTruocNCC'] ?? 0);
    $trangThaiThanhToan = trim($_POST['trangThaiThanhToan'] ?? 'CHUA_THANH_TOAN');
    $trangThaiNhapKho = trim($_POST['trangThaiNhapKho'] ?? 'BAN_NHAP');
    $ghiChu = trim($_POST['ghiChu'] ?? '');

    // Chi tiết mặt hàng đính kèm (nếu có)
    $maSP = trim($_POST['maSP'] ?? '');
    $soLuongNhap = intval($_POST['soLuongNhap'] ?? 0);
    $giaVonNhap = floatval($_POST['giaVonNhap'] ?? 0);
    $ghiChuChiTiet = trim($_POST['ghiChuChiTiet'] ?? '');

    // 1. Kiểm tra tính hợp lệ của dữ liệu
    if (empty($maPhieu)) {
        $errors[] = 'Mã phiếu nhập không được để trống';
    }

    if (empty($maNCC)) {
        $errors[] = 'Vui lòng chọn nhà cung cấp';
    }

    if (empty($nguoiNhap)) {
        $errors[] = 'Vui lòng chọn nhân viên lập phiếu';
    }

    if (empty($ngayNhap)) {
        $errors[] = 'Ngày giờ nhập kho không được để trống';
    }

    // 2. Kiểm tra trùng mã phiếu nhập (Primary Key)
    $sql_check = "SELECT maPhieu FROM phieunhap WHERE maPhieu = '$maPhieu'";
    $result_check = $conn->query($sql_check);
    if ($result_check && $result_check->num_rows > 0) {
        $errors[] = 'Mã phiếu nhập "' . htmlspecialchars($maPhieu) . '" đã tồn tại trong hệ thống';
    }

    // 3. Nếu không có lỗi thì tiến hành lưu vào DB
    if (empty($errors)) {
        $tongTienNhap = 0;
        if (!empty($maSP) && $soLuongNhap > 0) {
            $tongTienNhap = $soLuongNhap * $giaVonNhap;
        }

        $sql_insert = "INSERT INTO phieunhap (maPhieu, ngayNhap, maNCC, nguoiNhap, soHoaDonNCC, tongTienTraTruocNCC, tongTienNhap, trangThaiThanhToan, trangThaiNhapKho, ghiChu) 
                       VALUES ('$maPhieu', '$ngayNhap', '$maNCC', '$nguoiNhap', '$soHoaDonNCC', '$tongTienTraTruocNCC', '$tongTienNhap', '$trangThaiThanhToan', '$trangThaiNhapKho', '$ghiChu')";

        if ($conn->query($sql_insert)) {
            // Lưu dòng mặt hàng vào bảng chitietnhap nếu người dùng đã chọn sản phẩm
            if (!empty($maSP) && $soLuongNhap > 0) {
                $thanhTienNhap = $soLuongNhap * $giaVonNhap;
                $sql_detail = "INSERT INTO chitietnhap (maPhieu, maSP, soLuongNhap, giaVonNhap, thanhTienNhap, ghiChu) 
                               VALUES ('$maPhieu', '$maSP', '$soLuongNhap', '$giaVonNhap', '$thanhTienNhap', '$ghiChuChiTiet')";
                $conn->query($sql_detail);

                // Nếu chọn trạng thái là "Đã nhập kho" ngay từ đầu thì cộng trực tiếp số lượng tồn
                if ($trangThaiNhapKho === 'DA_NHAP_KHO') {
                    $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $soLuongNhap WHERE maSP = '$maSP'");
                }
            }

            echo "<script>window.location.href='phieu-nhap.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi tạo phiếu nhập: " . $conn->error;
        }
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
                        <li class="breadcrumb-item"><a href="phieu-nhap.php" class="text-decoration-none text-muted">Phiếu nhập kho</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Lập phiếu mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Lập Phiếu Nhập Kho Mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Khởi tạo chứng từ nhập hàng, đối chiếu thông tin nhà cung cấp và quản lý giá vốn sản phẩm.</p>
            </div>
            <a href="phieu-nhap.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger border-danger-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">Vui lòng kiểm tra lại các thông tin sau:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <form action="create.php" method="POST">
            <!-- Box 1: Thông tin chung phiếu nhập -->
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-file-earmark-arrow-down-fill me-2 text-primary"></i> Thông tin phiếu nhập kho
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu nhập <span class="text-danger">*</span></label>
                            <input type="text" name="maPhieu" class="form-control font-monospace text-uppercase fw-bold"
                                value="<?= htmlspecialchars($_POST['maPhieu'] ?? $autoMaPhieu) ?>" required>
                            <small class="text-muted">Khóa chính (PK), định dạng gợi ý: PN[NĂM]_[STT]</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Nhà cung cấp đối tác <span class="text-danger">*</span></label>
                            <select name="maNCC" class="form-select" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                <?php
                                $sqlNCC = "SELECT maNCC, tenCongTy FROM nhacungcap WHERE trangThai = 1 ORDER BY tenCongTy ASC";
                                $resNCC = $conn->query($sqlNCC);
                                if ($resNCC && $resNCC->num_rows > 0) {
                                    while ($ncc = $resNCC->fetch_assoc()) {
                                        $selected = (isset($_POST['maNCC']) && $_POST['maNCC'] === $ncc['maNCC']) ? 'selected' : '';
                                        echo "<option value='{$ncc['maNCC']}' $selected>" . htmlspecialchars($ncc['tenCongTy']) . " ({$ncc['maNCC']})</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nhân viên lập phiếu <span class="text-danger">*</span></label>
                            <select name="nguoiNhap" class="form-select" required>
                                <option value="">-- Chọn nhân viên --</option>
                                <?php
                                $sqlAcc = "SELECT maTk, hoTen, role FROM accounts ORDER BY hoTen ASC";
                                $resAcc = $conn->query($sqlAcc);
                                if ($resAcc && $resAcc->num_rows > 0) {
                                    while ($acc = $resAcc->fetch_assoc()) {
                                        $selected = (isset($_POST['nguoiNhap']) && $_POST['nguoiNhap'] === $acc['maTk']) ? 'selected' : (($acc['maTk'] === 'TK002') ? 'selected' : '');
                                        echo "<option value='{$acc['maTk']}' $selected>" . htmlspecialchars($acc['hoTen']) . " [{$acc['maTk']} - {$acc['role']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Thời gian nhập kho <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayNhap" class="form-control"
                                value="<?= htmlspecialchars($_POST['ngayNhap'] ?? date('Y-m-d\TH:i')) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Số hóa đơn NCC</label>
                            <input type="text" name="soHoaDonNCC" class="form-control font-monospace"
                                placeholder="VD: HD_FPT_8821, HD_DGW_4410..."
                                value="<?= htmlspecialchars($_POST['soHoaDonNCC'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tiền đã trả trước / cọc (₫)</label>
                            <input type="number" step="1000" name="tongTienTraTruocNCC" class="form-control font-monospace"
                                placeholder="0" value="<?= htmlspecialchars($_POST['tongTienTraTruocNCC'] ?? '0') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trạng thái thanh toán (trangThaiThanhToan)</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="DA_THANH_TOAN" <?= (isset($_POST['trangThaiThanhToan']) && $_POST['trangThaiThanhToan'] === 'DA_THANH_TOAN') ? 'selected' : '' ?>>Đã thanh toán toàn bộ</option>
                                <option value="THANH_TOAN_MOT_PHAN" <?= (isset($_POST['trangThaiThanhToan']) && $_POST['trangThaiThanhToan'] === 'THANH_TOAN_MOT_PHAN') ? 'selected' : '' ?>>Thanh toán một phần (Đặt cọc)</option>
                                <option value="CHUA_THANH_TOAN" <?= (!isset($_POST['trangThaiThanhToan']) || $_POST['trangThaiThanhToan'] === 'CHUA_THANH_TOAN') ? 'selected' : '' ?>>Chưa thanh toán</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trạng thái nhập kho (trangThaiNhapKho)</label>
                            <select name="trangThaiNhapKho" class="form-select">
                                <option value="BAN_NHAP" <?= (!isset($_POST['trangThaiNhapKho']) || $_POST['trangThaiNhapKho'] === 'BAN_NHAP') ? 'selected' : '' ?>>Bản nháp (Đang kiểm hàng / nhập serial)</option>
                                <option value="DA_NHAP_KHO" <?= (isset($_POST['trangThaiNhapKho']) && $_POST['trangThaiNhapKho'] === 'DA_NHAP_KHO') ? 'selected' : '' ?>>Đã nhập kho (Cộng trực tiếp tồn kho)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ghi chú phiếu nhập (ghiChu)</label>
                            <textarea name="ghiChu" rows="2" class="form-control"
                                placeholder="Nhập ghi chú xuất xứ lô hàng, chứng từ vận chuyển đính kèm..."><?= htmlspecialchars($_POST['ghiChu'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Mặt hàng nhập kho ban đầu (ChiTietNhap) -->
            <div class="card-modern mb-4">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-box-seam-fill me-2 text-primary"></i> Khởi tạo mặt hàng nhập kho (Tùy chọn)</div>
                    <small class="text-muted fw-normal">Có thể bổ sung thêm nhiều mặt hàng sau khi tạo phiếu</small>
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Sản phẩm laptop</label>
                            <select name="maSP" id="selectSP" class="form-select">
                                <option value="">-- Chưa gắn sản phẩm (Bổ sung sau) --</option>
                                <?php
                                $sqlSP = "SELECT maSP, tenSP, giaBan, soLuongTon FROM sanpham WHERE isDeleted = 0 ORDER BY tenSP ASC";
                                $resSP = $conn->query($sqlSP);
                                if ($resSP && $resSP->num_rows > 0) {
                                    while ($sp = $resSP->fetch_assoc()) {
                                        $selected = (isset($_POST['maSP']) && $_POST['maSP'] === $sp['maSP']) ? 'selected' : '';
                                        echo "<option value='{$sp['maSP']}' data-giaban='{$sp['giaBan']}' $selected>" . htmlspecialchars($sp['tenSP']) . " [{$sp['maSP']} - Tồn: {$sp['soLuongTon']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Số lượng nhập</label>
                            <input type="number" step="1" min="1" name="soLuongNhap" id="inputSoLuong"
                                value="<?= htmlspecialchars($_POST['soLuongNhap'] ?? '1') ?>" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Giá vốn nhập đơn vị (₫)</label>
                            <input type="number" step="1000" name="giaVonNhap" id="inputGiaVon"
                                value="<?= htmlspecialchars($_POST['giaVonNhap'] ?? '0') ?>" class="form-control font-monospace" placeholder="VD: 18000000">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Thành tiền tạm tính</label>
                            <div class="form-control bg-light text-danger fw-bold text-end font-monospace" id="displayThanhTien">0 ₫</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ghi chú dòng hàng nhập</label>
                            <input type="text" name="ghiChuChiTiet" class="form-control"
                                placeholder="VD: Hàng nguyên seal phân phối chính hãng, phụ kiện đầy đủ..."
                                value="<?= htmlspecialchars($_POST['ghiChuChiTiet'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành Động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="phieu-nhap.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu và tạo phiếu nhập
                </button>
            </div>
        </form>
    </main>
</div>

<script>
    // Tính toán thành tiền tự động
    function updateThanhTien() {
        const sl = parseFloat(document.getElementById('inputSoLuong').value) || 0;
        const gia = parseFloat(document.getElementById('inputGiaVon').value) || 0;
        const total = sl * gia;
        document.getElementById('displayThanhTien').innerText = total.toLocaleString('vi-VN') + ' ₫';
    }

    document.getElementById('inputSoLuong').addEventListener('input', updateThanhTien);
    document.getElementById('inputGiaVon').addEventListener('input', updateThanhTien);
    updateThanhTien();
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
