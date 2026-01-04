<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') and exit('Forbidden');
include _include(APP_PATH . 'model/db_check.func.php');
set_time_limit(0);

// 被收藏的总数
if (!db_find_field($db->tablepre . 'user', 'well_get_favorites')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_get_favorites` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

// 收藏数量
if (!db_find_field($db->tablepre . 'user', 'well_favorites')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_favorites` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

// 主题被收藏数
if (!db_find_field($db->tablepre . 'website_thread', 'well_favorites')) {
    $sql = "ALTER TABLE  `{$db->tablepre}website_thread` ADD  `well_favorites` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

if (!db_find_table($db->tablepre . 'well_favorites')) {
    $sql = "CREATE TABLE `{$db->tablepre}well_favorites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0', # 收藏用户
  `touid` int(11) unsigned NOT NULL DEFAULT '0', # 主题作者
  `tid` int(11) unsigned NOT NULL DEFAULT '0', # 主题tid
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0', # 0主题
  `create_date` int(11) unsigned NOT NULL DEFAULT '0', # 创建时间
  PRIMARY KEY (`id`),
  KEY `tid_uid` (`tid`,`uid`), # 收藏的内容
  KEY `touid_id` (`touid`,`id`), # 被哪些用户收藏
  KEY `uid_id` (`uid`,`id`) # 用户所有收藏数据
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

?>