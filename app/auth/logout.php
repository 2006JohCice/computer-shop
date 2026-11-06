<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_unset();
session_destroy();
header("Location: /computer-shop/public/client/index.php");
exit();
