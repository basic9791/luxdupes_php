<?php
/*
 * Copyright (C) 
 */
!defined('DEBUG') and exit('Forbidden');
include _include(APP_PATH . 'model/db_check.func.php');
set_time_limit(0);



if (!db_find_table($db->tablepre . 'bang_contact')) {
    $sql = "CREATE TABLE `{$db->tablepre}bang_contact` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100)  NOT NULL DEFAULT '' , # 名
  `email` varchar(100)  NOT NULL DEFAULT '', # 邮箱 
  `message` varchar(555) NULL, # 备注
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

?>