<?php
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý danh mục</h4>
            <div class="text-muted small">Phân loại cấu trúc sản phẩm linh kiện, thiết bị và máy tính TECHZONE.</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickCategoryModal" onclick="openQuickCategoryModal()">
                <i class="bi bi-lightning-charge me-1"></i> Thêm nhanh
            </button>
            <a href="create.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm danh mục mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Tổng danh mục</div>
                        <div class="stats-value">12</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-primary rounded">
                        <i class="bi bi-diagram-3-fill fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Đang hiển thị</div>
                        <div class="stats-value text-success">10</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-success rounded">
                        <i class="bi bi-eye-fill fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Danh mục nổi bật</div>
                        <div class="stats-value text-warning">5</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-warning rounded">
                        <i class="bi bi-star-fill fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title text-danger">Tạm ẩn</div>
                        <div class="stats-value text-danger">2</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-danger rounded">
                        <i class="bi bi-eye-slash-fill fs-4 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card mb-4">
        <!-- Toolbar Bộ lọc gửi request GET -->
        <form action="index.php" method="GET" class="table-toolbar d-flex justify-content-between align-items-center">
            <div class="position-relative" style="width: 320px;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                <input type="text" name="search" class="form-control ps-5" placeholder="Tìm kiếm tên danh mục, slug...">
            </div>
            <div class="d-flex gap-2">
                <select name="status" class="form-select" style="width: 180px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1">Đang hiển thị</option>
                    <option value="0">Tạm ẩn</option>
                </select>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table product-table mb-0 table-hover align-middle">
                <thead>
                    <tr>
                        <th width="70">ICON</th>
                        <th>TÊN DANH MỤC</th>
                        <th>ĐƯỜNG DẪN (SLUG)</th>
                        <th>DANH MỤC CHA</th>
                        <th>SỐ SẢN PHẨM</th>
                        <th>THỨ TỰ</th>
                        <th>TRẠNG THÁI</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="p-2 rounded bg-light text-primary text-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-laptop fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">Laptop Gaming</div>
                            <div class="text-muted small">Dòng máy tính xách tay cấu hình cao chuyên chơi game</div>
                        </td>
                        <td><code>laptop-gaming</code></td>
                        <td><span class="badge bg-light text-dark border">Danh mục gốc</span></td>
                        <td><span class="fw-semibold">142</span> sản phẩm</td>
                        <td>1</td>
                        <td><span class="badge bg-success">Đang hiển thị</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="create.php" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <button class="action-btn delete border-0 bg-transparent" title="Xóa"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="p-2 rounded bg-light text-primary text-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-pc-display fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">PC Đồng Bộ</div>
                            <div class="text-muted small">Máy tính để bàn nguyên bộ từ hãng ASUS, Dell, HP</div>
                        </td>
                        <td><code>pc-dong-bo</code></td>
                        <td><span class="badge bg-light text-dark border">Danh mục gốc</span></td>
                        <td><span class="fw-semibold">89</span> sản phẩm</td>
                        <td>2</td>
                        <td><span class="badge bg-success">Đang hiển thị</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="create.php" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <button class="action-btn delete border-0 bg-transparent" title="Xóa"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="p-2 rounded bg-light text-primary text-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-cpu fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">Linh Kiện Máy Tính</div>
                            <div class="text-muted small">CPU, GPU, RAM, Ổ cứng SSD, Mainboard, Nguồn</div>
                        </td>
                        <td><code>linh-kien-may-tinh</code></td>
                        <td><span class="badge bg-light text-dark border">Danh mục gốc</span></td>
                        <td><span class="fw-semibold">320</span> sản phẩm</td>
                        <td>3</td>
                        <td><span class="badge bg-success">Đang hiển thị</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="create.php" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <button class="action-btn delete border-0 bg-transparent" title="Xóa"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="p-2 rounded bg-light text-primary text-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-display fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">Màn Hình Máy Tính</div>
                            <div class="text-muted small">Màn hình gaming 144Hz-240Hz, màn hình đồ họa 2K-4K</div>
                        </td>
                        <td><code>man-hinh-may-tinh</code></td>
                        <td><span class="badge bg-light text-dark border">Danh mục gốc</span></td>
                        <td><span class="fw-semibold">76</span> sản phẩm</td>
                        <td>4</td>
                        <td><span class="badge bg-success">Đang hiển thị</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="create.php" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <button class="action-btn delete border-0 bg-transparent" title="Xóa"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="p-2 rounded bg-light text-muted text-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-headset fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-muted">Gaming Gear Tồn Kho</div>
                            <div class="text-muted small">Danh mục tạm ngưng trưng bày</div>
                        </td>
                        <td><code>gaming-gear-ton-kho</code></td>
                        <td><span class="badge bg-light text-dark border">Gaming Gear</span></td>
                        <td><span class="fw-semibold">0</span> sản phẩm</td>
                        <td>99</td>
                        <td><span class="badge bg-danger">Tạm ẩn</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="create.php" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <button class="action-btn delete border-0 bg-transparent" title="Xóa"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Add Category Modal (Frontend Only) -->
<div class="modal" id="quickCategoryModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <form action="create.php" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Thêm nhanh danh mục</h5>
                <button type="button" class="btn-close" onclick="closeQuickCategoryModal()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục (VD: Chuột Gaming)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Danh mục cha</label>
                    <select name="parent_id" class="form-select">
                        <option value="0">-- Danh mục gốc --</option>
                        <option value="1">Laptop & Máy tính xách tay</option>
                        <option value="2">PC & Máy trạm</option>
                        <option value="3">Gaming Gear & Phụ kiện</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1">Hiển thị ngay</option>
                        <option value="0">Tạm ẩn</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light rounded-pill px-4 border" onclick="closeQuickCategoryModal()">Đóng</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu lại
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT: ĐÃ COMMENT ĐỂ GIỮ GIAO DIỆN THUẦN HTML / CSS THEO YÊU CẦU
     =========================================================================
<script>
function openQuickCategoryModal() {
    document.getElementById('quickCategoryModal').style.display = 'block';
}
function closeQuickCategoryModal() {
    document.getElementById('quickCategoryModal').style.display = 'none';
}
</script>
========================================================================== -->

<?php
include __DIR__ . '/../layouts/footer.php';
?>
