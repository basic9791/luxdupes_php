<?php
/*
 * Copyright (C) www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');
// 检查是否登录
user_login_check();

FALSE === group_access($gid, 'allowverify') AND message(1, lang('user_group_insufficient_privilege'));

$action = param(1, 'thread');

// hook verify_start.php

// 从全局拉取$user
$header['mobile_title'] = '';
$header['mobile_linke'] = '';
list($member_navs, $member_menus) = nav_member();

switch ($action) {
    // hook verify_case_start.php
    case 'thread': // 审核主题 删除主题

        // hook verify_thread_start.php

        if ('GET' == $method) {

            $page = param(2, 1);
            $pagesize = 25;
            $safe_token = well_token_set($uid);
            $extra = array(); // 插件预留

            // hook verify_thread_get_start.php

            $threadlist = well_thread_verify_find($page, $pagesize);

            // hook verify_thread_get_before.php

            $page_url = url('verify-thread-{page}', $extra);
            $num = well_thread_verify_count();

            // hook verify_thread_get_center.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $extra['safe_token'] = $safe_token;
            $header['title'] = lang('well_publish_verify_thread');

            // hook verify_thread_get_end.php

            if ($ajax) {
                if ($threadlist) {
                    foreach ($threadlist as &$thread) $thread = well_thread_safe_info($thread);
                }

                $apilist['header'] = $header;
                $apilist['member_navlist'] = $member_navs;
                $apilist['member_menulist'] = $member_menus;
                $apilist['extra'] = $extra;
                $apilist['num'] = $num;
                $apilist['page'] = $page;
                $apilist['pagesize'] = $pagesize;
                $apilist['page_url'] = $page_url;
                $apilist['safe_token'] = $safe_token;
                $apilist['threadlist'] = $threadlist;

                $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
            } else {
                include _include(theme_load('verify_thread', '', 'well_publish'));
            }
        } elseif ('POST' == $method) {

            // 验证token
            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));

            // 支持批量 1通过 0退稿
            $tid = param(2, 0);
            $verified = param('verified', 0);
            $n = 0;

            // hook verify_thread_post_start.php

            if ($tid) {

                // hook verify_thread_post_before.php

                $thread = well_thread_read_cache($tid);
                well_verify_thread($thread, $verified);

                0 != $thread['status'] && 1 == $verified AND $n = 1;

                // hook verify_thread_post_verified.php

            } else {

                $tidarr = param('tid', array(0));
                empty($tidarr) AND message(1, lang('please_choose_thread'));

                // hook verify_thread_post_center.php

                $threadlist = well_thread_find_by_tids($tidarr);

                // hook verify_thread_post_middle.php

                $n = 0;
                foreach ($threadlist as $thread) {
                    if (0 == $thread['status']) continue;
                    ++$n;
                    // hook verify_thread_post_foreach.php
                    well_verify_thread($thread, $verified);
                }

                // hook verify_thread_post_foreach_after.php
            }

            // hook verify_thread_post_after.php

            if (1 == $verified && $n) {
                // hook verify_thread_post_runtime_start.php

                // 全站主题数增加
                runtime_set('articles+', $n);
                runtime_set('todayarticles+', $n);

                // hook verify_thread_post_runtime_end.php
            }

            // hook verify_thread_post_end.php

            message(0, lang('update_successfully'));
        }

        // hook verify_thread_end.php

        break;
    case 'comment': // 审核评论
        // hook verify_comment_start.php

        if ('GET' == $method) {

            $page = param(2, 1);
            $pagesize = 25;
            $safe_token = well_token_set($uid);
            $extra = array(); // 插件预留

            // hook verify_comment_get_start.php

            $postlist = well_comment_verify_find($page, $pagesize);

            // hook verify_comment_get_before.php

            $page_url = url('verify-comment-{page}', $extra);
            $num = well_comment_verify_count();

            // hook verify_comment_get_center.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $extra['safe_token'] = $safe_token;
            $header['title'] = lang('well_publish_verify_comment');

            // hook verify_comment_get_end.php

            if ($ajax) {
                if ($postlist) {
                    foreach ($postlist as &$_post) $_post = comment_filter($_post);
                }

                $apilist['header'] = $header;
                $apilist['member_navlist'] = $member_navs;
                $apilist['member_menulist'] = $member_menus;
                $apilist['extra'] = $extra;
                $apilist['num'] = $num;
                $apilist['page'] = $page;
                $apilist['pagesize'] = $pagesize;
                $apilist['page_url'] = $page_url;
                $apilist['safe_token'] = $safe_token;
                $apilist['postlist'] = $postlist;

                $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
            } else {
                include _include(theme_load('verify_comment', '', 'well_publish'));
            }
        } elseif ('POST' == $method) {

            // 验证token
            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));

            // 支持批量 1通过
            $pid = param(2, 0);
            $n = 0;

            // hook verify_comment_post_start.php

            if ($pid) {

                // hook verify_comment_post_before.php

                $comment = comment_read($pid);
                well_verify_comment($comment);

                0 != $comment['status'] AND $n = 1;

                // hook verify_comment_post_center.php

            } else {

                $pidarr = param('pid', array(0));
                empty($pidarr) AND message(1, lang('please_choose_thread'));

                // hook verify_comment_post_find_before.php

                $commentlist = comment_find($pidarr, count($pidarr), FALSE);
                $n = 0;
                // hook verify_comment_post_find_after.php
                foreach ($commentlist as $comment) {
                    if (0 == $comment['status']) continue;
                    ++$n;
                    // hook verify_comment_post_foreach.php
                    well_verify_comment($comment);
                }
            }

            // hook verify_comment_post_after.php

            // 全站评论数增加
            if ($n) {
                // hook verify_comment_post_runtime_start.php
                runtime_set('comments+', $n);
                runtime_set('todaycomments+', $n);
                // hook verify_comment_post_runtime_end.php
            }

            // hook verify_comment_post_end.php

            message(0, lang('update_successfully'));
        }

        // hook verify_comment_end.php
        break;
    case 'reject':
        // hook verify_reject_start.php

        if ('GET' == $method) {

            $page = param(2, 1);
            $pagesize = 25;
            $safe_token = well_token_set($uid);
            $extra = array(); // 插件预留

            // hook verify_reject_get_start.php

            $threadlist = well_thread_reject_find($page, $pagesize);

            // hook verify_reject_get_before.php

            $page_url = url('verify-reject-{page}', $extra);
            $num = well_thread_reject_count();

            // hook verify_reject_get_center.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $extra['safe_token'] = $safe_token;
            $header['title'] = lang('well_publish_reject');

            // hook verify_reject_get_end.php

            if ($ajax) {
                $apilist['header'] = $header;
                $apilist['member_navlist'] = $member_navs;
                $apilist['member_menulist'] = $member_menus;
                $apilist['extra'] = $extra;
                $apilist['num'] = $num;
                $apilist['page'] = $page;
                $apilist['pagesize'] = $pagesize;
                $apilist['page_url'] = $page_url;
                $apilist['safe_token'] = $safe_token;
                $apilist['threadlist'] = $threadlist;

                $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
            } else {
                include _include(theme_load('verify_reject', '', 'well_publish'));
            }
        } elseif ('POST' == $method) {
            // 验证token
            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));

            // 支持批量 1通过
            $tid = param(2, 0);

            // hook verify_reject_post_start.php

            if ($tid) {
                // hook verify_reject_post_before.php
                $thread = well_thread_read_cache($tid);
                well_verify_reject($thread);
                // hook verify_reject_post_center.php
            } else {

                $tidarr = param('tid', array(0));
                empty($tidarr) AND message(1, lang('please_choose_thread'));

                $threadlist = well_thread_find_by_tids($tidarr);

                // hook verify_reject_post_middle.php

                foreach ($threadlist as $thread) {
                    if (0 == $thread['status']) continue;
                    // hook verify_reject_post_foreach.php
                    well_verify_reject($thread);
                }

                // hook verify_reject_post_after.php
            }

            // hook verify_reject_post_end.php

            message(0, lang('update_successfully'));
        }

        // hook verify_reject_end.php

        break;
    // hook verify_case_end.php
}

function well_verify_thread($thread, $verified)
{
    global $uid, $time, $config, $forumlist;

    // hook verify_thread_func_start.php

    $tid = $thread['tid'];
    $_uid = $thread['uid'];
    $fid = $thread['fid'];
    $subject = $thread['subject'];
    $forum = array_value($forumlist, $fid);
    if (!$forum) return FALSE;

    // hook verify_thread_func_before.php

    if (1 == $verified) {

        switch (array_value($forum, 'model')) {
            // 需要时自行增加，根据模型写入不同小表
            // hook verify_thread_func_model_start.php
            case '0':
                // hook verify_thread_func_verified_start.php

                // 通过审核 创建总主题小表 如需扁平首页显示，总主题小表必须写入
                thread_tid_create(array('tid' => $tid, 'fid' => $fid, 'uid' => $_uid, 'verify_date' => $time));

                $user_update = array('well_verify_threads-' => 1, 'articles+' => 1);
                $operate_create = array('type' => 24);
                $thread_update = array('status' => 0);

                // hook verify_thread_func_verified_before.php
                break;
            // hook verify_thread_func_model_end.php
        }

        // 版块主题数增加
        forum_update($fid, array('threads+' => 1, 'todaythreads+' => 1));

        // 门户模式删除首页所有缓存
        if (1 == array_value($config, 'model')) {
            cache_delete('portal_index_thread');
            cache_delete('portal_channel_thread_' . $fid);
        }

        well_thread_verify_delete($tid);

        // hook verify_thread_func_verified_end.php

    } else {

        // hook verify_thread_func_reject_start.php

        // 创建退稿表
        well_thread_reject_create(array('tid' => $tid, 'uid' => $_uid));

        if (0 == $thread['status']) {
            // 版块主题数-1
            forum_update($fid, array('threads-' => 1));

            thread_tid_delete($tid);
        }

        // hook verify_thread_func_reject_before.php

        // 用户待审-1 退稿+1
        $user_update = array('well_verify_threads-' => 1, 'well_publish_rejects+' => 1);

        $operate_create = array('type' => 25);
        $thread_update = array('status' => 10);

        // hook verify_thread_func_reject_after.php
    }

    // hook verify_thread_func_center.php

    user_update($_uid, $user_update);

    // hook verify_thread_func_after.php

    // 更改主题状态
    well_thread_update($tid, $thread_update);

    include_once _include(APP_PATH . 'model/operate.func.php');
    $operate_create += array('uid' => $uid, 'tid' => $tid, 'subject' => $subject, 'create_date' => $time);
    operate_create($operate_create);

    // hook verify_thread_func_end.php

    return TRUE;
}

function well_verify_comment($comment)
{
    global $uid, $time;

    // hook verify_comment_func_start.php

    $pid = $comment['pid'];
    $fid = $comment['fid'];
    $tid = $comment['tid'];
    $_uid = $comment['uid'];

    // hook verify_comment_func_before.php

    // 创建回复小表
    $arr = array('pid' => $pid, 'fid' => $fid, 'tid' => $tid, 'uid' => $_uid);
    // hook verify_comment_func_create.php
    comment_pid_create($arr);

    // 用户待验证评论-1 评论+1
    $user_update = array('well_verify_comments-' => 1, 'comments+' => 1);
    // hook verify_comment_func_user.php
    user_update($_uid, $user_update);

    // 最后回复uid
    $update = array('posts+' => 1, 'last_date' => $time, 'lastuid' => $_uid);
    // hook verify_comment_func_thread.php
    well_thread_update($tid, $update);

    // hook verify_comment_func_thread_after.php

    // 更新主题表最后回复pid
    thread_tid_update_lastpid($tid, $pid);

    comment_update($pid, array('status' => 0));

    // hook verify_comment_func_lastpid_after.php

    well_comment_verify_delete($pid);

    // 版块评论数增加
    $update = array('todayposts+' => 1);
    // hook verify_comment_func_forum.php
    forum_update($fid, $update);

    // 创建审核日志
    include_once _include(APP_PATH . 'model/operate.func.php');
    $arr = array('type' => 26, 'uid' => $uid, 'tid' => $tid, 'pid' => $pid, 'subject' => $comment['subject'], 'create_date' => $time);
    operate_create($arr);

    // hook verify_comment_func_end.php
}

function well_verify_reject($thread)
{
    global $uid, $time, $config;

    // hook verify_reject_func_start.php

    $tid = $thread['tid'];
    $_uid = $thread['uid'];
    $fid = $thread['fid'];
    $subject = $thread['subject'];

    // hook verify_reject_func_before.php

    // 通过审核 创建小表
    thread_tid_create(array('tid' => $tid, 'fid' => $fid, 'uid' => $_uid, 'verify_date' => $time));

    // 版块主题数增加
    $fid AND forum_update($fid, array('threads+' => 1, 'todaythreads+' => 1));

    // 门户模式删除首页所有缓存
    if (1 == array_value($config, 'model')) {
        cache_delete('portal_index_thread');
        $fid AND cache_delete('portal_channel_thread_' . $fid);
    }

    // hook verify_reject_func_center.php

    well_thread_reject_delete($tid);

    // hook verify_reject_func_middle.php

    user_update($_uid, array('well_publish_rejects-' => 1, 'articles+' => 1));

    // hook verify_reject_func_after.php

    // 更改主题状态
    well_thread_update($tid, array('status' => 0));

    include_once _include(APP_PATH . 'model/operate.func.php');
    $operate_create = array('type' => 24, 'uid' => $uid, 'tid' => $tid, 'subject' => $subject, 'create_date' => $time);
    operate_create($operate_create);

    // hook verify_reject_func_end.php
}

// hook verify_end.php

?>