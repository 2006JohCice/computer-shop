<?php
// app/auth/logout.php - Đăng xuất hệ thống cơ bản bằng session_start() và session_destroy()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_unset();
session_destroy();

header("Location: /computer-shop/public/login.php");
exit();
