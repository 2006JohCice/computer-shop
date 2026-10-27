<?php
// app/views/admin/san-pham/create.php
$pageTitle = "Thêm sản phẩm mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>


    <?php
        include __DIR__ ."/../../config/database.php";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Xử lý thông số kỹ thuật (gộp thành 1 chuỗi)
            $cpu = trim($_POST['cpu'] ?? '');
            $ram = trim($_POST['ram'] ?? '');
            $storage = trim($_POST['storage'] ?? '');
            $vga = trim($_POST['vga'] ?? '');
            $screen = trim($_POST['screen'] ?? '');
            $weight = trim($_POST['weight'] ?? '');


            $maSP = trim($_POST['maSP'] ?? '');
            $tenSP = trim($_POST['tenSP'] ?? '');
            $giaBan = $_POST['giaBan'] ?? 0;
            $soLuongTon = $_POST['soLuongTon'] ?? 0;
            $maTH = trim($_POST['maTH'] ?? '');
            $maDM = trim($_POST['maDM'] ?? '');
            
            $thoiGianBaoHanh = $_POST['thoiGianBaoHanh'] ?? 0;
            // Tạo chuỗi thông số kỹ thuật. Bạn có thể định dạng HTML hoặc chuỗi thuần tùy ý
            $thongSoKyThuat = "CPU: $cpu | RAM: $ram | SSD: $storage | VGA: $vga | Màn hình: $screen | Trọng lượng: $weight";

            $moTa = trim($_POST['moTa'] ?? '');
            $viTriKho = trim($_POST['viTriKho'] ?? '');
            
         
            $trangThai = $_POST['trangThai'] ?? 1;

            
            // Lệnh die() dùng để dừng chương trình tại đây, không chạy xuống phần INSERT nữa
            // Khi nào test xong, thấy dữ liệu in ra đúng chuẩn, hãy xóa lệnh die() và echo này đi.
          

            $errors = [];
           if (empty($maSP)) {
                $errors[] = "Mã sản phẩm không được để trống.";
            }

            //trùng lặp maSP
            if (empty($errors)) {
                $sqlCheck = "SELECT maSP FROM sanpham WHERE maSP = '$maSP'";
                $resultCheck = $conn->query($sqlCheck);
                
                if ($resultCheck && $resultCheck->num_rows > 0) {
                    $errors[] = "Mã sản phẩm '$maSP' đã tồn tại trong hệ thống. Vui lòng nhập mã khác.";
                }
            }

            if (empty($errors)) {
                
                $sqlInsert = "INSERT INTO sanpham (maSP, tenSP, giaBan, soLuongTon, maTH, maDM, thoiGianBaoHanh, thongSoKyThuat, moTa, viTriKho, trangThai, isDeleted) 
                              VALUES ('$maSP', '$tenSP', '$giaBan', '$soLuongTon', '$maTH', '$maDM', '$thoiGianBaoHanh', '$thongSoKyThuat', '$moTa', '$viTriKho', '$trangThai', 0)";
                
                if ($conn->query($sqlInsert) === TRUE) {
                    
                    echo "<script>window.location.href='san-pham.php';</script>";
                    exit;
                } else {
                    $errors[] = "Lỗi khi thêm sản phẩm: " . $conn->error;
                }

            }
            
           
        }
    
    ?>

    <main class="tz-content-area">
        <!-- Header & Nút Quay Lại (Dựa theo ảnh mẫu số 4) -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm sản phẩm mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Nhập thông tin chi tiết, định giá và cấu hình để xuất bán laptop mới lên hệ thống TECHZONE.</p>
            </div>
            <a href="san-pham.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Khung Cảnh Báo Lỗi Mẫu (Tương tự ảnh số 4 khi dính lỗi Validate hoặc Foreign Key) -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger border-danger-subtle d-flex align-items-start gap-3 p-3 mb-4 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
            <div>
                <div class="fw-bold mb-1">Vui lòng kiểm tra lại các thông tin sau:</div>
                <ul class="mb-0 ps-3 small">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Thêm Sản Phẩm -->
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="row g-4">
                <!-- Cột trái: Thông tin chính -->
                <div class="col-xl-8">
                    <!-- Thẻ 1: Thông tin cơ bản -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-info-circle text-primary"></i>Thông tin cơ bản</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm Laptop <span class="text-danger">*</span></label>
                                <input type="text" name="tenSP" class="form-control" placeholder="Ví dụ: Dell XPS 13 9320 Plus (2024)" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mã SKU / Mã Sản Phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="maSP" class="form-control font-monospace" placeholder="Ví dụ: SP-DELL-XPS13" required>
                                    <small class="text-muted">Mã định danh duy nhất cho sản phẩm trong hệ thống.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Vị trí lưu kho</label>
                                    <input type="text" name="viTriKho" class="form-control" placeholder="Ví dụ: Kệ A-01, Ngăn 2">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Mô tả sản phẩm</label>
                                <textarea name="moTa" rows="4" class="form-control" placeholder="Mô tả tóm tắt tính năng nổi bật, ngoại hình và đối tượng sử dụng..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ 2: Cấu hình kỹ thuật Laptop (Đặc thù máy tính) -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-cpu text-info"></i>Thông số kỹ thuật chi tiết</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Bộ vi xử lý (CPU)</label>
                                    <input type="text" name="cpu" class="form-control" placeholder="Ví dụ: Intel Core i7-1360P hoặc Apple M3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bộ nhớ trong (RAM)</label>
                                    <input type="text" name="ram" class="form-control" placeholder="Ví dụ: 16GB LPDDR5 6000MHz">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ổ cứng lưu trữ (SSD/HDD)</label>
                                    <input type="text" name="storage" class="form-control" placeholder="Ví dụ: 512GB PCIe 4.0 NVMe SSD">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Card đồ họa (VGA / GPU)</label>
                                    <input type="text" name="vga" class="form-control" placeholder="Ví dụ: NVIDIA RTX 4070 8GB / Intel Iris Xe">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Màn hình</label>
                                    <input type="text" name="screen" class="form-control" placeholder="Ví dụ: 14 inch 3K OLED 120Hz">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Trọng lượng (kg)</label>
                                    <input type="text" name="weight" class="form-control" placeholder="Ví dụ: 1.24 kg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ 3: Định giá & Kho hàng -->
                    <div class="tz-card">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-tags text-primary"></i>Định giá & Số lượng</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Giá bán niêm yết (đ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">₫</span>
                                        <input type="number" name="giaBan" class="form-control" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số lượng tồn ban đầu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-box"></i></span>
                                        <input type="number" name="soLuongTon" class="form-control" placeholder="0" min="0" required>
                                    </div>
                                    <small class="text-muted">Các số Serial/IMEI tương ứng sẽ được nhập ở mục Quản lý Serial.</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Thời hạn bảo hành (tháng)</label>
                                    <select name="thoiGianBaoHanh" class="form-select">
                                        <option value="12">12 tháng (1 năm)</option>
                                        <option value="24" selected>24 tháng (2 năm)</option>
                                        <option value="36">36 tháng (3 năm)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái bán</label>
                                    <select name="trangThai" class="form-select">
                                        <option value="1" selected>Đang kinh doanh</option>
                                        <option value="0">Ngừng kinh doanh</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Phân loại & Ảnh đại diện (Dựa theo ảnh 3) -->
                <div class="col-xl-4">
                    <!-- Thẻ Phân loại -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-grid-3x3-gap text-warning"></i>Phân loại danh mục</h3>
                        </div>
                        <div class="tz-card-body">
                            <!-- Danh mục -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Danh mục sản phẩm <span class="text-danger">*</span></label>
                                    <a href="../danh-muc/create.php" class="text-primary text-decoration-none small"><i class="bi bi-plus-circle me-1"></i>Thêm danh mục mới</a>
                                </div>
                                <select name="maDM" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                     <?php
                                $sqlCategory = "SELECT * FROM danhmuc WHERE trangThai ='1'";
                                $resultCategory = $conn->query($sqlCategory);
                                if($resultCategory->num_rows > 0){
                                    while($row = $resultCategory->fetch_assoc()){
                                        $maDM = $row["maDM"];
                                        $tenDM = $row["tenDM"];
                                ?>
                                <option value="<?= $maDM ?>"><?= $tenDM ?></option>
                                <?php
                                        }
                                    }
                                ?>
                                </select>
                            </div>

                            <!-- Thương hiệu -->
                            <div class="mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Thương hiệu hãng <span class="text-danger">*</span></label>
                                    <a href="../thuong-hieu/create.php" class="text-primary text-decoration-none small"><i class="bi bi-plus-circle me-1"></i>Thêm thương hiệu mới</a>
                                </div>
                                <select name="maTH" class="form-select" required>
                                    <option value="">-- Chọn thương hiệu --</option>
                                   <?php
                                $sqlBrand = "SELECT * FROM thuonghieu WHERE trangThai ='1'";
                                $resultBrand = $conn->query($sqlBrand);
                                if($resultBrand->num_rows > 0){
                                    while($row = $resultBrand->fetch_assoc()){
                                        $maTH = $row["maTH"];
                                        $tenTH = $row["tenTH"];
                                ?>
                                <option value="<?= $maTH ?>"><?= $tenTH ?></option>
                                <?php
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ Upload Hình ảnh (Chuẩn như ảnh số 3) -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-image text-primary"></i>Hình ảnh đại diện</h3>
                        </div>
                        <div class="tz-card-body text-center">
                            <div class="p-4 border border-2 border-dashed rounded-3 bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                <i class="bi bi-cloud-arrow-up text-secondary" style="font-size: 42px;"></i>
                                <div class="fw-semibold text-dark mt-2" style="font-size: 13.5px;">Tải ảnh đại diện sản phẩm</div>
                                <p class="text-muted small mb-3">Click vào đây hoặc kéo thả ảnh vào</p>
                                
                                <label for="imageUpload" class="btn btn-sm btn-tz-primary px-3">
                                    <i class="bi bi-folder2-open"></i>Chọn ảnh
                                </label>
                                <input type="file" id="imageUpload" name="hinhAnh" class="d-none" accept="image/png, image/jpeg, image/webp">
                                
                                <small class="text-muted mt-3" style="font-size: 11px;">Định dạng: JPG, PNG, WEBP, GIF. Tối đa 5MB.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Thanh nút Hành động cuối -->
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="san-pham.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-tz-primary px-4">
                            <i class="bi bi-floppy"></i>Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
