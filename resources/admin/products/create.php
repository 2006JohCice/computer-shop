<?php
require_once __DIR__ . '/../../../config/config.php';

// Khởi tạo kết nối CSDL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Khởi tạo biến lưu dữ liệu và thông báo lỗi
$errors = [];
$name = '';
$description = '';
$price = '';
$stock = '';
$sku = '';
$brand = '';
$status = '1';

// Xử lý khi submit form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy dữ liệu và làm sạch dữ liệu đầu vào
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $rawStatus = $_POST['status'] ?? '1';
    $status = ($rawStatus === '1' || $rawStatus === 'active') ? '1' : '0';

    // 2. Kiểm tra tính hợp lệ (Validation)
    // Tên sản phẩm
    if ($name === '') {
        $errors[] = "Vui lòng nhập tên sản phẩm.";
    } elseif (mb_strlen($name) > 250) {
        $errors[] = "Tên sản phẩm không được vượt quá 250 ký tự.";
    }

    // Mã SKU
    if ($sku === '') {
        $errors[] = "Vui lòng nhập mã SKU.";
    } elseif (mb_strlen($sku) > 50) {
        $errors[] = "Mã SKU không được vượt quá 50 ký tự.";
    } else {
        // Kiểm tra mã SKU đã tồn tại chưa (SKU là UNIQUE trong database)
        $checkSkuStmt = $conn->prepare("SELECT id FROM products WHERE sku = ? LIMIT 1");
        if ($checkSkuStmt) {
            $checkSkuStmt->bind_param("s", $sku);
            $checkSkuStmt->execute();
            $skuResult = $checkSkuStmt->get_result();
            if ($skuResult && $skuResult->num_rows > 0) {
                $errors[] = "Mã SKU '{$sku}' đã tồn tại trong hệ thống. Vui lòng sử dụng mã khác.";
            }
            $checkSkuStmt->close();
        }
    }

    // Giá bán
    if ($price === '') {
        $errors[] = "Vui lòng nhập giá bán sản phẩm.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $errors[] = "Giá bán phải là số hợp lệ và lớn hơn hoặc bằng 0.";
    }

    // Tồn kho
    if ($stock === '') {
        $errors[] = "Vui lòng nhập số lượng tồn kho.";
    } elseif (!filter_var($stock, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]) && $stock !== '0') {
        $errors[] = "Số lượng tồn kho phải là số nguyên không âm.";
    }

    // Thương hiệu
    if (mb_strlen($brand) > 100) {
        $errors[] = "Thương hiệu không được vượt quá 100 ký tự.";
    }

    // 3. Xử lý tải ảnh sản phẩm
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Đã xảy ra lỗi khi tải ảnh lên (Mã lỗi: " . $file['error'] . ").";
        } else {
            // Giới hạn dung lượng: tối đa 5MB
            $maxFileSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                $errors[] = "Dung lượng ảnh không được vượt quá 5MB.";
            }

            // Kiểm tra định dạng phần mở rộng
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = "Định dạng file không hợp lệ! Chỉ chấp nhận file JPG, JPEG, PNG, WEBP, GIF.";
            }

            // Kiểm tra MIME Type thực tế
            if (empty($errors) && function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors[] = "Tệp tải lên không phải là định dạng hình ảnh hợp lệ.";
                }
            }

            // Lưu file vào thư mục public/images/products/
            if (empty($errors)) {
                $uploadDir = __DIR__ . '/../../../public/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $targetFile = $uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $imagePath = 'products/' . $newFileName;
                } else {
                    $errors[] = "Không thể lưu hình ảnh lên hệ thống. Vui lòng kiểm tra quyền thư mục.";
                }
            }
        }
    }

    // 4. Nếu không có lỗi, thực hiện INSERT vào database
    if (empty($errors)) {
        $priceVal = (float)$price;
        $stockVal = (int)$stock;
        $statusVal = (int)$status;
        $isDeleted = 0;

        $insertSql = "INSERT INTO products (name, sku, price, stock, brand, description, image, status, is_deleted) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);

        if ($insertStmt) {
            $insertStmt->bind_param(
                "ssdisssii",
                $name,
                $sku,
                $priceVal,
                $stockVal,
                $brand,
                $description,
                $imagePath,
                $statusVal,
                $isDeleted
            );

            if ($insertStmt->execute()) {
                $insertStmt->close();
                $conn->close();
                // Thêm thành công, chuyển hướng về trang danh sách sản phẩm
                header("Location: index.php?msg=created");
                exit();
            } else {
                $errors[] = "Lỗi khi lưu sản phẩm vào database: " . $insertStmt->error;
                $insertStmt->close();
            }
        } else {
            $errors[] = "Lỗi chuẩn bị truy vấn SQL: " . $conn->error;
        }
    }
}

// Include header layout sau khi xử lý xong POST redirect
include __DIR__ . '/../layouts/header.php';
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        background-color: #fff;
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
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
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
        min-height: 200px;
    }
    .dropzone-custom:hover { 
        border-color: #3b82f6; 
        background: #eff6ff; 
    }
    .dropzone-icon { 
        font-size: 2.25rem; 
        color: #94a3b8; 
        margin-bottom: 0.5rem; 
    }
    .preview-box {
        position: relative;
        display: inline-block;
    }
    .preview-img {
        max-width: 100%;
        max-height: 150px;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    .alert-custom-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Thêm sản phẩm mới</h3>
            <p class="text-muted small mb-0 mt-1">Nhập thông tin chi tiết và định giá để xuất bản sản phẩm mới lên hệ thống</p>
        </div>
        <a href="products.php" class="btn btn-light rounded-pill px-4 border bg-white shadow-sm fw-medium text-dark text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo lỗi (nếu có) -->
    <?php if (!empty($errors)): ?>
        <div class="alert-custom-danger">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-octagon-fill text-danger me-2 fs-5"></i>
                <span class="fw-bold">Vui lòng kiểm tra lại các thông tin sau:</span>
            </div>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="create.php" method="POST" enctype="multipart/form-data">
        <!-- Box 1: Thông tin cơ bản -->
        <div class="card-modern">
            <div class="card-header-modern">
                <i class="bi bi-info-circle me-2 text-primary"></i> Thông tin cơ bản
            </div>
            <div class="card-body-modern">
                <div class="form-group-custom">
                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-custom" name="name" required 
                           value="<?= htmlspecialchars($name) ?>" 
                           placeholder="Nhập tên sản phẩm (VD: Laptop Gaming Acer Nitro 5)">
                </div>
                <div class="form-group-custom mb-0">
                    <label class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control-custom" name="description" rows="5" 
                              placeholder="Nhập mô tả chi tiết, thông số nổi bật của sản phẩm..."><?= htmlspecialchars($description) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Box 2: Giá & Tồn kho -->
        <div class="card-modern">
            <div class="card-header-modern">
                <i class="bi bi-tag me-2 text-success"></i> Định giá & Kho hàng
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-custom mb-md-0">
                            <label class="form-label">Giá bán (₫) <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 translate-middle-y ms-3 text-muted fw-bold">₫</span>
                                <input type="number" class="form-control-custom ps-5 fw-bold text-primary" name="price" required 
                                       min="0" step="any"
                                       value="<?= htmlspecialchars($price) ?>" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-custom mb-0">
                            <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="bi bi-box-seam position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="number" class="form-control-custom ps-5" name="stock" required 
                                       min="0" step="1"
                                       value="<?= htmlspecialchars($stock) ?>" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box 3: Phân loại & Hình ảnh -->
        <div class="card-modern">
            <div class="card-header-modern">
                <i class="bi bi-box me-2 text-warning"></i> Phân loại & Hình ảnh
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <!-- Cột trái: Phân loại -->
                    <div class="col-md-6 pe-md-4">
                        <div class="form-group-custom">
                            <label class="form-label">Mã SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-custom bg-light" name="sku" required 
                                   value="<?= htmlspecialchars($sku) ?>" 
                                   placeholder="VD: SP-001, LAP-ACER-N5">
                            <div class="text-muted small mt-1">Mã định danh duy nhất cho sản phẩm.</div>
                        </div>
                        <div class="form-group-custom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Danh mục sản phẩm</label>
                                <a href="/computer-shop/resources/admin/categories/create.php" target="_blank" class="small text-primary text-decoration-none">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm danh mục mới
                                </a>
                            </div>
                            <select class="form-control-custom bg-white" name="category">
                                <option value="">-- Chọn danh mục --</option>
                                <!-- <option value="1">Laptop Gaming</option>
                                <option value="2">PC Đồng bộ</option>
                                <option value="3">Linh kiện máy tính</option>
                                <option value="4">Màn hình máy tính</option>
                                <option value="5">Gaming Gear & Phụ kiện</option> -->
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Thương hiệu</label>
                                <a href="/computer-shop/resources/admin/brands/create.php" target="_blank" class="small text-primary text-decoration-none">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm thương hiệu mới
                                </a>
                            </div>
                            <!-- <input type="text" class="form-control-custom" name="brand" 
                                   value="<?= htmlspecialchars($brand) ?>" 
                                   placeholder="Ví dụ: ASUS, Apple, Dell, Lenovo..."> -->
                                <select class="form-control-custom bg-white" name="brand">
                                <option value="">-- Chọn thương hiệu --</option>
                                
                               
                            </select>
                        </div>
                        <div class="form-group-custom mb-0">
                            <label class="form-label">Trạng thái bán</label>
                            <select class="form-control-custom bg-white" name="status">
                                <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Đang kinh doanh</option>
                                <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Ngừng kinh doanh</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Cột phải: Hình ảnh -->
                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0 border-start-md">
                        <div class="dropzone-custom d-flex flex-column align-items-center justify-content-center" 
                             onclick="document.getElementById('productImage').click()">
                            
                            <!-- Khi chưa chọn file -->
                            <div id="dropzoneEmpty" class="text-center">
                                <i class="bi bi-cloud-arrow-up dropzone-icon"></i>
                                <h6 class="fw-bold mb-1 text-dark">Tải ảnh đại diện sản phẩm</h6>
                                <p class="text-muted small mb-3">Click vào đây hoặc kéo thả ảnh vào</p>
                                <div>
                                    <span id="uploadBadge" class="badge bg-primary rounded-pill px-3 py-2 fw-medium">
                                        <i class="bi bi-upload me-1"></i> Chọn ảnh
                                    </span>
                                </div>
                                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                                    Định dạng: JPG, PNG, WEBP, GIF. Tối đa 5MB.
                                </div>
                            </div>

                            <!-- Xem trước khi đã chọn file -->
                            <div id="dropzonePreview" class="d-none text-center">
                                <div class="preview-box mb-2">
                                    <img id="imagePreviewImg" class="preview-img" src="#" alt="Preview ảnh">
                                </div>
                                <div id="previewFileName" class="text-dark small fw-semibold text-truncate mb-2" style="max-width: 250px;"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="event.stopPropagation(); document.getElementById('productImage').click();">
                                    <i class="bi bi-arrow-repeat me-1"></i> Đổi ảnh khác
                                </button>
                            </div>

                            <!-- Input file ẩn -->
                            <input type="file" class="d-none" id="productImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp,image/gif" 
                                   onchange="handleImagePreview(this)">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="text-end mt-2 mb-5">
            <a href="products.php" class="btn btn-light rounded-pill px-4 me-2 border fw-medium text-dark text-decoration-none">
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                <i class="bi bi-cloud-check-fill me-2"></i> Lưu sản phẩm
            </button>
        </div>
    </form>
</div>

<script>
function handleImagePreview(input) {
    const file = input.files[0];
    const emptyBox = document.getElementById('dropzoneEmpty');
    const previewBox = document.getElementById('dropzonePreview');
    const previewImg = document.getElementById('imagePreviewImg');
    const previewName = document.getElementById('previewFileName');

    if (file) {
        // Kiểm tra dung lượng (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.');
            input.value = '';
            return;
        }

        // Kiểm tra loại file
        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Chỉ chấp nhận các định dạng ảnh: JPG, PNG, WEBP, GIF.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewName.textContent = file.name;
            emptyBox.classList.add('d-none');
            previewBox.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.src = '#';
        previewName.textContent = '';
        previewBox.classList.add('d-none');
        emptyBox.classList.remove('d-none');
    }
}
</script>

<?php 
include __DIR__ . '/../layouts/footer.php';
?>
