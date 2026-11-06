<?php
// app/admin/don-hang/detail.php
$pageTitle = "Chi tiết đơn bán hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDH = htmlspecialchars($_GET['id'] ?? $_GET['maDH'] ?? 'DH001');

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$alertMsg = '';

// Xử lý Form Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'approve_order') {
        $conn->query("UPDATE donhang SET trangThaiDon = 'HOAN_THANH' WHERE maDH = '$maDH'");
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Đã hoàn tất duyệt và hoàn thành đơn hàng <strong>$maDH</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'ship_order') {
        $conn->query("UPDATE donhang SET trangThaiDon = 'DANG_GIAO' WHERE maDH = '$maDH'");
        $alertMsg = "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                        <i class='bi bi-truck me-2'></i>Đơn hàng <strong>$maDH</strong> đã được chuyển sang trạng thái Đang giao hàng!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'cancel_order') {
        // Hoàn trả serial về kho
        $conn->query("UPDATE serialsanpham SET idChiTietDonHang = NULL, trangThai = 'Trong kho' WHERE idChiTietDonHang IN (SELECT id FROM chitietdonhang WHERE maDH = '$maDH')");

        // Hoàn lại số lượng tồn kho sản phẩm
        $resIt = $conn->query("SELECT maSP, soLuong FROM chitietdonhang WHERE maDH = '$maDH'");
        if ($resIt && $resIt->num_rows > 0) {
            while ($it = $resIt->fetch_assoc()) {
                $sp = $it['maSP'];
                $sl = $it['soLuong'];
                $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $sl WHERE maSP = '$sp'");
            }
        }

        $conn->query("UPDATE donhang SET trangThaiDon = 'DA_HUY' WHERE maDH = '$maDH'");
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-x-circle me-2'></i>Đã hủy đơn hàng <strong>$maDH</strong> và hoàn trả toàn bộ số lượng sản phẩm/serial về kho!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'assign_serial') {
        $idCTDH = intval($_POST['idChiTietDonHang'] ?? 0);
        $serialVal = trim($_POST['maSerial'] ?? '');

        if (!empty($serialVal) && $idCTDH > 0) {
            $conn->query("UPDATE serialsanpham SET idChiTietDonHang = $idCTDH, trangThai = 'Đã bán', ngayXuatKho = NOW() WHERE maSerial_IMEI = '$serialVal'");

            // Tự động kích hoạt phiếu bảo hành điện tử chính hãng khi bán máy
            require_once __DIR__ . '/../../helpers/warranty_helper.php';
            $maPBHMoi = kichHoatBaoHanhTuDong($conn, $serialVal, $dh['maKH'] ?? '');

            $bhText = $maPBHMoi ? " và tự động kích hoạt Phiếu bảo hành điện tử (<strong>$maPBHMoi</strong>)" : "";
            $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='bi bi-upc-scan me-2'></i>Đã gán thành công mã Serial <strong>$serialVal</strong> cho đơn hàng$bhText!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                         </div>";
        }
    } elseif ($action === 'create_invoice') {
        // Tự động xuất hóa đơn nếu chưa có
        $chkHD = $conn->query("SELECT maHD FROM hoadon WHERE maDH = '$maDH'");
        if ($chkHD && $chkHD->num_rows > 0) {
            $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                            <i class='bi bi-exclamation-triangle me-2'></i>Đơn hàng này đã được xuất hóa đơn trước đó!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                         </div>";
        } else {
            $sqlD = "SELECT * FROM donhang WHERE maDH = '$maDH'";
            $resD = $conn->query($sqlD);
            $dRow = ($resD && $resD->num_rows > 0) ? $resD->fetch_assoc() : null;
            if ($dRow) {
                $maHD = 'HD_' . $maDH;
                $maKH = $dRow['maKH'];
                $tenKH = "Khách vãng lai";
                $sdt = "";
                $diaChi = "";
                if (!empty($maKH)) {
                    $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '$maKH'");
                    if ($resKH && $resKH->num_rows > 0) {
                        $rowKH = $resKH->fetch_assoc();
                        $tenKH = $rowKH['hoVaTen'];
                        $sdt = $rowKH['soDienThoai'];
                        $diaChi = $rowKH['diaChi'];
                    }
                }
                $tongTien = $dRow['tongThanhToan'];
                $conn->query("INSERT INTO hoadon (maHD, maDH, ngayXuat, tenKhachHang, soDienThoai, diaChiKhachHang, soTienThanhToan, trangThaiThanhToan, ghiChu) 
                              VALUES ('$maHD', '$maDH', NOW(), '$tenKH', '$sdt', '$diaChi', '$tongTien', 'DA_THANH_TOAN', 'Xuất từ chi tiết đơn hàng')");
                $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <i class='bi bi-receipt me-2'></i>Đã xuất thành công hóa đơn tài chính <strong>$maHD</strong>!
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                             </div>";
            }
        }
    }
}

// Truy vấn thông tin đơn hàng
$sql = "SELECT * FROM donhang WHERE maDH = '$maDH'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<script>alert('Không tìm thấy đơn hàng $maDH!'); window.location.href='don-hang.php';</script>";
    exit;
}
$dh = $res->fetch_assoc();

// Lấy thông tin khách hàng từ bảng khachhang
$dh['tenKhachHang'] = 'Khách vãng lai';
$dh['sdtKhach'] = '';
$dh['emailKhach'] = '';
$dh['diaChiKhach'] = '';
if (!empty($dh['maKH'])) {
    $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '" . $dh['maKH'] . "'");
    if ($resKH && $resKH->num_rows > 0) {
        $rowKH = $resKH->fetch_assoc();
        $dh['tenKhachHang'] = $rowKH['hoVaTen'];
        $dh['sdtKhach'] = $rowKH['soDienThoai'];
        $dh['emailKhach'] = $rowKH['email'];
        $dh['diaChiKhach'] = $rowKH['diaChi'];
    }
}

// Lấy thông tin khuyến mãi từ bảng khuyenmai
$dh['tenKM'] = '';
$dh['giaTriGiam'] = 0;
$dh['loaiKM'] = '';
if (!empty($dh['maKM'])) {
    $resKM = $conn->query("SELECT * FROM khuyenmai WHERE maKM = '" . $dh['maKM'] . "'");
    if ($resKM && $resKM->num_rows > 0) {
        $rowKM = $resKM->fetch_assoc();
        $dh['tenKM'] = $rowKM['tenKM'];
        $dh['giaTriGiam'] = $rowKM['giaTriGiam'];
        $dh['loaiKM'] = $rowKM['loaiKM'];
    }
}

// Lấy thông tin nhân viên phụ trách từ bảng accounts
$dh['tenNhanVien'] = 'Hệ thống / Chưa giao';
$dh['emailNV'] = '';
if (!empty($dh['nguoiPhuTrach'])) {
    $resNV = $conn->query("SELECT * FROM accounts WHERE maTk = '" . $dh['nguoiPhuTrach'] . "'");
    if ($resNV && $resNV->num_rows > 0) {
        $rowNV = $resNV->fetch_assoc();
        $dh['tenNhanVien'] = $rowNV['hoTen'];
        $dh['emailNV'] = $rowNV['email'];
    }
}

// Truy vấn các mặt hàng trong đơn từ bảng chitietdonhang
$sqlItems = "SELECT * FROM chitietdonhang WHERE maDH = '$maDH'";
$resItems = $conn->query($sqlItems);

// Kiểm tra hóa đơn đã xuất
$resHD = $conn->query("SELECT maHD, ngayXuat FROM hoadon WHERE maDH = '$maDH'");
$hoaDonRow = ($resHD && $resHD->num_rows > 0) ? $resHD->fetch_assoc() : null;
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header trang & Action buttons -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="don-hang.php" class="text-decoration-none text-muted">Đơn hàng</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= htmlspecialchars($maDH) ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Đơn Hàng: #<?= htmlspecialchars($maDH) ?></h2>
                    <?php if ($dh['trangThaiDon'] === 'HOAN_THANH'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5"><i class="bi bi-check2-circle me-1"></i>HOÀN THÀNH</span>
                    <?php elseif ($dh['trangThaiDon'] === 'DANG_GIAO'): ?>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5"><i class="bi bi-truck me-1"></i>ĐANG GIAO HÀNG</span>
                    <?php elseif ($dh['trangThaiDon'] === 'CHO_DUYET'): ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5"><i class="bi bi-hourglass-split me-1"></i>CHỜ DUYỆT</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1.5">ĐÃ HỦY</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if ($hoaDonRow): ?>
                    <a href="../hoa-don/detail.php?id=<?= urlencode($hoaDonRow['maHD']) ?>" class="btn btn-outline-success">
                        <i class="bi bi-receipt me-1"></i>Xem Hóa Đơn (<?= htmlspecialchars($hoaDonRow['maHD']) ?>)
                    </a>
                <?php else: ?>
                    <form action="detail.php?id=<?= urlencode($maDH) ?>" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="create_invoice">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-receipt-cutoff me-1"></i>Xuất Hóa Đơn VAT
                        </button>
                    </form>
                <?php endif; ?>

                <a href="edit.php?maDH=<?= urlencode($maDH) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil-square me-1"></i>Sửa đơn
                </a>
                <a href="don-hang.php" class="btn btn-back-pill">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Cột Trái: Thông tin khách hàng & Giao dịch -->
            <div class="col-lg-4">
                <div class="tz-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-circle text-primary me-2"></i>Thông Tin Khách Mua
                    </h5>
                    <div class="mb-2"><strong>Họ tên:</strong> <?= htmlspecialchars($dh['tenKhachHang']) ?></div>
                    <div class="mb-2"><strong>Số điện thoại:</strong> <span class="font-monospace text-primary"><?= htmlspecialchars($dh['sdtKhach']) ?></span></div>
                    <div class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($dh['emailKhach'] ?? 'Không có') ?></div>
                    <div class="mb-3"><strong>Địa chỉ nhận hàng:</strong> <?= htmlspecialchars($dh['diaChiKhach'] ?? 'Mua trực tiếp tại quầy') ?></div>

                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom pt-2">
                        <i class="bi bi-info-circle text-primary me-2"></i>Vận Chuyển & Thanh Toán
                    </h5>
                    <div class="mb-2"><strong>Kênh bán:</strong> <?= $dh['kenhBan'] === 'TAI_QUAY' ? 'Tại quầy Showroom' : 'Đặt Online' ?></div>
                    <div class="mb-2"><strong>Hình thức TT:</strong> <span class="font-monospace fw-semibold"><?= htmlspecialchars($dh['phuongThucThanhToan']) ?></span></div>
                    <div class="mb-2"><strong>Nhân viên phụ trách:</strong> <?= htmlspecialchars($dh['tenNhanVien'] ?? 'Chưa gán') ?></div>
                    <div class="mb-3"><strong>Thời gian đặt:</strong> <?= date('d/m/Y H:i:s', strtotime($dh['ngayTao'])) ?></div>

                    <!-- Hành động xử lý trạng thái đơn -->
                    <?php if ($dh['trangThaiDon'] !== 'HOAN_THANH' && $dh['trangThaiDon'] !== 'DA_HUY'): ?>
                        <div class="border-top pt-3 mt-3 d-flex flex-column gap-2">
                            <form action="detail.php?id=<?= urlencode($maDH) ?>" method="POST">
                                <input type="hidden" name="action" value="ship_order">
                                <button type="submit" class="btn btn-outline-info w-100"><i class="bi bi-truck me-1"></i>Chuyển Đang Giao Hàng</button>
                            </form>
                            <form action="detail.php?id=<?= urlencode($maDH) ?>" method="POST">
                                <input type="hidden" name="action" value="approve_order">
                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2-all me-1"></i>Xác Nhận Hoàn Thành Đơn</button>
                            </form>
                            <form action="detail.php?id=<?= urlencode($maDH) ?>" method="POST">
                                <input type="hidden" name="action" value="cancel_order">
                                <button type="submit" class="btn btn-outline-danger w-100" data-tz-confirm="Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng tồn kho và serial sẽ được hoàn trả." data-tz-title="Xác nhận hủy đơn">
                                    <i class="bi bi-x-circle me-1"></i>Hủy Đơn Hàng
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột Phải: Mặt hàng trong đơn & Serial gắn kèm -->
            <div class="col-lg-8">
                <div class="tz-card p-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-laptop text-primary me-2"></i>Mặt Hàng Trong Đơn & Gán Serial Máy
                    </h5>

                    <div class="table-responsive mb-4">
                        <table class="tz-table">
                            <thead>
                                <tr>
                                    <th>MÃ SP</th>
                                    <th>TÊN LAPTOP</th>
                                    <th class="text-center">SỐ LƯỢNG</th>
                                    <th class="text-end">ĐƠN GIÁ</th>
                                    <th class="text-end">THÀNH TIỀN</th>
                                    <th>MÃ SERIAL / IMEI GÁN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resItems && $resItems->num_rows > 0) {
                                    while ($item = $resItems->fetch_assoc()) {
                                        $idCT = $item['id'];
                                        $maSP = $item['maSP'];

                                        // Lấy thông tin laptop từ bảng sanpham
                                        $tenSP = $maSP;
                                        $thongSoKyThuat = '';
                                        $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSP'");
                                        if ($resSP && $resSP->num_rows > 0) {
                                            $rowSP = $resSP->fetch_assoc();
                                            $tenSP = $rowSP['tenSP'];
                                            $thongSoKyThuat = $rowSP['thongSoKyThuat'];
                                        }

                                        // Lấy các serial đã gán cho dòng chi tiết này
                                        $resSer = $conn->query("SELECT maSerial_IMEI, trangThai FROM serialsanpham WHERE idChiTietDonHang = $idCT");
                                        $serialsGaned = [];
                                        if ($resSer && $resSer->num_rows > 0) {
                                            while ($s = $resSer->fetch_assoc()) {
                                                $serialsGaned[] = $s['maSerial_IMEI'];
                                            }
                                        }

                                        // Lấy danh sách serial còn trong kho để gán nếu thiếu
                                        $resAvailable = $conn->query("SELECT maSerial_IMEI FROM serialsanpham WHERE maSP = '$maSP' AND trangThai = 'Trong kho' AND idChiTietDonHang IS NULL LIMIT 10");
                                ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($maSP) ?></span></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($tenSP) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($thongSoKyThuat) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-bold text-dark"><?= $item['soLuong'] ?> chiếc</div>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    (Kho hiện còn: <strong class="<?= ($rowSP['soLuongTon'] ?? 0) <= 0 ? 'text-danger' : 'text-success' ?>"><?= (int)($rowSP['soLuongTon'] ?? 0) ?></strong>)
                                                </small>
                                            </td>
                                            <td class="text-end"><?= number_format($item['donGia'], 0, ',', '.') ?> ₫</td>
                                            <td class="text-end fw-bold text-danger"><?= number_format($item['thanhTien'], 0, ',', '.') ?> ₫</td>
                                            <td>
                                                <?php if (!empty($serialsGaned)): ?>
                                                    <?php foreach ($serialsGaned as $ser): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace d-block mb-1">
                                                            <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($ser) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning border mb-1">Chưa gán Serial</span>
                                                <?php endif; ?>

                                                <?php if (count($serialsGaned) < $item['soLuong'] && $dh['trangThaiDon'] !== 'DA_HUY'): ?>
                                                    <form action="detail.php?id=<?= urlencode($maDH) ?>" method="POST" class="d-flex gap-1 mt-1">
                                                        <input type="hidden" name="action" value="assign_serial">
                                                        <input type="hidden" name="idChiTietDonHang" value="<?= $idCT ?>">
                                                        <select name="maSerial" class="form-select form-select-sm font-monospace" required style="width: 160px;">
                                                            <option value="">-- Chọn Serial kho --</option>
                                                            <?php
                                                            if ($resAvailable && $resAvailable->num_rows > 0) {
                                                                while ($av = $resAvailable->fetch_assoc()) {
                                                                    echo "<option value='{$av['maSerial_IMEI']}'>{$av['maSerial_IMEI']}</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-tz-primary px-2" title="Gán Serial"><i class="bi bi-plus-lg"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tổng kết tiền đơn hàng -->
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tổng tiền hàng:</span>
                                    <span class="fw-bold"><?= number_format($dh['tongTienHang'], 0, ',', '.') ?> ₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Voucher giảm giá (<?= htmlspecialchars($dh['tenKM'] ?? 'Không') ?>):</span>
                                    <span class="text-danger fw-bold">-<?= number_format($dh['tienGiamGia'], 0, ',', '.') ?> ₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí giao hàng:</span>
                                    <span><?= number_format($dh['phiVanChuyen'], 0, ',', '.') ?> ₫</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-6">Tổng thanh toán:</span>
                                    <span class="fw-bold fs-5 text-danger"><?= number_format($dh['tongThanhToan'], 0, ',', '.') ?> ₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
