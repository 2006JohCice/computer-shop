<?php 
include __DIR__ . '/layouts/header.php';
?>

<!-- Custom CSS for Recycle Bin UI -->
<style>
    .recycle-bin-header {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
        border-radius: 1rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .recycle-bin-icon-bg {
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8rem;
        color: rgba(220, 53, 69, 0.05);
        pointer-events: none;
    }
    .table-deleted {
        background-color: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .table-deleted th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-deleted td {
        padding: 1rem;
        vertical-align: middle;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-deleted tbody tr {
        transition: all 0.2s ease;
    }
    .table-deleted tbody tr:hover {
        background-color: #f8fafc;
    }
    .product-img-deleted {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 0.5rem;
        filter: grayscale(100%);
        opacity: 0.7;
    }
    .action-btn-restore {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        background-color: #e0f2fe;
        color: #0284c7;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn-restore:hover {
        background-color: #bae6fd;
        color: #0369a1;
        transform: translateY(-1px);
    }
    .action-btn-destroy {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        background-color: #fee2e2;
        color: #dc2626;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn-destroy:hover {
        background-color: #fecaca;
        color: #b91c1c;
        transform: translateY(-1px);
    }
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
</style>

<div class="container-fluid max-w-7xl mx-auto py-4">


    <!-- Data Table -->
    <div class="table-deleted">
        <div class="table-responsive">
            <table class="table product-table mb-0 table-hover align-middle">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th width="60">ẢNH</th>
                        <th>MÃ SKU</th>
                        <th width="250">SẢN PHẨM & CẤU HÌNH</th>
                        <th>DANH MỤC</th>
                        <th>THƯƠNG HIỆU</th>
                        <th>GIÁ BÁN / GIÁ GỐC</th>
                        <th>TỒN KHO</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>   
              <?php 
                    include __DIR__ . "/../../config/config.php";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "SELECT * FROM products where is_deleted='1'";
                    $result = $conn->query($sql);
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
                            $statusText = $status == 1 ? "Đang kinh doanh" : "Ngừng kinh doanh";
                           echo "<tr>";
                           echo "<td>
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' checked>
                            </div>
                        </td>";
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
                           echo " <td>
                            <div class='d-flex gap-1'>
                                <button class='action-btn' title='Khôi phục'>
                                <a href='restore.php?id=$id'>
                                <i class='bi bi-arrow-counterclockwise'></i>
                                </a>
                                </button>
                                <button class='action-btn delete' title='Xóa'>
                                <a href='./products/delete.php?id=$id'>
                                <i class='bi bi-trash'></i>
                                </a>
                                </button>
                            </div>
                        </td>";

                           echo "</tr>";

                    }
                    
                }else{
                        echo "<tr>
                        <td colspan='9' class='text-center py-5'>
                            <div class='empty-state'>
                                <div class='d-flex justify-content-center mb-3'>
                                    <i class='bi bi-trash' style='font-size: 4rem; color: #cbd5e1;'></i>
                                </div>
                                <h5 class='text-secondary mb-2 fw-bold'>Thùng rác rỗng</h5>
                                <p class='text-muted mb-0'>Hiện tại không có sản phẩm nào trong thùng rác.</p>
                            </div>
                        </td>
                        </tr>";
                    }
                ?>

                  
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <!-- <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center">
            <div class="text-muted small">Hiển thị 2 sản phẩm trong thùng rác</div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link text-muted border-0 bg-light rounded-start-pill" href="#">&laquo;</a></li>
                    <li class="page-item active"><a class="page-link bg-danger border-danger" href="#">1</a></li>
                    <li class="page-item disabled"><a class="page-link text-muted border-0 bg-light rounded-end-pill" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div> -->
    </div>


</div>

<script>
    // Khởi tạo Tooltip cho các nút thao tác
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<?php 
include __DIR__ . '/layouts/footer.php';
?>
