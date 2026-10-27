<?php
/**
 * ==============================================================================
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: public/index.php
 * MÔ TẢ: Front Controller - Tự động tải Trang Chủ Mua Sắm Khách Hàng (User Storefront)
 * ==============================================================================
 */

// Bắt đầu session nếu có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp file cấu hình chung và CSDL
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';

// Tải giao diện Trang Chủ Mua Sắm Khách Hàng (User Storefront)
require_once dirname(__DIR__) . '/app/views/client/home/index.php';
