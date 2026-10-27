<?php 
include __DIR__ . '/../layouts/header.php';
include __DIR__ . "/../../../config/config.php";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sqlProducts = "SELECT COUNT(*) AS total FROM products WHERE is_deleted = '0'";
    $resultProducts = $conn->query($sqlProducts);
    if ($resultProducts->num_rows > 0) {
                  while ($row = $resultProducts->fetch_assoc()) {
                    $totalProducts = $row["total"];
                  }
                  $sqlProductsActive = "SELECT COUNT(*) AS total FROM products WHERE is_deleted = '0' AND status = '1'";
                  $resultProductsActive = $conn->query($sqlProductsActive);
                  if ($resultProductsActive->num_rows > 0) {
                    while ($row = $resultProductsActive->fetch_assoc()) {
                      $totalProductsActive = $row["total"];
                    }
                  }
                  $sqlLowStock = "SELECT COUNT(*) AS total FROM products WHERE is_deleted = '0' AND stock <= 5";
                  $resultLowStock = $conn->query($sqlLowStock);
                  if ($resultLowStock->num_rows > 0) {
                    while ($row = $resultLowStock->fetch_assoc()) {
                      $totalLowStock = $row["total"];
                    }
                  }
                  $sqlHetHang = "SELECT COUNT(*) AS total FROM products WHERE is_deleted = '0' AND stock = 0";
                  $resultHetHang = $conn->query($sqlHetHang);
                  if ($resultHetHang->num_rows > 0) {
                    while ($row = $resultHetHang->fetch_assoc()) {
                      $totalHetHang = $row["total"];
                    }
                  }
    }

?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý sản phẩm</h4>
            <div class="text-muted small">Quản lý toàn bộ thông tin sản phẩm, hiển thị phân luồng kinh doanh TECHZONE.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="/computer-shop/resources/admin/categories/create.php" class="btn btn-light border">
                <i class="bi bi-diagram-3 me-1"></i> Thêm danh mục
            </a>
            <a href="/computer-shop/resources/admin/brands/create.php" class="btn btn-light border">
                <i class="bi bi-award me-1"></i> Thêm thương hiệu
            </a>
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Tổng sản phẩm</div>
                        <div class="stats-value"><?php echo $totalProducts; ?></div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-primary rounded">
                        <i class="bi bi-box-seam text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Đang kinh doanh</div>
                        <div class="stats-value text-primary"><?php echo $totalProductsActive; ?></div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-primary rounded">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title">Sắp hết hàng (< 5 chiếc)</div>
                        <div class="stats-value text-warning"><?php echo $totalLowStock; ?></div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-warning rounded">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-title text-danger">Hết hàng tồn</div>
                        <div class="stats-value text-danger"><?php echo $totalHetHang; ?></div>
                    </div>
                    <div class="p-2 bg-opacity-10 text-danger rounded">
                       <i class="bi bi-cart-x-fill text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form action="" method="GET" class="filter-section">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="filter-label">Tìm kiếm</div>
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                    <input type="text" name="search" class="form-control ps-5" id="searchProduct" placeholder="Tên sản phẩm, SKU...">
                </div>
            </div>
            <div class="col-md-2">
                <div class="filter-label">Danh mục</div>
                <select name="category" class="form-select" id="categoryProduct">
                    <option value="">Tất cả danh mục</option>
                    <option value="1">Laptop Gaming</option>
                    <option value="2">PC Đồng bộ</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="filter-label">Thương hiệu</div>
                <select name="brand" class="form-select" id="brandProduct">
                    <option value="">Tất cả thương hiệu</option>
                    <option value="asus">ASUS</option>
                    <option value="lg">LG</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="filter-label">Trạng thái bán</div>
                <select name="status" class="form-select" id="statusProduct">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1">Đang kinh doanh</option>
                    <option value="0">Ngừng kinh doanh</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
                <button type="submit" class="btn btn-primary w-100" id="filterBtn"><i class="bi bi-funnel"></i> Lọc Ngay</button>
            </div>
        </div>
    </form>

    <!-- Table Card -->
    <div class="table-card mb-4">
        <div class="table-toolbar d-flex justify-content-between align-items-center">
            <!-- <div class="d-flex align-items-center">
                <div class="form-check me-3 mb-0">
                    <input class="form-check-input" type="checkbox" id="checkAll" checked>
                </div>
                <span class="fw-semibold text-primary"><i class="bi bi-check-square-fill me-1"></i> Đã chọn 2 sản phẩm đang kinh doanh</span>
            </div> -->
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check2-circle"></i> Kích hoạt bán</button>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pause-circle"></i> Tạm ngừng</button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i> Xóa sản phẩm</button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table product-table mb-0 table-hover align-middle">
                <thead>
                    <tr>
                   
                        <th width="60">ẢNH</th>
                        <th>MÃ SKU</th>
                        <th width="250">SẢN PHẨM & CẤU HÌNH</th>
                        <th>DANH MỤC</th>
                        <th>THƯƠNG HIỆU</th>
                        <th>GIÁ BÁN / GIÁ GỐC</th>
                        <th>TỒN KHO</th>
                        <th>ĐÃ BÁN</th>
                        <th>TRẠNG THÁI</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>

                <tbody id="productTable">
                <?php 
                    $search = $_GET["search"] ?? "";
                    $category = $_GET["category"] ?? "";
                    $brand = $_GET["brand"] ?? "";
                    $status = $_GET["status"] ?? "";
                    $sqlsearch = "SELECT * FROM products WHERE is_deleted='0'";
                    if($search!=""){
                        $sqlsearch .=" AND name LIKE '%$search%'";
                    }
                    
                    if($category!=""){
                        $sqlsearch .=" AND category = '$category'";
                    }

                    if($brand!=""){
                        $sqlsearch .=" AND brand = '$brand'";
                    }

                    if($status!=""){
                        $sqlsearch .=" AND status = '$status'";
                    }
                    $result = $conn->query($sqlsearch);
                    if($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $id = $row["id"];
                            $name = $row["name"];
                            $sku = $row["sku"];
                            $description = $row["description"];
                            $price = $row["price"];
                            $stock = $row["stock"];
                            $status = $row["status"];
                            $brand = $row["brand"];
                            // $statusText = $status == 1 ? "Đang kinh doanh" : "Ngừng kinh doanh";
                           echo "<tr>";
                        //    echo "<td>
                        //     <div class='form-check'>
                        //         <input class='form-check-input' type='checkbox' checked>
                        //     </div>
                        //     </td>";
                           echo "<td>
                            <img src='https://via.placeholder.com/40' alt='Product' class='product-img'>
                           </td>";
                           echo "<td>
                            <div class='fw-medium'>" . $sku . "</div>
                            <div class='text-muted small'>Kho TP HCM</div>
                           </td>";
                           echo "<td>
                            <div class='fw-semibold text-primary cursor-pointer'>" . $name . "</div>
                            <div class='text-muted' style='font-size: 0.75rem;'>" . $description . "</div>
                           </td>";
                           echo "<td>
                          
                            <div class='text-muted small'>Danh mục</div>
                           </td>";
                           echo "<td>
                            <div class='fw-medium'>" . $brand . "</div>
                            <div class='text-muted small'>Thương hiệu</div>
                           </td>";
                           echo "<td>
                            <div class='fw-bold text-danger'>" . $price . " ₫</div>
                            <div class='text-muted small text-decoration-line-through'>" . $price . " ₫</div>
                           </td>";
                           echo "<td>
                            <span class='fw-medium'>" . $stock . " chiếc</span>
                            <div class='text-success small'>Có sẵn</div>
                           </td>";
                           echo "<td>
                            <div class='text-muted small'>Đã bán</div>
                           </td>";
                           if($status == 1) {
                            $statusText = "Đang kinh doanh";
                            $statusColor = "success";
                           } else {
                            $statusText = "Ngừng kinh doanh";
                            $statusColor = "danger";
                           }
                           echo "<td>
                            <span class='badge bg-" . $statusColor . "'>" . $statusText . "</span>
                           </td>";


                           echo " <td>
                            <div class='d-flex gap-1'>
                                <button class='action-btn' title='Chỉnh sửa'>
                                <a href='edit.php?id=$id'> 
                                <i class='bi bi-pencil-square'></i>
                                </a>
                                </button>
                                <button class='action-btn delete' title='Xóa'>
                                <a href='deletesoft.php?id=$id'> 
                                <i class='bi bi-trash'></i>
                                </a>
                                </button>
                            </div>
                        </td>";

                           echo "</tr>";

                        }
                    
                    }
                    ?>


                  
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <!-- <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <div class="text-muted small">Hiển thị 1 đến 10 trong tổng số 856 sản phẩm</div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo; Trước</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">...</a></li>
                    <li class="page-item"><a class="page-link" href="#">86</a></li>
                    <li class="page-item"><a class="page-link" href="#">Sau &raquo;</a></li>
                </ul>
            </nav>
        </div> -->
    </div>
</div>

</div>
<?php 
include __DIR__ . '/../layouts/footer.php';
?>
<script src="/computer-shop/resources/js/filterBtn.js"></script>

