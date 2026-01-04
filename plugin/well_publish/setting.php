<?php
/*
 * Copyright (C) www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

if ('GET' == $method) {

    $publish = setting_get('well_publish');

    $input = array();
    $input['enable_tag'] = form_radio_yes_no('enable_tag', $publish['enable_tag']);
    $input['enable_publish_limit'] = form_text('enable_publish_limit', $publish['enable_publish_limit']);
    $input['enable_comment_limit'] = form_text('enable_comment_limit', $publish['enable_comment_limit']);
    $input['enable_reject_limit'] = form_text('enable_reject_limit', $publish['enable_reject_limit']);

    $header['title'] = lang('well_publish');
    $header['mobile_title'] = lang('well_publish');
    $header['mobile_link'] = url('plugin-setting', array('dir' => 'well_publish'), TRUE);

    include _include(APP_PATH . 'plugin/well_publish/view/htm/setting.htm');

} elseif ('POST' == $method) {

    $enable_tag = param('enable_tag', 0);
    $enable_publish_limit = param('enable_publish_limit', 0);
    $enable_comment_limit = param('enable_comment_limit', 0);
    $enable_reject_limit = param('enable_reject_limit', 0);

    $arr = array(
        'enable_tag' => $enable_tag,
        'enable_publish_limit' => $enable_publish_limit,
        'enable_comment_limit' => $enable_comment_limit,
        'enable_reject_limit' => $enable_reject_limit,
    );

    setting_set('well_publish', $arr);

    message(0, lang('save_successfully'));
}

?>