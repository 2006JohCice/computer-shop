<?php
// app/views/admin/dashboard/index.php
$pageTitle = "Tổng Quan Hệ Thống";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Tiêu đề trang & Bộ lọc thời gian -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tổng Quan Hệ Thống</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Báo cáo số liệu kinh doanh và hoạt động kho hàng TechZone theo thời gian thực.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small fw-medium"><i class="bi bi-calendar3 me-1"></i>Thời gian:</span>
                <select class="form-select form-select-sm" style="width: 150px;">
                    <option selected>Hôm nay</option>
                    <option>Tuần này</option>
                    <option>Tháng này</option>
                    <option>Toàn thời gian</option>
                </select>
                <button class="btn btn-sm btn-tz-outline"><i class="bi bi-download"></i>Xuất Excel</button>
            </div>
        </div>

        <!-- 6 Thẻ Metric Thống kê (Dựa trên thiết kế mẫu Order Admin) -->
        <div class="row g-3 mb-4">
            <!-- Doanh thu -->
            <div class="col-xl-4 col-md-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG DOANH THU</div>
                        <div class="tz-metric-value text-primary">2.132.170.000 ₫</div>
                        <small class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>+12.5% so với tháng trước</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>

            <!-- Đơn bán hàng -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">TỔNG ĐƠN HÀNG</div>
                        <div class="tz-metric-value">115</div>
                        <small class="text-muted"><i class="bi bi-bag-check me-1"></i>Đã xử lý</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                        <i class="bi bi-bag"></i>
                    </div>
                </div>
            </div>

            <!-- Đơn chờ xử lý -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐƠN CHỜ DUYỆT</div>
                        <div class="tz-metric-value text-warning">8</div>
                        <small class="text-danger fw-semibold"><i class="bi bi-clock me-1"></i>Cần xử lý ngay</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <!-- Laptop trong kho -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">MÁY TỒN KHO</div>
                        <div class="tz-metric-value">142</div>
                        <small class="text-primary"><i class="bi bi-upc-scan me-1"></i>Có Serial/IMEI</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-info-light); color: var(--tz-info);">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div>

            <!-- Phiếu bảo hành -->
            <div class="col-xl-2 col-md-6 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG BẢO HÀNH</div>
                        <div class="tz-metric-value text-danger">4</div>
                        <small class="text-muted"><i class="bi bi-tools me-1"></i>Đang kiểm tra</small>
                    </div>
                    <div class="tz-metric-icon" style="background-color: #f3e8ff; color: #9333ea;">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khu vực Biểu đồ & Top bán chạy -->
        <div class="row g-4 mb-4">
            <!-- Biểu đồ Hoạt động Đơn hàng & Doanh thu -->
            <div class="col-xl-8">
                <div class="tz-card h-100">
                    <div class="tz-card-header">
                        <h3 class="tz-card-title"><i class="bi bi-graph-up text-primary"></i>Hoạt động Bán hàng Theo Tuần</h3>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Giao hàng</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Tại quầy</span>
                        </div>
                    </div>
                    <div class="tz-card-body">
                        <!-- Canvas Biểu đồ (Hỗ trợ Chart.js) -->
                        <canvas id="salesChart" style="max-height: 280px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Thương Hiệu & Laptop Bán Chạy -->
            <div class="col-xl-4">
                <div class="tz-card h-100">
                    <div class="tz-card-header">
                        <h3 class="tz-card-title"><i class="bi bi-trophy text-warning"></i>Top Hãng Laptop Bán Chạy</h3>
                    </div>
                    <div class="tz-card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 38px; height: 38px;">DELL</div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 13.5px;">Dell Technologies</div>
                                        <small class="text-muted">38 máy bán ra</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark">840.500.000 ₫</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-danger fw-bold" style="width: 38px; height: 38px;">ASUS</div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 13.5px;">ASUS ROG & TUF</div>
                                        <small class="text-muted">29 máy bán ra</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark">650.200.000 ₫</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-dark fw-bold" style="width: 38px; height: 38px;">APPLE</div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 13.5px;">MacBook M-Series</div>
                                        <small class="text-muted">22 máy bán ra</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark">541.470.000 ₫</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Đơn Hàng Mới Nhất Cần Xử Lý -->
        <div class="tz-card">
            <div class="tz-card-header">
                <h3 class="tz-card-title"><i class="bi bi-clock-history text-primary"></i>Đơn Hàng Gần Đây Cần Xử Lý</h3>
                <a href="../don-hang/don-hang.php" class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold">Xem tất cả đơn hàng <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ ĐƠN</th>
                            <th>KHÁCH HÀNG</th>
                            <th>NGÀY TẠO</th>
                            <th>PHƯƠNG THỨC</th>
                            <th>TỔNG TIỀN</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold text-primary">#DH-20261023-01</span></td>
                            <td>
                                <div class="fw-semibold">Nguyễn Văn An</div>
                                <small class="text-muted">0912 345 678</small>
                            </td>
                            <td>23/10/2026 14:15</td>
                            <td>Chuyển khoản (VNPay)</td>
                            <td class="fw-bold">35.990.000 ₫</td>
                            <td><span class="tz-badge tz-badge-warning"><i class="bi bi-clock"></i>Chờ duyệt</span></td>
                            <td class="text-end">
                                <a href="../don-hang/detail.php?id=DH01" class="btn btn-sm btn-tz-outline"><i class="bi bi-eye"></i> Chi tiết</a>
                                <a href="../don-hang/edit.php?id=DH01" class="btn btn-sm btn-tz-primary"><i class="bi bi-pencil-square"></i> Xử lý</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-bold text-primary">#DH-20261023-02</span></td>
                            <td>
                                <div class="fw-semibold">Trần Thị Mai (VIP)</div>
                                <small class="text-muted">0987 654 321</small>
                            </td>
                            <td>23/10/2026 13:40</td>
                            <td>Tiền mặt tại quầy</td>
                            <td class="fw-bold">42.990.000 ₫</td>
                            <td><span class="tz-badge tz-badge-success"><i class="bi bi-check2-circle"></i>Đã duyệt</span></td>
                            <td class="text-end">
                                <a href="../don-hang/detail.php?id=DH02" class="btn btn-sm btn-tz-outline"><i class="bi bi-eye"></i> Chi tiết</a>
                                <a href="../serial/serial.php" class="btn btn-sm btn-tz-outline"><i class="bi bi-upc-scan"></i> Serial</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Chart.js CDN để vẽ biểu đồ doanh thu -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'],
            datasets: [
                {
                    label: 'Giao hàng',
                    data: [18, 19, 12, 5, 8, 11, 17],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Tại quầy',
                    data: [13, 9, 3, 3, 7, 13, 14],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
