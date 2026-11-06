<?php
// app/client/tra-cuu-bao-hanh/index.php
// Tra cứu đơn hàng & Bảo hành điện tử chính hãng TechZone (100% dữ liệu từ CSDL)
// Không dùng Session / Cookies: Hỗ trợ chọn khách hàng, tìm kiếm theo Email tài khoản, Mã đơn hàng, SĐT

$pageTitle = "Tra Cứu Đơn Hàng & Bảo Hành Điện Tử";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

require_once dirname(__DIR__, 2) . '/config/database.php';
global $servername, $username, $password, $dbname;
if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    $conn = @new mysqli($servername, $username, $password, $dbname);
}
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Đồng bộ bảo hành tự động cho tất cả máy đã bán mà chưa tạo phiếu
require_once dirname(__DIR__, 2) . '/helpers/warranty_helper.php';
dongBoBaoHanhMayDaBan($conn);

// Xác định tab đang hoạt động
$activeTab = trim($_GET['tab'] ?? '');
if (isset($_GET['serial']) && !empty($_GET['serial'])) {
    $activeTab = 'warranty';
} elseif (empty($activeTab)) {
    $activeTab = 'order';
}

$alertMsg = '';

// Xử lý Khách hàng yêu cầu hủy đơn hàng (nếu đơn đang ở trạng thái CHO_DUYET)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'user_cancel_order') {
        $cancelDH = trim($_POST['maDH'] ?? '');
        $lyDo = trim($_POST['lyDo'] ?? '');

        if (!empty($cancelDH)) {
            $chkDH = $conn->query("SELECT trangThaiDon FROM donhang WHERE maDH = '$cancelDH'");
            if ($chkDH && $chkDH->num_rows > 0) {
                $statusCurrent = $chkDH->fetch_assoc()['trangThaiDon'];
                if ($statusCurrent === 'CHO_DUYET' || $statusCurrent === 'Tiếp nhận') {
                    $conn->query("UPDATE donhang SET trangThaiDon = 'DA_HUY' WHERE maDH = '$cancelDH'");

                    // Hoàn lại số lượng tồn kho cho các sản phẩm trong đơn vừa hủy
                    $resItems = $conn->query("SELECT maSP, soLuong FROM chitietdonhang WHERE maDH = '$cancelDH'");
                    if ($resItems && $resItems->num_rows > 0) {
                        while ($it = $resItems->fetch_assoc()) {
                            $spCancel = $it['maSP'];
                            $slCancel = (int)$it['soLuong'];
                            $conn->query("UPDATE sanpham SET soLuongTon = soLuongTon + $slCancel WHERE maSP = '$spCancel'");
                        }
                    }

                    // Cập nhật ghi chú nếu có trong userdathang
                    $lyDoEsc = $conn->real_escape_string($lyDo);
                    $conn->query("UPDATE userdathang SET ghiChuKhachHang = CONCAT(COALESCE(ghiChuKhachHang, ''), ' [Khách hủy: $lyDoEsc]') WHERE maDH = '$cancelDH'");

                    $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                    <i class='bi bi-info-circle-fill me-2'></i>Đơn hàng <strong>$cancelDH</strong> đã được hủy thành công theo yêu cầu của quý khách!
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 </div>";
                } else {
                    $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <i class='bi bi-exclamation-triangle-fill me-2'></i>Đơn hàng <strong>$cancelDH</strong> đã xuất kho hoặc đang giao, không thể hủy tự động. Quý khách vui lòng gọi Hotline 1900 8888 để được hỗ trợ!
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 </div>";
                }
            }
        }
    }
}

// ====================================================
// 1. LẤY DANH SÁCH TẤT CẢ KHÁCH HÀNG TỪ CSDL ĐỂ LÀM DROPDOWN
// ====================================================
$allCustomers = [];
$resAllKH = $conn->query("SELECT * FROM khachhang ORDER BY maKH ASC");
if ($resAllKH && $resAllKH->num_rows > 0) {
    while ($rKH = $resAllKH->fetch_assoc()) {
        $mKH = $rKH["maKH"];

        // Đếm số đơn hàng của khách hàng này
        $cntOrders = 0;
        $resCount = $conn->query("SELECT COUNT(*) as total FROM donhang WHERE maKH = '$mKH'");
        if ($resCount && $rCount = $resCount->fetch_assoc()) {
            $cntOrders = (int)$rCount["total"];
        }
        $rKH["soDonHang"] = $cntOrders;

        // Lấy thêm email tài khoản accounts nếu có
        $mTk = $rKH["maTk"] ?? "";
        $emailTK = "";
        $userTK = "";
        if (!empty($mTk)) {
            $resTK = $conn->query("SELECT email, tenTaiKhoan FROM accounts WHERE maTk = '$mTk'");
            if ($resTK && $rTK = $resTK->fetch_assoc()) {
                $emailTK = $rTK["email"] ?? "";
                $userTK = $rTK["tenTaiKhoan"] ?? "";
            }
        }
        $rKH["emailTK"] = $emailTK;
        $rKH["tenTaiKhoan"] = $userTK;
        $allCustomers[] = $rKH;
    }
}

// ====================================================
// 2. XÁC ĐỊNH MÃ KHÁCH HÀNG THEO SESSION ĐĂNG NHẬP HOẶC TÌM KIẾM
//    Session format:
//    $_SESSION['login'] = true;
//    $_SESSION['maTk'] = $acc['maTk'];
//    $_SESSION['user_name'] = $acc['hoTen'] ?? $user_input;
//    $_SESSION['role'] = $role;
// ====================================================
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk']);
$sessionMaTk = $_SESSION['maTk'] ?? '';
$sessionUserName = $_SESSION['user_name'] ?? '';
$sessionRole = $_SESSION['role'] ?? '';

// Lấy mã khách hàng liên kết với tài khoản đang đăng nhập
$loggedMaKH = '';
if ($isLoggedIn) {
    $maTkEsc = $conn->real_escape_string($sessionMaTk);
    $resKH = $conn->query("SELECT * FROM khachhang WHERE maTk = '$maTkEsc' LIMIT 1");
    if ($resKH && $rowKH = $resKH->fetch_assoc()) {
        $loggedMaKH = $rowKH['maKH'];
    } else {
        // Dự phòng: Tìm theo email của accounts
        $resAcc = $conn->query("SELECT email, hoTen FROM accounts WHERE maTk = '$maTkEsc' LIMIT 1");
        if ($resAcc && $rAcc = $resAcc->fetch_assoc()) {
            $emailAcc = $conn->real_escape_string($rAcc['email'] ?? '');
            if (!empty($emailAcc)) {
                $resKHEmail = $conn->query("SELECT * FROM khachhang WHERE email = '$emailAcc' LIMIT 1");
                if ($resKHEmail && $rowKHEmail = $resKHEmail->fetch_assoc()) {
                    $loggedMaKH = $rowKHEmail['maKH'];
                    $conn->query("UPDATE khachhang SET maTk = '$maTkEsc' WHERE maKH = '$loggedMaKH'");
                }
            }
        }
    }
}

$searchKeyword = trim($_GET['q'] ?? $_GET['id'] ?? $_GET['email'] ?? '');
$paramKH = trim($_GET['kh'] ?? '');
$activeMaKH = '';
$searchSingleOrder = null;
$searchStatusMessage = '';
$searchStatusType = 'info';

if (!empty($searchKeyword)) {
    // A. Kiểm tra tìm theo Email (có chứa dấu @)
    if (strpos($searchKeyword, '@') !== false) {
        $emailEsc = $conn->real_escape_string($searchKeyword);

        // 1. Tìm trong bảng accounts trước
        $sqlAcc = "SELECT * FROM accounts WHERE email = '$emailEsc' LIMIT 1";
        $resAcc = $conn->query($sqlAcc);
        if ($resAcc && $rAcc = $resAcc->fetch_assoc()) {
            $foundMaTk = $rAcc["maTk"];
            $sqlKHMatch = "SELECT * FROM khachhang WHERE maTk = '$foundMaTk' LIMIT 1";
            $resKHMatch = $conn->query($sqlKHMatch);
            if ($resKHMatch && $rKHMatch = $resKHMatch->fetch_assoc()) {
                $activeMaKH = $rKHMatch["maKH"];
                $searchStatusType = 'success';
                $searchStatusMessage = "Tìm thấy tài khoản đăng ký với email <strong>" . htmlspecialchars($searchKeyword) . "</strong> của khách hàng <strong>" . htmlspecialchars($rKHMatch["hoVaTen"]) . "</strong> (" . $activeMaKH . ").";
            }
        }

        // 2. Nếu chưa thấy, tìm tiếp trong cột email của bảng khachhang
        if (empty($activeMaKH)) {
            $sqlKHEmail = "SELECT * FROM khachhang WHERE email = '$emailEsc' LIMIT 1";
            $resKHEmail = $conn->query($sqlKHEmail);
            if ($resKHEmail && $rKHEmail = $resKHEmail->fetch_assoc()) {
                $activeMaKH = $rKHEmail["maKH"];
                $searchStatusType = 'success';
                $searchStatusMessage = "Tìm thấy email khách hàng <strong>" . htmlspecialchars($searchKeyword) . "</strong> của <strong>" . htmlspecialchars($rKHEmail["hoVaTen"]) . "</strong> (" . $activeMaKH . ").";
            }
        }
    }

    // B. Kiểm tra tìm theo Mã đơn hàng (VD: DH7117, DH001)
    if (empty($activeMaKH)) {
        $dhEsc = $conn->real_escape_string(strtoupper($searchKeyword));
        $sqlCheckDH = "SELECT * FROM donhang WHERE maDH = '$dhEsc' LIMIT 1";
        $resCheckDH = $conn->query($sqlCheckDH);
        if ($resCheckDH && $rCheckDH = $resCheckDH->fetch_assoc()) {
            $searchSingleOrder = $rCheckDH["maDH"];
            $activeMaKH = $rCheckDH["maKH"];
            $searchStatusType = 'success';
            $searchStatusMessage = "Tìm thấy đơn hàng <strong>" . htmlspecialchars($searchSingleOrder) . "</strong> của khách hàng mã <strong>$activeMaKH</strong>.";
        }
    }

    // C. Kiểm tra tìm theo Số điện thoại
    if (empty($activeMaKH)) {
        $sdtEsc = $conn->real_escape_string($searchKeyword);
        $sqlSdt = "SELECT * FROM khachhang WHERE soDienThoai = '$sdtEsc' LIMIT 1";
        $resSdt = $conn->query($sqlSdt);
        if ($resSdt && $rSdt = $resSdt->fetch_assoc()) {
            $activeMaKH = $rSdt["maKH"];
            $searchStatusType = 'success';
            $searchStatusMessage = "Tìm thấy số điện thoại <strong>" . htmlspecialchars($searchKeyword) . "</strong> của khách hàng <strong>" . htmlspecialchars($rSdt["hoVaTen"]) . "</strong>.";
        }
    }

    // D. Kiểm tra tìm theo Tên tài khoản (username)
    if (empty($activeMaKH)) {
        $userEsc = $conn->real_escape_string($searchKeyword);
        $sqlUser = "SELECT * FROM accounts WHERE tenTaiKhoan = '$userEsc' LIMIT 1";
        $resUser = $conn->query($sqlUser);
        if ($resUser && $rUser = $resUser->fetch_assoc()) {
            $foundMaTk = $rUser["maTk"];
            $sqlKHUser = "SELECT * FROM khachhang WHERE maTk = '$foundMaTk' LIMIT 1";
            $resKHUser = $conn->query($sqlKHUser);
            if ($resKHUser && $rKHUser = $resKHUser->fetch_assoc()) {
                $activeMaKH = $rKHUser["maKH"];
                $searchStatusType = 'success';
                $searchStatusMessage = "Tìm thấy tài khoản <strong>" . htmlspecialchars($searchKeyword) . "</strong> của khách hàng <strong>" . htmlspecialchars($rKHUser["hoVaTen"]) . "</strong>.";
            }
        }
    }

    // Nếu không tìm thấy bất kỳ dữ liệu nào
    if (empty($activeMaKH)) {
        $searchStatusType = 'danger';
        $searchStatusMessage = "Không tìm thấy dữ liệu nào phù hợp với từ khóa: <strong>" . htmlspecialchars($searchKeyword) . "</strong>. Vui lòng kiểm tra lại mã đơn hàng hoặc email đăng ký tài khoản.";
    }
}

// Nếu không tìm kiếm nhưng ĐÃ ĐĂNG NHẬP: Tự động dùng mã khách hàng của session
if (empty($activeMaKH) && $isLoggedIn && !empty($loggedMaKH)) {
    // Nếu là admin và có truyền ?kh= thì admin xem được khách khác
    if ($sessionRole === 'admin' && !empty($paramKH)) {
        $activeMaKH = $paramKH;
    } else {
        $activeMaKH = $loggedMaKH;
    }
}

// Nếu chưa đăng nhập nhưng có truyền ?kh= trên URL
if (empty($activeMaKH) && !empty($paramKH)) {
    $activeMaKH = $paramKH;
}

// Nếu vẫn chưa có mã khách hàng, mặc định lấy khách hàng đầu tiên có đơn hàng trong CSDL
if (empty($activeMaKH)) {
    $resDef = $conn->query("SELECT maKH FROM donhang ORDER BY ngayTao DESC LIMIT 1");
    if ($resDef && $rDef = $resDef->fetch_assoc()) {
        $activeMaKH = $rDef["maKH"];
    } else {
        $activeMaKH = 'KH001';
    }
}

// ====================================================
// 3. LẤY HỒ SƠ KHÁCH HÀNG & TÀI KHOẢN ĐANG XEM
// ====================================================
$customerProfile = null;
$accountProfile = null;
$sqlCurKH = "SELECT * FROM khachhang WHERE maKH = '$activeMaKH' LIMIT 1";
$resCurKH = $conn->query($sqlCurKH);
if ($resCurKH && $rCurKH = $resCurKH->fetch_assoc()) {
    $customerProfile = $rCurKH;
    $curMaTk = $rCurKH["maTk"] ?? "";
    if (!empty($curMaTk)) {
        $sqlCurAcc = "SELECT * FROM accounts WHERE maTk = '$curMaTk' LIMIT 1";
        $resCurAcc = $conn->query($sqlCurAcc);
        if ($resCurAcc && $rCurAcc = $resCurAcc->fetch_assoc()) {
            $accountProfile = $rCurAcc;
        }
    }
}

// ====================================================
// 4. LẤY DANH SÁCH ĐƠN HÀNG CỦA KHÁCH HÀNG ĐANG XEM
// ====================================================
$customerOrders = [];
if (!empty($searchSingleOrder)) {
    // Nếu tìm kiếm chính xác 1 mã đơn hàng
    $sqlListDH = "SELECT * FROM donhang WHERE maDH = '$searchSingleOrder'";
} else {
    // Liệt kê toàn bộ đơn hàng của khách hàng này
    $sqlListDH = "SELECT * FROM donhang WHERE maKH = '$activeMaKH' ORDER BY ngayTao DESC";
}
$resListDH = $conn->query($sqlListDH);
if ($resListDH && $resListDH->num_rows > 0) {
    while ($rDH = $resListDH->fetch_assoc()) {
        $mDH = $rDH["maDH"];

        // Lấy thông tin người nhận hàng từ userdathang nếu có
        $recipientInfo = [
            'tenNguoiNhan' => $customerProfile['hoVaTen'] ?? 'Khách Mua Hàng',
            'sdtNhanHang' => $customerProfile['soDienThoai'] ?? '',
            'diaChiGiaoHang' => $customerProfile['diaChi'] ?? '',
            'ghiChuKhachHang' => ''
        ];
        $resUD = $conn->query("SELECT * FROM userdathang WHERE maDH = '$mDH' LIMIT 1");
        if ($resUD && $rUD = $resUD->fetch_assoc()) {
            if (!empty($rUD['tenNguoiNhan'])) $recipientInfo['tenNguoiNhan'] = $rUD['tenNguoiNhan'];
            if (!empty($rUD['sdtNhanHang'])) $recipientInfo['sdtNhanHang'] = $rUD['sdtNhanHang'];
            if (!empty($rUD['diaChiGiaoHang'])) $recipientInfo['diaChiGiaoHang'] = $rUD['diaChiGiaoHang'];
            $recipientInfo['ghiChuKhachHang'] = $rUD['ghiChuKhachHang'] ?? '';
        }
        $rDH["recipient"] = $recipientInfo;

        // Lấy danh sách sản phẩm chi tiết trong đơn hàng
        $itemsList = [];
        $resItems = $conn->query("SELECT * FROM chitietdonhang WHERE maDH = '$mDH'");
        if ($resItems && $resItems->num_rows > 0) {
            while ($rItem = $resItems->fetch_assoc()) {
                $mSP = $rItem["maSP"];
                $idCT = (int)$rItem["id"];

                // Lấy thông tin laptop từ sanpham
                $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$mSP' LIMIT 1");
                $spInfo = ($resSP && $resSP->num_rows > 0) ? $resSP->fetch_assoc() : [];
                $rItem["tenSP"] = $spInfo["tenSP"] ?? $mSP;
                $rItem["thongSoKyThuat"] = $spInfo["thongSoKyThuat"] ?? "";
                $rItem["logo"] = $spInfo["logo"] ?? "";

                // Lấy các serial đã gán cho dòng chi tiết này
                $serials = [];
                $resSerials = $conn->query("SELECT * FROM serialsanpham WHERE idChiTietDonHang = $idCT");
                if ($resSerials && $resSerials->num_rows > 0) {
                    while ($rSer = $resSerials->fetch_assoc()) {
                        $sCode = $rSer["maSerial_IMEI"];
                        $resBH = $conn->query("SELECT * FROM phieubaohanh WHERE maSerial_IMEI = '$sCode' LIMIT 1");
                        $bhData = ($resBH && $resBH->num_rows > 0) ? $resBH->fetch_assoc() : null;
                        $rSer["baoHanh"] = $bhData;
                        $serials[] = $rSer;
                    }
                }
                $rItem["serials"] = $serials;
                $itemsList[] = $rItem;
            }
        }
        $rDH["items"] = $itemsList;
        $customerOrders[] = $rDH;
    }
}

// ----------------------------------------------------
// DỮ LIỆU TAB 2: TRA CỨU BẢO HÀNH THEO SERIAL/IMEI
// ----------------------------------------------------
$searchSerial = trim($_GET['serial'] ?? '');
if (empty($searchSerial) && $activeTab === 'warranty') {
    $searchSerial = 'SN_ASUS_F15_0001';
}

$warrantyData = null;
$warrantySerialData = null;
$warrantyProductData = null;
$warrantyCustomerData = null;
$warrantyError = '';

if (!empty($searchSerial)) {
    // 1. Tìm trong bảng phieubaohanh
    $resBH = $conn->query("SELECT * FROM phieubaohanh WHERE maSerial_IMEI = '$searchSerial'");
    if ($resBH && $resBH->num_rows > 0) {
        $warrantyData = $resBH->fetch_assoc();
    } else {
        // Kiểm tra xem Serial có trong serialsanpham không
        $chkSer = $conn->query("SELECT * FROM serialsanpham WHERE maSerial_IMEI = '$searchSerial'");
        if ($chkSer && $chkSer->num_rows > 0) {
            $rowS = $chkSer->fetch_assoc();
            if ($rowS['trangThai'] === 'Đã bán' || !empty($rowS['idChiTietDonHang'])) {
                // Tự động kích hoạt phiếu bảo hành ngay cho máy đã bán
                $autoMaPBH = kichHoatBaoHanhTuDong($conn, $searchSerial);
                if ($autoMaPBH) {
                    $resBHRetry = $conn->query("SELECT * FROM phieubaohanh WHERE maSerial_IMEI = '$searchSerial'");
                    if ($resBHRetry && $resBHRetry->num_rows > 0) {
                        $warrantyData = $resBHRetry->fetch_assoc();
                    }
                }
            } else {
                $warrantyError = "Mã Serial <strong>$searchSerial</strong> là thiết bị chính hãng TechZone hiện đang lưu kho (chưa xuất bán). Phiếu bảo hành sẽ được tự động kích hoạt ngay khi quý khách mua máy!";
            }
        } else {
            $warrantyError = "Không tìm thấy mã Serial / IMEI <strong>$searchSerial</strong> trong hệ thống. Quý khách vui lòng kiểm tra lại mã in trên thân máy hoặc trên vỏ hộp!";
        }
    }

    // Nếu đã có phiếu bảo hành, lấy thêm thông tin chi tiết
    if ($warrantyData) {
        $serVal = $warrantyData['maSerial_IMEI'];
        $resSer = $conn->query("SELECT * FROM serialsanpham WHERE maSerial_IMEI = '$serVal'");
        if ($resSer && $resSer->num_rows > 0) {
            $warrantySerialData = $resSer->fetch_assoc();
            $maSP = $warrantySerialData['maSP'];
            $resSP = $conn->query("SELECT * FROM sanpham WHERE maSP = '$maSP'");
            if ($resSP && $resSP->num_rows > 0) {
                $warrantyProductData = $resSP->fetch_assoc();
            }
        }

        $maKH = $warrantyData['maKH'];
        if (!empty($maKH)) {
            $resKH = $conn->query("SELECT * FROM khachhang WHERE maKH = '$maKH'");
            if ($resKH && $resKH->num_rows > 0) {
                $warrantyCustomerData = $resKH->fetch_assoc();
            }
        }
    }
}

// ----------------------------------------------------
// LẤY DANH SÁCH TOÀN BỘ PHIẾU BẢO HÀNH CỦA KHÁCH HÀNG HIỆN TẠI
// ----------------------------------------------------
$userWarranties = [];
if (!empty($activeMaKH)) {
    $resUserBH = $conn->query("SELECT * FROM phieubaohanh WHERE maKH = '$activeMaKH' ORDER BY ngayBatDau DESC");
    if ($resUserBH && $resUserBH->num_rows > 0) {
        while ($rUBH = $resUserBH->fetch_assoc()) {
            $sSer = $rUBH['maSerial_IMEI'];
            $resS = $conn->query("SELECT maSP FROM serialsanpham WHERE maSerial_IMEI = '$sSer' LIMIT 1");
            $mSP = ($resS && $rS = $resS->fetch_assoc()) ? $rS['maSP'] : '';
            $spName = 'Laptop Chính Hãng TechZone';
            $spLogo = '';
            $spSpecs = '';
            if (!empty($mSP)) {
                $resSPInfo = $conn->query("SELECT tenSP, logo, thongSoKyThuat FROM sanpham WHERE maSP = '$mSP' LIMIT 1");
                if ($resSPInfo && $rSPInfo = $resSPInfo->fetch_assoc()) {
                    $spName = $rSPInfo['tenSP'];
                    $spLogo = $rSPInfo['logo'];
                    $spSpecs = $rSPInfo['thongSoKyThuat'] ?? '';
                }
            }
            $rUBH['tenSP'] = $spName;
            $rUBH['logo'] = $spLogo;
            $rUBH['thongSoKyThuat'] = $spSpecs;
            $rUBH['maSP'] = $mSP;
            $userWarranties[] = $rUBH;
        }
    }
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Tra cứu đơn hàng & Bảo hành điện tử</li>
        </ol>
    </nav>

    <?= $alertMsg ?>

    <!-- Tabs chuyển đổi giữa Tra cứu đơn hàng và Tra cứu bảo hành Serial -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'order' ? 'active' : '' ?> fw-bold" id="pills-order-tab" data-bs-toggle="pill" data-bs-target="#pills-order" type="button" role="tab">
                <i class="bi bi-box-seam me-1"></i>Tra Cứu Đơn Hàng & Lịch Sử Mua Hàng
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link <?= $activeTab === 'warranty' ? 'active' : '' ?> fw-bold" id="pills-warranty-tab" data-bs-toggle="pill" data-bs-target="#pills-warranty" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i>Tra Cứu Hạn Bảo Hành Serial/IMEI
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <!-- ==============================================================
             TAB 1: TRA CỨU ĐƠN HÀNG & LIỆT KÊ ĐƠN HÀNG THEO KHÁCH HÀNG
             ============================================================== -->
        <div class="tab-pane fade <?= $activeTab === 'order' ? 'show active' : '' ?>" id="pills-order" role="tabpanel">

            <?php if (!$isLoggedIn): ?>
                <!-- THÔNG BÁO CHO KHÁCH CHƯA ĐĂNG NHẬP -->
                <div class="alert alert-primary border-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-3.5 rounded-3 shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; font-size: 20px;">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Bạn Chưa Đăng Nhập Tài Khoản</h6>
                            <div class="small text-secondary">
                                Vui lòng <strong>đăng nhập</strong> để hệ thống tự động tải toàn bộ lịch sử đơn hàng và phiếu bảo hành chính hãng của bạn, hoặc dùng ô tìm kiếm bên dưới để tra cứu nhanh.
                            </div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng Nhập Ngay
                    </a>
                </div>
            <?php endif; ?>

            <!-- THANH TÌM KIẾM ĐA NĂNG (EMAIL TÀI KHOẢN, MÃ ĐƠN HÀNG, SĐT) -->
            <div class="tz-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <h4 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                        <i class="bi bi-search text-primary me-2"></i>Tra Cứu Theo Email Đăng Ký Tài Khoản Hoặc Mã Đơn Hàng
                    </h4>
                    <?php if ($isLoggedIn): ?>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            <i class="bi bi-person-check-fill me-1"></i>Đồng bộ theo tài khoản: <?= htmlspecialchars($sessionUserName) ?> (<?= htmlspecialchars($activeMaKH) ?>)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-light text-secondary border">
                            <i class="bi bi-search me-1"></i>Tra cứu nhanh theo Email hoặc Mã đơn
                        </span>
                    <?php endif; ?>
                </div>

                <form action="tra-cuu.php" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="order">
                    <div class="col-lg-9 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="bi bi-envelope-at text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Nhập Email đăng ký tài khoản (VD: minhtuan@gmail.com) hoặc Mã đơn hàng (VD: DH7117, DH001)..." required>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-tz-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-search me-1"></i>Tìm Kiếm Đơn Hàng
                        </button>
                    </div>
                </form>

                <!-- Gợi ý từ khóa mẫu từ CSDL -->
                <div class="mt-2.5 small text-muted d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="fw-semibold">Gợi ý tra cứu nhanh:</span>
                    <a href="tra-cuu.php?tab=order&q=minhtuan@gmail.com" class="badge bg-light text-primary border text-decoration-none" title="Tra cứu theo email Đỗ Minh Tuấn">
                        <i class="bi bi-envelope me-1"></i>minhtuan@gmail.com
                    </a>
                    <a href="tra-cuu.php?tab=order&q=lananh.ng@gmail.com" class="badge bg-light text-primary border text-decoration-none" title="Tra cứu theo email Nguyễn Lan Anh">
                        <i class="bi bi-envelope me-1"></i>lananh.ng@gmail.com
                    </a>
                    <a href="tra-cuu.php?tab=order&q=DH7117" class="badge bg-light text-danger border text-decoration-none font-monospace">
                        <i class="bi bi-tag me-1"></i>DH7117
                    </a>
                    <a href="tra-cuu.php?tab=order&q=DH4458" class="badge bg-light text-success border text-decoration-none font-monospace">
                        <i class="bi bi-tag me-1"></i>DH4458
                    </a>
                </div>
            </div>

            <?php if ($sessionRole === 'admin'): ?>
                <!-- BỘ CHỌN KHÁCH HÀNG DÀNH CHO QUẢN TRỊ VIÊN (ADMIN VIEW) -->
                <div class="tz-card p-4 mb-4 border-warning shadow-sm">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-shield-lock-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 15px;">
                                    Quản Trị Viên: Tra Cứu Đơn Hàng Từng Khách Hàng
                                    <span class="badge bg-danger text-white ms-1" style="font-size: 10px;">ADMIN</span>
                                </h5>
                                <small class="text-muted" style="font-size: 12px;">Chuyển đổi giữa các khách hàng trong hệ thống để hỗ trợ kỹ thuật và kiểm tra</small>
                            </div>
                        </div>

                        <!-- Dropdown chọn khách hàng -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle fw-semibold px-3 py-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;">
                                <i class="bi bi-person-circle me-1.5"></i>
                                Khách hàng: <strong><?= htmlspecialchars($customerProfile['hoVaTen'] ?? $activeMaKH) ?></strong> (<?= htmlspecialchars($activeMaKH) ?>)
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="border-radius: 12px; min-width: 320px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header text-uppercase fw-bold" style="font-size: 11px;">Danh sách khách hàng trong CSDL:</h6></li>
                                <?php foreach ($allCustomers as $c): 
                                    $isCur = ($c['maKH'] === $activeMaKH);
                                ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between p-2 rounded-2 <?= $isCur ? 'active bg-primary' : '' ?>" href="tra-cuu.php?tab=order&kh=<?= urlencode($c['maKH']) ?>">
                                            <div>
                                                <div class="fw-bold <?= $isCur ? 'text-white' : 'text-dark' ?>" style="font-size: 13px;">
                                                    <?= htmlspecialchars($c['hoVaTen']) ?>
                                                </div>
                                                <small class="<?= $isCur ? 'text-white-50' : 'text-muted' ?>" style="font-size: 11px;">
                                                    Mã: <?= htmlspecialchars($c['maKH']) ?>
                                                    <?= !empty($c['emailTK']) ? ' • ' . htmlspecialchars($c['emailTK']) : (!empty($c['email']) ? ' • ' . htmlspecialchars($c['email']) : '') ?>
                                                </small>
                                            </div>
                                            <span class="badge <?= $isCur ? 'bg-white text-primary' : ($c['soDonHang'] > 0 ? 'bg-success' : 'bg-light text-secondary border') ?> rounded-pill ms-2">
                                                <?= $c['soDonHang'] ?> đơn
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Thanh Pills chuyển nhanh khách hàng 1 chạm -->
                    <div class="d-flex align-items-center gap-2 flex-wrap pt-2 border-top">
                        <span class="text-secondary small fw-semibold" style="font-size: 11.5px;">Chọn nhanh:</span>
                        <?php foreach ($allCustomers as $c): 
                            $isCur = ($c['maKH'] === $activeMaKH);
                        ?>
                            <a href="tra-cuu.php?tab=order&kh=<?= urlencode($c['maKH']) ?>" 
                               class="btn btn-sm <?= $isCur ? 'btn-primary' : 'btn-light border' ?> rounded-pill py-1 px-3 d-inline-flex align-items-center gap-1.5"
                               style="font-size: 12px; transition: all 0.2s;">
                                <i class="bi bi-person-fill"></i>
                                <span><?= htmlspecialchars($c['hoVaTen']) ?></span>
                                <?php if ($c['soDonHang'] > 0): ?>
                                    <span class="badge <?= $isCur ? 'bg-white text-primary' : 'bg-success text-white' ?> rounded-pill" style="font-size: 10px;">
                                        <?= $c['soDonHang'] ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- THÔNG BÁO KẾT QUẢ TÌM KIẾM (NẾU CÓ) -->
            <?php if (!empty($searchStatusMessage)): ?>
                <div class="alert alert-<?= $searchStatusType ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi <?= ($searchStatusType === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> fs-5"></i>
                    <div><?= $searchStatusMessage ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- THẺ HỒ SƠ KHÁCH HÀNG ĐANG XEM (CUSTOMER PROFILE BANNER) -->
            <?php if ($customerProfile): ?>
                <div class="p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%); border: 1.5px solid #bfdbfe;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;"><?= htmlspecialchars($customerProfile['hoVaTen']) ?></h5>
                                <span class="badge bg-primary text-white font-monospace">Mã KH: <?= htmlspecialchars($customerProfile['maKH']) ?></span>
                                <?php if ($accountProfile): ?>
                                    <span class="badge bg-info text-white font-monospace">
                                        <i class="bi bi-person-check me-1"></i>Tài khoản: <?= htmlspecialchars($accountProfile['tenTaiKhoan']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted mt-1 d-flex align-items-center gap-3 flex-wrap">
                                <span>
                                    <i class="bi bi-envelope text-primary me-1"></i>Email: 
                                    <strong><?= htmlspecialchars($accountProfile['email'] ?? $customerProfile['email'] ?? 'Chưa cập nhật') ?></strong>
                                </span>
                                <span>
                                    <i class="bi bi-telephone text-success me-1"></i>SĐT: 
                                    <strong><?= htmlspecialchars($customerProfile['soDienThoai'] ?: 'Chưa cập nhật') ?></strong>
                                </span>
                                <?php if (!empty($customerProfile['diaChi'])): ?>
                                    <span>
                                        <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($customerProfile['diaChi']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            <i class="bi bi-bag-check-fill me-1"></i>Tổng: <?= count($customerOrders) ?> đơn hàng
                        </span>
                        <?php if (!empty($searchSingleOrder)): ?>
                            <div class="mt-1">
                                <a href="tra-cuu.php?tab=order&kh=<?= urlencode($activeMaKH) ?>" class="small text-decoration-none">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Xem tất cả đơn của khách này
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ==============================================================
                 DANH SÁCH CÁC ĐƠN HÀNG CỦA KHÁCH HÀNG
                 ============================================================== -->
            <?php if (!empty($customerOrders)): ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                        <i class="bi bi-list-check text-primary me-2"></i>
                        <?= !empty($searchSingleOrder) ? "Chi Tiết Đơn Hàng #$searchSingleOrder" : "Lịch Sử Đơn Hàng Của Khách Hàng (" . count($customerOrders) . " đơn)" ?>
                    </h5>
                    <span class="text-muted small">Sắp xếp theo đơn mới nhất</span>
                </div>

                <div class="d-flex flex-column gap-4 mb-4">
                    <?php foreach ($customerOrders as $order): 
                        $status = $order['trangThaiDon'] ?? 'CHO_DUYET';
                        $canCancel = ($status === 'CHO_DUYET' || $status === 'Tiếp nhận');
                        $isSingleMatched = (!empty($searchSingleOrder) && $order['maDH'] === $searchSingleOrder);
                    ?>
                        <div class="tz-card p-4 <?= $isSingleMatched ? 'border-primary shadow-sm' : '' ?>" style="border-radius: 14px;">
                            <!-- Header của từng đơn hàng -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold font-monospace text-primary fs-5">#<?= htmlspecialchars($order['maDH']) ?></span>
                                    
                                    <!-- Badge trạng thái -->
                                    <?php if ($status === 'CHO_DUYET' || $status === 'Tiếp nhận'): ?>
                                        <span class="badge bg-warning text-dark px-2.5 py-1">
                                            <i class="bi bi-clock-history me-1"></i>Đang tiếp nhận (Chờ duyệt)
                                        </span>
                                    <?php elseif ($status === 'CHO_THANH_TOAN'): ?>
                                        <span class="badge bg-secondary text-white px-2.5 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán
                                        </span>
                                    <?php elseif ($status === 'DANG_GIAO'): ?>
                                        <span class="badge bg-info text-white px-2.5 py-1">
                                            <i class="bi bi-truck me-1"></i>Đang giao hàng
                                        </span>
                                    <?php elseif ($status === 'HOAN_THANH'): ?>
                                        <span class="badge bg-success text-white px-2.5 py-1">
                                            <i class="bi bi-check2-all me-1"></i>Giao hàng thành công
                                        </span>
                                    <?php elseif ($status === 'DA_HUY'): ?>
                                        <span class="badge bg-danger text-white px-2.5 py-1">
                                            <i class="bi bi-x-circle me-1"></i>Đơn hàng đã hủy
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary text-white px-2.5 py-1"><?= htmlspecialchars($status) ?></span>
                                    <?php endif; ?>

                                    <span class="text-muted small ms-2">
                                        <i class="bi bi-calendar3 me-1"></i>Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['ngayTao'])) ?></strong>
                                    </span>
                                </div>

                                <!-- Nút thao tác đơn hàng -->
                                <div>
                                    <?php if ($canCancel): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="openCancelModal('<?= htmlspecialchars($order['maDH']) ?>')">
                                            <i class="bi bi-x-circle me-1"></i>Yêu Cầu Hủy Đơn
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border py-1.5 px-2.5">
                                            <i class="bi bi-lock me-1"></i>Đã xuất kho / Không thể hủy
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Thông tin người nhận hàng -->
                            <div class="row g-2 p-2.5 bg-light rounded-3 mb-3 small">
                                <div class="col-md-4">
                                    <span class="text-muted">Người nhận:</span>
                                    <strong class="text-dark ms-1"><?= htmlspecialchars($order['recipient']['tenNguoiNhan']) ?></strong>
                                    <?php if (!empty($order['recipient']['sdtNhanHang'])): ?>
                                        <span class="text-muted font-monospace">(<?= htmlspecialchars($order['recipient']['sdtNhanHang']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5">
                                    <span class="text-muted">Địa chỉ nhận:</span>
                                    <span class="text-dark ms-1"><?= htmlspecialchars($order['recipient']['diaChiGiaoHang'] ?: 'Nhận tại cửa hàng') ?></span>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <span class="text-muted">Hình thức:</span>
                                    <strong class="text-primary ms-1"><?= htmlspecialchars($order['phuongThucThanhToan'] ?? 'COD') ?></strong>
                                </div>
                                <?php if (!empty($order['recipient']['ghiChuKhachHang'])): ?>
                                    <div class="col-12 text-muted fst-italic pt-1 border-top" style="font-size: 11.5px;">
                                        <i class="bi bi-chat-left-text me-1"></i>Ghi chú: <?= htmlspecialchars($order['recipient']['ghiChuKhachHang']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Bảng sản phẩm trong đơn hàng -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SẢN PHẨM LAPTOP</th>
                                            <th class="text-center" style="width: 80px;">SL</th>
                                            <th class="text-end" style="width: 140px;">ĐƠN GIÁ</th>
                                            <th class="text-end" style="width: 140px;">THÀNH TIỀN</th>
                                            <th>SERIAL / IMEI & BẢO HÀNH CHÍNH HÃNG</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($order['items'])): ?>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2.5">
                                                            <?php if (!empty($item['logo'])): ?>
                                                                <img src="<?= htmlspecialchars($item['logo']) ?>" alt="<?= htmlspecialchars($item['tenSP']) ?>" style="width: 48px; height: 48px; object-fit: contain;" class="rounded border p-1 bg-white">
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="fw-bold text-dark"><?= htmlspecialchars($item['tenSP']) ?></div>
                                                                <small class="text-muted font-monospace"><?= htmlspecialchars($item['maSP']) ?> • <?= htmlspecialchars($item['thongSoKyThuat']) ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center fw-bold"><?= $item['soLuong'] ?></td>
                                                    <td class="text-end"><?= number_format((float)$item['donGia'], 0, ',', '.') ?> ₫</td>
                                                    <td class="text-end fw-bold text-danger"><?= number_format((float)$item['thanhTien'], 0, ',', '.') ?> ₫</td>
                                                    <td>
                                                        <?php if (!empty($item['serials'])): ?>
                                                            <?php foreach ($item['serials'] as $ser): 
                                                                $bh = $ser['baoHanh'];
                                                            ?>
                                                                <div class="p-2 border rounded-2 mb-1.5 bg-light">
                                                                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                                                        <span class="badge bg-light text-danger border font-monospace py-1 px-2">
                                                                            <i class="bi bi-upc-scan me-1"></i>Serial: <strong><?= htmlspecialchars($ser['maSerial_IMEI']) ?></strong>
                                                                        </span>
                                                                        <a href="tra-cuu.php?tab=warranty&serial=<?= urlencode($ser['maSerial_IMEI']) ?>" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size: 11px;">
                                                                            <i class="bi bi-shield-check me-1"></i>Xem bảo hành
                                                                        </a>
                                                                    </div>
                                                                    <?php if ($bh): 
                                                                        $isExpired = (strtotime($bh['ngayKetThuc']) < time());
                                                                        $diff = (int)round((strtotime($bh['ngayKetThuc']) - time()) / 86400);
                                                                    ?>
                                                                        <div class="small <?= $isExpired ? 'text-danger' : 'text-success' ?> mt-1" style="font-size: 11px;">
                                                                            <i class="bi bi-patch-check-fill me-1"></i>Phiếu: <strong><?= htmlspecialchars($bh['maPhieuBH']) ?></strong> • Hạn: <strong><?= date('d/m/Y', strtotime($bh['ngayKetThuc'])) ?></strong> (<?= $diff > 0 ? "Còn $diff ngày" : "Hết hạn" ?>)
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="small text-muted mt-1" style="font-size: 11px;">
                                                                            <i class="bi bi-info-circle me-1"></i>Đã xuất bán, phiếu bảo hành đang đồng bộ.
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted small" style="font-size: 11.5px;">
                                                                <i class="bi bi-hourglass-split me-1 text-warning"></i>Đang chờ kho xuất và gán Serial/IMEI máy
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-2">Không có chi tiết sản phẩm.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer thanh toán đơn hàng -->
                            <div class="p-3 bg-light rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="small text-muted">
                                    <span>Kênh bán: <strong><?= htmlspecialchars($order['kenhBan'] ?? 'ONLINE') ?></strong></span>
                                    <span class="mx-2">•</span>
                                    <span>Phí giao hàng: <strong><?= number_format((float)$order['phiVanChuyen'], 0, ',', '.') ?> ₫</strong></span>
                                    <?php if ((float)$order['tienGiamGia'] > 0): ?>
                                        <span class="mx-2">•</span>
                                        <span>Giảm giá: <strong class="text-success">-<?= number_format((float)$order['tienGiamGia'], 0, ',', '.') ?> ₫</strong></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-dark small fw-semibold">Tổng thanh toán:</span>
                                    <strong class="text-danger fs-5 font-monospace"><?= number_format((float)$order['tongThanhToan'], 0, ',', '.') ?> ₫</strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Khách hàng chưa có đơn hàng nào -->
                <div class="tz-card p-5 text-center mb-4">
                    <i class="bi bi-inbox text-muted display-4 mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Khách Hàng <?= htmlspecialchars($customerProfile['hoVaTen'] ?? $activeMaKH) ?> Chưa Có Đơn Hàng Nào</h5>
                    <p class="text-muted small mb-3">Hiện tại chưa có đơn hàng nào được ghi nhận cho tài khoản này trong hệ thống.</p>
                    <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-tz-primary px-4">
                        <i class="bi bi-laptop me-1"></i>Khám Phá Laptop Mới Ngay
                    </a>
                </div>
            <?php endif; ?>
        </div>


        <!-- ==============================================================
             TAB 2: TRA CỨU BẢO HÀNH THEO SERIAL/IMEI THỰC TẾ
             ============================================================== -->
        <div class="tab-pane fade <?= $activeTab === 'warranty' ? 'show active' : '' ?>" id="pills-warranty" role="tabpanel">

            <?php if ($isLoggedIn && !empty($userWarranties)): ?>
                <!-- DANH SÁCH THIẾT BỊ ĐÃ KÍCH HOẠT BẢO HÀNH CỦA KHÁCH HÀNG -->
                <div class="tz-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 15.5px;">Thiết Bị Laptop Bảo Hành Của Quý Khách</h5>
                                <small class="text-muted" style="font-size: 12px;">Được tự động kích hoạt bảo hành chính hãng ngay khi xuất bán</small>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill font-monospace" style="font-size: 12px;">
                            <i class="bi bi-patch-check-fill me-1"></i><?= count($userWarranties) ?> thiết bị đã kích hoạt
                        </span>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($userWarranties as $uw): 
                            $uNgayKT = $uw['ngayKetThuc'];
                            $uIsConHan = (strtotime($uNgayKT) >= time());
                            $uDiffDays = (int)round((strtotime($uNgayKT) - time()) / 86400);
                            $isSelected = ($searchSerial === $uw['maSerial_IMEI']);
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border <?= $isSelected ? 'border-primary shadow-sm' : '' ?> rounded-3 p-3 transition" style="background-color: <?= $isSelected ? '#f0f9ff' : '#ffffff' ?>;">
                                    <div class="d-flex align-items-center gap-3 mb-2.5">
                                        <?php if (!empty($uw['logo'])): ?>
                                            <img src="<?= htmlspecialchars($uw['logo']) ?>" style="width: 48px; height: 48px; object-fit: contain;" class="rounded border p-1 bg-white">
                                        <?php else: ?>
                                            <div class="rounded bg-light p-2 text-primary fs-3"><i class="bi bi-laptop"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark small" style="line-height: 1.3;"><?= htmlspecialchars($uw['tenSP']) ?></div>
                                            <span class="badge bg-light text-danger border font-monospace mt-1" style="font-size: 11px;">
                                                <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($uw['maSerial_IMEI']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="small text-muted mb-2 font-monospace" style="font-size: 11.5px;">
                                        <div>Mã PBH: <strong class="text-primary"><?= htmlspecialchars($uw['maPhieuBH']) ?></strong></div>
                                        <div>Hạn BH: <strong><?= date('d/m/Y', strtotime($uw['ngayKetThuc'])) ?></strong></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                        <?php if ($uIsConHan): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 11px;">
                                                <i class="bi bi-check-circle me-1"></i>Còn <?= $uDiffDays ?> ngày
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2" style="font-size: 11px;">
                                                <i class="bi bi-x-circle me-1"></i>Hết hạn
                                            </span>
                                        <?php endif; ?>
                                        <a href="tra-cuu.php?tab=warranty&serial=<?= urlencode($uw['maSerial_IMEI']) ?>" class="btn btn-sm <?= $isSelected ? 'btn-primary' : 'btn-outline-primary' ?> py-0.5 px-2.5" style="font-size: 11.5px;">
                                            Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="tz-card p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                    <i class="bi bi-shield-check text-success me-2"></i>Tra Cứu Hạn Bảo Hành Điện Tử Theo Serial / IMEI
                </h4>
                <form action="tra-cuu.php" method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="tab" value="warranty">
                    <div class="col-md-9">
                        <label class="form-label small fw-semibold">Nhập mã Serial / IMEI của máy laptop <span class="text-danger">*</span></label>
                        <input type="text" name="serial" class="form-control font-monospace" placeholder="VD: SN_ASUS_F15_0001, SN_LENOVO_LOQ_0001, SN_DELL_3520_0001..." value="<?= htmlspecialchars($searchSerial) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="bi bi-search me-1"></i>Kiểm Tra Bảo Hành
                        </button>
                    </div>
                </form>
                <div class="mt-2.5 small text-muted">
                    Mã Serial máy đã bán mẫu:
                    <a href="tra-cuu.php?tab=warranty&serial=SN_ASUS_F15_0001" class="badge bg-light text-primary border text-decoration-none me-1">SN_ASUS_F15_0001 (ASUS TUF - 24T)</a>
                    <a href="tra-cuu.php?tab=warranty&serial=SN_LENOVO_LOQ_0001" class="badge bg-light text-info border text-decoration-none me-1">SN_LENOVO_LOQ_0001 (Lenovo LOQ - 24T)</a>
                    <a href="tra-cuu.php?tab=warranty&serial=SN_DELL_3520_0001" class="badge bg-light text-success border text-decoration-none">SN_DELL_3520_0001 (Dell ProSupport - 12T)</a>
                </div>
            </div>

            <?php if (!empty($warrantyError)): ?>
                <div class="alert alert-warning border-warning-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4 mt-0.5"></i>
                    <div>
                        <div class="fw-bold mb-1">Thông Báo Tra Cứu:</div>
                        <div class="small"><?= $warrantyError ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($warrantyData):
                $ngayBD = $warrantyData['ngayBatDau'];
                $ngayKT = $warrantyData['ngayKetThuc'];
                $isConHan = (strtotime($ngayKT) >= time());
                $diffDays = (int)round((strtotime($ngayKT) - time()) / 86400);

                $tenSP = $warrantyProductData['tenSP'] ?? 'Laptop Chính Hãng TechZone';
                $logoSP = $warrantyProductData['logo'] ?? '';
                $thongSoSP = $warrantyProductData['thongSoKyThuat'] ?? '';
                $thoiGianBH = $warrantyProductData['thoiGianBaoHanh'] ?? 24;

                $tenKH = $warrantyCustomerData['hoVaTen'] ?? 'Khách Hàng TechZone';
                $sdtKH = $warrantyCustomerData['soDienThoai'] ?? '';
                $maskedPhone = '';
                if (!empty($sdtKH) && strlen($sdtKH) >= 7) {
                    $maskedPhone = substr($sdtKH, 0, 4) . '***' . substr($sdtKH, -3);
                } else {
                    $maskedPhone = $sdtKH;
                }
            ?>
                <!-- Khung kết quả bảo hành từ CSDL thật -->
                <div class="tz-card p-4">
                    <?php if ($isConHan): ?>
                        <div class="d-flex align-items-center gap-3 p-3 bg-success-subtle text-success border border-success-subtle rounded-3 mb-4">
                            <i class="bi bi-patch-check-fill fs-1 text-success"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Thiết Bị Đang Trong Thời Hạn Bảo Hành Hợp Lệ (Còn <?= $diffDays ?> ngày)</h5>
                                <p class="mb-0 small">Thiết bị được kích hoạt bảo hành chính hãng tự động ngay khi mua máy, hỗ trợ 100% phần cứng tại hệ thống TechZone trên toàn quốc.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-3 p-3 bg-danger-subtle text-danger border border-danger-subtle rounded-3 mb-4">
                            <i class="bi bi-clock-history fs-1 text-danger"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Thiết Bị Đã Hết Thời Hạn Bảo Hành Chính Hãng</h5>
                                <p class="mb-0 small">Thiết bị đã quá thời gian bảo hành tiêu chuẩn <?= abs($diffDays) ?> ngày. Quý khách vẫn được hỗ trợ sửa chữa linh kiện chính hãng ưu đãi tại trung tâm TechZone.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4 align-items-center pb-3 mb-3 border-bottom">
                        <div class="col-md-2 text-center">
                            <?php if (!empty($logoSP)): ?>
                                <img src="<?= htmlspecialchars($logoSP) ?>" alt="<?= htmlspecialchars($tenSP) ?>" style="max-height: 110px; max-width: 100%; object-fit: contain;">
                            <?php else: ?>
                                <i class="bi bi-laptop text-primary display-4"></i>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-10">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">BẢO HÀNH CHÍNH HÃNG</span>
                            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($tenSP) ?></h4>
                            <p class="text-muted small mb-0 font-monospace"><?= htmlspecialchars($thongSoSP) ?></p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Mã Serial / IMEI Thiết Bị:</div>
                                <div class="font-monospace fw-bold fs-6 text-danger"><?= htmlspecialchars($warrantyData['maSerial_IMEI']) ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Mã Phiếu Bảo Hành Điện Tử:</div>
                                <div class="font-monospace fw-bold fs-6 text-primary"><?= htmlspecialchars($warrantyData['maPhieuBH']) ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Khách Hàng Sở Hữu:</div>
                                <div class="fw-bold text-dark">
                                    <?= htmlspecialchars($tenKH) ?>
                                    <?php if (!empty($maskedPhone)): ?>
                                        <span class="text-muted font-monospace small ms-1">(<?= htmlspecialchars($maskedPhone) ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Thời Gian Bảo Hành Tiêu Chuẩn:</div>
                                <div class="fw-bold text-dark"><?= $thoiGianBH ?> Tháng chính hãng</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Ngày Mua & Tự Động Kích Hoạt:</div>
                                <div class="fw-semibold text-dark"><i class="bi bi-calendar-check text-primary me-1"></i><?= date('d/m/Y H:i', strtotime($ngayBD)) ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Thời Hạn Kết Thúc Bảo Hành:</div>
                                <div class="fw-bold <?= $isConHan ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-shield-check me-1"></i><?= date('d/m/Y H:i', strtotime($ngayKT)) ?>
                                    <span class="badge <?= $isConHan ? 'bg-success' : 'bg-danger' ?> text-white ms-2">
                                        <?= $isConHan ? "Còn $diffDays ngày" : "Hết hạn" ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small mb-1">Chính Sách & Quyền Lợi Bảo Hành:</div>
                                <div class="text-dark small">
                                    <?= htmlspecialchars($warrantyData['moTaChinhSach'] ?? 'Bảo hành chính hãng phần cứng 100% tại trung tâm bảo hành toàn quốc.') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL HỦY ĐƠN HÀNG DÀNH CHO KHÁCH HÀNG (ĐA ĐƠN HÀNG) -->
<div class="modal fade" id="modalUserCancelOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-x-circle-fill me-2"></i>Hủy Đơn Hàng (<span id="cancelMaDHText">DH...</span>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="tra-cuu.php?tab=order<?= !empty($activeMaKH) ? '&kh=' . urlencode($activeMaKH) : '' ?>" method="POST">
                <input type="hidden" name="action" value="user_cancel_order">
                <input type="hidden" name="maDH" id="cancelMaDHInput" value="">
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Đơn hàng của bạn đang ở trạng thái <strong>Tiếp nhận</strong> (Chưa xuất kho). Bạn có quyền yêu cầu hủy đơn mà không mất bất kỳ khoản phí nào.</p>

                    <label class="form-label small fw-semibold">Vui lòng cho biết lý do bạn muốn hủy đơn <span class="text-danger">*</span></label>
                    <textarea name="lyDo" class="form-control" rows="3" placeholder="VD: Tôi muốn đổi sang mẫu máy khác, tôi bận không nhận hàng được..." required></textarea>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Giữ lại đơn hàng</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Xác Nhận Hủy Đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(maDH) {
    document.getElementById('cancelMaDHInput').value = maDH;
    document.getElementById('cancelMaDHText').textContent = maDH;
    const modal = new bootstrap.Modal(document.getElementById('modalUserCancelOrder'));
    modal.show();
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
