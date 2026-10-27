<?php
// app/admin/thuong-hieu/edit.php
$pageTitle = "Chỉnh sửa thương hiệu đối tác";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maTH = htmlspecialchars($_GET['maTH'] ?? '');
include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die($conn->connect_error);
}
$errors = [];
$sql = "SELECT * FROM thuonghieu WHERE maTH ='$maTH'";
$resultSql = $conn->query($sql);

if ($resultSql->num_rows > 0) {
    $row = $resultSql->fetch_assoc();
    $tenTH = $row['tenTH'];
    $xuatXu = $row['xuatXu'];
    $moTa = $row['moTa'];
    $logo = $row['logo'];
    $trangThai = $row['trangThai'];
}


if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $maTHEdit = trim($_POST['maTH'] ?? '');
    $tenTHEdit = trim($_POST['tenTH'] ?? '');
    $xuatXuEdit = trim($_POST['xuatXu'] ?? '');
    $moTaEdit = trim($_POST['moTa'] ?? '');
    $logoEdit = trim($_POST['logo'] ?? '');
    $trangThaiEdit = $_POST['trangThai'] ?? 1;

    // echo $maTHEdit;
    // echo "<br>";
    // echo $tenTHEdit;
    // echo "<br>";
    // echo $xuatXuEdit;
    // echo "<br>";
    // echo $moTaEdit;
    // echo "<br>";
    // echo $logoEdit;
    // echo "<br>";
    // echo $trangThaiEdit;
    // echo "<br>";


    if (empty($xuatXuEdit)) {
        $errors[] = "Xuất xứ không được để trống.";
    }
    if ($trangThaiEdit !== '1' && $trangThaiEdit !== '0') {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    if (empty($logoEdit)) {
        $errors[] = "Logo không được để trống.";
    }
    if (empty($tenTHEdit)) {
        $errors[] = "Tên thương hiệu không được để trống.";
    }


    if (empty($errors)) {
        $sqlUpdate = "UPDATE thuonghieu SET tenTH='$tenTHEdit', xuatXu='$xuatXuEdit', moTa='$moTaEdit', logo='$logoEdit', trangThai='$trangThaiEdit' WHERE maTH='$maTHEdit'";
        $resultSqlUpdate = $conn->query($sqlUpdate);
        if ($resultSqlUpdate) {
            echo "<script>";
            echo "window.location.href='thuong-hieu.php';";
            echo "</script>";
        } else {
            $errors[] = "Cập nhật thất bại";
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa (<?= $maTH ?>)</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập nhật thông tin thương hiệu</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa hồ sơ đối tác sản xuất và chứng nhận đại lý phân phối laptop</p>
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

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">

            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Thông tin cơ bản thương hiệu
                </div>
                <div class="card-body-modern">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" name="maTH" class="form-control font-monospace bg-light text-uppercase"
                                value="<?= $maTH ?>" readonly>
                            <!-- <small class="text-muted">Khóa chính (PK)</small> -->
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" name="tenTH" class="form-control"
                                value="<?= $tenTH ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Xuất xứ / Quốc gia <span class="text-danger">*</span></label>
                            <input type="text" name="xuatXu" class="form-control"
                                value="<?= $xuatXu ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Biểu tượng / Logo </label>
                            <input type="text" name="logo" class="form-control" value="<?= $logo ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái hợp tác </label>
                            <select name="trangThai" class="form-select">
                                <option value="1" <?= $trangThai == '1' ? 'selected' : '' ?>>1 - Đang hợp tác phân phối</option>
                                <option value="0" <?= $trangThai == '0' ? 'selected' : '' ?>>0 - Ngừng hợp tác</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern">
                <div class="card-header-modern">
                    <i class="bi bi-file-text me-2 text-primary"></i> Mô tả tóm tắt thương hiệu
                </div>
                <div class="card-body-modern">
                    <div class="mb-0">
                        <label class="form-label">Mô tả đặc điểm tiêu biểu </label>
                        <textarea name="moTa" rows="3" class="form-control"><?= $moTa ?></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-5">
                <a href="thuong-hieu.php" class="btn btn-tz-outline px-4">Hủy bỏ</a>
                <button type="submit" class="btn btn-tz-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Cập nhật thương hiệu
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>