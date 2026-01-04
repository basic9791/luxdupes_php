<?php
/*
 * Copyright (C) 
*/
!defined('DEBUG') and exit('Access Denied.');


$safe_token = well_token_set($uid);
$action = param(1);

// hook verify_start.php

// 从全局拉取$user
$header['mobile_title'] = '';
$header['mobile_linke'] = '';

include _include(APP_PATH . 'plugin/bang_contact/model/bang_contact.func.php');

$smtplist = include _include(APP_PATH . 'conf/smtp.conf.php');
$smtp = $smtplist[0];

switch ($action) {
  case 'send':
    user_login_check();
    include _include(XIUNOPHP_PATH . 'xn_send_mail.func.php');
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));
    $dataStr = str_replace('&quot;', '"', param('data'));
    $sendData = json_decode($dataStr);


    $newData = array(
      'email' => $sendData->email,
      'name' => $sendData->name,
      'message' => $sendData->message,
    );

    $orderId=bang_contact_create($newData);
    $message="
    <p>name: $sendData->name</p>
    <p>Email: $sendData->email</p>
    <p>Message:$sendData->message </p>
   ";

    

    $r = xn_send_mail($smtp, $conf['sitename'],$smtp['email'],$conf['sitename'].' new message', $message);

   
   
   
    message(0, $msgStr);
    break;
  default:
    $uid = _SESSION('uid');
    include _include(theme_load('contact', '', 'bang_contact'));
}
