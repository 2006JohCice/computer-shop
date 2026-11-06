<?php
// app/admin/phieu-nhap/edit.php
$pageTitle = "Chỉnh sửa phiếu nhập kho";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPhieu = htmlspecialchars($_GET['maPhieu'] ?? $_GET['id'] ?? '');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$errors = [];

// Truy vấn thông tin phiếu nhập hiện tại
$sql = "SELECT * FROM phieunhap WHERE maPhieu = '$maPhieu'";
$resultSql = $conn->query($sql);

if ($resultSql && $resultSql->num_rows > 0) {
    $row = $resultSql->fetch_assoc();
    $ngayNhap = $row['ngayNhap'];
    $maNCC = $row['maNCC'];
    $nguoiNhap = $row['nguoiNhap'];
    $soHoaDonNCC = $row['soHoaDonNCC'];
    $tongTienTraTruocNCC = $row['tongTienTraTruocNCC'];
    $tongTienNhap = $row['tongTienNhap'];
    $trangThaiThanhToan = $row['trangThaiThanhToan'];
    $trangThaiNhapKho = $row['trangThaiNhapKho'];
    $ghiChu = $row['ghiChu'];
} else {
    echo "<script>alert('Không tìm thấy mã phiếu nhập!'); window.location.href='phieu-nhap.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $maPhieuEdit = trim($_POST['maPhieu'] ?? '');
    $ngayNhapEdit = trim($_POST['ngayNhap'] ?? '');
    $maNCCEdit = trim($_POST['maNCC'] ?? '');
    $nguoiNhapEdit = trim($_POST['nguoiNhap'] ?? '');
    $soHoaDonNCCEdit = trim($_POST['soHoaDonNCC'] ?? '');
    $tongTienTraTruocNCCEdit = floatval($_POST['tongTienTraTruocNCC'] ?? 0);
    $trangThaiThanhToanEdit = trim($_POST['trangThaiThanhToan'] ?? 'CHUA_THANH_TOAN');
    $trangThaiNhapKhoEdit = trim($_POST['trangThaiNhapKho'] ?? 'BAN_NHAP');
    $ghiChuEdit = trim($_POST['ghiChu'] ?? '');

    // Kiểm tra dữ liệu
    if (empty($maNCCEdit)) {
        $errors[] = "Nhà cung cấp không được để trống.";
    }
    if (empty($nguoiNhapEdit)) {
        $errors[] = "Người lập phiếu không được để trống.";
    }
    if (empty($ngayNhapEdit)) {
        $errors[] = "Thời gian nhập kho không được để trống.";
    }

    if (empty($errors)) {
        // Nếu chuyển trạng thái từ Bản Nháp sang Đã Nhập Kho, cập nhật số lượng tồn cho các sản phẩm
        if ($trangThaiNhapKho !== 'DA_NHAP_KHO' && $trangThaiNhapKhoEdit === 'DA_NHAP_KHO') {
            $sqlItems = "SELECT maSP, soLuongNhap FROM chitietnhap WHERE maPhieu = '$maPhieuEdit'";
            $resItems = $conn->query($sqlItems);
            if ($resItems && $resItems->num_rows > 0) {
                while ($item = $resItems->fetch_assoc()) {
                    $itemSP = $item['maSP'];
                    $itemSL = $item['soLuongNhap'];
                    $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $itemSL WHERE maSP = '$itemSP'");
                }
            }
        }

        $sqlUpdate = "UPDATE phieunhap SET 
                        ngayNhap = '$ngayNhapEdit',
                        maNCC = '$maNCCEdit',
                        nguoiNhap = '$nguoiNhapEdit',
                        soHoaDonNCC = '$soHoaDonNCCEdit',
                        tongTienTraTruocNCC = '$tongTienTraTruocNCCEdit',
                        trangThaiThanhToan = '$trangThaiThanhToanEdit',
                        trangThaiNhapKho = '$trangThaiNhapKhoEdit',
                        ghiChu = '$ghiChuEdit'
                      WHERE maPhieu = '$maPhieuEdit'";

        $resultSqlUpdate = $conn->query($sqlUpdate);
        if ($resultSqlUpdate) {
            echo "<script>window.location.href='phieu-nhap.php';</script>";
            exit;
        } else {
            $errors[] = "Cập nhật phiếu nhập thất bại: " . $conn->error;
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
                        <li class="breadcrumb-item"><a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($maPhieu) ?></a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập Nhật Phiếu Nhập: <?= htmlspecialchars($maPhieu) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin chứng từ, cập nhật hóa đơn đối chiếu và trạng thái nhập hàng.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="detail.php?id=<?= urlencode($maPhieu) ?>" class="btn btn-outline-info rounded-pill px-3">
                    <i class="bi bi-eye me-1"></i> Xem chi tiết & Serial
                </a>
                <a href="phieu-nhap.php" class="btn-back-pill">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
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

        <form action="" method="POST">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <i class="bi bi-pencil-square me-2 text-primary"></i> Thông tin cơ bản phiếu nhập kho
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu nhập <span class="text-danger">*</span></label>
                            <input type="text" name="maPhieu" class="form-control font-monospace text-uppercase bg-light fw-bold"
                                value="<?= htmlspecialchars($maPhieu) ?>" readonly>
                            <small class="text-muted">Khóa chính (PK) không thể thay đổi</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Nhà cung cấp đối tác <span class="text-danger">*</span></label>
                            <select name="maNCC" class="form-select" required>
                                <?php
                                $sqlNCC = "SELECT maNCC, tenCongTy FROM nhacungcap ORDER BY tenCongTy ASC";
                                $resNCC = $conn->query($sqlNCC);
                                if ($resNCC && $resNCC->num_rows > 0) {
                                    while ($ncc = $resNCC->fetch_assoc()) {
                                        $selected = ($maNCC === $ncc['maNCC']) ? 'selected' : '';
                                        echo "<option value='{$ncc['maNCC']}' $selected>" . htmlspecialchars($ncc['tenCongTy']) . " ({$ncc['maNCC']})</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nhân viên lập phiếu <span class="text-danger">*</span></label>
                            <select name="nguoiNhap" class="form-select" required>
                                <?php
                                $sqlAcc = "SELECT maTk, hoTen, role FROM accounts ORDER BY hoTen ASC";
                                $resAcc = $conn->query($sqlAcc);
                                if ($resAcc && $resAcc->num_rows > 0) {
                                    while ($acc = $resAcc->fetch_assoc()) {
                                        $selected = ($nguoiNhap === $acc['maTk']) ? 'selected' : '';
                                        echo "<option value='{$acc['maTk']}' $selected>" . htmlspecialchars($acc['hoTen']) . " [{$acc['maTk']} - {$acc['role']}]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Thời gian nhập kho <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ngayNhap" class="form-control"
                                value="<?= date('Y-m-d\TH:i', strtotime($ngayNhap)) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Số hóa đơn NCC</label>
                            <input type="text" name="soHoaDonNCC" class="form-control font-monospace"
                                value="<?= htmlspecialchars($soHoaDonNCC ?? '') ?>" placeholder="VD: HD_FPT_8821">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tiền đã trả trước / cọc (₫)</label>
                            <input type="number" step="1000" name="tongTienTraTruocNCC" class="form-control font-monospace"
                                value="<?= htmlspecialchars($tongTienTraTruocNCC) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tổng tiền nhập hàng (₫)</label>
                            <input type="text" class="form-control font-monospace bg-light fw-bold text-danger"
                                value="<?= number_format($tongTienNhap, 0, ',', '.') ?> ₫" readonly>
                            <small class="text-muted">Tự động tổng hợp từ chi tiết các mặt hàng</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Trạng thái thanh toán</label>
                            <select name="trangThaiThanhToan" class="form-select">
                                <option value="DA_THANH_TOAN" <?= $trangThaiThanhToan === 'DA_THANH_TOAN' ? 'selected' : '' ?>>Đã thanh toán toàn bộ</option>
                                <option value="THANH_TOAN_MOT_PHAN" <?= $trangThaiThanhToan === 'THANH_TOAN_MOT_PHAN' ? 'selected' : '' ?>>Thanh toán một phần (Đặt cọc)</option>
                                <option value="CHUA_THANH_TOAN" <?= $trangThaiThanhToan === 'CHUA_THANH_TOAN' ? 'selected' : '' ?>>Chưa thanh toán</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Trạng thái nhập kho</label>
                            <select name="trangThaiNhapKho" class="form-select">
                                <option value="BAN_NHAP" <?= $trangThaiNhapKho === 'BAN_NHAP' ? 'selected' : '' ?>>Bản nháp (Đang kiểm hàng / nhập serial)</option>
                                <option value="DA_NHAP_KHO" <?= $trangThaiNhapKho === 'DA_NHAP_KHO' ? 'selected' : '' ?>>Đã nhập kho (Cộng tồn kho)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ghi chú phiếu nhập</label>
                            <textarea name="ghiChu" rows="2" class="form-control"><?= htmlspecialchars($ghiChu ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành Động -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="phieu-nhap.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi phiếu nhập
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
