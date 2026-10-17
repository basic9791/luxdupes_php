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
include _include(APP_PATH . 'plugin/bang_order/model/bang_coupon.func.php');

switch ($action) {
  case 'coupon':
    // 应用优惠券：校验并返回优惠金额（不核销，核销在下单时）
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

    $coupon_code = trim(param('coupon_code'));
    $amount = param('amount', 0);
    $coupon_code or message(1, 'Please enter a coupon code');

    $rs = bang_coupon_check($coupon_code, $amount);
    if (0 != $rs['code']) message(1, $rs['message']);

    $data = array(
      'code' => $rs['coupon']['code'],
      'name' => $rs['coupon']['name'],
      'type' => $rs['coupon']['type'],
      'value' => $rs['coupon']['value'],
      'discount' => $rs['discount'],
      'payable' => $rs['payable'],
    );
    message(0, xn_json_encode($data));
    break;
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

    // -------- 优惠券处理（服务端重新校验，不信任前端传来的金额） --------
    $coupon_code = trim(param('coupon_code', ''));
    $coupon_amount = 0;
    $coupon = NULL;
    if ($coupon_code) {
        // 商品原价小计
        $goods_total = 0;
        if ($goodsArr) {
            foreach ($goodsArr as $pro) {
                $goods_total += floatval($pro->num) * floatval($pro->price);
            }
        }
        $rs = bang_coupon_check($coupon_code, $goods_total);
        if (0 != $rs['code']) message(1, $rs['message']);
        $coupon_amount = $rs['discount'];
        $price_total = $rs['payable']; // 以服务端计算的应付金额为准
        $coupon = array(
            'code' => $rs['coupon']['code'],
            'name' => $rs['coupon']['name'],
            'discount' => $rs['discount'],
        );
    }

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
      'notes_man'=>$sitename,
      'coupon_code' => $coupon_code,
      'coupon_amount' => $coupon_amount,
    );

    // $sqladd = db_array_to_insert_sqladd($newData);
    // echo "INSERT INTO luxdupes_bang_order $sqladd";
    $orderId=bang_order_create($newData);

    // 下单成功，核销优惠券（一次性置为已用并禁用；可重复则累加次数）
    if ($coupon_code) {
      bang_coupon_use($coupon_code);
    }

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
      $price_total,$payment,$manEmail,$conf,$time,$coupon);


    
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
   

    $myEmail = $smtp['email'];

     $messageMain = "<div style='background:#f5f5f5;padding:10px; margin-bottom:25px'>
              <div><b>Form:</b> $email</div>
              <div><b>Date:</b> $myEmail</div>
              <div><b>To:</b> $email</div>     
              <div><b>Subject:</b> $subject</div></div>
              <div style='border:1px solid #ccc; padding:10px'>
              $message
              </div>
              ";

    $r = xn_send_mail($smtp, $conf['sitename'],$smtp['email'],"新订单：$orderCode", $messageMain);
   
    $ms2=str_replace("'",'‘',$message);
    $paymentData="{'oid':$orderId,'orderCode':$orderCode,'time':$time,'alipayId':'$alipayId','familyName':'$familyName','givenName':'$givenName','phone':'$phone','message':'$ms2'}";
   
    message(0, $paymentData);
    //bang_order_create

    break;
  default:
    include _include(theme_load('cart_list', '', 'bang_order'));
}
