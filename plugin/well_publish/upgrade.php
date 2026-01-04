<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') and exit('Forbidden');
include _include(APP_PATH . 'model/db_check.func.php');
set_time_limit(0);

if (!db_find_field($db->tablepre . 'well_comment_verify', 'tid')) {
    $sql = "ALTER TABLE `{$db->tablepre}well_comment_verify` ADD `tid` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

if (!db_find_index($db->tablepre . 'well_comment_verify', 'tid_pid')) {
    $sql = "ALTER TABLE `{$db->tablepre}well_comment_verify` ADD INDEX `tid_pid` (`tid`,`pid`)";
    $r = db_exec($sql);
}

if (!db_find_field($db->tablepre . 'group', 'allowthumbnail')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowthumbnail` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowthumbnail` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

if (!db_find_field($db->tablepre . 'group', 'allowbrief')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowbrief` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowbrief` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

if (!db_find_field($db->tablepre . 'group', 'allow_auto_brief')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allow_auto_brief` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allow_auto_brief` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

if (!db_find_field($db->tablepre . 'group', 'allowkeywords')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowkeywords` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowkeywords` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

if (!db_find_field($db->tablepre . 'group', 'allowdescription')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowdescription` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowdescription` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

?>