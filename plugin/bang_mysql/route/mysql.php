<?php
/*
 * Copyright (C) www.wellcms.cn
 * 表website_tag_thread 根据tid 查询更新fid
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

$tagIdList = db_sql_find("SELECT tagid FROM `{$d->tablepre}website_tag` WHERE 1 LIMIT 200", 'tagid', $d);
$forumList = db_sql_find("SELECT fid,name FROM `{$d->tablepre}forum` WHERE 1 LIMIT 400", 'fid', $d);

$tagForumList=array();
if ($tagIdList) {
  foreach ($tagIdList as $tag) {
    $tagid=$tag['tagid'];
    $tagForumList[$tagid]=array();
    foreach ($forumList as $forum) {
      $fid=$forum['fid'];
      $n = db_count('website_tag_thread', array('tagid' => $tagid,'fid' =>$fid));
      // echo "$fid \n n=$n\n";
      if($n>0){
        $forum['count']=$n;
        array_push($tagForumList[$tagid],$forum); 
      }
    }
  }
  foreach ($tagForumList as $key => $tagForum) {
      usort($tagForumList[$key], function($a, $b) {
            return $b['count'] - $a['count'];
        });
  }

  $jsonData = json_encode($tagForumList);
  
  header("Content-type: text/json");
  echo $jsonData;
  // echo "{\"startId\":$tid,\"count_3\":$count}"; //$endId
}else{
  header("Content-type: text/json");
  echo "{\"startId\":0,\"count\":0,\"error\":0}";
}



