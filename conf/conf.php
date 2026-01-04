<?php
return array (
  'db' => 
  array (
    'type' => 'pdo_mysql',
    'mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'luxdupes',
        'password' => 'luxdupes',
        'name' => 'luxdupes',
        'tablepre' => 'luxdupes_',
        'charset' => 'utf8',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
    'pdo_mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'luxdupes',
        'password' => 'luxdupes',
        'name' => 'luxdupes',
        'tablepre' => 'luxdupes_',
        'charset' => 'utf8',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
  ),
  'cache' => 
  array (
    'enable' => true,
    'type' => 'mysql',
    'memcached' => 
    array (
      'host' => 'localhost',
      'port' => '11211',
      'cachepre' => 'welux.com_',
    ),
    'redis' => 
    array (
      'host' => 'localhost',
      'port' => '6379',
      'cachepre' => 'welux.com_',
    ),
    'xcache' => 
    array (
      'cachepre' => 'welux.com_',
    ),
    'yac' => 
    array (
      'cachepre' => 'welux.com_',
    ),
    'apc' => 
    array (
      'cachepre' => 'welux.com_',
    ),
    'mysql' => 
    array (
      'cachepre' => 'welux.com_',
    ),
  ),
  'tmp_path' => './tmp/',
  'log_path' => './log/',
  'view_url' => 'view/',
  'upload_url' => 'upload/',
  'upload_path' => './upload/',
  'upload_quick' => 0,
  'upload_attach_size' => 20,
  'upload_attach_total' => 30,
  'attach_on' => 0,
  'attach_delete' => 0,
  'cloud_url' => '',
  'path' => '/',
  'logo_mobile_url' => 'img/luxdupes/logo.png',
  'logo_pc_url' => 'img/luxdupes/logo.png',
  'favicon_ico' => '/view/img/luxdupes/favicon.ico',
  'logo_water_url' => 'img/water-small.png',
  'pay_alipayId' => '86-15819915273',
  'pay_familyName' => 'YANG',
  'pay_givenName' => 'BANG CHAO',
  'pay_phone' => '15819915273',
  'phone' => '15819915273',
  'phone_hot' => '86',
  'id_ck_51_la' => 'id:"KY5EHJjvs8fRoeWj",ck:"KY5EHJjvs8fRoeWj"',
  'domain' => 'welux.com',
  'sitename' => 'Website',
  'sitebrief' => 'Site Brief',
  'timezone' => 'Asia/Shanghai',
  'lang' => 'zh-cn',
  'runlevel' => 5,
  'runlevel_reason' => 'The site is under maintenance, please visit later.',
  'cookie_pre' => 'luxdupes_',
  'cookie_domain' => 'welux.com',
  'cookie_path' => '/',
  'cookie_lifetime' => '8640000',
  'auth_key' => 'E7KYMNZD3KG8G46HKJXBBFP2W7APSS5NZWGDK24CCPHD8XP6HMG9822UZNFX6KSH',
  'pagesize' => 48,
  'postlist_pagesize' => 500,
  'listsize' => 500,
  'linksize' => 20,
  'tagsize' => 60,
  'comment_pagesize' => 48,
  'cache_thread_list_pages' => 10,
  'online_update_span' => 120,
  'online_hold_time' => 3600,
  'session_delay_update' => 0,
  'upload_image_width' => 927,
  'upload_resize' => 'clip',
  'order_default' => 'lastpid',
  'attach_dir_save_rule' => 'Ym',
  'update_views_on' => 1,
  'user_create_email_on' => 1,
  'user_create_on' => 1,
  'user_resetpw_on' => 1,
  'admin_bind_ip' => 1,
  'cdn_on' => 0,
  'api_on' => 1,
  'url_rewrite_on' => 3,
  'disabled_plugin' => 0,
  'version' => '2.3.10',
  'static_version' => '?1766470927',
  'license_date' => 'January 1, 2023',
  'installed' => 1,
  'compress' => 1,
  'upload_token' => 1,
  'message_token' => 0,
  'comment_token' => 0,
  'login_only' => 0,
  'login_ip' => 0,
  'login_ua' => 0,
  'thumbnail_width' => 400,
  'thumbnail_height' => 400,
  'cache_thread' => 1,
  'img_base64' => 0,
);
?>