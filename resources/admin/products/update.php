<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xử lý cập nhật sản phẩm</title>
</head>
<body>
    <h2>Xử lý cập nhật sản phẩm vào cơ sở dữ liệu</h2>
    <?php
        // 1. Đọc toàn bộ dữ liệu đã nhập từ form
        $id          = $_REQUEST['id'] ?? $_POST['id'] ?? $_GET['id'] ?? '';
        $name        = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price       = $_POST['price'] ?? 0;
        $stock       = $_POST['stock'] ?? 0;
        $sku         = $_POST['sku'] ?? '';
        $brand       = $_POST['brand'] ?? '';
        
        // Đọc và chuẩn hóa trạng thái (hỗ trợ cả active/inactive lẫn 1/0)
        $rawStatus   = $_POST['status'] ?? '1';
        $status      = ($rawStatus === 'active' || $rawStatus === '1' || $rawStatus == 1) ? 1 : 0;

        // 2. Kết nối cơ sở dữ liệu
        include __DIR__ . '/../../../config/config.php';
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Lỗi kết nối: " . $conn->connect_error);
        } else {
            // 3. Kiểm tra nếu có tải ảnh mới lên
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                $uploadDir = __DIR__ . '/../../../public/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $targetFile = $uploadDir . $imageName;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

                // Cập nhật cả hình ảnh mới
                $sql = "UPDATE products SET 
                            name = '$name',
                            sku = '$sku',
                            price = '$price',
                            stock = '$stock',
                            brand = '$brand',
                            description = '$description',
                            image = '$imageName',
                            status = '$status'
                        WHERE id = '$id'";
            } else {
                // Cập nhật thông tin và giữ nguyên ảnh cũ
                $sql = "UPDATE products SET 
                            name = '$name',
                            sku = '$sku',
                            price = '$price',
                            stock = '$stock',
                            brand = '$brand',
                            description = '$description',
                            status = '$status'
                        WHERE id = '$id'";
            }

            // 4. Thực thi câu lệnh SQL
            if ($conn->query($sql)) {
                // Cập nhật thành công, quay về danh sách sản phẩm
                header("Location: index.php");
                exit();
            } else {
                echo "Lỗi cập nhật: " . $sql . "<br>" . $conn->error;
            }
        }
    ?>
</body>
</html>
