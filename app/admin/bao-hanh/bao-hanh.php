<?php
// app/admin/bao-hanh/bao-hanh.php
$pageTitle = "Quản lý bảo hành & sửa chữa";

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $maPBH = htmlspecialchars($_POST['maPhieuBH'] ?? '');
        $successMsg = "Đã tiếp nhận thành công phiếu bảo hành: <strong>$maPBH</strong>";
    } elseif ($action === 'update') {
        $maPBH = htmlspecialchars($_POST['maPhieuBH'] ?? '');
        $trangThai = htmlspecialchars($_POST['trangThai'] ?? '');
        $successMsg = "Đã cập nhật trạng thái phiếu BH <strong>$maPBH</strong> sang: <strong>$trangThai</strong>";
    } elseif ($action === 'delete') {
        $maPBH = htmlspecialchars($_POST['maPhieuBH'] ?? '');
        $successMsg = "Đã xóa phiếu bảo hành: <strong>$maPBH</strong>";
    } elseif ($action === 'traKhach') {
        $maPBH = htmlspecialchars($_POST['maPhieuBH'] ?? '');
        $successMsg = "Đã bàn giao máy phiếu: <strong>$maPBH</strong> lại cho khách hàng thành công!";
    }
}

require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div><?= $successMsg ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Header trang -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Dịch Vụ Bảo Hành & Sửa Chữa</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Tiếp nhận máy bảo hành, tra cứu thời hạn bảo hành theo Serial/IMEI và theo dõi tiến độ kỹ thuật.</p>
            </div>
            <a href="create.php" class="btn btn-tz-primary">
                <i class="bi bi-shield-plus me-1"></i> Tiếp nhận bảo hành mới
            </a>
        </div>

        <!-- 4 Thẻ Metric Bảo Hành -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG PHIẾU TIẾP NHẬN</div>
                        <div class="tz-metric-value">14</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG KIỂM TRA LỖI</div>
                        <div class="tz-metric-value text-warning">4</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">CHỜ LINH KIỆN HÃNG</div>
                        <div class="tz-metric-value text-danger">2</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-cpu"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ XỬ LÝ XONG</div>
                        <div class="tz-metric-value text-success">8</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung Tra Cứu Nhanh Serial Hạn Bảo Hành -->
        <div class="tz-card mb-4 border-primary-subtle" style="background-color: #f8fbff;">
            <div class="tz-card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Quét hoặc nhập số Serial / IMEI máy để kiểm tra ngay hạn bảo hành...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-search me-1"></i> Kiểm Tra Thời Hạn Máy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Phiếu Bảo Hành -->
        <div class="tz-card">
            <div class="tz-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h3 class="tz-card-title mb-0"><i class="bi bi-tools text-primary me-2"></i>Danh Sách Máy Đang Tiếp Nhận Xử Lý</h3>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 form-control-sm" placeholder="Tìm theo mã phiếu, SĐT...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ PHIẾU (maPhieuBH)</th>
                            <th>MÃ SERIAL / IMEI (FK)</th>
                            <th>KHÁCH HÀNG (maKH)</th>
                            <th>THỜI HẠN BẢO HÀNH (ngayBatDau - ngayKetThuc)</th>
                            <th>CHÍNH SÁCH BẢO HÀNH (moTaChinhSach)</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">PBH202610-001</span></td>
                            <td>
                                <a href="../serial/serial.php?serial=DELL-XPS-9320-001" class="badge bg-light text-primary border font-monospace text-decoration-none fw-bold" title="Xem thông tin Serial">
                                    <i class="bi bi-upc-scan me-1"></i>DELL-XPS-9320-001
                                </a>
                                <small class="text-muted d-block">Dell XPS 13 Plus (SP01)</small>
                            </td>
                            <td>
                                <a href="../khach-hang/khach-hang.php?search=KH01" class="fw-bold text-dark text-decoration-none">Nguyễn Văn An</a>
                                <small class="text-muted d-block">0912 345 678 • KH01</small>
                            </td>
                            <td>
                                <small class="text-muted d-block">Bắt đầu: 20/10/2026</small>
                                <span class="badge bg-danger-subtle text-danger font-monospace">Hết hạn: 20/10/2028</span>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 220px;" title="Bảo hành phần cứng 24 tháng chính hãng Dell, 1 đổi 1 trong 30 ngày">
                                    Bảo hành phần cứng 24 tháng chính hãng Dell, 1 đổi 1 trong 30 ngày
                                </div>
                            </td>
                            <td><span class="tz-badge tz-badge-warning"><i class="bi bi-tools"></i>Đang xử lý</span></td>
                            <td class="text-end">
                                <a href="edit.php?maPhieuBH=PBH202610-001" class="btn btn-sm btn-outline-primary" title="Cập nhật tiến độ"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="bao-hanh.php" style="display:inline-block;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="maPhieuBH" value="PBH202610-001">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" data-tz-confirm="Bạn có chắc muốn xóa phiếu bảo hành PBH202610-001 không?" data-tz-title="Xác nhận xóa phiếu bảo hành"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">PBH202610-002</span></td>
                            <td>
                                <a href="../serial/serial.php?serial=ASUS-G14-2024-001" class="badge bg-light text-primary border font-monospace text-decoration-none fw-bold" title="Xem thông tin Serial">
                                    <i class="bi bi-upc-scan me-1"></i>ASUS-G14-2024-001
                                </a>
                                <small class="text-muted d-block">Asus ROG Zephyrus G14 (SP02)</small>
                            </td>
                            <td>
                                <a href="../khach-hang/khach-hang.php?search=KH02" class="fw-bold text-dark text-decoration-none">Trần Thị Mai <span class="badge bg-warning text-dark ms-1">VIP</span></a>
                                <small class="text-muted d-block">0987 654 321 • KH02</small>
                            </td>
                            <td>
                                <small class="text-muted d-block">Bắt đầu: 18/10/2026</small>
                                <span class="badge bg-danger-subtle text-danger font-monospace">Hết hạn: 18/10/2028</span>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 220px;" title="Bảo hành toàn cầu ASUS VIP 24 tháng, hỗ trợ vệ sinh tra keo tản nhiệt miễn phí">
                                    Bảo hành toàn cầu ASUS VIP 24 tháng, vệ sinh miễn phí
                                </div>
                            </td>
                            <td><span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đã xử lý xong</span></td>
                            <td class="text-end">
                                <a href="edit.php?maPhieuBH=PBH202610-002" class="btn btn-sm btn-outline-primary" title="Cập nhật tiến độ"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="bao-hanh.php" style="display:inline-block;">
                                    <input type="hidden" name="action" value="traKhach">
                                    <input type="hidden" name="maPhieuBH" value="PBH202610-002">
                                    <button type="submit" class="btn btn-sm btn-tz-primary" title="Trả máy cho khách"><i class="bi bi-box-arrow-right"></i> Trả khách</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
