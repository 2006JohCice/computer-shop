<?php
        include __DIR__ . "/../../../config/config.php";
        
        $user = $_POST["username"] ?? '';
        $pass = $_POST["password"] ?? '';

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
             die("Lỗi kết nối: " . $conn->connect_error);
        }else{
           $sql = "SELECT * FROM accounts WHERE username='$user' AND password='$pass'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                header("Location: ../home.php");
                exit();
            } else {
                header("Location: login.php");
                exit();
            }
        }
?>