<?php
// app/views/client/cart/index.php
$pageTitle = "Giỏ Hàng Của Bạn";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Giỏ hàng của bạn</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 24px;">
                <i class="bi bi-cart3 text-primary me-2"></i>Giỏ Hàng Của Bạn
            </h2>
            <p class="text-secondary mb-0 small">Quản lý các sản phẩm laptop đã chọn trước khi tiến hành đặt hàng và thanh toán.</p>
        </div>

        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmClearCart()">
            <i class="bi bi-trash3 me-1"></i>Xóa Sạch Giỏ Hàng
        </button>
    </div>

    <div class="row g-4" id="cartContentArea">
        <!-- Cột Trái: Bảng Sản Phẩm Trong Giỏ -->
        <div class="col-lg-8">
            <div class="tz-card p-4">
                <div class="table-responsive">
                    <table class="table align-middle" id="cartTable">
                        <thead class="table-light">
                            <tr>
                                <th>SẢN PHẨM LAPTOP</th>
                                <th style="width: 140px;">ĐƠN GIÁ</th>
                                <th style="width: 140px;" class="text-center">SỐ LƯỢNG</th>
                                <th style="width: 150px;" class="text-end">TẠM TÍNH</th>
                                <th style="width: 50px;" class="text-end">XÓA</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <!-- Render bằng JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Tiếp tục chọn thêm laptop
                    </a>
                    <span class="text-success small"><i class="bi bi-shield-check me-1"></i>Tự động lưu và cập nhật tổng tiền tức thì</span>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Tóm Tắt & Nút Thanh Toán -->
        <div class="col-lg-4">
            <div class="tz-card p-4">
                <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                    <i class="bi bi-receipt text-primary me-2"></i>Tóm Tắt Đơn Hàng
                </h4>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Tạm tính giỏ hàng:</span>
                    <span class="fw-bold text-dark" id="subtotalDisplay">0 ₫</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Phí vận chuyển dự kiến:</span>
                    <span class="text-success fw-semibold">Miễn phí giao hàng</span>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-dark fs-6">TỔNG THANH TOÁN:</span>
                        <span class="fw-bold text-danger fs-4" id="totalDisplay">0 ₫</span>
                    </div>

                    <a href="<?= BASE_URL ?>/client/checkout.php" class="btn btn-tz-cta w-100 py-3 text-center justify-content-center" id="btnCheckout">
                        <i class="bi bi-credit-card me-1"></i>Tiến Hành Đặt Hàng (Checkout)
                    </a>
                </div>
            </div>

            <!-- Cam kết mua hàng -->
            <div class="p-3 bg-white rounded-3 border mt-3" style="font-size: 12.5px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <span>Bảo hành điện tử 100% kích hoạt theo mã Serial/IMEI</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-counterclockwise text-primary fs-5"></i>
                    <span>Đổi mới trong 30 ngày nếu phát hiện lỗi phần cứng NSX</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function renderCartPage() {
    const cart = CartManager.getCart();
    const tbody = document.getElementById('cartTableBody');
    const keys = Object.keys(cart);

    if (keys.length === 0) {
        document.getElementById('cartContentArea').innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted d-block mb-3"></i>
                <h4 class="text-secondary">Giỏ hàng của bạn đang trống</h4>
                <p class="text-muted small">Hãy dạo quanh cửa hàng và chọn những mẫu laptop yêu thích nhất!</p>
                <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-tz-cta mt-2">
                    <i class="bi bi-laptop me-1"></i>Xem Sản Phẩm Ngay
                </a>
            </div>
        `;
        return;
    }

    tbody.innerHTML = '';
    let total = 0;

    for (const [maSP, qty] of Object.entries(cart)) {
        const item = TECHZONE_CATALOG[maSP];
        if (!item) continue;

        const subtotal = item.giaBan * qty;
        total += subtotal;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.img}" class="rounded border p-1" style="width: 60px; height: 60px; object-fit: contain;">
                    <div>
                        <div class="fw-bold text-dark">${item.tenSP}</div>
                        <small class="text-muted font-monospace">${item.thuongHieu} • Mã SP: ${maSP}</small>
                    </div>
                </div>
            </td>
            <td><span class="fw-semibold text-secondary">${Number(item.giaBan).toLocaleString('vi-VN')} ₫</span></td>
            <td>
                <div class="input-group input-group-sm justify-content-center" style="max-width: 110px; margin: auto;">
                    <button class="btn btn-outline-secondary" type="button" onclick="CartManager.capNhatSoLuong('${maSP}', ${qty - 1})">-</button>
                    <input type="number" class="form-control text-center" value="${qty}" min="1" max="10" onchange="CartManager.capNhatSoLuong('${maSP}', parseInt(this.value) || 1)">
                    <button class="btn btn-outline-secondary" type="button" onclick="CartManager.capNhatSoLuong('${maSP}', ${qty + 1})">+</button>
                </div>
            </td>
            <td class="text-end fw-bold text-danger">${Number(subtotal).toLocaleString('vi-VN')} ₫</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-link text-danger p-0" title="Bỏ sản phẩm" onclick="confirmRemoveItem('${maSP}')">
                    <i class="bi bi-trash3 fs-5"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    document.getElementById('subtotalDisplay').textContent = Number(total).toLocaleString('vi-VN') + ' ₫';
    document.getElementById('totalDisplay').textContent = Number(total).toLocaleString('vi-VN') + ' ₫';
}

function confirmClearCart() {
    TechZoneNotify.confirm({
        title: 'Xóa Sạch Giỏ Hàng',
        message: 'Bạn có chắc chắn muốn xóa toàn bộ sản phẩm laptop đang có trong giỏ hàng không?',
        confirmText: 'Đồng ý xóa',
        cancelText: 'Giữ lại',
        type: 'danger',
        onConfirm: function () {
            CartManager.xoaToanBoGio();
            TechZoneNotify.success('Đã làm trống giỏ hàng thành công!', 'Giỏ hàng');
        }
    });
}

function confirmRemoveItem(maSP) {
    const item = TECHZONE_CATALOG[maSP];
    const name = item ? item.tenSP : maSP;
    TechZoneNotify.confirm({
        title: 'Xóa Sản Phẩm Khỏi Giỏ',
        message: `Bạn có chắc muốn bỏ sản phẩm <strong>${name}</strong> khỏi giỏ hàng của bạn không?`,
        confirmText: 'Đồng ý xóa',
        cancelText: 'Giữ lại',
        type: 'warning',
        onConfirm: function () {
            CartManager.xoaSanPham(maSP);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renderCartPage();
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
