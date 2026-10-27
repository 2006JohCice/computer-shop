/**
 * ==============================================================================
 * TECHZONE CLIENT JAVASCRIPT ENGINE
 * Quản lý tương tác Client Storefront:
 * - Giỏ hàng lưu trữ LocalStorage đồng bộ theo thời gian thực
 * - Thêm, tăng giảm, xóa sản phẩm, tính tổng tiền giỏ hàng
 * - Áp dụng Voucher khuyến mãi và giảm giá tự động
 * - Đặt hàng & Xác nhận Thanh toán Online mã QR (VNPAY / MOMO / Chuyển khoản)
 * - Tra cứu đơn hàng & Yêu cầu hủy đơn hàng
 * ==============================================================================
 */

// Dữ liệu catalogue sản phẩm tĩnh để map thông tin giỏ hàng
const TECHZONE_CATALOG = {
    'SP01': {
        maSP: 'SP01',
        tenSP: 'Dell XPS 13 9320 Plus',
        giaBan: 35990000,
        img: 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80',
        thongSo: 'Core i7-1360P • 16GB RAM • 512GB SSD • OLED 3.5K',
        thuongHieu: 'DELL'
    },
    'SP02': {
        maSP: 'SP02',
        tenSP: 'ASUS ROG Strix G16 G614',
        giaBan: 42990000,
        img: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&auto=format&fit=crop&q=80',
        thongSo: 'Core i9-13980HX • 32GB RAM • RTX 4070 8GB • 240Hz',
        thuongHieu: 'ASUS'
    },
    'SP03': {
        maSP: 'SP03',
        tenSP: 'Apple MacBook Pro 14 M3 Pro',
        giaBan: 49990000,
        img: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80',
        thongSo: 'M3 Pro 11-Core • 18GB RAM • 512GB SSD • Liquid Retina XDR',
        thuongHieu: 'APPLE'
    },
    'SP04': {
        maSP: 'SP04',
        tenSP: 'Lenovo ThinkPad X1 Carbon Gen 11',
        giaBan: 38500000,
        img: 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=80',
        thongSo: 'Core i7-1365U • 16GB RAM • 512GB SSD • Siêu nhẹ 1.12kg',
        thuongHieu: 'LENOVO'
    }
};

// Dữ liệu mã voucher mẫu
const TECHZONE_VOUCHERS = {
    'BACK2SCHOOL': { giam: 10, loai: 'percent', max: 2000000 },
    'TECHZONE500K': { giam: 500000, loai: 'fixed', max: 500000 },
    'KM_CHAOMUNG': { giam: 5, loai: 'percent', max: 1800000 }
};

// TECHZONE GIỎ HÀNG QUẢN LÝ
const CartManager = {
    getCart: function () {
        const raw = localStorage.getItem('tz_cart');
        return raw ? JSON.parse(raw) : { 'SP01': 1 }; // Mặc định 1 sản phẩm demo
    },

    saveCart: function (cart) {
        localStorage.setItem('tz_cart', JSON.stringify(cart));
        this.updateHeaderBadge();
    },

    themSanPham: function (maSP, soLuong = 1, triggerBtn = null) {
        // Tự động phát hiện nút kích hoạt nếu không truyền trực tiếp
        if (!triggerBtn && window.event) {
            triggerBtn = window.event.currentTarget || (window.event.target ? window.event.target.closest('button') : null);
        }

        // CHỐNG SPAM: Ngăn click liên tục khi đang xử lý
        if (triggerBtn && (triggerBtn.classList.contains('is-loading') || triggerBtn.disabled)) {
            return;
        }

        let originalContent = '';
        let isRoundBtn = false;
        let isBigBtn = false;

        if (triggerBtn) {
            triggerBtn.classList.add('is-loading');
            triggerBtn.disabled = true;
            originalContent = triggerBtn.innerHTML;
            isRoundBtn = triggerBtn.classList.contains('btn-tz-add-cart');
            isBigBtn = triggerBtn.classList.contains('btn-sp-add-cart') || triggerBtn.classList.contains('btn-sp-buy-now');

            // Hiệu ứng icon xoay xoay (Spinner)
            if (isRoundBtn) {
                triggerBtn.innerHTML = '<span class="tz-btn-spinner"></span>';
            } else if (isBigBtn) {
                triggerBtn.innerHTML = '<span class="tz-btn-spinner me-2"></span><span>Đang thêm vào giỏ...</span>';
            }
        }

        // Giữ hiệu ứng xoay 550ms tạo trải nghiệm phản hồi mượt mà và chống spam click
        setTimeout(() => {
            const cart = this.getCart();
            cart[maSP] = (cart[maSP] || 0) + soLuong;
            this.saveCart(cart);

            const itemName = TECHZONE_CATALOG[maSP]?.tenSP || maSP;
            if (window.TechZoneNotify) {
                TechZoneNotify.success(`Đã thêm <strong>${itemName}</strong> vào giỏ hàng thành công!`, 'Giỏ Hàng TechZone');
            }

            // Chuyển sang trạng thái thành công (Checkmark xanh)
            if (triggerBtn) {
                triggerBtn.classList.remove('is-loading');
                triggerBtn.classList.add('is-success');

                if (isRoundBtn) {
                    triggerBtn.innerHTML = '<i class="bi bi-check-lg" style="color: #ffffff; font-size: 19px;"></i>';
                } else if (isBigBtn) {
                    triggerBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i><span>ĐÃ THÊM THÀNH CÔNG!</span>';
                }

                // Trả về nút ban đầu và mở khóa sau 600ms
                setTimeout(() => {
                    triggerBtn.classList.remove('is-success');
                    triggerBtn.innerHTML = originalContent;
                    triggerBtn.disabled = false;
                }, 650);
            }
        }, 550);
    },

    capNhatSoLuong: function (maSP, soLuongMoi) {
        const cart = this.getCart();
        if (soLuongMoi <= 0) {
            delete cart[maSP];
            if (window.TechZoneNotify) {
                TechZoneNotify.info('Đã xóa sản phẩm khỏi giỏ hàng.', 'Giỏ hàng');
            }
        } else {
            cart[maSP] = soLuongMoi;
        }
        this.saveCart(cart);
        if (typeof renderCartPage === 'function') renderCartPage();
        if (typeof renderCheckoutPage === 'function') renderCheckoutPage();
    },

    xoaSanPham: function (maSP) {
        const cart = this.getCart();
        delete cart[maSP];
        this.saveCart(cart);
        if (window.TechZoneNotify) {
            TechZoneNotify.info('Đã xóa sản phẩm khỏi giỏ hàng.', 'Giỏ hàng');
        }
        if (typeof renderCartPage === 'function') renderCartPage();
        if (typeof renderCheckoutPage === 'function') renderCheckoutPage();
    },

    xoaToanBoGio: function () {
        this.saveCart({});
        if (typeof renderCartPage === 'function') renderCartPage();
        if (typeof renderCheckoutPage === 'function') renderCheckoutPage();
    },

    tinhTongTien: function () {
        const cart = this.getCart();
        let total = 0;
        for (const [maSP, qty] of Object.entries(cart)) {
            const item = TECHZONE_CATALOG[maSP];
            if (item) {
                total += item.giaBan * qty;
            }
        }
        return total;
    },

    updateHeaderBadge: function () {
        const cart = this.getCart();
        let count = 0;
        for (const qty of Object.values(cart)) count += qty;
        const badges = document.querySelectorAll('.cart-badge-count');
        badges.forEach(b => b.textContent = count);
    },

    showToast: function (htmlMsg) {
        if (window.TechZoneNotify) {
            TechZoneNotify.success(htmlMsg, 'Giỏ hàng');
        }
    }
};

// Khởi tạo badge ngay khi tải trang
document.addEventListener('DOMContentLoaded', () => {
    CartManager.updateHeaderBadge();
});
