<?php
// app/views/client/checkout/index.php
$pageTitle = "Thanh Toán & Đặt Hàng";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/cart.php" class="text-decoration-none text-muted">Giỏ hàng</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Thanh toán đặt hàng</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1" style="font-size: 24px;">
            <i class="bi bi-credit-card-2-front text-primary me-2"></i>Thanh Toán & Đặt Hàng Laptop
        </h2>
        <p class="text-secondary mb-0 small">Hoàn tất thông tin nhận hàng và lựa chọn phương thức thanh toán an toàn, bảo mật.</p>
    </div>

    <form id="checkoutForm" onsubmit="handlePlaceOrder(event);">
        <div class="row g-4">
            <!-- CỘT TRÁI: THÔNG TIN NGƯỜI NHẬN & PHƯƠNG THỨC -->
            <div class="col-lg-7">
                <!-- 1. Thông tin giao nhận -->
                <div class="tz-card p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>1. Thông Tin Người Nhận Hàng
                    </h4>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="tenNguoiNhan" id="tenNguoiNhan" class="form-control" value="Nguyễn Văn An" placeholder="Nhập tên người nhận..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                            <input type="tel" name="sdtNhanHang" id="sdtNhanHang" class="form-control" value="0912345678" placeholder="Nhập SĐT..." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
                            <textarea name="diaChiGiaoHang" id="diaChiGiaoHang" class="form-control" rows="2" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành..." required>123 Đường Láng, Đống Đa, Hà Nội</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea name="ghiChuKhachHang" id="ghiChuKhachHang" class="form-control" rows="2" placeholder="VD: Giao giờ hành chính, gọi trước 15 phút, cài sẵn Windows 11 + Office..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Phương thức giao hàng (phuongThucGiaoHang) -->
                <div class="tz-card p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-truck text-primary me-2"></i>2. Phương Thức Vận Chuyển
                    </h4>

                    <div class="d-flex flex-column gap-2">
                        <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="15000" class="form-check-input mt-0" checked onchange="updateShippingFee(15000)">
                                <div>
                                    <div class="fw-bold text-dark">Giao Hàng Tiêu Chuẩn</div>
                                    <small class="text-muted">Nhận hàng trong 2-3 ngày làm việc</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary">15.000 ₫</span>
                        </label>

                        <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="30000" class="form-check-input mt-0" onchange="updateShippingFee(30000)">
                                <div>
                                    <div class="fw-bold text-dark">Giao Hàng Nhanh 24H</div>
                                    <small class="text-muted">Giao trong ngày hoặc sáng hôm sau</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary">30.000 ₫</span>
                        </label>

                        <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="phuongThucGiaoHang" value="50000" class="form-check-input mt-0" onchange="updateShippingFee(50000)">
                                <div>
                                    <div class="fw-bold text-dark">Giao Hỏa Tốc Nội Thành 2H</div>
                                    <small class="text-muted">Kỹ thuật viên giao tận tay và hướng dẫn cài đặt</small>
                                </div>
                            </div>
                            <span class="fw-bold text-primary">50.000 ₫</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Phương thức thanh toán (phuongThucThanhToan) -->
                <div class="tz-card p-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-wallet2 text-success me-2"></i>3. Phương Thức Thanh Toán
                    </h4>

                    <div class="d-flex flex-column gap-2">
                        <label class="p-3 border rounded-3 d-flex align-items-center gap-3" style="cursor: pointer;">
                            <input type="radio" name="phuongThucThanhToan" value="COD" class="form-check-input mt-0" checked>
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-cash-coin text-success me-2"></i>Thanh toán khi nhận hàng (COD)</div>
                                <small class="text-muted">Kiểm tra máy và Serial nguyên vẹn trước khi thanh toán tiền mặt</small>
                            </div>
                        </label>

                        <label class="p-3 border rounded-3 d-flex align-items-center gap-3" style="cursor: pointer;">
                            <input type="radio" name="phuongThucThanhToan" value="VNPAY" class="form-check-input mt-0">
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-credit-card text-primary me-2"></i>Chuyển khoản Online VNPAY QR</div>
                                <small class="text-muted">Hỗ trợ tất cả ứng dụng ngân hàng và ví điện tử VNPAY</small>
                            </div>
                        </label>

                        <label class="p-3 border rounded-3 d-flex align-items-center gap-3" style="cursor: pointer;">
                            <input type="radio" name="phuongThucThanhToan" value="SEPAY" class="form-check-input mt-0">
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-qr-code-scan text-danger me-2"></i>Quét mã QR Tự Động (SEPAY / MOMO)</div>
                                <small class="text-muted">Xác nhận thanh toán tự động qua Webhook trong 3 giây</small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & MÃ KHUYẾN MÃI -->
            <div class="col-lg-5">
                <div class="tz-card p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                        <i class="bi bi-bag-check text-primary me-2"></i>Sản Phẩm Trong Đơn Hàng
                    </h4>

                    <!-- Danh sách item từ giỏ hàng -->
                    <div id="checkoutItemsList" class="mb-3">
                        <!-- Render JS -->
                    </div>

                    <!-- Ô NHẬP MÃ GIẢM GIÁ -->
                    <div class="mb-3 pt-3 border-top">
                        <label class="form-label small fw-semibold"><i class="bi bi-tag text-primary me-1"></i>Mã Khuyến Mãi (Voucher)</label>
                        <div class="input-group">
                            <input type="text" id="voucherCodeInput" class="form-control text-uppercase font-monospace" placeholder="VD: BACK2SCHOOL">
                            <button type="button" class="btn btn-outline-primary" onclick="applyVoucher()">Áp dụng</button>
                        </div>
                        <div id="voucherFeedback" class="small mt-1"></div>
                        <small class="text-muted d-block mt-1">Mã dùng thử: <code>BACK2SCHOOL</code> (-10%), <code>TECHZONE500K</code> (-500k)</small>
                    </div>

                    <!-- Bảng tính chi phí -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small">Tổng tiền hàng:</span>
                            <span class="fw-semibold text-dark" id="checkoutSubtotal">0 ₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small">Phí vận chuyển:</span>
                            <span class="fw-semibold text-dark" id="checkoutShipping">15.000 ₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none !important;">
                            <span class="text-success small fw-semibold">Chiết khấu Voucher:</span>
                            <span class="text-success fw-bold" id="checkoutDiscount">-0 ₫</span>
                        </div>

                        <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">TỔNG CỘNG:</span>
                            <span class="fw-bold text-danger fs-4" id="checkoutFinalTotal">0 ₫</span>
                        </div>
                    </div>

                    <!-- NÚT HOÀN TẤT ĐẶT HÀNG -->
                    <button type="submit" class="btn btn-tz-cta w-100 py-3 mt-4 text-center justify-content-center fs-6">
                        <i class="bi bi-shield-check me-2"></i>HOÀN TẤT ĐẶT HÀNG
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- MODAL XÁC NHẬN THANH TOÁN ONLINE (VNPAY / MOMO / SEPAY) -->
<div class="modal fade" id="modalOnlinePayment" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-qr-code me-2"></i>Quét Mã QR Thanh Toán Online
                </h5>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-secondary small mb-3">Đơn hàng <strong id="modalOrderCode" class="font-monospace text-primary">DH...</strong> đã được khởi tạo. Vui lòng quét mã QR chuyển khoản chính xác:</p>
                
                <div class="p-3 bg-light rounded-3 d-inline-block border mb-3">
                    <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TECHZONE_PAYMENT" alt="TechZone Payment QR" class="img-fluid" style="width: 200px; height: 200px;">
                    <div class="fw-bold text-danger fs-5 mt-2" id="modalPaymentAmount">35.990.000 ₫</div>
                    <small class="text-muted font-monospace">Nội dung CK: <strong id="modalTransferNote">DH01</strong></small>
                </div>

                <div class="alert alert-info py-2 small mb-0">
                    <i class="bi bi-info-circle me-1"></i>Hệ thống tự động kích hoạt bảo hành Serial/IMEI ngay sau khi giao dịch thành công.
                </div>
            </div>
            <div class="modal-footer bg-light border-top justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="location.href='<?= BASE_URL ?>/client/tra-cuu.php'">Thanh toán sau</button>
                <button type="button" class="btn btn-success" onclick="confirmOnlinePaymentSuccess()">
                    <i class="bi bi-check2-circle me-1"></i>Xác Nhận Đã Thanh Toán Thành Công
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentShippingFee = 15000;
let appliedDiscount = 0;
let appliedVoucherCode = '';

function updateShippingFee(fee) {
    currentShippingFee = fee;
    document.getElementById('checkoutShipping').textContent = Number(fee).toLocaleString('vi-VN') + ' ₫';
    recalcTotal();
}

function recalcTotal() {
    const subtotal = CartManager.tinhTongTien();
    const finalTotal = Math.max(0, subtotal + currentShippingFee - appliedDiscount);
    document.getElementById('checkoutSubtotal').textContent = Number(subtotal).toLocaleString('vi-VN') + ' ₫';
    document.getElementById('checkoutFinalTotal').textContent = Number(finalTotal).toLocaleString('vi-VN') + ' ₫';
}

function applyVoucher() {
    const code = document.getElementById('voucherCodeInput').value.trim().toUpperCase();
    const feedback = document.getElementById('voucherFeedback');
    const voucher = TECHZONE_VOUCHERS[code];
    const subtotal = CartManager.tinhTongTien();

    if (!code) {
        feedback.className = 'small mt-1 text-danger';
        feedback.textContent = 'Vui lòng nhập mã khuyến mãi!';
        return;
    }

    if (voucher) {
        if (voucher.loai === 'percent') {
            appliedDiscount = Math.min((subtotal * voucher.giam) / 100, voucher.max);
        } else {
            appliedDiscount = voucher.giam;
        }
        appliedVoucherCode = code;

        feedback.className = 'small mt-1 text-success fw-semibold';
        feedback.innerHTML = `<i class="bi bi-check-circle me-1"></i>Áp dụng mã <strong>${code}</strong> thành công! Giảm: -${Number(appliedDiscount).toLocaleString('vi-VN')} ₫`;
        
        document.getElementById('discountRow').style.setProperty('display', 'flex', 'important');
        document.getElementById('checkoutDiscount').textContent = `-${Number(appliedDiscount).toLocaleString('vi-VN')} ₫`;
        recalcTotal();
        TechZoneNotify.success(`Áp dụng mã giảm giá ${code} thành công!`, 'Khuyến mãi');
    } else {
        feedback.className = 'small mt-1 text-danger';
        feedback.textContent = 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn!';
        appliedDiscount = 0;
        appliedVoucherCode = '';
        document.getElementById('discountRow').style.setProperty('display', 'none', 'important');
        recalcTotal();
        TechZoneNotify.error('Mã khuyến mãi không tồn tại hoặc đã hết hạn!', 'Khuyến mãi');
    }
}

function renderCheckoutPage() {
    const cart = CartManager.getCart();
    const listContainer = document.getElementById('checkoutItemsList');
    listContainer.innerHTML = '';

    for (const [maSP, qty] of Object.entries(cart)) {
        const item = TECHZONE_CATALOG[maSP];
        if (!item) continue;
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center justify-content-between mb-2 pb-2 border-bottom';
        div.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <img src="${item.img}" style="width: 44px; height: 44px; object-fit: contain;" class="rounded border">
                <div>
                    <div class="fw-bold text-dark small">${item.tenSP}</div>
                    <small class="text-muted">SL: ${qty} x ${Number(item.giaBan).toLocaleString('vi-VN')} ₫</small>
                </div>
            </div>
            <div class="fw-bold text-dark small">${Number(item.giaBan * qty).toLocaleString('vi-VN')} ₫</div>
        `;
        listContainer.appendChild(div);
    }

    recalcTotal();
}

let generatedOrderId = '';

function handlePlaceOrder(event) {
    event.preventDefault();
    const cart = CartManager.getCart();
    if (Object.keys(cart).length === 0) {
        TechZoneNotify.warning("Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước.", "Thông báo");
        return;
    }

    // Tự sinh mã đơn hàng
    generatedOrderId = 'DH' + Math.floor(1000 + Math.random() * 9000);
    const method = document.querySelector('input[name="phuongThucThanhToan"]:checked').value;
    const finalTotal = document.getElementById('checkoutFinalTotal').textContent;

    if (method === 'COD') {
        TechZoneNotify.confirm({
            title: 'Xác Nhận Đặt Hàng',
            message: `Bạn đang chọn thanh toán tiền mặt khi nhận hàng (COD).<br>Mã đơn hàng dự kiến: <strong class="text-primary font-monospace">${generatedOrderId}</strong><br>Tổng thanh toán: <strong class="text-danger">${finalTotal}</strong>`,
            confirmText: 'Xác nhận đặt ngay',
            cancelText: 'Kiểm tra lại',
            type: 'primary',
            onConfirm: function () {
                CartManager.xoaToanBoGio();
                TechZoneNotify.success(`Đặt hàng thành công! Mã đơn: ${generatedOrderId}`, 'Hoàn tất đơn hàng');
                setTimeout(() => {
                    location.href = `<?= BASE_URL ?>/client/tra-cuu.php?id=${generatedOrderId}`;
                }, 1200);
            }
        });
    } else {
        // Mở Modal quét QR thanh toán Online
        document.getElementById('modalOrderCode').textContent = generatedOrderId;
        document.getElementById('modalPaymentAmount').textContent = finalTotal;
        document.getElementById('modalTransferNote').textContent = `TECHZONE ${generatedOrderId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('modalOnlinePayment'));
        modal.show();
    }
}

function confirmOnlinePaymentSuccess() {
    TechZoneNotify.success(`Đã xác nhận giao dịch thanh toán trực tuyến cho đơn hàng ${generatedOrderId}!`, 'Thanh toán thành công');
    CartManager.xoaToanBoGio();
    setTimeout(() => {
        location.href = `<?= BASE_URL ?>/client/tra-cuu.php?id=${generatedOrderId}&paid=1`;
    }, 1200);
}

document.addEventListener('DOMContentLoaded', () => {
    renderCheckoutPage();
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
