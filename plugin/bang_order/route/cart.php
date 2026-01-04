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

include _include(APP_PATH . 'plugin/bang_order/model/bang_order.func.php');

switch ($action) {
  case 'save':
    include _include(XIUNOPHP_PATH . 'xn_send_mail.func.php');
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));
    $dataStr = str_replace('&quot;', '"', param('deliveryAddress'));
    $deliveryAddress = json_decode($dataStr);
    $goods = param('cartData');
    $goodsArr=json_decode(str_replace('&quot;', '"', $goods));
    $price_total = param('price_total');
    $payment = param('paypalType');
    $tids = explode(",", param('tids'));
    $email = $deliveryAddress->email;

     is_email($email, $err) || message('email', $err);

    if ($deliveryAddress->username && $deliveryAddress->password) {
      
      $username = $deliveryAddress->username;
      $password = $deliveryAddress->password;
      $_gid = '101';
      $username && !is_username($username, $err) and message('username', $err);

      $_user = user_read_by_email($email);
      $_user and message('email', lang('email_is_in_use'));

      $_user = user_read_by_username($username);
      $_user and message('username', lang('user_already_exists'));

      // hook admin_user_create_post_before.php
      $salt = xn_rand(16);
      $arr = array(
                'username' => $username,
                'password' => md5(md5($password) . $salt),
                'salt' => $salt,
                'gid' => $_gid,
                'email' => $email,
                'create_ip' => $longip,
                'create_date' => $time
            );
            // hook admin_user_create_post_after.php
      $uid = user_create($arr);
      FALSE === $uid and message(-1, lang('create_failed'));
    }

    
    // echo "$price_total--- $tids";
    if (!$uid) {
      $uid = 9999999;
    } else {
      $user_update = array($uid => array('bang_order+' => 1));
      // 更新用户数据
      user_big_update(array('uid' => $uid), $user_update);
    }
    // echo 'firstName>>' . $deliveryAddress->firstName;
    
    $sitename= $conf['sitename'];
    $newData = array(
      'uid' => $uid,
      'create_date' => $time,
      'price_total' => $price_total,      
      'payment' => $payment,
      'goods' => $goods,
      'state' => 1,
      'shipping' => 1,
      'first_name' => $deliveryAddress->firstName,
      'last_name' => $deliveryAddress->lastName,
      'country_region' => $deliveryAddress->country,
      'city' => $deliveryAddress->city,
      'province' => $deliveryAddress->province,
      'ZIP_code' => $deliveryAddress->ZIP_code,
      'phone' => $deliveryAddress->phone,
      'email' => $deliveryAddress->email,
      'address' => $deliveryAddress->address,
      'notes' => $deliveryAddress->notes,
      'url_man' => $deliveryAddress->url_man,
      'notes_man'=>$sitename
    );

    // $sqladd = db_array_to_insert_sqladd($newData);
    // echo "INSERT INTO luxdupes_bang_order $sqladd";
    $orderId=bang_order_create($newData);

    $dt = new DateTime();
    $dt->setTimestamp($time); 
    $oIdStr='00000'.$orderId;
    $orderCode=$dt->format('y').$dt->format('m').$dt->format('d').substr($oIdStr,-3);

    

    
    $update_thread['bang_order+'] = 1;


    bang_order_update($orderId,array('orderCode' => $orderCode,));

    $smtplist = include _include(APP_PATH . 'conf/smtp.conf.php');
    $smtp = $smtplist[0];
    $manEmail = $smtp['email'];
      // 发送订单邮件
    $email = $deliveryAddress->email;
    $totalPrice= $deliveryAddress->totalPrice.'';
     $subject = "Great! Your Order (#$orderCode) Is Ready - Just One Step Left: Complete Payment Now -  $sitename";

     $message = getPaymentMessageHtml(
      $deliveryAddress,$goodsArr,$orderCode,
      $price_total,$payment,$manEmail,$conf,$time);

    $manMessage=" <h2 style='color: #720eec;'>OrderCode: $orderCode</h2>
                $table

                <h2 style='color: #720eec;'>Shipping address</h2>
                $billing
                来源：$sitename";
    
    $message = preg_replace('/\s+/', ' ', $message);

    $isSendMail=true;
    foreach($goodsArr as $pro){
      if($pro->price<1){
        $isSendMail=false;
        break;
      }
    }

    if($isSendMail){
       $r = xn_send_mail($smtp, $conf['sitename'], $email, $subject, $message);
    }
   


    $r = xn_send_mail($smtp, $conf['sitename'],$smtp['email'],"新订单：$orderCode", $manMessage);
   
    $ms2=str_replace("'",'‘',$message);
    $paymentData="{'oid':$orderId,'orderCode':$orderCode,'time':$time,'alipayId':'$alipayId','familyName':'$familyName','givenName':'$givenName','phone':'$phone','message':'$ms2'}";
   
    message(0, $paymentData);
    //bang_order_create

    break;
  default:
    include _include(theme_load('cart_list', '', 'bang_order'));
}
