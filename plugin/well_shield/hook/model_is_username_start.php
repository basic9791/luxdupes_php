<?php exit;
if (xn_strlen($username) <= 1) {
    $err = lang('well_shield_username_limit_character');
    return FALSE;
}

$setting = setting_get('conf');
$filter = array_value($setting, 'filter');
$arr = array_value($filter, 'username');
if (array_value($arr, 'limit') && !preg_match('#^[\w]*$#i', $username)) {
    // 用户名必须是英文、下划线、数字
    $err = lang('well_shield_username_limit_tips');
    return FALSE;
}

?>