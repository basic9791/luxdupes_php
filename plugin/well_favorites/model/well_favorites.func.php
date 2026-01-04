<?php
/*
 * Copyright (C) www.wellcms.cn
 */

// hook model_well_favorites_start.php

// ------------> 原生CURD，无关联其他数据。
function well_favorites_create($arr = array(), $d = NULL)
{
    // hook model_well_favorites_create_start.php
    $r = db_insert('well_favorites', $arr, $d);
    // hook model_well_favorites_create_end.php
    return $r;
}

function well_favorites__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_well_favorites__update_start.php
    $r = db_update('well_favorites', $cond, $update, $d);
    // hook model_well_favorites__update_end.php
    return $r;
}

function well_favorites__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_well_favorites__read_start.php
    $r = db_find_one('well_favorites', $cond, $orderby, $col, $d);
    // hook model_well_favorites__read_end.php
    return $r;
}

function well_favorites__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_well_favorites__find_start.php
    $arr = db_find('well_favorites', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_well_favorites__find_end.php
    return $arr;
}

function well_favorites__delete($cond = array(), $d = NULL)
{
    // hook model_well_favorites__delete_start.php
    $r = db_delete('well_favorites', $cond, $d);
    // hook model_well_favorites__delete_end.php
    return $r;
}

function well_favorites__count($cond = array(), $d = NULL)
{
    // hook model_well_favorites__count_start.php
    $n = db_count('well_favorites', $cond, $d);
    // hook model_well_favorites__count_end.php
    return $n;
}

function well_favorites_big_insert($arr = array(), $d = NULL)
{
    // hook model_well_favorites_big_insert_start.php
    $r = db_big_insert('well_favorites', $arr, $d);
    // hook model_well_favorites_big_insert_end.php
    return $r;
}

function well_favorites_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_well_favorites_big_update_start.php
    $r = db_big_update('well_favorites', $cond, $update, $d);
    // hook model_well_favorites_big_update_end.php
    return $r;
}

//--------------------------强相关--------------------------

function well_favorites_update($id, $update)
{
    global $conf;
    if (!$id || empty($update)) return FALSE;
    // hook model_well_favorites_update_start.php
    $r = well_favorites__update(array('id' => $id), $update);
    if ('mysql' != $conf['cache']['type']) {
        $read = well_favorites_read($id);
        $read and cache_delete('well_favorites_read_by_tid_and_uid_cache_' . $read['tid'] . '_' . $read['uid']);
    }
    // hook model_well_favorites_update_end.php
    return $r;
}

function well_favorites_read($id)
{
    // hook model_well_favorites_read_start.php
    $r = well_favorites__read(array('id' => $id));
    // hook model_well_favorites_read_end.php
    return $r;
}

function well_favorites_read_by_tid_and_uid($tid, $_uid)
{
    // hook model_well_favorites_read_by_tid_and_uid_start.php
    $r = well_favorites__read(array('tid' => $tid, 'uid' => $_uid));
    // hook model_well_favorites_read_by_tid_and_uid_end.php
    return $r;
}

function well_favorites_find($page = 1, $pagesize = 20)
{
    // hook model_well_favorites_find_start.php

    $arrlist = well_favorites__find(array(), array('id' => -1), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $uidarr += arrlist_values($arrlist, 'touid');

    $userlist = user__find(array('uid' => array_unique($uidarr)), array(), 1, count($uidarr));

    // hook model_well_favorites_find_center.php

    foreach ($arrlist as &$val) {
        well_favorites_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['to_username'] = isset($userlist[$val['touid']]) ? $userlist[$val['touid']]['username'] : '';
    }

    // hook model_well_favorites_find_end.php
    return $arrlist;
}

function well_favorites_find_by_id($id, $page = 1, $pagesize = 20)
{
    $key = 'well_favorites_find_by_id_' . md5(xn_json_encode($id));
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc
    if (isset($cache[$key])) return $cache[$key];

    // hook model_well_favorites_find_by_id_start.php
    $arrlist = well_favorites__find(array('id' => $id), array(), $page, $pagesize);
    // hook model_well_favorites_find_by_id_end.php
    return $arrlist;
}

// 谁收藏过主题
function well_favorites_find_by_tid($tid, $page = 1, $pagesize = 20)
{
    // hook model_well_favorites_find_by_tid_start.php

    $arrlist = well_favorites__find(array('tid' => $tid), array(), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_well_favorites_find_by_tid_center.php

    foreach ($arrlist as &$val) {
        well_favorites_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['online_status'] = $userlist[$val['uid']]['online_status'];
    }

    // hook model_well_favorites_find_by_tid_end.php

    return $arrlist;
}

// 被哪些用户收藏
function well_favorites_find_by_touid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_well_favorites_find_by_touid_start.php

    $arrlist = well_favorites__find(array('touid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

    // hook model_well_favorites_find_by_touid_before.php

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_well_favorites_find_by_touid_center.php

    $tidarr = arrlist_values($arrlist, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize, FALSE);

    // hook model_well_favorites_find_by_touid_middle.php

    foreach ($arrlist as &$val) {
        well_favorites_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['subject'] = $threadlist[$val['tid']]['subject'];
        $val['url'] = $threadlist[$val['tid']]['url'];
        // hook model_well_favorites_find_by_touid_foreach.php
    }

    // hook model_well_favorites_find_by_touid_end.php

    return $arrlist;
}

// 用户收藏哪些
function well_favorites_find_by_uid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_well_favorites_find_by_uid_start.php

    $arrlist = well_favorites__find(array('uid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

    // hook model_well_favorites_find_by_uid_before.php

    $tidarr = arrlist_values($arrlist, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize, FALSE);


    // hook model_well_favorites_find_by_uid_after.php

    foreach ($arrlist as &$val) {
        well_favorites_format($val);
        $val = $val + $threadlist[$val['tid']];
        // hook model_well_favorites_find_by_uid_foreach.php
    }

    // hook model_well_favorites_find_by_uid_end.php

    return $arrlist;
}

function well_favorites_delete($id)
{
    global $conf;

    if (empty($id)) return FALSE;

    // hook model_well_favorites_delete_start.php

    if ('mysql' != $conf['cache']['type']) {
        $arrlist = well_favorites__find(array('id' => $id), 1, is_array($id) ? count($id) : 1);

        if (!$arrlist) return FALSE;

        foreach ($arrlist as $val) {
            cache_delete('well_favorites_read_by_tid_and_uid_cache_' . $val['tid'] . '_' . $val['uid']);
        }
    }

    $r = well_favorites__delete(array('id' => $id));
    if (FALSE === $r) return FALSE;

    // hook model_well_favorites_delete_end.php
    return $r;
}

function well_favorites_format(&$val)
{
    if (empty($val)) return;

    // hook model_well_favorites_format_start.php

    $val['create_date_fmt'] = date('Y-m-d', $val['create_date']);

    $val['type_fmt'] = '';
    switch ($val['type']) {
        case '0':
            $val['type_fmt'] = lang('thread');
            break;
        // hook model_well_favorites_format_case.php
    }

    // hook model_well_favorites_format_end.php
}

//--------------------------cache--------------------------
// 用户是否收藏
function well_favorites_read_by_tid_and_uid_cache($tid, $_uid)
{
    global $conf;

    // hook model_well_favorites_read_by_tid_and_uid_cache_start.php

    $key = 'well_favorites_read_by_tid_and_uid_cache_' . $tid . '_' . $_uid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc

    if (isset($cache[$key])) return $cache[$key];

    if ('mysql' == $conf['cache']['type']) {
        $r = well_favorites_read_by_tid_and_uid($tid, $_uid);
    } else {
        $r = cache_get($key);
        if (NULL === $r) {
            $r = well_favorites_read_by_tid_and_uid($tid, $_uid);
            $r and cache_set($key, $r, 300);
        }
    }

    $cache[$key] = $r ? $r : NULL;

    // hook model_well_favorites_read_by_tid_and_uid_cache_end.php

    return $cache[$key];
}

// hook model_well_favorites_end.php