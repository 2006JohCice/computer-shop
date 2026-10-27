<?php
// app/admin/tai-khoan/edit.php
$pageTitle = "Chỉnh sửa tài khoản";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';

$maTk = htmlspecialchars($_GET['id'] ?? 'TK01');
?>

<div class="tz-main-wrapper">
    <?php require_once dirname(__DIR__) . '/layouts/topbar.php'; ?>

    <main class="tz-content-area">
        <!-- Header điều hướng & Nút quay lại -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-muted">Quản trị</a></li>
                        <li class="breadcrumb-item"><a href="tai-khoan.php" class="text-decoration-none text-muted">Tài khoản</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Cập Nhật Tài Khoản (<?= $maTk ?>)</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Chỉnh sửa thông tin cá nhân, vai trò phân quyền và trạng thái kích hoạt tài khoản.</p>
            </div>
            <a href="tai-khoan.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

        <form action="tai-khoan.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="maTk" value="<?= $maTk ?>">

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="tz-card p-4">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Thông Tin Chi Tiết
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mã tài khoản (maTk)</label>
                                <input type="text" class="form-control bg-light font-monospace" value="<?= $maTk ?>" readonly>
                                <small class="text-muted">Mã định danh duy nhất trong CSDL (Khóa chính)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên đăng nhập (Username)</label>
                                <input type="text" class="form-control bg-light font-monospace" value="admin_tai" readonly>
                                <small class="text-muted">Tên đăng nhập không thể thay đổi sau khi tạo</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="hoTen" class="form-control" value="Nguyễn Đăng Tài" required>
                                <small class="text-muted">Họ và tên hiển thị trên hệ thống</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="admin.tai@techzone.vn" required>
                                <small class="text-muted">Email liên lạc chính thức</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò (Role) <span class="text-danger">*</span></label>
                                <select name="role" class="form-select">
                                    <option value="admin" selected>admin (Quản trị viên toàn quyền)</option>
                                    <option value="user">user (Khách hàng thông thường)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trạng thái (trangThai) <span class="text-danger">*</span></label>
                                <select name="trangThai" class="form-select">
                                    <option value="active" selected>active (Đang hoạt động)</option>
                                    <option value="inactive">inactive (Khóa đăng nhập)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="tai-khoan.php" class="btn btn-outline-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-tz-primary">
                                <i class="bi bi-save me-1"></i>Lưu Thay Đổi
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="tz-card p-4">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="bi bi-shield-lock text-warning me-2"></i>Đặt Lại Mật Khẩu
                        </h4>
                        <p class="text-secondary small mb-3">Quản trị viên cấp cao có thể cấp phát mật khẩu mới cho tài khoản này nếu được yêu cầu.</p>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mật khẩu mới</label>
                            <input type="password" class="form-control form-control-sm" placeholder="Nhập mật khẩu mới...">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-warning w-100" onclick="TechZoneNotify.success('Đã tạo liên kết đặt lại mật khẩu và gửi về email quản trị thành công!', 'Bảo mật tài khoản');">
                            <i class="bi bi-key me-1"></i>Gửi liên kết đổi mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
