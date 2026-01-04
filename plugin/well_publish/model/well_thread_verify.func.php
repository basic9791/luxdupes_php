<?php
/*
 * Copyright (C) www.wellcms.cn
 */

// hook model_well_thread_verify_start.php

// ------------> 原生CURD，无关联其他数据。
function well_thread_verify_create($arr = array(), $d = NULL)
{
    // hook model_well_thread_verify_create_start.php
    $r = db_replace('well_thread_verify', $arr, $d);
    // hook model_well_thread_verify_create_end.php
    return $r;
}

function well_thread_verify_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_well_thread_verify_update_start.php
    $r = db_update('well_thread_verify', $cond, $update, $d);
    // hook model_well_thread_verify_update_end.php
    return $r;
}

function well_thread_verify_read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_well_thread_verify_read_start.php
    $r = db_find_one('well_thread_verify', $cond, $orderby, $col, $d);
    // hook model_well_thread_verify_read_end.php
    return $r;
}

function well_thread_verify__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_well_thread_verify__find_start.php
    $arr = db_find('well_thread_verify', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_well_thread_verify__find_end.php
    return $arr;
}

function well_thread_verify__delete($cond = array(), $d = NULL)
{
    // hook model_well_thread_verify__delete_start.php
    $r = db_delete('well_thread_verify', $cond, $d);
    // hook model_well_thread_verify__delete_end.php
    return $r;
}

function well_thread_verify_count($cond = array(), $d = NULL)
{
    // hook model_well_thread_verify_count_start.php
    $n = db_count('well_thread_verify', $cond, $d);
    // hook model_well_thread_verify_count_end.php
    return $n;
}

//--------------------------强相关--------------------------
// 全部主题，以最后更新排序
function well_thread_verify_find($page = 1, $pagesize = 20)
{
    // hook model_well_thread_verify_find_start.php
    $arr = well_thread_verify__find(array(), array('last_date' => -1), $page, $pagesize);

    if (empty($arr)) return NULL;

    // hook model_well_thread_verify_find_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_well_thread_verify_find_after.php

    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_well_thread_verify_find_end.php
    return $threadlist;
}

// 查询用户所有主题
function well_thread_verify_find_by_uid($uid, $page = 1, $pagesize = 20)
{
    // hook model_well_thread_verify_find_by_uid_start.php

    $arr = well_thread_verify__find(array('uid' => $uid), array('tid' => -1), $page, $pagesize);

    if (empty($arr)) return NULL;

    // hook model_well_thread_verify_find_by_uid_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_well_thread_verify_find_by_uid_after.php

    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_well_thread_verify_find_by_uid_end.php
    return $threadlist;
}

function well_thread_verify_delete($tid)
{
    // hook model_well_thread_verify_delete_start.php
    $r = well_thread_verify__delete(array('tid' => $tid));
    // hook model_well_thread_verify_delete_end.php
    return $r;
}

// 查看版块按最后回复排序主题
function well_thread_find_tid_by_fid_lastpid($fid, $page = 1, $pagesize = 20)
{
    // hook model_thread_find_tid_by_fid_lastpid_start.php

    $arr = thread_tid__find(array('fid' => $fid), array('lastpid' => -1), $page, $pagesize, 'tid', array('tid', 'verify_date', 'lastpid'));

    // hook model_thread_find_tid_by_fid_lastpid_end.php

    return $arr;
}

// 查看版块按最后回复排序主题
function well_thread_find_by_fid_lastpid($fid, $page = 1, $pagesize = 20)
{
    // hook model_thread_find_by_fid_lastpid_start.php

    $arr = well_thread_find_tid_by_fid_lastpid($fid, $page, $pagesize);
    if (empty($arr)) return NULL;
    // hook model_thread_find_by_fid_lastpid_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_thread_find_by_fid_lastpid_center.php

    $arrlist = well_thread__find(array('tid' => $tidarr), array(), 1, $pagesize);

    // hook model_thread_find_by_fid_lastpid_middle.php

    $i = 0;
    foreach ($arr as &$val) {
        $val = array_merge($val, $arrlist[$val['tid']]);
        ++$i;
        $val['i'] = $i;
        $val['verify_date_fmt'] = humandate($val['verify_date']);
        well_thread_format($val);
        // hook model_thread_find_by_fid_lastpid_fmt.php
    }

    // hook model_thread_find_by_fid_lastpid_end.php

    return $arr;
}

// 查看作者按最后回复排序主题
function well_thread_find_by_uid_lastpid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_thread_find_by_uid_lastpid_start.php

    $arr = thread_tid__find(array('uid' => $_uid), array('lastpid' => -1), $page, $pagesize, '', array('tid', 'verify_date', 'lastpid'));
    if (empty($arr)) return NULL;

    // hook model_thread_find_by_uid_lastpid_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_thread_find_by_uid_lastpid_center.php

    $arrlist = well_thread__find(array('tid' => $tidarr), array(), 1, $pagesize, 'tid');

    // hook model_thread_find_by_uid_lastpid_middle.php

    $i = 0;
    foreach ($arr as &$val) {
        $val = array_merge($val, $arrlist[$val['tid']]);
        ++$i;
        $val['i'] = $i;
        $val['verify_date_fmt'] = humandate($val['verify_date']);
        well_thread_format($val);
        // hook model_thread_find_by_uid_lastpid_fmt.php
    }

    // hook model_thread_find_by_uid_lastpid_end.php

    return $arr;
}

// 支持批量删除未审核投稿和草稿 tid / type 0 待审核文章 1退稿 2草稿箱
function well_thread_publish_delete($tid, $type = 0)
{
    if (empty($tid)) return;
    if (is_array($tid)) {
        well_thread_publish_delete_tids($tid, $type);
    } else {
        well_thread_publish_delete_tid($tid, $type);
    }
}

function well_thread_publish_delete_tids($tids, $type = 0)
{
    global $gid, $uid, $time, $conf, $config;

    // hook model_thread_publish_delete_tids_start.php

    $threadlist = well_thread__find(array('tid' => $tids), array('tid' => 1), 1, count($tids), 'tid');
    if (empty($threadlist)) return FALSE;

    $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

    // hook model_thread_publish_delete_tids_before.php

    // 需要删除的tid
    $tids = array();
    $tagids = array();
    $tagarr = array();
    // 统计主题作者和数量 array('作者' => '内容数量')
    $uidarr = array();
    // 统计版块主题数量 array('版块' => '内容数量')
    $fidarr = array();
    // 统计置顶主题 array('tid')
    $sticky_tids = array();
    // 有附件的主题
    $attach_tids = array();
    // 有评论的主题
    $post_tids = array();
    // 统计主题属性
    $flag_tids = array();
    $attachs = 0; // 统计附件数量图片和其他文件
    $posts = 0; // 统计评论数量
    $stickys = 0; // 统计置顶数量
    $index_stickys = 0; // 统计全局置顶数量
    $flags = 0; // 统计属性数量
    $operate_create = array();

    // hook model_thread_publish_delete_tids_center.php

    foreach ($threadlist as $thread) {

        if (0 == $thread['status']) continue;

        if ($uid != $thread['uid']) {
            !forum_access_mod($thread['fid'], $gid, 'allowdelete') and message(-1, lang('user_group_insufficient_privilege'));
        }

        // 删除主图
        if ($thread['icon']) {
            $day = date($attach_dir_save_rule, $thread['icon']);
            $file = $conf['upload_path'] . 'thumbnail/' . $day . '/' . $thread['uid'] . '_' . $thread['tid'] . '_' . $thread['icon'] . '.jpeg';
            is_file($file) and unlink($file);
        }

        $tids[] = $thread['tid'];

        if ($thread['tag']) {
            $_tagarr = xn_json_decode($thread['tag']);
            foreach ($_tagarr as $_tagid => $tagname) {
                $tagids[] = $_tagid;
                isset($tagarr[$_tagid]) ? $tagarr[$_tagid] += 1 : $tagarr[$_tagid] = 1;
            }

            // 删除标签主题表
            $arlist = well_tag_thread_find_by_tid($tid, 1, 10);
            if ($arlist) {
                $ids = array();
                foreach ($arlist as $val) $ids[] = $val['id'];

                well_tag_thread_delete($ids);
            }
        }

        if ($thread['images'] || $thread['files']) {
            $attach_tids[] = $thread['tid'];
            $attachs += $thread['images'] += $thread['files'];
        }

        if ($thread['posts']) {
            $post_tids[] = $thread['tid'];
            $posts += $thread['posts'];
        }

        if ($thread['sticky']) {
            $stickys += 1;
            $sticky_tids[] = $thread['uid'];
            3 == $thread['sticky'] and $index_stickys += 1;
        }

        if ($thread['flags']) {
            $flags += $thread['flags'];
            $flag_tids[] = $thread['tid'];
        }

        isset($fidarr[$thread['fid']]) ? $fidarr[$thread['fid']] += 1 : $fidarr[$thread['fid']] = 1;

        isset($uidarr[$thread['uid']]) ? $uidarr[$thread['uid']] += 1 : $uidarr[$thread['uid']] = 1;

        $operate_create[] = array('type' => $type ? 21 : 20, 'uid' => $uid, 'tid' => $thread['tid'], 'subject' => $thread['subject'], 'create_date' => $time);
    }

    // hook model_thread_publish_delete_tids_middle.php

    // 更新tag统计
    if (!empty($tagids)) {
        $tagids = array_unique($tagids);
        $update = array();
        foreach ($tagarr as $tagid => $n) {
            $update[$tagid] = array('count-' => $n);
        }

        tag_big_update(array('tagid' => $tagids), $update);
    }

    // 清理附件
    $attach_tids and well_attach_delete_by_tids($attach_tids, $attachs);

    // 删除评论
    $post_tids and comment_delete_by_tids($post_tids, $posts);

    $stickys and sticky_thread__delete($sticky_tids);

    if ($index_stickys) {
        $config['index_stickys'] -= $index_stickys;
        setting_set('conf', $config);
    }

    $flag_tids and flag_thread_delete_by_tids($flag_tids, $flags);

    // 更新版块统计主题数  评论数 置顶数$stickys-$index_stickys
    if (!empty($fidarr)) {
        $fids = array();
        $update = array();
        foreach ($fidarr as $_fid => $n) {
            $fids[] = $_fid;
            $update[$_fid] = array('threads-' => $n);
        }

        forum_big_update(array('fid' => $fids), $update);
    }

    $user_update = array();
    // 删除待审主题、退稿表
    if (!empty($tids)) {
        // 删除主题
        well_thread__delete($tids);

        // 删除内容
        data__delete($tids);

        switch ($type) {
                // hook model_thread_publish_delete_tids_case_start.php
            case 0:
                // 待审核表
                well_thread_verify_delete($tids);

                $uids = array();
                foreach ($uidarr as $_uid => $n) {
                    $uids[] = $_uid;
                    // 待审核-1
                    $user_update[$_uid] = array('well_verify_threads-' => $n);
                }

                break;
            case 1:
                // 退稿
                well_thread_reject_delete($tids);

                // 退稿-1
                $uids = array();
                foreach ($uidarr as $_uid => $n) {
                    $uids[] = $_uid;
                    // 待审核-1
                    $user_update[$_uid] = array('well_publish_rejects-' => $n);
                }
                break;
                // hook model_thread_publish_delete_tids_case_end.php
            default:
                message(-1, lang('data_malformation'));
                break;
        }
    }

    // hook model_thread_publish_delete_tids_after.php

    // 减少用户主题数
    !empty($uids) && !empty($user_update) and user_big_update(array('uid' => $uids), $user_update);

    include_once _include(APP_PATH . 'model/operate.func.php');
    operate_big_insert($operate_create);

    // hook model_thread_publish_delete_tids_end.php

    return TRUE;
}

function well_thread_publish_delete_tid($tid, $type = 0)
{
    global $conf, $uid, $gid, $forumlist, $time;

    // hook model_thread_publish_delete_start.php

    $thread = well_thread_read_cache($tid);
    empty($thread) and message(-1, lang('thread_not_exists'));

    if (0 == $thread['status']) return TRUE;

    $forum = array_value($forumlist, $thread['fid']);

    // hook model_thread_publish_delete_before.php

    $thread['sticky'] and message(-1, lang('user_group_insufficient_privilege'));

    // 待审主题用户可以自行删除
    if ($uid != $thread['uid']) {
        !forum_access_mod($thread['fid'], $gid, 'allowdelete') and message(-1, lang('user_group_insufficient_privilege'));
    }

    // hook model_thread_publish_delete_center.php

    // 删除主图
    if ($thread['icon']) {
        $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');
        $day = date($attach_dir_save_rule, $thread['icon']);
        $file = $conf['upload_path'] . 'thumbnail/' . $day . '/' . $thread['uid'] . '_' . $thread['tid'] . '_' . $thread['icon'] . '.jpeg';
        is_file($file) and unlink($file);
    }

    // 删除tag
    if ($thread['tag']) {
        $tagids = array_keys($thread['tag_fmt']);
        well_oldtag_delete($tagids, $tid);
    }

    // 删除主题属性 同时更新
    if ($thread['flags']) {
        $r = flag_thread_delete_by_tid($tid);
        if (FALSE === $r) return FALSE;
    }

    // 删除内容
    $r = data_delete($tid);
    if (FALSE === $r) return FALSE;

    // 删除所有回复 同时更新了用户评论数
    $n = $thread['posts'] ? comment_delete_by_tid($tid) : 0;

    // 删除附件
    ($thread['images'] || $thread['files']) && well_attach_delete_by_tid($tid);

    // 删除主题
    $r = well_thread_delete($tid);
    if (FALSE === $r) return FALSE;

    // hook model_thread_publish_delete_middle.php

    $arr = array('uid' => $uid, 'tid' => $tid, 'subject' => $thread['subject'], 'comment' => '', 'create_date' => $time);

    $user_update = array();
    switch (array_value($forum, 'model')) {
            // hook home_content_delete_case_start.php
        case '0': // 文章
            switch ($type) {
                    // hook home_content_delete_case_0_case_start.php
                case '0':
                    $arr['type'] = 20;
                    // 待审核表
                    well_thread_verify_delete($tid);

                    // 待审核-1
                    $user_update['well_verify_threads-'] = 1;
                    break;
                case '1':
                    $arr['type'] = 21;
                    // 退稿
                    well_thread_reject_delete($tid);

                    // 退稿-1
                    $user_update['well_publish_rejects-'] = 1;
                    break;
                    // hook home_content_delete_case_0_case_end.php
                default:
                    message(-1, lang('data_malformation'));
                    break;
            }

            break;
            // hook home_content_delete_case_end.php
    }

    // hook model_thread_publish_delete_after.php

    $thread['uid'] and isset($user_update) and user_update($thread['uid'], $user_update);

    // hook model_thread_publish_delete_start.php

    include_once _include(APP_PATH . 'model/operate.func.php');
    operate_create($arr);

    return TRUE;
}

// hook model_well_thread_verify_end.php