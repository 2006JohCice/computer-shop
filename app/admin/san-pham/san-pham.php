<?php
// app/admin/san-pham/san-pham.php
$pageTitle = "Quản lý sản phẩm";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<?php
include __DIR__ . "/../../config/database.php";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$totalActive = 0;
$sqlTotalActive = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0";
$resultTotalActive = $conn->query($sqlTotalActive);
if ($resultTotalActive && $resultTotalActive->num_rows > 0) {
    while ($row = $resultTotalActive->fetch_assoc()) {
        $totalActive = $row['total'];
    }
}

$totalInStock = 0;
$sqlInStock = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0 AND soLuongTon > 0";
$resultInStock = $conn->query($sqlInStock);
if ($resultInStock && $resultInStock->num_rows > 0) {
    while ($row = $resultInStock->fetch_assoc()) {
        $totalInStock = $row['total'];
    }
}

$totalOutOfStock = 0;
$sqlOutOfStock = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0 AND soLuongTon = 0";
$resultOutOfStock = $conn->query($sqlOutOfStock);
if ($resultOutOfStock && $resultOutOfStock->num_rows > 0) {
    while ($row = $resultOutOfStock->fetch_assoc()) {
        $totalOutOfStock = $row['total'];
    }
}


$totalTrash = 0;
$sqlTrash = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 1";
$resultTrash = $conn->query($sqlTrash);
if ($resultTrash && $resultTrash->num_rows > 0) {
    while ($row = $resultTrash->fetch_assoc()) {
        $totalTrash = $row['total'];
    }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maSP = $_POST['maSP'];

    // echo $maSP;

    $sqlDeleteSoft = "UPDATE sanpham SET isDeleted = 1 WHERE maSP = '$maSP'";

    $resultSql = $conn->query($sqlDeleteSoft);

    if ($resultSql === TRUE) {
        echo "<script>
                        window.location.href='san-pham.php?tab=active';
                    </script>";
    } else {
        echo "<script>alert('Lỗi khi chuyển sản phẩm vào Thùng rác');</script>";
    }
}

?>



<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">


        <!-- Header trang & Action buttons -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Sản phẩm</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Sản Phẩm Laptop</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý kho laptop, phân loại cấu hình, tồn kho, vị trí lưu trữ và thời hạn bảo hành.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="../danh-muc/create.php" class="btn btn-tz-outline"><i class="bi bi-folder-plus"></i>Thêm danh mục</a>
                <a href="../thuong-hieu/create.php" class="btn btn-tz-outline"><i class="bi bi-patch-plus"></i>Thêm thương hiệu</a>
                <a href="create.php" class="btn btn-tz-primary"><i class="bi bi-plus-lg"></i>Thêm sản phẩm mới</a>
            </div>
        </div>

        <!-- 4 Thẻ Stat Thống kê Sản phẩm -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG SẢN PHẨM HIỆN HÀNH</div>
                        <!-- Thay số 12 bằng biến -->
                        <div class="tz-metric-value text-primary"><?= $totalActive ?></div>
                        <small class="text-muted">Đang quản lý trong hệ thống</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỒN KHO AN TOÀN </div>
                        <!-- Thay số 4 bằng biến -->
                        <div class="tz-metric-value text-success"><?= $totalInStock ?></div>
                        <small class="text-success"><i class="bi bi-check-circle me-1"></i>Sẵn sàng xuất bán</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">CẢNH BÁO HẾT HÀNG</div>
                        <!-- Thay số 1 bằng biến -->
                        <div class="tz-metric-value text-danger"><?= $totalOutOfStock ?></div>
                        <small class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Cần tạo phiếu nhập kho</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-cart-x"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <a href="backup.php" class="text-decoration-none">
                    <div class="tz-metric-card" style="cursor: pointer;">
                        <div>
                            <div class="tz-metric-title">THÙNG RÁC</div>
                            <!-- Thay số 23 bằng biến -->
                            <div class="tz-metric-value text-secondary"><?= $totalTrash ?></div>
                            <small class="text-muted"><i class="bi bi-trash3 me-1"></i>Chờ khôi phục / xóa vĩnh viễn</small>
                        </div>
                        <div class="tz-metric-icon" style="background-color: #f1f5f9; color: #64748b;">
                            <i class="bi bi-archive"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Thanh Công Cụ Tìm Kiếm -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="san-pham.php" method="GET" class="row g-2 align-items-center">

                    <div class="col-xl-4 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="" class="form-control border-start-0 ps-0" placeholder="Nhập Mã SP hoặc Tên sản phẩm để tìm kiếm...">
                        </div>
                    </div>



                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select name="danhMuc" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <?php
                            $sqlCategory = "SELECT * FROM danhmuc WHERE trangThai ='1'";
                            $resultCategory = $conn->query($sqlCategory);
                            if ($resultCategory->num_rows > 0) {
                                while ($row = $resultCategory->fetch_assoc()) {
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
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select name="thuongHieu" class="form-select">
                            <option value="">Tất cả thương hiệu</option>
                            <?php
                            $sqlBrand = "SELECT * FROM thuonghieu WHERE trangThai ='1'";
                            $resultBrand = $conn->query($sqlBrand);
                            if ($resultBrand->num_rows > 0) {
                                while ($row = $resultBrand->fetch_assoc()) {
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
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select name="trangthai" class="form-select">
                            <option value="">Chọn Trạng Thái</option>
                            <option value="1">Hiển Thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-12">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2 Tab Chuyển Đổi Chế Độ Xem: Tab 1 Hiện hành vs Tab 2 Thùng rác -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="san-pham.php?tab=active">
                        <i class="bi bi-check2-circle me-1"></i>Sản phẩm hiện hành
                        <!-- <span class="badge rounded-pill bg-light text-primary ms-1">34</span> -->
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link fw-semibold text-secondary" href="backup.php">
                        <i class="bi bi-trash3 me-1"></i>Thùng rác đã xóa
                        <span class="badge rounded-pill bg-danger-subtle text-danger ms-1"><?= $totalTrash ?></span>
                    </a>
                </li>
            </ul>

            <span class="text-secondary small"><i class="bi bi-clock-history me-1"></i>Dữ liệu đồng bộ tức thì</span>
        </div>

        <!-- Bảng Dữ Liệu Laptop (Data Table) -->
        <div class="tz-card">
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">MÃ SP</th>
                            <th style="width: 80px;">Ảnh</th>
                            <th>TÊN SẢN PHẨM & CẤU HÌNH</th>
                            <th>DANH MỤC</th>
                            <th>THƯƠNG HIỆU</th>
                            <th>GIÁ BÁN (VNĐ)</th>
                            <th>TỒN KHO</th>
                            <th>BẢO HÀNH</th>
                            <th>VỊ TRÍ KHO</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 120px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $search = $_GET["search"] ?? "";
                        $category = $_GET["danhMuc"] ?? "";
                        $brand = $_GET["thuongHieu"] ?? "";
                        $status = $_GET["trangthai"] ?? "";

                        $sql = "SELECT * FROM sanpham WHERE isDeleted = 0";

                        if ($search != "") {

                            $sql .= " AND tenSP LIKE '%$search%'";
                        }

                        if ($category != "") {

                            $sql .= " AND maDM = '$category'";
                        }

                        if ($brand != "") {

                            $sql .= " AND maTH = '$brand'";
                        }

                        if ($status != "") {

                            $sql .= " AND trangThai = '$status'";
                        }

                        $resultProducts = $conn->query($sql);


                        if ($resultProducts->num_rows > 0) {
                            while ($row = $resultProducts->fetch_assoc()) {

                                $maSP = $row["maSP"];
                                $tenSP = $row["tenSP"];
                                $maDM = $row["maDM"] ?? "";
                                $maTH = $row["maTH"] ?? "";
                                $thongSo = $row["thongSoKyThuat"];
                                $giaBan = number_format($row["giaBan"], 0, ',', '.');
                                $soLuongTon = $row["soLuongTon"];
                                $thoiGianBaoHanh = $row["thoiGianBaoHanh"];
                                $viTriKho = $row["viTriKho"];
                                $trangThai = $row["trangThai"];
                                $logo = $row["logo"];

                                // Lấy tên danh mục từ bảng danhmuc
                                $tenDM = "";
                                if (!empty($maDM)) {
                                    $sqlDM = "SELECT tenDM FROM danhmuc WHERE maDM = '$maDM'";
                                    $resDM = $conn->query($sqlDM);
                                    if ($resDM && $resDM->num_rows > 0) {
                                        $tenDM = $resDM->fetch_assoc()['tenDM'];
                                    }
                                }

                                // Lấy tên thương hiệu từ bảng thuonghieu
                                $tenTH = "";
                                if (!empty($maTH)) {
                                    $sqlTH = "SELECT tenTH FROM thuonghieu WHERE maTH = '$maTH'";
                                    $resTH = $conn->query($sqlTH);
                                    if ($resTH && $resTH->num_rows > 0) {
                                        $tenTH = $resTH->fetch_assoc()['tenTH'];
                                    }
                                }
                        ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border font-monospace"><?= $maSP ?></span></td>
                                    <td class="text-center">
                                        <div class="tz-logo-cell">
                                            <img src=" <?= $logo  ?>">

                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $tenSP ?></div>
                                        <small class="text-muted" style="font-size: 11.5px;"><?= $thongSo ?></small>
                                    </td>
                                    <td><span class="text-secondary"><?= $tenDM ?></span></td>
                                    <td><span class="fw-bold text-primary"><?= $tenTH ?></span></td>
                                    <td><span class="fw-bold text-danger"><?= $giaBan ?> ₫</span></td>
                                    <td>
                                        <?php if ($soLuongTon > 5): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">
                                                <i class="bi bi-box-seam me-1"></i><?= $soLuongTon ?> chiếc
                                            </span>
                                        <?php elseif ($soLuongTon > 0): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold px-2 py-1" title="Sắp hết hàng">
                                                <i class="bi bi-exclamation-circle me-1"></i><?= $soLuongTon ?> chiếc
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger text-white fw-bold px-2 py-1 shadow-sm">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>HẾT HÀNG (0)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border"><?= $thoiGianBaoHanh ?> tháng</span></td>
                                    <td><small class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= $viTriKho ?></small></td>
                                    <td>
                                        <?php if ($trangThai == 1): ?>
                                            <span class="tz-badge tz-badge-success"><i class="bi bi-eye"></i>Hiển thị</span>
                                        <?php else: ?>
                                            <span class="tz-badge tz-badge-secondary"><i class="bi bi-eye-slash"></i>Ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="edit.php?id=<?= $maSP ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil-square"></i></a>

                                            <!-- Form Xóa Mềm -->
                                            <form method="POST" action="san-pham.php" class="d-inline">
                                                <input type="hidden" name="maSP" value="<?= $maSP ?>">
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Chuyển thùng rác"
                                                    data-tz-confirm="Bạn có chắc muốn chuyển sản phẩm <?= $maSP ?> vào Thùng rác?"
                                                    data-tz-title="Xác nhận chuyển thùng rác"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {


                            ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-folder-x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy Sản Phẩm nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có Sản Phẩm máy nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="san-pham.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm Sản Phẩm mới
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>