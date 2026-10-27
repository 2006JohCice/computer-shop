<?php
// Layout Header (Giao diện Header)
include_once __DIR__ . '/header.php';
?>

<!-- Link CSS riêng cho trang chi tiết sản phẩm -->
<link rel="stylesheet" href="../../css/layouts/thongtinsp.css">

<main class="sp-detail-page">
    <div class="sp-container">

        <!-- ==================== BREADCRUMB ==================== -->
        <nav class="sp-breadcrumb" aria-label="Breadcrumb">
            <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="#">PC Gaming & Đồ Họa</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="#">PC TechZone Series</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span class="current">PC TechZone Nova i5 13400F / RTX 4060 8GB</span>
        </nav>

        <!-- ==================== KHỐI TỔNG QUAN SẢN PHẨM ==================== -->
        <section class="sp-overview-card">
            <div class="sp-overview-grid">

                <!-- 1. Cột Trái: Bộ Sưu Tập Ảnh Sản Phẩm -->
                <div class="sp-gallery-col">
                    <div class="sp-main-image-wrap">
                        <div class="sp-img-badges">
                            <span class="sp-badge-discount">-26% GIẢM</span>
                            <span class="sp-badge-installment">TRẢ GÓP 0%</span>
                        </div>
                        <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&q=80&w=700" alt="PC TechZone Nova" id="mainProductImg">
                    </div>

                    <!-- Danh sách ảnh thu nhỏ -->
                    <div class="sp-gallery-thumbs">
                        <div class="sp-thumb-item active">
                            <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&q=80&w=200" alt="Góc chính diện">
                        </div>
                        <div class="sp-thumb-item">
                            <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=200" alt="Góc nghiêng mặt kính">
                        </div>
                        <div class="sp-thumb-item">
                            <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&q=80&w=200" alt="Nội thất linh kiện">
                        </div>
                        <div class="sp-thumb-item">
                            <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=200" alt="Hệ thống tản nhiệt RGB">
                        </div>
                    </div>

                    <!-- Cam kết dịch vụ đi kèm -->
                    <div class="sp-warranty-commit">
                        <div class="sp-commit-item">
                            <i class="fa-solid fa-certificate"></i>
                            <span>Linh kiện chính hãng 100%</span>
                        </div>
                        <div class="sp-commit-item">
                            <i class="fa-solid fa-wrench"></i>
                            <span>Bảo hành tận nơi 36 tháng</span>
                        </div>
                        <div class="sp-commit-item">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span>1 đổi 1 trong 30 ngày đầu</span>
                        </div>
                        <div class="sp-commit-item">
                            <i class="fa-solid fa-soap"></i>
                            <span>Vệ sinh máy miễn phí trọn đời</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Cột Giữa: Thông Tin Chi Tiết & Tùy Chọn Mua Hàng -->
                <div class="sp-info-col">
                    <h1 class="sp-title">
                        PC Gaming TechZone Nova | Intel Core i5-13400F | RTX 4060 8GB | RAM 16GB RGB | SSD 512GB NVMe
                    </h1>

                    <div class="sp-rating-row">
                        <div class="sp-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <a href="#reviews" class="sp-reviews-count">(128 đánh giá của khách hàng)</a>
                        <div class="sp-sku-stock">
                            <span>Mã SP: <strong>TZ-PC-NV13400</strong></span>
                            <span>•</span>
                            <span class="sp-stock-status"><i class="fa-solid fa-circle-check"></i> Còn hàng</span>
                        </div>
                    </div>

                    <!-- Hộp Giá Bán -->
                    <div class="sp-price-box">
                        <span class="sp-current-price">18.490.000₫</span>
                        <span class="sp-old-price">24.990.000₫</span>
                        <span class="sp-save-tag">Tiết kiệm 6.500.000₫</span>
                    </div>

                    <!-- Khuyến Mãi Quà Tặng Độc Quyền -->
                    <div class="sp-promo-box">
                        <div class="sp-promo-title">
                            <i class="fa-solid fa-gift"></i>
                            <span>QUÀ TẶNG & KHUYẾN MÃI ĐI KÈM</span>
                        </div>
                        <ul class="sp-promo-list">
                            <li>
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Tặng bàn phím cơ Gaming TechZone RGB trị giá <strong>850.000₫</strong></span>
                            </li>
                            <li>
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Tặng lót chuột Gaming cỡ lớn 80x30cm trị giá <strong>150.000₫</strong></span>
                            </li>
                            <li>
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Giảm thêm <strong>300.000₫</strong> khi mua kèm bất kỳ màn hình từ 24 inch</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Miễn phí lắp đặt, cài đặt Windows 11 Pro bản quyền & bộ phần mềm văn phòng</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tùy Chọn Nâng Cấp Cấu Hình (Variants) -->
                    <div class="sp-variants-wrap">
                        <!-- Bộ nhớ RAM -->
                        <div>
                            <div class="sp-variant-group-label">Nâng cấp dung lượng RAM:</div>
                            <div class="sp-variant-options">
                                <button type="button" class="sp-opt-btn active">
                                    <span>16GB (2x8GB) DDR4 3200MHz</span>
                                    <span class="sp-opt-extra-price">Mặc định</span>
                                </button>
                                <button type="button" class="sp-opt-btn">
                                    <span>32GB (2x16GB) DDR4 3200MHz</span>
                                    <span class="sp-opt-extra-price">+1.150.000₫</span>
                                </button>
                            </div>
                        </div>

                        <!-- Ổ cứng SSD -->
                        <div>
                            <div class="sp-variant-group-label">Nâng cấp ổ cứng lưu trữ SSD:</div>
                            <div class="sp-variant-options">
                                <button type="button" class="sp-opt-btn active">
                                    <span>SSD 512GB M.2 NVMe PCIe 4.0</span>
                                    <span class="sp-opt-extra-price">Mặc định</span>
                                </button>
                                <button type="button" class="sp-opt-btn">
                                    <span>SSD 1TB M.2 NVMe PCIe 4.0 Gen4</span>
                                    <span class="sp-opt-extra-price">+890.000₫</span>
                                </button>
                            </div>
                        </div>

                        <!-- Màu vỏ case -->
                        <div>
                            <div class="sp-variant-group-label">Màu sắc vỏ Case Gaming:</div>
                            <div class="sp-variant-options">
                                <button type="button" class="sp-opt-btn active">
                                    <span>Đen Huyền Bí (Black Vision)</span>
                                    <span class="sp-opt-extra-price">Tiêu chuẩn</span>
                                </button>
                                <button type="button" class="sp-opt-btn">
                                    <span>Trắng Tuyết (White Frost RGB)</span>
                                    <span class="sp-opt-extra-price">+150.000₫</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Số lượng mua -->
                    <div class="sp-quantity-row">
                        <span class="sp-quantity-label">Số lượng:</span>
                        <div class="sp-qty-control">
                            <button type="button" class="sp-qty-btn">-</button>
                            <input type="text" value="1" class="sp-qty-input" readonly>
                            <button type="button" class="sp-qty-btn">+</button>
                        </div>
                    </div>

                    <!-- Nhóm Nút Mua Hàng -->
                    <div class="sp-action-buttons">
                        <div class="sp-btn-row-main">
                            <button type="button" class="sp-btn-buynow">
                                <span class="sp-btn-buynow-text">MUA NGAY</span>
                                <span class="sp-btn-buynow-sub">Giao tận nơi hoặc nhận tại Showroom</span>
                            </button>
                            <button type="button" class="sp-btn-addcart">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span>THÊM VÀO GIỎ</span>
                            </button>
                        </div>

                        <div class="sp-btn-installment-row">
                            <button type="button" class="sp-btn-installment">
                                <span class="sp-btn-inst-text">TRẢ GÓP 0% QUA THẺ</span>
                                <span class="sp-btn-inst-sub">Visa, Mastercard, JCB</span>
                            </button>
                            <button type="button" class="sp-btn-installment">
                                <span class="sp-btn-inst-text">TRẢ GÓP QUA CCCD</span>
                                <span class="sp-btn-inst-sub">Duyệt hồ sơ nhanh 5 phút</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Cột Phải: Chính Sách Showroom & Tình Trạng Hàng -->
                <aside class="sp-sidebar-col">
                    <!-- Chính sách TechZone -->
                    <div class="sp-side-card">
                        <div class="sp-side-card-title">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>CHÍNH SÁCH BÁN HÀNG</span>
                        </div>
                        <ul class="sp-policy-list">
                            <li>
                                <i class="fa-solid fa-truck-fast"></i>
                                <span>Giao hàng miễn phí nội thành bán kính 15km</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-handshake-angle"></i>
                                <span>Hỗ trợ kỹ thuật lắp đặt & hướng dẫn tận nhà</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-credit-card"></i>
                                <span>Đa dạng phương thức thanh toán linh hoạt</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-headset"></i>
                                <span>Hỗ trợ từ xa qua Teamviewer / Ultraviewer 24/7</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tình trạng tại các Showroom -->
                    <div class="sp-side-card">
                        <div class="sp-side-card-title">
                            <i class="fa-solid fa-store"></i>
                            <span>HỆ THỐNG CÒN HÀNG</span>
                        </div>
                        <ul class="sp-showroom-list">
                            <li class="sp-showroom-item">
                                <strong>Showroom Quận 1, TP.HCM</strong>
                                <span>123 Đường Công Nghệ, Bến Nghé</span>
                                <span class="sp-showroom-status"><i class="fa-solid fa-check"></i> Sẵn hàng trải nghiệm</span>
                            </li>
                            <li class="sp-showroom-item">
                                <strong>Showroom Đống Đa, Hà Nội</strong>
                                <span>456 Phố Thái Hà, Trung Liệt</span>
                                <span class="sp-showroom-status"><i class="fa-solid fa-check"></i> Sẵn hàng trải nghiệm</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Hotline liên hệ nhanh -->
                    <div class="sp-hotline-banner">
                        <div>CẦN TƯ VẤN THÊM CẤU HÌNH?</div>
                        <div class="sp-hotline-number">1900 8888</div>
                        <small style="color: #94a3b8;">(Miễn phí cước gọi từ 8h00 - 21h30)</small>
                    </div>
                </aside>

            </div>
        </section>

        <!-- ==================== NỘI DUNG BÀI VIẾT & THÔNG SỐ KỸ THUẬT ==================== -->
        <div class="sp-details-container">
            <!-- Cột Lớn: Bài viết mô tả chi tiết sản phẩm -->
            <article class="sp-article-card">
                <h2>ĐẶC ĐIỂM NỔI BẬT CỦA PC GAMING TECHZONE NOVA</h2>

                <p>
                    <strong>PC TechZone Nova</strong> là sự kết hợp hoàn hảo giữa sức mạnh xử lý của vi xử lý Intel Core i5 thế hệ 13 và card đồ họa thế hệ mới NVIDIA GeForce RTX 4060. Dàn máy được thiết kế chuyên biệt để đáp ứng mượt mà tất cả các tựa game Esport và game bom tấn AAA ở độ phân giải Full HD / 2K mức đồ họa cao nhất.
                </p>

                <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=900" alt="Hiệu năng vượt trội">

                <h3>1. Hiệu năng đỉnh cao với CPU Intel Core i5-13400F</h3>
                <p>
                    Trang bị kiến trúc Raptor Lake lai với 10 nhân (6 nhân hiệu năng cao P-core và 4 nhân tiết kiệm điện E-core) cùng 16 luồng xử lý, mức xung nhịp boost tối đa lên tới 4.6GHz. Cấu hình này không chỉ mang đến FPS ổn định trong mọi trận combat mà còn xử lý xuất sắc các tác vụ livestream, render video 4K hay đồ họa Photoshop, Premiere Pro.
                </p>

                <h3>2. Trải nghiệm đồ họa Ray Tracing thế hệ mới với RTX 4060 8GB</h3>
                <p>
                    Sở hữu kiến trúc Ada Lovelace tiên tiến, card màn hình RTX 4060 mang lại hiệu suất vượt bậc cùng công nghệ nâng cấp hình ảnh bằng trí tuệ nhân tạo DLSS 3. Bạn sẽ tận hưởng khung hình siêu mượt mà trong Cyberpunk 2077, Black Myth: Wukong, Call of Duty hay Valorant với đồ họa chân thực từng chi tiết ánh sáng.
                </p>

                <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&q=80&w=900" alt="Nội thất tản nhiệt cao cấp">

                <h3>3. Tản nhiệt khí tháp CR-1000 EVO ARGB mát mẻ & yên tĩnh</h3>
                <p>
                    Dàn máy được TechZone trang bị bộ tản nhiệt tháp hiệu năng cao với 4 ống đồng tiếp xúc trực tiếp, giúp duy trì nhiệt độ CPU luôn dưới mức 65°C ngay cả khi tải nặng liên tục nhiều giờ liền. Hệ thống 4 quạt ARGB đồng bộ màu sắc tạo nên không gian giải trí đỉnh cao.
                </p>

                <h3>4. Nguồn công suất thực 650W chuẩn 80 Plus Bronze</h3>
                <p>
                    Trái tim của hệ thống sử dụng bộ nguồn 650W đạt chứng nhận chuẩn 80 Plus Bronze với đầy đủ các mạch bảo vệ quá áp, quá dòng, mang lại sự bền bỉ dài lâu và dễ dàng nâng cấp phần cứng trong tương lai.
                </p>
            </article>

            <!-- Cột Nhỏ: Bảng thông số kỹ thuật chi tiết (Sticky) -->
            <aside class="sp-specs-card">
                <div class="sp-specs-title">
                    <i class="fa-solid fa-list-check"></i>
                    <span>THÔNG SỐ KỸ THUẬT</span>
                </div>

                <table class="sp-specs-table">
                    <tbody>
                        <tr>
                            <td class="spec-name">Bộ vi xử lý (CPU)</td>
                            <td class="spec-value">Intel Core i5-13400F (10 Nhân, 16 Luồng, Turbo 4.6GHz, 20MB Cache)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Bo mạch chủ (Mainboard)</td>
                            <td class="spec-value">B760M Gaming DDR4 (Hỗ trợ Wifi / Bluetooth)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Bộ nhớ RAM</td>
                            <td class="spec-value">16GB (2 x 8GB) DDR4 3200MHz tản nhiệt thép RGB</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Card đồ họa (VGA)</td>
                            <td class="spec-value">NVIDIA GeForce RTX 4060 8GB GDDR6 (Dual Fan)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Ổ cứng (SSD)</td>
                            <td class="spec-value">512GB M.2 PCIe 4.0 NVMe (Tốc độ đọc 3500MB/s)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Nguồn máy tính (PSU)</td>
                            <td class="spec-value">650W Chuẩn 80 Plus Bronze (Công suất thực)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Tản nhiệt CPU</td>
                            <td class="spec-value">Tản nhiệt khí tháp CR-1000 EVO ARGB</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Vỏ máy tính (Case)</td>
                            <td class="spec-value">TechZone Vision Black (Kính cường lực + 4 Fan ARGB)</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Hệ điều hành</td>
                            <td class="spec-value">Hỗ trợ Windows 11 64-bit bản quyền</td>
                        </tr>
                        <tr>
                            <td class="spec-name">Bảo hành</td>
                            <td class="spec-value">36 Tháng chính hãng (Linh kiện bảo hành theo tem)</td>
                        </tr>
                    </tbody>
                </table>
            </aside>
        </div>

        <!-- ==================== ĐÁNH GIÁ TỪ KHÁCH HÀNG ==================== -->
        <section class="sp-reviews-card" id="reviews">
            <div class="sp-reviews-header">
                <div class="sp-rating-score-box">
                    <div class="sp-score-big">4.9</div>
                    <div class="sp-score-meta">
                        <div class="sp-stars" style="font-size: 1.1rem;">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div style="font-size: 0.8125rem; color: #64748b;">128 người đã đánh giá sản phẩm này</div>
                    </div>
                </div>

                <!-- Thanh phân bổ sao -->
                <div class="sp-review-bars">
                    <div class="sp-bar-row">
                        <span>5 ★</span>
                        <div class="sp-bar-track"><div class="sp-bar-fill" style="width: 88%;"></div></div>
                        <span>88%</span>
                    </div>
                    <div class="sp-bar-row">
                        <span>4 ★</span>
                        <div class="sp-bar-track"><div class="sp-bar-fill" style="width: 10%;"></div></div>
                        <span>10%</span>
                    </div>
                    <div class="sp-bar-row">
                        <span>3 ★</span>
                        <div class="sp-bar-track"><div class="sp-bar-fill" style="width: 2%;"></div></div>
                        <span>2%</span>
                    </div>
                </div>
            </div>

            <!-- Danh sách nhận xét mẫu -->
            <div class="sp-comments-list">
                <div class="sp-comment-item">
                    <div class="sp-comment-user">
                        <div class="sp-user-avatar">NA</div>
                        <div>
                            <div class="sp-user-name">Nguyễn Tuấn Anh</div>
                            <span class="sp-verified-badge"><i class="fa-solid fa-circle-check"></i> Đã mua hàng tại TechZone</span>
                        </div>
                    </div>
                    <div class="sp-stars" style="font-size: 0.8rem; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="sp-comment-text">
                        Máy đóng gói rất cẩn thận, đi dây nguồn gọn gàng đẹp mắt. Mình test thử game Black Myth Wukong với Cyberpunk 2077 trên màn 2K bật DLSS chạy mượt mà 80-90 FPS. Shop hỗ trợ cài sẵn Win và game rất nhiệt tình!
                    </p>
                </div>

                <div class="sp-comment-item">
                    <div class="sp-comment-user">
                        <div class="sp-user-avatar">HM</div>
                        <div>
                            <div class="sp-user-name">Hoàng Minh</div>
                            <span class="sp-verified-badge"><i class="fa-solid fa-circle-check"></i> Đã mua hàng tại TechZone</span>
                        </div>
                    </div>
                    <div class="sp-stars" style="font-size: 0.8rem; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="sp-comment-text">
                        Giao hỏa tốc đúng trong 2 tiếng tại TP.HCM. Kỹ thuật viên qua bàn giao tận nhà, hướng dẫn chỉnh led quạt rất chi tiết. Đáng giá từng xu!
                    </p>
                </div>
            </div>
        </section>

    </div>
</main>

<?php
// Layout Footer (Giao diện Footer)
include_once __DIR__ . '/footer.php';
?>
