<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') AND exit('Forbidden');
include _include(APP_PATH . 'model/db_check.func.php');
set_time_limit(0); // 大数据量创建索引会超时
// 所有限制对管理员无效 投稿列表列出开放的投稿版块 进入版块或投稿页面再判断是否开启投稿和该版块用户投稿权限

// 容易出错放前面需要判断
if (!db_find_field($db->tablepre . 'website_thread_tid', 'lastpid')) {
    $sql = "ALTER TABLE  `{$db->tablepre}website_thread_tid` ADD  `lastpid` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, 'website_thread_tid 表 lastpid 字段建立失败');
}

// 热门主题，以最后回复为准
if (!db_find_index($db->tablepre . 'website_thread_tid', 'lastpid')) {
    $sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` ADD INDEX `lastpid` (`lastpid`)";
    $r = db_exec($sql);
    FALSE === $r AND message(1, 'website_thread_tid 表 lastpid 索引建立失败');
}

// 热门主题，以最后回复为准
if (!db_find_index($db->tablepre . 'website_thread_tid', 'fid_lastpid')) {
    $sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` ADD INDEX `fid_lastpid` (`fid`,`lastpid`)";
    $r = db_exec($sql);
    FALSE === $r AND message(1, 'website_thread_tid 表 fid_lastpid 联合索引建立失败');
}

// 查看自己的热门主题
if (!db_find_index($db->tablepre . 'website_thread_tid', 'uid_lastpid')) {
    $sql = "ALTER TABLE `{$db->tablepre}website_thread_tid` ADD INDEX `uid_lastpid` (`uid`,`lastpid`)";
    $r = db_exec($sql);
    FALSE === $r AND message(1, 'website_thread_tid 表 uid_lastpid 联合索引建立失败');
}

//--- TODO 未开发 ---
// 用户表增加每天投稿数量well_publishs 投稿比对时间戳是否当天+1或清零
if (!db_find_field($db->tablepre . 'user', 'well_publishs')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_publishs` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 用户表增加最后投稿时间well_publish_latest 投稿比对时间戳是否当天
if (!db_find_field($db->tablepre . 'user', 'well_publish_latest')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_publish_latest` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 用户表增加每天评论数量well_comments 评论比对时间戳是否当天+1或清零
if (!db_find_field($db->tablepre . 'user', 'well_comments')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_comments` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 用户表增加最后投稿时间well_publish_latest 评论比对时间戳是否当天
if (!db_find_field($db->tablepre . 'user', 'well_comment_latest')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_comment_latest` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}
//--- TODO 未开发 ---


// 版块投稿 0关闭 1开启 限制对管理员无效
if (!db_find_field($db->tablepre . 'forum', 'well_publish')) {
    $sql = "ALTER TABLE `{$db->tablepre}forum` ADD `well_publish` TINYINT( 1 ) NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 管理审核
if (!db_find_field($db->tablepre . 'group', 'allowverify')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowverify` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowverify` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 允许发缩略图
if (!db_find_field($db->tablepre . 'group', 'allowthumbnail')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowthumbnail` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowthumbnail` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 写简介
if (!db_find_field($db->tablepre . 'group', 'allowbrief')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowbrief` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowbrief` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 自动获取简介
if (!db_find_field($db->tablepre . 'group', 'allow_auto_brief')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allow_auto_brief` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allow_auto_brief` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 填写关键词
if (!db_find_field($db->tablepre . 'group', 'allowkeywords')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowkeywords` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowkeywords` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 填写描述
if (!db_find_field($db->tablepre . 'group', 'allowdescription')) {
    $sql = "ALTER TABLE  `{$db->tablepre}group` ADD  `allowdescription` TINYINT( 1 ) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));

    // 增加管理员审核权限
    $sql = "UPDATE `{$db->tablepre}group` SET `allowdescription` = '1' WHERE `gid` = '1'";
    $r = db_exec($sql);
}

// 用户表需要增加待审核主题和评论 主题编辑，内容改变，开启审核，则进入审核
// 用户表增加待验证主题数
if (!db_find_field($db->tablepre . 'user', 'well_verify_threads')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_verify_threads` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 用户表增加待验证评论数
if (!db_find_field($db->tablepre . 'user', 'well_verify_comments')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_verify_comments` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 待审主题 审核主题通过删除数据 修改时主题和内容同时变动重新进入待审
if (!db_find_table($db->tablepre . 'well_thread_verify')) {
    $sql = "CREATE TABLE `{$db->tablepre}well_thread_verify` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `last_date` int(11) unsigned NOT NULL DEFAULT '0', # 最后操作时间
  PRIMARY KEY (`tid`),
  KEY `last_date` (`last_date`),
  KEY `uid_tid` (`uid`,`tid`) # 用户未审核主题最后更新排序
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 用户表增加退稿数
if (!db_find_field($db->tablepre . 'user', 'well_publish_rejects')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_publish_rejects` INT(11) unsigned NOT NULL DEFAULT  '0'";
    $r = db_exec($sql);
    FALSE === $r AND message(1, lang('create_failed'));
}

// 退稿主题
if (!db_find_table($db->tablepre . 'well_thread_reject')) {
    $sql = "CREATE TABLE `{$db->tablepre}well_thread_reject` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `uid_tid` (`uid`,`tid`) # 用户退稿主题
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 此表只显示未审核的主题 审核主题通过删除数据
if (!db_find_table($db->tablepre . 'well_comment_verify')) {
    $sql = "CREATE TABLE `{$db->tablepre}well_comment_verify` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `last_date` int(11) unsigned NOT NULL DEFAULT '0', # 最后操作时间
  PRIMARY KEY (`pid`),
  KEY `last_date` (`last_date`),
  KEY `tid_pid` (`tid`,`pid`),
  KEY `uid_pid` (`uid`,`pid`) # 用户未审核回复
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 限制对管理员无效
$arr = array(
    'enable_tag' => 0, // 投稿可填写tag开关
    'enable_publish_limit' => 1, // 投稿数量限制 0不限
    'enable_comment_limit' => 0, // 评论数量限制 0不限
    'enable_reject_limit' => 10, // 退稿数量限制 0不限 达到限制必须删除或编辑才能继续投稿
);
setting_set('well_publish', $arr);

cache_truncate();

?>