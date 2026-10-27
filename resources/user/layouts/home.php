<?php
// Layout Header (Giao diện Header)
include_once __DIR__ . '/header.php';
?>

<main class="home-main">
    <div class="home-container">

        <!-- ==================== 1. HERO SECTION ==================== -->
        <section class="hero-section">
            <!-- Left: Quick Category Navigation -->
            <aside class="hero-category-sidebar">
                <ul class="category-nav-list">
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-desktop item-icon"></i>
                            <span>PC Gaming Đồ Họa</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-laptop item-icon"></i>
                            <span>Laptop Gaming & AI</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-microchip item-icon"></i>
                            <span>CPU - Bộ Vi Xử Lý</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-hard-drive item-icon"></i>
                            <span>VGA - Card Màn Hình</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-memory item-icon"></i>
                            <span>RAM & Ổ Cứng SSD</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-tv item-icon"></i>
                            <span>Màn Hình Máy Tính</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-keyboard item-icon"></i>
                            <span>Bàn Phím & Chuột Gear</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-fan item-icon"></i>
                            <span>Tản Nhiệt & Vỏ Case</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="category-nav-item">
                        <a href="#">
                            <i class="fa-solid fa-chair item-icon"></i>
                            <span>Bàn Ghế Gaming / Ergonomic</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Center: Big Hero Showcase Slider -->
            <div class="hero-main-slider">
                <div class="slider-content">
                    <div class="hero-tag">
                        <i class="fa-solid fa-bolt"></i> THẾ HỆ PHẦN CỨNG MỚI NHẤT
                    </div>
                    <h1 class="hero-title">
                        BỨT PHÁ <span>HIỆU NĂNG</span> CÙNG RTX 40 & AI
                    </h1>
                    <p class="hero-desc">
                        Trải nghiệm sức mạnh đồ họa đỉnh cao, công nghệ tản nhiệt buồng hơi độc quyền và cấu hình tối ưu sẵn sàng cho mọi tựa game AAA.
                    </p>
                    <div class="hero-actions">
                        <a href="#" class="btn-primary">
                            <span>MUA NGAY HÔM NAY</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="#" class="btn-secondary">
                            <i class="fa-solid fa-sliders"></i>
                            <span>TÙY BIẾN CẤU HÌNH</span>
                        </a>
                    </div>
                </div>
                <div class="slider-visual">
                    <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&q=80&w=800" alt="TechZone Gaming PC">
                </div>
            </div>

            <!-- Right: Sub Banners -->
            <div class="hero-side-banners">
                <a href="#" class="side-banner-card card-blue">
                    <span class="side-badge">SIÊU PHẨM 2026</span>
                    <div>
                        <h3 class="side-title">Laptop AI Next-Gen</h3>
                        <p class="side-sub">Mỏng nhẹ - Pin trâu - NPU AI tích hợp</p>
                    </div>
                    <span class="side-link">Khám phá ngay <i class="fa-solid fa-arrow-right"></i></span>
                </a>

                <a href="#" class="side-banner-card card-purple">
                    <span class="side-badge">DEAL BÙNG NỔ</span>
                    <div>
                        <h3 class="side-title">Màn Hình OLED 240Hz</h3>
                        <p class="side-sub">Giảm tới 25% + Trả góp 0%</p>
                    </div>
                    <span class="side-link">Xem ưu đãi <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </section>

        <!-- ==================== 2. VALUE PROPOSITIONS ==================== -->
        <section class="features-grid">
            <div class="feature-box">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="feature-title">100% Hàng Chính Hãng</h4>
                    <p class="feature-desc">Cam kết xuất xứ & bảo hành chính hãng</p>
                </div>
            </div>

            <div class="feature-box">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="feature-title">Giao Nhanh 2 Giờ</h4>
                    <p class="feature-desc">Giao hàng hỏa tốc nội thành 0 đồng</p>
                </div>
            </div>

            <div class="feature-box">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h4 class="feature-title">Đổi Mới 30 Ngày</h4>
                    <p class="feature-desc">Lỗi phần cứng do NSX đổi 1-1 ngay</p>
                </div>
            </div>

            <div class="feature-box">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <h4 class="feature-title">Trả Góp 0% Lãi Suất</h4>
                    <p class="feature-desc">Xét duyệt online đơn giản trong 5 phút</p>
                </div>
            </div>
        </section>

        <!-- ==================== 3. FLASH SALE ==================== -->
        <section class="flash-sale-box">
            <div class="flash-sale-header">
                <div class="flash-sale-title">
                    <i class="fa-solid fa-bolt"></i>
                    <span>GIÁ SỐC GIỜ VÀNG - FLASH SALE</span>
                </div>
                <div class="flash-timer">
                    <span>Kết thúc sau:</span>
                    <span class="timer-unit">02</span> :
                    <span class="timer-unit">45</span> :
                    <span class="timer-unit">18</span>
                </div>
            </div>

            <div class="products-grid">
                <!-- Product 1 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-28%</span>
                        <span class="badge-installment">Góp 0%</span>
                    </div>
                    <a href="thongtinsp.php" class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=500" alt="PC Gaming TechZone Pro">
                    </a>
                    <div class="product-details">
                        <span class="product-category-name">PC GAMING</span>
                        <a href="thongtinsp.php" style="text-decoration: none; color: inherit;">
                            <h3 class="product-title">PC TechZone Nova i5 13400F / RTX 4060 8GB / 16GB RGB / 500GB NVMe</h3>
                        </a>
                        <div class="product-specs-list">
                            <span class="spec-chip">Core i5</span>
                            <span class="spec-chip">RTX 4060</span>
                            <span class="spec-chip">16GB RAM</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">18.490.000₫</span>
                            <span class="old-price">25.990.000₫</span>
                        </div>
                        <div class="stock-progress-bar">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 78%;"></div>
                            </div>
                            <div class="progress-label">
                                <span>Đã bán 18/23</span>
                                <span>Sắp hết</span>
                            </div>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-18%</span>
                        <span class="badge-installment">Góp 0%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&q=80&w=500" alt="Laptop Gaming ASUS ROG">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">LAPTOP GAMING</span>
                        <h3 class="product-title">Laptop ASUS ROG Strix G16 Core i7-13650HX / RTX 4060 / 165Hz QHD</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">i7-13650HX</span>
                            <span class="spec-chip">RTX 4060</span>
                            <span class="spec-chip">165Hz</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">34.990.000₫</span>
                            <span class="old-price">42.500.000₫</span>
                        </div>
                        <div class="stock-progress-bar">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 90%;"></div>
                            </div>
                            <div class="progress-label">
                                <span>Đã bán 9/10</span>
                                <span>Hot</span>
                            </div>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-35%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&q=80&w=500" alt="Màn Hình Gaming">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">MÀN HÌNH</span>
                        <h3 class="product-title">Màn hình Gaming LG UltraGear 27GR95QE OLED 240Hz 0.03ms 2K QHD</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">27 inch OLED</span>
                            <span class="spec-chip">240Hz</span>
                            <span class="spec-chip">0.03ms</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">17.890.000₫</span>
                            <span class="old-price">27.500.000₫</span>
                        </div>
                        <div class="stock-progress-bar">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 65%;"></div>
                            </div>
                            <div class="progress-label">
                                <span>Đã bán 13/20</span>
                                <span>Còn ít</span>
                            </div>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-22%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=500" alt="Bàn Phím Cơ Custom">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">GAMING GEAR</span>
                        <h3 class="product-title">Bàn phím cơ không dây TechZone Lumina 75 RGB Hot-swap Gateron Pro</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">3 Mode Wireless</span>
                            <span class="spec-chip">Gasket Mount</span>
                            <span class="spec-chip">RGB</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">1.990.000₫</span>
                            <span class="old-price">2.550.000₫</span>
                        </div>
                        <div class="stock-progress-bar">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 50%;"></div>
                            </div>
                            <div class="progress-label">
                                <span>Đã bán 15/30</span>
                                <span>Vừa mở bán</span>
                            </div>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== 4. FEATURED PC GAMING ==================== -->
        <section class="featured-section">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon-badge">
                        <i class="fa-solid fa-desktop"></i>
                    </div>
                    <h2 class="section-title">DÀN MÁY PC TECHZONE BÁN CHẠY</h2>
                </div>
                <div class="filter-tabs">
                    <button class="tab-btn active">Tất cả cấu hình</button>
                    <button class="tab-btn">Dưới 15 Triệu</button>
                    <button class="tab-btn">15 - 25 Triệu</button>
                    <button class="tab-btn">25 - 40 Triệu</button>
                    <button class="tab-btn">Workstation Đồ Họa</button>
                </div>
                <a href="#" class="section-view-all">Xem tất cả (48) <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="products-grid">
                <!-- Card 1 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-12%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&q=80&w=500" alt="PC Gaming">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">PC GAMING ESPORT</span>
                        <h3 class="product-title">PC TechZone Battle i3 12100F / GTX 1650 4GB / 16GB RAM / 256GB SSD</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">i3-12100F</span>
                            <span class="spec-chip">GTX 1650</span>
                            <span class="spec-chip">16GB</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">9.890.000₫</span>
                            <span class="old-price">11.200.000₫</span>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Chọn cấu hình
                        </button>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-15%</span>
                        <span class="badge-installment">Góp 0%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=500" alt="PC Gaming">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">PC GAMING PRO</span>
                        <h3 class="product-title">PC TechZone Phantom i5 14400F / RTX 4060 Ti 8GB / 32GB RGB / 1TB SSD</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">i5-14400F</span>
                            <span class="spec-chip">RTX 4060 Ti</span>
                            <span class="spec-chip">32GB RAM</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">24.500.000₫</span>
                            <span class="old-price">28.900.000₫</span>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Chọn cấu hình
                        </button>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-10%</span>
                        <span class="badge-installment">Góp 0%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&q=80&w=500" alt="PC Gaming Ryzen">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">PC AMD ADVANTAGE</span>
                        <h3 class="product-title">PC TechZone Apex Ryzen 7 7800X3D / RTX 4070 SUPER 12GB / 32GB DDR5</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">R7 7800X3D</span>
                            <span class="spec-chip">RTX 4070 SUPER</span>
                            <span class="spec-chip">DDR5</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">41.990.000₫</span>
                            <span class="old-price">46.500.000₫</span>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Chọn cấu hình
                        </button>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="product-card">
                    <div class="product-badges">
                        <span class="badge-discount">-8%</span>
                        <span class="badge-installment">Góp 0%</span>
                    </div>
                    <div class="product-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=500" alt="Workstation Đồ Họa">
                    </div>
                    <div class="product-details">
                        <span class="product-category-name">WORKSTATION ULTRA</span>
                        <h3 class="product-title">PC TechZone Creator Core i9 14900K / RTX 4090 24GB / 64GB DDR5 / Water 360</h3>
                        <div class="product-specs-list">
                            <span class="spec-chip">i9-14900K</span>
                            <span class="spec-chip">RTX 4090 24G</span>
                            <span class="spec-chip">64GB RAM</span>
                        </div>
                        <div class="product-pricing">
                            <span class="current-price">86.990.000₫</span>
                            <span class="old-price">94.500.000₫</span>
                        </div>
                        <button type="button" class="product-card-btn">
                            <i class="fa-solid fa-cart-plus"></i> Chọn cấu hình
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== 5. BUILD PC PROMO BANNER ==================== -->
        <section class="build-pc-banner">
            <div class="build-pc-content">
                <span class="build-badge">
                    <i class="fa-solid fa-screwdriver-wrench"></i> CÔNG CỤ THÔNG MINH
                </span>
                <h2 class="build-title">Tự Xây Dựng Cấu Hình PC Theo Chuẩn Chuyên Gia</h2>
                <p class="build-desc">
                    Hệ thống tự động kiểm tra tương thích linh kiện 100%, tính toán công suất nguồn chính xác và gợi ý cấu hình tối ưu nhất trong ngân sách của bạn.
                </p>
                <div class="build-highlights">
                    <div class="build-highlight-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Kiểm tra nghẽn cổ chai & tương thích</span>
                    </div>
                    <div class="build-highlight-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Miễn phí công lắp đặt & cài đặt Win bản quyền</span>
                    </div>
                    <div class="build-highlight-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Bảo hành tận nơi 12 tháng tại nhà</span>
                    </div>
                    <div class="build-highlight-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Tặng combo quà gear trị giá tới 1.500.000đ</span>
                    </div>
                </div>
                <a href="#" class="btn-primary">
                    <i class="fa-solid fa-microchip"></i>
                    <span>BẮT ĐẦU XÂY DỰNG CẤU HÌNH</span>
                </a>
            </div>

            <div class="build-img-wrap">
                <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&q=80&w=700" alt="TechZone PC Builder">
            </div>
        </section>

        <!-- ==================== 6. TECH NEWS / BLOG ==================== -->
        <section class="featured-section">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon-badge">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <h2 class="section-title">TIN TỨC CÔNG NGHỆ & ĐÁNH GIÁ</h2>
                </div>
                <a href="#" class="section-view-all">Xem tất cả bài viết <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="news-grid">
                <a href="#" class="news-card">
                    <div class="news-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&q=80&w=600" alt="Đánh giá RTX Series">
                    </div>
                    <div class="news-meta">
                        <div class="news-date">
                            <i class="fa-regular fa-calendar"></i> 05/10/2026 • Đánh giá phần cứng
                        </div>
                        <h3 class="news-heading">Đánh giá sức mạnh thế hệ GPU mới: Đột phá công nghệ khử răng cưa và AI DLSS 4</h3>
                        <p class="news-excerpt">Thực tế hiệu năng khi chơi game độ phân giải 4K Max Settings cùng hệ thống tản nhiệt tiên tiến...</p>
                    </div>
                </a>

                <a href="#" class="news-card">
                    <div class="news-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=600" alt="Build PC Gaming 2026">
                    </div>
                    <div class="news-meta">
                        <div class="news-date">
                            <i class="fa-regular fa-calendar"></i> 02/10/2026 • Hướng dẫn tư vấn
                        </div>
                        <h3 class="news-heading">Top 5 cấu hình PC Gaming đáng mua nhất trong tầm giá 20 triệu năm 2026</h3>
                        <p class="news-excerpt">Tối ưu chi phí tối đa nhưng vẫn sở hữu card đồ họa RTX mới nhất cùng RAM DDR5 bus cao...</p>
                    </div>
                </a>

                <a href="#" class="news-card">
                    <div class="news-thumb-wrap">
                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=600" alt="Màn hình gaming OLED">
                    </div>
                    <div class="news-meta">
                        <div class="news-date">
                            <i class="fa-regular fa-calendar"></i> 28/09/2026 • Tin công nghệ
                        </div>
                        <h3 class="news-heading">Màn hình OLED 240Hz có thực sự cần thiết cho game thủ Esport hiện nay?</h3>
                        <p class="news-excerpt">Tìm hiểu sự khác biệt giữa tấm nền IPS và OLED về độ trễ, dải màu chuẩn và trải nghiệm thi đấu...</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- ==================== 7. BRAND PARTNERS ==================== -->
        <section class="brands-section">
            <div class="brands-title">ĐỐI TÁC CÔNG NGHỆ CHÍNH HÃNG</div>
            <div class="brands-grid">
                <span class="brand-item">ASUS ROG</span>
                <span class="brand-item">MSI</span>
                <span class="brand-item">GIGABYTE</span>
                <span class="brand-item">INTEL</span>
                <span class="brand-item">AMD</span>
                <span class="brand-item">NVIDIA</span>
                <span class="brand-item">CORSAIR</span>
                <span class="brand-item">RAZER</span>
                <span class="brand-item">KINGSTON</span>
            </div>
        </section>

    </div>
</main>

<?php
// Layout Footer (Giao diện Footer)
include_once __DIR__ . '/footer.php';
?>
