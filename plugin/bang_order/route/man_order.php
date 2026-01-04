<?php
/*
 * Copyright (C) 
*/
!defined('DEBUG') and exit('Access Denied.');


$safe_token = well_token_set($uid);
$action = param(1);

include _include(APP_PATH . 'plugin/bang_order/model/bang_order.func.php');
include _include(XIUNOPHP_PATH . 'xn_send_mail.func.php');


switch ($action) {
  case 'sendEmail':
    $dataStr = str_replace('&quot;', '"', param('deliveryAddress'));
    $deliveryAddress = json_decode($dataStr);
    $goods = param('cartData');
    $goodsArr=json_decode(str_replace('&quot;', '"', $goods));
    $id = param('id');
    $orderCode = param('orderCode');
    $price_total = param('price_total');
    $payment = param('payment');
    $create_date = param('create_date');
    $email = param('email');

    $smtplist = include _include(APP_PATH . 'conf/smtp.conf.php');
    $smtp = $smtplist[0];
    $manEmail = $smtp['email'];

    $message = getPaymentMessageHtml(
      $deliveryAddress,$goodsArr,$orderCode,
      $price_total,$payment,$manEmail,$conf,$create_date);
    $subject = "Great! Your Order (#$orderCode) Is Ready - Just One Step Left: Complete Payment Now - ". $conf['sitename'];

    $r = xn_send_mail($smtp, $conf['sitename'], $email, $subject, $message);
    message(0, "{'message':'ok'}");
    break;
  case 'update':
      // `state` 状态 1.待支付 2.已支付 3.运输中 4.已收货 5.已完成 9.已取消
    $shipping_code=param('shipping_code');
    $shipping_link=param('shipping_link');
    $shipping_name=param('shipping_name');
    $price_total = param('price_total');
    $notes_man=param('notes_man');
    $goods = param('goods');
    $state=param('state');
    $orderCode=param('orderCode');
    $first_name=param('first_name');
    $email=param('email');
    $id=param('id');
    
    
    bang_order_update($id,array(
      'shipping_code' => $shipping_code,
      'shipping_link' => $shipping_link,
      'shipping_name' => $shipping_name,
      'state' => $state,
      'goods' => $goods,
      'price_total' => $price_total,
      'notes_man' => $notes_man,
    ));
    $shipping_link=$shipping_link?$shipping_link:"https://t.17track.net/en#nums=$shipping_code";
    if($state==2||$state==3||$state==5){
      $sitename=$conf['sitename'];
      $domain=$conf['domain'];
      $smtplist = include _include(APP_PATH . 'conf/smtp.conf.php');
      $smtp = $smtplist[0];
      $subject="[Order #$orderCode] Status Update Notification";
      $emailStr="Dear $first_name <br/>";
        if($state==2){
          $emailStr.="We have received the payment for your order, and currently, we are in the process of preparing the goods for shipment. Please be patient and wait a little while longer.<br/>";
        }elseif($state==3){
          $emailStr.="Your order has been shipped. The logistics tracking number is: $shipping_code.<br/>
          You can check the shipping progress through the following link<a href='$shipping_link'>$shipping_link</a>
          ";
        }elseif($state==5){
          $subject="[Order #$orderCode] Review Request";
          $emailStr.="Your order has been successfully received, marking the completion of the entire shopping process.<br/>
          To continuously improve our service quality and enhance your shopping experience, we kindly request that you log in to our website to share your shopping feedback. Alternatively, you can also reply to this email directly with your comments.
          ";
        }

        
        $emailStr.="Thanks for using $sitename!<br />";
        $emailStr.="<br/>Email:". $smtp['email'];
        $emailStr.="<br/>Website:<a href='https://$domain'>https://$domain</a> <br />";

        
        $r = xn_send_mail($smtp, $conf['sitename'], $email, $subject, $emailStr);
    }
    message(0, "{'message':'ok'}");
    break;
  default:

    $page = param('page');
    $pagesize = 20;
    $safe_token = well_token_set($uid);
    $extra = array('safe_token' => $safe_token); // 插件预留
    $num = 0;
    $orderCode=param('orderCode');
    $state=param('state');
    $first_name=param('firstName');
    $cond = array();

     $page_url = '/admin/index.php?0=bangOrder&page={page}';
    //  $page_url = url('home-order-{page}', $extra);
    if($orderCode){
      $cond['orderCode']=$orderCode;
      $page_url.="&orderCode=$orderCode";
    }
    if($state){
      $cond['state']=$state;
      $page_url.="&state=$state";
    }
    if($first_name){
      $cond['first_name']=$first_name;
      $page_url.="&first_name=$first_name";
    }
   

   
    $threadlist = bang_order_find_by( $cond,$page, $pagesize) ;
    $num =  bang_order__count( $cond);


    // hook user_order_get_center.php



   

    // hook user_order_get_pagination_before.php

    // $pagination = pagination($page_url, $num, $page, $pagesize);
    $pagination = pager($page_url, $num, $page, $pagesize);











    include _include(APP_PATH . 'plugin/bang_order/view/htm/order_list.htm');
}
