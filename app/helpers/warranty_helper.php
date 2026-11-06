<?php
// app/helpers/warranty_helper.php
// Hàm hỗ trợ tự động kích hoạt bảo hành điện tử khi bán máy laptop

if (!function_exists('kichHoatBaoHanhTuDong')) {
    /**
     * Tự động kích hoạt phiếu bảo hành khi máy được bán / gán serial
     * @param mysqli $conn
     * @param string $maSerial Mã Serial/IMEI của máy
     * @param string $maKH Mã khách hàng mua máy (tùy chọn, tự tìm từ đơn hàng nếu trống)
     * @param string|null $ngayXuat Thời gian kích hoạt (mặc định NOW)
     * @return string|false Mã phiếu bảo hành nếu thành công, false nếu thất bại
     */
    function kichHoatBaoHanhTuDong($conn, $maSerial, $maKH = '', $ngayXuat = null) {
        $maSerial = trim($maSerial);
        if (empty($maSerial)) return false;

        // 1. Kiểm tra đã có phiếu bảo hành cho Serial này chưa
        $chk = $conn->query("SELECT maPhieuBH FROM phieubaohanh WHERE maSerial_IMEI = '$maSerial'");
        if ($chk && $chk->num_rows > 0) {
            $rowExisting = $chk->fetch_assoc();
            return $rowExisting['maPhieuBH']; // Đã kích hoạt trước đó
        }

        // 2. Lấy thông tin serial và mã sản phẩm
        $resSer = $conn->query("SELECT * FROM serialsanpham WHERE maSerial_IMEI = '$maSerial'");
        if (!$resSer || $resSer->num_rows === 0) return false;
        $rowSer = $resSer->fetch_assoc();
        $maSP = $rowSer['maSP'];
        $idCTDH = $rowSer['idChiTietDonHang'];

        // Lấy thông tin sản phẩm và thời gian bảo hành (tháng)
        $tenSP = 'Laptop chính hãng';
        $thoiGianBH = 24; // Mặc định 24 tháng nếu không có
        if (!empty($maSP)) {
            $resSP = $conn->query("SELECT tenSP, thoiGianBaoHanh FROM sanpham WHERE maSP = '$maSP'");
            if ($resSP && $resSP->num_rows > 0) {
                $rowSP = $resSP->fetch_assoc();
                $tenSP = $rowSP['tenSP'];
                if (!empty($rowSP['thoiGianBaoHanh'])) {
                    $thoiGianBH = (int)$rowSP['thoiGianBaoHanh'];
                }
            }
        }

        // 3. Nếu chưa có mã khách hàng, tìm từ đơn hàng thông qua idChiTietDonHang
        if (empty($maKH) && !empty($idCTDH)) {
            $resCT = $conn->query("SELECT maDH FROM chitietdonhang WHERE id = $idCTDH");
            if ($resCT && $resCT->num_rows > 0) {
                $rowCT = $resCT->fetch_assoc();
                $maDH = $rowCT['maDH'];
                $resDH = $conn->query("SELECT maKH FROM donhang WHERE maDH = '$maDH'");
                if ($resDH && $resDH->num_rows > 0) {
                    $rowDH = $resDH->fetch_assoc();
                    $maKH = $rowDH['maKH'];
                }
            }
        }

        // Nếu vẫn không có mã KH, tìm khách hàng đầu tiên trong CSDL hoặc đặt mặc định KH001
        if (empty($maKH)) {
            $resFirstKH = $conn->query("SELECT maKH FROM khachhang ORDER BY maKH ASC LIMIT 1");
            if ($resFirstKH && $resFirstKH->num_rows > 0) {
                $maKH = $resFirstKH->fetch_assoc()['maKH'];
            } else {
                $maKH = 'KH001';
            }
        }

        // 4. Xác định ngày bắt đầu và ngày kết thúc bảo hành
        $startDate = !empty($ngayXuat) ? $ngayXuat : (!empty($rowSer['ngayXuatKho']) ? $rowSer['ngayXuatKho'] : date('Y-m-d H:i:s'));
        $startTimestamp = strtotime($startDate);
        $endDate = date('Y-m-d H:i:s', strtotime("+$thoiGianBH months", $startTimestamp));

        // 5. Tự động sinh mã phiếu bảo hành tiếp theo (VD: BH2026_004)
        $resMax = $conn->query("SELECT maPhieuBH FROM phieubaohanh ORDER BY maPhieuBH DESC LIMIT 1");
        $autoMaPBH = 'BH' . date('Y') . '_001';
        if ($resMax && $resMax->num_rows > 0) {
            $rowMax = $resMax->fetch_assoc();
            $lastMa = $rowMax['maPhieuBH'];
            if (preg_match('/^BH(\d+)_(\d+)$/', $lastMa, $m)) {
                $year = $m[1];
                $num = intval($m[2]) + 1;
                $autoMaPBH = 'BH' . $year . '_' . str_pad($num, 3, '0', STR_PAD_LEFT);
            } else {
                $autoMaPBH = 'BH' . date('Ymd_His');
            }
        }

        $chinhSach = "Bảo hành chính hãng $tenSP $thoiGianBH tháng theo Serial/IMEI, 1 đổi 1 trong 30 ngày nếu lỗi NSX";
        $trangThai = (strtotime($endDate) >= time()) ? 'Đang hiệu lực' : 'Hết hạn';

        $sqlInsert = "INSERT INTO phieubaohanh (maPhieuBH, maSerial_IMEI, maKH, ngayBatDau, ngayKetThuc, moTaChinhSach, trangThai)
                      VALUES ('$autoMaPBH', '$maSerial', '$maKH', '$startDate', '$endDate', '$chinhSach', '$trangThai')";
        
        if ($conn->query($sqlInsert)) {
            return $autoMaPBH;
        }
        return false;
    }
}

if (!function_exists('dongBoBaoHanhMayDaBan')) {
    /**
     * Tự động quét và kích hoạt bảo hành cho tất cả máy đã bán mà chưa có phiếu bảo hành
     */
    function dongBoBaoHanhMayDaBan($conn) {
        $resSold = $conn->query("SELECT maSerial_IMEI FROM serialsanpham WHERE (trangThai = 'Đã bán' OR idChiTietDonHang IS NOT NULL) AND maSerial_IMEI NOT IN (SELECT maSerial_IMEI FROM phieubaohanh)");
        if ($resSold && $resSold->num_rows > 0) {
            while ($rowS = $resSold->fetch_assoc()) {
                kichHoatBaoHanhTuDong($conn, $rowS['maSerial_IMEI']);
            }
        }
    }
}

