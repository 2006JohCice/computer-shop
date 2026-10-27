<?php
// app/admin/thuong-hieu/thuong-hieu.php
$pageTitle = "Quản lý thương hiệu laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';


include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Loi ket noi database");
}
//Tong thuong hieu
$sqlTongThuongHieu = "SELECT count(*) as total FROM thuonghieu";
$resultTongThuongHieu = $conn->query($sqlTongThuongHieu);
$rowTongThuongHieu = $resultTongThuongHieu->fetch_assoc();
$totalThuongHieu = $rowTongThuongHieu['total'];
//tong đang hợp tác
$sqlTHHoatDong = "SELECT count(*) as total FROM thuonghieu WHERE trangthai = '1' ";
$resultTHHoatDong = $conn->query($sqlTHHoatDong);
$rowTHHoatDong = $resultTHHoatDong->fetch_assoc();
$totalTHHoatDong = $rowTHHoatDong['total'];

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $maTHDelete = $_POST['maTH'];
    echo "<script>";
    echo "console.log('MaTH la: " . $maTHDelete . "');";
    echo "</script>";

    $sqlDelete = "DELETE FROM thuonghieu WHERE maTH = '$maTHDelete' ";
    if ($conn->query($sqlDelete)) {
        echo "<script>";
        echo "window.location.href='thuong-hieu.php';";
        echo "</script>";
    } else {
        $errors[] = "Xóa thất bại";
    }
}




?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">


        <!-- Header trang -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Thương hiệu</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thương Hiệu Laptop Đối Tác</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý các hãng sản xuất chính hãng được phân phối tại hệ thống TECHZONE.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-patch-plus"></i>Thêm thương hiệu mới
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

        <!-- 3 Thẻ Metric -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG THƯƠNG HIỆU </div>
                        <div class="tz-metric-value"><?= $totalThuongHieu ?></div>
                        <small class="text-muted"><i class="bi bi-patch-check me-1"></i>Hãng sản xuất</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-patch-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG HỢP TÁC </div>
                        <div class="tz-metric-value text-success"><?= $totalTHHoatDong ?></div>
                        <small class="text-success"><i class="bi bi-shield-check me-1"></i>Đại lý ủy quyền</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">HÃNG NHIỀU MẪU MÁY NHẤT</div>
                        <div class="tz-metric-value text-primary">DELL</div>
                        <small class="text-muted"><i class="bi bi-award me-1"></i>8 mẫu laptop trong kho</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-award"></i>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="tz-card">
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-tags text-primary"></i>Danh Sách Thương Hiệu Laptop
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        thương hiệu
                    </span>
                </div>

                <form action="thuong-hieu.php" method="GET" class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2 mb-0 ms-xl-4">
                    <div class="input-group flex-grow-1" style="min-width: 220px; max-width: 480px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control border-start-0 ps-2" placeholder="Nhập tên hoặc mã thương hiệu để tìm kiếm...">
                    </div>

                    <div class="input-group flex-shrink-0" style="width: 175px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-geo-alt text-danger"></i>
                        </span>
                        <select name="xuatXu" class="form-select border-start-0 ps-2">
                            <option value="">Xuất Xứ </option>
                            <?php
                            $sqlXuatXu = "SELECT * FROM thuonghieu";
                            $resultXuatXu = $conn->query($sqlXuatXu);
                            if ($resultXuatXu->num_rows > 0) {
                                while ($row = $resultXuatXu->fetch_assoc()) {
                                    $xuatXu = $row['xuatXu'];
                                    echo "<option value='$xuatXu'" . ($_GET['xuatXu'] == $xuatXu ? "selected" : "") . ">$xuatXu</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dropdown Lọc Trạng Thái Hợp Tác -->
                    <div class="input-group flex-shrink-0" style="width: 175px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-toggle-on text-info"></i>
                        </span>
                        <select name="trangthai" class="form-select border-start-0 ps-2">
                            <option value="">Trạng Thái </option>
                            <option value="1" <?= ($_GET['trangthai'] ?? '') === '1' ? 'selected' : '' ?>>Đang hợp tác</option>
                            <option value="0" <?= ($_GET['trangthai'] ?? '') === '0' ? 'selected' : '' ?>>Ngừng hợp tác</option>
                        </select>
                    </div>

                    <!-- Nút Thao Tác: Lọc & Đặt lại -->
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="submit" class="btn btn-tz-primary d-inline-flex align-items-center justify-content-center px-3" title="Lọc dữ liệu">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc
                        </button>
                        <a href="thuong-hieu.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th style="width: 110px;">MÃ HÃNG</th>
                            <th style="width: 80px;" class="text-center">LOGO</th>
                            <th>TÊN THƯƠNG HIỆU</th>
                            <th>XUẤT XỨ</th>
                            <th>MÔ TẢ HÃNG</th>
                            <th>SẢN PHẨM LIÊN KẾT</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 100px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // lay thong tin tìm kiem
                        $search = $_GET['search'] ?? '';
                        $xuatXu = $_GET['xuatXu'] ?? '';
                        $trangThai = $_GET['trangthai'] ?? '';

                        // Lay thuong hieu ra
                        $sqlThuongHieu = "SELECT * FROM thuonghieu WHERE 1 = 1";

                        if ($search != "") {
                            $sqlThuongHieu .= " AND maTH LIKE '%$search%' OR tenTH LIKE '%$search%'";
                        }
                        if ($xuatXu != "") {
                            $sqlThuongHieu .= " AND xuatXu = '$xuatXu'";
                        }
                        if ($trangThai != "") {
                            $sqlThuongHieu .= " AND trangThai = $trangThai";
                        }
                        $resultThuongHieu = $conn->query($sqlThuongHieu);
                        if ($resultThuongHieu->num_rows > 0) {
                            while ($row = $resultThuongHieu->fetch_assoc()) {
                                $maTH = $row["maTH"];
                                $tenTH = $row["tenTH"];
                                $xuatXu = $row["xuatXu"];
                                $logoTH = $row["logo"];
                                $moTa = $row["moTa"];
                                $trangThai = $row["trangThai"];

                                $sqlTHHD = "SELECT * FROM sanpham WHERE maTH = '$maTH'";
                                $resultTH = $conn->query($sqlTHHD);
                                $totalTH = $resultTH->num_rows;

                        ?>

                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">
                                            <?= $maTH ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="tz-logo-cell">
                                            <?= $logoTH  ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="../san-pham/san-pham.php?thuongHieu=<?= $maTH ?>"
                                            class="fw-bold text-dark text-decoration-none"
                                            title="Xem các laptop thuộc hãng <?= $tenTH ?>">
                                            <?= $tenTH ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i>
                                            <?= $xuatXu ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 250px;" title="<?= $moTa ?>">
                                            <?= $moTa ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="../san-pham/san-pham.php?thuongHieu=<?= $maTH ?>" class="badge bg-light text-dark border text-decoration-none" title="Xem danh sách laptop của hãng">
                                            <i class="bi bi-laptop me-1 text-primary"></i> <?php echo $totalTH; ?> mẫu máy
                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                        if ($trangThai == 1) { ?>
                                            <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đang hợp tác</span>
                                        <?php } else { ?>
                                            <span class="tz-badge tz-badge-secondary"><i class="bi bi-eye-slash"></i>Ngừng hợp tác</span>
                                        <?php } ?>



                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?maTH=<?= $maTH ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="thuong-hieu.php" class="d-inline">
                                            <input type="hidden" name="maTH" value="<?= $maTH ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                data-tz-confirm="Bạn có chắc muốn xóa thương hiệu tên<?= $tenTH ?>?"
                                                data-tz-title="Xác nhận xóa thương hiệu">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-tags"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy thương hiệu nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có thương hiệu nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="thuong-hieu.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm thương hiệu mới
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>