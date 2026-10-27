<?php
/**
 * ==============================================================================
 * HỆ THỐNG: QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: app/views/admin/layouts/app.php
 * MÔ TẢ: Master Layout kế thừa duy nhất cho toàn bộ giao diện Admin
 * GỒM: header (HTML, CSS), sidebar (Menu), topbar (Header Admin), footer (JS)
 * ==============================================================================
 */

$pageTitle = $pageTitle ?? 'Tổng Quan Quản Trị';

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';
?>

<div class="tz-main-wrapper">
    <?php require_once __DIR__ . '/topbar.php'; ?>

    <main class="tz-content-area">
        <?php if (!empty($content)): ?>
            <?= $content ?>
        <?php else: ?>
            <!-- GIAO DIỆN XEM TRƯỚC (PREVIEW) KHI MỞ TRỰC TIẾP APP.PHP -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Tổng Quan Hệ Thống Quản Trị</h2>
                    <p class="text-secondary mb-0" style="font-size: 13.5px;">
                        Giao diện Master Layout kế thừa toàn bộ: <strong>Header, Sidebar, Topbar và Footer</strong>.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 px-3 rounded-pill fw-semibold">
                        <i class="bi bi-layers-fill me-1"></i> Master Layout Admin
                    </span>
                </div>
            </div>

            <!-- 4 Thẻ Thống Kê (Metrics) -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="tz-metric-card">
                        <div>
                            <div class="tz-metric-title">TỔNG SẢN PHẨM</div>
                            <div class="tz-metric-value text-primary">128</div>
                            <small class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>12 sản phẩm mới</small>
                        </div>
                        <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                            <i class="bi bi-laptop"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="tz-metric-card">
                        <div>
                            <div class="tz-metric-title">ĐƠN BÁN HÀNG</div>
                            <div class="tz-metric-value text-success">48</div>
                            <small class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Hoàn thành 95%</small>
                        </div>
                        <div class="tz-metric-icon" style="background-color: #dcfce7; color: #16a34a;">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="tz-metric-card">
                        <div>
                            <div class="tz-metric-title">KHÁCH HÀNG VIP</div>
                            <div class="tz-metric-value text-warning">512</div>
                            <small class="text-secondary fw-semibold"><i class="bi bi-people me-1"></i>Đang hoạt động</small>
                        </div>
                        <div class="tz-metric-icon" style="background-color: #fef3c7; color: #d97706;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="tz-metric-card">
                        <div>
                            <div class="tz-metric-title">DOANH THU THÁNG</div>
                            <div class="tz-metric-value text-danger">2.132.170.000 ₫</div>
                            <small class="text-success fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i>+15.8% so với tháng trước</small>
                        </div>
                        <div class="tz-metric-icon" style="background-color: #fee2e2; color: #dc2626;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng Dữ Liệu Laptop Mẫu -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Sản phẩm Laptop Mới Nhập</h5>
                    <a href="/computer-shop/app/admin/san-pham/san-pham.php" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 tz-table">
                        <thead class="table-light">
                            <tr>
                                <th>MÃ LAPTOP</th>
                                <th>TÊN SẢN PHẨM</th>
                                <th>THƯƠNG HIỆU</th>
                                <th>GIÁ BÁN</th>
                                <th>TỒN KHO</th>
                                <th>TRẠNG THÁI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary">SP-DELL-XPS13</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-laptop fs-4 text-secondary"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Dell XPS 13 Plus 9320</div>
                                            <small class="text-muted">Core i7-1360P | 16GB | 512GB SSD | 3.5K OLED</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Dell</td>
                                <td class="fw-bold text-dark">38.990.000 ₫</td>
                                <td><span class="badge bg-success-subtle text-success">Còn 15 máy</span></td>
                                <td><span class="badge bg-success">Đang kinh doanh</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary">SP-MAC-M3PRO</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-laptop fs-4 text-secondary"></i>
                                        <div>
                                            <div class="fw-bold text-dark">MacBook Pro 14 inch M3 Pro</div>
                                            <small class="text-muted">Apple M3 Pro | 18GB RAM | 512GB SSD | Space Black</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Apple</td>
                                <td class="fw-bold text-dark">49.990.000 ₫</td>
                                <td><span class="badge bg-success-subtle text-success">Còn 8 máy</span></td>
                                <td><span class="badge bg-success">Đang kinh doanh</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary">SP-ASUS-G16</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-laptop fs-4 text-secondary"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Asus ROG Zephyrus G16 (2026)</div>
                                            <small class="text-muted">Intel Core Ultra 9 | RTX 4070 | 32GB RAM | 240Hz OLED</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Asus</td>
                                <td class="fw-bold text-dark">54.490.000 ₫</td>
                                <td><span class="badge bg-warning-subtle text-warning">Còn 3 máy</span></td>
                                <td><span class="badge bg-success">Đang kinh doanh</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
