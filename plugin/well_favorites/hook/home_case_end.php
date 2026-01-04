<?php exit;
case 'favorites':
    include _include(APP_PATH . 'plugin/well_favorites/model/well_favorites.func.php');

        // hook home_favorites_start.php

        if ('GET' == $method) {
            $safe_token = well_token_set($uid);
            $page = param(2, 1);
            $pagesize = 20;
            $extra = array(); // 插件预留

            // hook home_favorites_get_start.php

            $threadlist = well_favorites_find_by_uid($uid, $page, $pagesize);

            // hook home_favorites_get_before.php

            $num = $user['well_favorites'] > $pagesize * 100 ? $pagesize * 100 : $user['well_favorites'];

            // hook home_favorites_get_center.php

            $page_url = url('my-follow-{page}', $extra);

            // hook my_follow_get_after.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $header['title'] = lang('well_favorites');
            $safe_token = well_token_set($uid);
            $active = 'home-favorites';

            // hook home_favorites_get_end.php

            if (1 == $ajax) {
                $apilist['header'] = $header;
                $apilist['extra'] = $extra;
                $apilist['num'] = $num;
                $apilist['page'] = $page;
                $apilist['pagesize'] = $pagesize;
                $apilist['page_url'] = $page_url;
                $apilist['active'] = $active;
                $apilist['safe_token'] = $safe_token;
                $apilist['threadlist'] = $threadlist;
                $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
            } else {
                include _include(theme_load('home_favorites', '', 'well_favorites'));
            }

        } elseif ('POST' == $method) {

            $type = param('type', 0); // 0主题 1评论

            $update_user = array();
            $update_thread = array();

            // hook home_favorites_post_start.php

            if (0 == $type) {
                // 收藏
                $tid = param('tid', 0);

                $thread = well_thread_read_cache($tid);
                empty($thread) and message(-1, lang('thread_not_exists'));

                if ($user['well_favorites'] >= 1000) message(2, lang('well_favorites_clear_tips'));

                if ($uid == $thread['uid']) message(2, lang('well_favorites_tips'));

                $uids = array($uid, $thread['uid']);
                $user_update = array();

                // 赞主题 查询是否赞过
                $read = well_favorites_read_by_tid_and_uid_cache($tid, $uid);

                if ($read) message(1, lang('well_favorites_added'));

                well_favorites_create(array('uid' => $uid, 'touid' => $thread['uid'], 'tid' => $thread['tid'], 'create_date' => $time));

                $user_update = array(
                    $uid => array('well_favorites+' => 1),
                    $thread['uid'] => array('well_get_favorites+' => 1)
                );
                $update_thread['well_favorites+'] = 1;
                $state = 0;

                // 更新主题数据
                well_thread_update($tid, $update_thread);

                // 更新用户数据
                user_big_update(array('uid' => $uids), $user_update);

                message(0, lang('well_favorites_added'));

            } elseif (1 == $type) {
                // 取消
                $idarr = _POST('idarr');
                !is_array($idarr) && $idarr = xn_json_decode($idarr);
                if (empty($idarr)) message(1, lang('data_is_empty'));
                $arrlist = well_favorites_find_by_id($idarr, 1, count($idarr));

                $n = 0;
                $idarr = array();
                $tidarr = array();
                $uidarr = array();
                foreach ($arrlist as $val) {
                    if ($val['uid'] != $uid) continue;
                    ++$n;
                    $idarr[] = $val['id'];

                    isset($tidarr[$val['tid']]) ? $tidarr[$val['tid']] += 1 : $tidarr[$val['tid']] = 1;

                    isset($uidarr[$val['touid']]) ? $uidarr[$val['touid']] += 1 : $uidarr[$val['touid']] = 1;
                }

                $tids = array();
                $update = array();
                foreach ($tidarr as $tid => $n) {
                    $tids[] = $tid;
                    $update[$tid] = array('well_favorites-' => $n);
                }

                thread_big_update(array('tid' => $tids), $update);

                $uids = array();
                $update = array();
                foreach ($uidarr as $_uid => $n) {
                    $uids[] = $_uid;
                    $update[$_uid] = array('well_get_favorites-' => $n);
                }

                //user_update($uid, array('well_favorites-' => $n));
                $uids[] = $uid;
                $update[$uid] = array('well_favorites-' => $n);
                user_big_update(array('uid' => $uids), $update);

                well_favorites_delete($idarr);

                message(0, lang('delete_successfully'));
            } elseif (2 == $type) {
                // 是否收藏主题
                $tid = param('tid', 0);

                $thread = well_thread_read_cache($tid);
                empty($thread) and message(-1, lang('thread_not_exists'));

                $read = well_favorites_read_by_tid_and_uid_cache($tid, $uid);

                $read ? message(0, lang('well_favorites_added')) : message(1, lang('none'));
            }
            
            // hook home_favorites_post_end.php
        }
        break;
?>