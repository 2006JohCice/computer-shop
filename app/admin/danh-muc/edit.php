<?php
// app/admin/danh-muc/edit.php
$pageTitle = "Chỉnh sửa danh mục laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDM = $_GET['maDM'] ?? $_POST['maDM'] ?? '';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   

    $maDMEdit = trim($_POST['maDM'] ?? '');
    $tenDMEdit = trim($_POST['tenDM'] ?? '');
    $maKMEdit = $_POST['maKM'] ?? null;
    $moTaEdit = trim($_POST['moTa'] ?? '');
    $trangThaiEdit = $_POST['trangThai'] ?? 1;
    
    // echo $maDMEdit;
    // echo "<br>";
    // echo $maKMEdit;
    // echo "<br>";
    // echo $tenDMEdit;
    // echo "<br>";
    // echo $moTaEdit;
    // echo "<br>";
    // echo $trangThaiEdit;
    // echo "<br>";
   
    if (empty($maDMEdit)) {
    $errors[] = "Mã danh mục không được để trống.";
    }
    if (empty($tenDMEdit)) {
    $errors[] = "Tên danh mục không được để trống.";
    }


    echo "<br>";

    if (empty($errors)) {
        if (empty($maKMEdit)) {
            $sqlUpdate = "UPDATE danhmuc 
                          SET tenDM = '$tenDMEdit',  maKM = NULL,trangThai = $trangThaiEdit, moTa = '$moTaEdit' 
                          WHERE maDM = $maDMEdit";
        } else {
            $sqlUpdate = "UPDATE danhmuc
                          SET tenDM = '$tenDMEdit', maKM = '$maKMEdit', trangThai = $trangThaiEdit, moTa = '$moTaEdit' 
                          WHERE maDM = $maDMEdit";
        }

        
        

        if ($conn->query($sqlUpdate) === TRUE) {
            // header("Location: danh-muc.php");
            echo "<script>window.location.href = 'danh-muc.php';</script>";
            exit;
        } else {
            $errors[] = "Lỗi khi cập nhật: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM danhmuc WHERE maDM = $maDM";
$result = $conn->query($sql);

if ($result -> num_rows > 0) {
    $row = $result->fetch_assoc();
    $maDM = $row["maDM"];
    $tenDM = $row["tenDM"];
    $maKM = $row["maKM"];
    $moTa = $row["moTa"];
    $trangThai = $row["trangThai"];


    // cái này để in ra dòng máy

    $sqlProducts = "SELECT * FROM sanpham WHERE maDM = $maDM";
    $resultProducts = $conn->query($sqlProducts);
    $totalProducts = $resultProducts->num_rows;

    //End cái này để in ra dòng máy

}


?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="danh-muc.php" class="text-decoration-none text-muted">Danh mục máy</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật danh mục: #<?= $maDM ?></h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin phân khúc theo biểu đồ thực thể DanhMuc</p>
            </div>
            <a href="danh-muc.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form action="edit.php" method="POST">
            <input type="hidden" name="action" value="update">

            <!-- Box 1: Thông tin cơ bản danh mục (DanhMuc) -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-folder2-open me-2 text-primary"></i> Thông tin danh mục
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã danh mục (maDM) <span class="text-danger">*</span></label>
                            <input type="number" name="maDM" class="form-control bg-light font-monospace" value="<?= htmlspecialchars($maDM) ?>" readonly>
                            <!-- <small class="text-muted">Mã định danh số nguyên khóa chính (int PK)</small> -->
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Tên danh mục laptop (tenDM) <span class="text-danger">*</span></label>
                            <input type="text" name="tenDM" class="form-control" 
                                   value="<?= $tenDM ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Áp dụng Khuyến Mãi (maKM) <small class="text-muted">Mã KM</small></label>
                            <select name="maKM" class="form-select">
                                <option value="">-- Không áp dụng (Gỡ mã) --</option>
                                <?php
                                $sqlKM = "SELECT * FROM khuyenmai";
                                $resultSqlKM = $conn->query($sqlKM);
                                
                                if($resultSqlKM->num_rows > 0){
                                    while($row = $resultSqlKM->fetch_assoc()){
                                        $maKhuyenMai = $row["maKM"];
                                        $tenKhuyenMai = $row["tenKM"];
                                ?>
                                <option value="<?php echo $maKhuyenMai ?>" <?php if($maKhuyenMai == $maKM){echo "selected";} ?>><?php echo $maKhuyenMai ?> - <?php echo $tenKhuyenMai ?></option>
                                
                                <?php
                                    }
                                }
                                ?>
                               
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Tiêu chuẩn kỹ thuật & Mô tả danh mục -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-file-text me-2 text-primary"></i> Mô tả chi tiết danh mục 
                </div>
                <div class="card-body-modern">
                    <div class="mb-0">
                        <label class="form-label">Mô tả đặc điểm tiêu chuẩn của phân khúc </label>
                        <textarea name="moTa" rows="4" class="form-control"><?=$moTa?></textarea>
                    </div>
                </div>
            </div>

            <!-- Box 3: Trạng thái hiển thị -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-eye me-2 text-primary"></i> Trạng thái hoạt động 
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái danh mục </label>
                            <select name="trangThai" class="form-select">
                                <option value="1" <?= $trangThai == 1 ? 'selected' : '' ?>>1 - Hiển thị hoạt động</option>
                                <option value="0" <?= $trangThai == 0 ? 'selected' : '' ?>>0 - Tạm ẩn</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu / Hủy cuối trang -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="danh-muc.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
