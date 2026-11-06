<?php
// app/admin/hoa-don/detail.php
$pageTitle = "Chi tiết hóa đơn & Bản in";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maHD = htmlspecialchars($_GET['id'] ?? $_GET['maHD'] ?? 'HD_VAT_001');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$alertMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_payment') {
        $conn->query("UPDATE hoadon SET trangThaiThanhToan = 'DA_THANH_TOAN' WHERE maHD = '$maHD'");
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Đã cập nhật trạng thái Đã thanh toán thành công cho hóa đơn <strong>$maHD</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Truy vấn thông tin hóa đơn
$sql = "SELECT * FROM hoadon WHERE maHD = '$maHD'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy hóa đơn $maHD!'); window.location.href='hoa-don.php';</script>";
    exit;
}
$hd = $res->fetch_assoc();
$maDH = $hd['maDH'];

// Lấy thông tin đơn hàng gốc nếu có
$hd['ngayDatHang'] = $hd['ngayXuat'];
$hd['phuongThucThanhToan'] = 'TIEN_MAT';
$hd['tongTienHang'] = $hd['soTienThanhToan'];
$hd['tienGiamGia'] = 0;
$hd['phiVanChuyen'] = 0;
$hd['tenNhanVien'] = 'Thu ngân';

if (!empty($maDH)) {
    $resDH = $conn->query("SELECT * FROM donhang WHERE maDH = '$maDH'");
    if ($resDH && $resDH->num_rows > 0) {
        $rowDH = $resDH->fetch_assoc();
        $hd['ngayDatHang'] = $rowDH['ngayTao'];
        $hd['phuongThucThanhToan'] = $rowDH['phuongThucThanhToan'];
        $hd['tongTienHang'] = $rowDH['tongTienHang'];
        $hd['tienGiamGia'] = $rowDH['tienGiamGia'];
        $hd['phiVanChuyen'] = $rowDH['phiVanChuyen'];

        $nguoiPhuTrach = $rowDH['nguoiPhuTrach'];
        if (!empty($nguoiPhuTrach)) {
            $resNV = $conn->query("SELECT hoTen FROM accounts WHERE maTk = '$nguoiPhuTrach'");
            if ($resNV && $resNV->num_rows > 0) {
                $hd['tenNhanVien'] = $resNV->fetch_assoc()['hoTen'];
            }
        }
    }
}

// Truy vấn danh sách mặt hàng của đơn hàng này từ bảng chitietdonhang
$sqlItems = "SELECT * FROM chitietdonhang WHERE maDH = '$maDH'";
$resItems = $conn->query($sqlItems);
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header điều hướng & Nút In hóa đơn -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 d-print-none">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="hoa-don.php" class="text-decoration-none text-muted">Hóa đơn</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= htmlspecialchars($maHD) ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Hóa Đơn: <?= htmlspecialchars($maHD) ?></h2>
                    <?php if ($hd['trangThaiThanhToan'] === 'DA_THANH_TOAN'): ?>
                        <span class="tz-badge tz-badge-success"><i class="bi bi-check2-circle"></i>ĐÃ THANH TOÁN</span>
                    <?php else: ?>
                        <span class="tz-badge tz-badge-warning"><i class="bi bi-clock-history"></i>CHƯA THANH TOÁN</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-tz-primary">
                    <i class="bi bi-printer me-1"></i>In Hóa Đơn VAT
                </button>
                <a href="../don-hang/detail.php?id=<?= urlencode($maDH) ?>" class="btn btn-outline-info">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Xem Đơn Gốc (<?= htmlspecialchars($maDH) ?>)
                </a>
                <a href="hoa-don.php" class="btn btn-back-pill">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Bản In Hóa Đơn Trực Quan -->
        <div class="tz-card p-5 mb-5 shadow-sm" id="invoicePrintArea">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">TECHZONE LAPTOP STORE</h3>
                    <p class="text-secondary small mb-0">Hệ thống phân phối Laptop & Linh kiện chính hãng</p>
                    <p class="text-secondary small mb-0">Địa chỉ: 123 Đường Cầu Giấy, Quận Cầu Giấy, TP. Hà Nội</p>
                    <p class="text-secondary small mb-0">Hotline: 1900 6868 | Email: cskh@techzone.vn</p>
                </div>
                <div class="text-end">
                    <h4 class="fw-bold text-dark mb-1 font-monospace">HÓA ĐƠN BÁN HÀNG</h4>
                    <div class="font-monospace text-primary fw-bold fs-5"><?= htmlspecialchars($hd['maHD']) ?></div>
                    <div class="text-muted small">Mã đơn gốc: <strong><?= htmlspecialchars($hd['maDH']) ?></strong></div>
                    <div class="text-muted small">Ngày xuất: <?= date('d/m/Y H:i', strtotime($hd['ngayXuat'])) ?></div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-6">
                    <h6 class="fw-bold text-dark text-uppercase small border-bottom pb-1">Khách Hàng / Đơn Vị Nhận</h6>
                    <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($hd['tenKhachHang']) ?></div>
                    <div class="text-muted small">Điện thoại: <span class="font-monospace text-dark"><?= htmlspecialchars($hd['soDienThoai']) ?></span></div>
                    <div class="text-muted small">Địa chỉ: <?= htmlspecialchars($hd['diaChiKhachHang'] ?? 'Tại quầy') ?></div>
                    <?php if (!empty($hd['maSoThueKH'])): ?>
                        <div class="text-muted small">Mã số thuế: <span class="font-monospace text-dark"><?= htmlspecialchars($hd['maSoThueKH']) ?></span></div>
                    <?php endif; ?>
                </div>

                <div class="col-6 text-end">
                    <h6 class="fw-bold text-dark text-uppercase small border-bottom pb-1">Chứng Từ & Thanh Toán</h6>
                    <div class="text-muted small">Hình thức: <strong class="text-dark"><?= htmlspecialchars($hd['phuongThucThanhToan'] ?? 'Tiền mặt') ?></strong></div>
                    <div class="text-muted small">Nhân viên lập: <strong><?= htmlspecialchars($hd['tenNhanVien'] ?? 'Thu ngân') ?></strong></div>
                    <div class="text-muted small">Ghi chú: <?= htmlspecialchars($hd['ghiChu'] ?? 'Hóa đơn đã bao gồm thuế GTGT') ?></div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>TÊN MẶT HÀNG SẢN PHẨM</th>
                            <th class="text-center" style="width: 100px;">SỐ LƯỢNG</th>
                            <th class="text-end" style="width: 160px;">ĐƠN GIÁ</th>
                            <th class="text-end" style="width: 180px;">THÀNH TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        if ($resItems && $resItems->num_rows > 0) {
                            while ($it = $resItems->fetch_assoc()) {
                                $maSP = $it['maSP'];
                                $tenSP = $maSP;
                                $thongSoKyThuat = '';
                                $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSP'");
                                if ($resSP && $resSP->num_rows > 0) {
                                    $rowSP = $resSP->fetch_assoc();
                                    $tenSP = $rowSP['tenSP'];
                                    $thongSoKyThuat = $rowSP['thongSoKyThuat'];
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><?= $stt++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tenSP) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($thongSoKyThuat) ?></small>
                                    </td>
                                    <td class="text-center fw-bold"><?= $it['soLuong'] ?></td>
                                    <td class="text-end font-monospace"><?= number_format($it['donGia'], 0, ',', '.') ?> ₫</td>
                                    <td class="text-end font-monospace fw-bold text-dark"><?= number_format($it['thanhTien'], 0, ',', '.') ?> ₫</td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-3 text-muted'>Đơn hàng trọn gói dịch vụ</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tổng thanh toán đơn hàng:</td>
                            <td class="text-end font-monospace fw-bold text-danger fs-5">
                                <?= number_format($hd['soTienThanhToan'], 0, ',', '.') ?> ₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row pt-4 text-center">
                <div class="col-6">
                    <div class="fw-bold text-dark">NGƯỜI MUA HÀNG</div>
                    <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                </div>
                <div class="col-6">
                    <div class="fw-bold text-dark">ĐẠI DIỆN CỬA HÀNG TECHZONE</div>
                    <small class="text-muted">(Ký, đóng dấu)</small>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
