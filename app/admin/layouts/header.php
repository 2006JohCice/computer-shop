<?php
// app/views/admin/layouts/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra session đăng nhập cơ bản cho Admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: /computer-shop/public/login.php");
    exit();
}

// 2. Phân quyền truy cập: Khách hàng thường (role = user) không được vào khu vực quản trị
$userRole = strtolower(trim($_SESSION['role'] ?? 'user'));
if ($userRole === 'user') {
    header("Location: /computer-shop/public/client/index.php");
    exit();
}

$configFile = dirname(__DIR__, 2) . '/config/config.php';
if (!file_exists($configFile)) {
    $configFile = dirname(__DIR__, 3) . '/config/config.php';
}
if (file_exists($configFile)) {
    require_once $configFile;
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'TECHZONE - Quản Trị Hệ Thống');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/computer-shop/public');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= APP_NAME ?></title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Thay thế hoàn toàn Emoji) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Admin Design System Style (Tách riêng trong public/css/admin.css) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css?v=<?= time() ?>">
    <!-- Global Notification & Dialog System -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/techzone-notify.css?v=<?= time() ?>">

    <!-- Style thanh cuộn siêu mỏng đẹp tinh tế (Custom Modern Scrollbar) -->
    <style>
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
            transition: background-color 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        } */
    </style>
</head>

<body>