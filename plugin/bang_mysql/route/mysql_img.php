<?php
/*
 * Copyright (C) www.wellcms.cn
 * 通过远程获取缩略图
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

$threadlist = db_sql_find("SELECT tid,fid,uid FROM `{$d->tablepre}website_thread` WHERE  tid > $startId LIMIT $count;", 'tid', $d);
// $threadlist = db_sql_find("SELECT tid,subject FROM `{$d->tablepre}website_thread` WHERE tid > $startId AND tid < $endId ;", 'tid', $d);


if ($threadlist) {
  foreach ($threadlist as &$thread) {
    // echo "<br/>";

    $tid=(int) $thread['tid'];
    $fid=(int) $thread['fid'];
    $uid=(int) $thread['uid'];

    $threadDetail = db_sql_find("SELECT tid,message FROM `{$d->tablepre}website_data` WHERE tid=$tid ;", 'tid', $d);
    if(count($threadDetail)>0){
      $message =  $threadDetail[$tid]['message'];
      if (preg_match_all('#<img[^>]+src="(.*?)"#i', $message, $match)) {
        // echo '<br/>xx[002]';
        $time=time();
        foreach ($match[1] as $_url) {
          if (FALSE !== strpos($_url, 'http')) {  //
            // echo "<br/>xx[003]tid=$tid";
            well_save_remote_image(array('tid' => $tid, 'fid' => $fid, 'uid' => $uid, 'message' => $message, 'thumbnail' => 1, 'save_image' => 0));
            break;
        } 
        }
      }else{
        well_thread_delete_all($tid);
      }
    }else{
      well_thread_delete_all($tid);
    }

    //well_thread_delete_all($tid);
    // $r = db_exec("UPDATE `{$d->tablepre}website_data` SET `message`='{$message}' WHERE `tid`={$tid}", $d);

  }
  header("Content-type: text/json");
  echo "{\"startId\":$tid,\"count\":$count}"; //$endId
}else{
  header("Content-type: text/json");
  echo "{\"startId\":0,\"count\":0}";
}



