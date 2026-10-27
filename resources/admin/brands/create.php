<?php
// Nhúng thanh header chung của Admin (đã bao gồm brand.css và category.css)
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <!-- 1. Tiêu đề trang & Nút hành động -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="/computer-shop/resources/admin/brands/brands.php" class="text-decoration-none text-muted small">
                    <i class="bi bi-award-fill me-1"></i> Quản lý thương hiệu
                </a>
                <i class="bi bi-chevron-right text-muted small" style="font-size: 0.75rem;"></i>
                <span class="text-primary small fw-semibold">Thêm thương hiệu mới</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Thêm thương hiệu mới</h3>
            <p class="text-muted small mb-0 mt-1">Giao diện khai báo nhà sản xuất và đối tác phân phối tại TECHZONE</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/computer-shop/resources/admin/brands/brands.php" class="btn btn-light rounded-pill px-4 border bg-white shadow-sm fw-medium text-dark text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
            <!-- <button type="submit" form="brandForm" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                <i class="bi bi-cloud-check-fill me-2"></i> Lưu thương hiệu
            </button> -->
        </div>
    </div>

    <!-- 2. Form Giao diện thêm thương hiệu (Thuần HTML / CSS) -->
    <form id="brandForm" action="solveCreate.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Cột trái: Thông tin thương hiệu (col-md-8) -->
            <div class="col-md-8">
                <!-- Khung 1: Thông tin thương hiệu -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-building-fill text-primary me-2 fs-5"></i> Thông tin thương hiệu
                    </div>
                    <div class="card-body-modern">
                        <div class="form-group-custom">
                            <label class="form-label" for="brandName">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-custom" id="brandName" name="name" required
                                   placeholder="Ví dụ: ASUS, Apple, Dell, MSI, Logitech...">
                            <div class="mt-2">
                                <span class="text-muted small me-2" style="font-size: 0.75rem;">Thương hiệu phổ biến:</span>
                                <span class="brand-preset-badge">ASUS</span>
                                <span class="brand-preset-badge">Apple</span>
                                <span class="brand-preset-badge">Dell</span>
                                <span class="brand-preset-badge">MSI</span>
                                <span class="brand-preset-badge">Logitech</span>
                                <span class="brand-preset-badge">LG</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="brandcode">Mã định danh</label>
                                    <div class="position-relative">
                                        <i class="bi bi-tag position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" class="form-control-custom ps-5" id="brandcode" name="code"
                                               placeholder="asus">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="brandCountry">Quốc gia / Xuất xứ</label>
                                    <select class="form-control-custom form-select" id="brandCountry" name="country">
                                        <option value="">-- Chọn quốc gia --</option>
                                        <option value="Đài Loan">🇹🇼 Đài Loan (Taiwan)</option>
                                        <option value="Hoa Kỳ">🇺🇸 Hoa Kỳ (United States)</option>
                                        <option value="Hàn Quốc">🇰🇷 Hàn Quốc (South Korea)</option>
                                        <option value="Nhật Bản">🇯🇵 Nhật Bản (Japan)</option>
                                        <option value="Thụy Sĩ">🇨🇭 Thụy Sĩ (Switzerland)</option>
                                        <option value="Đức">🇩🇪 Đức (Germany)</option>
                                        <option value="Trung Quốc">🇨🇳 Trung Quốc (China)</option>
                                        <option value="Việt Nam">🇻🇳 Việt Nam</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label" for="brandWebsite">Website chính thức của hãng</label>
                            <div class="position-relative">
                                <i class="bi bi-globe position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="url" class="form-control-custom ps-5" id="brandWebsite" name="website"
                                       placeholder="https://www.asus.com">
                            </div>
                        </div>

                        <div class="form-group-custom mb-0">
                            <label class="form-label" for="brandDesc">Mô tả & Giới thiệu về thương hiệu</label>
                            <textarea class="form-control-custom" id="brandDesc" name="description" rows="4"
                                      placeholder="Thông tin tổng quan về thế mạnh các dòng sản phẩm, công nghệ độc quyền của hãng..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Khung 2: Chính sách bảo hành & Hỗ trợ kỹ thuật -->
                <!-- <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-shield-check text-success me-2 fs-5"></i> Chính sách bảo hành & Hỗ trợ kỹ thuật
                    </div>
                    <div class="card-body-modern">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="warrantyPeriod">Thời hạn bảo hành tiêu chuẩn</label>
                                    <select class="form-control-custom form-select" id="warrantyPeriod" name="warranty_period">
                                        <option value="12">12 Tháng (1 Năm)</option>
                                        <option value="24" selected>24 Tháng (2 Năm)</option>
                                        <option value="36">36 Tháng (3 Năm)</option>
                                        <option value="60">60 Tháng (5 Năm)</option>
                                        <option value="lifetime">Bảo hành trọn đời (Lifetime)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="supportHotline">Tổng đài hỗ trợ hãng (Hotline)</label>
                                    <div class="position-relative">
                                        <i class="bi bi-telephone position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" class="form-control-custom ps-5" id="supportHotline" name="support_hotline"
                                               placeholder="1800-xxxx hoặc 1900-xxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-custom mb-0">
                            <label class="form-label" for="warrantyAddress">Địa chỉ trung tâm bảo hành</label>
                            <textarea class="form-control-custom" id="warrantyAddress" name="warranty_address" rows="3"
                                      placeholder="Ví dụ: Trung tâm bảo hành ủy quyền tại Hà Nội, TP.HCM, Đà Nẵng..."></textarea>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Cột phải: Logo & Trạng thái (col-md-4) -->
            <div class="col-md-4">
                <!-- Khung 3: Logo thương hiệu -->
                <!-- <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-image text-danger me-2 fs-5"></i> Logo thương hiệu
                    </div>
                    <div class="card-body-modern">
                        <div class="dropzone-custom d-flex flex-column align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-cloud-arrow-up dropzone-icon"></i>
                                <h6 class="fw-bold mb-1 text-dark">Tải logo thương hiệu</h6>
                                <p class="text-muted small mb-2">Nhấp hoặc kéo thả file ảnh vào đây</p>
                                <span class="badge bg-primary rounded-pill px-3 py-1 fw-medium">
                                    <i class="bi bi-upload me-1"></i> Tải ảnh lên
                                </span>
                                <div class="text-muted mt-2" style="font-size: 0.72rem;">Khuyến nghị file PNG nền trong suốt, tối đa 2MB.</div>
                            </div>
                            <input type="file" name="logo" class="d-none" accept="image/*">
                        </div>
                    </div>
                </div> -->

                <!-- Khung 4: Trạng thái & Phân loại đối tác -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-sliders text-warning me-2 fs-5"></i> Cài đặt hợp tác
                    </div>
                    <div class="card-body-modern">
                        <!-- Switch 1: Trạng thái kinh doanh -->
                        <div class="switch-container">
                            <div>
                                <div class="switch-label">Đang kinh doanh</div>
                                <div class="switch-desc">Sản phẩm hãng được bán tại shop</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <!-- Switch 2: Đối tác chính hãng -->
                        <!-- <div class="switch-container">
                            <div>
                                <div class="switch-label">Đối tác chính hãng</div>
                                <div class="switch-desc">Gắn huy hiệu Authorized Partner</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="is_official" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div> -->

                        <!-- Switch 3: Hiển thị logo trang chủ -->
                        <!-- <div class="switch-container">
                            <div>
                                <div class="switch-label">Thanh logo trang chủ</div>
                                <div class="switch-desc">Hiển thị trong slider thương hiệu</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="show_home" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div> -->

                        <!-- Thứ tự sắp xếp -->
                        <div class="form-group-custom mt-3 mb-0">
                            <label class="form-label" for="brandOrder">Thứ tự hiển thị ưu tiên</label>
                            <div class="position-relative">
                                <i class="bi bi-sort-numeric-down position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="number" class="form-control-custom ps-5" id="brandOrder" name="display_order" value="1" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Nút Lưu / Hủy cuối trang -->
        <div class="d-flex justify-content-end gap-2 mt-2 mb-5">
            <a href="/computer-shop/resources/admin/brands/brands.php" class="btn btn-light rounded-pill px-4 border text-dark text-decoration-none fw-medium">
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-medium">
                <i class="bi bi-cloud-check-fill me-2"></i> Lưu thương hiệu
            </button>
        </div>
    </form>
</div>

<!-- =========================================================================
     JAVASCRIPT: ĐÃ COMMENT ĐỂ GIỮ GIAO DIỆN THUẦN HTML / CSS THEO YÊU CẦU
     =========================================================================
<script>
// Logic JavaScript (nếu cần xử lý tương tác sau này):
// - Tự động tạo code thương hiệu
// - Chọn nhanh thương hiệu mẫu
// - Xem trước logo khi upload
</script>
========================================================================== -->

<?php
include __DIR__ . '/../layouts/footer.php';
?>
