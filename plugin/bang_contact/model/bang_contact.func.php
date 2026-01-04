<?php
/*
 * Copyright (C) 
 */

// hook model_bang_contact_start.php

// ------------> 原生CURD，无关联其他数据。
function bang_contact_create($arr = array(), $d = NULL)
{   
    // echo 'sss1===============';
    // hook model_bang_contact_create_start.php
    $r = db_insert('bang_contact', $arr, $d);
    //  echo 'sss2===============';
    // hook model_bang_contact_create_end.php
    return $r;
}

function bang_contact__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_contact__update_start.php
    $r = db_update('bang_contact', $cond, $update, $d);
    // hook model_bang_contact__update_end.php
    return $r;
}

function bang_contact__read($cond = array(), $contactby = array(), $col = array(), $d = NULL)
{
    // hook model_bang_contact__read_start.php
    $r = db_find_one('bang_contact', $cond, $contactby, $col, $d);
    // hook model_bang_contact__read_end.php
    return $r;
}

function bang_contact__find($cond = array(), $contactby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_bang_contact__find_start.php
    $arr = db_find('bang_contact', $cond, $contactby, $page, $pagesize, $key, $col, $d);
    // hook model_bang_contact__find_end.php
    return $arr;
}

function bang_contact__delete($cond = array(), $d = NULL)
{
    // hook model_bang_contact__delete_start.php
    $r = db_delete('bang_contact', $cond, $d);
    // hook model_bang_contact__delete_end.php
    return $r;
}

function bang_contact__count($cond = array(), $d = NULL)
{
    // hook model_bang_contact__count_start.php
    $n = db_count('bang_contact', $cond, $d);
    // hook model_bang_contact__count_end.php
    return $n;
}

function bang_contact_big_insert($arr = array(), $d = NULL)
{
    // hook model_bang_contact_big_insert_start.php
    $r = db_big_insert('bang_contact', $arr, $d);
    // hook model_bang_contact_big_insert_end.php
    return $r;
}

function bang_contact_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_contact_big_update_start.php
    $r = db_big_update('bang_contact', $cond, $update, $d);
    // hook model_bang_contact_big_update_end.php
    return $r;
}

//--------------------------强相关--------------------------

function bang_contact_update($id, $update)
{
    global $conf;
    if (!$id || empty($update)) return FALSE;
    // hook model_bang_contact_update_start.php
    $r = bang_contact__update(array('id' => $id), $update);
    if ('mysql' != $conf['cache']['type']) {
        $read = bang_contact_read($id);
        $read and cache_delete('bang_contact_read_by_tid_and_uid_cache_' . $read['tid'] . '_' . $read['uid']);
    }
    // hook model_bang_contact_update_end.php
    return $r;
}

function bang_contact_read($id)
{
    // hook model_bang_contact_read_start.php
    $r = bang_contact__read(array('id' => $id));
    // hook model_bang_contact_read_end.php
    return $r;
}

function bang_contact_read_by_tid_and_uid($tid, $_uid)
{
    // hook model_bang_contact_read_by_tid_and_uid_start.php
    $r = bang_contact__read(array('tid' => $tid, 'uid' => $_uid));
    // hook model_bang_contact_read_by_tid_and_uid_end.php
    return $r;
}

function bang_contact_find($page = 1, $pagesize = 20)
{
    // hook model_bang_contact_find_start.php

    $arrlist = bang_contact__find(array(), array('id' => -1), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $uidarr += arrlist_values($arrlist, 'touid');

    $userlist = user__find(array('uid' => array_unique($uidarr)), array(), 1, count($uidarr));

    // hook model_bang_contact_find_center.php

    foreach ($arrlist as &$val) {
        bang_contact_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['to_username'] = isset($userlist[$val['touid']]) ? $userlist[$val['touid']]['username'] : '';
    }

    // hook model_bang_contact_find_end.php
    return $arrlist;
}

function bang_contact_find_by_id($id, $page = 1, $pagesize = 20)
{
    $key = 'bang_contact_find_by_id_' . md5(xn_json_encode($id));
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc
    if (isset($cache[$key])) return $cache[$key];

    // hook model_bang_contact_find_by_id_start.php
    $arrlist = bang_contact__find(array('id' => $id), array(), $page, $pagesize);
    // hook model_bang_contact_find_by_id_end.php
    return $arrlist;
}

// 谁收藏过主题
function bang_contact_find_by_tid($tid, $page = 1, $pagesize = 20)
{
    // hook model_bang_contact_find_by_tid_start.php

    $arrlist = bang_contact__find(array('tid' => $tid), array(), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_bang_contact_find_by_tid_center.php

    foreach ($arrlist as &$val) {
        bang_contact_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['online_status'] = $userlist[$val['uid']]['online_status'];
    }

    // hook model_bang_contact_find_by_tid_end.php

    return $arrlist;
}

// 被哪些用户收藏
function bang_contact_find_by_touid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_bang_contact_find_by_touid_start.php

    $arrlist = bang_contact__find(array('touid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

    // hook model_bang_contact_find_by_touid_before.php

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_bang_contact_find_by_touid_center.php

    $tidarr = arrlist_values($arrlist, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize, FALSE);

    // hook model_bang_contact_find_by_touid_middle.php

    foreach ($arrlist as &$val) {
        bang_contact_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['subject'] = $threadlist[$val['tid']]['subject'];
        $val['url'] = $threadlist[$val['tid']]['url'];
        // hook model_bang_contact_find_by_touid_foreach.php
    }

    // hook model_bang_contact_find_by_touid_end.php

    return $arrlist;
}

// 用户收藏哪些
function bang_contact_find_by_uid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_bang_contact_find_by_uid_start.php

    $arrlist = bang_contact__find(array('uid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

  

    return $arrlist;
}

function bang_contact_delete($id)
{
    global $conf;

    if (empty($id)) return FALSE;

    // hook model_bang_contact_delete_start.php

    if ('mysql' != $conf['cache']['type']) {
        $arrlist = bang_contact__find(array('id' => $id), 1, is_array($id) ? count($id) : 1);

        if (!$arrlist) return FALSE;

        foreach ($arrlist as $val) {
            cache_delete('bang_contact_read_by_tid_and_uid_cache_' . $val['tid'] . '_' . $val['uid']);
        }
    }

    $r = bang_contact__delete(array('id' => $id));
    if (FALSE === $r) return FALSE;

    // hook model_bang_contact_delete_end.php
    return $r;
}

function bang_contact_format(&$val)
{
    if (empty($val)) return;

    // hook model_bang_contact_format_start.php

    $val['create_date_fmt'] = date('Y-m-d', $val['create_date']);

    $val['type_fmt'] = '';
    switch ($val['type']) {
        case '0':
            $val['type_fmt'] = lang('thread');
            break;
        // hook model_bang_contact_format_case.php
    }

    // hook model_bang_contact_format_end.php
}

//--------------------------cache--------------------------
// 用户是否收藏
function bang_contact_read_by_tid_and_uid_cache($tid, $_uid)
{
    global $conf;

    // hook model_bang_contact_read_by_tid_and_uid_cache_start.php

    $key = 'bang_contact_read_by_tid_and_uid_cache_' . $tid . '_' . $_uid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc

    if (isset($cache[$key])) return $cache[$key];

    if ('mysql' == $conf['cache']['type']) {
        $r = bang_contact_read_by_tid_and_uid($tid, $_uid);
    } else {
        $r = cache_get($key);
        if (NULL === $r) {
            $r = bang_contact_read_by_tid_and_uid($tid, $_uid);
            $r and cache_set($key, $r, 300);
        }
    }

    $cache[$key] = $r ? $r : NULL;

    // hook model_bang_contact_read_by_tid_and_uid_cache_end.php

    return $cache[$key];
}

// hook model_bang_contact_end.php