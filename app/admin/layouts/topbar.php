<?php
// app/views/admin/layouts/topbar.php
?>
<!-- Topbar Header -->
<header class="tz-topbar">
    <!-- Breadcrumb -->
    <div class="d-flex align-items-center gap-2">
        <span class="text-secondary fw-medium">Quản trị</span>
        <i class="bi bi-chevron-right text-muted" style="font-size: 11px;"></i>
        <span class="text-secondary">Hệ thống 2026</span>
        <?php if (!empty($pageTitle)): ?>
            <i class="bi bi-chevron-right text-muted" style="font-size: 11px;"></i>
            <span class="text-primary fw-semibold"><?= htmlspecialchars($pageTitle) ?></span>
        <?php endif; ?>
    </div>

    <!-- Actions & User Profile -->
    <div class="d-flex align-items-center gap-3">
        <!-- Chuyển đổi Dark/Light mode -->
        <button class="btn btn-sm btn-outline-light text-secondary border-0 p-2 rounded-circle" title="Chế độ giao diện" id="themeToggleBtn">
            <i class="bi bi-moon-stars" style="font-size: 18px;"></i>
        </button>

        <!-- Chuông thông báo -->
        <div class="position-relative">
            <button class="btn btn-sm btn-outline-light text-secondary border-0 p-2 rounded-circle" title="Thông báo hệ thống">
                <i class="bi bi-bell" style="font-size: 18px;"></i>
                <span class="position-absolute top-1 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
            </button>
        </div>

        <div class="vr my-2" style="height: 24px; color: #cbd5e1;"></div>

        <!-- Thông tin Quản trị viên -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                    <div class="fw-bold text-dark" style="font-size: 13px;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Quản Trị Viên') ?></div>
                    <small class="text-muted" style="font-size: 11px;"><?= htmlspecialchars(strtoupper($_SESSION['role'] ?? 'ADMIN')) ?></small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="border-radius: 10px;">
                <li><a class="dropdown-item py-2" href="../tai-khoan/tai-khoan.php"><i class="bi bi-person me-2"></i>Hồ sơ & Tài khoản</a></li>
                <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalDoiMatKhau"><i class="bi bi-key me-2"></i>Đổi mật khẩu</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger" href="/computer-shop/app/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- Modal Đổi Mật Khẩu -->
<div class="modal fade" id="modalDoiMatKhau" tabindex="-1" aria-labelledby="modalDoiMatKhauLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalDoiMatKhauLabel">
                    <i class="bi bi-shield-lock-fill text-primary me-2"></i>Đổi Mật Khẩu Quản Trị
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" onsubmit="event.preventDefault(); TechZoneNotify.success('Đã cập nhật mật khẩu mới thành công!', 'Bảo mật tài khoản'); bootstrap.Modal.getInstance(document.getElementById('modalDoiMatKhau')).hide();">
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">Để bảo vệ tài khoản quản trị, vui lòng đặt mật khẩu mạnh kết hợp chữ hoa, chữ thường và số:</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" name="matKhauCu" class="form-control" placeholder="Nhập mật khẩu cũ..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" name="matKhauMoi" class="form-control" placeholder="Tối thiểu 8 ký tự..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-check2-circle"></i></span>
                            <input type="password" name="xacNhanMatKhauMoi" class="form-control" placeholder="Nhập lại mật khẩu mới..." required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-tz-primary"><i class="bi bi-check2 me-1"></i>Xác nhận đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>
