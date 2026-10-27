<?php
// app/admin/don-hang/detail.php
$pageTitle = "Chi tiết đơn bán hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maDH = htmlspecialchars($_GET['id'] ?? 'DH01');

// Trạng thái đơn mô phỏng: tiep_nhan | da_duyet | van_chuyen | hoan_thanh | da_huy
$statusParam = $_GET['status'] ?? 'tiep_nhan';

// Xử lý Action POST
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'approve_order') {
        $statusParam = 'da_duyet';
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Đã duyệt đơn hàng thành công! Đã tự động cập nhật tồn kho và kích hoạt bảo hành theo Serial máy.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'cancel_order') {
        $statusParam = 'da_huy';
        $lyDo = htmlspecialchars($_POST['lyDo'] ?? '');
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-x-circle me-2'></i>Đơn hàng đã được hủy thành công! Số lượng sản phẩm và mã Serial đã được hoàn trả về kho.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'assign_serial') {
        $serial = htmlspecialchars($_POST['maSerial'] ?? '');
        $alertMsg = "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                        <i class='bi bi-upc-scan me-2'></i>Đã gán thành công mã Serial <strong>$serial</strong> cho đơn hàng! Hạn bảo hành: 24 tháng kể từ ngày xuất kho.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'update_shipping') {
        $newShippingStatus = htmlspecialchars($_POST['trangThaiMoi'] ?? 'van_chuyen');
        $statusParam = $newShippingStatus;
        $alertMsg = "<div class='alert alert-primary alert-dismissible fade show' role='alert'>
                        <i class='bi bi-truck me-2'></i>Đã cập nhật trạng thái vận chuyển đơn hàng thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Bảng trạng thái Stepper
$stepIndex = 1;
if ($statusParam === 'tiep_nhan') $stepIndex = 1;
elseif ($statusParam === 'da_duyet') $stepIndex = 2;
elseif ($statusParam === 'van_chuyen') $stepIndex = 3;
elseif ($statusParam === 'hoan_thanh') $stepIndex = 4;
elseif ($statusParam === 'da_huy') $stepIndex = -1;
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header trang & Action buttons -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="don-hang.php" class="text-decoration-none text-muted">Đơn hàng</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= $maDH ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Chi Tiết Đơn Bán Hàng: <?= $maDH ?></h2>
                    <?php if ($statusParam === 'tiep_nhan'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Tiếp nhận (Chờ duyệt)</span>
                    <?php elseif ($statusParam === 'da_duyet'): ?>
                        <span class="badge bg-primary text-white px-3 py-2"><i class="bi bi-check2-circle me-1"></i>Đã duyệt</span>
                    <?php elseif ($statusParam === 'van_chuyen'): ?>
                        <span class="badge bg-info text-white px-3 py-2"><i class="bi bi-truck me-1"></i>Đang vận chuyển</span>
                    <?php elseif ($statusParam === 'hoan_thanh'): ?>
                        <span class="badge bg-success text-white px-3 py-2"><i class="bi bi-check2-all me-1"></i>Hoàn thành</span>
                    <?php elseif ($statusParam === 'da_huy'): ?>
                        <span class="badge bg-danger text-white px-3 py-2"><i class="bi bi-x-octagon me-1"></i>Đã hủy đơn</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nút điều hướng nhanh Demo & Quay lại -->
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group">
                    <a href="detail.php?id=<?= $maDH ?>&status=tiep_nhan" class="btn btn-sm <?= $statusParam === 'tiep_nhan' ? 'btn-warning' : 'btn-outline-secondary' ?>">Tiếp nhận</a>
                    <a href="detail.php?id=<?= $maDH ?>&status=da_duyet" class="btn btn-sm <?= $statusParam === 'da_duyet' ? 'btn-primary' : 'btn-outline-secondary' ?>">Đã duyệt</a>
                    <a href="detail.php?id=<?= $maDH ?>&status=van_chuyen" class="btn btn-sm <?= $statusParam === 'van_chuyen' ? 'btn-info text-white' : 'btn-outline-secondary' ?>">Vận chuyển</a>
                    <a href="detail.php?id=<?= $maDH ?>&status=hoan_thanh" class="btn btn-sm <?= $statusParam === 'hoan_thanh' ? 'btn-success' : 'btn-outline-secondary' ?>">Hoàn thành</a>
                </div>
                <a href="don-hang.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- THANH TIẾN TRÌNH STEPPER (5 Trạng Thái DonHang.trangThaiDon) -->
        <div class="tz-card p-4 mb-4">
            <h5 class="fw-bold text-dark mb-4" style="font-size: 15px;">
                <i class="bi bi-diagram-3 text-primary me-2"></i>Tiến Trình Xử Lý Đơn Hàng (DonHang.trangThaiDon)
            </h5>
            
            <?php if ($statusParam === 'da_huy'): ?>
                <div class="p-3 bg-danger-subtle text-danger border border-danger-subtle rounded d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-x-circle-fill fs-4 me-2 align-middle"></i>
                        <span class="fw-bold">ĐƠN HÀNG ĐÃ BỊ HỦY.</span> Lý do: <em>Khách hàng đổi ý / không nhận máy</em>. Mã Serial đã được hoàn về trạng thái [Trong kho].
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between position-relative px-4">
                    <!-- Progress Bar nền -->
                    <div class="position-absolute top-50 start-0 translate-middle-y w-100" style="height: 4px; background: #e2e8f0; z-index: 1;">
                        <div style="height: 100%; background: #2563eb; width: <?= ($stepIndex - 1) * 33.33 ?>%; transition: width 0.3s ease;"></div>
                    </div>

                    <!-- Bước 1: Tiếp nhận -->
                    <div class="text-center position-relative" style="z-index: 2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-2 text-white <?= $stepIndex >= 1 ? 'bg-primary' : 'bg-secondary' ?>" style="width: 38px; height: 38px;">
                            1
                        </div>
                        <div class="fw-bold <?= $stepIndex >= 1 ? 'text-primary' : 'text-muted' ?>" style="font-size: 13.5px;">Tiếp nhận</div>
                        <small class="text-muted">tiepNhanDon()</small>
                    </div>

                    <!-- Bước 2: Đã duyệt -->
                    <div class="text-center position-relative" style="z-index: 2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-2 text-white <?= $stepIndex >= 2 ? 'bg-primary' : 'bg-secondary' ?>" style="width: 38px; height: 38px;">
                            2
                        </div>
                        <div class="fw-bold <?= $stepIndex >= 2 ? 'text-primary' : 'text-muted' ?>" style="font-size: 13.5px;">Đã duyệt</div>
                        <small class="text-muted">duyetDon()</small>
                    </div>

                    <!-- Bước 3: Đang vận chuyển -->
                    <div class="text-center position-relative" style="z-index: 2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-2 text-white <?= $stepIndex >= 3 ? 'bg-primary' : 'bg-secondary' ?>" style="width: 38px; height: 38px;">
                            3
                        </div>
                        <div class="fw-bold <?= $stepIndex >= 3 ? 'text-primary' : 'text-muted' ?>" style="font-size: 13.5px;">Đang vận chuyển</div>
                        <small class="text-muted">capNhatTrangThaiVanChuyen()</small>
                    </div>

                    <!-- Bước 4: Hoàn thành -->
                    <div class="text-center position-relative" style="z-index: 2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-2 text-white <?= $stepIndex >= 4 ? 'bg-success' : 'bg-secondary' ?>" style="width: 38px; height: 38px;">
                            4
                        </div>
                        <div class="fw-bold <?= $stepIndex >= 4 ? 'text-success' : 'text-muted' ?>" style="font-size: 13.5px;">Hoàn thành</div>
                        <small class="text-muted">Giao thành công</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <!-- Cột Trái: Bảng Chi Tiết Đơn Hàng & Gán Serial -->
            <div class="col-lg-8">
                <div class="tz-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                            <i class="bi bi-cart-check text-primary me-2"></i>Mặt Hàng Đã Đặt
                        </h4>
                        <span class="text-muted small">Mã đơn: <?= $maDH ?></span>
                    </div>

                    <div class="table-responsive">
                        <table class="tz-table">
                            <thead>
                                <tr>
                                    <th>SẢN PHẨM</th>
                                    <th>ĐƠN GIÁ</th>
                                    <th>SỐ LƯỢNG</th>
                                    <th>THÀNH TIỀN</th>
                                    <th>GÁN SERIAL/IMEI (XUẤT BÁN)</th>
                                    <?php if ($statusParam === 'tiep_nhan'): ?>
                                    <th class="text-end" style="width: 80px;">XÓA</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">Dell XPS 13 9320 Plus</div>
                                        <small class="text-muted font-monospace">Mã SP: SP01 • BH: 24 tháng</small>
                                    </td>
                                    <td>35.990.000 ₫</td>
                                    <td>
                                        <?php if ($statusParam === 'tiep_nhan'): ?>
                                            <input type="number" class="form-control form-control-sm" value="1" min="1" max="5" style="width: 70px;">
                                        <?php else: ?>
                                            <span class="fw-bold">1 chiếc</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-danger">35.990.000 ₫</td>
                                    <td>
                                        <!-- BƯỚC GÁN SERIAL XUẤT BÁN -->
                                        <?php if ($statusParam === 'tiep_nhan'): ?>
                                            <form action="detail.php?id=<?= $maDH ?>" method="POST" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="action" value="assign_serial">
                                                <select name="maSerial" class="form-select form-select-sm font-monospace" required style="max-width: 200px;">
                                                    <option value="">-- Chọn Serial trong kho --</option>
                                                    <option value="SN-DELL-XPS-99201" selected>SN-DELL-XPS-99201 (Trong kho)</option>
                                                    <option value="SN-DELL-XPS-99202">SN-DELL-XPS-99202 (Trong kho)</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-success text-nowrap" title="Gán Serial"><i class="bi bi-check-lg"></i> Gán</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace py-2 px-2">
                                                <i class="bi bi-upc-scan me-1"></i>SN-DELL-XPS-99201 (Đã bán)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($statusParam === 'tiep_nhan'): ?>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa sản phẩm (xoaSanPhamKhoiDon)"><i class="bi bi-trash3"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Các nút hành động quy trình -->
                <div class="tz-card p-4">
                    <h5 class="fw-bold text-dark mb-3" style="font-size: 15px;">Thao Tác Điều Phối Đơn Hàng</h5>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($statusParam === 'tiep_nhan'): ?>
                            <!-- Nút DUYỆT ĐƠN HÀNG -->
                            <form action="detail.php?id=<?= $maDH ?>" method="POST">
                                <input type="hidden" name="action" value="approve_order">
                                <button type="submit" class="btn btn-tz-primary py-2 px-3 fw-bold" data-tz-confirm="Hệ thống sẽ duyệt đơn hàng <?= $maDH ?> và tự động trừ số lượng tồn kho sản phẩm." data-tz-title="Xác nhận duyệt đơn hàng" data-tz-type="primary" data-tz-btn-text="Xác nhận duyệt">
                                    <i class="bi bi-check2-circle me-1"></i>DUYỆT ĐƠN HÀNG
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($statusParam === 'da_duyet' || $statusParam === 'van_chuyen'): ?>
                            <!-- Cập nhật trạng thái vận chuyển -->
                            <form action="detail.php?id=<?= $maDH ?>" method="POST" class="d-inline-flex gap-2">
                                <input type="hidden" name="action" value="update_shipping">
                                <select name="trangThaiMoi" class="form-select form-select-sm" style="width: 200px;">
                                    <option value="van_chuyen" <?= $statusParam === 'van_chuyen' ? 'selected' : '' ?>>Đang vận chuyển</option>
                                    <option value="hoan_thanh">Hoàn thành (Đã giao)</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Cập nhật vận chuyển</button>
                            </form>

                            <!-- Nút Xuất Hóa Đơn -->
                            <a href="../hoa-don/detail.php?maDH=<?= $maDH ?>" class="btn btn-outline-success">
                                <i class="bi bi-receipt me-1"></i>Xuất Hóa Đơn
                            </a>

                            <!-- Nút Tạo Phiếu Bảo Hành -->
                            <a href="../bao-hanh/create.php?maDH=<?= $maDH ?>&serial=SN-DELL-XPS-99201" class="btn btn-outline-info">
                                <i class="bi bi-shield-check me-1"></i>Tạo Phiếu Bảo Hành
                            </a>
                        <?php endif; ?>

                        <?php if ($statusParam !== 'da_huy' && $statusParam !== 'hoan_thanh'): ?>
                            <!-- Nút HỦY ĐƠN HÀNG -->
                            <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal" data-bs-target="#modalHuyDon">
                                <i class="bi bi-x-circle me-1"></i>Hủy Đơn Hàng
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Thông tin Khách hàng, Kênh bán, UserDatHang & Thanh toán -->
            <div class="col-lg-4">
                <!-- Box 1: Thông tin Kênh bán & Người phụ trách (DonHang) -->
                <div class="tz-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 15px;">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>Kênh Bán & Phụ Trách
                    </h5>
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kênh bán (kenhBan):</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2.5 py-1">
                            <i class="bi bi-globe me-1"></i>ONLINE (Trực tuyến)
                        </span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Người phụ trách (nguoiPhuTrach):</span>
                        <a href="../tai-khoan/tai-khoan.php?search=TK002" class="text-decoration-none fw-semibold font-monospace small">
                            <i class="bi bi-person-badge me-1"></i>TK002 (Trần Quản Trị)
                        </a>
                    </div>
                    <div class="mb-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Khách đặt hàng (maKH):</span>
                        <a href="../khach-hang/khach-hang.php?search=KH01" class="text-decoration-none fw-bold text-dark small">
                            Nguyễn Văn An (KH01)
                        </a>
                    </div>
                </div>

                <!-- Box 2: Thông tin mở rộng Đơn Online (UserDatHang - Quan hệ 1..0-1 với DonHang) -->
                <div class="tz-card p-4 mb-4" style="border-left: 4px solid var(--tz-primary);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 15px;">
                            <i class="bi bi-truck text-primary me-2"></i>Giao Nhận Online (UserDatHang)
                        </h5>
                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;">#UDH-01</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-muted small d-block">Người nhận hàng (tenNguoiNhan):</span>
                        <strong class="text-dark">Nguyễn Văn An</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">SĐT nhận hàng (sdtNhanHang):</span>
                        <a href="tel:0912345678" class="text-decoration-none fw-semibold">0912 345 678</a>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Địa chỉ giao hàng (diaChiGiaoHang):</span>
                        <span class="text-secondary small">123 Đường Láng, P. Láng Thượng, Q. Đống Đa, Hà Nội</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Ghi chú giao nhận (ghiChuKhachHang):</span>
                        <span class="text-muted small fst-italic">"Giao giờ hành chính, gọi trước khi đến 15 phút"</span>
                    </div>
                    <div class="mb-0 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Mã GD (maGiaoDichOnline):</span>
                            <span class="badge bg-light text-dark font-monospace border">VNPAY-99882210</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">TT Thanh toán:</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>DA_THANH_TOAN</span>
                        </div>
                    </div>
                </div>

                <!-- Box 3: Chi Tiết Thanh Toán (DonHang) -->
                <div class="tz-card p-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 15px;">
                        <i class="bi bi-wallet2 text-primary me-2"></i>Chi Tiết Thanh Toán (DonHang)
                    </h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Tổng tiền hàng (tongTienHang):</span>
                        <span class="fw-semibold">37.990.000 ₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Phí vận chuyển (phiVanChuyen):</span>
                        <span class="fw-semibold">+ 50.000 ₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Voucher KM (maKM: KM2026-T10):</span>
                        <span class="text-success fw-semibold">- 3.799.000 ₫</span>
                    </div>

                    <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">TỔNG THANH TOÁN:</span>
                        <span class="fw-bold text-danger fs-5 font-monospace">34.241.000 ₫</span>
                    </div>

                    <div class="mt-3 p-2 bg-light rounded" style="font-size: 12.5px;">
                        <div><i class="bi bi-credit-card me-1"></i>Phương thức TT: <strong>Chuyển khoản VNPAY</strong></div>
                        <div class="mt-1"><i class="bi bi-truck me-1"></i>Phương thức giao: <strong>Giao hàng hỏa tốc</strong></div>
                    </div>

                    <div class="mt-3 pt-3 border-top text-center">
                        <a href="../hoa-don/detail.php?maDH=<?= $maDH ?>" class="btn btn-outline-success btn-sm w-100 fw-semibold">
                            <i class="bi bi-receipt me-1"></i>Xem Hóa Đơn Xuất (HoaDon.maDH)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hủy Đơn Hàng -->
        <div class="modal fade" id="modalHuyDon" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hủy Đơn Hàng (<?= $maDH ?>)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="detail.php?id=<?= $maDH ?>" method="POST">
                        <input type="hidden" name="action" value="cancel_order">
                        <div class="modal-body p-4">
                            <p class="text-danger fw-semibold mb-2">Cảnh báo: Hành động này sẽ hủy đơn và thực hiện 2 thao tác tự động:</p>
                            <ol class="small text-muted mb-3">
                                <li>Hoàn trả lại số lượng sản phẩm vào kho hàng.</li>
                                <li>Đưa mã Serial của sản phẩm về lại trạng thái sẵn sàng xuất bán.</li>
                            </ol>
                            
                            <label class="form-label fw-semibold">Lý do hủy đơn <span class="text-danger">*</span></label>
                            <textarea name="lyDo" class="form-control" rows="3" placeholder="Nhập lý do khách hủy hoặc cửa hàng từ chối..." required></textarea>
                        </div>
                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Xác nhận Hủy Đơn Hàng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
