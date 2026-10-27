<?php
include __DIR__ . '/../layouts/header.php';

?>





<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý thương hiệu</h4>
            <div class="text-muted small">Quản lý danh sách các nhà sản xuất, thương hiệu đối tác cung ứng của TECHZONE.</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickBrandModal" onclick="openQuickBrandModal()">
                <i class="bi bi-lightning-charge me-1"></i> Thêm nhanh
            </button>
            <a href="create.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm thương hiệu mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Tổng thương hiệu</div>
                        <div class="stats-value">18</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-primary rounded">
                        <i class="bi bi-award-fill fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Đối tác chính hãng</div>
                        <div class="stats-value text-success">14</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-success rounded">
                        <i class="bi bi-patch-check-fill fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Đang kinh doanh</div>
                        <div class="stats-value text-primary">16</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-primary rounded">
                        <i class="bi bi-check-circle-fill fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title text-danger">Tạm ngưng</div>
                        <div class="stats-value text-danger">2</div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-danger rounded">
                        <i class="bi bi-pause-circle-fill fs-4 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card mb-4">
        <!-- Toolbar Bộ lọc gửi request GET -->
        <form action="index.php" method="GET" class="table-toolbar d-flex justify-content-between align-items-center">
            <div class="position-relative" style="width: 320px;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                <input type="text" name="search" class="form-control ps-5" placeholder="Tìm kiếm tên hãng, quốc gia...">
            </div>
            <div class="d-flex gap-2">
                <select name="country" class="form-select" style="width: 180px;">
                    <option value="">Tất cả quốc gia</option>
                    <option value="tw">Đài Loan</option>
                    <option value="us">Hoa Kỳ</option>
                    <option value="kr">Hàn Quốc</option>
                </select>
                <select name="status" class="form-select" style="width: 180px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1">Đang kinh doanh</option>
                    <option value="0">Tạm ngưng</option>
                </select>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table product-table mb-0 table-hover align-middle">
                <thead>
                    <tr>
                        <th width="80">LOGO</th>
                        <th>Mã</th>
                        <th>TÊN THƯƠNG HIỆU</th>
                        <th>Xuất Xứ</th>
                        <th>WEBSITE</th>
                        <th>PHÂN CẤP ĐỐI TÁC</th>
                        <th>TRẠNG THÁI</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
               <tbody>
                <?php 
                include __DIR__ . '/../../../config/config.php';

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Lỗi kết nối: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM brand";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row["id"] ?? "";
                        $name = $row["name"] ?? "";
                        $origin = $row["origin"] ?? "";
                        $code = $row["code"] ?? "";
                        $website = $row["website"] ?? "";
                        $description = $row["description"] ?? "";
                        $status = $row["status"] ?? 1;
                        $position = $row["position"] ?? 1;
                ?>
                    <tr>
                        <td>
                            <div class="p-1 rounded bg-light border text-center d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <!-- <span class="fw-bold text-primary" style="font-size: 0.9rem;"><img src="https://tse2.mm.bing.net/th/id/OIP.RCQiIGgdJg1Emf0nA_iERAHaHa?r=0&pid=Api&h=220&P=0" alt=""></span> -->
                                <span class="fw-bold text-primary" style="font-size: 0.9rem;"><img src="" alt=""></span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary"><?=$code ?></div>
                           
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?=$name ?></div>
                            <div class="text-muted small"><?=$description ?></div>
                        </td>
                        <td><span class="text-dark"><?=$origin ?></span></td>
                        <td>
                            <a href="<?=$website ?>" target="_blank" class="text-primary text-decoration-none small">
                                <i class="bi bi-link-45deg"></i> <?=$website ?>
                            </a>
                        </td>
                        <td><span class="fw-semibold"><?=$position ?></span></td>
                        <td>
                            <?php if ($status == 1): ?>
                                <span class="badge bg-success">Đang kinh doanh</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tạm ngưng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="edit.php?id=<?= $id ?>" class="action-btn" title="Chỉnh sửa"><i class="bi bi-pencil-square"></i></a>
                                <a href="delete.php?id=<?= $id ?>" class="action-btn delete border-0 bg-transparent" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?');"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php 
                    } 
                } else {
                    echo '<tr><td colspan="7" class="text-center py-4 text-muted">Chưa có thương hiệu nào.</td></tr>';
                } 
                ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- Quick Add Brand Modal (Frontend Only) -->
<div class="modal" id="quickBrandModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <form action="create.php" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-award-fill text-primary me-2"></i> Thêm nhanh thương hiệu</h5>
                <button type="button" class="btn-close" onclick="closeQuickBrandModal()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Tên thương hiệu <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên hãng (VD: Corsair, Razer...)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Quốc gia / Xuất xứ</label>
                    <select name="country" class="form-select">
                        <option value="">-- Chọn quốc gia --</option>
                        <option value="Hoa Kỳ">🇺🇸 Hoa Kỳ</option>
                        <option value="Đài Loan">🇹🇼 Đài Loan</option>
                        <option value="Hàn Quốc">🇰🇷 Hàn Quốc</option>
                        <option value="Nhật Bản">🇯🇵 Nhật Bản</option>
                        <option value="Việt Nam">🇻🇳 Việt Nam</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Website chính thức</label>
                    <input type="url" name="website" class="form-control" placeholder="https://...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Trạng thái hợp tác</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang kinh doanh</option>
                        <option value="0">Tạm ngưng</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light rounded-pill px-4 border" onclick="closeQuickBrandModal()">Đóng</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-check-lg me-1"></i> Lưu lại
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 
<script>
function openQuickBrandModal() {
    document.getElementById('quickBrandModal').style.display = 'block';
}
function closeQuickBrandModal() {
    document.getElementById('quickBrandModal').style.display = 'none';
}
</script>
 -->

<?php
include __DIR__ . '/../layouts/footer.php';
?>
