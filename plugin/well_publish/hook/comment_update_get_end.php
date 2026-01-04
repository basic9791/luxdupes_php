<?php exit;
$op = param('op');
'pending' == $op AND $referer = url('home-pending',array('op'=>'pending'));
?>