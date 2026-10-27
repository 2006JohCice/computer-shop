/**
 * TECHZONE GLOBAL NOTIFICATION & CONFIRMATION SYSTEM
 * Phiên bản UI/UX Pro Max - Không phụ thuộc backend, tương tác mượt mà.
 */

const TechZoneNotify = (function () {
    let toastContainer = null;
    let confirmModalEl = null;
    let confirmModalInstance = null;
    let pendingConfirmCallback = null;
    let pendingCancelCallback = null;

    // Khởi tạo container khi DOM sẵn sàng
    function init() {
        if (!toastContainer) {
            toastContainer = document.querySelector('.tz-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'tz-toast-container';
                document.body.appendChild(toastContainer);
            }
        }
        initConfirmModal();
        bindAutoConfirmAttributes();
    }

    // Modal xác nhận tùy biến
    function initConfirmModal() {
        if (confirmModalEl) return;

        const modalHtml = `
            <div class="modal fade tz-confirm-modal" id="tzGlobalConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                    <div class="modal-content text-center p-3">
                        <div class="modal-body pt-4 pb-2 px-3">
                            <div id="tzConfirmBadge" class="tz-modal-badge tz-badge-danger mx-auto">
                                <i id="tzConfirmIcon" class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <h4 id="tzConfirmTitle" class="fw-bold text-dark mb-2" style="font-size: 19px;">Xác nhận hành động</h4>
                            <p id="tzConfirmMessage" class="text-secondary small mb-3" style="line-height: 1.6; font-size: 14px;">Bạn có chắc chắn muốn thực hiện thao tác này?</p>
                        </div>
                        <div class="modal-footer justify-content-center gap-2 border-0 pt-0 pb-3">
                            <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary" id="tzConfirmBtnCancel" data-bs-dismiss="modal">
                                Hủy bỏ
                            </button>
                            <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold shadow-sm" id="tzConfirmBtnOk">
                                Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        confirmModalEl = document.getElementById('tzGlobalConfirmModal');
        
        if (window.bootstrap && window.bootstrap.Modal) {
            confirmModalInstance = new bootstrap.Modal(confirmModalEl);
        }

        const btnOk = document.getElementById('tzConfirmBtnOk');
        const btnCancel = document.getElementById('tzConfirmBtnCancel');

        btnOk.addEventListener('click', function () {
            if (confirmModalInstance) confirmModalInstance.hide();
            if (typeof pendingConfirmCallback === 'function') {
                const cb = pendingConfirmCallback;
                pendingConfirmCallback = null;
                cb();
            }
        });

        btnCancel.addEventListener('click', function () {
            if (typeof pendingCancelCallback === 'function') {
                const cb = pendingCancelCallback;
                pendingCancelCallback = null;
                cb();
            }
        });
    }

    // Hiển thị Toast thông báo
    function showToast(options) {
        init();
        const config = Object.assign({
            type: 'success', // success, danger, warning, info
            title: '',
            message: '',
            duration: 3500
        }, typeof options === 'string' ? { message: options } : options);

        const iconMap = {
            success: 'bi-check-circle-fill',
            danger: 'bi-exclamation-octagon-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const defaultTitleMap = {
            success: 'Thành công',
            danger: 'Thông báo lỗi',
            warning: 'Cảnh báo',
            info: 'Thông tin hệ thống'
        };

        const iconClass = iconMap[config.type] || iconMap.info;
        const titleText = config.title || defaultTitleMap[config.type] || 'Thông báo';

        const toastEl = document.createElement('div');
        toastEl.className = `tz-toast tz-toast-${config.type}`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="tz-toast-icon-wrap">
                <i class="bi ${iconClass}"></i>
            </div>
            <div class="tz-toast-content">
                <div class="tz-toast-title">${titleText}</div>
                <div class="tz-toast-message">${config.message}</div>
            </div>
            <button type="button" class="tz-toast-close" aria-label="Đóng">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="tz-toast-progress">
                <div class="tz-toast-progress-bar"></div>
            </div>
        `;

        // Gán thời gian chạy cho viền màu (Mặc định 3800ms)
        const duration = config.duration || 3800;
        toastEl.style.setProperty('--tz-toast-duration', `${duration}ms`);

        toastContainer.appendChild(toastEl);

        // Kích hoạt animation xuất hiện và viền màu chạy
        requestAnimationFrame(() => {
            toastEl.classList.add('show');
        });

        const pBar = toastEl.querySelector('.tz-toast-progress-bar');

        // VIỀN MÀU CHẠY: Chỉ khi viền màu chạy xong 100% thời lượng thì mới đóng thông báo
        let isClosed = false;
        const triggerClose = () => {
            if (isClosed) return;
            isClosed = true;
            closeToast(toastEl);
        };

        if (pBar) {
            pBar.addEventListener('animationend', (e) => {
                if (e.animationName === 'tzProgressRun') {
                    triggerClose();
                }
            });
        }

        // Dự phòng timer an toàn trong trường hợp animation bị can thiệp
        let fallbackTimer = setTimeout(triggerClose, duration + 200);

        // Nút tắt thủ công (x)
        const btnClose = toastEl.querySelector('.tz-toast-close');
        if (btnClose) {
            btnClose.addEventListener('click', () => {
                clearTimeout(fallbackTimer);
                triggerClose();
            });
        }

        return toastEl;
    }

    function closeToast(toastEl) {
        if (!toastEl) return;
        toastEl.classList.remove('show');
        toastEl.classList.add('hide');
        setTimeout(() => {
            if (toastEl.parentElement) {
                toastEl.parentElement.removeChild(toastEl);
            }
        }, 360);
    }

    // Modal Xác Nhận (Thay thế hoàn toàn window.confirm mặc định của trình duyệt)
    function showConfirm(options) {
        init();
        if (!confirmModalInstance) {
            confirmModalInstance = new bootstrap.Modal(confirmModalEl);
        }

        const config = Object.assign({
            title: 'Xác nhận hành động',
            message: 'Bạn có chắc chắn muốn thực hiện thao tác này?',
            confirmText: 'Xác nhận',
            cancelText: 'Hủy bỏ',
            type: 'danger', // danger, warning, primary, success
            onConfirm: null,
            onCancel: null
        }, options);

        pendingConfirmCallback = config.onConfirm;
        pendingCancelCallback = config.onCancel;

        const titleEl = document.getElementById('tzConfirmTitle');
        const msgEl = document.getElementById('tzConfirmMessage');
        const badgeEl = document.getElementById('tzConfirmBadge');
        const iconEl = document.getElementById('tzConfirmIcon');
        const btnOk = document.getElementById('tzConfirmBtnOk');
        const btnCancel = document.getElementById('tzConfirmBtnCancel');

        titleEl.textContent = config.title;
        msgEl.innerHTML = config.message;
        btnOk.textContent = config.confirmText;
        btnCancel.textContent = config.cancelText;

        // Reset badge color & button style
        badgeEl.className = `tz-modal-badge tz-badge-${config.type} mx-auto`;
        btnOk.className = `btn px-4 py-2 rounded-3 fw-semibold shadow-sm ${
            config.type === 'danger' ? 'btn-danger' : 
            config.type === 'warning' ? 'btn-warning text-dark' : 
            config.type === 'success' ? 'btn-success' : 'btn-primary'
        }`;

        const iconMap = {
            danger: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            primary: 'bi-question-circle-fill',
            success: 'bi-check-circle-fill'
        };
        iconEl.className = `bi ${iconMap[config.type] || 'bi-question-circle-fill'}`;

        confirmModalInstance.show();
    }

    // Modal Thông Báo Đơn Giản (Thay thế window.alert)
    function showAlert(options) {
        init();
        const config = Object.assign({
            title: 'Thông báo',
            message: '',
            type: 'info',
            okText: 'Đã hiểu',
            onOk: null
        }, typeof options === 'string' ? { message: options } : options);

        showConfirm({
            title: config.title,
            message: config.message,
            confirmText: config.okText,
            cancelText: 'Đóng',
            type: config.type,
            onConfirm: config.onOk
        });
    }

    // Tự động gắn sự kiện xác nhận cho các phần tử có thuộc tính [data-tz-confirm]
    function bindAutoConfirmAttributes() {
        document.addEventListener('click', function (e) {
            const targetBtn = e.target.closest('[data-tz-confirm]');
            if (!targetBtn) return;

            const message = targetBtn.getAttribute('data-tz-confirm');
            const title = targetBtn.getAttribute('data-tz-title') || 'Xác nhận thao tác';
            const type = targetBtn.getAttribute('data-tz-type') || 'danger';
            const confirmText = targetBtn.getAttribute('data-tz-btn-text') || 'Đồng ý';

            e.preventDefault();
            e.stopPropagation();

            showConfirm({
                title: title,
                message: message,
                type: type,
                confirmText: confirmText,
                onConfirm: function () {
                    // Nếu là thẻ form submit
                    const form = targetBtn.closest('form');
                    if (form && targetBtn.type === 'submit') {
                        // Bỏ thuộc tính để không lặp vô hạn rồi submit
                        form.submit();
                    } else if (targetBtn.tagName.toLowerCase() === 'a' && targetBtn.href && targetBtn.href !== '#' && !targetBtn.href.startsWith('javascript:')) {
                        window.location.href = targetBtn.href;
                    } else if (typeof targetBtn.onclick === 'function') {
                        targetBtn.onclick();
                    }
                }
            });
        }, true);
    }

    // Lắng nghe DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        init: init,
        toast: showToast,
        success: (msg, title) => showToast({ type: 'success', message: msg, title: title || 'Thành công' }),
        error: (msg, title) => showToast({ type: 'danger', message: msg, title: title || 'Thông báo lỗi' }),
        danger: (msg, title) => showToast({ type: 'danger', message: msg, title: title || 'Thông báo lỗi' }),
        warning: (msg, title) => showToast({ type: 'warning', message: msg, title: title || 'Cảnh báo' }),
        info: (msg, title) => showToast({ type: 'info', message: msg, title: title || 'Thông tin' }),
        confirm: showConfirm,
        alert: showAlert
    };
})();

// Gắn toàn cục window
window.TechZoneNotify = TechZoneNotify;
