<?php
/*
 * Copyright (C) www.wellcms.cn
 */

// hook model_well_thread_reject_start.php

// ------------> 原生CURD，无关联其他数据。
function well_thread_reject_create($arr = array(), $d = NULL)
{
    // hook model_well_thread_reject_create_start.php
    $r = db_replace('well_thread_reject', $arr, $d);
    // hook model_well_thread_reject_create_end.php
    return $r;
}

function well_thread_reject_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_well_thread_reject_update_start.php
    $r = db_update('well_thread_reject', $cond, $update, $d);
    // hook model_well_thread_reject_update_end.php
    return $r;
}

function well_thread_reject_read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_well_thread_reject_read_start.php
    $r = db_find_one('well_thread_reject', $cond, $orderby, $col, $d);
    // hook model_well_thread_reject_read_end.php
    return $r;
}

function well_thread_reject__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_well_thread_reject__find_start.php
    $arr = db_find('well_thread_reject', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_well_thread_reject__find_end.php
    return $arr;
}

function well_thread_reject__delete($cond = array(), $d = NULL)
{
    // hook model_well_thread_reject__delete_start.php
    $r = db_delete('well_thread_reject', $cond, $d);
    // hook model_well_thread_reject__delete_end.php
    return $r;
}

function well_thread_reject_count($cond = array(), $d = NULL)
{
    // hook model_well_thread_reject_count_start.php
    $n = db_count('well_thread_reject', $cond, $d);
    // hook model_well_thread_reject_count_end.php
    return $n;
}

//--------------------------强相关--------------------------
// 全部主题，以最后更新排序
function well_thread_reject_find($page = 1, $pagesize = 20)
{
    // hook model_well_thread_reject_find_start.php

    $arr = well_thread_reject__find(array(), array('tid' => -1), $page, $pagesize);

    if (empty($arr)) return NULL;

    // hook model_well_thread_reject_find_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_well_thread_reject_find_after.php

    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_well_thread_reject_find_end.php

    return $threadlist;
}

// 查询用户所有主题 最后更新排序
function well_thread_reject_find_by_uid($uid, $page = 1, $pagesize = 20)
{
    // hook model_well_thread_reject_find_by_uid_start.php

    $arr = well_thread_reject__find(array('uid' => $uid), array('tid' => -1), $page, $pagesize);

    if (empty($arr)) return NULL;

    // hook model_well_thread_reject_find_by_uid_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_well_thread_reject_find_by_uid_after.php

    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_well_thread_reject_find_by_uid_end.php

    return $threadlist;
}

function well_thread_reject_delete($tid)
{
    // hook model_well_thread_reject_delete_start.php
    $r = well_thread_reject__delete(array('tid' => $tid));
    // hook model_well_thread_reject_delete_end.php
    return $r;
}

// hook model_well_thread_reject_end.php

?>