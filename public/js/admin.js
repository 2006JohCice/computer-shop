/**
 * ==============================================================================
 * HỆ THỐNG QUẢN LÝ CỬA HÀNG BÁN LAPTOP (TECHZONE)
 * FILE: public/js/admin.js
 * MÔ TẢ: Tập lệnh tương tác giao diện Admin tách riêng
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. CHỌN TẤT CẢ CHECKBOX TRONG BẢNG (SELECT ALL)
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.tz-table tbody input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // 2. TOGGLE DARK / LIGHT MODE
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (icon.classList.contains('bi-moon-stars')) {
                icon.classList.replace('bi-moon-stars', 'bi-sun-fill');
                document.body.classList.add('dark-mode');
            } else {
                icon.classList.replace('bi-sun-fill', 'bi-moon-stars');
                document.body.classList.remove('dark-mode');
            }
        });
    }

    // 3. XÁC NHẬN HÀNH ĐỘNG XÓA (CUSTOM CONFIRM DIALOG)
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm-delete') || 'Bạn có chắc chắn muốn xóa bản ghi này?';
            const targetHref = this.getAttribute('href');
            const targetForm = this.closest('form');

            if (window.TechZoneNotify) {
                TechZoneNotify.confirm({
                    title: 'Xác nhận xóa',
                    message: message,
                    type: 'danger',
                    confirmText: 'Đồng ý xóa',
                    cancelText: 'Hủy bỏ',
                    onConfirm: function () {
                        if (targetForm) targetForm.submit();
                        else if (targetHref && targetHref !== '#') window.location.href = targetHref;
                    }
                });
            } else if (confirm(message)) {
                if (targetForm) targetForm.submit();
                else if (targetHref && targetHref !== '#') window.location.href = targetHref;
            }
        });
    });

    // 4. PREVIEW ẢNH KHI CHỌN FILE
    const imageInput = document.getElementById('imageUpload');
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const previewContainer = document.querySelector('.image-preview-box');
                if (previewContainer) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewContainer.innerHTML = `<img src="${event.target.result}" class="img-fluid rounded-3" style="max-height: 180px; object-fit: contain;">`;
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }

    // 5. AUTO-DISMISS ALERT SAU 5 GIÂY (NẾU CÓ)
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});
