<?php
    echo "oke";  
    $user = $_POST["username"];
    $pass = $_POST["password"];
    $confirmpass = $_POST["confirm-password"];
    $email = $_POST["email"];
    $role = "user";
    $status="1";
    echo $user;  
    echo "<br>";  
    echo $pass;
    echo "<br>";
    echo $confirmpass;
    echo "<br>";
    echo $email;
    echo "<br>";
    
    include __DIR__."/../../config/config.php";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error){
        die("". $conn->connect_error);
    }else{
        echo "kết nối oke";
        if($user=="" || $pass=="" || $confirmpass=="" || $email==""){
            echo "<script>alert('Vui lòng nhập đầy đủ thông tin');</script>";
        }elseif($pass!=$confirmpass){
            echo "<script>alert('Mật khẩu không khớp');</script>";
        }else{
           $sql = "INSERT INTO accounts (username,password,email,role,status) VALUES ('$user','$pass','$email ','$role','$status')";

           if($conn->query($sql)){
            echo "Đăng ký thành công";
             header("Location: login.php");
            
           }else{
            //  echo "<script>alert('Đăng ký thất bại');</script>";
             header("Location: signup.php");
            
           }
        }
       
    }
    $conn->close();
    // header("Location: login.php");
    exit();
        
    
?>