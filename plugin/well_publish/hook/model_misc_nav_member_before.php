<?php exit;
if (group_access(GLOBALS('gid'), 'allowverify')) {
    $navs += array('verify' => array('url' => url('verify'), 'name' => lang('well_verify_content'), 'active' => 'menu-verify'));
}
?>