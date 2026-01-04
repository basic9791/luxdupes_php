<?php
/*
 * Copyright (C) www.wellcms.cn
*/
!defined('DEBUG') and exit('Access Denied.');

$tid = param(1, 0);

// hook read_start.php

$thread = 1 == array_value($conf, 'cache_thread') ? well_thread_read_cache($tid) : well_thread_read($tid);
// hook read_cache_after.php
if (empty($thread)) {
    if ('1' == _GET('ajax')) {
        message(-2, lang('thread_not_exists'));
    } else {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        include _include(theme_load('read_404'), array_value($thread, 'fid'));
		exit;
    }
}

if (array_value($conf, 'upload_token', 0)) {
            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));
        }

// hook read_status_before.php


// hook read_center.php

// 用户读取版块主题的权限
// forum_access_user($fid, $gid, 'allowread') || message(-1, lang('user_group_insufficient_privilege'));

// if(empty($uid) && $fid<373) {   //本来用来限制部份怎禁止未登录用户访问的，先暂时开放，后继看情况处理

//     message(-1, lang('user_group_insufficient_privilege'));


// }

// hook read_middle.php

// 大站可用单独的点击服务，减少 db 压力 / if request is huge, separate it from mysql server


// hook read_after.php

switch ($thread['type']) {
  case '9':
        // 投票 / Article
        // hook read_article_start.php
         $data = data_read_cache($tid);
         empty($data) and message(-1, lang('data_malformation'));
         $voteData= str_replace('&quot;','"', $data['message']);
         $jsonData=json_decode( $voteData,true);
         $dataStr= str_replace('&quot;','"', param('voteParams'));
         $voteParam = json_decode( $dataStr,true);

         $index=0;
         foreach ($jsonData['voteList'] as $vs) { 
            $curPs=[];
            foreach($voteParam as $vp){
              if($vp['name']==='item'.$index){
                array_push($curPs, $vp);
              }

            }
           
            foreach($curPs as $cp){
              foreach($vs['list'] as $key=>$ls){
                if($cp['value'] == $ls['name'] ){
                //  $ls['zNum'] =(int) $ls['zNum'];
                //  $ls['jNum']  =(int)$ls['jNum'];
                //  $vs['zCount'] =(int)$vs['zCount'];
                //  $vs['jCount'] =(int) $vs['jCount'];

                  $jsonData['voteList'][$index]['list'][$key]['jNum']= $ls['jNum']+1;
                  $jsonData['voteList'][$index]['list'][$key]['zNum']= $ls['zNum']+1;
                  $jsonData['voteList'][$index]['zCount']=$vs['zCount']+1;
                  $jsonData['voteList'][$index]['jCount']=$vs['jCount']+1;
    
                }
              }
            }

            $index++;
          } 
          $message=json_encode($jsonData);
          $update = array('tid' => $tid, 'gid' => 1, 'doctype' => 0, 'message' => $message);
          !empty($update) && FALSE === data_update($tid, $update) and message(-1, lang('update_post_failed'));
           message(0, '投票成功');
        break;
    default:
        message(-1, lang('data_malformation'));
        break;
}

// hook read_end.php

?>