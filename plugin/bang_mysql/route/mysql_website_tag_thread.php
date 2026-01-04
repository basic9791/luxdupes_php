<?php
/*
 * Copyright (C) www.wellcms.cn
 * 表根据tid 查询fid下是否有tid的主题，并返回条数JSON
*/
!defined('DEBUG') and exit('Access Denied.');

// hook sitemap_start.php

$startId = $_GET['sId'];
$count =(int) $_GET['count'];
if($count<1){
    $count=3;
}
$endId=$startId + $count;

$http = http_url_path();
1 < $conf['url_rewrite_on'] and $http = rtrim($http, '/');


$d = $_SERVER['db'];

$threadlist = db_sql_find("SELECT tid,id FROM `{$d->tablepre}website_tag_thread` WHERE fid<1 LIMIT $count;", 'tid', $d);
// $threadlist = db_sql_find("SELECT tid,subject FROM `{$d->tablepre}website_thread` WHERE tid > $startId AND tid <pre $endId ;", 'tid', $d);
// echo 111;
// print_r($threadlist);
if ($threadlist) {
  foreach ($threadlist as $thread) {
    $tid=$thread['tid'];
    $id=$thread['id'];
    $tDetail = db_sql_find_one("SELECT tid,fid FROM `{$d->tablepre}website_thread` WHERE tid=$tid;",  $d);
    db_update('website_tag_thread', array('id' => $id),$tDetail, $d);

  }
  header("Content-type: text/json");
  echo "{\"startId\":$tid,\"count_3\":$count}"; //$endId
}else{
  header("Content-type: text/json");
  echo "{\"startId\":0,\"count\":0,\"error\":0}";
}



