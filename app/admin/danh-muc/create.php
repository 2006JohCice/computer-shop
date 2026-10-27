<?php
// app/views/admin/danh-muc/create.php
$pageTitle = "Thêm danh mục laptop mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<?php
include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenDM = $_POST['tenDM'];
    $maKM = $_POST['maKM'];


    $moTa = $_POST['moTa'];
    $trangThai = $_POST['trangThai'];
    // echo $tenDM;
    // echo "<br/>";
    // echo $maKM;
    // echo "<br/>";
    // echo $moTa;
    // echo "<br/>";
    // echo $trangThai;

    if (empty($maKM)) {
        $sql = "INSERT INTO danhmuc (tenDM,maKM, moTa, trangThai) VALUES ('$tenDM', NULL,'$moTa', '$trangThai')";
    } else {
        $sql = "INSERT INTO danhmuc (tenDM, maKM, moTa, trangThai) VALUES ('$tenDM', '$maKM', '$moTa', '$trangThai')";
    }

    $result = $conn->query($sql);
    // echo $sql;
    if ($result == TRUE) {
        echo "<script> window.location.href='danh-muc.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi thêm danh mục');</script>";
    }
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm danh mục laptop mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Tạo mới phân khúc sản phẩm máy tính xách tay phục vụ nhu cầu tìm kiếm của khách hàng</p>
            </div>
            <a href="danh-muc.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form action="create.php" method="POST">
            <!-- Box 1: Thông tin cơ bản danh mục -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-folder2-open me-2 text-primary"></i> Thông tin danh mục
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <!-- <div class="col-md-3">
                            <label class="form-label">Mã danh mục (maDM) <span class="text-danger">*</span></label>
                            <input type="number" name="maDM" class="form-control" placeholder="VD: 5" required>
                            <small class="text-muted">Mã định danh số nguyên (int PK)</small>
                        </div> -->
                        <div class="col-md-5">
                            <label class="form-label">Tên danh mục laptop <span class="text-danger">*</span></label>
                            <input type="text" name="tenDM" class="form-control"
                                placeholder="Ví dụ: Laptop Gaming, Laptop Mỏng Nhẹ..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Áp dụng Khuyến Mãi <small class="text-muted"></small></label>
                            <select name="maKM" class="form-select">
                                <option value="">-- Không áp dụng --</option>
                                <?php
                                $sqlKM = "SELECT * FROM khuyenmai";
                                $resultKM = $conn->query($sqlKM);
                                if ($resultKM->num_rows > 0) {
                                    while ($row = $resultKM->fetch_assoc()) {
                                        $maKM = $row["maKM"];
                                        $tenKM = $row["tenKM"];

                                ?>
                                        <option value="<?php echo $maKM ?>"><?= $tenKM  ?></option>

                                <?php
                                    }
                                }
                                $conn->close();
                                ?>



                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box 2: Tiêu chuẩn kỹ thuật & Mô tả danh mục -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-file-text me-2 text-primary"></i> Mô tả chi tiết danh mục (moTa)
                </div>
                <div class="card-body-modern">
                    <div class="mb-0">
                        <label class="form-label">Mô tả đặc điểm tiêu chuẩn của phân khúc (moTa)</label>
                        <textarea name="moTa" rows="4" class="form-control"
                            placeholder="Mô tả tiêu chuẩn của phân khúc laptop này (Ví dụ: CPU tối thiểu Core i7, màn hình 144Hz, card đồ họa rời RTX...)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Box 3: Cấu hình hiển thị -->
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-eye me-2 text-primary"></i> Trạng thái hoạt động (trangThai)
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái danh mục (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>1 - Hiển thị hoạt động</option>
                                <option value="0">0 - Tạm ẩn</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu / Hủy cuối trang -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="danh-muc.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu danh mục
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>