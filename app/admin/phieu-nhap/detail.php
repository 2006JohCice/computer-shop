<?php
// app/admin/phieu-nhap/detail.php
$pageTitle = "Chi tiết phiếu nhập kho";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maPhieu = htmlspecialchars($_GET['id'] ?? 'PN2026102402');

// Cho phép test chuyển trạng thái giữa Bản Nháp và Đã nhập kho
$simulatedStatus = $_GET['status'] ?? ($maPhieu === 'PN2026102401' ? 'Đã nhập kho' : 'Bản Nháp');

// Xử lý các Form Actions
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_invoice_ncc') {
        $soHD = htmlspecialchars($_POST['soHoaDonNCC'] ?? '');
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Đã cập nhật số hóa đơn <strong>$soHD</strong> thành công cho phiếu <strong>$maPhieu</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'confirm_import') {
        $simulatedStatus = 'Đã nhập kho';
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check-all me-2'></i>Kiểm đếm đầy đủ Serial/IMEI hoàn tất! Đã cộng dồn tồn kho và chuyển trạng thái phiếu sang <strong>Đã nhập kho</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'edit_draft') {
        $lyDo = htmlspecialchars($_POST['lyDo'] ?? '');
        $alertMsg = "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                        <i class='bi bi-pencil-square me-2'></i>Cập nhật thành công thông tin phiếu nháp <strong>$maPhieu</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'add_item') {
        $maSP = htmlspecialchars($_POST['maSP'] ?? '');
        $sl = htmlspecialchars($_POST['soLuongNhap'] ?? 1);
        $alertMsg = "<div class='alert alert-primary alert-dismissible fade show' role='alert'>
                        <i class='bi bi-plus-circle me-2'></i>Đã thêm $sl sản phẩm <strong>$maSP</strong> vào phiếu và cập nhật tổng giá trị!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

$isDraft = ($simulatedStatus === 'Bản Nháp');
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="phieu-nhap.php" class="text-decoration-none text-muted">Phiếu nhập</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= $maPhieu ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Phiếu Nhập Kho: <?= $maPhieu ?></h2>
                    <?php if ($isDraft): ?>
                        <span class="tz-badge tz-badge-warning"><i class="bi bi-pencil-fill"></i>BẢN NHÁP (ĐANG MỞ CHỈNH SỬA)</span>
                    <?php else: ?>
                        <span class="tz-badge tz-badge-success"><i class="bi bi-lock-fill"></i>ĐÃ NHẬP KHO (KHÓA CHI TIẾT)</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <!-- Chuyển đổi trạng thái demo -->
                <div class="btn-group">
                    <a href="detail.php?id=<?= $maPhieu ?>&status=Bản Nháp" class="btn btn-sm <?= $isDraft ? 'btn-warning' : 'btn-outline-warning' ?>">Bản Nháp</a>
                    <a href="detail.php?id=<?= $maPhieu ?>&status=Đã nhập kho" class="btn btn-sm <?= !$isDraft ? 'btn-success' : 'btn-outline-success' ?>">Đã nhập kho</a>
                </div>
                <a href="phieu-nhap.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
        </div>

        <?php if (!$isDraft): ?>
        <!-- TRƯỜNG HỢP 2: ĐÃ NHẬP KHO - THÔNG BÁO KHÓA & CHỈ MỞ DUY NHẤT Ô SỐ HÓA ĐƠN NCC -->
        <div class="alert alert-secondary border d-flex align-items-center justify-content-between p-3 mb-4">
            <div>
                <i class="bi bi-shield-lock-fill text-success fs-4 me-2 align-middle"></i>
                <span class="fw-semibold text-dark">Phiếu nhập này đã hoàn tất nhập kho.</span> Toàn bộ các dòng sản phẩm, số lượng và giá vốn đã được cố định. Theo quy định, <strong>chỉ được phép cập nhật Hóa Đơn NCC</strong> qua phương thức <code>capNhatHoaDonNCC()</code>.
            </div>
            <form action="detail.php?id=<?= $maPhieu ?>" method="POST" class="d-flex align-items-center gap-2">
                <input type="hidden" name="action" value="update_invoice_ncc">
                <input type="text" name="soHoaDonNCC" class="form-control form-control-sm font-monospace" placeholder="Nhập số hóa đơn đỏ NCC..." value="VAT-ASUS-88912" required style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-tz-primary text-nowrap"><i class="bi bi-check2 me-1"></i>Cập nhật HĐ NCC</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cột Trái: Thông tin chung phiếu nhập -->
            <div class="col-lg-4">
                <div class="tz-card p-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-info-circle text-primary me-2"></i>Thông Tin Phiếu Nhập
                    </h4>

                    <form action="detail.php?id=<?= $maPhieu ?>" method="POST">
                        <input type="hidden" name="action" value="edit_draft">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Mã phiếu (maPhieu)</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace fw-bold" value="<?= $maPhieu ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Thời gian nhập (ngayNhap)</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="24/10/2026 14:00" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Người lập phiếu (nguoiNhap)</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="Nguyễn Đăng Tài (Admin)" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nhà cung cấp (maNCC)</label>
                            <select name="maNCC" class="form-select form-select-sm" <?= !$isDraft ? 'disabled' : '' ?>>
                                <option value="NCC_DELL" selected>Dell Global B.V Việt Nam</option>
                                <option value="NCC_ASUS">Công Ty TNHH ASUS Technology</option>
                                <option value="NCC_APPLE">Công Ty TNHH Apple Việt Nam</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Số hóa đơn NCC (soHoaDonNCC)</label>
                            <input type="text" name="soHoaDonNCC" class="form-control form-control-sm font-monospace" value="Chưa bổ sung" <?= !$isDraft ? 'readonly' : '' ?>>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Đã trả trước NCC (tongTienTraTruocNCC)</label>
                            <input type="number" name="tongTienTraTruocNCC" class="form-control form-control-sm" value="30000000" <?= !$isDraft ? 'readonly' : '' ?>>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Trạng thái thanh toán (trangThaiThanhToan)</label>
                            <select name="trangThaiThanhToan" class="form-select form-select-sm" <?= !$isDraft ? 'disabled' : '' ?>>
                                <option value="coc" selected>coc (Đặt cọc trước)</option>
                                <option value="thanhtoan">thanhtoan (Đã thanh toán đủ)</option>
                            </select>
                        </div>

                        <?php if ($isDraft): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-danger">Lý do sửa phiếu (lyDo) <span class="text-danger">*</span></label>
                            <textarea name="lyDo" class="form-control form-control-sm" rows="2" placeholder="Bắt buộc nhập lý do chỉnh sửa phiếu nhập..." required></textarea>
                            <small class="text-muted">Theo yêu cầu hàm: <code>suaPhieuNhap(maPhieu, lyDo, trangThai)</code></small>
                        </div>

                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 mb-2">
                            <i class="bi bi-save me-1"></i>Lưu Thay Đổi Thông Tin Phiếu
                        </button>
                        <?php endif; ?>
                    </form>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small">Tổng tiền nhập:</span>
                            <span class="fw-bold text-danger fs-5">95.000.000 ₫</span>
                        </div>

                        <?php if ($isDraft): ?>
                        <!-- NÚT CHỐT: XÁC NHẬN NHẬP KHO -->
                        <form action="detail.php?id=<?= $maPhieu ?>" method="POST">
                            <input type="hidden" name="action" value="confirm_import">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold" data-tz-confirm="Bạn có chắc chắn muốn chốt nhập kho? Sau khi chốt, mã Serial sẽ đưa vào kho và số lượng tồn sẽ tự động được cộng vào danh sách hàng sẵn có." data-tz-title="Xác nhận chốt nhập kho" data-tz-type="success" data-tz-btn-text="Đồng ý chốt nhập">
                                <i class="bi bi-check2-all me-1"></i>XÁC NHẬN NHẬP KHO
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Bảng Mặt hàng nhập & Nhập tay Serial/IMEI -->
            <div class="col-lg-8">
                <?php if ($isDraft): ?>
                <!-- Form Thêm Mặt Hàng Vào Phiếu -->
                <div class="tz-card p-3 mb-4 bg-light border">
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 15px;">
                        <i class="bi bi-plus-circle text-primary me-2"></i>Thêm Mặt Hàng Vào Phiếu
                    </h5>
                    <form action="detail.php?id=<?= $maPhieu ?>" method="POST" class="row g-2 align-items-end">
                        <input type="hidden" name="action" value="add_item">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Chọn Sản Phẩm <span class="text-danger">*</span></label>
                            <select name="maSP" class="form-select form-select-sm" required>
                                <option value="SP01">SP01 - Dell XPS 13 9320 Plus</option>
                                <option value="SP02">SP02 - ASUS ROG Strix G16</option>
                                <option value="SP03">SP03 - MacBook Pro 14 M3 Pro</option>
                                <option value="SP04">SP04 - Lenovo ThinkPad X1 Carbon</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" name="soLuongNhap" class="form-control form-control-sm" min="1" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Giá vốn nhập (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="giaVonNhap" class="form-control form-control-sm" placeholder="VD: 31000000" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-tz-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i>Thêm vào phiếu
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Bảng Danh Sách Mặt Hàng Trong Phiếu -->
                <div class="tz-card">
                    <div class="tz-card-header d-flex justify-content-between align-items-center">
                        <h4 class="tz-card-title mb-0"><i class="bi bi-list-check text-primary"></i>Chi Tiết Mặt Hàng Nhập Kho</h4>
                        <span class="badge bg-light text-dark border">2 dòng sản phẩm</span>
                    </div>

                    <div class="table-responsive">
                        <table class="tz-table">
                            <thead>
                                <tr>
                                    <th>MÃ SP</th>
                                    <th>TÊN SẢN PHẨM</th>
                                    <th>SỐ LƯỢNG</th>
                                    <th>GIÁ VỐN NHẬP</th>
                                    <th>THÀNH TIỀN</th>
                                    <th>SERIAL / IMEI</th>
                                    <?php if ($isDraft): ?>
                                    <th class="text-end" style="width: 100px;">THAO TÁC</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dòng 1 -->
                                <tr>
                                    <td><span class="badge bg-light text-dark border font-monospace">SP01</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">Dell XPS 13 9320 Plus</div>
                                        <small class="text-muted">Core i7-1360P / 16GB / 512GB</small>
                                    </td>
                                    <td><span class="fw-bold text-primary">2 chiếc</span></td>
                                    <td>28.500.000 ₫</td>
                                    <td class="fw-bold text-dark">57.000.000 ₫</td>
                                    <td>
                                        <!-- Nút Mở Modal Nhập Tay Serial: SerialSanPham -->
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalSerial_1">
                                            <i class="bi bi-upc-scan me-1"></i>Nhập Serial (2 / 2)
                                        </button>
                                    </td>
                                    <?php if ($isDraft): ?>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" title="Sửa dòng (suaChiTietNhap)"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa dòng (xoaChiTietNhap)"><i class="bi bi-trash3"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>

                                <!-- Dòng 2 -->
                                <tr>
                                    <td><span class="badge bg-light text-dark border font-monospace">SP04</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">Lenovo ThinkPad X1 Carbon Gen 11</div>
                                        <small class="text-muted">Core i7-1365U / 16GB / 512GB</small>
                                    </td>
                                    <td><span class="fw-bold text-primary">1 chiếc</span></td>
                                    <td>38.000.000 ₫</td>
                                    <td class="fw-bold text-dark">38.000.000 ₫</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalSerial_2">
                                            <i class="bi bi-upc-scan me-1"></i>Nhập Serial (1 / 1)
                                        </button>
                                    </td>
                                    <?php if ($isDraft): ?>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" title="Sửa dòng (suaChiTietNhap)"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa dòng (xoaChiTietNhap)"><i class="bi bi-trash3"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Khu Vực Nhập Tay Serial/IMEI cho Dòng 1 (Dell XPS: 2 máy) -->
                <div class="modal fade" id="modalSerial_1" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light border-bottom">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="bi bi-upc-scan text-primary me-2"></i>Nhập Tay Mã Serial/IMEI (SP01 - SL: 2)
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="text-secondary small mb-3">Nhập tay từng mã Serial vật lý, hệ thống tự động kiểm tra tính duy nhất trước khi lưu:</p>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Serial Máy 1 / 2 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light font-monospace">#1</span>
                                        <input type="text" class="form-control font-monospace" value="SN-DELL-XPS-99201" placeholder="Nhập tay mã Serial/IMEI..." <?= !$isDraft ? 'readonly' : '' ?>>
                                        <button type="button" class="btn btn-outline-secondary" onclick="TechZoneNotify.success('Mã Serial hợp lệ, chưa từng tồn tại trong hệ thống!', 'Kiểm tra Serial');"><i class="bi bi-check-lg text-success"></i> Kiểm tra</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Serial Máy 2 / 2 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light font-monospace">#2</span>
                                        <input type="text" class="form-control font-monospace" value="SN-DELL-XPS-99202" placeholder="Nhập tay mã Serial/IMEI..." <?= !$isDraft ? 'readonly' : '' ?>>
                                        <button type="button" class="btn btn-outline-secondary" onclick="TechZoneNotify.success('Mã Serial hợp lệ, chưa từng tồn tại trong hệ thống!', 'Kiểm tra Serial');"><i class="bi bi-check-lg text-success"></i> Kiểm tra</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <?php if ($isDraft): ?>
                                <button type="button" class="btn btn-tz-primary" onclick="TechZoneNotify.success('Đã lưu 2 mã Serial vào trạng thái [Trong kho] thành công!', 'Nhập kho Serial'); bootstrap.Modal.getInstance(document.getElementById('modalSerial_1')).hide();">
                                    <i class="bi bi-check2 me-1"></i>Lưu Mã Serial Nhập Kho
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Khu Vực Nhập Tay Serial/IMEI cho Dòng 2 (Lenovo ThinkPad: 1 máy) -->
                <div class="modal fade" id="modalSerial_2" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light border-bottom">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="bi bi-upc-scan text-primary me-2"></i>Nhập Tay Mã Serial/IMEI (SP04 - SL: 1)
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Serial Máy 1 / 1 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light font-monospace">#1</span>
                                        <input type="text" class="form-control font-monospace" value="SN-LNV-X1-88401" placeholder="Nhập tay mã Serial/IMEI..." <?= !$isDraft ? 'readonly' : '' ?>>
                                        <button type="button" class="btn btn-outline-secondary" onclick="TechZoneNotify.success('Mã Serial hợp lệ, chưa từng tồn tại!', 'Kiểm tra Serial');"><i class="bi bi-check-lg text-success"></i> Kiểm tra</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <?php if ($isDraft): ?>
                                <button type="button" class="btn btn-tz-primary" onclick="TechZoneNotify.success('Đã lưu mã Serial vào trạng thái [Trong kho] thành công!', 'Nhập kho Serial'); bootstrap.Modal.getInstance(document.getElementById('modalSerial_2')).hide();">
                                    <i class="bi bi-check2 me-1"></i>Lưu Mã Serial Nhập Kho
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
