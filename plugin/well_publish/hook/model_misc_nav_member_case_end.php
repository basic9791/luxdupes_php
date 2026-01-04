<?php exit;
case 'verify':
            // hook model_misc_nav_member_verify_start.php
            $menus += array(
                // hook model_misc_nav_member_verify_thread_before.php
                'verify-thread' => array('url' => url('verify-thread'), 'name' => lang('thread'), 'active' => 'verify-thread'),
                // hook model_misc_nav_member_verify_comment_before.php
                'verify-comment' => array('url' => url('verify-comment'), 'name' => lang('comment'), 'active' => 'verify-comment'),
                // hook model_misc_nav_member_verify_reject_before.php
                'verify-reject' => array('url' => url('verify-reject'), 'name' => lang('well_publish_reject'), 'active' => 'verify-reject'),
                // hook model_misc_nav_member_verify_reject_after.php
            );
            // hook model_misc_nav_member_verify_end.php
            break;
?>