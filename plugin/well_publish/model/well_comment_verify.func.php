<?php
/*
 * Copyright (C) www.wellcms.cn
 */

// hook model_well_comment_verify_start.php

// ------------> 原生CURD，无关联其他数据。
function well_comment_verify_create($arr = array(), $d = NULL)
{
    // hook model_well_comment_verify_create_start.php
    $r = db_replace('well_comment_verify', $arr, $d);
    // hook model_well_comment_verify_create_end.php
    return $r;
}

function well_comment_verify_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_well_comment_verify_update_start.php
    $r = db_update('well_comment_verify', $cond, $update, $d);
    // hook model_well_comment_verify_update_end.php
    return $r;
}

function well_comment_verify_read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_well_comment_verify_read_start.php
    $r = db_find_one('well_comment_verify', $cond, $orderby, $col, $d);
    // hook model_well_comment_verify_read_end.php
    return $r;
}

function well_comment_verify__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'pid', $col = array(), $d = NULL)
{
    // hook model_well_comment_verify__find_start.php
    $arr = db_find('well_comment_verify', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_well_comment_verify__find_end.php
    return $arr;
}

function well_comment_verify__delete($cond = array(), $d = NULL)
{
    // hook model_well_comment_verify__delete_start.php
    $r = db_delete('well_comment_verify', $cond, $d);
    // hook model_well_comment_verify__delete_end.php
    return $r;
}

function well_comment_verify_count($cond = array(), $d = NULL)
{
    // hook model_well_comment_verify_count_start.php
    $n = db_count('well_comment_verify', $cond, $d);
    // hook model_well_comment_verify_count_end.php
    return $n;
}

//--------------------------强相关--------------------------
// 降序
function well_comment_verify_find($page = 1, $pagesize = 20)
{
    // hook model_well_comment_verify_find_start.php
    $arr = well_comment_verify__find(array(), array('last_date' => 1), $page, $pagesize);

    if (empty($arr)) return NULL;
    $pidarr = arrlist_values($arr, 'pid');

    // hook model_well_comment_verify_find_before.php

    $postlist = comment_find($pidarr, $pagesize = 20, $desc = TRUE);

    // hook model_well_comment_verify_find_end.php
    return $postlist;
}

function well_comment_verify_find_by_uid($_uid, $page = 1, $pagesize = 20)
{
    if (empty($_uid)) return NULL;
    // hook model_well_comment_verify_find_by_uid_start.php
    $arr = well_comment_verify__find(array('uid' => $_uid), array('pid' => -1), $page, $pagesize);

    if (empty($arr)) return NULL;
    $pidarr = arrlist_values($arr, 'pid');

    // hook model_well_comment_verify_find_by_uid_before.php

    $postlist = comment_find($pidarr, $pagesize = 20, $desc = TRUE);

    // hook model_well_comment_verify_find_by_uid_end.php
    return $postlist;
}

function well_comment_verify_delete($pid)
{
    // hook model_well_comment_verify_delete_start.php
    $r = well_comment_verify__delete(array('pid' => $pid));
    // hook model_well_comment_verify_delete_end.php
    return $r;
}

function well_comment_pending_delete($post)
{
    global $gid, $uid, $time, $forumlist;

    if (0 == $post['status']) return;

    // hook model_well_comment_pending_delete_start.php

    $forum = isset($forumlist[$post['fid']]) ? $forumlist[$post['fid']] : NULL;
    empty($forum) AND message(1, lang('forum_not_exists'));

    empty($forum['type']) AND message(1, lang('user_group_insufficient_privilege'));

    // hook model_well_comment_pending_delete_before.php

    $allowdelete = forum_access_mod($post['fid'], $gid, 'allowdelete');
    empty($allowdelete) && empty($post['allowdelete']) AND message(1, lang('insufficient_delete_privilege'));

    empty($allowdelete) && ($post['closed'] OR empty($forum['comment'])) AND message(1, lang('thread_has_already_closed'));

    $comment_delete = array('pid' => $post['pid']);
    $verify_delete = array('pid' => $post['pid']);

    // hook model_well_comment_pending_delete_center.php

    comment__delete($comment_delete);

    // hook model_well_comment_pending_delete_middle.php

    well_attach_delete_by_pid($post['pid']);

    user_update($post['uid'], array('well_verify_comments-' => 1));

    well_comment_verify_delete($verify_delete);

    // hook model_well_comment_pending_delete_end.php

    include_once _include(APP_PATH . 'model/operate.func.php');
    operate_create(array('type' => 23, 'uid' => $uid, 'tid' => $post['tid'], 'pid' => $post['pid'], 'subject' => $post['subject'], 'comment' => '', 'create_date' => $time));
}

function well_comment_pending_delete_by_pids($pids)
{
    global $gid, $uid, $time, $forumlist;

    // hook model_well_comment_pending_delete_by_pids_start.php

    $commentlist = comment_find($pids, count($pids), FALSE);

    // hook model_well_comment_pending_delete_by_pids_before.php

    $pidarr = array();
    $uidarr = array();
    $operate_create = array();
    foreach ($commentlist as $comment) {

        if (0 == $comment['status'] || empty($forumlist[$comment['fid']]) || empty($forumlist[$comment['fid']]['type'])) continue;

        // 允许用户删除未审核的评论
        $allowdelete = $uid == $comment['uid'] || 1 == $gid || forum_access_mod($comment['fid'], $gid, 'allowdelete');
        if (empty($allowdelete)) continue;

        $pidarr[] = $comment['pid'];

        isset($uidarr[$comment['uid']]) ? $uidarr[$comment['uid']] += 1 : $uidarr[$comment['uid']] = 1;

        $operate_create[] = array('type' => 23, 'uid' => $uid, 'tid' => $comment['tid'], 'subject' => $comment['subject'], 'create_date' => $time);

        // hook model_well_comment_pending_delete_by_pids_foreach.php
    }

    // hook model_well_comment_pending_delete_by_pids_center.php

    comment__delete(array('pid' => $pidarr));

    well_attach_delete_by_pid($pidarr);

    well_comment_verify_delete($pidarr);

    // hook model_well_comment_pending_delete_by_pids_middle.php

    $uids = array();
    $user_update = array();
    foreach ($uidarr as $_uid => $n) {
        // hook model_well_comment_pending_delete_by_pids_user_update_before.php
        $uids[] = $_uid;
        // 待审核-1
        $user_update[$_uid] = array('well_verify_comments-' => $n);
        // hook model_well_comment_pending_delete_by_pids_user_update.php
    }

    // hook model_well_comment_pending_delete_by_pids_after.php

    !empty($uids) && !empty($user_update) AND user_big_update(array('uid' => $uids), $user_update);

    // hook model_well_comment_pending_delete_by_pids_end.php

    include_once _include(APP_PATH . 'model/operate.func.php');
    operate_big_insert($operate_create);
}

function well_comment_pending_delete_by_tid($tid)
{
    global $gid, $uid, $time;

    // hook model_well_comment_pending_delete_by_tid_start.php

    $commentlist = well_comment_verify__find(array('tid' => $tid), array(), 1, 10000, '');
    if(!$commentlist) return NULL;

    // hook model_well_comment_pending_delete_by_tid_before.php

    $pidarr = array();
    $uidarr = array();
    foreach ($commentlist as $comment) {

        $pidarr[] = $comment['pid'];

        isset($uidarr[$comment['uid']]) ? $uidarr[$comment['uid']] += 1 : $uidarr[$comment['uid']] = 1;

        // hook model_well_comment_pending_delete_by_tid_foreach.php
    }

    // hook model_well_comment_pending_delete_by_tid_center.php

    comment__delete(array('pid' => $pidarr));

    well_attach_delete_by_pid($pidarr);

    well_comment_verify_delete($pidarr);

    // hook model_well_comment_pending_delete_by_tid_middle.php

    $uids = array();
    $user_update = array();
    foreach ($uidarr as $_uid => $n) {
        // hook model_well_comment_pending_delete_by_tid_user_update_before.php
        $uids[] = $_uid;
        // 待审核-1
        $user_update[$_uid] = array('well_verify_comments-' => $n);
        // hook model_well_comment_pending_delete_by_tid_user_update.php
    }

    // hook model_well_comment_pending_delete_by_tid_after.php

    !empty($uids) && !empty($user_update) AND user_big_update(array('uid' => $uids), $user_update);

    // hook model_well_comment_pending_delete_by_tid_end.php
}

// hook model_well_comment_verify_end.php

?>