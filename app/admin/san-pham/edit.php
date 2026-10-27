<?php
// app/admin/san-pham/edit.php
$pageTitle = "Chỉnh sửa thông tin Laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
include __DIR__ . "/../../config/database.php";
    $maSP = $_GET['id'] ?? '';
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM sanpham WHERE maSP = '$maSP' ";
    $resultSql =  $conn->query($sql);
    if ($resultSql->num_rows > 0){
        while($row = $resultSql -> fetch_assoc()){
            $tenSP = $row["tenSP"];
            $maSP = $row["maSP"];
            $thoiGianBaoHanh = $row["thoiGianBaoHanh"];
            $moTa = $row["moTa"];
            $maDMEdit = $row["maDM"];
            $maTHEdit = $row["maTH"];
            $trangThai = $row["trangThai"];
            $soLuongTon = $row["soLuongTon"];
            $giaBan = $row["giaBan"];
            $thongSoKyThuat = $row["thongSoKyThuat"];
        }
    }

    if($_SERVER["REQUEST_METHOD"] === 'POST'){
        $maSP = $_POST['maSP'] ;
        $tenSP = $_POST['tenSP'];
        $maTH = $_POST['maTH'];
        $maDM = $_POST['maDM'];
        $giaBan = $_POST['giaBan'];
        $soLuongTon = $_POST['soLuongTon'];
        $moTa = $_POST['moTa'];
        $thongSoKyThuat = $_POST['thongSoKyThuat'];
        $trangThai = $_POST['trangThai'];
        $thoiGianBaoHanh = $_POST['thoiGianBaoHanh'];

        // echo $maSP;
        // echo "<br>";
        // echo $tenSP;
        // echo "<br>";
        // echo $maTH;
        // echo "<br>";
        // echo $maDM;
        // echo "<br>";
        // echo $giaBan;
        // echo "<br>";
        // echo $soLuongTon;
        // echo "<br>";
        // echo $moTa;
        // echo "<br>";
        // echo $thongSoKyThuat;
        // echo "<br>";
        // echo $trangThai;
        // echo "<br>";
        // echo $thoiGianBaoHanh;
        
        $sqlUpdate = "UPDATE sanpham SET
            tenSP = '$tenSP',
            maTH = '$maTH',
            maDM = '$maDM',
            giaBan = '$giaBan',
            soLuongTon = '$soLuongTon',
            moTa = '$moTa',
            thongSoKyThuat = '$thongSoKyThuat',
            trangThai = '$trangThai',
            thoiGianBaoHanh = '$thoiGianBaoHanh'
        WHERE
            maSP = '$maSP' ";
            $resultSql = $conn->query($sqlUpdate);
            if($resultSql === TRUE){
                echo "<script>";
                echo "window.location.href='san-pham.php';";
                echo "</script>";
            }else{
            echo "<script>";
            echo "alert('Lỗi khi cập nhật sản phẩm');";
            echo "</script>";
        }

    }

?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header & Nút Quay Lại -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="san-pham.php" class="text-decoration-none text-muted">Sản phẩm Laptop</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật sản phẩm: <?= htmlspecialchars($maSP) ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông số kỹ thuật, cấu hình, giá bán và phân loại sản phẩm laptop.</p>
            </div>
            <a href="san-pham.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Form Chỉnh Sửa Sản Phẩm (Gửi bằng POST Form thuần) -->
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            
            <div class="row g-4">
                <!-- Cột trái: Thông tin chính -->
                <div class="col-xl-8">
                    <!-- Thẻ 1: Thông tin cơ bản (SanPham) -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-info-circle text-primary"></i>Thông tin cơ bản</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm Laptop (tenSP) <span class="text-danger">*</span></label>
                                <input type="text" name="tenSP" class="form-control" value="<?=$tenSP  ?>" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mã SKU / Mã Sản Phẩm (maSP) <span class="text-danger">*</span></label>
                                    <input type="text" name="maSP" class="form-control font-monospace" value="<?= htmlspecialchars($maSP) ?>" readonly>
                                    <small class="text-muted">Mã định danh duy nhất (PK) cố định</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thời hạn bảo hành (thoiGianBaoHanh)</label>
                                    <select name="thoiGianBaoHanh" class="form-select">
                                       
                                            <option value="12" <?php echo ($thoiGianBaoHanh == 12) ? 'selected' : ''; ?>>12 tháng (1 năm)</option>
                                            <option value="24" <?php echo ($thoiGianBaoHanh == 24) ? 'selected' : ''; ?>>24 tháng (2 năm)</option>
                                            <option value="36" <?php echo ($thoiGianBaoHanh == 36) ? 'selected' : ''; ?>>36 tháng (3 năm)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Mô tả sản phẩm (moTa)</label>
                                <textarea name="moTa" rows="4" class="form-control"><?php echo $moTa; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ 2: Cấu hình kỹ thuật Laptop (thongSoKyThuat) -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-cpu text-info"></i>Thông số kỹ thuật (thongSoKyThuat)</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="mb-3">
                                <label class="form-label">Chi tiết cấu hình phần cứng</label>
                                <textarea name="thongSoKyThuat" rows="3" class="form-control font-monospace"><?php echo $thongSoKyThuat; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ 3: Định giá & Kho hàng (giaBan, soLuongTon) -->
                    <div class="tz-card">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-tags text-primary"></i>Định giá & Kho hàng</h3>
                        </div>
                        <div class="tz-card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Giá bán niêm yết (giaBan) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">₫</span>
                                        <input type="number" name="giaBan" class="form-control" value=<?php echo $giaBan; ?> min="0"  required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số lượng tồn kho (soLuongTon) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-box"></i></span>
                                        <input type="number" name="soLuongTon" class="form-control" value=<?php echo $soLuongTon; ?> min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái kinh doanh (trangThai)</label>
                                    <select name="trangThai" class="form-select">
                                        <option value="1" <?php echo ($trangThai == 1) ? 'selected': '' ?>>1 - Đang kinh doanh</option>
                                        <option value="0" <?php echo ($trangThai == 0) ? 'selected': ''  ?>>0 - Ngừng kinh doanh</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Phân loại & Ảnh đại diện (maDM, maTH, hinhAnh) -->
                <div class="col-xl-4">
                    <!-- Thẻ Phân loại -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-grid-3x3-gap text-warning"></i>Phân loại & Hãng</h3>
                        </div>
                        <div class="tz-card-body">
                            <!-- Danh mục (maDM) -->
                            <div class="mb-3">
                                <label class="form-label mb-1">Danh mục sản phẩm (maDM) <span class="text-danger">*</span></label>
                                <select name="maDM" class="form-select" required>
                                    <?php
                                        $sqlCategory = "SELECT * FROM danhmuc WHERE trangThai ='1'";
                                        $resultCategory = $conn->query($sqlCategory);
                                        if($resultCategory->num_rows>0){
                                            while($row = $resultCategory->fetch_assoc()){
                                                $tenDM = $row["tenDM"];
                                                $maDM = $row["maDM"];

                                          

                                    ?>
                                        <option value="<?=$maDM ?>" <?= ($maDM == $maDMEdit) ? 'selected':''  ?> ><?=$tenDM  ?></option>
                                    <?php
                                      }
                                        }
                                    ?>
                                </select>
                            </div>

                            <!-- Thương hiệu (maTH) -->
                            <div class="mb-0">
                                <label class="form-label mb-1">Thương hiệu hãng (maTH) <span class="text-danger">*</span></label>
                                <select name="maTH" class="form-select" required>
                                 <?php
                                        $sqlBrand = "SELECT * FROM thuonghieu WHERE trangThai ='1'";
                                        $resultBrand = $conn->query($sqlBrand);
                                        if($resultBrand->num_rows>0){
                                            while($row = $resultBrand->fetch_assoc()){
                                                $tenTH = $row["tenTH"];
                                                $maTH = $row["maTH"];


                                    ?>
                                        <option value="<?=$maTH ?>" <?= ($maTH == $maTHEdit) ? 'selected':''  ?> ><?=$tenTH  ?></option>
                                    <?php
                                      }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ Upload Hình ảnh (hinhAnh) -->
                    <div class="tz-card mb-4">
                        <div class="tz-card-header">
                            <h3 class="tz-card-title"><i class="bi bi-image text-primary"></i>Hình ảnh đại diện (hinhAnh)</h3>
                        </div>
                        <div class="tz-card-body text-center">
                            <div class="p-3 border rounded-3 bg-light d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-laptop text-primary" style="font-size: 50px;"></i>
                                <span class="badge bg-light text-muted border my-2">Dell-XPS-13-2024.png</span>
                                <input type="file" name="hinhAnh" class="form-control form-control-sm mt-2" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Thanh nút Hành động cuối -->
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="san-pham.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-tz-primary px-4">
                            <i class="bi bi-floppy"></i>Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
