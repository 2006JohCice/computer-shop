<?php
    include __DIR__ . '/../../../config/config.php';

    $name = $_POST["name"];
    $slug = $_POST["slug"];
    $description= $_POST["description"];
    $status = $_POST["status"];
    $is_featured = $_POST["is_featured"] ?? 1;
    $display_order = $_POST["display_order"];
    $conn = new mysqli($servername,$username,$password,$dbname);
    // echo $slug
    if($conn->connect_errno){
        die("kết nối thất bại");
    }else{
        echo "da kết nối ọ";
       $sql = "INSERT INTO category (name,status,position,description,deleted,slug) VALUES ('$name','$status','$display_order','$description','$is_featured','$slug')";
       if($conn->query($sql)) {
            header("Location: index.php");
          
        }
    }

?>