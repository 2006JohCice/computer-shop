<?php
    include __DIR__ ."/../../../config/config.php";

    $name = $_POST['name'] ?? '';
    $code = $_POST['code'] ?? '';
    $country = $_POST['country'] ?? '';
    $website = $_POST['website'] ?? '';
    $status = $_POST['status'] ?? 1;
    $display_order = $_POST['display_order'] ;
    $description = $_POST['description'] ??'';
   
    $conn = new mysqli($servername,$username,$password,$dbname);

    if($conn->connect_error){
        echo "Kết nối không thành công". $conn->connect_error;
    }else{
       $sql = "INSERT INTO brand (name,origin,code,website,description,status,position) VALUES ('$name','$country','$code','$website','$description','$status','$display_order')";
       if ($conn->query($sql)) {
        header("Location: index.php");
       }else{
           echo "Thêm thất bại";
       }
    }

?>