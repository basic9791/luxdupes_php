<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') AND exit('Forbidden');

$sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` DROP INDEX `fid_lastpid`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` DROP INDEX `uid_lastpid`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` DROP INDEX `lastpid`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` DROP `lastpid`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_publishs`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_publish_latest`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_comments`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_comment_latest`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}forum` DROP `well_publish`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allowverify`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allowthumbnail`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allowbrief`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allow_auto_brief`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allowkeywords`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}group` DROP `allowdescription`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_verify_threads`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_verify_comments`";
$r = db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_publish_rejects`";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS `{$db->tablepre}well_thread_verify`";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS `{$db->tablepre}well_thread_reject`";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS `{$db->tablepre}well_comment_verify`";
$r = db_exec($sql);

setting_delete('well_publish');

cache_truncate();

?>