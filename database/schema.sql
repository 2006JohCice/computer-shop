-- ========================================================
-- CƠ SỞ DỮ LIỆU: QUẢN LÝ CỬA HÀNG BÁN LAPTOP
-- Dùng cho Web PHP (MySQL / MariaDB / phpMyAdmin)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `quan_ly_cua_hang_laptop`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `quan_ly_cua_hang_laptop`;

-- 1. Bảng Nhà Cung Cấp (NhaCungCap)
CREATE TABLE IF NOT EXISTS `nha_cung_cap` (
    `maNCC` VARCHAR(50) NOT NULL PRIMARY KEY,
    `tenCongTy` VARCHAR(255) NOT NULL,
    `soDienThoai` VARCHAR(20),
    `diaChiTruSo` VARCHAR(255),
    `quocTich` VARCHAR(100) DEFAULT 'Việt Nam',
    `maSoThue` VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Bảng Danh Mục (DanhMuc)
CREATE TABLE IF NOT EXISTS `danh_muc` (
    `maDM` INT AUTO_INCREMENT PRIMARY KEY,
    `tenDM` VARCHAR(150) NOT NULL,
    `moTa` TEXT,
    `trangThai` INT DEFAULT 1 -- 1: Đang hiển thị, 0: Khóa/Ẩn
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Bảng Thương Hiệu (ThuongHieu)
CREATE TABLE IF NOT EXISTS `thuong_hieu` (
    `maTH` VARCHAR(50) NOT NULL PRIMARY KEY,
    `tenTH` VARCHAR(150) NOT NULL,
    `moTa` TEXT,
    `trangThai` INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Bảng Khuyến Mãi (KhuyenMai)
CREATE TABLE IF NOT EXISTS `khuyen_mai` (
    `maKM` VARCHAR(50) NOT NULL PRIMARY KEY,
    `tenKM` VARCHAR(200) NOT NULL,
    `phanTramGiam` DOUBLE DEFAULT 0.0,
    `ngayBatDau` DATETIME NOT NULL,
    `ngayKetThuc` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Bảng Khách Hàng (KhachHang)
CREATE TABLE IF NOT EXISTS `khach_hang` (
    `maKH` VARCHAR(50) NOT NULL PRIMARY KEY,
    `hoVaTen` VARCHAR(150) NOT NULL,
    `soDienThoai` VARCHAR(20) NOT NULL,
    `email` VARCHAR(150),
    `diaChi` VARCHAR(255),
    `doiTuongUuTien` INT DEFAULT 0 -- 0: Tiêu chuẩn, 1: Bạc, 2: Vàng, 3: VIP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Bảng Sản Phẩm (SanPham)
CREATE TABLE IF NOT EXISTS `san_pham` (
    `maSP` VARCHAR(50) NOT NULL PRIMARY KEY,
    `tenSP` VARCHAR(255) NOT NULL,
    `giaBan` DOUBLE NOT NULL DEFAULT 0,
    `soLuongTon` INT NOT NULL DEFAULT 0,
    `maTH` VARCHAR(50) NOT NULL,
    `maDM` INT NOT NULL,
    `thoiGianBaoHanh` INT DEFAULT 12, -- Số tháng bảo hành (vd: 12, 24, 36)
    `thongSoKyThuat` TEXT,            -- CPU, RAM, SSD, GPU, Màn hình...
    `moTa` TEXT,
    `viTriKho` VARCHAR(100),          -- Kệ A1, Dãy 2...
    `trangThai` INT DEFAULT 1,        -- 1: Đang kinh doanh, 0: Ngừng bán
    `isDeleted` INT DEFAULT 0,        -- 0: Chưa xóa, 1: Đã xóa mềm
    CONSTRAINT `fk_sp_thuonghieu` FOREIGN KEY (`maTH`) REFERENCES `thuong_hieu` (`maTH`) ON UPDATE CASCADE,
    CONSTRAINT `fk_sp_danhmuc` FOREIGN KEY (`maDM`) REFERENCES `danh_muc` (`maDM`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Bảng Phiếu Nhập (PhieuNhap)
CREATE TABLE IF NOT EXISTS `phieu_nhap` (
    `maPhieu` VARCHAR(50) NOT NULL PRIMARY KEY,
    `ngayNhap` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `nguoiNhap` VARCHAR(150) NOT NULL,
    `maNCC` VARCHAR(50) NOT NULL,
    `soHoaDonNCC` VARCHAR(100),
    `tongTienTraTruocNCC` DOUBLE DEFAULT 0,
    `trangThaiThanhToan` VARCHAR(50) DEFAULT 'Chưa thanh toán',
    `trangThai` VARCHAR(50) DEFAULT 'Chờ duyệt',
    `ghiChu` TEXT,
    CONSTRAINT `fk_pn_ncc` FOREIGN KEY (`maNCC`) REFERENCES `nha_cung_cap` (`maNCC`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Bảng Chi Tiết Nhập (ChiTietNhap)
CREATE TABLE IF NOT EXISTS `chi_tiet_nhap` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255),
    `maPhieu` VARCHAR(50) NOT NULL,
    `maSP` VARCHAR(50) NOT NULL,
    `soLuongNhap` DOUBLE NOT NULL DEFAULT 1,
    `giaVon` DOUBLE NOT NULL DEFAULT 0,
    `ghiChu` VARCHAR(255),
    CONSTRAINT `fk_ctn_phieu` FOREIGN KEY (`maPhieu`) REFERENCES `phieu_nhap` (`maPhieu`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ctn_sanpham` FOREIGN KEY (`maSP`) REFERENCES `san_pham` (`maSP`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Bảng Đơn Hàng (DonHang)
CREATE TABLE IF NOT EXISTS `don_hang` (
    `maDH` VARCHAR(50) NOT NULL PRIMARY KEY,
    `maKH` VARCHAR(50) NOT NULL,
    `maKM` VARCHAR(50) NULL,
    `ngayTao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `phuongThucThanhToan` VARCHAR(100) DEFAULT 'Tiền mặt',
    `phuongThucGiaoHang` VARCHAR(100) DEFAULT 'Tại quầy',
    `phiVanChuyen` DOUBLE DEFAULT 0,
    `tongTienHang` DOUBLE DEFAULT 0,
    `trangThaiDon` VARCHAR(50) DEFAULT 'Chờ duyệt',
    CONSTRAINT `fk_dh_khachhang` FOREIGN KEY (`maKH`) REFERENCES `khach_hang` (`maKH`) ON UPDATE CASCADE,
    CONSTRAINT `fk_dh_khuyenmai` FOREIGN KEY (`maKM`) REFERENCES `khuyen_mai` (`maKM`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Bảng Chi Tiết Đơn Hàng (ChiTietDonHang)
CREATE TABLE IF NOT EXISTS `chi_tiet_don_hang` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `maDH` VARCHAR(50) NOT NULL,
    `maSP` VARCHAR(50) NOT NULL,
    `soLuong` INT NOT NULL DEFAULT 1,
    `donGia` DOUBLE NOT NULL DEFAULT 0,
    `thanhTien` DOUBLE NOT NULL DEFAULT 0,
    CONSTRAINT `fk_ctdh_donhang` FOREIGN KEY (`maDH`) REFERENCES `don_hang` (`maDH`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ctdh_sanpham` FOREIGN KEY (`maSP`) REFERENCES `san_pham` (`maSP`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Bảng Quản Lý Serial/IMEI Sản Phẩm (SerialSanPham)
-- Điểm mấu chốt của cửa hàng Laptop: Quản lý từng máy theo Serial Number
CREATE TABLE IF NOT EXISTS `serial_san_pham` (
    `maSerial_IMEI` VARCHAR(100) NOT NULL PRIMARY KEY,
    `maSP` VARCHAR(50) NOT NULL,
    `idChiTietNhap` INT NOT NULL,
    `idChiTietDonHang` INT NULL, -- NULL nếu còn tồn trong kho
    `ngayXuatKho` DATETIME NULL,
    `hanBaoHanh` DATETIME NULL,
    `trangThai` VARCHAR(50) DEFAULT 'TrongKho', -- TrongKho / DaBan / DangBaoHanh / LoiDoiTra / Hong
    `ghiChu` TEXT,
    CONSTRAINT `fk_serial_sanpham` FOREIGN KEY (`maSP`) REFERENCES `san_pham` (`maSP`) ON UPDATE CASCADE,
    CONSTRAINT `fk_serial_ctnhap` FOREIGN KEY (`idChiTietNhap`) REFERENCES `chi_tiet_nhap` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_serial_ctdonhang` FOREIGN KEY (`idChiTietDonHang`) REFERENCES `chi_tiet_don_hang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Bảng Hóa Đơn (HoaDon)
CREATE TABLE IF NOT EXISTS `hoa_don` (
    `maHD` VARCHAR(50) NOT NULL PRIMARY KEY,
    `maDH` VARCHAR(50) NOT NULL,
    `maKM` VARCHAR(50) NULL,
    `maKH` VARCHAR(50) NOT NULL,
    `soDienThoai` VARCHAR(20),
    `tenKhachHang` VARCHAR(150),
    `tongTienHang` DOUBLE NOT NULL DEFAULT 0,
    `diaChiGiaoHang` VARCHAR(255),
    `tienGiamGia` DOUBLE DEFAULT 0,
    `soTienThanhToan` DOUBLE NOT NULL DEFAULT 0,
    `trangThaiThanhToan` VARCHAR(50) DEFAULT 'Chưa thanh toán',
    `ghiChu` TEXT,
    CONSTRAINT `fk_hd_donhang` FOREIGN KEY (`maDH`) REFERENCES `don_hang` (`maDH`) ON UPDATE CASCADE,
    CONSTRAINT `fk_hd_khuyenmai` FOREIGN KEY (`maKM`) REFERENCES `khuyen_mai` (`maKM`) ON UPDATE CASCADE,
    CONSTRAINT `fk_hd_khachhang` FOREIGN KEY (`maKH`) REFERENCES `khach_hang` (`maKH`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Bảng Phiếu Bảo Hành (PhieuBaoHanh)
CREATE TABLE IF NOT EXISTS `phieu_bao_hanh` (
    `maPhieuBH` VARCHAR(50) NOT NULL PRIMARY KEY,
    `maKH` VARCHAR(50) NOT NULL,
    `maSP` VARCHAR(50) NOT NULL,
    `ngayTiepNhan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `moTaLoi` TEXT,
    `trangThai` VARCHAR(50) DEFAULT 'Tiếp nhận',
    CONSTRAINT `fk_pbh_khachhang` FOREIGN KEY (`maKH`) REFERENCES `khach_hang` (`maKH`) ON UPDATE CASCADE,
    CONSTRAINT `fk_pbh_sanpham` FOREIGN KEY (`maSP`) REFERENCES `san_pham` (`maSP`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Các chỉ mục hỗ trợ truy vấn nhanh
CREATE INDEX `idx_sp_ten` ON `san_pham` (`tenSP`);
CREATE INDEX `idx_kh_sdt` ON `khach_hang` (`soDienThoai`);
CREATE INDEX `idx_serial_sp` ON `serial_san_pham` (`maSP`, `trangThai`);
CREATE INDEX `idx_dh_kh` ON `don_hang` (`maKH`, `ngayTao`);
