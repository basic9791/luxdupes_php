<?php
/*
 * Copyright (C) 
 */
// 遍历栏目tid 按照: 发布时间 倒序，不包含置顶
function well_thread_find_tid_res($page = 1, $pagesize = 20)
{


    $key = 'website_comment_res_list_tid_' . $page . '_' . $pagesize;
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];


    $arr = thread_tid_find_by_fid_res($page, $pagesize, TRUE);



    if (empty($arr)) return NULL;



    return $arr;
}




function thread_tid_find_by_fid_res($page = 1, $pagesize = 1000, $desc = TRUE)
{

    $orderby = TRUE == $desc ? -1 : 1;
    $arr = thread_tid__find_res($cond = array('quotepid' => 0), array('create_date' => $orderby), $page, $pagesize, 'tid', array('tid', 'uid','create_date','message','rating'));
    return $arr;
}
function thread_tid_find_by_fid_res_count()
{

    $r = db_count('website_comment',$cond = array('quotepid' => 0),$d);
    return $r;
}


function thread_tid__find_res($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
  
    $arr = db_find('website_comment', $cond, $orderby, $page, $pagesize, $key, $col, $d);


    // $dir = substr(sprintf("%09d", $user['uid']), 0, 3);
    // // hook model_user_format_avatar_url_before.php
    // $user['avatar_url'] = $user['avatar'] ? file_path() . "avatar/$dir/$user[uid].png?" . $user['avatar'] : view_path() . 'img/avatar.png';

    // 批量查询 website_thread 表获取 subject 和 image_url
    if (!empty($arr)) {
        $tids = array_keys($arr);
        $threads = db_find('website_thread', array('tid' => $tids), array(), 1, count($tids), 'tid', array('tid', 'subject', 'image_url'));
        if (!empty($threads)) {
            foreach ($arr as $tid => &$v) {
                if (isset($threads[$tid])) {
                    $v['subject'] = $threads[$tid]['subject'];
                    $v['image_url'] = $threads[$tid]['image_url'];
                }
            }
            unset($v);
        }

        // 批量查询 user 表获取 username
        $uids = array_unique(array_column($arr, 'uid'));
        $uids = array_filter($uids);
        if (!empty($uids)) {
            $users = db_find('user', array('uid' => $uids), array(), 1, count($uids), 'uid', array('uid', 'username','avatar'));
            if (!empty($users)) {
                foreach ($arr as &$v) {
                    if (isset($v['uid']) && isset($users[$v['uid']])) {
                        $v['username'] = $users[$v['uid']]['username'];
                        $dir = substr(sprintf("%09d", $user['uid']), 0, 3);
                        $v['avatar_url'] = $v['avatar'] ? file_path() . "avatar/$dir/$user[uid].png?" . $v['avatar'] : view_path() . 'img/avatar.png';
                    }
                }
                unset($v);
            }
        }

        
    }

    return $arr;
}





