<?php

/**
 * ==============================================================================
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP
 * FILE: public/login.php
 * MÔ TẢ: Điểm truy cập trực tiếp trang đăng nhập / đăng ký TechZone
 * ==============================================================================
 */

// Nạp file cấu hình chung và CSDL
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';

// Nạp View Đăng Nhập / Đăng Ký Dùng Chung Duy Nhất
require_once dirname(__DIR__) . '/app/auth/login.php';
require_once dirname(__DIR__) . '/app/auth/logout.php';
