<?php
/*
 * Copyright (C) www.wellcms.cn
 */

!defined('DEBUG') and exit('Forbidden');

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_get_favorites`";
db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_favorites`";
db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}website_thread` DROP `well_favorites`";
db_exec($sql);

$sql = "DROP TABLE IF EXISTS `{$db->tablepre}well_favorites`";
db_exec($sql);

?>