<?php
/*
 * Copyright (C) 
*/
!defined('DEBUG') and exit('Access Denied.');


$safe_token = well_token_set($uid);
$page = param('page',1);

include _include(APP_PATH . 'plugin/bang_reviews/model/bang_reviews.func.php');


    


    $uid = _SESSION('uid');
    $total = thread_tid_find_by_fid_res_count();
    if ($total <= 0) {
        exit('no data');
    }
    // $dir = substr(sprintf("%09d", 1), 0, 3);
    // $avatar_url = $user['avatar'] ? file_path() . "avatar/$dir/1.png?" . $user['avatar'] : view_path() . 'img/avatar.png';
    $pageTotal = ceil($total / 20);
    

    $tidlistRes = well_thread_find_tid_res( $page, 20);
    include _include(theme_load('reviews', '', 'bang_reviews'));

