<?php
/*
 * Copyright (C) 
 */

!defined('DEBUG') and exit('Forbidden');

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `well_get_contact`";
db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}user` DROP `bang_contact`";
db_exec($sql);

$sql = "ALTER TABLE `{$db->tablepre}website_thread` DROP `bang_contact`";
db_exec($sql);

$sql = "DROP TABLE IF EXISTS `{$db->tablepre}bang_contact`";
db_exec($sql);

?>