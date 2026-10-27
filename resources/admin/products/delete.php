<?php
//Xóa bản ghi
$id = $_REQUEST["id"] ?? '';
include __DIR__ . "/../../../config/config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    $sql = "DELETE FROM products WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: ../recycle-bin.php");
    } else {
        echo "Lỗi xóa: " . $sql . "<br>" . $conn->error;
    }

}


?>