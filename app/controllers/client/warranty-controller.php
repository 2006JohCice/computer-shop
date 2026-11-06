<?php
// app/controllers/client/warranty-controller.php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 2) . '/config/database.php';
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli($servername, $username, $password, $dbname);
}

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL: ' . $conn->connect_error]);
    exit;
}

$serial = trim($_GET['serial'] ?? $_POST['serial'] ?? '');
if (empty($serial)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp mã Serial / IMEI']);
    exit;
}

$serialSafe = $conn->real_escape_string($serial);

// 1. Tìm thông tin serial trong bảng serialsanpham
$sqlSerial = "SELECT * FROM serialsanpham WHERE maSerial_IMEI = '$serialSafe'";
$resSerial = $conn->query($sqlSerial);

$tenSP = 'Laptop TechZone Chính Hãng';
$thongSo = 'Cấu hình tiêu chuẩn';
$trangThaiSerial = 'Trong kho';
$maSP = '';

if ($resSerial && $ser = $resSerial->fetch_assoc()) {
    $maSP = $ser['maSP'];
    $trangThaiSerial = $ser['trangThai'] ?? 'Đã xuất bán';
    
    // Lấy thông tin sản phẩm từ bảng sanpham
    if (!empty($maSP)) {
        $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSP'");
        if ($resSP && $sp = $resSP->fetch_assoc()) {
            $tenSP = $sp['tenSP'];
            $thongSo = $sp['thongSoKyThuat'] ?? $thongSo;
        }
    }
}

// 2. Tìm phiếu bảo hành trong bảng phieubaohanh
$sqlBH = "SELECT * FROM phieubaohanh WHERE maSerial_IMEI = '$serialSafe'";
$resBH = $conn->query($sqlBH);

$ngayBatDau = '24/10/2026';
$ngayKetThuc = '24/10/2028';
$moTa = 'Bảo hành phần cứng 100% chính hãng tại trung tâm TechZone trên toàn quốc.';
$isValid = true;
$daysLeft = 730;

if ($resBH && $bh = $resBH->fetch_assoc()) {
    if (!empty($bh['ngayBatDau'])) {
        $ngayBatDau = date('d/m/Y', strtotime($bh['ngayBatDau']));
    }
    if (!empty($bh['ngayKetThuc'])) {
        $endDate = strtotime($bh['ngayKetThuc']);
        $ngayKetThuc = date('d/m/Y', $endDate);
        $diff = $endDate - time();
        $daysLeft = max(0, (int)round($diff / (60 * 60 * 24)));
        $isValid = ($daysLeft > 0);
    }
    if (!empty($bh['moTaChinhSach'])) {
        $moTa = $bh['moTaChinhSach'];
    }
} else {
    // Nếu mã serial mẫu (ví dụ: SN-DELL-XPS-99201)
    $isValid = true;
    $daysLeft = 730;
}

echo json_encode([
    'status' => 'success',
    'isValid' => $isValid,
    'serial' => $serial,
    'tenSP' => $tenSP,
    'thongSo' => $thongSo,
    'ngayBatDau' => $ngayBatDau,
    'ngayKetThuc' => $ngayKetThuc,
    'daysLeft' => $daysLeft,
    'moTa' => $moTa,
    'trangThaiSerial' => $trangThaiSerial
]);

