<?php exit;
case 'favorites':
        include _include(APP_PATH . 'plugin/well_favorites/model/well_favorites.func.php');

        // hook user_favorites_start.php

        if ('GET' == $method) {

            $_uid = param(2, 1);
            $_user = user_read_cache($_uid);

            empty($_user) and message(-1, lang('user_not_exists'));
    
            $allowdelete = group_access($gid, 'allowdelete');

            $page = param(3, 1);
            $pagesize = 20;
            $safe_token = well_token_set($uid);
            $extra = array('safe_token' => $safe_token); // 插件预留
            $num = 0;
            $menulist = user_menu($_uid);

            // hook user_favorites_get_start.php

            // 从默认的地方读取主题列表
            $list_from_default = 1;

            // hook user_favorites_get_default.php

            if (1 == $list_from_default) {

                // hook user_favorites_get_before.php

                $threadlist = $_user['well_favorites'] ? well_favorites_find_by_uid($_uid, $page, $pagesize) : '';

                $num = $_user['well_favorites'] > $pagesize * 100 ? $pagesize * 100 : $_user['well_favorites'];

                // hook user_favorites_get_center.php
            }

            // 从默认的地方读取主题列表
            $pagination_from_default = 1;

            // hook user_favorites_get_pagination_default.php

            if (1 == $pagination_from_default){
                $page_url = url('user-favorites-'.$_uid.'-{page}', $extra);

                // hook user_favorites_get_pagination_before.php

                $pagination = pagination($page_url, $num, $page, $pagesize);
            }

            // hook home_like_get_pagination_after.php

            $header['title'] = lang('well_favorites');

            // hook home_like_get_end.php

            if ('1' == _GET('ajax')) {
                if ($threadlist) {
                    foreach ($threadlist as &$thread) $thread = well_thread_safe_info($thread);
                }

                $apilist['header'] = $header;
                $apilist['menulist'] = $menulist;
                $apilist['this_user'] = user_safe_info($_user);
                $apilist['extra'] = $extra;
                $apilist['num'] = $num;
                $apilist['page'] = $page;
                $apilist['pagesize'] = $pagesize;
                $apilist['page_url'] = $page_url;
                $apilist['safe_token'] = $safe_token;
                $apilist['threadlist'] = $threadlist;

                $conf['api_on'] ? message(0, $apilist) : message(0, lang('closed'));
            } else {
                include _include(theme_load('user_favorites', '', 'well_favorites'));
            }

        } elseif ('POST' == $method) {
            // hook user_favorites_post_start.php
            // hook user_favorites_post_end.php
        }
        break;
?>