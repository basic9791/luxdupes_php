<?php
/*
 * Copyright (C) www.wellcms.cn
 * 详情页内容处理
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

$threadlist = db_sql_find("SELECT tid,message FROM `{$d->tablepre}website_data` WHERE tid > $startId LIMIT $count;", 'tid', $d);
// $threadlist = db_sql_find("SELECT tid,subject FROM `{$d->tablepre}website_thread` WHERE tid > $startId AND tid <pre $endId ;", 'tid', $d);

if ($threadlist) {
  foreach ($threadlist as &$thread) {
    // echo "<br/>";
    if($thread['message']){
      $tid=$thread['tid'];
      $messageArr=explode("</pre>",$thread['message']);
      if (array_key_exists(1, $messageArr)) {
        $newData=$messageArr[1].str_replace('<pre>','<div>',$messageArr[0]).'</div>';
        $update['message']=str_replace('<div></div>','',$newData);
        // echo " $newData--------";
       
        // isset($update['message']) and data_message_format($update);
       
        $r = db_update('website_data', array('tid' => $tid), $update, $d);
        
      }

    }
    //well_thread_delete_all($tid);
    // 

  }
  header("Content-type: text/json");
  echo "{\"startId\":$tid,\"count_3\":$count}"; //$endId
}else{
  header("Content-type: text/json");
  echo "{\"startId\":0,\"count\":0,\"error\":0}";
}



