-- ========================================================
-- DỮ LIỆU MẪU (SEED DATA) CHO CỬA HÀNG BÁN LAPTOP
-- ========================================================

USE `quan_ly_cua_hang_laptop`;

-- 1. Nhà Cung Cấp
INSERT INTO `nha_cung_cap` (`maNCC`, `tenCongTy`, `soDienThoai`, `diaChiTruSo`, `quocTich`, `maSoThue`) VALUES
('NCC01', 'Công ty Cổ phần Thế Giới Số (Digiworld)', '02839290059', '195 Cô Bắc, P. Cô Giang, Quận 1, TP. HCM', 'Việt Nam', '0302861742'),
('NCC02', 'Công ty TNHH Synnex FPT', '02473006666', 'Tòa nhà FPT, Phố Duy Tân, Cầu Giấy, Hà Nội', 'Việt Nam', '0101778163');

-- 2. Danh Mục
INSERT INTO `danh_muc` (`maDM`, `tenDM`, `moTa`, `trangThai`) VALUES
(1, 'Laptop Gaming', 'Dòng laptop chuyên chơi game cấu hình cao, tản nhiệt tốt', 1),
(2, 'Laptop Văn Phòng & Học Tập', 'Thiết kế mỏng nhẹ, pin trâu, phù hợp sinh viên và dân văn phòng', 1),
(3, 'Laptop Đồ Họa & Kỹ Thuật', 'Màn hình màu chuẩn, chip mạnh mẽ, card đồ họa rời', 1),
(4, 'Laptop Cao Cấp - Doanh Nhân', 'Vỏ nhôm/magie nguyên khối, bảo mật cao cấp, sang trọng', 1);

-- 3. Thương Hiệu
INSERT INTO `thuong_hieu` (`maTH`, `tenTH`, `moTa`, `trangThai`) VALUES
('DELL', 'Dell Technologies', 'Thương hiệu laptop nổi tiếng về độ bền và ổn định từ Mỹ', 1),
('ASUS', 'ASUStek Computer Inc.', 'Hãng công nghệ Đài Loan với dải ROG, TUF, ZenBook đa dạng', 1),
('APPLE', 'Apple Inc.', 'Hệ sinh thái MacBook chạy macOS mỏng nhẹ, pin trâu', 1),
('LENOVO', 'Lenovo Group Limited', 'Thương hiệu nổi tiếng với ThinkPad và Legion gaming', 1);

-- 4. Khuyến Mãi
INSERT INTO `khuyen_mai` (`maKM`, `tenKM`, `phanTramGiam`, `ngayBatDau`, `ngayKetThuc`) VALUES
('KM_CHAOMUNG', 'Ưu đãi chào mừng khách hàng mới', 5.0, '2026-01-01 00:00:00', '2026-12-31 23:59:59'),
('KM_BACKTOSCHOOL', 'Mùa tựu trường rộn ràng', 10.0, '2026-08-01 00:00:00', '2026-10-31 23:59:59');

-- 5. Khách Hàng
INSERT INTO `khach_hang` (`maKH`, `hoVaTen`, `soDienThoai`, `email`, `diaChi`, `doiTuongUuTien`) VALUES
('KH01', 'Nguyễn Văn An', '0912345678', 'an.nguyen@gmail.com', '123 Đường Láng, Đống Đa, Hà Nội', 0),
('KH02', 'Trần Thị Mai', '0987654321', 'mai.tran@gmail.com', '45 Hai Bà Trưng, Quận 1, TP. HCM', 1);

-- 6. Sản Phẩm Laptop
INSERT INTO `san_pham` (`maSP`, `tenSP`, `giaBan`, `soLuongTon`, `maTH`, `maDM`, `thoiGianBaoHanh`, `thongSoKyThuat`, `moTa`, `viTriKho`, `trangThai`, `isDeleted`) VALUES
('SP01', 'Dell XPS 13 9320 Plus', 35990000, 2, 'DELL', 4, 24, 'Intel Core i7-1360P, 16GB LPDDR5, 512GB SSD, 13.4 OLED 3.5K Touch', 'Laptop doanh nhân siêu mỏng nhẹ cao cấp', 'Kệ A-01', 1, 0),
('SP02', 'Asus ROG Zephyrus G14 (2024)', 42990000, 2, 'ASUS', 1, 24, 'AMD Ryzen 9 8945HS, 32GB RAM, 1TB SSD, RTX 4070 8GB, 14 3K OLED 120Hz', 'Gaming mỏng nhẹ đỉnh cao', 'Kệ B-02', 1, 0);

-- 7. Phiếu Nhập
INSERT INTO `phieu_nhap` (`maPhieu`, `ngayNhap`, `nguoiNhap`, `maNCC`, `soHoaDonNCC`, `tongTienTraTruocNCC`, `trangThaiThanhToan`, `trangThai`, `ghiChu`) VALUES
('PN20261001', '2026-10-01 09:30:00', 'Quản lý kho Nguyễn Tuấn', 'NCC01', 'HD-DGW-9821', 100000000, 'Đã thanh toán', 'Đã nhập kho', 'Nhập đợt 1 tháng 10/2026');

-- 8. Chi Tiết Nhập
INSERT INTO `chi_tiet_nhap` (`id`, `name`, `maPhieu`, `maSP`, `soLuongNhap`, `giaVon`, `ghiChu`) VALUES
(1, 'Nhập Dell XPS 13', 'PN20261001', 'SP01', 2, 29500000, 'Seal Digiworld'),
(2, 'Nhập Asus ROG Zephyrus G14', 'PN20261001', 'SP02', 2, 35000000, 'Seal FPT Synnex');

-- 9. Serial/IMEI Sản Phẩm
INSERT INTO `serial_san_pham` (`maSerial_IMEI`, `maSP`, `idChiTietNhap`, `idChiTietDonHang`, `ngayXuatKho`, `hanBaoHanh`, `trangThai`, `ghiChu`) VALUES
('DELL-XPS-9320-001', 'SP01', 1, NULL, NULL, NULL, 'TrongKho', 'Nguyên seal'),
('DELL-XPS-9320-002', 'SP01', 1, NULL, NULL, NULL, 'TrongKho', 'Nguyên seal'),
('ASUS-G14-2024-001', 'SP02', 2, NULL, NULL, NULL, 'TrongKho', 'Nguyên seal'),
('ASUS-G14-2024-002', 'SP02', 2, NULL, NULL, NULL, 'TrongKho', 'Nguyên seal');
