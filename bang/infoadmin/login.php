<?php
require_once 'config.php';
 $_SESSION['isLogin']=false;
$pToken=$_GET['pToken'] ?? '';
if($pToken==$token){
    $_SESSION['isLogin']=true;
    // echo '111';
}else{
    $_SESSION['isLogin']=false;
    // echo '222';
}

echo '<br/><a href="content-management.html">内容管理</a> <a href="index.html">用户管理</a>';

?>