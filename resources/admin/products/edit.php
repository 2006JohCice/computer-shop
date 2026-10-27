<?php
require_once __DIR__ . '/../../../config/config.php';
include __DIR__ . '/../layouts/header.php';

// Lấy ID sản phẩm từ URL (edit.php?id=...)
$id = $_GET['id'] ?? '';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $conn->connect_error);
}

// Đọc dữ liệu sản phẩm từ CSDL
$product = [];
if (!empty($id)) {
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
}
?>
<style>
    .form-group-custom {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-custom {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        width: 100%;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .card-modern {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header-modern {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
        font-weight: 600;
        color: #1e293b;
    }

    .card-body-modern {
        padding: 1.5rem;
    }

    .dropzone-custom {
        border: 2px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }

    .dropzone-custom:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .dropzone-icon {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Chỉnh sửa sản phẩm</h3>
        </div>
        <a href="index.php"
            class="btn btn-light rounded-pill px-4 border bg-white shadow-sm fw-medium text-dark text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Form chỉnh sửa chuyển dữ liệu đến update.php -->
    <form action="update.php" method="POST" enctype="multipart/form-data">
        <!-- Input ẩn chứa ID của sản phẩm -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id'] ?? $id) ?>">

        <!-- Box 1: Thông tin cơ bản -->
        <div class="card-modern">
            <div class="card-header-modern"><i class="bi bi-info-circle me-2 text-primary"></i> Thông tin cơ bản</div>
            <div class="card-body-modern">
                <div class="form-group-custom">
                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-custom" name="name" required
                        value="<?= htmlspecialchars($product['name'] ?? '') ?>"
                        placeholder="Nhập tên sản phẩm (VD: Laptop Gaming Acer Nitro 5)">
                </div>
                <div class="form-group-custom mb-0">
                    <label class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control-custom" name="description" rows="5"
                        placeholder="Nhập mô tả chi tiết, điểm nổi bật của sản phẩm..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Box 2: Giá & Tồn kho -->
        <div class="card-modern">
            <div class="card-header-modern"><i class="bi bi-tag me-2 text-success"></i> Định giá & Kho hàng</div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-custom mb-md-0">
                            <label class="form-label">Giá bán (₫) <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <span
                                    class="position-absolute top-50 translate-middle-y ms-3 text-muted fw-bold">₫</span>
                                <input type="number" class="form-control-custom ps-5 fw-bold text-primary" name="price"
                                    required value="<?= htmlspecialchars($product['price'] ?? 0) ?>" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-custom mb-0">
                            <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i
                                    class="bi bi-box-seam position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="number" class="form-control-custom ps-5" name="stock" required
                                    value="<?= htmlspecialchars($product['stock'] ?? 0) ?>" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box 3: Phân loại & Hình ảnh -->
        <div class="card-modern">
            <div class="card-header-modern"><i class="bi bi-box me-2 text-warning"></i> Phân loại & Hình ảnh</div>
            <div class="card-body-modern">
                <div class="row">
                    <!-- Cột trái: Phân loại -->
                    <div class="col-md-6 pe-md-4">
                        <div class="form-group-custom">
                            <label class="form-label">Mã SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-custom bg-light" name="sku" required
                                value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: SP-001">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label">Thương hiệu</label>
                            <input type="text" class="form-control-custom" name="brand"
                                value="<?= htmlspecialchars($product['brand'] ?? '') ?>"
                                placeholder="Ví dụ: ASUS, Apple...">
                        </div>
                        <div class="form-group-custom mb-0">
                            <label class="form-label">Trạng thái bán</label>
                            <select class="form-control-custom bg-white" name="status">
                                <option value="1" <?= (($product['status'] ?? '') == 1 || ($product['status'] ?? '') === 'active') ? 'selected' : '' ?>> Đang kinh doanh</option>
                                <option value="0" <?= (($product['status'] ?? '') == 0 || ($product['status'] ?? '') === 'inactive') ? 'selected' : '' ?>> Ngừng kinh doanh</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cột phải: Hình ảnh -->
                    <div
                        class="col-md-6 ps-md-4 mt-4 mt-md-0 border-start-md d-flex justify-content-center align-items-center">
                        <div class="dropzone-custom d-flex flex-column align-items-center justify-content-center"
                            style="width: 240px; height: 240px; padding: 1rem;"
                            onclick="document.getElementById('productImage').click()">
                            <i class="bi bi-cloud-arrow-up dropzone-icon mb-2" style="font-size: 2.5rem;"></i>
                            <h6 class="fw-bold mb-1 text-dark">Thay đổi ảnh</h6>
                            <p class="text-muted small mb-2">Click để chọn tệp mới</p>
                            <input type="file" class="d-none" id="productImage" name="image" accept="image/*"
                                onchange="document.getElementById('uploadText').innerText = this.files[0] ? this.files[0].name : 'Chưa có file'">
                            <div class="mb-2">
                                <span id="uploadText"
                                    class="badge bg-primary rounded-pill px-3 py-1 fw-medium text-truncate"
                                    style="max-width: 150px;"><?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'Giữ nguyên ảnh cũ' ?></span>
                            </div>
                            <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">Hỗ trợ JPG, PNG,
                                WEBP.<br>Tối đa 5MB.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-2 mb-5">
            <a href="index.php"
                class="btn btn-light rounded-pill px-4 me-2 border fw-medium text-dark text-decoration-none">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                <i class="bi bi-check-lg me-2"></i> Cập nhật
            </button>
        </div>
    </form>
</div>
<?php
include __DIR__ . '/../layouts/footer.php';
?>
