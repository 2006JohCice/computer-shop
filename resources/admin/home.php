<?php 
// Nhúng giao diện Header & Sidebar chung
include __DIR__ . '/layouts/header.php';
?>

<!-- Link CSS riêng cho trang Home -->
<link rel="stylesheet" href="/computer-shop/resources/css/admin/home.css">

<div class="dashboard-container">
    
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Tổng Quan Hệ Thống</h1>
        <div class="page-filter">
            <i class="bi bi-calendar3"></i> Thời gian:
            <select>
                <option>Toàn thời gian</option>
                <option>Tháng này</option>
                <option>Tuần này</option>
            </select>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <!-- KPI 1 -->
        <div class="kpi-item">
            <div class="kpi-icon icon-users"><i class="bi bi-people-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Tổng Users</div>
                <div class="kpi-value">7</div>
                <div class="kpi-desc">+4% tuần trước</div>
            </div>
        </div>
        <!-- KPI 2 -->
        <div class="kpi-item">
            <div class="kpi-icon icon-orders"><i class="bi bi-bag-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Tổng Đơn Hàng</div>
                <div class="kpi-value">113</div>
                <div class="kpi-desc">+2% tuần trước</div>
            </div>
        </div>
        <!-- KPI 3 -->
        <div class="kpi-item">
            <div class="kpi-icon icon-pending"><i class="bi bi-clock-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Đơn Đang Chờ</div>
                <div class="kpi-value">2</div>
                <div class="kpi-desc neutral">Cần xử lý</div>
            </div>
        </div>
        <!-- KPI 4 -->
        <div class="kpi-item">
            <div class="kpi-icon icon-success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Đơn Hoàn Thành</div>
                <div class="kpi-value">78</div>
                <div class="kpi-desc">+12% tuần trước</div>
            </div>
        </div>
        <!-- KPI 5 -->
        <div class="kpi-item">
            <div class="kpi-icon icon-store"><i class="bi bi-shop"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Nhà Hàng</div>
                <div class="kpi-value">5</div>
                <div class="kpi-desc neutral text-green">3 active</div>
            </div>
        </div>
        <!-- KPI 6 -->
        <div class="kpi-item" style="border-right: none;">
            <div class="kpi-icon icon-revenue"><i class="bi bi-cash-stack"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">Doanh Thu</div>
                <div class="kpi-value">2.111.931.558 đ</div>
                <div class="kpi-desc neutral">+0 chờ duyệt</div>
            </div>
        </div>
    </div>

    <!-- Charts and Top List -->
    <div class="grid-2">
        
        <!-- Hoạt động đơn hàng -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Hoạt động Đơn Hàng <span style="font-weight:400; color:#94a3b8; font-size:0.8rem;">Theo tuần</span></h3>
                <i class="bi bi-chevron-up text-muted"></i>
            </div>
            <div class="panel-body">
                <div class="chart-legend">
                    <div class="legend-item"><div class="dot blue"></div> Giao hàng</div>
                    <div class="legend-item"><div class="dot green"></div> Đặt bàn</div>
                </div>
                <!-- SVG fake chart for mockup perfection -->
                <div class="fake-chart"></div>
                <div style="display:flex; justify-content:space-between; margin-top:10px; color:#94a3b8; font-size:0.75rem;">
                    <span>Thứ 2</span><span>Thứ 3</span><span>Thứ 4</span><span>Thứ 5</span><span>Thứ 6</span><span>Thứ 7</span><span>CN</span>
                </div>
            </div>
        </div>

        <!-- Top Nhà Hàng -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Top Nhà Hàng</h3>
                <i class="bi bi-chevron-up text-muted"></i>
            </div>
            <div class="panel-body">
                <div class="top-list">
                    <div class="top-item">
                        <div class="top-item-name">SHOP ORDER</div>
                        <div class="progress-track"><div class="progress-fill green" style="width: 80%;"></div></div>
                        <div class="top-item-value">3.3<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.6rem;"></i></div>
                    </div>
                    <div class="top-item">
                        <div class="top-item-name">Order Shop 2</div>
                        <div class="progress-track"><div class="progress-fill blue" style="width: 40%;"></div></div>
                        <div class="top-item-value">0.0<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.6rem;"></i></div>
                    </div>
                    <div class="top-item">
                        <div class="top-item-name">Quán Ăn Vặt</div>
                        <div class="progress-track"><div class="progress-fill" style="width: 0%;"></div></div>
                        <div class="top-item-value">0.0<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.6rem;"></i></div>
                    </div>
                    <div class="top-item">
                        <div class="top-item-name">Nhật Việt</div>
                        <div class="progress-track"><div class="progress-fill" style="width: 0%;"></div></div>
                        <div class="top-item-value">0.0<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.6rem;"></i></div>
                    </div>
                    <div class="top-item">
                        <div class="top-item-name">Bánh Quy</div>
                        <div class="progress-track"><div class="progress-fill" style="width: 0%;"></div></div>
                        <div class="top-item-value">0.0<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.6rem;"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Thống Kê Truy Cập -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Thống Kê Truy Cập Web Hàng Tháng</h3>
            <i class="bi bi-chevron-up text-muted"></i>
        </div>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Thời Gian</th>
                        <th>Tổng Lượt Truy Cập</th>
                        <th>Đã Đăng Ký (Users)</th>
                        <th>Chưa Đăng Ký (Guests)</th>
                        <th>Tỷ Lệ Đăng Ký</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Tháng 1</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr><td>Tháng 2</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr><td>Tháng 3</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr><td>Tháng 4</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr><td>Tháng 5</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr><td>Tháng 6</td><td>0</td><td class="text-green">0</td><td>0</td><td>0%</td></tr>
                    <tr>
                        <td>Tháng 7</td><td>1</td><td class="text-green">0</td><td>1</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-track flex-grow-1" style="width:100px;"><div class="progress-fill" style="width:0%"></div></div>
                                <span>0%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Tháng 8</td><td>17</td><td class="text-green">0</td><td>17</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-track flex-grow-1" style="width:100px;"><div class="progress-fill" style="width:0%"></div></div>
                                <span>0%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Tháng 9</td><td>19</td><td class="text-green">0</td><td>19</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-track flex-grow-1" style="width:100px;"><div class="progress-fill" style="width:0%"></div></div>
                                <span>0%</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="height: 3rem;"></div>
</div>

<?php 
// Nhúng Footer chung
include __DIR__ . '/layouts/footer.php';
?>
