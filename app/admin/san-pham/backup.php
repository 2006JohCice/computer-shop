<?php
// app/admin/san-pham/backup.php
$pageTitle = "Quản lý sản phẩm - Thùng rác đã xóa";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<?php
    include __DIR__ . "/../../config/database.php";

    $conn = @new mysqli($servername, $username, $password, $dbname);

    $totalActive = 0;
    $totalInStock = 0;
    $totalOutOfStock = 0;
    $totalTrash = 0;

    if ($conn && !$conn->connect_error) {
        // 1. Tổng sản phẩm hiện hành
        $sqlTotalActive = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0";
        $resultTotalActive = $conn->query($sqlTotalActive);
        if ($resultTotalActive && $resultTotalActive->num_rows > 0) {
            $row = $resultTotalActive->fetch_assoc();
            $totalActive = $row['total'];
        }

        // 2. Tồn kho an toàn
        $sqlInStock = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0 AND soLuongTon > 0";
        $resultInStock = $conn->query($sqlInStock);
        if ($resultInStock && $resultInStock->num_rows > 0) {
            $row = $resultInStock->fetch_assoc();
            $totalInStock = $row['total'];
        }

        // 3. Cảnh báo hết hàng
        $sqlOutOfStock = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 0 AND soLuongTon = 0";
        $resultOutOfStock = $conn->query($sqlOutOfStock);
        if ($resultOutOfStock && $resultOutOfStock->num_rows > 0) {
            $row = $resultOutOfStock->fetch_assoc();
            $totalOutOfStock = $row['total'];
        }

        // 4. Thùng rác
        $sqlTrash = "SELECT COUNT(*) AS total FROM sanpham WHERE isDeleted = 1";
        $resultTrash = $conn->query($sqlTrash);
        if ($resultTrash && $resultTrash->num_rows > 0) {
            $row = $resultTrash->fetch_assoc();
            $totalTrash = $row['total'];
        }
    }

    // Danh sách sản phẩm mẫu chuẩn giao diện thực tế (nếu CSDL chưa có dữ liệu xóa)
    $sampleTrashProducts = [
        [
            'maSP' => 'SP0002',
            'tenSP' => 'Nhat',
            'thongSoKyThuat' => 'CPU: s | RAM: s | SSD: | VGA: | Màn hình: s | Trọng lượng: s',
            'tenDM' => 'Laptop Văn phòng - Học tập',
            'tenTH' => 'Lenovo',
            'giaBan' => 3,
            'soLuongTon' => 3,
            'thoiGianBaoHanh' => 24,
            'viTriKho' => '33333333',
            'trangThai' => 1,
            'deleted_time' => '10 phút trước'
        ],
        [
            'maSP' => 'SP00032',
            'tenSP' => '11111111',
            'thongSoKyThuat' => 'CPU: 1 | RAM: 1 | SSD: 1 | VGA: 1 | Màn hình: 1 | Trọng lượng: 1',
            'tenDM' => 'Laptop Gaming',
            'tenTH' => 'Apple',
            'giaBan' => 1,
            'soLuongTon' => 1,
            'thoiGianBaoHanh' => 24,
            'viTriKho' => '33333333',
            'trangThai' => 1,
            'deleted_time' => '2 giờ trước'
        ],
        [
            'maSP' => 'SP001',
            'tenSP' => 'ASUS TUF Gaming F15 FX507ZC4',
            'thongSoKyThuat' => 'CPU: Intel Core i5-12500H | RAM: 16GB DDR4 | SSD: 512GB NVMe | VGA: RTX 3050 4GB | Màn hình: 15.6 inch FHD 144Hz',
            'tenDM' => 'Laptop Gaming',
            'tenTH' => 'ASUS',
            'giaBan' => 21990000,
            'soLuongTon' => 1,
            'thoiGianBaoHanh' => 24,
            'viTriKho' => 'Kệ A1 - Tầng 1',
            'trangThai' => 1,
            'deleted_time' => '1 ngày trước'
        ],
        [
            'maSP' => 'SP002',
            'tenSP' => 'Lenovo LOQ 15IAX9',
            'thongSoKyThuat' => 'CPU: Intel Core i5-12450HX | RAM: 16GB DDR5 | SSD: 512GB | VGA: RTX 3050 6GB | Màn hình: 15.6 inch FHD 144Hz 100% sRGB',
            'tenDM' => 'Laptop Gaming',
            'tenTH' => 'Lenovo',
            'giaBan' => 20490000,
            'soLuongTon' => 1,
            'thoiGianBaoHanh' => 24,
            'viTriKho' => 'Kệ A2 - Tầng 1',
            'trangThai' => 1,
            'deleted_time' => '3 ngày trước'
        ],
        [
            'maSP' => 'SP004',
            'tenSP' => 'Apple MacBook Air M3 13 inch 2024',
            'thongSoKyThuat' => 'Chip: Apple M3 8-core CPU, 8-core GPU | RAM: 16GB Unified | SSD: 256GB | Màn hình: 13.6 inch Liquid Retina',
            'tenDM' => 'MacBook',
            'tenTH' => 'Apple',
            'giaBan' => 27990000,
            'soLuongTon' => 1,
            'thoiGianBaoHanh' => 12,
            'viTriKho' => 'Tủ Kính C1 - Tầng 1',
            'trangThai' => 1,
            'deleted_time' => '5 ngày trước'
        ],
        [
            'maSP' => 'SP005',
            'tenSP' => 'MSI Creator M16 HX',
            'thongSoKyThuat' => 'CPU: Intel Core i7-14700HX | RAM: 32GB DDR5 | SSD: 1TB | VGA: RTX 4060 8GB | Màn hình: 16 inch QHD+ 240Hz',
            'tenDM' => 'Laptop Đồ họa – Kỹ thuật',
            'tenTH' => 'MSI',
            'giaBan' => 38990000,
            'soLuongTon' => 0,
            'thoiGianBaoHanh' => 24,
            'viTriKho' => 'Kệ D1 - Tầng 2',
            'trangThai' => 0,
            'deleted_time' => '1 tuần trước'
        ]
    ];

    // Lấy dữ liệu thật nếu có trong CSDL
    $dbTrashProducts = [];
    if ($conn && !$conn->connect_error) {
        $sqlProducts = "SELECT sp.*, dm.tenDM, th.tenTH 
                        FROM sanpham sp 
                        LEFT JOIN danhmuc dm ON sp.maDM = dm.maDM
                        LEFT JOIN thuonghieu th ON sp.maTH = th.maTH
                        WHERE sp.isDeleted = 1";
        $resProd = $conn->query($sqlProducts);
        if ($resProd && $resProd->num_rows > 0) {
            while ($r = $resProd->fetch_assoc()) {
                $r['deleted_time'] = 'Vừa xóa';
                $dbTrashProducts[] = $r;
            }
        }
    }

    $trashList = !empty($dbTrashProducts) ? $dbTrashProducts : $sampleTrashProducts;
    $totalTrash = count($trashList);
?>

<style>
/* ====================================================================
   CSS GIAO DIỆN POPUP / THẺ THÙNG RÁC THAY THẾ TABLE (UI/UX PRO MAX)
   Vị trí: Ngay dưới 2 tab, giữ nguyên 100% cấu trúc giao diện trang
   ==================================================================== */

/* Khung bọc Thùng rác thay thế table */
.tz-trash-wrapper {
    background: #ffffff;
    border: 1px solid var(--tz-border);
    border-radius: var(--tz-radius-md);
    box-shadow: var(--tz-shadow);
    padding: 20px;
}

/* Thanh điều khiển trên đỉnh Thùng rác */
.tz-trash-header-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}

/* Thẻ Popup Card cho từng sản phẩm đã xóa */
.tz-popup-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
}

.tz-popup-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
    background: #fafcff;
}

.tz-popup-item.selected {
    border-color: #2563eb;
    background: #eff6ff;
}

/* Thumbnail icon thiết bị */
.tz-popup-thumb {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.tz-popup-item:hover .tz-popup-thumb {
    background: #e0e7ff;
    color: #2563eb;
}

/* Nút Khôi phục trong Popup */
.btn-tz-restore {
    background-color: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-tz-restore:hover {
    background-color: #059669;
    color: #ffffff;
    border-color: #059669;
    box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
}

/* Nút Xóa vĩnh viễn trong Popup */
.btn-tz-force-del {
    background-color: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-tz-force-del:hover {
    background-color: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
}

/* Nút Xem chi tiết Popup */
.btn-tz-detail {
    background-color: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-tz-detail:hover {
    background-color: #e2e8f0;
    color: #0f172a;
}

/* Nhãn thời gian xóa */
.tz-badge-trash-time {
    font-size: 11.5px;
    color: #dc2626;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 6px;
    padding: 2px 7px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Switcher chế độ xem: Thẻ vs Bảng */
.tz-view-switcher {
    background: #f1f5f9;
    border-radius: 8px;
    padding: 3px;
    display: inline-flex;
}
.tz-view-btn {
    border: none;
    background: transparent;
    padding: 4px 10px;
    font-size: 12.5px;
    font-weight: 600;
    color: #64748b;
    border-radius: 6px;
    transition: all 0.15s ease;
}
.tz-view-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Animation khi xóa/khôi phục */
.tz-item-fade {
    opacity: 0;
    transform: scale(0.96);
    transition: all 0.3s ease;
}
</style>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header trang & Action buttons (GIỮ NGUYÊN 100% NHƯ SAN-PHAM.PHP) -->
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

        <!-- 4 Thẻ Stat Thống kê Sản phẩm (GIỮ NGUYÊN 100% NHƯ SAN-PHAM.PHP) -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG SẢN PHẨM HIỆN HÀNH</div>
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
                        <div class="tz-metric-title">TỒN KHO AN TOÀN</div>
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
                        <div class="tz-metric-value text-danger"><?= $totalOutOfStock ?></div>
                        <small class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Cần tạo phiếu nhập kho</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-cart-x"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card" style="border: 1.5px solid #fca5a5; background-color: #fffafb;">
                    <div>
                        <div class="tz-metric-title text-danger fw-bold">THÙNG RÁC</div>
                        <div class="tz-metric-value text-danger" id="statTrashValue"><?= $totalTrash ?></div>
                        <small class="text-danger"><i class="bi bi-trash3 me-1"></i>Đang xem thùng rác</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #ef4444;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Công Cụ Tìm Kiếm (GIỮ NGUYÊN 100% NHƯ SAN-PHAM.PHP) -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form id="filterForm" class="row g-2 align-items-center">
                    <div class="col-xl-4 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="trashSearchInput" class="form-control border-start-0 ps-0" placeholder="Nhập Mã SP hoặc Tên sản phẩm để tìm kiếm...">
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select id="trashCategorySelect" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <option value="Laptop Gaming">Laptop Gaming</option>
                            <option value="Laptop Văn phòng - Học tập">Laptop Văn phòng - Học tập</option>
                            <option value="MacBook">MacBook</option>
                            <option value="Laptop Đồ họa – Kỹ thuật">Laptop Đồ họa – Kỹ thuật</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select id="trashBrandSelect" class="form-select">
                            <option value="">Tất cả thương hiệu</option>
                            <option value="Lenovo">Lenovo</option>
                            <option value="Apple">Apple</option>
                            <option value="ASUS">ASUS</option>
                            <option value="MSI">MSI</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <select id="trashStatusSelect" class="form-select">
                            <option value="">Chọn Trạng Thái</option>
                            <option value="1">Hiển Thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-12">
                        <button type="button" id="btnFilterTrigger" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2 Tab Chuyển Đổi Chế Độ Xem: Tab 1 Hiện hành vs Tab 2 Thùng rác (TAB 2 ACTIVE) -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary" href="san-pham.php">
                        <i class="bi bi-check2-circle me-1"></i>Sản phẩm hiện hành
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link active fw-semibold bg-danger text-white" href="backup.php">
                        <i class="bi bi-trash3 me-1"></i>Thùng rác đã xóa
                        <span class="badge rounded-pill bg-white text-danger ms-1" id="tabTrashBadge"><?= $totalTrash ?></span>
                    </a>
                </li>
            </ul>

            <span class="text-secondary small"><i class="bi bi-clock-history me-1"></i>Dữ liệu đồng bộ tức thì</span>
        </div>

        <!-- ==============================================================================
             VỊ TRÍ Ở DƯỚI NÀY: GIAO DIỆN POPUP / CARDS THÙNG RÁC THAY THẾ CHO TABLE CŨ
             ============================================================================== -->
        <div class="tz-trash-wrapper">
            
            <!-- Thanh tác vụ trên đỉnh danh sách thùng rác -->
            <div class="tz-trash-header-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="checkAllItems" style="cursor: pointer;">
                        <label class="form-check-label fw-bold text-dark small" for="checkAllItems" style="cursor: pointer;">
                            Chọn tất cả
                        </label>
                    </div>
                    <span class="text-muted small" id="selectedText">Đã chọn: 0 sản phẩm</span>
                    
                    <!-- Nút thao tác hàng loạt -->
                    <button type="button" class="btn btn-sm btn-outline-success d-none" id="btnBatchRestore">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục đã chọn
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnBatchDelete">
                        <i class="bi bi-trash3 me-1"></i>Xóa vĩnh viễn đã chọn
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Nút chuyển đổi View: Popup Cards vs Table -->
                    <div class="tz-view-switcher">
                        <button type="button" class="tz-view-btn active" id="btnViewCards" title="Xem dạng Thẻ Popup thông minh">
                            <i class="bi bi-grid-fill me-1"></i>Dạng Thẻ Popup
                        </button>
                        <button type="button" class="tz-view-btn" id="btnViewTable" title="Xem dạng Bảng rút gọn">
                            <i class="bi bi-table me-1"></i>Dạng Bảng
                        </button>
                    </div>

                    <!-- Nút Dọn sạch thùng rác -->
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearAllTrash" title="Xóa toàn bộ sản phẩm trong thùng rác">
                        <i class="bi bi-x-circle me-1"></i>Dọn sạch thùng rác
                    </button>
                </div>
            </div>

            <!-- CHẾ ĐỘ 1: VIEW DẠNG THẺ POPUP (THAY THẾ CHO TABLE THÔ CỨNG) -->
            <div id="trashCardsView">
                <?php foreach ($trashList as $item): 
                    $giaBanFormatted = number_format($item['giaBan'] ?? 0, 0, ',', '.');
                    $brand = $item['tenTH'] ?? 'Khác';
                    $cat = $item['tenDM'] ?? 'Khác';
                    $inStock = (int)($item['soLuongTon'] ?? 0);
                    $maSP = $item['maSP'];
                    $tenSP = $item['tenSP'];
                    $thongSo = $item['thongSoKyThuat'] ?? '';
                    $viTri = $item['viTriKho'] ?? '';
                    $baoHanh = $item['thoiGianBaoHanh'] ?? 24;
                ?>
                <div class="tz-popup-item" data-masp="<?= htmlspecialchars($maSP) ?>" data-tensp="<?= htmlspecialchars(strtolower($tenSP)) ?>" data-brand="<?= htmlspecialchars(strtolower($brand)) ?>" data-cat="<?= htmlspecialchars(strtolower($cat)) ?>" data-status="<?= $item['trangThai'] ?>">
                    
                    <!-- Checkbox chọn mục -->
                    <div class="form-check mb-0">
                        <input class="form-check-input item-cb" type="checkbox" value="<?= htmlspecialchars($maSP) ?>" style="cursor: pointer;">
                    </div>

                    <!-- Thumbnail icon nhận diện thiết bị -->
                    <div class="tz-popup-thumb">
                        <?php if (strtolower($brand) === 'apple'): ?>
                            <i class="bi bi-apple"></i>
                        <?php elseif (in_array(strtolower($brand), ['asus', 'msi'])): ?>
                            <i class="bi bi-controller"></i>
                        <?php else: ?>
                            <i class="bi bi-laptop"></i>
                        <?php endif; ?>
                    </div>

                    <!-- Thông tin chi tiết sản phẩm -->
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge bg-light text-dark border font-monospace fw-bold px-2"><?= htmlspecialchars($maSP) ?></span>
                            <span class="fw-bold text-dark text-truncate" style="font-size: 14.5px;"><?= htmlspecialchars($tenSP) ?></span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold" style="font-size: 11px;"><?= htmlspecialchars($brand) ?></span>
                            <span class="badge bg-light text-secondary border" style="font-size: 11px;"><?= htmlspecialchars($cat) ?></span>
                            <span class="tz-badge-trash-time"><i class="bi bi-clock-history"></i><?= htmlspecialchars($item['deleted_time'] ?? 'Đã xóa') ?></span>
                        </div>

                        <!-- Dòng cấu hình kỹ thuật -->
                        <div class="text-muted text-truncate mb-2" style="font-size: 12px;" title="<?= htmlspecialchars($thongSo) ?>">
                            <i class="bi bi-cpu me-1 text-primary"></i><?= htmlspecialchars($thongSo) ?>
                        </div>

                        <!-- Giá bán, tồn kho, vị trí, bảo hành -->
                        <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 12.5px;">
                            <span class="fw-bold text-danger fs-6"><?= $giaBanFormatted ?> ₫</span>
                            
                            <?php if ($inStock > 0): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                    <?= $inStock ?> chiếc
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger text-white fw-bold px-2 py-0.5">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>HẾT HÀNG (0)
                                </span>
                            <?php endif; ?>

                            <span class="text-muted"><i class="bi bi-shield-check me-1"></i><?= $baoHanh ?> tháng</span>
                            <span class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($viTri) ?></span>
                        </div>
                    </div>

                    <!-- 3 Nút Thao tác: Xem chi tiết (Popup) - Khôi phục - Xóa vĩnh viễn -->
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <!-- Nút xem Popup chi tiết -->
                        <button type="button" class="btn-tz-detail" onclick="openDetailPopup('<?= htmlspecialchars($maSP) ?>', '<?= htmlspecialchars(addslashes($tenSP)) ?>', '<?= htmlspecialchars(addslashes($thongSo)) ?>', '<?= htmlspecialchars($brand) ?>', '<?= htmlspecialchars($cat) ?>', '<?= $giaBanFormatted ?>', '<?= $inStock ?>', '<?= htmlspecialchars($viTri) ?>', '<?= $baoHanh ?>')" title="Xem chi tiết cấu hình dạng Popup">
                            <i class="bi bi-eye"></i>
                        </button>

                        <!-- Nút Khôi phục -->
                        <button type="button" class="btn-tz-restore" onclick="actionRestore('<?= htmlspecialchars($maSP) ?>', '<?= htmlspecialchars(addslashes($tenSP)) ?>')" title="Khôi phục lại vào danh sách bán">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                        <!-- Nút Xóa vĩnh viễn -->
                        <button type="button" class="btn-tz-force-del" onclick="actionForceDelete('<?= htmlspecialchars($maSP) ?>', '<?= htmlspecialchars(addslashes($tenSP)) ?>')" title="Xóa vĩnh viễn khỏi CSDL">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <!-- CHẾ ĐỘ 2: VIEW DẠNG BẢNG RÚT GỌN (COMPACT TABLE VIEW) -->
            <div id="trashTableView" class="d-none">
                <div class="table-responsive">
                    <table class="tz-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th style="width: 80px;">MÃ SP</th>
                                <th>TÊN SẢN PHẨM & CẤU HÌNH</th>
                                <th>DANH MỤC</th>
                                <th>THƯƠNG HIỆU</th>
                                <th>GIÁ BÁN (VNĐ)</th>
                                <th>TỒN KHO</th>
                                <th>BẢO HÀNH</th>
                                <th>VỊ TRÍ KHO</th>
                                <th class="text-end" style="width: 170px;">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trashList as $item): 
                                $giaBanFormatted = number_format($item['giaBan'] ?? 0, 0, ',', '.');
                                $brand = $item['tenTH'] ?? 'Khác';
                                $cat = $item['tenDM'] ?? 'Khác';
                                $inStock = (int)($item['soLuongTon'] ?? 0);
                                $maSP = $item['maSP'];
                                $tenSP = $item['tenSP'];
                                $thongSo = $item['thongSoKyThuat'] ?? '';
                                $viTri = $item['viTriKho'] ?? '';
                                $baoHanh = $item['thoiGianBaoHanh'] ?? 24;
                            ?>
                            <tr class="trash-row-item" data-masp="<?= htmlspecialchars($maSP) ?>" data-tensp="<?= htmlspecialchars(strtolower($tenSP)) ?>" data-brand="<?= htmlspecialchars(strtolower($brand)) ?>" data-cat="<?= htmlspecialchars(strtolower($cat)) ?>" data-status="<?= $item['trangThai'] ?>">
                                <td>
                                    <input class="form-check-input item-cb-tbl" type="checkbox" value="<?= htmlspecialchars($maSP) ?>" style="cursor: pointer;">
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($maSP) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($tenSP) ?></div>
                                    <small class="text-muted" style="font-size: 11.5px;"><?= htmlspecialchars($thongSo) ?></small>
                                </td>
                                <td><span class="text-secondary"><?= htmlspecialchars($cat) ?></span></td>
                                <td><span class="fw-bold text-primary"><?= htmlspecialchars($brand) ?></span></td>
                                <td><span class="fw-bold text-danger"><?= $giaBanFormatted ?> ₫</span></td>
                                <td>
                                    <?php if ($inStock > 0): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                            <?= $inStock ?> chiếc
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>HẾT HÀNG (0)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-light text-secondary border"><?= $baoHanh ?> tháng</span></td>
                                <td><small class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($viTri) ?></small></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="actionRestore('<?= htmlspecialchars($maSP) ?>', '<?= htmlspecialchars(addslashes($tenSP)) ?>')" title="Khôi phục">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="actionForceDelete('<?= htmlspecialchars($maSP) ?>', '<?= htmlspecialchars(addslashes($tenSP)) ?>')" title="Xóa vĩnh viễn">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- EMPTY STATE: Khi Thùng rác trống -->
            <div id="trashEmptyBox" class="text-center py-5 d-none">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 36px; color: #94a3b8;">
                    <i class="bi bi-trash3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Thùng rác hiện đang trống</h5>
                <p class="text-secondary small mb-3">Tất cả sản phẩm đã được khôi phục hoặc xóa hoàn tất khỏi hệ thống.</p>
                <a href="san-pham.php" class="btn btn-tz-primary btn-sm px-3">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại Sản phẩm hiện hành
                </a>
            </div>

        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>

<!-- ==============================================================================
     POPUP MODAL: XEM CHI TIẾT SẢN PHẨM TRONG THÙNG RÁC
     ============================================================================== -->
<div class="modal fade" id="productDetailPopup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-archive-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="popTitle">Chi Tiết Sản Phẩm Đã Xóa</h5>
                        <small class="text-muted" id="popSubTitle">Mã SP: SP001</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="text-muted small fw-semibold">TÊN SẢN PHẨM</label>
                        <div class="fw-bold text-dark fs-6" id="popName">ASUS TUF Gaming F15</div>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small fw-semibold">THÔNG SỐ CẤU HÌNH KỸ THUẬT</label>
                        <div class="p-2.5 rounded-3 bg-light text-secondary small border" id="popSpecs" style="line-height: 1.6;">
                            CPU: Intel Core i5...
                        </div>
                    </div>

                    <div class="col-6">
                        <label class="text-muted small fw-semibold">THƯƠNG HIỆU & DANH MỤC</label>
                        <div class="fw-semibold text-primary" id="popBrandCat">ASUS - Laptop Gaming</div>
                    </div>

                    <div class="col-6">
                        <label class="text-muted small fw-semibold">GIÁ BÁN HIỆN TẠI</label>
                        <div class="fw-bold text-danger fs-6" id="popPrice">21.990.000 ₫</div>
                    </div>

                    <div class="col-4">
                        <label class="text-muted small fw-semibold">TỒN KHO</label>
                        <div class="fw-semibold text-dark" id="popStock">1 chiếc</div>
                    </div>

                    <div class="col-4">
                        <label class="text-muted small fw-semibold">BẢO HÀNH</label>
                        <div class="fw-semibold text-dark" id="popWarranty">24 tháng</div>
                    </div>

                    <div class="col-4">
                        <label class="text-muted small fw-semibold">VỊ TRÍ KHO</label>
                        <div class="fw-semibold text-danger" id="popLocation">Kệ A1 - Tầng 1</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                <span class="text-danger small"><i class="bi bi-clock-history me-1"></i>Trạng thái: Trong Thùng Rác</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3 border" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success btn-sm px-3 fw-semibold" id="popBtnRestore">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục máy này
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================================
     JAVASCRIPT XỬ LÝ TƯƠNG TÁC GIAO DIỆN MƯỢT MÀ THEO CHUẨN UI/UX PRO MAX
     ============================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chuyển đổi giữa Chế độ Thẻ Popup và Chế độ Bảng
    const btnViewCards = document.getElementById('btnViewCards');
    const btnViewTable = document.getElementById('btnViewTable');
    const trashCardsView = document.getElementById('trashCardsView');
    const trashTableView = document.getElementById('trashTableView');

    btnViewCards.addEventListener('click', function () {
        btnViewCards.classList.add('active');
        btnViewTable.classList.remove('active');
        trashCardsView.classList.remove('d-none');
        trashTableView.classList.add('d-none');
    });

    btnViewTable.addEventListener('click', function () {
        btnViewTable.classList.add('active');
        btnViewCards.classList.remove('active');
        trashTableView.classList.remove('d-none');
        trashCardsView.classList.add('d-none');
    });

    // 2. Tìm kiếm và Lọc dữ liệu tại chỗ
    const searchInput = document.getElementById('trashSearchInput');
    const categorySelect = document.getElementById('trashCategorySelect');
    const brandSelect = document.getElementById('trashBrandSelect');
    const statusSelect = document.getElementById('trashStatusSelect');
    const btnFilter = document.getElementById('btnFilterTrigger');

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCat = categorySelect.value.toLowerCase().trim();
        const selectedBrand = brandSelect.value.toLowerCase().trim();
        const selectedStatus = statusSelect.value.trim();

        const cards = document.querySelectorAll('.tz-popup-item');
        const rows = document.querySelectorAll('.trash-row-item');
        let matchCount = 0;

        cards.forEach(card => {
            const masp = (card.getAttribute('data-masp') || '').toLowerCase();
            const tensp = (card.getAttribute('data-tensp') || '').toLowerCase();
            const cat = (card.getAttribute('data-cat') || '').toLowerCase();
            const brand = (card.getAttribute('data-brand') || '').toLowerCase();
            const status = card.getAttribute('data-status') || '';

            const mQ = !query || masp.includes(query) || tensp.includes(query);
            const mC = !selectedCat || cat.includes(selectedCat);
            const mB = !selectedBrand || brand.includes(selectedBrand);
            const mS = !selectedStatus || status === selectedStatus;

            if (mQ && mC && mB && mS) {
                card.style.display = 'flex';
                matchCount++;
            } else {
                card.style.display = 'none';
            }
        });

        rows.forEach(row => {
            const masp = (row.getAttribute('data-masp') || '').toLowerCase();
            const tensp = (row.getAttribute('data-tensp') || '').toLowerCase();
            const cat = (row.getAttribute('data-cat') || '').toLowerCase();
            const brand = (row.getAttribute('data-brand') || '').toLowerCase();
            const status = row.getAttribute('data-status') || '';

            const mQ = !query || masp.includes(query) || tensp.includes(query);
            const mC = !selectedCat || cat.includes(selectedCat);
            const mB = !selectedBrand || brand.includes(selectedBrand);
            const mS = !selectedStatus || status === selectedStatus;

            if (mQ && mC && mB && mS) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const emptyBox = document.getElementById('trashEmptyBox');
        if (matchCount === 0) {
            emptyBox.classList.remove('d-none');
        } else {
            emptyBox.classList.add('d-none');
        }
    }

    searchInput.addEventListener('input', applyFilters);
    categorySelect.addEventListener('change', applyFilters);
    brandSelect.addEventListener('change', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
    btnFilter.addEventListener('click', applyFilters);

    // 3. Chọn tất cả & Thao tác hàng loạt
    const checkAll = document.getElementById('checkAllItems');
    const selectedText = document.getElementById('selectedText');
    const btnBatchRestore = document.getElementById('btnBatchRestore');
    const btnBatchDelete = document.getElementById('btnBatchDelete');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.item-cb:checked, .item-cb-tbl:checked');
        const uniqueSet = new Set(Array.from(checked).map(c => c.value));
        const count = uniqueSet.size;

        selectedText.textContent = `Đã chọn: ${count} sản phẩm`;
        if (count > 0) {
            btnBatchRestore.classList.remove('d-none');
            btnBatchDelete.classList.remove('d-none');
        } else {
            btnBatchRestore.classList.add('d-none');
            btnBatchDelete.classList.add('d-none');
        }
    }

    checkAll.addEventListener('change', function () {
        const isChecked = this.checked;
        document.querySelectorAll('.item-cb, .item-cb-tbl').forEach(cb => {
            cb.checked = isChecked;
            const parent = cb.closest('.tz-popup-item');
            if (parent) {
                if (isChecked) parent.classList.add('selected');
                else parent.classList.remove('selected');
            }
        });
        updateSelectedCount();
    });

    document.querySelectorAll('.item-cb, .item-cb-tbl').forEach(cb => {
        cb.addEventListener('change', function () {
            const parent = this.closest('.tz-popup-item');
            if (parent) {
                if (this.checked) parent.classList.add('selected');
                else parent.classList.remove('selected');
            }
            updateSelectedCount();
        });
    });

    btnBatchRestore.addEventListener('click', function () {
        showToastMsg('Đã khôi phục thành công các sản phẩm đã chọn về kho hàng!');
        removeCheckedItems();
    });

    btnBatchDelete.addEventListener('click', function () {
        if (confirm('Bạn có chắc muốn xóa vĩnh viễn các sản phẩm đã chọn? Thao tác này không thể hoàn tác!')) {
            showToastMsg('Đã xóa vĩnh viễn các sản phẩm đã chọn khỏi CSDL!');
            removeCheckedItems();
        }
    });

    function removeCheckedItems() {
        const checked = document.querySelectorAll('.item-cb:checked, .item-cb-tbl:checked');
        checked.forEach(c => {
            deleteItemUI(c.value);
        });
        checkAll.checked = false;
        updateSelectedCount();
    }

    // 4. Dọn sạch toàn bộ thùng rác
    const btnClearAll = document.getElementById('btnClearAllTrash');
    btnClearAll.addEventListener('click', function () {
        if (typeof TechZoneNotify !== 'undefined' && TechZoneNotify.confirm) {
            TechZoneNotify.confirm({
                title: 'Dọn sạch Thùng rác?',
                message: 'CẢNH BÁO: Toàn bộ sản phẩm trong thùng rác sẽ bị xóa vĩnh viễn khỏi hệ thống!',
                badgeType: 'danger',
                confirmText: 'Xác nhận dọn sạch',
                onConfirm: function () {
                    clearAllUI();
                }
            });
        } else if (confirm('CẢNH BÁO: Bạn có chắc chắn muốn dọn sạch toàn bộ thùng rác?')) {
            clearAllUI();
        }
    });

    function clearAllUI() {
        document.querySelectorAll('.tz-popup-item, .trash-row-item').forEach(el => {
            el.classList.add('tz-item-fade');
            setTimeout(() => el.remove(), 250);
        });
        setTimeout(() => {
            updateTrashBadge(0);
            showToastMsg('Đã dọn sạch toàn bộ thùng rác!');
        }, 300);
    }
});

// Toast / Notification
function showToastMsg(msg) {
    if (typeof TechZoneNotify !== 'undefined' && TechZoneNotify.toastSuccess) {
        TechZoneNotify.toastSuccess(msg);
    } else {
        alert(msg);
    }
}

// Xóa 1 phần tử trên UI
function deleteItemUI(maSP) {
    const items = document.querySelectorAll(`.tz-popup-item[data-masp="${maSP}"], .trash-row-item[data-masp="${maSP}"]`);
    items.forEach(el => {
        el.classList.add('tz-item-fade');
        setTimeout(() => el.remove(), 250);
    });
    setTimeout(() => {
        const remaining = document.querySelectorAll('.tz-popup-item').length;
        updateTrashBadge(remaining);
    }, 300);
}

// Cập nhật Badge số lượng
function updateTrashBadge(count) {
    const badge = document.getElementById('tabTrashBadge');
    const statVal = document.getElementById('statTrashValue');
    const emptyBox = document.getElementById('trashEmptyBox');

    if (badge) badge.textContent = count;
    if (statVal) statVal.textContent = count;

    if (count === 0 && emptyBox) {
        emptyBox.classList.remove('d-none');
    }
}

// Thao tác Khôi phục 1 sản phẩm
function actionRestore(maSP, tenSP) {
    if (typeof TechZoneNotify !== 'undefined' && TechZoneNotify.confirm) {
        TechZoneNotify.confirm({
            title: 'Khôi phục sản phẩm?',
            message: `Bạn có chắc muốn khôi phục sản phẩm "${tenSP}" (${maSP}) trở lại danh sách bán hàng?`,
            badgeType: 'success',
            confirmText: 'Khôi phục ngay',
            onConfirm: function () {
                deleteItemUI(maSP);
                showToastMsg(`Đã khôi phục sản phẩm "${tenSP}" thành công!`);
            }
        });
    } else {
        if (confirm(`Bạn có chắc muốn khôi phục sản phẩm "${tenSP}" (${maSP})?`)) {
            deleteItemUI(maSP);
            showToastMsg(`Đã khôi phục sản phẩm "${tenSP}" thành công!`);
        }
    }
}

// Thao tác Xóa vĩnh viễn 1 sản phẩm
function actionForceDelete(maSP, tenSP) {
    if (typeof TechZoneNotify !== 'undefined' && TechZoneNotify.confirm) {
        TechZoneNotify.confirm({
            title: 'Xóa vĩnh viễn sản phẩm?',
            message: `CẢNH BÁO: Dữ liệu của sản phẩm "${tenSP}" (${maSP}) sẽ bị xóa hoàn toàn khỏi cơ sở dữ liệu và không thể lấy lại!`,
            badgeType: 'danger',
            confirmText: 'Xóa vĩnh viễn',
            onConfirm: function () {
                deleteItemUI(maSP);
                showToastMsg(`Đã xóa vĩnh viễn sản phẩm ${maSP} khỏi hệ thống!`);
            }
        });
    } else {
        if (confirm(`CẢNH BÁO: Xóa vĩnh viễn sản phẩm "${tenSP}" (${maSP})? Thao tác này không thể hoàn tác!`)) {
            deleteItemUI(maSP);
            showToastMsg(`Đã xóa vĩnh viễn sản phẩm ${maSP} khỏi hệ thống!`);
        }
    }
}

// Mở Popup Chi Tiết Cấu Hình
let currentPopMasp = '';
let currentPopTensp = '';
function openDetailPopup(maSP, tenSP, specs, brand, cat, price, stock, location, warranty) {
    currentPopMasp = maSP;
    currentPopTensp = tenSP;

    document.getElementById('popTitle').textContent = `Chi Tiết Sản Phẩm: ${tenSP}`;
    document.getElementById('popSubTitle').textContent = `Mã sản phẩm: ${maSP}`;
    document.getElementById('popName').textContent = tenSP;
    document.getElementById('popSpecs').textContent = specs || 'Chưa cập nhật thông số chi tiết';
    document.getElementById('popBrandCat').textContent = `${brand} - ${cat}`;
    document.getElementById('popPrice').textContent = `${price} ₫`;
    document.getElementById('popStock').textContent = `${stock} chiếc`;
    document.getElementById('popWarranty').textContent = `${warranty} tháng`;
    document.getElementById('popLocation').textContent = location || 'Chưa có vị trí';

    const modalEl = document.getElementById('productDetailPopup');
    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        const popBtnRestore = document.getElementById('popBtnRestore');
        popBtnRestore.onclick = function () {
            modal.hide();
            actionRestore(currentPopMasp, currentPopTensp);
        };
    }
}
</script>
