<?php
/*
 * Copyright (C) 
 */
!defined('DEBUG') and exit('Forbidden');
include _include(APP_PATH . 'model/db_check.func.php');
set_time_limit(0);

// 被购买的总数
if (!db_find_field($db->tablepre . 'user', 'well_get_order')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `well_get_order` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

// 购买数量
if (!db_find_field($db->tablepre . 'user', 'bang_order')) {
    $sql = "ALTER TABLE  `{$db->tablepre}user` ADD  `bang_order` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

// 主题被购买数
if (!db_find_field($db->tablepre . 'website_thread', 'bang_order')) {
    $sql = "ALTER TABLE  `{$db->tablepre}website_thread` ADD  `bang_order` int(10) unsigned NOT NULL DEFAULT '0'";
    $r = db_exec($sql);
}

if (!db_find_table($db->tablepre . 'bang_order')) {
    $sql = "CREATE TABLE `{$db->tablepre}bang_order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `orderCode` varchar(12)  NOT NULL DEFAULT '', #订单编码
  `state` int(3) unsigned NOT NULL DEFAULT '0', # 状态 1.待支付 2.已支付 3.运输中 4.已收货 5.已完成 9.已取消
  `uid` int(11) unsigned NOT NULL DEFAULT '0', # 购买用户

  `create_date` int(11) unsigned NOT NULL DEFAULT '0', # 创建时间
  `first_name` varchar(100)  NOT NULL DEFAULT '' , # 名
  `last_name` varchar(100)  NOT NULL DEFAULT '', # 姓
  `country_region` varchar(100)  NOT NULL DEFAULT '', # 国家地区
  `city` varchar(100)  NOT NULL DEFAULT '', # 城市
  `province` varchar(100)  NOT NULL DEFAULT '', # 州/省
  `ZIP_code` varchar(100)  NOT NULL DEFAULT '', # 邮政编码
  `phone` varchar(100)  NOT NULL DEFAULT '', # 电话号码
  `email` varchar(100)  NOT NULL DEFAULT '', # 邮箱 
  `address` varchar(100)  NOT NULL DEFAULT '', # 地址
  `goods` TEXT NOT NULL , # 商品列表JSON
  `price_total` DECIMAL(10,2) NULL,  # 总费用
  `payment` int(3) unsigned NOT NULL DEFAULT '0', # 支付方式 1. Remitly（remitly.com or REMITLY App）2.Wise transfer 3. Alipay(支付宝)
  `payment_date` int(11) unsigned NOT NULL DEFAULT '0', #支付时间
  `payment_id` varchar(12) NULL, # 支付id
  `payment_code` varchar(16) NULL ,# 支付编码
  `payment_user` varchar(255) NULL ,# 支付用户信息
  `shipping`  int(3) unsigned NOT NULL DEFAULT '0', # 配送方式
  `shipping_price` DECIMAL(5,2) NULL , # 运费 
  `shipping_link` varchar(100) NULL ,  # 物流查询链接
  `shipping_code` varchar(22) NULL,  # 物流查询编码 
  `shipping_name` varchar(30) NULL,  # 物流公司名称
  `notes` varchar(255) NULL, # 备注
  `notes_man` varchar(255) , # 管理备注
  `url_man` varchar(255) , # 订单域名
  
  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 订单：优惠券码
if (!db_find_field($db->tablepre . 'bang_order', 'coupon_code')) {
    $sql = "ALTER TABLE  `{$db->tablepre}bang_order` ADD  `coupon_code` varchar(32) NOT NULL DEFAULT ''";
    $r = db_exec($sql);
}

// 订单：优惠券优惠金额
if (!db_find_field($db->tablepre . 'bang_order', 'coupon_amount')) {
    $sql = "ALTER TABLE  `{$db->tablepre}bang_order` ADD  `coupon_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00'";
    $r = db_exec($sql);
}

// 优惠券表
if (!db_find_table($db->tablepre . 'bang_coupon')) {
    $sql = "CREATE TABLE `{$db->tablepre}bang_coupon` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32)  NOT NULL DEFAULT '', # 优惠券码（唯一）
  `name` varchar(100)  NOT NULL DEFAULT '', # 优惠券名称
  `type` int(1) unsigned NOT NULL DEFAULT '1', # 优惠类型 1.固定金额 2.订单百分比
  `value` DECIMAL(10,2) NOT NULL DEFAULT '0.00', # 优惠值（金额或百分比）
  `use_type` int(1) unsigned NOT NULL DEFAULT '1', # 使用类型 1.一次性 2.可重复
  `total_num` int(11) unsigned NOT NULL DEFAULT '1', # 可使用总次数（一次性固定为1）
  `used_num` int(11) unsigned NOT NULL DEFAULT '0', # 已使用次数
  `min_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00', # 最低消费金额
  `start_time` int(11) unsigned NOT NULL DEFAULT '0', # 有效期开始时间
  `end_time` int(11) unsigned NOT NULL DEFAULT '0', # 有效期结束时间
  `status` int(1) unsigned NOT NULL DEFAULT '1', # 状态 1.启用 0.禁用
  `notes` varchar(255) NOT NULL DEFAULT '', # 备注
  `create_date` int(11) unsigned NOT NULL DEFAULT '0', # 创建时间
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

?>