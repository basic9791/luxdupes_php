<?php
/*
 * Copyright (C) scp98.com
 * 内容图片批量添加 alt +标题
 * 
*/
!defined('DEBUG') and exit('Access Denied.');

// hook sitemap_start.php

$startId = $_GET['sId'];
$count = $_GET['count'];
$endId=$startId + $count;

$http = http_url_path();
1 < $conf['url_rewrite_on'] and $http = rtrim($http, '/');


$d = $_SERVER['db'];

$threadlist = db_sql_find("SELECT tid,subject FROM `{$d->tablepre}website_thread` WHERE tid > $startId AND tid < $endId ;", 'tid', $d);


if ($threadlist) {

  foreach ($threadlist as &$thread) {
    // echo "<br/>";

    $tid=(int) $thread['tid'];
    $subject = $thread['subject'];
    $threadDetail = db_sql_find("SELECT tid,message FROM `{$d->tablepre}website_data` WHERE tid=$tid ;", 'tid', $d);
    $search='<img src=';
    $replace="<img alt=\"$subject\" src=";
    $message = str_replace($search,$replace,   $threadDetail[$tid]['message']);

    $r = db_exec("UPDATE `{$d->tablepre}website_data` SET `message`='{$message}' WHERE `tid`={$tid}", $d);
    // echo $message;
  }
  header("Content-type: text/json");
  echo "{\"startId\":$endId,\"count\":$count}";
}else{
  header("Content-type: text/json");
  echo "{\"startId\":0,\"count\":0}";
}



