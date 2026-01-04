<?php exit;
case 'latest': // 最新回复评论主题，按照最后回复排序

        // hook home_latest_start.php

        if ('GET' == $method) {

            $page = param(2, 1);
            $pagesize = 20;
            $extra = array(); // 插件预留
            $num = 0;

            // hook home_latest_get_start.php

            $allowdelete = group_access($gid, 'allowdelete') || group_access($gid, 'allowuserdelete') || 1 == $gid;
            $access = array('allowdelete' => $allowdelete);

            // 从默认的地方读取主题列表
            $latest_list_from_default = 1;

            // hook home_latest_from_default_start.php

            if (1 == $latest_list_from_default) {

                // hook home_latest_from_default_before.php

                $threadlist = well_thread_find_by_uid_lastpid($uid, $page, $pagesize);

                // hook home_latest_from_default_center.php

                $num = $user['articles'];

                // hook home_latest_from_default_after.php
            }

            // hook home_latest_from_default_end.php

            $page_url = url('home-latest-{page}', $extra);

            // hook home_latest_get_before.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $header['title'] = lang('well_latest_news');
            $safe_token = well_token_set($uid);

            // hook home_latest_get_end.php

            if ('1' == _GET('ajax')) {
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
                include _include(theme_load('home_latest', '', 'well_publish'));
            }
        }

        // hook home_latest_end.php

        break;
    case 'content': // 用户创建、编辑、删除主题

        // 配置数据
        $well_publish_conf = setting_get('well_publish');

        // hook home_content_start.php

        // 用户是否写作有权限
        FALSE === group_access($gid, 'allowthread') AND message(1, lang('user_group_insufficient_privilege'));

        // 返回栏目数据(仅列表)
        $forumlist = category_list($forumlist);

        // hook home_content_before.php

        // 过滤用户具有写作权限的版块
        foreach ($forumlist as $_fid => $_forum) {
            if (group_access($gid, 'managecontent')) continue;
            if (0 != $_forum['category'] || 1 != $_forum['well_publish']) {
                unset($forumlist[$_fid]);
            }

            if (empty($group['allowthread']) || !forum_access_user($_fid, $gid, 'allowthread')) {
                unset($forumlist[$_fid]);
            }

            // hook home_content_forumlist_foreach.php
        }

        // hook home_content_center.php

        empty($forumlist) AND message(1, lang('well_publish_not_open'));

        // 过滤版块相关数据
        $forumlist = forum_filter($forumlist);
        $forumlist_show = $forumlist;

        $op = param('op');

        // hook home_content_middle.php

        switch ($op) {
            // hook home_content_case_start.php
            case 'create':

                $publish_setting = setting_get('well_publish');
                // 投稿限制，超过之后必须等待审核后才能继续投稿
                FALSE === group_access($gid, 'managecontent') && $publish_setting['enable_publish_limit'] && $user['well_verify_threads'] >= $publish_setting['enable_publish_limit'] AND message(1, lang('well_publish_publish_limit_tips', array('n' => $publish_setting['enable_publish_limit'])));

                // 退稿限制，达到限制，必须编辑退稿或删除退稿才能继续投稿
                $publish_setting['enable_reject_limit'] && $user['well_publish_rejects'] >= $publish_setting['enable_reject_limit'] AND message(1, lang('well_publish_reject_limit_tips', array('n' => $publish_setting['enable_publish_limit'])));

                // hook home_content_create_start.php

                if ('GET' == $method) {

                    $safe_token = well_token_set($uid);
                    $referer = http_referer();
                    $extra = array('safe_token' => $safe_token);

                    $fid = param('fid', 0);
                    $forum = array_value($forumlist, $fid);
                    empty($forum) and $fid = 0;

                    // hook home_content_create_get_start.php

                    $managecontent = group_access($gid, 'managecontent');
                    $access = array('managecontent' => $managecontent, 'flag' => '');
                    if ($managecontent) {
                        $forum_flagids = array();
                        $category_flagids = array();
                        $index_flagids = array();
                        $index_flag = flag_forum_show(0);
                        $index_flag AND flag_filter($index_flag);
                        $access['flag'] = array('forum' => $forum_flagids, 'category' => $category_flagids, 'index' => $index_flagids, 'index_flag' => $index_flag);
                    }

                    $access['allowthumbnail'] = $allowthumbnail = group_access($gid, 'allowthumbnail');
                    $access['allowattach'] = $allowattach = group_access($gid, 'allowattach');
                    $access['allowbrief'] = $allowbrief = group_access($gid, 'allowbrief');
                    $access['allow_auto_brief'] = $allow_auto_brief = group_access($gid, 'allow_auto_brief');
                    $access['allowkeywords'] = $allowkeywords = group_access($gid, 'allowkeywords');
                    $access['allowdescription'] = $allowdescription = group_access($gid, 'allowdescription');

                    // hook home_content_create_get_before.php

                    // 获取主图
                    $thumbnail = view_path() . 'img/nopic.png';

                    $picture = $forum ? $forum['thumbnail'] : $config['picture_size'];
                    $pic_width = $picture['width'];
                    $pic_height = $picture['height'];

                    // hook home_content_create_get_center.php

                    $input = $filelist = array();
                    $form_title = lang('well_publish_article');
                    $form_action = url('home-content', array('op' => 'create', 'safe_token' => $safe_token));
                    $form_thumbnailDelete = url('home-content', array('op' => 'thumbnailDelete', 'safe_token' => $safe_token));
                    $form_submit_txt = lang('submit');
                    $form_subject = $form_message = $form_brief = $form_closed = $form_keyword = $form_description = $tagstr = '';

                    // 是否开启上传
                    $allowattach = group_access($gid, 'allowattach');
                    $access['allowattach'] = $allowattach;

                    // hook home_content_create_get_middle.php

                    $setting = array_value($config, 'setting');
                    $form_doctype = 0;
                    $form = array('action' => $form_action, 'thumbnail' => $thumbnail, 'picture' => array('width' => $pic_width, 'height' => $pic_height), 'submit_txt' => $form_submit_txt, 'doctype' => $form_doctype, 'subject' => $form_subject, 'message' => $form_message, 'brief' => $form_brief, 'closed' => $form_closed, 'title' => $form_title, 'keyword' => $form_keyword, 'description' => $form_description);

                    // 初始化附件
                    $_SESSION['tmp_thumbnail'] = $_SESSION['tmp_website_files'] = array();

                    // hook home_content_create_get_after.php

                    $breadcrumb_flag = lang('well_publish_article');
                    $header['title'] = lang('well_publish_article');
                    $header['mobile_title'] = lang('well_publish_article');
                    $header['mobile_link'] = url('home-latest');

                    // hook home_content_create_get_end.php

                    if ('1' == _GET('ajax')) {
                        $apilist['header'] = $header;
                        $apilist['member_navlist'] = $member_navs;
                        $apilist['member_menulist'] = $member_menus;
                        $apilist['extra'] = $extra;
                        $apilist['safe_token'] = $safe_token;
                        $apilist['forum'] = $forum;
                        $apilist['form'] = $form;
                        $apilist['tagstr'] = $tagstr;
                        $apilist['access'] = $access;
                        $apilist['picture'] = $picture;
                        $apilist['referer'] = $referer;
                        $apilist['thread'] = '';
                        $apilist['thread_data'] = '';

                        $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
                    } else {
                        include _include(theme_load('home_content_post', '', 'well_publish'));
                    }

                } elseif ('POST' == $method) {

                    // 验证token
                    if (array_value($conf, 'message_token')) {
                        $safe_token = param('safe_token');
                        FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));
                    }

                    // 统一更新主题数据
                    $thread_update = array();
                    // 统一更新用户数据
                    $user_update = array();

                    // hook home_content_create_post_start.php

                    $fid = param('fid', 0);
                    $forum = array_value($forumlist, $fid);
                    empty($forum) AND message(-1, lang('forum_not_exists'));

                    // hook home_content_create_post_forum_after.php

                    // 普通用户权限判断
                    !forum_access_user($fid, $gid, 'allowthread') AND message(1, lang('user_group_insufficient_privilege'));

                    // hook home_content_create_post_access_after.php

                    $subject = param('subject');
                    $subject = filter_all_html($subject);
                    empty($subject) AND message('subject', lang('please_input_subject'));
                    xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
                    // 过滤标题 关键词

                    // hook home_content_create_post_subject_after.php

                    $type = 0;
                    // 主图和本地化图片完全后台设置控制
                    $setting = array_value($config, 'setting');
                    $thumbnail = array_value($setting, 'thumbnail_on', 0);
                    !group_access($gid, 'allowthumbnail') and $thumbnail = 0;
                    $save_image = array_value($setting, 'save_image_on', 0);

                    $brief_auto = param('brief_auto', 0);
                    $closed = param('closed', 0);
                    if (1 < $closed && !forum_access_user($fid, $gid, 'allowdelete')) message(1, lang('illegal_operation'));

                    $doctype = param('doctype', 0);
                    $doctype > 10 AND message(1, lang('doc_type_not_supported'));

                    // hook home_content_create_post_before.php

                    $message = param('message', '', FALSE);
                    $message = trim($message);
                    empty($message) ? message('message', lang('please_input_message')) : xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));

                    // 过滤所有html标签
                    $_message = htmlspecialchars_decode($message);
                    $_message = filter_all_html($_message);
                    $_message = htmlspecialchars($_message, ENT_QUOTES);

                    // 过滤内容 关键词
                    // hook home_content_create_post_message_after.php

                    // hook home_content_create_post_brief_start.php

                    if (group_access($gid, 'allowbrief') || group_access($gid, 'allow_auto_brief')) {
                        $brief = param('brief');
                        if ($brief) {
                            // 过滤简介 关键词
                            // hook home_content_create_post_brief_before.php

                            xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
                        } else {
                            $brief = ($brief_auto AND $_message) ? xn_substr($_message, 0, 120) : '';
                        }
                    } else {
                        $brief = '';
                    }

                    // hook home_content_create_post_brief_end.php

                    if (group_access($gid, 'allowkeywords')) {
                        $keyword = param('keyword');
                        // 过滤内容 关键词
                        // hook home_content_create_post_keyword_before.php
                        // 超出则截取
                        xn_strlen($keyword) > 64 AND $keyword = xn_substr($keyword, 0, 64);
                    } else {
                        $keyword = '';
                    }

                    // hook home_content_create_post_description_before.php

                    if (group_access($gid, 'allowdescription')) {
                        $description = param('description');
                        // 过滤内容 关键词
                        // hook home_content_create_post_description_center.php
                        // 超出则截取
                        xn_strlen($description) > 120 AND $description = xn_substr($description, 0, 120);
                    } else {
                        $description = '';
                    }

                    // hook home_content_create_post_description_after.php

                    if (group_access($gid, 'managecontent') || $well_publish_conf['enable_tag']) {
                        $tags = param('tags', '', FALSE);
                        $tags = xn_html_safe(filter_all_html($tags));
                    } else {
                        $tags = '';
                    }

                    // 过滤标签 关键词
                    // hook home_content_create_post_tag_after.php

                    if (group_access($gid, 'managecontent')) {
                        // 首页flag
                        $flag_index_arr = array_filter(param('index', array()));
                        // 频道flag
                        $flag_cate_arr = array_filter(param('category', array()));
                        // 栏目flag
                        $flag_forum_arr = array_filter(param('forum', array()));
                        // 统计主题绑定flag数量
                        $flags = count($flag_index_arr) + count($flag_cate_arr) + count($flag_forum_arr);
                    } else {
                        $flags = 0;
                    }

                    // hook home_content_create_post_flags.php

                    $thread = array(
                        'fid' => $fid,
                        'type' => $type,
                        'doctype' => $doctype,
                        'subject' => $subject,
                        'brief' => $brief,
                        'keyword' => $keyword,
                        'description' => $description,
                        'closed' => $closed,
                        'flags' => $flags,
                        'thumbnail' => $thumbnail,
                        'save_image' => $save_image,
                        'delete_pic' => 0,
                        'message' => $message,
                        'time' => $time,
                        'longip' => $longip,
                        'gid' => $gid,
                        'uid' => $uid,
                        'conf' => $conf,
                    );

                    group_access($gid, 'publishverify') && 1 != $gid AND $thread['status'] = 1;

                    // hook home_content_create_post_middle.php

                    $result = thread_create_handle($thread);
                    FALSE === $result and message(-1, lang('create_thread_failed'));
                    unset($thread);
                    $tid = $result['tid'];
                    $result['icon'] and $thread_update['icon'] = $result['icon'];
                    $result['images'] and $thread_update['images'] = $result['images'];
                    $result['files'] and $thread_update['files'] = $result['files'];

                    !empty($result['user_update']) and $user_update += $result['user_update'];

                    // hook home_content_create_post_after.php

                    if (group_access($gid, 'managecontent') || $well_publish_conf['enable_tag']) {
                        $tag_json = well_tag_post($tid, $fid, $tags);
                        if (xn_strlen($tag_json) >= 120) {
                            $s = xn_substr($tag_json, -1, NULL);
                            if ('}' != $s) {
                                $len = mb_strripos($tag_json, ',', 0, 'UTF-8');
                                $tag_json = $len ? xn_substr($tag_json, 0, $len) . '}' : '';
                            }
                        }

                        !empty($tag_json) AND $thread_update['tag'] = $tag_json;
                    }

                    // hook home_content_create_post_thread_update.php

                    if (group_access($gid, 'managecontent')) {
                        // 首页flag
                        !empty($flag_index_arr) && FALSE === flag_create_thread(0, 1, $tid, $flag_index_arr) AND message(-1, lang('create_failed'));

                        // 频道flag
                        $forum['fup'] && !empty($flag_cate_arr) && FALSE === flag_create_thread($forum['fup'], 2, $tid, $flag_cate_arr) AND message(-1, lang('create_failed'));

                        // 栏目flag
                        !empty($flag_forum_arr) && FALSE === flag_create_thread($fid, 3, $tid, $flag_forum_arr) AND message(-1, lang('create_failed'));
                    }

                    // hook home_content_create_post_end.php

                    !empty($thread_update) && FALSE === well_thread_update($tid, $thread_update) AND message(-1, lang('update_thread_failed'));

                    !empty($user_update) && FALSE === user_update($uid, $user_update) AND message(-1, lang('update_failed'));

                    // hook home_content_create_post_message.php

                    message(0, lang('create_successfully'));
                }
                break;
            case 'update':

                // hook home_content_update_start.php

                $tid = param('tid', 0);
                empty($tid) AND message(1, lang('data_malformation'));

                $thread = well_thread_read($tid);
                empty($thread) AND message(-1, lang('thread_not_exists'));

                $fid = $thread['fid'];
                $forum = array_value($forumlist, $fid);
                empty($forum) and message('fid', lang('forum_not_exists'));

                // hook home_content_update_before.php

                // 编辑权限
                $allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
                !$allowupdate && !$thread['allowupdate'] AND message(-1, lang('have_no_privilege_to_update'));
                !$allowupdate && $thread['closed'] AND message(-1, lang('thread_has_already_closed'));

                // hook home_content_update_center.php

                $thread_data = data_read($tid);

                // hook home_content_update_after.php

                $managecontent = group_access($gid, 'managecontent');
                if ($managecontent) {
                    // 主题绑定了哪些flag array(1,2,3)
                    list($index_flagids, $category_flagids, $forum_flagids, $flagarr) = flag_forum_by_tid($tid);
                }

                // hook home_content_update_end.php

                if ('GET' == $method) {

                    $safe_token = well_token_set($uid);
                    $referer = http_referer();
                    // 插件预留
                    $extra = array('tid' => $tid, 'safe_token' => $safe_token);

                    // hook home_content_update_get_start.php

                    $thread_data['message'] = htmlspecialchars($thread_data['message']);
                    ($uid != $thread['uid']) AND $thread_data['message'] = xn_html_safe($thread_data['message']);

                    // hook home_content_update_get_forum_after.php

                    $access = array('managecontent' => $managecontent, 'flag' => '');
                    if ($managecontent) {
                        $index_flag = flag_forum_show(0);
                        $index_flag AND flag_filter($index_flag);
                        $access['flag'] = array('forum' => $forum_flagids, 'category' => $category_flagids, 'index' => $index_flagids, 'index_flag' => $index_flag, 'flagarr' => $flagarr);
                    }

                    $access['allowthumbnail'] = $allowthumbnail = group_access($gid, 'allowthumbnail');
                    $access['allowattach'] = $allowattach = group_access($gid, 'allowattach');
                    $access['allowbrief'] = $allowbrief = group_access($gid, 'allowbrief');
                    $access['allow_auto_brief'] = $allow_auto_brief = group_access($gid, 'allow_auto_brief');
                    $access['allowkeywords'] = $allowkeywords = group_access($gid, 'allowkeywords');
                    $access['allowdescription'] = $allowdescription = group_access($gid, 'allowdescription');

                    // hook home_content_update_get_flag_after.php

                    // 初始化附件
                    $_SESSION['tmp_thumbnail'] = $_SESSION['tmp_website_files'] = array();

                    // hook home_content_update_get_icon_after.php

                    $picture = $forum['thumbnail'];
                    $pic_width = $picture['width'];
                    $pic_height = $picture['height'];

                    // hook home_content_update_get_files_before.php

                    $attachlist = array();
                    $imagelist = array();
                    $input = array();
                    $filelist = array();

                    // 是否开启上传
                    $allowattach = forum_access_user($fid, $gid, 'allowattach');
                    $access['allowattach'] = $allowattach;
                    if ($allowattach) {
                        $thread['files'] AND list($attachlist, $imagelist, $filelist) = well_attach_find_by_tid($tid, $thread['files']);
                        $apilist['attach'] = array('attachlist' => $attachlist, 'imagelist' => $imagelist, 'filelist' => $filelist);
                    }

                    if (group_access($gid, 'managecontent') || $well_publish_conf['enable_tag']) {
                        $tagstr = $thread['tag_fmt'] ? implode(',', $thread['tag_fmt']) . ',' : '';
                    }

                    // hook home_content_update_get_files_after.php

                    $form_thumbnailDelete = url('home-content', array('op' => 'thumbnailDelete', 'tid' => $tid, 'safe_token' => $safe_token));
                    $form_title = lang('edit');
                    $form_action = url('home-content', array('op' => 'update', 'tid' => $tid) + $extra);
                    $form_submit_txt = lang('submit');
                    $form_subject = $thread['subject'];
                    $form_message = $thread_data['message'];
                    $form_brief = $thread['brief'];
                    $form_doctype = $thread_data['doctype'];
                    $form_closed = $thread['closed'] >= 1 ? 'checked="checked"' : '';
                    $form_keyword = $thread['keyword'];
                    $form_description = $thread['description'];

                    empty($filelist) || $filelist += (array)_SESSION('tmp_website_files');
                    $thumbnail = $thread['icon_fmt'];

                    $form = array('action' => $form_action, 'thumbnail' => $thumbnail, 'picture' => array('width' => $pic_width, 'height' => $pic_height), 'submit_txt' => $form_submit_txt, 'doctype' => $form_doctype, 'subject' => $form_subject, 'message' => $form_message, 'brief' => $form_brief, 'closed' => $form_closed, 'title' => $form_title, 'keyword' => $form_keyword, 'description' => $form_description);

                    // hook home_content_update_get_after.php

                    $breadcrumb_flag = lang('well_publish_edit');
                    $header['title'] = lang('well_publish_edit');
                    $header['mobile_title'] = lang('well_publish_edit');
                    $header['mobile_link'] = url('home-latest');

                    // hook home_content_update_get_end.php

                    if ('1' == _GET('ajax')) {
                        $apilist['header'] = $header;
                        $apilist['member_navlist'] = $member_navs;
                        $apilist['member_menulist'] = $member_menus;
                        $apilist['extra'] = $extra;
                        $apilist['safe_token'] = $safe_token;
                        $apilist['thread'] = $thread;
                        $apilist['thread_data'] = $thread_data;
                        $apilist['forum'] = $forum;
                        $apilist['picture'] = $picture;
                        $apilist['form'] = $form;
                        $apilist['tagstr'] = $tagstr;
                        $apilist['access'] = $access;
                        $apilist['referer'] = $referer;

                        $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
                    } else {
                        include _include(theme_load('home_content_post', '', 'well_publish'));
                    }

                } elseif ('POST' == $method) {
                    // 验证token
                    if (array_value($conf, 'message_token', 0)) {
                        $safe_token = param('safe_token');
                        FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));
                    }

                    // hook home_content_update_post_start.php

                    // 统一更新用户数据
                    $user_update = array();
                    // 主题更新
                    $update = array();
                    $forum_update = array();

                    $thread_default = param('thread_default', 'default'); // 默认入库
                    $subject = param('subject');
                    $subject = filter_all_html($subject);
                    empty($subject) AND message('subject', lang('please_input_subject'));

                    xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
                    // 过滤标题 关键词

                    // hook home_content_update_post_subject_before.php

                    $subject_edit = FALSE;
                    if ($subject != $thread['subject']) {
                        $update['subject'] = $subject;
                        $thread['sticky'] > 0 AND cache_delete('sticky_thread_list');
                        $subject_edit = TRUE;
                    }

                    // hook home_content_update_post_subject_after.php

                    $closed = param('closed', 0);
                    if (1 < $closed && !forum_access_user($fid, $gid, 'allowdelete')) message(1, lang('illegal_operation'));
                    $closed != $thread['closed'] AND $update['closed'] = $closed;

                    // hook home_content_update_post_closed_after.php

                    $doctype = param('doctype', 0);
                    $doctype > 10 AND message(1, lang('doc_type_not_supported'));

                    // hook home_content_update_post_message_before.php

                    $message = param('message', '', FALSE);
                    $message = trim($message);
                    empty($message) ? message('message', lang('please_input_message')) : xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));

                    $_message = htmlspecialchars_decode($message);
                    $_message = filter_all_html($_message);
                    $_message = htmlspecialchars($_message, ENT_QUOTES);
                    // 过滤内容 关键词
                    // hook home_content_update_post_message_center.php

                    // hook home_content_update_post_message_after.php

                    if (group_access($gid, 'allowbrief') || group_access($gid, 'allow_auto_brief')) {
                        $brief_auto = param('brief_auto', 0);
                        $brief = param('brief');
                        if ($brief) {
                            // 过滤简介 关键词
                            // hook home_content_update_post_brief_before.php

                            xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
                        } else {
                            $brief = ($brief_auto AND $_message) ? xn_html_safe(xn_substr($_message, 0, 120)) : '';
                        }
                        $brief and $brief = filter_all_html($brief);

                        // hook home_content_update_post_brief_after.php

                        $brief != $thread['brief'] AND $update['brief'] = $brief;
                    }

                    // hook home_content_update_post_keyword_before.php

                    if (group_access($gid, 'allowkeywords')) {
                        $keyword = param('keyword');
                        // 过滤内容 关键词
                        // hook home_content_update_post_keyword_center.php
                        // 超出则截取
                        xn_strlen($keyword) > 64 AND $keyword = xn_substr($keyword, 0, 64);

                        $keyword != $thread['keyword'] AND $update['keyword'] = $keyword;
                    }

                    // hook home_content_update_post_keyword_after.php

                    if (group_access($gid, 'allowdescription')) {
                        $description = param('description');
                        // 过滤内容 关键词
                        // hook home_content_update_post_description_before.php
                        // 超出则截取
                        xn_strlen($description) > 120 AND $description = xn_substr($description, 0, 120);
                        $description != $thread['description'] AND $update['description'] = $description;
                    }

                    // hook home_content_update_post_fid_before.php

                    $newfid = param('fid', 0);
                    $forum = array_value($forumlist, $newfid);
                    empty($forum) and message('fid', lang('forum_not_exists'));

                    // hook home_content_update_post_fid_center.php

                    $forum_threads = 0;
                    if ($fid != $newfid) {

                        $forum_threads = 1;
                        0 == $thread['status'] and $forum_update['threads+'] = 1;

                        // hook home_content_update_post_fid_access.php

                        if ($thread['uid'] != $uid && !forum_access_mod($thread['fid'], $gid, 'allowupdate')) message(1, lang('user_group_insufficient_privilege'));

                        // hook home_content_update_post_fid_update.php

                        0 == $thread['status'] and forum__update($thread['fid'], array('threads-' => 1));
                        sticky_thread_update_by_tid($tid, $newfid);

                        thread_tid_update($tid, $newfid);

                        $update['fid'] = $newfid;
                        $fid = $newfid;
                    }

                    // hook home_content_update_post_fid_after.php

                    $upload_thumbnail = well_attach_assoc_type('thumbnail');
                    if (group_access($gid, 'allowthumbnail') && !empty($upload_thumbnail)) {

                        // 关联主图 assoc thumbnail主题主图 post:内容图片或附件
                        $thumbnail_assoc = array('tid' => $tid, 'uid' => $uid);
                        // hook home_content_update_post_attach_before.php
                        $return = well_attach_assoc_thumbnail($thumbnail_assoc);
                        unset($thumbnail_assoc);

                        if ($return) {
                            // Ym变更删除旧图
                            $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');
                            $old_day = $thread['icon'] ? date($attach_dir_save_rule, $thread['icon']) : '';

                            // hook home_content_update_post_unlink_before.php

                            $file = $conf['upload_path'] . 'thumbnail/' . $old_day . '/' . $thread['uid'] . '_' . $tid . '_' . $thread['icon'] . '.jpeg';
                            is_file($file) AND unlink($file);

                            // hook home_content_update_post_unlink_after.php

                            $update['icon'] = $time;
                        }

                        // hook home_content_update_post_attach_before.php
                    }

                    // hook home_content_update_post_attach_after.php

                    if (group_access($gid, 'managecontent') || $well_publish_conf['enable_tag']) {
                        $tags = param('tags', '', FALSE);
                        $tags = xn_html_safe(filter_all_html($tags));

                        // 过滤标签 关键词
                        // hook home_content_update_post_tag_center.php

                        $tag_json = well_tag_post_update($tid, $fid, $tags, $thread['tag_fmt']);
                        if (xn_strlen($tag_json) >= 120) {
                            $s = xn_substr($tag_json, -1, NULL);
                            if ('}' != $s) {
                                $len = mb_strripos($tag_json, ',', 0, 'UTF-8');
                                $tag_json = $len ? xn_substr($tag_json, 0, $len) . '}' : '';
                            }
                        }

                        $tag_json != $thread['tag'] AND $update['tag'] = $tag_json;
                    }

                    // hook home_content_update_post_tag_after.php

                    if (group_access($gid, 'managecontent')) {
                        // 首页flag
                        $flag_index_arr = array_filter(param('index', array()));
                        // 首页需要再创建的
                        $new_index_flagids = empty($flag_index_arr) ? array() : array_diff($flag_index_arr, $index_flagids);
                        // 返回首页被取消的flagid
                        $old_index_flagids = array_diff($index_flagids, $flag_index_arr);

                        // 频道flag
                        $flag_cate_arr = array_filter(param('category', array()));
                        // 频道需要再创建的
                        $new_cate_flagids = empty($flag_cate_arr) ? array() : array_diff($flag_cate_arr, $category_flagids);
                        // 返回频道被取消的flagid
                        $old_cate_flagids = array_diff($category_flagids, $flag_cate_arr);

                        // 栏目flag
                        $flag_forum_arr = array_filter(param('forum', array()));
                        // 需要再创建的
                        $new_forum_flagids = empty($flag_forum_arr) ? array() : array_diff($flag_forum_arr, $forum_flagids);
                        // 返回被取消的flagid
                        $old_forum_flagids = array_diff($forum_flagids, $flag_forum_arr);

                        $flags = $thread['flags'] + count($new_index_flagids) + count($new_cate_flagids) + count($new_forum_flagids) - count($old_index_flagids) - count($old_cate_flagids) - count($old_forum_flagids);
                        $thread['flags'] != $flags AND $update['flags'] = $flags;
                    }

                    // hook home_content_update_post_arr_after.php

                    $new_md5 = md5($message);
                    $old_md5 = md5($thread_data['message']);
                    if ('default' == $thread_default && ($new_md5 != $old_md5 || TRUE == $subject_edit)) {
                        // 通过审核的内容，有更新进入审核
                        if (1 != $thread['status'] && group_access($gid, 'publishverify') && !group_access($gid, 'managecreatethread')) {
                            // hook home_content_update_verify_before.php

                            $update['status'] = 1;

                            thread_tid_delete($thread['tid']);

                            well_thread_verify_create(array('tid' => $thread['tid'], 'uid' => $thread['uid'], 'last_date' => $time));

                            user_update($thread['uid'], array('articles-' => 1, 'well_verify_threads+' => 1));

                            // hook home_content_update_verify_after.php

                            // 移动了版块，审核后才+1
                            if ($forum_threads) {
                                unset($forum_update['threads+']);
                            } else {
                                // 未移动版块，需-1
                                0 == $thread['status'] and $forum_update['threads-'] = 1;
                            }
                        }
                    }

                    // hook home_content_update_post_before.php

                    $data_update = array();

                    $tmp_file = well_attach_assoc_type('post');
                    if ($new_md5 != $old_md5 || !empty($tmp_file)) {
                        // 如果开启云储存或使用图床，需要把内容中的附件链接替换掉
                        $message = data_message_replace_url($tid, $message);

                        // 主图和本地化图片完全后台设置控制
                        $setting = array_value($config, 'setting');
                        $save_image = array_value($setting, 'save_image_on', 0);

                        // 关联附件
                        $assoc = array('uid' => $thread['uid'], 'gid' => $gid, 'tid' => $tid, 'fid' => $thread['fid'], 'time' => $time, 'conf' => $conf, 'message' => $message, 'thumbnail' => 0, 'save_image' => $save_image, 'sess_file' => forum_access_user($fid, $gid, 'allowattach'));
                        // hook home_data_update_assoc_before.php
                        $result = well_attach_assoc_handle($assoc);
                        unset($assoc);
                        $message = $result['message'];
                        $images = $result['images'];
                        $result['images'] and $update['images'] = $result['images'];
                        $result['files'] and $update['files'] = $result['files'];

                        // hook home_data_update_assoc_after.php

                        $data_update = array('tid' => $tid, 'gid' => $gid, 'doctype' => $doctype, 'message' => $message);

                        // hook home_content_data_update_arr.php
                    }

                    // hook home_content_data_update_before.php

                    !empty($data_update) && FALSE === data_update($tid, $data_update) AND message(-1, lang('update_post_failed'));

                    // hook home_content_update_post_center.php

                    if (group_access($gid, 'managecontent')) {
                        // 首页flag
                        !empty($new_index_flagids) && FALSE === flag_create_thread(0, 1, $tid, $new_index_flagids) AND message(-1, lang('create_failed'));

                        // 返回首页被取消的flagid
                        !empty($old_index_flagids) AND flag_thread_delete_by_ids($old_index_flagids, $flagarr);

                        // 频道flag
                        $forum['fup'] && !empty($new_cate_flagids) && FALSE === flag_create_thread($forum['fup'], 2, $tid, $new_cate_flagids) AND message(-1, lang('create_failed'));
                        // 返回频道被取消的flagid
                        !empty($old_cate_flagids) AND flag_thread_delete_by_ids($old_cate_flagids, $flagarr);

                        // 栏目flag
                        !empty($new_forum_flagids) && FALSE === flag_create_thread($fid, 3, $tid, $new_forum_flagids) AND message(-1, lang('create_failed'));
                        // 返回被取消的flagid
                        !empty($old_forum_flagids) AND flag_thread_delete_by_ids($old_forum_flagids, $flagarr);
                    }

                    // hook home_content_update_thread_before.php

                    !empty($update) && FALSE === well_thread_update($tid, $update) AND message(-1, lang('update_thread_failed'));
                    !empty($forum_update) and forum_update($fid, $forum_update);
                    !empty($user_update) && FALSE === user_update($uid, $user_update) AND message(-1, lang('update_failed'));

                    // hook home_content_update_post_end.php

                    message(0, lang('update_successfully'));
                }
                break;
            case 'reject': // 退稿
                // hook home_content_reject_start.php

                if ('GET' == $method) {

                    $page = param(2, 1);
                    $pagesize = 25;
                    $safe_token = well_token_set($uid);
                    $extra = array('op' => $op); // 插件预留

                    // hook home_content_reject_get_start.php

                    $threadlist = well_thread_reject_find_by_uid($uid, $page, $pagesize);

                    // hook home_content_reject_get_before.php

                    $allowdelete = group_access($gid, 'allowdelete') || group_access($gid, 'allowuserdelete') || 1 == $gid;
                    $access = array('allowdelete' => $allowdelete);

                    $page_url = url('home-content-{page}', $extra);
                    $num = $user['well_publish_rejects'] > 1000 ? 1000 : $user['well_publish_rejects'];

                    // hook home_content_reject_get_center.php

                    $pagination = pagination($page_url, $num, $page, $pagesize);

                    $extra['safe_token'] = $safe_token;
                    $header['title'] = lang('well_publish_reject');

                    // hook home_content_reject_get_end.php

                    if ($ajax) {
                        if ($threadlist) {
                            foreach ($threadlist as &$thread) $thread = well_thread_safe_info($thread);
                        }

                        $apilist['header'] = $header;
                        $apilist['member_navlist'] = $member_navs;
                        $apilist['member_menulist'] = $member_menus;
                        $apilist['page'] = $page;
                        $apilist['pagesize'] = $pagesize;
                        $apilist['num'] = $num;
                        $apilist['extra'] = $extra;
                        $apilist['safe_token'] = $safe_token;
                        $apilist['access'] = $access;
                        $apilist['threadlist'] = $threadlist;

                        $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
                    } else {
                        include _include(theme_load('home_content_reject', '', 'well_publish'));
                    }
                }

                // hook home_content_reject_end.php
                break;
            case 'delete': // 只能删除待审核主题、评论、草稿

                if ('POST' != $method) message(1, lang('method_error'));

                // 验证token
                $safe_token = param('safe_token');
                FALSE === well_token_verify($uid, $safe_token) AND message(1, lang('illegal_operation'));

                // hook home_content_delete_start.php

                // 0待审核主题 1退稿 2草稿箱 3待审核评论
                $type = param('type', 0);

                // hook home_content_delete_before.php

                switch ($type) {
                    case '0':
                    case '1':
                    case '2': // 支持批量删除待审核主题 退稿 草稿箱
                        $tid = param('tid', 0);
                        !$tid AND $tid = param('tid', array(0));
                        well_thread_publish_delete($tid, $type);
                        break;
                    case '3': // 支持批量删除待审核评论

                        $pid = param('pid', 0);

                        if ($pid) {
                            $post = comment_read($pid);
                            empty($post) AND message(-1, lang('post_not_exists'));
                            // 允许用户删除未审核的评论
                            $allowdelete = $uid == $post['uid'] || 1 == $gid || group_access($gid, 'allowdelete') || group_access($gid, 'allowuserdelete');

                            empty($allowdelete) AND message(-1, lang('user_group_insufficient_privilege'));

                            well_comment_pending_delete($post);
                        } else {
                            $pid = _POST('pid');
                            !is_int($pid) && !is_array($pid) && $pid = xn_json_decode($pid);
                            if (empty($pid) || !is_array($pid)) message(-1, lang('post_not_exists'));
                            well_comment_pending_delete_by_pids($pid);
                        }

                        break;
                }

                // hook home_content_delete_end.php

                message(0, lang('delete_successfully'));

                break;
            case 'thumbnailDelete':
                // 验证token
                if (array_value($conf, 'message_token', 0)) {
                    $safe_token = param('safe_token');
                    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));
                }

                // hook home_content_thumbnailDelete_start.php

                $tid = param('tid', 0);

                $thread_update = array();
                if ($tid) {
                    empty($tid) and message(1, lang('data_malformation'));

                    $thread = well_thread_read($tid);
                    empty($thread) and message(-1, lang('thread_not_exists'));

                    // hook home_content_thumbnailDelete_before.php

                    if (!group_access($gid, 'allowdelete') && $uid != $thread['uid']) message(-1, lang('user_group_insufficient_privilege'));

                    // 删除
                    if ($thread['icon']) {
                        // Ym变更删除旧图
                        $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');
                        $day = date($attach_dir_save_rule, $thread['icon']);

                        $file = $conf['upload_path'] . 'thumbnail/' . $day . '/' . $thread['uid'] . '_' . $tid . '_' . $thread['icon'] . '.jpeg';
                        is_file($file) and unlink($file);

                        $thread_update['icon'] = 0;

                        // hook home_content_thumbnailDelete_center.php
                    }
                }

                // hook home_content_thumbnailDelete_after.php

                well_thread_update($tid, $thread_update);

                // 初始化附件
                $_SESSION['tmp_thumbnail'] = array();

                // hook home_content_thumbnailDelete_end.php

                message(0, array('message' => lang('delete_successfully'), 'thumbnail' => view_path() . 'img/nopic.png'));
                break;
            // hook home_content_case_end.php
            default:
                http_location($conf['path']);
                break;
        }

        // hook home_content_end.php

        break;
    case 'pending': // 待审内容

        // hook home_pending_start.php

        $op = param('op');

        // hook home_pending_before.php

        switch ($op) {
            // hook home_pending_case_start.php
            case 'thread': // 待审主题

                // hook home_pending_thread_start.php

                if ('GET' == $method) {

                    $page = param(2, 1);
                    $pagesize = 20;
                    $safe_token = well_token_set($uid);
                    $extra = array('op' => $op); // 插件预留

                    // hook home_pending_thread_get_start.php

                    $threadlist = well_thread_verify_find_by_uid($uid, $page, $pagesize);

                    // hook home_pending_thread_get_before.php

                    $allowdelete = group_access($gid, 'allowdelete') || group_access($gid, 'allowuserdelete') || 1 == $gid;
                    $access = array('allowdelete' => $allowdelete);

                    $page_url = url('home-pending-{page}', $extra);
                    $num = $user['well_verify_threads'] > 1000 ? 1000 : $user['well_verify_threads'];

                    // hook home_pending_thread_get_center.php

                    $pagination = pagination($page_url, $num, $page, $pagesize);

                    $extra['safe_token'] = $safe_token;
                    $header['title'] = lang('well_publish_pending_thread');

                    // hook home_pending_thread_get_end.php

                    if ($ajax) {
                        if ($threadlist) {
                            foreach ($threadlist as &$thread) $thread = well_thread_safe_info($thread);
                        }

                        $apilist['header'] = $header;
                        $apilist['member_navlist'] = $member_navs;
                        $apilist['member_menulist'] = $member_menus;
                        $apilist['extra'] = $extra;
                        $apilist['page'] = $page;
                        $apilist['pagesize'] = $pagesize;
                        $apilist['num'] = $num;
                        $apilist['page_url'] = $page_url;
                        $apilist['safe_token'] = $safe_token;
                        $apilist['access'] = $access;
                        $apilist['threadlist'] = $threadlist;

                        $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
                    } else {
                        include _include(theme_load('home_pending_thread', '', 'well_publish'));
                    }
                }

                // hook home_pending_thread_end.php

                break;
            case 'comment': // 待审评论
                // hook home_pending_comment_start.php

                if ('GET' == $method) {

                    $page = param(2, 1);
                    $pagesize = 20;
                    $safe_token = well_token_set($uid);
                    $extra = array('op' => $op); // 插件预留

                    // hook home_pending_comment_get_start.php

                    $postlist = well_comment_verify_find_by_uid($uid, $page, $pagesize);

                    // hook home_pending_comment_get_before.php

                    $allowdelete = group_access($gid, 'allowdelete') || group_access($gid, 'allowuserdelete') || 1 == $gid;
                    $access = array('allowdelete' => $allowdelete);

                    $page_url = url('home-pending-{page}', $extra);
                    $num = $user['well_verify_threads'] > 1000 ? 1000 : $user['well_verify_threads'];

                    // hook home_pending_comment_get_center.php

                    $pagination = pagination($page_url, $num, $page, $pagesize);

                    $extra['safe_token'] = $safe_token;
                    $header['title'] = lang('well_publish_pending_comment');

                    // hook home_pending_comment_get_end.php

                    if ($ajax) {
                        if ($postlist) {
                            foreach ($postlist as &$_post) $_post = comment_filter($_post);
                        }

                        $apilist['header'] = $header;
                        $apilist['member_navlist'] = $member_navs;
                        $apilist['member_menulist'] = $member_menus;
                        $apilist['extra'] = $extra;
                        $apilist['page'] = $page;
                        $apilist['pagesize'] = $pagesize;
                        $apilist['num'] = $num;
                        $apilist['page_url'] = $page_url;
                        $apilist['safe_token'] = $safe_token;
                        $apilist['access'] = $access;
                        $apilist['postlist'] = $postlist;

                        $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
                    } else {
                        include _include(theme_load('home_pending_comment', '', 'well_publish'));
                    }
                }

                // hook home_pending_comment_end.php
                break;
            // hook home_pending_case_end.php
        }

        // hook home_pending_end.php

        break;
?>