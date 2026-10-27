<?php
// app/admin/danh-muc/danh-muc.php
$pageTitle = "Quản lý danh mục laptop";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';


// Dữ liệu danh mục  maDM, tenDM, maKM (FK KhuyenMai), moTa, trangThai
include __DIR__ . '/../../config/database.php';
$conn = new mysqli($servername, $username, $password, $dbname);

// Tính tổng danh mục
$sqlTotalDM = "SELECT COUNT(*) as count FROM danhmuc";
$resultTotalDM = $conn->query($sqlTotalDM);
$rowTotalDM = $resultTotalDM->fetch_assoc();
$totalDMCount = $rowTotalDM['count'];

// Tính số lượng danh mục có khuyến mãi
$sqlKMCount = "SELECT COUNT(*) as count FROM danhmuc WHERE maKM IS NOT NULL";
$resultKMCount = $conn->query($sqlKMCount);
$rowKMCount = $resultKMCount->fetch_assoc();
$kmCount = $rowKMCount['count'];

// Tính tổng sản phẩm liên kết
// $sqlProducts = "SELECT COUNT(*) as count FROM sanpham";
// $resultProducts = $conn->query($sqlProducts);
// $rowProducts = $resultProducts->fetch_assoc();
// $totalProducts = $rowProducts['count'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maDM = $_POST['maDM'];




    // nếu như chỉ có đoạn này thì các sản phẩm đang thuộc danh mục thì danh mục ko đc xóa 
    $sqlDelete = "DELETE FROM danhmuc WHERE maDM = $maDM";
    $conn->query($sqlDelete);

    echo "<script>window.location.href='danh-muc.php';</script>";
    exit;
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Danh mục máy</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Danh Mục Sản Phẩm Laptop</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Phân loại các dòng laptop theo mục đích sử dụng và liên kết chương trình khuyến mãi theo danh mục.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="../khuyen-mai/khuyen-mai.php" class="btn btn-tz-outline">
                    <i class="bi bi-tags"></i>Quản lý Khuyến Mãi
                </a>
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-plus-lg"></i>Thêm danh mục mới
                </a>
            </div>
        </div>

        <!-- 3 Thẻ Metric tóm tắt -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG DANH MỤC</div>
                        <div class="tz-metric-value"><?= $totalDMCount ?></div>
                        <small class="text-muted"><i class="bi bi-grid me-1"></i>Phân khúc sản phẩm</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">DANH MỤC CÓ KHUYẾN MÃI </div>
                        <div class="tz-metric-value text-warning"><?= $kmCount  ?></div>
                        <small class="text-warning"><i class="bi bi-tag-fill me-1"></i>Đang chạy chiết khấu</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG LAPTOP LIÊN KẾT</div>
                        <div class="tz-metric-value text-primary"></div>
                        <small class="text-muted"><i class="bi bi-laptop me-1"></i>Mẫu máy đang bán</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-info-light); color: var(--tz-info);">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Bảng Dữ Liệu Danh Mục (Thể hiện rõ quan hệ với KhuyenMai và SanPham) -->
        <div class="tz-card">
            <!-- Header: Tiêu đề danh sách & Tổng số lượng danh mục -->
            <div class="tz-card-header py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="tz-card-title mb-0">
                        <i class="bi bi-list-nested text-primary"></i>Danh Sách Danh Mục Máy Tính
                    </h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace px-2.5 py-1">
                        <?= $totalDMCount ?? '0' ?> danh mục
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="create.php" class="btn btn-sm btn-tz-primary">
                        <i class="bi bi-plus-lg me-1"></i>Thêm danh mục
                    </a>
                </div>
            </div>

            <!-- Thanh Công Cụ Lọc & Tìm Kiếm Chuẩn UI/UX Pro Max -->
            <div class="p-3 border-bottom bg-light-subtle">
                <form action="danh-muc.php" method="GET" class="row g-2 align-items-center">
                    <div class="col-xl-4 col-lg-4 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control border-start-0 ps-2" placeholder="Nhập tên danh mục để tìm kiếm...">
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                                <i class="bi bi-tag text-warning"></i>
                            </span>
                            <select name="KM_option" class="form-select border-start-0 ps-2">
                                <option value="">Áp Dụng KM (Tất cả)</option>
                                <option value="da_ap" <?= ($_GET['KM_option'] ?? '') === 'da_ap' ? 'selected' : '' ?>>Đã Áp Dụng</option>
                                <option value="chua_ap" <?= ($_GET['KM_option'] ?? '') === 'chua_ap' ? 'selected' : '' ?>>Chưa Áp Dụng</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                                <i class="bi bi-toggle-on text-info"></i>
                            </span>
                            <select name="trangthai" class="form-select border-start-0 ps-2">
                                <option value="">Trạng Thái (Tất cả)</option>
                                <option value="1" <?= ($_GET['trangthai'] ?? '') === '1' ? 'selected' : '' ?>>Hiển Thị</option>
                                <option value="0" <?= ($_GET['trangthai'] ?? '') === '0' ? 'selected' : '' ?>>Ẩn</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-tz-primary flex-fill d-inline-flex align-items-center justify-content-center" title="Lọc dữ liệu">
                                <i class="bi bi-funnel-fill me-1"></i>Lọc
                            </button>
                            <a href="danh-muc.php" class="btn btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5" title="Đặt lại bộ lọc">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th style="width: 80px;">MÃ DM</th>
                            <th>TÊN DANH MỤC</th>
                            <th>KHUYẾN MÃI ÁP DỤNG </th>
                            <th>MÔ TẢ CHI TIẾT </th>
                            <th>SẢN PHẨM LIÊN KẾT</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if ($conn->connect_error) {
                            die("connect fail: " . $conn->connect_error);
                        }

                        $search = $_GET['search'] ?? '';
                        $KM_option = $_GET['KM_option'] ?? '';
                        $trangthai = $_GET['trangthai'] ?? '';

                        $sql = "SELECT * FROM danhmuc WHERE 1=1";
                        if (!empty($search)) {
                            $sql .= " AND tenDM LIKE '%$search%'";
                        }

                        if (!empty($KM_option)) {
                            if ($KM_option === 'da_ap') {
                                $sql .= " AND maKM IS NOT NULL AND maKM != ''";
                            } elseif ($KM_option === 'chua_ap') {
                                $sql .= " AND (maKM IS NULL OR maKM = '')";
                            }
                        }

                        if (!empty($trangthai)) {
                            $sql .= " AND trangThai = $trangthai";
                        }

                        $resultSql = $conn->query($sql);
                        if ($resultSql->num_rows > 0) {
                            while ($row = $resultSql->fetch_assoc()) {
                                $tenDM = $row["tenDM"];
                                $maDM  = $row["maDM"];
                                $trangThai = $row["trangThai"];
                                $moTa = $row["moTa"];
                                $maKM = $row["maKM"];
                                $sqlProducts = "SELECT * FROM sanpham WHERE maDM = '$maDM'";
                                $resultProducts = $conn->query($sqlProducts);
                                $totalProducts = $resultProducts->num_rows;

                        ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td><span class="badge bg-light text-dark border font-monospace"><?= $maDM ?></span></td>
                                    <td class="fw-bold text-dark">
                                        <a href="../san-pham/san-pham.php?danhMuc=<?= $maDM ?>" class="text-decoration-none text-dark" title="Xem các sản phẩm thuộc danh mục này">
                                            <i class="bi bi-folder-fill text-primary me-2"></i>
                                            <?= $tenDM ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <?php
                                            if ($maKM != null) {
                                            ?>
                                                <a href="../khuyen-mai/khuyen-mai.php?search=" class="badge bg-warning-subtle text-dark border border-warning text-decoration-none fw-semibold px-2 py-1" title="Chi tiết khuyến mãi">
                                                    <i class="bi bi-tag-fill text-danger me-1"></i>
                                                    <?= $maKM ?>
                                                </a>
                                            <?php
                                            } else {
                                            ?>
                                                <span class="badge bg-light text-muted border"><i class="bi bi-dash-circle me-1"></i>Chưa áp dụng</span>
                                            <?php
                                            }
                                            ?>
                                            </a>
                                        </div>
                                        <!-- <span class="badge bg-light text-muted border"><i class="bi bi-dash-circle me-1"></i>Chưa áp dụng</span> -->
                                        <!-- <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-primary text-decoration-none" 
                                        onclick="openApDungKM()" 
                                        title="Gán hoặc đổi mã khuyến mãi (apDungKhuyenMai)">
                                    <i class="bi bi-pencil-fill" style="font-size: 11px;"></i>
                                </button> -->
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 260px;" title="">
                                            <?= $moTa ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="../san-pham/san-pham.php?danhMuc=<?= $maDM ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none" title="Xem danh sách sản phẩm">
                                            <i class="bi bi-laptop me-1"></i><?php echo $totalProducts ?> dòng máy
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($trangThai == 1) {
                                        ?>
                                            <span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Hoạt động</span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="tz-badge tz-badge-secondary"><i class="bi bi-eye-slash"></i>Ẩn</span>
                                        <?php
                                        } ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?maDM=<?= $maDM ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="danh-muc.php" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="maDM" value="<?= $maDM ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Xóa" data-tz-confirm="Bạn có chắc muốn xóa danh mục <?= $tenDM ?>"
                                                data-tz-title="Xác nhận xóa danh mục">
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
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="tz-modal-badge tz-badge-warning mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                            <i class="bi bi-folder-x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy danh mục nào</h5>
                                        <p class="text-muted small mb-3">Hiện chưa có danh mục máy nào trong hệ thống hoặc không khớp với bộ lọc tìm kiếm.</p>
                                        <div class="d-flex gap-2">
                                            <a href="danh-muc.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Tải lại danh sách
                                            </a>
                                            <a href="create.php" class="btn btn-sm btn-tz-primary px-3 rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm danh mục mới
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

<!-- MODAL GÁN KHUYẾN MÃI CHO DANH MỤC: apDungKhuyenMai(maDM, maKM) -->
<!-- <div class="modal fade" id="modalApDungKM" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <form method="POST" action="danh-muc.php">
                <input type="hidden" name="action" value="apDungKhuyenMai">
                <input type="hidden" name="maDM" id="modalMaDM">
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <h5 class="modal-title fw-bold text-dark fs-6">
                        <i class="bi bi-tag-fill text-danger me-2"></i>Áp Dụng Khuyến Mãi Danh Mục
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Danh mục được chọn</label>
                        <div class="fw-bold text-dark" id="modalTenDM">Laptop Gaming</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn mã khuyến mãi (maKM)</label>
                        <select name="maKM" id="modalSelectKM" class="form-select">
                            <option value="">-- Không áp dụng khuyến mãi (Gỡ mã) --</option>
                            <option value="KM2026-T10">KM2026-T10 - Flash Sale Mùa Thu (-10%)</option>
                            <option value="BACK2SCHOOL">BACK2SCHOOL - Tựu trường giảm 1.500.000₫</option>
                            <option value="VIP2026">VIP2026 - Đặc quyền VIP Giảm 5%</option>
                        </select>
                        <small class="text-muted">Chọn mã để áp dụng chiết khấu tự động cho toàn bộ laptop thuộc danh mục này.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5 border-top">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-tz-primary btn-sm px-3">
                        <i class="bi bi-check2-circle me-1"></i>Lưu áp dụng (apDungKhuyenMai)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!-- <script>
function openApDungKM(maDM, tenDM, maKM) {
    document.getElementById('modalMaDM').value = maDM;
    document.getElementById('modalTenDM').textContent = `#${maDM} - ${tenDM}`;
    document.getElementById('modalSelectKM').value = maKM || '';
    if (window.bootstrap && window.bootstrap.Modal) {
        new bootstrap.Modal(document.getElementById('modalApDungKM')).show();
    }
}
</script> -->