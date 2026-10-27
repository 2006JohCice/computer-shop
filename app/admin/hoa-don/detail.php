<?php
// app/admin/hoa-don/detail.php
$pageTitle = "Chi tiết hóa đơn & Bản in";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maHD = htmlspecialchars($_GET['id'] ?? 'HD2026102301');
$maDH = htmlspecialchars($_GET['maDH'] ?? 'DH01');

// Trạng thái thanh toán: ChuaThanhToan | DaThanhToan | DaHoanTien
$paymentStatus = $_GET['payment'] ?? 'DaThanhToan';

// Xử lý Action POST
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_payment') {
        $paymentStatus = 'DaThanhToan';
        $alertMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check2-circle me-2'></i>Cập nhật trạng thái Đã thanh toán thành công cho hóa đơn <strong>$maHD</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    } elseif ($action === 'cancel_invoice') {
        $paymentStatus = 'DaHoanTien';
        $lyDo = htmlspecialchars($_POST['lyDo'] ?? '');
        $alertMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-arrow-return-left me-2'></i>Đã hủy hóa đơn <strong>$maHD</strong> và chuyển trạng thái sang <strong>Đã hoàn tiền</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <?= $alertMsg ?>

        <!-- Header điều hướng & Nút In hóa đơn -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 d-print-none">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="hoa-don.php" class="text-decoration-none text-muted">Hóa đơn</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?= $maHD ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 22px;">Hóa Đơn Tài Chính: <?= $maHD ?></h2>
                    <?php if ($paymentStatus === 'DaThanhToan'): ?>
                        <span class="tz-badge tz-badge-success"><i class="bi bi-check2-circle"></i>ĐÃ THANH TOÁN</span>
                    <?php elseif ($paymentStatus === 'ChuaThanhToan'): ?>
                        <span class="tz-badge tz-badge-warning"><i class="bi bi-clock-history"></i>CHƯA THANH TOÁN</span>
                    <?php else: ?>
                        <span class="tz-badge tz-badge-danger"><i class="bi bi-arrow-return-left"></i>ĐÃ HOÀN TIỀN</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Nút In Hóa Đơn -->
                <button type="button" class="btn btn-tz-primary" onclick="window.print();">
                    <i class="bi bi-printer-fill me-1"></i>In Hóa Đơn
                </button>

                <?php if ($paymentStatus === 'ChuaThanhToan'): ?>
                <!-- Nút Cập nhật thanh toán -->
                <form action="detail.php?id=<?= $maHD ?>" method="POST" class="d-inline">
                    <input type="hidden" name="action" value="update_payment">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-credit-card me-1"></i>Xác nhận Đã thanh toán
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($paymentStatus !== 'DaHoanTien'): ?>
                <!-- Nút Hủy Hóa Đơn -->
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHuyHD">
                    <i class="bi bi-trash3 me-1"></i>Hủy Hóa Đơn
                </button>
                <?php endif; ?>

                <a href="hoa-don.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>

        <!-- KHUNG HÓA ĐƠN PRINT VIEW CHUẨN CÔNG TY TECHZONE -->
        <div class="tz-card p-5 mx-auto" style="max-width: 900px; background: #fff;" id="invoicePrintArea">
            <!-- Header Hóa Đơn -->
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-sm-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded bg-primary text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-cpu-fill fs-3"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">TECHZONE VIỆT NAM</h3>
                            <small class="text-muted">Hệ Thống Phân Phối Laptop & Linh Kiện Chính Hãng</small>
                        </div>
                    </div>
                    <p class="text-secondary small mb-0">
                        <i class="bi bi-geo-alt me-1"></i>Số 236 Hoàng Quốc Việt, Cổ Nhuế 1, Bắc Từ Liêm, Hà Nội<br>
                        <i class="bi bi-telephone me-1"></i>Hotline: 1900 8899 • MST: 0108928412
                    </p>
                </div>
                <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                    <h2 class="fw-bold text-primary mb-1" style="font-size: 24px;">HÓA ĐƠN BÁN HÀNG</h2>
                    <div class="font-monospace fw-bold text-dark fs-6 mb-1">Mã HĐ: <?= $maHD ?></div>
                    <div class="text-muted small">Liên kết Đơn hàng: <strong class="text-dark font-monospace"><?= $maDH ?></strong></div>
                    <div class="text-muted small">Ngày xuất: <strong>23/10/2026 14:15</strong></div>
                </div>
            </div>

            <!-- Khách hàng & Giao hàng -->
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 13px;">KHÁCH HÀNG (KhachHang):</h6>
                    <div class="fw-bold text-dark fs-6">Nguyễn Văn An</div>
                    <div class="text-secondary small"><i class="bi bi-telephone me-1"></i>0912 345 678</div>
                    <div class="text-secondary small"><i class="bi bi-geo-alt me-1"></i>123 Đường Láng, Đống Đa, Hà Nội</div>
                    <div class="text-secondary small"><i class="bi bi-building me-1"></i>MST Khách hàng (maSoThueKH): <strong class="font-monospace">0108928412-001</strong></div>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 13px;">THANH TOÁN:</h6>
                    <div class="text-secondary small">Phương thức: <strong>Chuyển khoản Online VNPAY</strong></div>
                    <div class="text-secondary small">Trạng thái: 
                        <?php if ($paymentStatus === 'DaThanhToan'): ?>
                            <span class="badge bg-success text-white">ĐÃ THANH TOÁN ĐỦ</span>
                        <?php elseif ($paymentStatus === 'ChuaThanhToan'): ?>
                            <span class="badge bg-warning text-dark">CHƯA THANH TOÁN</span>
                        <?php else: ?>
                            <span class="badge bg-danger text-white">ĐÃ HOÀN TIỀN</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-secondary small">Mã khuyến mãi: <strong class="font-monospace text-primary">KM_CHAOMUNG (-5%)</strong></div>
                </div>
            </div>

            <!-- Bảng Sản Phẩm Kèm Mã Serial/IMEI -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">STT</th>
                            <th>TÊN MÁY TÍNH / THIẾT BỊ</th>
                            <th>MÃ SERIAL / IMEI VẬT LÝ</th>
                            <th class="text-center" style="width: 80px;">SL</th>
                            <th class="text-end" style="width: 140px;">ĐƠN GIÁ</th>
                            <th class="text-end" style="width: 150px;">THÀNH TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold">1</td>
                            <td>
                                <div class="fw-bold text-dark">Dell XPS 13 Plus 9320</div>
                                <small class="text-muted">Core i7-1360P / 16GB RAM / 512GB SSD / OLED</small>
                            </td>
                            <td>
                                <!-- Mã Serial/IMEI vật lý -->
                                <span class="badge bg-light text-danger border font-monospace px-2 py-1">
                                    <i class="bi bi-upc-scan me-1"></i>SN-DELL-XPS-99201
                                </span>
                                <small class="d-block text-muted" style="font-size: 11px;">Bảo hành 24T chính hãng</small>
                            </td>
                            <td class="text-center fw-bold">1</td>
                            <td class="text-end">35.990.000 ₫</td>
                            <td class="text-end fw-bold text-dark">35.990.000 ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tóm tắt chi phí & Chữ ký -->
            <div class="row align-items-end mb-5">
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded text-center d-inline-block border">
                        <i class="bi bi-qr-code fs-1 text-primary"></i>
                        <small class="d-block text-muted mt-1" style="font-size: 11px;">Quét tra cứu hóa đơn điện tử</small>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Tổng tiền hàng:</span>
                        <span class="fw-semibold">35.990.000 ₫</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Chiết khấu Voucher (KM_CHAOMUNG):</span>
                        <span class="text-success fw-semibold">- 1.799.500 ₫</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Phí vận chuyển giao hàng:</span>
                        <span class="fw-semibold">+ 50.000 ₫</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-top mt-2">
                        <span class="fw-bold text-dark fs-6">SỐ TIỀN THANH TOÁN:</span>
                        <span class="fw-bold text-danger fs-5">34.240.500 ₫</span>
                    </div>
                </div>
            </div>

            <!-- Khung Ký Tên -->
            <div class="row text-center mt-5 pt-3 border-top">
                <div class="col-4">
                    <div class="fw-bold text-dark">Người Lập Hóa Đơn</div>
                    <small class="text-muted">(Ký, họ tên)</small>
                    <div class="mt-4 pt-4 fw-semibold text-secondary">Nguyễn Đăng Tài</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold text-dark">Người Giao Hàng</div>
                    <small class="text-muted">(Ký, họ tên)</small>
                    <div class="mt-4 pt-4 text-muted">.........................</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold text-dark">Khách Hàng Nhận Máy</div>
                    <small class="text-muted">(Ký, họ tên)</small>
                    <div class="mt-4 pt-4 fw-semibold text-secondary">Nguyễn Văn An</div>
                </div>
            </div>
        </div>

        <!-- Modal Hủy Hóa Đơn -->
        <div class="modal fade" id="modalHuyHD" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-arrow-return-left me-2"></i>Hủy Hóa Đơn (<?= $maHD ?>)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="detail.php?id=<?= $maHD ?>" method="POST">
                        <input type="hidden" name="action" value="cancel_invoice">
                        <div class="modal-body p-4">
                            <p class="text-secondary small mb-3">Xác nhận chuyển trạng thái thanh toán của hóa đơn sang <strong>Đã hoàn tiền</strong>:</p>
                            <label class="form-label fw-semibold">Lý do hủy hóa đơn <span class="text-danger">*</span></label>
                            <textarea name="lyDo" class="form-control" rows="3" placeholder="Nhập lý do xuất sai hoặc đổi trả hàng..." required></textarea>
                        </div>
                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-check2 me-1"></i>Xác nhận Hủy Hóa Đơn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .tz-sidebar, .tz-topbar, .d-print-none, .btn-back-pill, .modal { display: none !important; }
    .tz-main-wrapper { margin-left: 0 !important; width: 100% !important; }
    .tz-content-area { padding: 0 !important; }
    #invoicePrintArea { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; }
}
</style>
