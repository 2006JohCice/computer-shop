<?php
// Nhúng thanh header chung của Admin (đã bao gồm category.css và brand.css)
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <!-- 1. Tiêu đề trang & Nút hành động -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="/computer-shop/resources/admin/categories/categories.php" class="text-decoration-none text-muted small">
                    <i class="bi bi-diagram-3-fill me-1"></i> Quản lý danh mục
                </a>
                <i class="bi bi-chevron-right text-muted small" style="font-size: 0.75rem;"></i>
                <span class="text-primary small fw-semibold">Thêm danh mục mới</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Thêm danh mục sản phẩm</h3>
            <p class="text-muted small mb-0 mt-1">Thêm mới danh mục phân loại thiết bị công nghệ TECHZONE</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/computer-shop/resources/admin/categories/categories.php" class="btn btn-light rounded-pill px-4 border bg-white shadow-sm fw-medium text-dark text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- 2. Form Giao diện thêm danh mục (Thuần HTML / CSS) -->
    <form id="categoryForm" action="solveCreate.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Cột trái: Thông tin danh mục chính (col-md-8) -->
            <div class="col-md-8">
                <!-- Khung 1: Thông tin cơ bản -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-info-circle-fill text-primary me-2 fs-5"></i> Thông tin cơ bản
                    </div>
                    <div class="card-body-modern">
                        <div class="form-group-custom">
                            <label class="form-label" for="categoryName">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-custom" id="categoryName" name="name" required
                                   placeholder="Ví dụ: Laptop Gaming, PC Đồng bộ, Bàn phím cơ...">
                          
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="categorySlug">Đường dẫn Slug (URL tĩnh)</label>
                                    <input type="text" class="form-control-custom" id="categorySlug" name="slug"
                                           placeholder="laptop-gaming">
                                    <div class="slug-preview">
                                    <i class="bi bi-link-45deg text-muted"></i>
                                    <span>Đường dẫn hiển thị: <code>https://techzone.vn/danh-muc/</code><span class="slug-highlight">laptop-gaming</span></span>
                                  </div>   
                                </div>
                                  
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label" for="parentCategory">Danh mục cha</label>
                                    <select class="form-control-custom form-select" id="parentCategory" name="parent_id">
                                        <option value="0">-- Danh mục gốc (Cấp cao nhất) --</option>
                                        <option value="1">Laptop & Máy tính xách tay</option>
                                        <option value="2">PC & Máy trạm</option>
                                        <option value="3">Linh kiện máy tính</option>
                                        <option value="4">Màn hình máy tính</option>
                                        <option value="5">Gaming Gear & Phụ kiện</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-custom mb-0">
                            <label class="form-label" for="categoryDesc">Mô tả danh mục</label>
                            <textarea class="form-control-custom" id="categoryDesc" name="description" rows="4"
                                      placeholder="Nhập mô tả ngắn giới thiệu các dòng sản phẩm thuộc danh mục này..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Khung 2: Cấu hình SEO Google -->
                <!-- <div class="card-modern">
                    <div class="card-header-modern justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-google text-success me-2 fs-5"></i> Cấu hình & Xem trước SEO
                        </div>
                        <span class="badge bg-light text-muted border">Tùy chọn</span>
                    </div>
                    <div class="card-body-modern">
                        <div class="form-group-custom">
                            <label class="form-label" for="metaTitle">Tiêu đề SEO (Meta Title)</label>
                            <input type="text" class="form-control-custom" id="metaTitle" name="meta_title"
                                   placeholder="Tiêu đề hiển thị trên thanh tìm kiếm Google...">
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label" for="metaKeywords">Từ khóa SEO (Keywords)</label>
                            <input type="text" class="form-control-custom" id="metaKeywords" name="meta_keywords"
                                   placeholder="laptop gaming, pc đồ họa, phụ kiện máy tính...">
                        </div>

                        <div>
                            <div class="form-label mb-2"><i class="bi bi-eye me-1"></i> Khung mô phỏng Google Search</div>
                            <div class="seo-preview-box">
                                <div class="seo-title">Laptop Gaming Chính Hãng - Giá Tốt Nhất Tại TECHZONE</div>
                                <div class="seo-url">https://techzone.vn › danh-muc › laptop-gaming</div>
                                <div class="seo-desc">Khám phá các sản phẩm công nghệ chất lượng cao, bảo hành chính hãng và nhiều chương trình ưu đãi độc quyền tại TECHZONE.</div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Cột phải: Cài đặt hiển thị & Icon/Ảnh (col-md-4) -->
            <div class="col-md-4">
                <!-- Khung 3: Cài đặt hiển thị -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-sliders text-warning me-2 fs-5"></i> Cài đặt hiển thị
                    </div>
                    <div class="card-body-modern">
                        <!-- Switch 1: Kích hoạt -->
                        <div class="switch-container">
                            <div>
                                <div class="switch-label">Trạng thái kích hoạt</div>
                                <div class="switch-desc">Hiển thị danh mục trên website</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <!-- Switch 2: Ghim Menu chính -->
                        <!-- <div class="switch-container">
                            <div>
                                <div class="switch-label">Ghim Menu chính</div>
                                <div class="switch-desc">Xuất hiện trên thanh điều hướng đầu trang</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="show_in_menu" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div> -->

                        <!-- Switch 3: Danh mục nổi bật -->
                        <div class="switch-container">
                            <div>
                                <div class="switch-label">Danh mục Nổi bật</div>
                                <div class="switch-desc">Có huy hiệu HOT trên trang chủ</div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="is_featured" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <!-- Thứ tự hiển thị -->
                        <div class="form-group-custom mt-3 mb-0">
                            <label class="form-label" for="displayOrder">Thứ tự ưu tiên sắp xếp</label>
                            <div class="position-relative">
                                <i class="bi bi-sort-numeric-down position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="number" class="form-control-custom ps-5" id="displayOrder" name="display_order" value="1" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khung 4: Chọn Icon đại diện -->
                <!-- <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-emoji-smile text-info me-2 fs-5"></i> Chọn Icon đại diện
                    </div>
                    <div class="card-body-modern">
                        <div class="icon-picker-grid mb-3">
                            <div class="icon-pill active">
                                <i class="bi bi-laptop"></i>
                                <span>Laptop</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-pc-display"></i>
                                <span>PC Bộ</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-cpu"></i>
                                <span>Linh kiện</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-display"></i>
                                <span>Màn hình</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-mouse"></i>
                                <span>Chuột</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-keyboard"></i>
                                <span>Bàn phím</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-headphones"></i>
                                <span>Tai nghe</span>
                            </div>
                            <div class="icon-pill">
                                <i class="bi bi-controller"></i>
                                <span>Tay cầm</span>
                            </div>
                        </div>
                        <input type="hidden" name="icon" value="bi-laptop">
                    </div>
                </div> -->

                <!-- Khung 5: Ảnh đại diện danh mục -->
                <!-- <div class="card-modern">
                    <div class="card-header-modern">
                        <i class="bi bi-image text-danger me-2 fs-5"></i> Ảnh đại diện danh mục
                    </div>
                    <div class="card-body-modern">
                        <div class="dropzone-custom d-flex flex-column align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-cloud-arrow-up dropzone-icon"></i>
                                <h6 class="fw-bold mb-1 text-dark">Tải ảnh danh mục</h6>
                                <p class="text-muted small mb-2">Nhấp hoặc kéo thả tệp vào đây</p>
                                <span class="badge bg-primary rounded-pill px-3 py-1 fw-medium">
                                    <i class="bi bi-upload me-1"></i> Chọn file
                                </span>
                                <div class="text-muted mt-2" style="font-size: 0.72rem;">Hỗ trợ PNG, JPG, WEBP. Tối đa 2MB.</div>
                            </div>
                            <input type="file" name="image" class="d-none" accept="image/*">
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- 3. Nút Lưu / Hủy cuối trang -->
        <div class="d-flex justify-content-end gap-2 mt-2 mb-5">
            <a href="/computer-shop/resources/admin/categories/categories.php" class="btn btn-light rounded-pill px-4 border text-dark text-decoration-none fw-medium">
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-medium">
                <i class="bi bi-cloud-check-fill me-2"></i> Lưu danh mục
            </button>
        </div>
    </form>
</div>

<!--
<script>
// Logic JavaScript (nếu cần xử lý tương tác sau này):
// - Tự động tạo slug khi gõ tên danh mục
// - Chọn icon đại diện
// - Xem trước hình ảnh khi upload
</script>
//

<?php
include __DIR__ . '/../layouts/footer.php';
?>
