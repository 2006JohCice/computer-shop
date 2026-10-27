<?php
// app/views/client/products/detail.php
$pageTitle = "Chi Tiết Sản Phẩm Laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

$maSP = htmlspecialchars($_GET['id'] ?? 'SP01');

// Database catalogue mô phỏng sản phẩm laptop
$catalog = [
    'SP01' => [
        'maSP' => 'SP01',
        'tenSP' => 'Dell XPS 13 9320 Plus (Intel Core i7-1360P, 16GB, 512GB, 13.4" OLED 3.5K)',
        'danhMuc' => 'Laptop Doanh Nhân',
        'dmSlug' => 'doanh-nhan',
        'thuongHieu' => 'DELL',
        'giaBan' => 35990000,
        'giaGoc' => 41990000,
        'tietKiem' => 6000000,
        'soLuongTon' => 5,
        'thoiGianBaoHanh' => 24,
        'viTriKho' => 'Kệ A1 - Tầng 2',
        'imgMain' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=700&auto=format&fit=crop&q=80',
        'thumbs' => [
            'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=500&auto=format&fit=crop&q=80'
        ],
        'specs' => [
            'Vi xử lý (CPU)' => 'Intel Core i7-1360P (12 nhân 16 luồng, Turbo Boost 5.0 GHz, 18MB Cache)',
            'Bộ nhớ RAM' => '16GB LPDDR5 6000MHz Dual Channel Onboard',
            'Ổ cứng lưu trữ' => '512GB M.2 PCIe NVMe Gen 4x4 SSD tốc độ cao',
            'Card đồ họa (VGA)' => 'Intel Iris Xe Graphics tích hợp mượt mà tác vụ đồ họa 2D & video',
            'Màn hình hiển thị' => '13.4 inch OLED 3.5K (3456 x 2160), 100% DCI-P3, 400 nits, InfinityEdge Touch',
            'Cổng kết nối' => '2x Thunderbolt 4 (Type-C) hỗ trợ DisplayPort & Power Delivery',
            'Bàn phím & Touchpad' => 'Bàn phím tràn viền cảm ứng điện dung, Touchpad kính tàng hình haptic',
            'Thời lượng Pin' => '3-cell 55Wh Li-ion, hỗ trợ sạc nhanh ExpressCharge 80% trong 60 phút',
            'Trọng lượng máy' => '1.23 kg - Siêu mỏng 15.28 mm gia công từ nhôm nguyên khối CNC',
            'Hệ điều hành' => 'Windows 11 Home SL bản quyền trọn đời'
        ]
    ],
    'SP02' => [
        'maSP' => 'SP02',
        'tenSP' => 'ASUS ROG Strix G16 G614 Gaming (Core i9-13980HX, 32GB, RTX 4070 8GB, 240Hz)',
        'danhMuc' => 'Laptop Gaming',
        'dmSlug' => 'gaming',
        'thuongHieu' => 'ASUS',
        'giaBan' => 42990000,
        'giaGoc' => 48990000,
        'tietKiem' => 6000000,
        'soLuongTon' => 8,
        'thoiGianBaoHanh' => 24,
        'viTriKho' => 'Kệ G1 - Tầng 1',
        'imgMain' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=700&auto=format&fit=crop&q=80',
        'thumbs' => [
            'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=500&auto=format&fit=crop&q=80'
        ],
        'specs' => [
            'Vi xử lý (CPU)' => 'Intel Core i9-13980HX (24 nhân 32 luồng, xung nhịp cực đại 5.6 GHz)',
            'Bộ nhớ RAM' => '32GB DDR5 4800MHz (2 khe cắm nâng cấp tối đa 64GB)',
            'Ổ cứng lưu trữ' => '1TB M.2 PCIe Gen4 NVMe SSD (hỗ trợ thêm 1 khe mở rộng)',
            'Card đồ họa (VGA)' => 'NVIDIA GeForce RTX 4070 8GB GDDR6 TGP 140W + MUX Switch',
            'Màn hình hiển thị' => '16.0 inch QHD+ (2560 x 1600) 16:10, 240Hz 3ms, 100% DCI-P3, G-Sync',
            'Hệ thống tản nhiệt' => 'Hệ thống tản nhiệt 3 quạt Tri-Fan, Keo tản nhiệt kim loại lỏng Conductonaut Extreme',
            'Cổng kết nối' => '1x Thunderbolt 4, 1x USB 3.2 Gen 2 Type-C, 2x USB 3.2 Gen 2 Type-A, HDMI 2.1, LAN 2.5G',
            'Thời lượng Pin' => '4-cell 90Wh Li-ion',
            'Trọng lượng máy' => '2.50 kg',
            'Hệ điều hành' => 'Windows 11 Home 64-bit'
        ]
    ],
    'SP03' => [
        'maSP' => 'SP03',
        'tenSP' => 'Apple MacBook Pro 14 M3 Pro (18GB Unified, 512GB SSD, Liquid Retina XDR)',
        'danhMuc' => 'Laptop Đồ Họa',
        'dmSlug' => 'do-hoa',
        'thuongHieu' => 'APPLE',
        'giaBan' => 49990000,
        'giaGoc' => 54990000,
        'tietKiem' => 5000000,
        'soLuongTon' => 3,
        'thoiGianBaoHanh' => 12,
        'viTriKho' => 'Kệ VIP - Ô 01',
        'imgMain' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=700&auto=format&fit=crop&q=80',
        'thumbs' => [
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&auto=format&fit=crop&q=80'
        ],
        'specs' => [
            'Bộ vi xử lý' => 'Apple M3 Pro (11 nhân CPU gồm 5 nhân hiệu năng cao và 6 nhân tiết kiệm điện)',
            'Bộ nhớ đồ họa' => '14 nhân GPU kiến trúc Dynamic Caching thế hệ mới + Ray tracing phần cứng',
            'Bộ nhớ Unified' => '18GB Unified Memory băng thông cực lớn 150GB/s',
            'Ổ cứng SSD' => '512GB PCIe SSD siêu tốc độ đọc ghi lên tới 7.4GB/s',
            'Màn hình hiển thị' => '14.2 inch Liquid Retina XDR (3024 x 1964), 1000 nits duy trì, ProMotion 120Hz',
            'Âm thanh' => 'Hệ thống 6 loa độ trung thực cao với loa trầm khử lực, Spatial Audio',
            'Cổng kết nối' => '3x Thunderbolt 4, HDMI, Đầu đọc thẻ SDXC, Cổng sạc MagSafe 3',
            'Thời lượng Pin' => 'Pin 70Wh cho thời gian sử dụng liên tục lên đến 18 giờ',
            'Trọng lượng' => '1.61 kg - Vỏ nhôm tái chế 100% Space Black chống bám vân tay',
            'Hệ điều hành' => 'macOS Sonoma tối ưu đồ họa và lập trình'
        ]
    ],
    'SP04' => [
        'maSP' => 'SP04',
        'tenSP' => 'Lenovo ThinkPad X1 Carbon Gen 11 (Core i7-1365U, 16GB, 512GB SSD, 14" WUXGA)',
        'danhMuc' => 'Laptop Doanh Nhân',
        'dmSlug' => 'doanh-nhan',
        'thuongHieu' => 'LENOVO',
        'giaBan' => 38500000,
        'giaGoc' => 43500000,
        'tietKiem' => 5000000,
        'soLuongTon' => 4,
        'thoiGianBaoHanh' => 36,
        'viTriKho' => 'Kệ B2 - Ô 04',
        'imgMain' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=700&auto=format&fit=crop&q=80',
        'thumbs' => [
            'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80'
        ],
        'specs' => [
            'Vi xử lý (CPU)' => 'Intel Core i7-1365U vPro (10 nhân 12 luồng, tối đa 5.2GHz)',
            'Bộ nhớ RAM' => '16GB LPDDR5 6400MHz',
            'Ổ cứng lưu trữ' => '512GB SSD M.2 PCIe Gen4 NVMe Opal 2.0',
            'Đồ họa' => 'Intel Iris Xe Graphics',
            'Màn hình' => '14.0 inch WUXGA (1920 x 1200) IPS, chống chói, 400 nits, 100% sRGB',
            'Độ bền quân đội' => 'Đạt tiêu chuẩn quân đội Mỹ MIL-STD-810H',
            'Trọng lượng' => '1.12 kg - Sợi carbon kết hợp hợp kim magie siêu nhẹ',
            'Bảo hành' => '36 Tháng chính hãng Lenovo Premier Support tận nơi'
        ]
    ]
];

$product = $catalog[$maSP] ?? $catalog['SP01'];
?>

<div class="container py-4">
    <!-- ==================== BREADCRUMB ==================== -->
    <nav class="sp-breadcrumb">
        <a href="<?= BASE_URL ?>/client/index.php"><i class="bi bi-house me-1"></i>Trang chủ</a>
        <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
        <a href="<?= BASE_URL ?>/client/index.php?dm=<?= $product['dmSlug'] ?>"><?= $product['danhMuc'] ?></a>
        <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
        <span class="current text-truncate" style="max-width: 450px;"><?= $product['tenSP'] ?></span>
    </nav>

    <!-- ==================== KHỐI TỔNG QUAN SẢN PHẨM ==================== -->
    <section class="sp-overview-card">
        <div class="row g-4">
            <!-- 1. CỘT TRÁI: BỘ SƯU TẬP ẢNH SẢN PHẨM (GALLERY) -->
            <div class="col-lg-5">
                <div class="sp-main-image-wrap">
                    <div class="sp-img-badges">
                        <span class="sp-badge-discount">-<?= round(($product['tietKiem'] / $product['giaGoc']) * 100) ?>% GIẢM</span>
                        <span class="sp-badge-installment">TRẢ GÓP 0%</span>
                    </div>
                    <img src="<?= $product['imgMain'] ?>" alt="<?= htmlspecialchars($product['tenSP']) ?>" id="mainProductImg">
                </div>

                <!-- Thumbs -->
                <div class="sp-gallery-thumbs">
                    <?php foreach ($product['thumbs'] as $idx => $thumb): ?>
                    <div class="sp-thumb-item <?= $idx === 0 ? 'active' : '' ?>" onclick="switchProductThumb('<?= $thumb ?>', this)">
                        <img src="<?= $thumb ?>" alt="Thumbnail <?= $idx + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. CỘT PHẢI: THÔNG TIN MÁY, GIÁ TIỀN & ĐẶT HÀNG -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white font-monospace"><?= $product['thuongHieu'] ?></span>
                    <span class="badge bg-light text-secondary border font-monospace">MÃ SP: <?= $product['maSP'] ?></span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2-circle me-1"></i>Sẵn hàng tại kho (<?= $product['soLuongTon'] ?> máy)</span>
                </div>

                <h1 class="sp-product-title"><?= $product['tenSP'] ?></h1>

                <!-- Đánh giá sao & Vị trí kho -->
                <div class="sp-rating-bar">
                    <div class="sp-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="fw-bold text-dark">4.9 / 5</span>
                    <span class="text-muted">(128 đánh giá của khách hàng)</span>
                    <span class="text-muted">•</span>
                    <span class="text-secondary"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Kho: <?= $product['viTriKho'] ?></span>
                </div>

                <!-- Khung Giá & Khuyến Mãi -->
                <div class="sp-price-box">
                    <div>
                        <div class="sp-price-current"><?= number_format($product['giaBan'], 0, ',', '.') ?> ₫</div>
                        <small class="text-muted">Đã bao gồm thuế VAT 10% & Xuất Hóa Đơn Điện Tử</small>
                    </div>
                    <div class="sp-price-old"><?= number_format($product['giaGoc'], 0, ',', '.') ?> ₫</div>
                    <span class="sp-price-saving">Tiết kiệm: <?= number_format($product['tietKiem'], 0, ',', '.') ?> ₫</span>
                </div>

                <!-- Khung Quà Tặng Độc Quyền (Học hỏi từ Unnamed) -->
                <div class="sp-gift-box">
                    <div class="sp-gift-header">
                        <i class="bi bi-gift-fill text-primary"></i>
                        <span>ƯU ĐÃI & QUÀ TẶNG KÈM THEO MÁY</span>
                    </div>
                    <ul class="sp-gift-list">
                        <li><i class="bi bi-check-circle-fill"></i><span>Tặng <strong>Balo Laptop Cao Cấp Chống Nước TechZone</strong> trị giá 850.000₫</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Tặng <strong>Chuột Không Dây Gaming Silent RGB</strong> trị giá 350.000₫</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Giảm thêm <strong>500.000₫</strong> khi thanh toán chuyển khoản qua VNPAY / SEPAY QR</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Miễn phí lắp đặt, vệ sinh máy trọn đời & cài sẵn <strong>Windows 11 Pro bản quyền</strong></span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>Hỗ trợ kỹ thuật từ xa qua Ultraviewer / Teamviewer 24/7 trọn đời máy</span></li>
                    </ul>
                </div>

                <!-- Bộ chọn số lượng & 2 Nút Mua Hàng -->
                <div class="sp-buy-actions">
                    <div class="sp-qty-select">
                        <button type="button" class="sp-qty-btn" onclick="adjustQty(-1)">-</button>
                        <input type="number" id="buyQtyInput" class="sp-qty-input" value="1" min="1" max="5">
                        <button type="button" class="sp-qty-btn" onclick="adjustQty(1)">+</button>
                    </div>

                    <!-- Nút Thêm vào giỏ -->
                    <button type="button" class="btn-sp-add-cart" onclick="addCurrentProductToCart()">
                        <i class="bi bi-cart-plus me-1"></i>THÊM VÀO GIỎ
                    </button>

                    <!-- Nút Mua ngay (Chuyển checkout) -->
                    <button type="button" class="btn-sp-buy-now" onclick="buyCurrentProductNow()">
                        <i class="bi bi-lightning-charge-fill me-1"></i>MUA NGAY (TRẢ GÓP 0%)
                    </button>
                </div>

                <!-- Cam Kết Dịch Vụ -->
                <div class="sp-policy-box">
                    <div class="sp-policy-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Bảo hành <strong><?= $product['thoiGianBaoHanh'] ?> Tháng</strong> chính hãng kích hoạt theo Serial/IMEI</span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Lỗi phần cứng NSX đổi mới trong <strong>30 ngày</strong></span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-truck"></i>
                        <span>Giao hỏa tốc trong <strong>2 giờ</strong> nội thành Hà Nội & TP.HCM</span>
                    </div>
                    <div class="sp-policy-item">
                        <i class="bi bi-credit-card"></i>
                        <span>Trả góp 0% lãi suất xét duyệt nhanh trong 5 phút</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== BẢNG THÔNG SỐ KỸ THUẬT & ĐÁNH GIÁ ==================== -->
    <div class="row g-4">
        <!-- Cột Trái: Đánh Giá Chi Tiết Máy & Review Khách Hàng -->
        <div class="col-lg-7">
            <div class="tz-card p-4 mb-4">
                <h3 class="fw-bold text-dark mb-3 fs-5">
                    <i class="bi bi-file-text text-primary me-2"></i>Đặc Điểm Nổi Bật & Trải Nghiệm Thực Tế
                </h3>
                <div class="text-secondary" style="font-size: 14.5px; line-height: 1.8;">
                    <p>
                        <strong><?= $product['tenSP'] ?></strong> là dòng sản phẩm đại diện cho xu hướng điện toán cá nhân thế hệ mới của hãng <strong><?= $product['thuongHieu'] ?></strong>. Được trang bị kiến trúc vi xử lý tiên tiến cùng công nghệ tăng tốc trí tuệ nhân tạo (NPU AI Engine), chiếc máy sẵn sàng xử lý mượt mà từ các tác vụ văn phòng văn phòng đa nhiệm, xử lý đồ họa kiến trúc nặng cho tới trải nghiệm các tựa game bom tấn AAA.
                    </p>
                    <h5 class="fw-bold text-dark mt-3 mb-2">1. Hiệu năng vượt bậc với chip xử lý thế hệ mới</h5>
                    <p>
                        Nhờ tối ưu hóa điện năng và xung nhịp boost mạnh mẽ, thiết bị có khả năng giải phóng toàn bộ hiệu năng mà vẫn duy trì nhiệt độ mát mẻ suốt nhiều giờ tải nặng liên tục. Khả năng đa nhiệm được củng cố với bộ nhớ RAM băng thông cao, cho phép mở hàng chục tab Chrome và các ứng dụng đồ họa nặng cùng lúc mà không giật lag.
                    </p>
                    <h5 class="fw-bold text-dark mt-3 mb-2">2. Màn hình chuẩn màu sắc nét & Chống mỏi mắt</h5>
                    <p>
                        Trang bị tấm nền công nghệ cao với dải màu đạt chuẩn chuyên nghiệp, độ tương phản sâu và độ sáng vượt trội giúp hiển thị chi tiết hình ảnh chân thực ngay cả trong điều kiện ánh sáng ngoài trời.
                    </p>
                </div>
            </div>

            <!-- Khung Review Khách Hàng -->
            <div class="tz-review-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0 fs-5"><i class="bi bi-chat-heart text-danger me-2"></i>Khách Hàng Đánh Giá</h4>
                    <span class="badge bg-primary">4.9 / 5 Sao</span>
                </div>

                <div class="tz-review-item">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-dark">Trần Hoàng Long</strong>
                        <span class="text-muted small">18/10/2026</span>
                    </div>
                    <div class="sp-stars mb-2" style="font-size: 12px;">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary small mb-0">
                        Máy đóng gói rất cẩn thận, nguyên seal hộp. Mình kiểm tra mã Serial trên hệ thống TechZone đúng hạn bảo hành 24 tháng chính hãng. Dùng rất mượt, pin trâu, đáng giá từng đồng!
                    </p>
                </div>

                <div class="tz-review-item">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-dark">Nguyễn Thị Thu Hà</strong>
                        <span class="text-muted small">15/10/2026</span>
                    </div>
                    <div class="sp-stars mb-2" style="font-size: 12px;">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary small mb-0">
                        Giao hàng hỏa tốc trong 2 giờ tại Hà Nội cực kỳ nhanh. Kỹ thuật viên qua bàn giao tận nhà, hướng dẫn cài đặt phần mềm rất nhiệt tình. 10 điểm cho dịch vụ!
                    </p>
                </div>
            </div>
        </div>

        <!-- Cột Phải: BẢNG THÔNG SỐ KỸ THUẬT CHI TIẾT -->
        <div class="col-lg-5">
            <div class="tz-specs-card">
                <h3 class="fw-bold text-dark mb-3 pb-2 border-bottom fs-5">
                    <i class="bi bi-cpu text-primary me-2"></i>Bảng Thông Số Kỹ Thuật
                </h3>

                <table class="tz-specs-table">
                    <tbody>
                        <?php foreach ($product['specs'] as $name => $val): ?>
                        <tr>
                            <td class="spec-name"><?= $name ?></td>
                            <td class="spec-val"><?= $val ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Showroom còn hàng -->
            <div class="tz-card p-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 15px;">
                    <i class="bi bi-shop text-primary me-2"></i>Showroom Sẵn Hàng Trải Nghiệm
                </h5>
                <div class="mb-3">
                    <div class="fw-bold text-dark small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Showroom Quận 1, TP.HCM</div>
                    <div class="text-muted small ps-3">123 Đường Công Nghệ, P. Bến Nghé • <span class="text-success fw-bold">Sẵn hàng</span></div>
                </div>
                <div>
                    <div class="fw-bold text-dark small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Showroom Đống Đa, Hà Nội</div>
                    <div class="text-muted small ps-3">456 Phố Thái Hà, P. Trung Liệt • <span class="text-success fw-bold">Sẵn hàng</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchProductThumb(src, el) {
    document.getElementById('mainProductImg').src = src;
    document.querySelectorAll('.sp-thumb-item').forEach(item => item.classList.remove('active'));
    el.classList.add('active');
}

function adjustQty(delta) {
    const input = document.getElementById('buyQtyInput');
    let val = parseInt(input.value) || 1;
    val = Math.max(1, Math.min(val + delta, 5));
    input.value = val;
}

function addCurrentProductToCart() {
    const qty = parseInt(document.getElementById('buyQtyInput').value) || 1;
    CartManager.themSanPham('<?= $product['maSP'] ?>', qty);
}

function buyCurrentProductNow() {
    const qty = parseInt(document.getElementById('buyQtyInput').value) || 1;
    CartManager.themSanPham('<?= $product['maSP'] ?>', qty);
    location.href = '<?= BASE_URL ?>/client/checkout.php';
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
