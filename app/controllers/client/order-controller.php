<?php
// app/controllers/client/order-controller.php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 2) . '/config/database.php';
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli($servername, $username, $password, $dbname);
}

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL: ' . $conn->connect_error]);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!$data) {
    $data = $_POST;
}

$maDH = trim($data['maDH'] ?? '');
if (empty($maDH)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']);
    exit;
}

$maDHSafe = $conn->real_escape_string($maDH);
$hoTen = $conn->real_escape_string(trim($data['hoTen'] ?? 'Khách Mua Online'));
$sdt = $conn->real_escape_string(trim($data['sdt'] ?? ''));
$diaChi = $conn->real_escape_string(trim($data['diaChi'] ?? ''));
$ghiChu = $conn->real_escape_string(trim($data['ghiChu'] ?? ''));
$phuongThuc = $conn->real_escape_string(trim($data['phuongThuc'] ?? 'COD'));
$phiShip = (float)($data['phiShip'] ?? 15000);
$tongTienHang = (float)($data['tongTienHang'] ?? 0);
$tienGiamGia = (float)($data['tienGiamGia'] ?? 0);
$tongThanhToan = (float)($data['tongThanhToan'] ?? ($tongTienHang + $phiShip - $tienGiamGia));
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartItems = $data['cartItems'] ?? [];
$maKH = '';

// Lấy mã khách hàng từ Session đăng nhập
if (isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk'])) {
    $sessionMaTk = $conn->real_escape_string($_SESSION['maTk']);
    $resKH = $conn->query("SELECT maKH FROM khachhang WHERE maTk = '$sessionMaTk' LIMIT 1");
    if ($resKH && $rowKH = $resKH->fetch_assoc()) {
        $maKH = $rowKH['maKH'];
    }
}

if (empty($maKH)) {
    $maKH = $conn->real_escape_string($data['maKH'] ?? $_GET['kh'] ?? 'KH001');
}

// 0. KIỂM TRA TỒN KHO TRƯỚC KHI TẠO ĐƠN
if (!empty($cartItems) && is_array($cartItems)) {
    foreach ($cartItems as $spKey => $qty) {
        $spSafe = $conn->real_escape_string($spKey);
        $q = (int)$qty;
        if ($q > 0) {
            $chkSP = $conn->query("SELECT tenSP, soLuongTon FROM sanpham WHERE maSP = '$spSafe' AND isDeleted = 0");
            if ($chkSP && $rowSP = $chkSP->fetch_assoc()) {
                $tonKho = (int)($rowSP['soLuongTon'] ?? 0);
                $tenSP = $rowSP['tenSP'] ?? $spKey;
                if ($tonKho <= 0) {
                    echo json_encode(['success' => false, 'message' => "Sản phẩm \"$tenSP\" hiện đã hết hàng trong kho. Không thể đặt hàng!"]);
                    exit;
                }
                if ($q > $tonKho) {
                    echo json_encode(['success' => false, 'message' => "Sản phẩm \"$tenSP\" chỉ còn $tonKho máy trong kho. Không đủ số lượng đặt!"]);
                    exit;
                }
            }
        }
    }
}

$trangThaiDon = 'CHO_DUYET';
$trangThaiTT = 'Chờ thanh toán tiền mặt (COD)';

// 1. Lưu vào bảng donhang (Không dùng ORM phức tạp)
$sqlDon = "INSERT INTO donhang (maDH, maKH, ngayTao, kenhBan, phuongThucThanhToan, phiVanChuyen, tongTienHang, tienGiamGia, tongThanhToan, trangThaiDon)
           VALUES ('$maDHSafe', '$maKH', NOW(), 'ONLINE', '$phuongThuc', $phiShip, $tongTienHang, $tienGiamGia, $tongThanhToan, '$trangThaiDon')
           ON DUPLICATE KEY UPDATE tongThanhToan = VALUES(tongThanhToan), trangThaiDon = VALUES(trangThaiDon)";
$conn->query($sqlDon);

// 2. Lưu vào bảng userdathang
$sqlUser = "INSERT INTO userdathang (maDH, tenNguoiNhan, sdtNhanHang, diaChiGiaoHang, ghiChuKhachHang, trangThaiThanhToan)
            VALUES ('$maDHSafe', '$hoTen', '$sdt', '$diaChi', '$ghiChu', '$trangThaiTT')
            ON DUPLICATE KEY UPDATE tenNguoiNhan = VALUES(tenNguoiNhan), sdtNhanHang = VALUES(sdtNhanHang)";
$conn->query($sqlUser);

// 3. Lưu chi tiết sản phẩm vào chitietdonhang & TRỪ SỐ LƯỢNG TỒN KHO TRONG BẢNG sanpham
if (!empty($cartItems) && is_array($cartItems)) {
    foreach ($cartItems as $spKey => $qty) {
        $spSafe = $conn->real_escape_string($spKey);
        $q = (int)$qty;
        if ($q > 0) {
            // Lấy đơn giá từ bảng sanpham
            $donGia = 0;
            $resGia = $conn->query("SELECT giaBan FROM sanpham WHERE maSP = '$spSafe'");
            if ($resGia && $rG = $resGia->fetch_assoc()) {
                $donGia = (float)$rG['giaBan'];
            }
            $thanhTien = $q * $donGia;
            $conn->query("INSERT INTO chitietdonhang (maDH, maSP, soLuong, donGia, thanhTien) VALUES ('$maDHSafe', '$spSafe', $q, $donGia, $thanhTien)");

            // Trừ số lượng tồn kho của sản phẩm
            $conn->query("UPDATE sanpham SET soLuongTon = GREATEST(0, soLuongTon - $q) WHERE maSP = '$spSafe'");
        }
    }
}

// 4. Xóa sạch giỏ hàng trong CSDL của khách sau khi đặt thành công
$conn->query("DELETE FROM giohang WHERE maKH = '$maKH'");

echo json_encode([
    'success' => true,
    'message' => 'Tạo đơn hàng thành công',
    'maDH' => $maDH,
    'tongThanhToan' => $tongThanhToan,
    'trangThaiDon' => $trangThaiDon
]);

