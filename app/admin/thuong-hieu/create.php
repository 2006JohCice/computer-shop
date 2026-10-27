<?php
// app/admin/thuong-hieu/create.php
$pageTitle = "Thêm thương hiệu đối tác mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';


include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Không thể kết nối ");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maTH = $_POST['maTH'];
    $tenTH = $_POST['maTH'];
    $xuatXu = $_POST['xuatXu'];
    $logo = $_POST['logo'];
    $trangThai = $_POST['trangThai'];
    $moTa = $_POST['moTa'];

    $errors = [];
    $sql_check = "SELECT maTH FROM thuonghieu WHERE maTH = '$maTH'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        $errors[] = 'Mã thương hiệu đã tồn tại';
        // echo "<script>alert('Mã thương hiệu đã tồn tại');</script>";
    }

    if (empty($maTH)) {
        $errors[] = 'Mã thương hiệu không được để trống';
    }

    if (empty($tenTH)) {
        $errors[] = 'Tên thương hiệu không được để trống';
    }

    if (empty($xuatXu)) {
        $errors[] = 'Xuất xứ không được để trống';
    }

    if (isset($trangThai) && $trangThai !== '1' && $trangThai !== '0') {
        $errors[] = 'Trạng thái không hợp lệ';
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO thuonghieu (maTH, tenTH, xuatXu, logo, trangThai, moTa) VALUES ('$maTH', '$tenTH', '$xuatXu', '$logo', '$trangThai', '$moTa')";
        $result_insert = $conn->query($sql_insert);
        if ($result_insert) {
            echo "<script> window.location.href='thuong-hieu.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi thêm thương hiệu');</script>";
        }
    }
}



?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="thuong-hieu.php" class="text-decoration-none text-muted">Thương hiệu</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm thương hiệu mới</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Đăng ký hãng sản xuất hoặc đối tác ủy quyền phân phối vào hệ thống TECHZONE</p>
            </div>
            <a href="thuong-hieu.php" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
        <?php if (!empty($errors)) { ?>
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
        <?php } ?>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Thông tin cơ bản thương hiệu
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" name="maTH" class="form-control font-monospace text-uppercase"
                                placeholder="VD: DELL, ASUS, MSI..." required>
                            <!-- <small class="text-muted">Khóa chính (PK) viết tắt</small> -->
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" name="tenTH" class="form-control"
                                placeholder="VD: Dell Technologies, ASUStek Inc." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Xuất xứ / Quốc gia <span class="text-danger">*</span></label>
                            <input type="text" name="xuatXu" class="form-control"
                                placeholder="VD: Hoa Kỳ, Đài Loan, Nhật Bản..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đường dẫn Logo / Biểu tượng </label>
                            <input type="text" name="logo" class="form-control"
                                placeholder="VD: assets/logo/dell.png hoặc bi-laptop">
                            <small class="text-muted">Sao Chép Icon Trên <a href="https://worldvectorlogo.com/" target="_blank">https://worldvectorlogo.com/</a></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái hợp tác (trangThai)</label>
                            <select name="trangThai" class="form-select">
                                <option value="1" selected>1 - Đang hợp tác phân phối</option>
                                <option value="0">0 - Ngừng hợp tác</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-file-text me-2 text-primary"></i> Giới thiệu & Mô tả hãng (moTa)
                </div>
                <div class="card-body-modern">
                    <div class="mb-0">
                        <label class="form-label">Mô tả tóm tắt thương hiệu (moTa)</label>
                        <textarea name="moTa" rows="3" class="form-control"
                            placeholder="Nhập thông tin giới thiệu, lịch sử phát triển hoặc điểm nổi bật của dải sản phẩm laptop..."></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="thuong-hieu.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu thương hiệu
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>