<?php
//Xóa bản ghi
$id = $_REQUEST["id"] ?? '';
include __DIR__ . "/../../../config/config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}else{
    $sql = "UPDATE products SET is_deleted='1' WHERE id=$id";
      if ($conn->query($sql)) {
          header("Location: index.php");
        } else {
            echo "Lỗi xóa: " . $sql . "<br>" . $conn->error;
        }

}


?>