<?php
// app/views/client/layouts/header.php
if (!defined('APP_NAME')) {
    define('APP_NAME', 'TECHZONE - Thế Giới Laptop Chính Hãng');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/computer-shop/public');
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
                <a href="<?= BASE_URL ?>/login.php"><i class="bi bi-person me-1"></i>Đăng nhập / Đăng ký</a>
            </div>
        </div>
    </div>
