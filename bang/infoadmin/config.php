<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'u962307395_cn15819915273');
define('DB_PASS', '450963604@Qaz');
define('DB_NAME', 'u962307395_usersys');


$token='OjKLBHnFqTBjZNjWeUOzsqvSHXMBKerDh'; //OjKLBHnFqTBjZNjWeUOzsqvSHXMBKerDh
session_start();



function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function checkLogin($token) {
 if(!$_SESSION['isLogin']){
    $pToken = $_GET['pToken'] ?? '';
    if($pToken==$token){
        $_SESSION['isLogin']=true;
    }else{
        header('Location: login.php');
        exit;
    }
    
} 

}
?>