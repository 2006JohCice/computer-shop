<?php
// app/views/client/tra-cuu-bao-hanh/index.php
$pageTitle = "Tra Cứu Đơn Hàng & Bảo Hành Serial/IMEI";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

$searchOrder = htmlspecialchars($_GET['id'] ?? 'DH01');
$searchPhone = htmlspecialchars($_GET['sdt'] ?? '0912345678');
$isPaidDemo = isset($_GET['paid']);

// Xử lý Hủy đơn từ phía khách hàng
$alertMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'user_cancel_order') {
        $lyDo = htmlspecialchars($_POST['lyDo'] ?? '');
        $alertMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='bi bi-info-circle-fill me-2'></i>Đơn hàng <strong>$searchOrder</strong> đã được tiếp nhận yêu cầu hủy. Bộ phận CSKH TechZone sẽ xử lý ngay!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                     </div>";
    }
}

// Trạng thái đơn mẫu để hiển thị
$orderStatus = 'Tiếp nhận';
if ($searchOrder === 'DH01') $orderStatus = 'Hoàn thành';
elseif ($searchOrder === 'DH02') $orderStatus = 'Đã duyệt';
elseif ($searchOrder === 'DH03') $orderStatus = 'Tiếp nhận';

$canCancel = ($orderStatus === 'Tiếp nhận');
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Tra cứu đơn hàng & Bảo hành</li>
        </ol>
    </nav>

    <?= $alertMsg ?>

    <!-- Tabs chuyển đổi giữa Tra cứu đơn hàng và Tra cứu bảo hành Serial -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="pills-order-tab" data-bs-toggle="pill" data-bs-target="#pills-order" type="button" role="tab">
                <i class="bi bi-box-seam me-1"></i>Tra Cứu Tiến Trình Đơn Hàng
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link fw-bold" id="pills-warranty-tab" data-bs-toggle="pill" data-bs-target="#pills-warranty" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i>Tra Cứu Hạn Bảo Hành Serial/IMEI
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- TAB 1: TRA CỨU ĐƠN HÀNG -->
        <div class="tab-pane fade show active" id="pills-order" role="tabpanel">
            <div class="tz-card p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                    <i class="bi bi-search text-primary me-2"></i>Tra Cứu Đơn Hàng
                </h4>
                <form action="tra-cuu.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Mã đơn hàng <span class="text-danger">*</span></label>
                        <input type="text" name="id" class="form-control text-uppercase font-monospace" value="<?= $searchOrder ?>" placeholder="VD: DH01, DH02, DH03..." required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                        <input type="tel" name="sdt" class="form-control" value="<?= $searchPhone ?>" placeholder="VD: 0912345678" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-tz-primary w-100">
                            <i class="bi bi-search me-1"></i>Tra Cứu
                        </button>
                    </div>
                </form>
                <div class="mt-2 small text-muted">
                    Thử nghiệm mã: <a href="tra-cuu.php?id=DH03&sdt=0903112233" class="badge bg-light text-primary border">DH03 (Chờ duyệt - Được phép hủy)</a>
                    <a href="tra-cuu.php?id=DH01&sdt=0912345678" class="badge bg-light text-success border">DH01 (Đã xuất kho gán Serial)</a>
                </div>
            </div>

            <!-- KẾT QUẢ ĐƠN HÀNG HIỂN THỊ -->
            <div class="tz-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 mb-4 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold text-dark mb-0 fs-5">Chi Tiết Đơn Hàng: <?= $searchOrder ?></h3>
                            <?php if ($orderStatus === 'Tiếp nhận'): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Đang tiếp nhận</span>
                            <?php elseif ($orderStatus === 'Đã duyệt'): ?>
                                <span class="badge bg-primary text-white"><i class="bi bi-check2 me-1"></i>Đã duyệt xuất kho</span>
                            <?php else: ?>
                                <span class="badge bg-success text-white"><i class="bi bi-check2-all me-1"></i>Giao hàng thành công</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Ngày đặt: 24/10/2026 • Người nhận: Nguyễn Văn An (0912 345 678)</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if ($canCancel): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalUserCancelOrder">
                                <i class="bi bi-x-circle me-1"></i>Hủy Đơn Hàng Này
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Đơn hàng đã xuất kho">
                                <i class="bi bi-lock me-1"></i>Không thể hủy (Đã xuất kho)
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$canCancel): ?>
                    <div class="alert alert-warning py-2 small mb-4">
                        <i class="bi bi-info-circle-fill me-1"></i>Đơn hàng đã được xuất kho và bàn giao cho đơn vị vận chuyển. Nếu quý khách muốn thay đổi thông tin hoặc cần hỗ trợ, vui lòng gọi Hotline: <strong>1900 8899</strong>.
                    </div>
                <?php endif; ?>

                <!-- Bảng danh sách sản phẩm và SERIAL/IMEI -->
                <h5 class="fw-bold text-dark mb-3" style="font-size: 15px;">Danh Sách Sản Phẩm Trong Đơn:</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>SẢN PHẨM</th>
                                <th>SỐ LƯỢNG</th>
                                <th>GIÁ TIỀN</th>
                                <th>MÃ SERIAL / IMEI & HẠN BẢO HÀNH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">Dell XPS 13 Plus 9320</div>
                                    <small class="text-muted font-monospace">SP01 • Intel Core i7-1360P / 16GB / 512GB</small>
                                </td>
                                <td><span class="fw-bold">1</span></td>
                                <td class="fw-bold text-danger">35.990.000 ₫</td>
                                <td>
                                    <?php if ($orderStatus !== 'Tiếp nhận'): ?>
                                        <div class="badge bg-light text-danger border font-monospace py-2 px-3">
                                            <i class="bi bi-upc-scan me-1"></i>SN-DELL-XPS-99201
                                        </div>
                                        <div class="small text-success mt-1"><i class="bi bi-shield-check me-1"></i>Hạn bảo hành chính hãng: 24/10/2028</div>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Đang chờ kho kiểm đếm và gán Serial</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tóm tắt thanh toán -->
                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Trạng thái thanh toán:</span>
                        <strong class="text-success ms-1"><?= ($isPaidDemo || $orderStatus !== 'Tiếp nhận') ? 'Đã Thanh Toán (Online)' : 'Chờ Thanh Toán (COD khi nhận)' ?></strong>
                    </div>
                    <div>
                        <span class="text-muted small">Tổng tiền thanh toán:</span>
                        <strong class="text-danger fs-5 ms-1">36.140.500 ₫</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: TRA CỨU HẠN BẢO HÀNH THEO SERIAL/IMEI -->
        <div class="tab-pane fade" id="pills-warranty" role="tabpanel">
            <div class="tz-card p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                    <i class="bi bi-shield-check text-success me-2"></i>Tra Cứu Hạn Bảo Hành Điện Tử Theo Serial
                </h4>
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Nhập mã Serial / IMEI của máy laptop <span class="text-danger">*</span></label>
                        <input type="text" id="serialInput" class="form-control font-monospace" placeholder="VD: SN-DELL-XPS-99201" value="SN-DELL-XPS-99201">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success w-100" onclick="checkSerialWarranty()">
                            <i class="bi bi-search me-1"></i>Kiểm Tra Bảo Hành
                        </button>
                    </div>
                </div>
            </div>

            <!-- Khung kết quả bảo hành -->
            <div class="tz-card p-4" id="warrantyResultArea">
                <div class="d-flex align-items-center gap-3 p-3 bg-success-subtle text-success border border-success-subtle rounded-3 mb-3">
                    <i class="bi bi-patch-check-fill fs-1"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Máy Tính Đang Trong Thời Hạn Bảo Hành Hợp Lệ</h5>
                        <p class="mb-0 small">Thiết bị được bảo hành 100% phần cứng chính hãng tại trung tâm TechZone trên toàn quốc.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Sản phẩm:</div>
                        <div class="fw-bold text-dark">Dell XPS 13 Plus 9320 (Intel Core i7)</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Mã Serial / IMEI:</div>
                        <div class="font-monospace fw-bold text-danger">SN-DELL-XPS-99201</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Ngày kích hoạt:</div>
                        <div class="fw-semibold">24/10/2026</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Thời hạn kết thúc bảo hành:</div>
                        <div class="fw-bold text-success">24/10/2028 (Còn 730 ngày)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HỦY ĐƠN HÀNG DÀNH CHO USER -->
<div class="modal fade" id="modalUserCancelOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Hủy Đơn Hàng (<?= $searchOrder ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="tra-cuu.php?id=<?= $searchOrder ?>&sdt=<?= $searchPhone ?>" method="POST">
                <input type="hidden" name="action" value="user_cancel_order">
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Đơn hàng của bạn đang ở trạng thái <strong>Tiếp nhận</strong> (Chưa xuất kho). Bạn có quyền yêu cầu hủy mà không mất bất kỳ khoản phí nào.</p>
                    
                    <label class="form-label small fw-semibold">Vui lòng cho biết lý do bạn muốn hủy đơn <span class="text-danger">*</span></label>
                    <textarea name="lyDo" class="form-control" rows="3" placeholder="VD: Tôi muốn đổi sang mẫu máy khác, tôi bận không nhận hàng được..." required></textarea>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Giữ lại đơn hàng</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Xác Nhận Hủy Đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkSerialWarranty() {
    const serial = document.getElementById('serialInput').value.trim();
    if (!serial) {
        TechZoneNotify.warning("Vui lòng nhập mã Serial / IMEI để tra cứu!", "Bảo hành");
        return;
    }
    TechZoneNotify.success(`Đã tra cứu thành công mã Serial [${serial}]!<br>Trạng thái: <strong>Còn hạn bảo hành chính hãng</strong>.`, "Kết quả tra cứu");
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
