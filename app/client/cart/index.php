<?php
// app/views/client/cart/index.php
$pageTitle = "Giỏ Hàng Của Bạn";
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

include __DIR__ . "/../../config/database.php";
$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    $conn->set_charset("utf8mb4");
}



// 1. Xác định mã khách hàng từ Session đăng nhập:
//    $_SESSION['login'] = true;
//    $_SESSION['maTk'] = $acc['maTk'];
//    $_SESSION['user_name'] = $acc['hoTen'] ?? $user_input;
//    $_SESSION['role'] = $role;
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true && !empty($_SESSION['maTk']);
$sessionMaTk = $_SESSION['maTk'] ?? '';
$sessionUserName = $_SESSION['user_name'] ?? '';
$sessionRole = $_SESSION['role'] ?? '';

$activeMaKH = '';
$customerProfile = null;

if ($isLoggedIn) {
    // 1. Truy vấn bảng khachhang theo maTk từ session
    $maTkEsc = $conn->real_escape_string($sessionMaTk);
    $resKH = $conn->query("SELECT * FROM khachhang WHERE maTk = '$maTkEsc' LIMIT 1");
    if ($resKH && $rowKH = $resKH->fetch_assoc()) {
        $activeMaKH = $rowKH['maKH'];
        $customerProfile = $rowKH;
    } else {
        // Nếu maTk chưa lưu trong khachhang, tìm theo email trong accounts
        $resAcc = $conn->query("SELECT email, hoTen FROM accounts WHERE maTk = '$maTkEsc' LIMIT 1");
        if ($resAcc && $rAcc = $resAcc->fetch_assoc()) {
            $emailAcc = $conn->real_escape_string($rAcc['email'] ?? '');
            if (!empty($emailAcc)) {
                $resKHEmail = $conn->query("SELECT * FROM khachhang WHERE email = '$emailAcc' LIMIT 1");
                if ($resKHEmail && $rowKHEmail = $resKHEmail->fetch_assoc()) {
                    $activeMaKH = $rowKHEmail['maKH'];
                    $customerProfile = $rowKHEmail;
                    $conn->query("UPDATE khachhang SET maTk = '$maTkEsc' WHERE maKH = '$activeMaKH'");
                }
            }
        }
    }
}

// Nếu chưa đăng nhập hoặc chưa tìm thấy maKH theo session
if (empty($activeMaKH)) {
    $requestedKh = trim($_GET['kh'] ?? '');
    if (!empty($requestedKh)) {
        $activeMaKH = $requestedKh;
    } else {
        $activeMaKH = 'KH001';
    }
    $resDef = $conn->query("SELECT * FROM khachhang WHERE maKH = '$activeMaKH' LIMIT 1");
    if ($resDef && $rDef = $resDef->fetch_assoc()) {
        $customerProfile = $rDef;
    }
}

$activeCustomerName = $customerProfile['hoVaTen'] ?? ($sessionUserName ?: 'Khách hàng');

// 2. Lấy dữ liệu sản phẩm trong bảng giohang từ CSDL
$dbCartItems = [];
$cartSubtotal = 0;
$sqlCart = "SELECT * FROM giohang WHERE maKH = '$activeMaKH' ORDER BY ngayCapNhat DESC";
$resCart = $conn->query($sqlCart);

if ($resCart && $resCart->num_rows > 0) {
    while ($cartRow = $resCart->fetch_assoc()) {
        $spCode = $cartRow['maSP'];
        $qty = (int)$cartRow['soLuong'];

        // Lấy thông tin chi tiết sản phẩm từ bảng sanpham (Không JOIN phức tạp)
        $spSql = "SELECT * FROM sanpham WHERE maSP = '$spCode' AND isDeleted = 0";
        $spRes = $conn->query($spSql);
        if ($spRes && $spData = $spRes->fetch_assoc()) {
            // Lấy tên thương hiệu
            $thCode = $spData['maTH'] ?? '';
            $thName = 'TECHZONE';
            if (!empty($thCode)) {
                $thRes = $conn->query("SELECT tenTH FROM thuonghieu WHERE maTH = '$thCode'");
                if ($thRes && $thRow = $thRes->fetch_assoc()) {
                    $thName = $thRow['tenTH'];
                }
            }

            // Gán ảnh minh họa theo thương hiệu
            $upperBrand = strtoupper($thCode);
            $upperName = strtoupper($spData['tenSP']);
            $img = 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80';
            if (strpos($upperBrand, 'ASUS') !== false || strpos($upperName, 'ASUS') !== false || strpos($upperName, 'TUF') !== false) {
                $img = 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&auto=format&fit=crop&q=80';
            } elseif (strpos($upperBrand, 'APPLE') !== false || strpos($upperName, 'MACBOOK') !== false || strpos($upperName, 'APPLE') !== false) {
                $img = 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80';
            } elseif (strpos($upperBrand, 'LENOVO') !== false || strpos($upperName, 'THINKPAD') !== false || strpos($upperName, 'LOQ') !== false) {
                $img = 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=80';
            } elseif (strpos($upperBrand, 'DELL') !== false || strpos($upperName, 'DELL') !== false || strpos($upperName, 'INSPIRON') !== false || strpos($upperName, 'XPS') !== false) {
                $img = 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&auto=format&fit=crop&q=80';
            } elseif (strpos($upperBrand, 'MSI') !== false || strpos($upperName, 'MSI') !== false) {
                $img = 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500&auto=format&fit=crop&q=80';
            }

            $giaBan = (float)$spData['giaBan'];
            $soLuongTon = (int)($spData['soLuongTon'] ?? 0);
            $lineTotal = $giaBan * $qty;
            $cartSubtotal += $lineTotal;

            $dbCartItems[] = [
                'maGioHang' => $cartRow['maGioHang'],
                'maSP' => $spCode,
                'tenSP' => $spData['tenSP'],
                'thuongHieu' => $thName,
                'giaBan' => $giaBan,
                'soLuong' => $qty,
                'soLuongTon' => $soLuongTon,
                'lineTotal' => $lineTotal,
                'img' => $img,
                'thongSo' => $spData['thongSoKyThuat'] ?? '',
                'thoiGianBaoHanh' => $spData['thoiGianBaoHanh'] ?? 12
            ];
        }
    }
}
// Kiểm tra xem trong giỏ có sản phẩm nào hết hàng không
$hasOutOfStock = false;
foreach ($dbCartItems as $it) {
    if ($it['soLuongTon'] <= 0) {
        $hasOutOfStock = true;
        break;
    }
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3 text-muted" style="font-size: 13px;">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/client/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Giỏ hàng của bạn</li>
        </ol>
    </nav>

    <!-- Thông báo nếu chưa đăng nhập -->
    <?php if (!$isLoggedIn): ?>
        <div class="alert alert-info d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 border-info-subtle shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                <div style="font-size: 13.5px;">Bạn đang xem giỏ hàng ở chế độ khách vãng lai. <strong>Đăng nhập tài khoản</strong> để đồng bộ và lưu trữ giỏ hàng lâu dài!</div>
            </div>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-primary px-3 rounded-pill text-nowrap fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập ngay
            </a>
        </div>
    <?php endif; ?>

    <!-- Header & Thông tin Tài khoản / Giỏ Hàng -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 24px;">
                <i class="bi bi-cart3 text-primary me-2"></i>Giỏ Hàng Của Bạn
            </h2>
            <p class="text-secondary mb-0 small">
                <?php if ($isLoggedIn): ?>
                    Lưu trữ trong CSDL tài khoản: <strong class="text-primary"><?= htmlspecialchars($sessionUserName) ?></strong> (Mã KH: <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($activeMaKH) ?></span>).
                <?php else: ?>
                    Quản lý các sản phẩm laptop đang chọn (Khách mua: <strong class="text-primary"><?= htmlspecialchars($activeCustomerName) ?></strong>).
                <?php endif; ?>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <?php if ($isLoggedIn): ?>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill font-monospace" style="font-size: 13px;">
                    <i class="bi bi-person-check-fill me-1"></i><?= htmlspecialchars($sessionUserName) ?> (<?= htmlspecialchars($activeMaKH) ?>)
                </span>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 12.5px;">
                    <i class="bi bi-person-circle me-1"></i>Đăng nhập
                </a>
            <?php endif; ?>

            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmClearCart()">
                <i class="bi bi-trash3 me-1"></i>Xóa Sạch Giỏ Hàng
            </button>
        </div>
    </div>

    <div class="row g-4" id="cartContentArea">
        <?php if (empty($dbCartItems)): ?>
            <!-- Giỏ hàng trống -->
            <div class="col-12 text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted d-block mb-3"></i>
                <h4 class="text-secondary">Giỏ hàng của bạn đang trống</h4>
                <p class="text-muted small">Tài khoản <strong><?= htmlspecialchars($activeCustomerName) ?></strong> hiện chưa có sản phẩm laptop nào trong giỏ hàng CSDL.</p>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-tz-cta">
                        <i class="bi bi-laptop me-1"></i>Khám Phá Laptop Ngay
                    </a>
                </div>
            </div>
        <?php else: ?>
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
                                <?php foreach ($dbCartItems as $item): 
                                    $isOut = ($item['soLuongTon'] <= 0);
                                    $maxAllowed = max(1, $item['soLuongTon']);
                                ?>
                                    <tr id="row-<?= htmlspecialchars($item['maSP']) ?>" class="<?= $isOut ? 'table-danger-subtle' : '' ?>">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['img']) ?>" class="rounded border p-1" style="width: 60px; height: 60px; object-fit: contain; <?= $isOut ? 'filter: grayscale(80%);' : '' ?>">
                                                <div>
                                                    <a href="<?= BASE_URL ?>/client/product-detail.php?id=<?= urlencode($item['maSP']) ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                                        <?= htmlspecialchars($item['tenSP']) ?>
                                                    </a>
                                                    <div class="small text-muted font-monospace mt-1">
                                                        <?= htmlspecialchars($item['thuongHieu']) ?> • Mã: <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['maSP']) ?></span>
                                                        • Bảo hành: <?= (int)$item['thoiGianBaoHanh'] ?>T
                                                        <?php if ($isOut): ?>
                                                            <span class="badge bg-danger text-white ms-1"><i class="bi bi-x-circle me-1"></i>TẠM HẾT HÀNG</span>
                                                        <?php elseif ($item['soLuongTon'] <= 3): ?>
                                                            <span class="badge bg-warning text-dark ms-1">Kho chỉ còn: <?= $item['soLuongTon'] ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Kho còn: <?= $item['soLuongTon'] ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-secondary"><?= number_format($item['giaBan'], 0, ',', '.') ?> ₫</span>
                                        </td>
                                        <td>
                                            <?php if ($isOut): ?>
                                                <div class="text-center text-danger fw-semibold small">
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Hết hàng</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="input-group input-group-sm justify-content-center" style="max-width: 110px; margin: auto;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty('<?= $item['maSP'] ?>', <?= $item['soLuong'] - 1 ?>)">-</button>
                                                    <input type="number" class="form-control text-center" value="<?= $item['soLuong'] ?>" min="1" max="<?= $maxAllowed ?>" onchange="updateItemQty('<?= $item['maSP'] ?>', parseInt(this.value) || 1)">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty('<?= $item['maSP'] ?>', <?= $item['soLuong'] + 1 ?>)">+</button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($item['lineTotal'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" title="Bỏ sản phẩm khỏi giỏ" onclick="confirmRemoveItem('<?= $item['maSP'] ?>')">
                                                <i class="bi bi-trash3 fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/client/index.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Thêm laptop
                        </a>
                        <!-- <span class="text-success small"><i class="bi bi-shield-check me-1"></i>Đồng bộ trực tiếp với CSDL MySQL table: giohang</span> -->
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
                        <span class="fw-bold text-dark" id="subtotalDisplay"><?= number_format($cartSubtotal, 0, ',', '.') ?> ₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển dự kiến:</span>
                        <span class="text-success fw-semibold">Miễn phí giao hàng</span>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark fs-6">TỔNG THANH TOÁN:</span>
                            <span class="fw-bold text-danger fs-4" id="totalDisplay"><?= number_format($cartSubtotal, 0, ',', '.') ?> ₫</span>
                        </div>

                        <?php if ($hasOutOfStock): ?>
                            <button type="button" class="btn btn-secondary w-100 py-3 text-center justify-content-center disabled" style="opacity: 0.65; cursor: not-allowed;" disabled>
                                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i>Có Sản Phẩm Hết Hàng (Không Thể Đặt)
                            </button>
                            <div class="alert alert-danger py-2 px-3 small mt-2 mb-0 rounded-3 d-flex align-items-center gap-2">
                                <i class="bi bi-x-octagon-fill fs-5 text-danger flex-shrink-0"></i>
                                <div>Vui lòng xóa các sản phẩm <strong>Tạm hết hàng</strong> khỏi giỏ để có thể tiến hành đặt hàng!</div>
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/client/checkout.php?kh=<?= $activeMaKH ?>" class="btn btn-tz-cta w-100 py-3 text-center justify-content-center" id="btnCheckout">
                                <i class="bi bi-credit-card me-1"></i>Tiến Hành Đặt Hàng (Checkout)
                            </a>
                        <?php endif; ?>
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
        <?php endif; ?>
    </div>
</div>

<script>
    // Thiết lập biến khách hàng hiện tại cho toàn bộ script
    window.CURRENT_MAKH = '<?= $activeMaKH ?>';

    // Đồng bộ danh sách sản phẩm từ CSDL vào LocalStorage
    const dbCartMap = {};
    <?php foreach ($dbCartItems as $item): ?>
        dbCartMap['<?= $item['maSP'] ?>'] = <?= (int)$item['soLuong'] ?>;
    <?php endforeach; ?>
    localStorage.setItem('tz_cart', JSON.stringify(dbCartMap));
    if (typeof CartManager !== 'undefined') {
        CartManager.updateHeaderBadge();
    }

    function updateItemQty(maSP, newQty) {
        if (newQty <= 0) {
            confirmRemoveItem(maSP);
            return;
        }
        // Gửi yêu cầu cập nhật lên CSDL
        fetch(window.BASE_URL + '/client/api-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'update',
                    maKH: window.CURRENT_MAKH,
                    maSP: maSP,
                    soLuong: newQty
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    TechZoneNotify.error(data.message || 'Lỗi cập nhật giỏ hàng', 'Giỏ hàng');
                }
            })
            .catch(() => {
                location.reload();
            });
    }

    function confirmRemoveItem(maSP) {
        TechZoneNotify.confirm({
            title: 'Xóa Sản Phẩm Khỏi Giỏ',
            message: `Bạn có chắc muốn bỏ sản phẩm <strong>${maSP}</strong> khỏi giỏ hàng cơ sở dữ liệu không?`,
            confirmText: 'Đồng ý xóa',
            cancelText: 'Giữ lại',
            type: 'warning',
            onConfirm: function() {
                fetch(window.BASE_URL + '/client/api-cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'remove',
                            maKH: window.CURRENT_MAKH,
                            maSP: maSP
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        TechZoneNotify.success('Đã xóa sản phẩm khỏi giỏ hàng CSDL!', 'Giỏ hàng');
                        setTimeout(() => location.reload(), 500);
                    })
                    .catch(() => {
                        location.reload();
                    });
            }
        });
    }

    function confirmClearCart() {
        TechZoneNotify.confirm({
            title: 'Xóa Sạch Giỏ Hàng',
            message: `Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng của khách hàng <strong>${window.CURRENT_MAKH}</strong> trong CSDL không?`,
            confirmText: 'Đồng ý xóa',
            cancelText: 'Giữ lại',
            type: 'danger',
            onConfirm: function() {
                fetch(window.BASE_URL + '/client/api-cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'clear',
                            maKH: window.CURRENT_MAKH
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        localStorage.removeItem('tz_cart');
                        TechZoneNotify.success('Đã làm trống giỏ hàng thành công!', 'Giỏ hàng');
                        setTimeout(() => location.reload(), 500);
                    })
                    .catch(() => {
                        location.reload();
                    });
            }
        });
    }
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>