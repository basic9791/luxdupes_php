<?php exit;
// 评论是否达到限制
$publish_setting = setting_get('well_publish');
FALSE === group_access($gid, 'managecontent') && $publish_setting['enable_comment_limit'] && $user['well_verify_comments'] >= $publish_setting['enable_comment_limit'] AND message(1, lang('well_publish_comment_limit_tips', array('n' => $publish_setting['enable_publish_limit'])));
?>