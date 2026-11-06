<?php
// app/views/client/layouts/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'TECHZONE - Thế Giới Laptop Chính Hãng');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/computer-shop/public');
}

// Nạp thông tin sản phẩm từ CSDL vào TECHZONE_CATALOG toàn cục
$globalCatalog = [];
$sessionMaKH = '';
$globalCartCount = 0;

if (file_exists(dirname(__DIR__, 2) . '/config/database.php')) {
    include_once dirname(__DIR__, 2) . '/config/database.php';
    $hdrConn = @new mysqli($servername, $username, $password, $dbname);
    if (!$hdrConn->connect_error) {
        $hdrConn->set_charset("utf8mb4");

        // Lấy mã khách hàng & số lượng giỏ hàng của tài khoản đang đăng nhập
        if (isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk'])) {
            $sMaTk = $hdrConn->real_escape_string($_SESSION['maTk']);
            $rKH = $hdrConn->query("SELECT maKH FROM khachhang WHERE maTk = '$sMaTk' LIMIT 1");
            if ($rKH && $rowKH = $rKH->fetch_assoc()) {
                $sessionMaKH = $rowKH['maKH'];
            } else {
                $rAcc = $hdrConn->query("SELECT email FROM accounts WHERE maTk = '$sMaTk' LIMIT 1");
                if ($rAcc && $rowAcc = $rAcc->fetch_assoc()) {
                    $emAcc = $hdrConn->real_escape_string($rowAcc['email'] ?? '');
                    if (!empty($emAcc)) {
                        $rKHEm = $hdrConn->query("SELECT maKH FROM khachhang WHERE email = '$emAcc' LIMIT 1");
                        if ($rKHEm && $rowKHEm = $rKHEm->fetch_assoc()) {
                            $sessionMaKH = $rowKHEm['maKH'];
                            $hdrConn->query("UPDATE khachhang SET maTk = '$sMaTk' WHERE maKH = '$sessionMaKH'");
                        }
                    }
                }
            }

            if (!empty($sessionMaKH)) {
                $rCnt = $hdrConn->query("SELECT SUM(soLuong) as total FROM giohang WHERE maKH = '$sessionMaKH'");
                if ($rCnt && $rowC = $rCnt->fetch_assoc()) {
                    $globalCartCount = (int)($rowC['total'] ?? 0);
                }
            }
        }

        $hdrRes = $hdrConn->query("SELECT maSP, tenSP, giaBan, soLuongTon, maTH, thongSoKyThuat FROM sanpham WHERE isDeleted = 0");
        if ($hdrRes) {
            while ($sp = $hdrRes->fetch_assoc()) {
                $upperBrand = strtoupper($sp['maTH'] ?? '');
                $upperName = strtoupper($sp['tenSP'] ?? '');
                $img = 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80';
                if (strpos($upperBrand, 'ASUS') !== false || strpos($upperName, 'ASUS') !== false || strpos($upperName, 'TUF') !== false) {
                    $img = 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&auto=format&fit=crop&q=80';
                } elseif (strpos($upperBrand, 'APPLE') !== false || strpos($upperName, 'MACBOOK') !== false || strpos($upperName, 'APPLE') !== false) {
                    $img = 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80';
                } elseif (strpos($upperBrand, 'LENOVO') !== false || strpos($upperName, 'THINKPAD') !== false || strpos($upperName, 'LOQ') !== false) {
                    $img = 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=80';
                } elseif (strpos($upperBrand, 'DELL') !== false || strpos($upperName, 'DELL') !== false || strpos($upperName, 'INSPIRON') !== false || strpos($upperName, 'XPS') !== false) {
                    $img = 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80';
                } elseif (strpos($upperBrand, 'MSI') !== false || strpos($upperName, 'MSI') !== false) {
                    $img = 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500&auto=format&fit=crop&q=80';
                }
                $globalCatalog[$sp['maSP']] = [
                    'maSP' => $sp['maSP'],
                    'tenSP' => $sp['tenSP'],
                    'giaBan' => (float)$sp['giaBan'],
                    'soLuongTon' => (int)($sp['soLuongTon'] ?? 0),
                    'img' => $img,
                    'thongSo' => $sp['thongSoKyThuat'] ?? '',
                    'thuongHieu' => $sp['maTH']
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= APP_NAME ?></title>

    <!-- Google Fonts: Plus Jakarta Sans & Rubik -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Rubik:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Chuẩn UI/UX Pro Max: 100% SVG/Icons, No Emojis) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Client Custom Design System Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/client.css?v=<?= time() ?>">
    <!-- Global Notification & Dialog System -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/techzone-notify.css?v=<?= time() ?>">
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        window.TECHZONE_CATALOG = <?= json_encode($globalCatalog, JSON_UNESCAPED_UNICODE) ?>;
        window.CURRENT_MAKH = '<?= $sessionMaKH ?>';
        window.IS_LOGGED_IN = <?= (isset($_SESSION['login']) && $_SESSION['login'] === true) ? 'true' : 'false' ?>;
        window.SESSION_ROLE = '<?= htmlspecialchars($_SESSION['role'] ?? '') ?>';
    </script>
</head>

<body>
    <!-- Topbar Thông Báo & Liên kết Nhanh -->
    <div class="tz-client-topbar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-shield-check text-primary me-1"></i>Cam kết 100% Laptop Chính Hãng - Bảo hành theo Serial/IMEI</span>
                <span class="text-white-50">•</span>
                <span><i class="bi bi-truck text-warning me-1"></i>Miễn phí giao hàng nội thành cho hóa đơn từ 10.000.000đ</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>/client/tra-cuu.php"><i class="bi bi-search me-1"></i>Tra cứu Đơn & Bảo hành</a>
                <span class="text-white-50">|</span>
                <a href="/computer-shop/app/admin/dashboard/dashboard.php" class="text-warning fw-semibold"><i class="bi bi-speedometer2 me-1"></i>Admin Portal</a>
                <span class="text-white-50">|</span>
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <span class="text-light font-monospace small"><i class="bi bi-person-check-fill text-info me-1"></i><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                    <a href="/computer-shop/app/auth/logout.php" class="text-danger small ms-1" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i></a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php"><i class="bi bi-person me-1"></i>Đăng nhập / Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
