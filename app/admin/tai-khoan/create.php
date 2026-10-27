<?php
// app/admin/tai-khoan/create.php
$pageTitle = "Tạo tài khoản quản trị mới";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/sidebar.php';
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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tạo Admin</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 22px;">Thêm Mới Quản Trị Viên (Admin)</h2>
                <p class="text-secondary mb-0" style="font-size: 13.5px;">Tạo tài khoản phân quyền quản trị cao cấp cho nhân viên hệ thống TechZone.</p>
            </div>
            <a href="tai-khoan.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

        <form action="tai-khoan.php" method="POST">
            <input type="hidden" name="action" value="create">

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="tz-card p-4">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="bi bi-shield-lock-fill text-danger me-2"></i>Thông Tin Tài Khoản Quản Trị
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="hoTen" class="form-control" placeholder="VD: Nguyễn Đăng Tài" required>
                                </div>
                                <small class="text-muted">Họ tên đầy đủ của nhân sự phụ trách</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="VD: admin@techzone.vn" required>
                                </div>
                                <small class="text-muted">Dùng để nhận thông báo và khôi phục tài khoản</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-at"></i></span>
                                    <input type="text" name="tenTaiKhoan" class="form-control font-monospace" placeholder="VD: admin_nhanvien01" required>
                                </div>
                                <small class="text-muted">Chữ thường, không dấu, không khoảng trắng</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu ban đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                    <input type="password" name="matKhau" class="form-control" placeholder="Tối thiểu 8 ký tự..." required>
                                </div>
                                <small class="text-muted">Nên bao gồm chữ hoa, chữ thường và số</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò (Role)</label>
                                <input type="text" name="role" class="form-control bg-light text-danger fw-bold" value="admin" readonly>
                                <small class="text-danger"><i class="bi bi-info-circle me-1"></i>Trường vai trò cố định là "admin" cho màn hình này</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trạng thái kích hoạt</label>
                                <select name="trangThai" class="form-select">
                                    <option value="active" selected>active (Hoạt động ngay)</option>
                                    <option value="inactive">inactive (Tạm khóa)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="tai-khoan.php" class="btn btn-outline-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-tz-primary">
                                <i class="bi bi-check2-circle me-1"></i>Lưu & Tạo Tài Khoản Admin
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="tz-card p-4">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="bi bi-shield-check text-primary me-2"></i>Quyền Hạn Quản Trị
                        </h4>
                        <ul class="list-unstyled mb-0" style="font-size: 13.5px; line-height: 1.8;">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Toàn quyền quản lý Sản phẩm, Danh mục & Thương hiệu.</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Tạo và duyệt Phiếu nhập kho, gán mã Serial/IMEI.</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Tiếp nhận, gán serial và duyệt đơn bán hàng.</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Xuất và in Hóa đơn tài chính, tạo phiếu bảo hành.</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Cài đặt mã khuyến mãi và ưu đãi VIP khách hàng.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
</div>
