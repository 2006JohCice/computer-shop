<?php
// app/admin/serial/serial.php
$pageTitle = "Quản lý Serial / IMEI Laptop";

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

        <!-- Header & Action -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Quản lý Serial / IMEI (SerialSanPham)</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Theo dõi chính xác từng chiếc máy tính theo số Serial/IMEI duy nhất từ khi nhập kho đến bảo hành.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-tz-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Serial máy mới
                </a>
            </div>
        </div>

        <!-- Metric tóm tắt Serial -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">MÁY TRONG KHO</div>
                        <div class="tz-metric-value text-primary">142</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-primary-light); color: var(--tz-primary);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐÃ BÁN CHO KHÁCH</div>
                        <div class="tz-metric-value text-success">389</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-success-light); color: var(--tz-success);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">ĐANG BẢO HÀNH</div>
                        <div class="tz-metric-value text-warning">4</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-warning-light); color: var(--tz-warning);">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="tz-metric-card">
                    <div>
                        <div class="tz-metric-title">LỖI ĐỔI TRẢ / HỎNG</div>
                        <div class="tz-metric-value text-danger">2</div>
                    </div>
                    <div class="tz-metric-icon" style="background-color: var(--tz-danger-light); color: var(--tz-danger);">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Tìm Kiếm Serial -->
        <div class="tz-card mb-4">
            <div class="tz-card-body p-3">
                <form action="" method="GET" class="row g-2 align-items-center">
                    <div class="col-xl-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="serial" class="form-control border-start-0 ps-0" placeholder="Nhập số Serial / IMEI cần tra cứu...">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <select name="san_pham" class="form-select">
                            <option value="">-- Tất cả laptop --</option>
                            <option value="SP01">Dell XPS 13 9320</option>
                            <option value="SP02">Asus ROG G14</option>
                            <option value="SP03">MacBook Air M3</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <select name="trang_thai" class="form-select">
                            <option value="">-- Trạng thái máy --</option>
                            <option value="TrongKho">Trong kho (Chưa bán)</option>
                            <option value="DaBan">Đã bán</option>
                            <option value="DangBaoHanh">Đang bảo hành</option>
                            <option value="LoiDoiTra">Lỗi đổi trả</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-12">
                        <button type="submit" class="btn btn-tz-primary w-100 justify-content-center">
                            <i class="bi bi-funnel"></i> Tra cứu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách Serial / IMEI -->
        <div class="tz-card">
            <div class="table-responsive">
                <table class="tz-table">
                    <thead>
                        <tr>
                            <th>MÃ SERIAL / IMEI</th>
                            <th>MÃ Sản Phẩm</th>
                            <th>MODEL SẢN PHẨM</th>
                            <th>PHIẾU NHẬP</th>
                            <th>ĐƠN HÀNG XUẤT</th>
                            <th>NGÀY XUẤT KHO</th>
                            <th>HẠN BẢO HÀNH</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">DELL-XPS-9320-001</span></td>
                            <td><span class="fw-bold font-monospace text-primary">SP01</span></td>
                            <td>
                                <div class="fw-semibold">Dell XPS 13 Plus 9320</div>
                                <small class="text-muted">SKU: SP01</small>
                            </td>
                            <td><a href="../phieu-nhap/detail.php?id=PN20261001" class="text-decoration-none font-monospace">PN20261001</a></td>
                            <td><span class="text-muted">— (Chưa xuất)</span></td>
                            <td><span class="text-muted">—</span></td>
                            <td><span class="text-muted">— (24 tháng khi bán)</span></td>
                            <td><span class="tz-badge tz-badge-primary"><i class="bi bi-box"></i>Trong kho</span></td>
                            <td class="text-end">
                                <a href="edit.php?serial=DELL-XPS-9320-001" class="btn btn-sm btn-outline-primary" title="Đổi trạng thái"><i class="bi bi-pencil-square"></i> Sửa</a>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="fw-bold font-monospace text-primary">ASUS-G14-2024-001</span></td>
                            <td>
                                <div class="fw-semibold">Asus ROG Zephyrus G14</div>
                                <small class="text-muted">SKU: SP02</small>
                            </td>
                            <td><a href="../phieu-nhap/detail.php?id=PN20261001" class="text-decoration-none font-monospace">PN20261001</a></td>
                            <td><a href="../don-hang/detail.php?id=DH02" class="text-decoration-none text-success fw-semibold font-monospace">#DH02</a></td>
                            <td>23/10/2026</td>
                            <td><span class="text-danger fw-semibold">23/10/2028</span></td>
                            <td><span class="tz-badge tz-badge-success"><i class="bi bi-check2"></i>Đã bán</span></td>
                            <td class="text-end">
                                <a href="edit.php?serial=ASUS-G14-2024-001" class="btn btn-sm btn-outline-primary" title="Đổi trạng thái"><i class="bi bi-pencil-square"></i> Sửa</a>
                                <a href="../bao-hanh/create.php?serial=ASUS-G14-2024-001" class="btn btn-sm btn-outline-warning" title="Tạo phiếu bảo hành"><i class="bi bi-shield-plus"></i> BH</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>