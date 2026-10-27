<?php
// app/admin/phieu-nhap/phieu-nhap.php
$pageTitle = "Quản lý nhập kho";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$filterNCC = htmlspecialchars($_GET['maNCC'] ?? '');

// Dữ liệu mẫu Phiếu Nhập
$sampleReceipts = [
    [
        'maPhieu' => 'PN2026102401',
        'ngayNhap' => '2026-10-24 09:30:00',
        'maNCC' => 'NCC_ASUS',
        'tenCongTy' => 'Công Ty TNHH ASUS Technology Việt Nam',
        'nguoiNhap' => 'TK001 (Nguyễn Đăng Tài)',
        'soHoaDonNCC' => 'VAT-ASUS-88912',
        'tongTienTraTruocNCC' => 50000000,
        'tongTienNhap' => 125000000,
        'trangThaiThanhToan' => 'thanhtoan', // thanhtoan | coc
        'trangThai' => 'Đã nhập kho', // Đã nhập kho (Badge xanh)
        'ghiChu' => 'Nhập lô laptop ROG Strix G16 chuẩn phân phối chính hãng'
    ],
    [
        'maPhieu' => 'PN2026102402',
        'ngayNhap' => '2026-10-24 14:00:00',
        'maNCC' => 'NCC_DELL',
        'tenCongTy' => 'Dell Global B.V Việt Nam',
        'nguoiNhap' => 'TK002 (Trần Quản Trị)',
        'soHoaDonNCC' => 'Chưa bổ sung',
        'tongTienTraTruocNCC' => 30000000,
        'tongTienNhap' => 95000000,
        'trangThaiThanhToan' => 'coc', // coc
        'trangThai' => 'Bản Nháp', // Bản Nháp (Badge vàng)
        'ghiChu' => 'Lô Dell XPS 13 Plus 9320 đang kiểm đếm Serial'
    ],
    [
        'maPhieu' => 'PN2026102001',
        'ngayNhap' => '2026-10-20 11:15:00',
        'maNCC' => 'NCC_APPLE',
        'tenCongTy' => 'Công Ty TNHH Apple Việt Nam',
        'nguoiNhap' => 'TK001 (Nguyễn Đăng Tài)',
        'soHoaDonNCC' => 'INV-APL-2026-90',
        'tongTienTraTruocNCC' => 150000000,
        'tongTienNhap' => 150000000,
        'trangThaiThanhToan' => 'thanhtoan',
        'trangThai' => 'Đã nhập kho',
        'ghiChu' => 'Nhập lô MacBook Pro 14 M3 Pro nguyên seal VAT'
    ]
];

if (!empty($filterNCC)) {
    $sampleReceipts = array_filter($sampleReceipts, function ($r) use ($filterNCC) {
        return $r['maNCC'] === $filterNCC;
    });
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Phiếu nhập kho</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản Lý Nhập Kho & Serial/IMEI</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Quản lý chứng từ nhập hàng, đối chiếu công nợ nhà cung cấp và kiểm đếm Serial vật lý.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Nút Mở Modal Báo cáo Lịch sử Nhập - Xuất Kho -->
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalBaoCaoNhapXuat">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i>Báo cáo Nhập - Xuất kho
                </button>
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tạo phiếu nhập mới
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG TIỀN NHẬP KHO</div>
                        <div class="tz-metric-value text-primary">370.000.000 ₫</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">PHIẾU ĐÃ NHẬP KHO</div>
                        <div class="tz-metric-value text-success">2 Phiếu</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">PHIẾU BẢN NHÁP (ĐANG ĐẾM)</div>
                        <div class="tz-metric-value text-warning">1 Phiếu</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TIỀN ĐÃ ĐẶT CỌC NCC</div>
                        <div class="tz-metric-value text-danger">30.000.000 ₫</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh lọc theo Nhà Cung Cấp -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="phieu-nhap.php" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-building"></i></span>
                            <select name="maNCC" class="form-select border-start-0 ps-0">
                                <option value="">-- Tất cả nhà cung cấp --</option>
                                <option value="NCC_ASUS" <?= $filterNCC === 'NCC_ASUS' ? 'selected' : '' ?>>Công Ty TNHH ASUS Technology Việt Nam</option>
                                <option value="NCC_DELL" <?= $filterNCC === 'NCC_DELL' ? 'selected' : '' ?>>Dell Global B.V Việt Nam</option>
                                <option value="NCC_APPLE" <?= $filterNCC === 'NCC_APPLE' ? 'selected' : '' ?>>Công Ty TNHH Apple Việt Nam</option>
                                <option value="NCC_LENOVO" <?= $filterNCC === 'NCC_LENOVO' ? 'selected' : '' ?>>Lenovo Pte Ltd Việt Nam</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Mã phiếu nhập, số hóa đơn NCC...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-funnel me-1"></i>Lọc dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách Phiếu Nhập -->
        <div class="tz-card">
            <div class="tz-card-header d-flex justify-content-between align-items-center">
                <h3 class="tz-card-title"><i class="bi bi-box-arrow-in-down text-primary"></i>Danh Sách Phiếu Nhập Kho</h3>
                <span class="badge bg-light text-secondary border">Hiển thị <?= count($sampleReceipts) ?> phiếu</span>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">MÃ PHIẾU</th>
                            <th>NGÀY NHẬP</th>
                            <th>NHÀ CUNG CẤP</th>
                            <th>NGƯỜI NHẬP</th>
                            <th>SỐ HÓA ĐƠN NCC</th>
                            <th>ĐÃ TRẢ TRƯỚC</th>
                            <th>TỔNG TIỀN NHẬP</th>
                            <th>THANH TOÁN</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end" style="width: 100px;">CHI TIẾT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sampleReceipts as $pn): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark border font-monospace fw-bold"><?= htmlspecialchars($pn['maPhieu']) ?></span></td>
                                <td><small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($pn['ngayNhap'])) ?></small></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($pn['tenCongTy']) ?></div>
                                    <small class="text-primary font-monospace"><?= htmlspecialchars($pn['maNCC']) ?></small>
                                </td>
                                <td><span class="text-secondary"><i class="bi bi-person me-1"></i><?= htmlspecialchars($pn['nguoiNhap']) ?></span></td>
                                <td>
                                    <?php if ($pn['soHoaDonNCC'] !== 'Chưa bổ sung'): ?>
                                        <span class="font-monospace text-dark fw-semibold"><?= htmlspecialchars($pn['soHoaDonNCC']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Chưa bổ sung</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-secondary"><?= number_format($pn['tongTienTraTruocNCC'], 0, ',', '.') ?> ₫</span></td>
                                <td><span class="fw-bold text-danger"><?= number_format($pn['tongTienNhap'], 0, ',', '.') ?> ₫</span></td>
                                <td>
                                    <?php if ($pn['trangThaiThanhToan'] === 'thanhtoan'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Đặt cọc</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pn['trangThai'] === 'Đã nhập kho'): ?>
                                        <span class="tz-badge tz-badge-success"><i class="bi bi-check2-circle"></i>Đã nhập kho</span>
                                    <?php else: ?>
                                        <span class="tz-badge tz-badge-warning"><i class="bi bi-pencil-fill"></i>Bản Nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="detail.php?id=<?= $pn['maPhieu'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết & Serial">
                                        <i class="bi bi-eye me-1"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Báo Cáo Lịch Sử Nhập - Xuất Kho -->
        <div class="modal fade" id="modalBaoCaoNhapXuat" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Báo Cáo Đối Chiếu Lịch Sử Nhập - Xuất Kho
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">Trích xuất biến động nhập kho, xuất bán và kiểm đếm tồn kho Serial theo thời gian thực:</p>

                        <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Từ ngày</label>
                                <input type="date" class="form-control" value="2026-10-01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Đến ngày</label>
                                <input type="date" class="form-control" value="2026-10-24">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-tz-primary w-100" onclick="TechZoneNotify.success('Đã trích xuất dữ liệu đối chiếu từ ngày 01/10/2026 đến 24/10/2026 thành công!', 'Báo cáo kho');">
                                    <i class="bi bi-filter me-1"></i>Trích xuất số liệu
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>MÃ PHIẾU / HÓA ĐƠN</th>
                                        <th>LOẠI GIAO DỊCH</th>
                                        <th>THỜI GIAN</th>
                                        <th>ĐỐI TÁC / KHÁCH HÀNG</th>
                                        <th>SỐ LƯỢNG MÁY</th>
                                        <th>TỔNG GIÁ TRỊ</th>
                                        <th>TRẠNG THÁI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-primary-subtle text-primary border font-monospace">PN2026102401</span></td>
                                        <td><span class="badge bg-success text-white"><i class="bi bi-arrow-down-circle me-1"></i>Nhập Kho</span></td>
                                        <td>24/10/2026 09:30</td>
                                        <td>Công Ty TNHH ASUS Technology Việt Nam</td>
                                        <td><strong>3 máy</strong> (Serial đã kiểm)</td>
                                        <td class="fw-bold text-danger">125.000.000 ₫</td>
                                        <td><span class="text-success fw-semibold"><i class="bi bi-check2"></i>Đã vào kho</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border font-monospace">HD2026102301</span></td>
                                        <td><span class="badge bg-danger text-white"><i class="bi bi-arrow-up-circle me-1"></i>Xuất Bán (Đơn hàng)</span></td>
                                        <td>23/10/2026 14:15</td>
                                        <td>Nguyễn Văn An (0912 345 678)</td>
                                        <td><strong>1 máy</strong> (Dell XPS 13)</td>
                                        <td class="fw-bold text-primary">36.140.500 ₫</td>
                                        <td><span class="text-success fw-semibold"><i class="bi bi-check2"></i>Đã giao khách</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>