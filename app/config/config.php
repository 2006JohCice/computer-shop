<?php


date_default_timezone_set('Asia/Ho_Chi_Minh');

define('APP_ROOT', dirname(dirname(__DIR__)));
define('APP_PATH', APP_ROOT . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', APP_ROOT . DIRECTORY_SEPARATOR . 'public');

define('BASE_URL', 'http://localhost/computer-shop/public');


define('APP_NAME', 'Hệ Thống Quản Lý Cửa Hàng Bán Laptop');
define('APP_VERSION', '1.0.0');
define('CURRENCY_UNIT', '₫');


define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
