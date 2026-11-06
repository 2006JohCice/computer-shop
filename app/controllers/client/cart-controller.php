<?php
// app/controllers/client/cart-controller.php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 2) . '/config/database.php';
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli($servername, $username, $password, $dbname);
}

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL: ' . $conn->connect_error]);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đọc dữ liệu đầu vào (hỗ trợ cả JSON body lẫn FormData)
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? $_GET['action'] ?? '';
$maKH = '';

// Lấy mã khách hàng theo Session đăng nhập
if (isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk'])) {
    $sessionMaTk = $conn->real_escape_string($_SESSION['maTk']);
    $resKH = $conn->query("SELECT maKH FROM khachhang WHERE maTk = '$sessionMaTk' LIMIT 1");
    if ($resKH && $rowKH = $resKH->fetch_assoc()) {
        $maKH = $rowKH['maKH'];
    } else {
        $resAcc = $conn->query("SELECT email FROM accounts WHERE maTk = '$sessionMaTk' LIMIT 1");
        if ($resAcc && $rAcc = $resAcc->fetch_assoc()) {
            $emailAcc = $conn->real_escape_string($rAcc['email'] ?? '');
            if (!empty($emailAcc)) {
                $resKHEmail = $conn->query("SELECT maKH FROM khachhang WHERE email = '$emailAcc' LIMIT 1");
                if ($resKHEmail && $rowKHEmail = $resKHEmail->fetch_assoc()) {
                    $maKH = $rowKHEmail['maKH'];
                    $conn->query("UPDATE khachhang SET maTk = '$sessionMaTk' WHERE maKH = '$maKH'");
                }
            }
        }
    }
}

if (empty($maKH)) {
    $maKH = $input['maKH'] ?? $_GET['maKH'] ?? 'KH001';
}

$maSP = trim($input['maSP'] ?? '');
$soLuong = (int)($input['soLuong'] ?? 1);

$maKHSafe = $conn->real_escape_string($maKH);
$maSPSafe = $conn->real_escape_string($maSP);

// 1. Thêm sản phẩm vào giỏ hàng CSDL
if ($action === 'add') {
    if (empty($maSP)) {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu mã sản phẩm']);
        exit;
    }

    // Kiểm tra tồn kho sản phẩm trong CSDL
    $sqlSP = "SELECT tenSP, soLuongTon, trangThai FROM sanpham WHERE maSP = '$maSPSafe' AND isDeleted = 0";
    $resSP = $conn->query($sqlSP);
    if ($resSP && $rSP = $resSP->fetch_assoc()) {
        $tonKho = (int)($rSP['soLuongTon'] ?? 0);
        $tenSP = $rSP['tenSP'] ?? $maSP;
        if ($tonKho <= 0) {
            echo json_encode(['status' => 'error', 'message' => "Sản phẩm \"$tenSP\" hiện đã hết hàng, không thể thêm vào giỏ!"]);
            exit;
        }

        // Kiểm tra số lượng hiện tại trong giỏ
        $checkSql = "SELECT * FROM giohang WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'";
        $resCheck = $conn->query($checkSql);
        $curQty = 0;
        if ($resCheck && $resCheck->num_rows > 0) {
            $curQty = (int)$resCheck->fetch_assoc()['soLuong'];
        }

        $newQty = $curQty + $soLuong;
        if ($newQty > $tonKho) {
            echo json_encode(['status' => 'error', 'message' => "Kho chỉ còn $tonKho máy \"$tenSP\". Không thể thêm vượt quá tồn kho!"]);
            exit;
        }

        if ($curQty > 0) {
            $conn->query("UPDATE giohang SET soLuong = $newQty, ngayCapNhat = NOW() WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'");
        } else {
            $conn->query("INSERT INTO giohang (maKH, maSP, soLuong, ngayCapNhat) VALUES ('$maKHSafe', '$maSPSafe', $soLuong, NOW())");
        }

        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng thành công', 'soLuong' => $newQty]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh']);
        exit;
    }
}

// 2. Cập nhật số lượng sản phẩm
if ($action === 'update') {
    if (empty($maSP)) {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu mã sản phẩm']);
        exit;
    }

    if ($soLuong <= 0) {
        $conn->query("DELETE FROM giohang WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'");
        echo json_encode(['status' => 'success', 'message' => 'Đã xóa sản phẩm khỏi giỏ']);
        exit;
    } else {
        // Kiểm tra tồn kho
        $sqlSP = "SELECT tenSP, soLuongTon FROM sanpham WHERE maSP = '$maSPSafe' AND isDeleted = 0";
        $resSP = $conn->query($sqlSP);
        if ($resSP && $rSP = $resSP->fetch_assoc()) {
            $tonKho = (int)($rSP['soLuongTon'] ?? 0);
            $tenSP = $rSP['tenSP'] ?? $maSP;
            if ($tonKho <= 0) {
                $conn->query("DELETE FROM giohang WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'");
                echo json_encode(['status' => 'error', 'message' => "Sản phẩm \"$tenSP\" đã hết hàng, hệ thống đã tự động gỡ khỏi giỏ hàng."]);
                exit;
            }
            if ($soLuong > $tonKho) {
                $soLuong = $tonKho;
            }
        }

        $checkSql = "SELECT * FROM giohang WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'";
        $resCheck = $conn->query($checkSql);
        if ($resCheck && $resCheck->num_rows > 0) {
            $conn->query("UPDATE giohang SET soLuong = $soLuong, ngayCapNhat = NOW() WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'");
        } else {
            $conn->query("INSERT INTO giohang (maKH, maSP, soLuong, ngayCapNhat) VALUES ('$maKHSafe', '$maSPSafe', $soLuong, NOW())");
        }

        echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật số lượng', 'soLuong' => $soLuong]);
        exit;
    }
}

// 3. Xóa sản phẩm khỏi giỏ hàng
if ($action === 'remove') {
    $conn->query("DELETE FROM giohang WHERE maKH = '$maKHSafe' AND maSP = '$maSPSafe'");
    echo json_encode(['status' => 'success', 'message' => 'Đã xóa sản phẩm khỏi giỏ']);
    exit;
}

// 4. Xóa sạch giỏ hàng
if ($action === 'clear') {
    $conn->query("DELETE FROM giohang WHERE maKH = '$maKHSafe'");
    echo json_encode(['status' => 'success', 'message' => 'Đã làm trống giỏ hàng']);
    exit;
}

// 5. Lấy danh sách sản phẩm trong giỏ hàng
if ($action === 'get') {
    $items = [];
    $sql = "SELECT * FROM giohang WHERE maKH = '$maKHSafe'";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            $items[$r['maSP']] = (int)$r['soLuong'];
        }
    }
    echo json_encode(['status' => 'success', 'cart' => $items]);
    exit;
}

// 6. Đồng bộ toàn bộ giỏ từ LocalStorage lên Database
if ($action === 'sync_all') {
    $cart = $input['cart'] ?? [];
    if (is_array($cart)) {
        $conn->query("DELETE FROM giohang WHERE maKH = '$maKHSafe'");
        foreach ($cart as $sp => $qty) {
            $spS = $conn->real_escape_string($sp);
            $q = (int)$qty;
            if ($q > 0) {
                $conn->query("INSERT INTO giohang (maKH, maSP, soLuong, ngayCapNhat) VALUES ('$maKHSafe', '$spS', $q, NOW())");
            }
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Đồng bộ giỏ hàng thành công']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);

