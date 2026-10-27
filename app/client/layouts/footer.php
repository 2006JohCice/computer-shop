<?php
// app/views/client/layouts/footer.php
?>
    <!-- CLIENT FOOTER (REDESIGNED LUXURY & COMPACT) -->
    <footer class="tz-client-footer">
        <!-- 1. Dải Cam Kết Thương Hiệu Vàng (Trust Guarantees Strip) -->
        <div class="tz-footer-trust-strip">
            <div class="container">
                <div class="row g-3">
                    <div class="col-xl-3 col-md-6">
                        <div class="tz-trust-item">
                            <div class="tz-trust-icon"><i class="bi bi-patch-check-fill"></i></div>
                            <div class="tz-trust-text">
                                <strong>100% Laptop Chính Hãng</strong>
                                <span>Phân phối ủy quyền, hóa đơn VAT</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="tz-trust-item">
                            <div class="tz-trust-icon"><i class="bi bi-truck-front-fill"></i></div>
                            <div class="tz-trust-text">
                                <strong>Giao Hàng Siêu Tốc 2H</strong>
                                <span>Miễn phí nội thành từ 10 triệu</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="tz-trust-item">
                            <div class="tz-trust-icon"><i class="bi bi-arrow-repeat"></i></div>
                            <div class="tz-trust-text">
                                <strong>Đổi Mới 1 - Đổi - 1 Trong 30 Ngày</strong>
                                <span>Yên tâm khi gặp lỗi phần cứng NSX</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="tz-trust-item">
                            <div class="tz-trust-icon"><i class="bi bi-shield-lock-fill"></i></div>
                            <div class="tz-trust-text">
                                <strong>Bảo Hành Điện Tử Tận Nơi</strong>
                                <span>Tra cứu Serial/IMEI tức thì</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Thân Footer 4 Cột -->
        <div class="tz-footer-main">
            <div class="container">
                <div class="row g-4">
                    <!-- Cột 1: Thông tin thương hiệu -->
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="tz-brand-icon-box" style="width: 34px; height: 34px; font-size: 17px;">
                                <i class="bi bi-cpu-fill"></i>
                            </div>
                            <span class="fs-5 fw-bold text-white">TECH<span class="text-primary">ZONE</span></span>
                        </div>
                        <p class="small text-secondary mb-3 pe-lg-3" style="line-height: 1.6;">
                            Hệ thống bán lẻ & dịch vụ máy tính, laptop chính hãng hàng đầu Việt Nam. Tích hợp quản lý định danh Serial/IMEI, xuất hóa đơn VAT điện tử và tra cứu bảo hành tức thì.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="#" class="tz-footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="tz-footer-social-btn" title="YouTube"><i class="bi bi-youtube"></i></a>
                            <a href="#" class="tz-footer-social-btn" title="TikTok"><i class="bi bi-tiktok"></i></a>
                            <a href="#" class="tz-footer-social-btn" title="Zalo Chat"><i class="bi bi-chat-dots-fill"></i></a>
                        </div>
                    </div>

                    <!-- Cột 2: Danh mục sản phẩm -->
                    <div class="col-lg-2 col-md-6">
                        <div class="tz-footer-heading">Danh Mục Laptop</div>
                        <ul class="tz-footer-links">
                            <li><a href="<?= BASE_URL ?>/client/index.php?dm=gaming"><i class="bi bi-chevron-right text-primary"></i>Laptop Gaming</a></li>
                            <li><a href="<?= BASE_URL ?>/client/index.php?dm=van-phong"><i class="bi bi-chevron-right text-primary"></i>Laptop Văn Phòng</a></li>
                            <li><a href="<?= BASE_URL ?>/client/index.php?dm=do-hoa"><i class="bi bi-chevron-right text-primary"></i>Laptop Đồ Họa</a></li>
                            <li><a href="<?= BASE_URL ?>/client/index.php?dm=doanh-nhan"><i class="bi bi-chevron-right text-primary"></i>Laptop Doanh Nhân</a></li>
                            <li><a href="<?= BASE_URL ?>/client/index.php"><i class="bi bi-chevron-right text-primary"></i>Tất cả thiết bị</a></li>
                        </ul>
                    </div>

                    <!-- Cột 3: Chính sách & Bảo hành -->
                    <div class="col-lg-3 col-md-6">
                        <div class="tz-footer-heading">Chính Sách & Dịch Vụ</div>
                        <ul class="tz-footer-links">
                            <li><a href="<?= BASE_URL ?>/client/tra-cuu.php"><i class="bi bi-chevron-right text-primary"></i>Tra cứu Đơn hàng & Hủy đơn</a></li>
                            <li><a href="<?= BASE_URL ?>/client/tra-cuu.php"><i class="bi bi-chevron-right text-primary"></i>Kiểm tra Bảo hành Serial/IMEI</a></li>
                            <li><a href="#"><i class="bi bi-chevron-right text-primary"></i>Chính sách Đổi trả 1-đổi-1</a></li>
                            <li><a href="#"><i class="bi bi-chevron-right text-primary"></i>Phương thức Thanh toán & Trả góp</a></li>
                            <li><a href="/computer-shop/app/admin/dashboard/dashboard.php"><i class="bi bi-chevron-right text-warning"></i>Cổng Quản Trị Hệ Thống</a></li>
                        </ul>
                    </div>

                    <!-- Cột 4: Tổng đài hỗ trợ -->
                    <div class="col-lg-3 col-md-6">
                        <div class="tz-footer-heading">Tổng Đài & Showroom</div>
                        <div class="mb-3">
                            <small class="text-secondary d-block" style="font-size: 11.5px;">Mua hàng & Tư vấn kỹ thuật (Miễn phí):</small>
                            <a href="tel:19008899" class="fs-5 fw-bold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                <i class="bi bi-telephone-inbound-fill"></i>1900 8899
                            </a>
                        </div>
                        <div class="mb-3">
                            <small class="text-secondary d-block" style="font-size: 11.5px;">Hỗ trợ Bảo hành & Khiếu nại:</small>
                            <a href="tel:18006688" class="fw-bold text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                <i class="bi bi-headset"></i>1800 6688
                            </a>
                        </div>
                        <div class="small text-secondary mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>Hà Nội: 123 Đường Láng, Đống Đa
                        </div>
                        <div class="small text-secondary">
                            <i class="bi bi-envelope-at me-1"></i>support@techzone.vn
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dải Bản Quyền Dưới Cùng (Bottom Copyright Strip) -->
        <div class="tz-footer-bottom">
            <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    &copy; <?= date('Y') ?> TECHZONE. Hệ thống Bán lẻ & Dịch vụ Laptop Chính Hãng.
                </div>
                <div class="d-flex align-items-center gap-2 text-secondary small">
                    <span>Đối tác thanh toán:</span>
                    <span class="badge bg-light text-dark border">VNPAY</span>
                    <span class="badge bg-light text-dark border">MOMO</span>
                    <span class="badge bg-light text-dark border">SEPAY</span>
                    <span class="badge bg-light text-dark border">VISA</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- FLOATING CONTACT WIDGET (Góc Trái - Zalo & Phone 0569847809) -->
    <div class="tz-floating-contact" aria-label="Kênh liên hệ hỗ trợ nhanh">
        <!-- Nút Gọi Hotline -->
        <a href="tel:0569847809" class="tz-float-btn tz-float-phone" title="Gọi ngay Hotline: 0569 847 809">
            <div class="tz-float-pulse"></div>
            <div class="tz-float-icon">
                <i class="bi bi-telephone-fill"></i>
            </div>
            <span class="tz-float-label">
                <strong>0569 847 809</strong>
                <small>Hotline tư vấn 24/7</small>
            </span>
        </a>

        <!-- Nút Chat Zalo -->
        <a href="https://zalo.me/0569847809" target="_blank" rel="noopener noreferrer" class="tz-float-btn tz-float-zalo" title="Chat Zalo tư vấn: 0569 847 809">
            <div class="tz-float-pulse"></div>
            <div class="tz-float-icon">
                <svg viewBox="0 0 48 48" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4C12.95 4 4 12.06 4 22C4 26.54 5.92 30.68 9.15 33.87C8.44 36.96 7.02 39.77 4.88 41.97C4.54 42.32 4.71 42.91 5.18 43C9.44 43.83 14.28 42.18 17.65 39.46C19.64 39.82 21.76 40 24 40C35.05 40 44 31.94 44 22C44 12.06 35.05 4 24 4Z" fill="white"/>
                    <path d="M19.5 27.5H13.5V25.3L17.8 19.7H13.5V17.5H19.5V19.7L15.2 25.3H19.5V27.5ZM24.5 27.5H22.5V17.5H24.5V27.5ZM30.5 27.5H28.5L25.8 21.5V27.5H23.8V17.5H25.8L28.5 23.5V17.5H30.5V27.5ZM35.5 27.5C33.3 27.5 31.5 25.7 31.5 22.5C31.5 19.3 33.3 17.5 35.5 17.5C37.7 17.5 39.5 19.3 39.5 22.5C39.5 25.7 37.7 27.5 35.5 27.5ZM35.5 19.5C34.4 19.5 33.5 20.8 33.5 22.5C33.5 24.2 34.4 25.5 35.5 25.5C36.6 25.5 37.5 24.2 37.5 22.5C37.5 20.8 36.6 19.5 35.5 19.5Z" fill="#0068FF"/>
                </svg>
            </div>
            <span class="tz-float-label">
                <strong>Chat Zalo</strong>
                <small>0569 847 809</small>
            </span>
        </a>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Notification & Dialog System -->
    <script src="<?= BASE_URL ?>/js/techzone-notify.js?v=<?= time() ?>"></script>

    <!-- Client Script -->
    <script src="<?= BASE_URL ?>/js/client.js?v=<?= time() ?>"></script>
</body>
</html>
