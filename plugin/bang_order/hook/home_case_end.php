<?php exit;
case 'order':
    include _include(APP_PATH . 'plugin/bang_order/model/bang_order.func.php');

            $safe_token = well_token_set($uid);
            $page = param(2, 1);
            $pagesize = 20;
            $extra = array(); // 插件预留

            // hook home_order_get_start.php
           
            $threadlist = bang_order_find_by_uid($uid, $page, $pagesize);
            

            //  print_r( $threadlist);

            // hook home_order_get_before.php

            $num = $user['bang_order'] > $pagesize * 100 ? $pagesize * 100 : $user['bang_order'];



            // hook home_order_get_center.php

            $page_url = url('home-order-{page}', $extra);
           
                // print_r($threadlist);
                
            // hook my_follow_get_after.php

            $pagination = pagination($page_url, $num, $page, $pagesize);

            $header['title'] = lang('bang_order');
            $safe_token = well_token_set($uid);
            $active = 'home-order';

            // hook home_order_get_end.php

            
                include _include(theme_load('home_order', '', 'bang_order'));
           


        break;
?>