<?php
/*
 * Copyright (C) 
*/
!defined('DEBUG') and exit('Access Denied.');


$safe_token = well_token_set($uid);
$action = param(1);

include _include(APP_PATH . 'plugin/bang_order/model/bang_order.func.php');
include _include(APP_PATH . 'plugin/bang_order/model/bang_coupon.func.php');
include _include(APP_PATH . 'plugin/bang_order/model/bang_coupon_task.func.php');
include _include(XIUNOPHP_PATH . 'xn_send_mail.func.php');


// 允许的分页数量
$pagesize_list = array(20, 50, 100, 200, 300, 500);

switch ($action) {
  // 删除订单（支持单个 / 批量）
  case 'delete':
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

    $ids = param('ids', array());
    is_array($ids) or $ids = explode(',', $ids);
    $ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids))));
    empty($ids) and message(1, 'Please select at least one order');

    $r = bang_order__delete(array('id' => $ids));
    FALSE === $r and message(1, 'Delete failed');
    message(0, 'Deleted successfully');
    break;

  // 异步查找优惠券（后台发送弹窗用）
  case 'couponSearch':
    $keyword = trim(param('keyword', ''));
    $db = $_SERVER['db'];
    $kw = addslashes($keyword);
    $sql = "SELECT * FROM {$db->tablepre}bang_coupon";
    $kw and $sql .= " WHERE `code` LIKE '%$kw%' OR `name` LIKE '%$kw%'";
    $sql .= " ORDER BY id DESC LIMIT 50";
    $list = db_sql_find($sql, 'id');
    $data = array();
    if ($list) {
      foreach ($list as $v) {
        bang_coupon_format($v);
        $data[] = array(
          'id' => $v['id'],
          'code' => $v['code'],
          'name' => $v['name'],
          'type' => $v['type'],
          'value' => $v['value'],
          'value_fmt' => $v['value_fmt'],
          'type_fmt' => $v['type_fmt'],
          'use_type' => $v['use_type'],
          'use_type_fmt' => $v['use_type_fmt'],
          'end_time' => $v['end_time'],
          'end_time_fmt' => $v['end_time_fmt'],
          'is_available' => $v['is_available'],
        );
      }
    }
    message(0, xn_json_encode($data));
    break;

  // 定时发送：只创建任务，到点后由 CLI 脚本执行（不受 web 超时限制）
  case 'couponSend':
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

    $rs = bang_coupon_task_prepare(
      param('ids', array()),
      param('coupon_code'),
      param('subject', '', FALSE),
      param('content', '', FALSE),
      param('send_time', ''),
      FALSE
    );
    if (!empty($rs['err'])) message(1, $rs['err']);

    // 已到点的任务尝试后台拉起 CLI，失败则由 cron 兜底
    $spawned = 0;
    if ($rs['send_time'] <= time()) $spawned = bang_coupon_task_spawn_cli() ? 1 : 0;

    message(0, xn_json_encode(array(
      'id' => $rs['task_id'],
      'total' => $rs['total'],
      'send_time_fmt' => date('Y-m-d H:i', $rs['send_time']),
      'spawned' => $spawned,
    )));
    break;

  // 立即发送：当前请求内直接发送，不等 CLI 定时脚本
  case 'couponSendNow':
    $safe_token = param('safe_token');
    FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

    $rs = bang_coupon_task_prepare(
      param('ids', array()),
      param('coupon_code'),
      param('subject', '', FALSE),
      param('content', '', FALSE),
      '',
      TRUE
    );
    if (!empty($rs['err'])) message(1, $rs['err']);

    // 解除 web 执行时间限制，避免大批量发送被中断
    if (function_exists('set_time_limit')) @set_time_limit(0);
    if (function_exists('ignore_user_abort')) @ignore_user_abort(TRUE);

    $run = bang_coupon_task_run($rs['task_id']);

    $task = bang_coupon_task_read($rs['task_id']);
    empty($task) and message(1, 'Task not found');
    bang_coupon_task_format($task);

    // 若未全部完成，后台再拉起 CLI 补发剩余
    $spawned = 0;
    if (2 != intval($task['status'])) $spawned = bang_coupon_task_spawn_cli() ? 1 : 0;

    message(0, xn_json_encode(array(
      'id' => $rs['task_id'],
      'total' => intval($task['total']),
      'sent' => intval($task['sent']),
      'fail' => intval($task['fail']),
      'status' => intval($task['status']),
      'status_fmt' => $task['status_fmt'],
      'spawned' => $spawned,
      'message' => isset($run['message']) ? $run['message'] : '',
    )));
    break;

  // 查询任务进度
  case 'taskStatus':
    $id = param('id');
    $task = bang_coupon_task_read($id);
    empty($task) and message(1, 'Task not found');
    bang_coupon_task_format($task);
    message(0, xn_json_encode(array(
      'id' => $task['id'],
      'status' => $task['status'],
      'status_fmt' => $task['status_fmt'],
      'total' => $task['total'],
      'sent' => $task['sent'],
      'fail' => $task['fail'],
    )));
    break;
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

    $page = max(1, intval(param('page', 1)));
    $pagesize = intval(param('pagesize', 20));
    in_array($pagesize, $pagesize_list) or $pagesize = 20;
    $safe_token = well_token_set($uid);
    $extra = array('safe_token' => $safe_token); // 插件预留
    $num = 0;
    $orderCode=param('orderCode');
    $state=param('state');
    $first_name=param('firstName');
    $country=trim(param('country'));
    $url_man=trim(param('url_man'));
    $dedup=intval(param('dedup'));
    $coupon_send_start=trim(param('coupon_send_start'));
    $coupon_send_end=trim(param('coupon_send_end'));
    $coupon_send_status=param('coupon_send_status');
    $sort=param('sort');
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
    // 国家地区（模糊）
    if($country){
      $cond['country_region']=array('LIKE'=>$country);
      $page_url.="&country=".urlencode($country);
    }
    // 订单域名 url_man（模糊）
    if($url_man){
      $cond['url_man']=array('LIKE'=>$url_man);
      $page_url.="&url_man=".urlencode($url_man);
    }
    // 过滤重复邮箱：同一邮箱只保留最早的一单
    if($dedup){
      $cond['email']=array('>'=>'');
      $page_url.="&dedup=1";
    }
    // 1.未发送过 2.已发送过
    if(''!==strval($coupon_send_status) && FALSE!==$coupon_send_status){
      if('1'==strval($coupon_send_status)){
        $cond['coupon_send_date']=0;
      }elseif('2'==strval($coupon_send_status)){
        $cond['coupon_send_date']=array('>'=>0);
      }
      $page_url.="&coupon_send_status=".intval($coupon_send_status);
    }
    // 优惠券发送日期
    if($coupon_send_start){
      $start=strtotime($coupon_send_start.' 00:00:00');
      $start and $cond['coupon_send_date']=array('>='=>$start);
      $page_url.="&coupon_send_start=".urlencode($coupon_send_start);
    }
    if($coupon_send_end){
      $end=strtotime($coupon_send_end.' 23:59:59');
      if($end){
        if(isset($cond['coupon_send_date']) && is_array($cond['coupon_send_date'])){
          $cond['coupon_send_date']['<=']=$end;
        }else{
          $cond['coupon_send_date']=array('<='=>$end);
        }
      }
      $page_url.="&coupon_send_end=".urlencode($coupon_send_end);
    }
    // 分页数量
    $page_url.="&pagesize=$pagesize";
    // 排序：默认新的在前面
    $orderby = ('asc' == $sort) ? array('id' => 1) : array('id' => -1);
    $page_url.="&sort=".('asc' == $sort ? 'asc' : 'desc');

    $threadlist = bang_order_admin_find($cond, $page, $pagesize, $dedup);
    $num = bang_order_admin_count($cond, $dedup);

    // hook user_order_get_center.php



   

    // hook user_order_get_pagination_before.php

    // $pagination = pagination($page_url, $num, $page, $pagesize);
    $pagination = pager($page_url, $num, $page, $pagesize);






    include _include(APP_PATH . 'plugin/bang_order/view/htm/order_list.htm');
}
