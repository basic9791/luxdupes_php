<?php
/*
 * Copyright (C) 
 */

// hook model_bang_order_start.php

// ------------> 根据条件生成支付请求的邮件主体。
function getPaymentMessageHtml($deliveryAddress,$goodsArr,$orderCode,$price_total,$payment,$manEmail,$conf,$time){
       
    $alipayId=$conf['pay_alipayId']; //'86-15819915273';
    $familyName=$conf['pay_familyName']; //'YANG';
    $givenName=$conf['pay_givenName']; //'BANG CHAO';
    $phone=$conf['pay_phone']; //'15819915273';
    $dt = new DateTime();
    $dt->setTimestamp($time); 

    


   
   
    $firstName= $deliveryAddress->firstName;
    $sitename=$conf['sitename'];
    $domain=$conf['domain'];
    $monthName = strftime('%B', $time); 
    $hTitle="[Order #$orderCode] ($monthName ".$dt->format('d').", ".$dt->format('Y').")";
    

   
    $proList="";
    $billing="<div style='padding:10px; border:1px solid #ddd; margin:10px 0;'>
    $deliveryAddress->firstName $deliveryAddress->lastName<br/>
    $deliveryAddress->address <br/>
    $deliveryAddress->city $deliveryAddress->ZIP_code <br/>
    $deliveryAddress->country<br/>
    $deliveryAddress->phone<br/>
    $deliveryAddress->email<br/>
    </div>";
    
    foreach($goodsArr as $pro){
      $proList.="<tbody>
          <tr >
          <td><a href='/read/$pro->tid'>$pro->proName</a><span style='color:#aaa; padding-left:10px'>$pro->proNotes</span></td>
          <td style='color: #720eec;'>$pro->num</td>
          <td style='color: #720eec;'>$$pro->price</td>
          </tr>
          </tbody>";

    }



  
  $table="<table border='1' width='100%' cellspacing='0' cellpadding='6'>
          <thead>
          <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Price</th>
          </tr>
          </thead>
          $proList
          <tr >
          <th colspan='2'>Subtotal:</th>
          <td style='color: #720eec;'>$$price_total</td>
          </tr>
          <tr >
          <th colspan='3'>Payment method:Remitly(remitly.com or REMITLY App)</td>
          </tr>
          <tr >
          <th colspan='2'>Total:</th>
          <td style='color: #720eec;'>$$price_total</td>
          </tr>
          </table>";

    if($payment==1){
          $message = "<div style='max-width:800px; margin:0 auto'>
            <h1 style=' background-color: #720eec;        color: #fff;        padding: 25px;        margin-bottom: 15px;        font-size: 18px;        font-weight: bold;'>	Thank you for your order</h1>

                <div style='padding:15px;'>
                <span>Hi: <b style='color:#720eec'>$firstName</b></span><br />
                <span>Currently, your order is awaiting for your payment to be processed.</span><br /><br />

                <h3>Please take 1 minute to learn how to pay on Remitly (STEP BY STEP)</h3>

                Thank you so much for your purchase. We already received your order.<br />


                As a replica website, we are not allowed by most payment gateways. We are sorry for this inconvenience.<br /><br />

                Please kindly note that we just sell replica bags, we never scam our customers. Shopping with us will
                always be safe.
                All orders will be shipped out within 24hours once we received your payments.<br />
                Please use below information when paying on Remitly. (<a rel='nofollow'
                    href='https://remitly.com/'>https://remitly.com/</a>)<br />


                Here is a step-by-step instruction video showing how to pay on Remitly(<a rel='nofollow'
                    href='https://remitly.com/'>https://remitly.com/</a>):<br />
                Link: <a rel='nofollow'
                    href='https://www.youtube.com/watch?v=97SfdQNt3xs'>https://www.youtube.com/watch?v=97SfdQNt3xs</a><br /><br />




                1. Please help set to send money to China.<br />
                2. Set the delivery method as “<b style='color: #720eec;'>Alipay</b>”<br /><br />





                Recipient’s Alipay ID: <b style='color: #720eec;'>$alipayId</b><br />
                Family Name: <b style='color: #720eec;'>$familyName</b><br />
                Given Name:<b style='color: #720eec;'>$givenName</b><br />
                Recipient’s phone number: <b style='color: #720eec;'>$phone</b><br />

                Kindly help to choose the purpose of the payment as “Shopping payments”, the payment will fail for other
                purpose.<br /><br />


                Once complete the payment, please send us an email at <a rel='nofollow'
                    href='mailto:$manEmail'>$manEmail</a>.<br />
                The tracking number &amp;tracking link will be added to your order and sent to your email box after 2-3
                days’ processing time.<br />
                We provide 30 days money-back guarantee if you are not satisfied with your purchase.<br /><br />
                <h2 style='color: #720eec;'>$hTitle</h2>
                $table

                <h2 style='color: #720eec;'>Shipping address</h2>
                $billing
                Thanks for using $sitename!<br /><br />

                 <br/>Email:<a 
                    href='mailto:$manEmail'>$manEmail</a>
        <br/>Website:<a href='https://$domain'>https://$domain</a> <br /><br />

                <a rel='nofollow' style='margin:25px 0 ; display: block;' role='button' href='https://remitly.com/'
                    aria-pressed='true' class='btn btn-block btn-dark'>To Remitly</a><br />
                    </div>
    </div>";

    }elseif($payment==2){
      $message = "<div style='max-width:800px; margin:0 auto'>
            <h1 style=' background-color: #720eec;        color: #fff;        padding: 25px;        margin-bottom: 15px;        font-size: 18px;        font-weight: bold;'>	Thank you for your order</h1>

                <div style='padding:15px;'>
                <span>Hi: <b style='color:#720eec'>$firstName</b></span><br />
                Currently, your order is awaiting for your payment to be processed.<br /><br />


Thank you so much for your purchase. We already received your order.<br />
As a replica website, we are not allowed by most payment gateways. We are sorry for this inconvenience. <br />


Please kindly note that we just sell replica bags, we never scam our customers. Shopping with us will always be safe. All orders will be shipped out within 24hours once we received your payments.<br /><br />
<div>

      1.Click the button below or scan the QR code on the left to initiate payment.<br/>
      <a href='https://wise.com/pay/me/bangchaoy' style='display:inline-block; padding:5px 10px;color:#fff; background:#720eec; margin:15px 15px 15px 0;'>To Payment</a>
         Or Scan the QR code.
<img style='max-width:150px' src='//$domain/upload/wise.png'/>
     
      <br/>
      
      2.On the page, enter the total order amount ( US$ $price_total )  in the required field to proceed with the transfer.<br/><br/>
This step ensures a secure and verified transaction.
3.Once the transfer is completed, please send us a screenshot via email or to our WhatsApp support team. Our team will then begin processing your order.<br/><br/>

If you have any questions or requests, feel free to email us at:<a rel='nofollow'
                    href='mailto:$manEmail'>$manEmail</a>.
<br/>
The tracking number &tracking link will be added to your order and sent to your email box after 2-3 days’ processing time.<br/>
We provide 30 days money-back guarantee if you are not satisfied with your purchase.

</div>



                  <h2 style='color: #720eec;'>$hTitle</h2>
                $table

                <h2 style='color: #720eec;'>Shipping address</h2>
                $billing
                Thanks for using $sitename!<br /><br />


         <br/>Email:<a 
                    href='mailto:$manEmail'>$manEmail</a>
        <br/>Website:<a href='https://$domain'>https://$domain</a> <br />

    </div>";

    }elseif($payment==3){

      $message = "<div style='max-width:800px; margin:0 auto'>
            <h1 style=' background-color: #720eec;        color: #fff;        padding: 25px;        margin-bottom: 15px;        font-size: 18px;        font-weight: bold;'>	Thank you for your order</h1>

                <div style='padding:15px;'>
                <span>Hi: <b style='color:#720eec'>$firstName</b></span><br />
                Currently, your order is awaiting for your payment to be processed.<br /><br />


Thank you so much for your purchase. We already received your order.<br />
As a replica website, we are not allowed by most payment gateways. We are sorry for this inconvenience. <br /><br />


Please kindly note that we just sell replica bags, we never scam our customers. Shopping with us will always be safe. All orders will be shipped out within 24hours once we received your payments.<br /><br />
<div>

      1.Click the button below or scan the QR code on the left to initiate payment.<br/>
      <div>
      <a href='https://remit.alipay.com/?m=MTU4MTk5MTUyNzM=,QkFORyBDSEFP,WUFORw==,Q04=,IA==,IA==,IA==,IA==&d=1754875622011&v=v1.0' style='display:inline-block; padding:5px 10px;color:#fff; background:#720eec; margin:15px 15px 15px 0;'>To Payment</a> 

      </div>
        Or Scan the QR code.<br/>
<img style='max-width:629px;width:100%;' src='//$domain/upload/alipay.png'/>
     
      <br/><br/>

      Recipient Information:<br/>
        Alipay ID: <b style='color: #720eec;'> 15819915273</b><br/>
        Last Name: <b style='color: #720eec;'> YANG</b><br/>
        First Name: <b style='color: #720eec;'> BANG CHAO</b><br/>
        Total order amount: <b style='color: #720eec;'>$$price_total</b><br/><br/>
      
If you have any questions or requests, feel free to email us at:<a 
                    href='mailto:$manEmail'>$manEmail</a>.
<br/>
The tracking number &tracking link will be added to your order and sent to your email box after 2-3 days’ processing time.<br/>
We provide 30 days money-back guarantee if you are not satisfied with your purchase.
</div>



                  <h2 style='color: #720eec;'>$hTitle</h2>
                $table

                <h2 style='color: #720eec;'>Shipping address</h2>
                $billing
                Thanks for using $sitename!<br /><br />

        <br/>Email:<a 
                    href='mailto:$manEmail'>$manEmail</a>
        <br/>Website:<a href='https://$domain'>https://$domain</a> <br />

    </div>";
      

    }
    return $message;






}




// ------------> 原生CURD，无关联其他数据。
function bang_order_create($arr = array(), $d = NULL)
{   
    // echo 'sss1===============';
    // hook model_bang_order_create_start.php
    $r = db_insert('bang_order', $arr, $d);
    //  echo 'sss2===============';
    // hook model_bang_order_create_end.php
    return $r;
}

function bang_order__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_order__update_start.php
    $r = db_update('bang_order', $cond, $update, $d);
    // hook model_bang_order__update_end.php
    return $r;
}

function bang_order__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_bang_order__read_start.php
    $r = db_find_one('bang_order', $cond, $orderby, $col, $d);
    // hook model_bang_order__read_end.php
    return $r;
}

function bang_order__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_bang_order__find_start.php
    $arr = db_find('bang_order', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_bang_order__find_end.php
    return $arr;
}

function bang_order__delete($cond = array(), $d = NULL)
{
    // hook model_bang_order__delete_start.php
    $r = db_delete('bang_order', $cond, $d);
    // hook model_bang_order__delete_end.php
    return $r;
}

function bang_order__count($cond = array(), $d = NULL)
{
    // hook model_bang_order__count_start.php
    $n = db_count('bang_order', $cond, $d);
    // hook model_bang_order__count_end.php
    return $n;
}

function bang_order_big_insert($arr = array(), $d = NULL)
{
    // hook model_bang_order_big_insert_start.php
    $r = db_big_insert('bang_order', $arr, $d);
    // hook model_bang_order_big_insert_end.php
    return $r;
}

function bang_order_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_order_big_update_start.php
    $r = db_big_update('bang_order', $cond, $update, $d);
    // hook model_bang_order_big_update_end.php
    return $r;
}

//--------------------------强相关--------------------------

function bang_order_update($id, $update)
{
    global $conf;
    if (!$id || empty($update)) return FALSE;
    // hook model_bang_order_update_start.php
    $r = bang_order__update(array('id' => $id), $update);
    if ('mysql' != $conf['cache']['type']) {
        $read = bang_order_read($id);
        $read and cache_delete('bang_order_read_by_tid_and_uid_cache_' . $read['tid'] . '_' . $read['uid']);
    }
    // hook model_bang_order_update_end.php
    return $r;
}

function bang_order_read($id)
{
    // hook model_bang_order_read_start.php
    $r = bang_order__read(array('id' => $id));
    // hook model_bang_order_read_end.php
    return $r;
}

function bang_order_read_by_tid_and_uid($tid, $_uid)
{
    // hook model_bang_order_read_by_tid_and_uid_start.php
    $r = bang_order__read(array('tid' => $tid, 'uid' => $_uid));
    // hook model_bang_order_read_by_tid_and_uid_end.php
    return $r;
}

function bang_order_find($page = 1, $pagesize = 20)
{
    // hook model_bang_order_find_start.php

    $arrlist = bang_order__find(array(), array('id' => -1), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $uidarr += arrlist_values($arrlist, 'touid');

    $userlist = user__find(array('uid' => array_unique($uidarr)), array(), 1, count($uidarr));

    // hook model_bang_order_find_center.php

    foreach ($arrlist as &$val) {
        bang_order_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['to_username'] = isset($userlist[$val['touid']]) ? $userlist[$val['touid']]['username'] : '';
    }

    // hook model_bang_order_find_end.php
    return $arrlist;
}

function bang_order_find_by_id($id, $page = 1, $pagesize = 20)
{
    $key = 'bang_order_find_by_id_' . md5(xn_json_encode($id));
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc
    if (isset($cache[$key])) return $cache[$key];

    // hook model_bang_order_find_by_id_start.php
    $arrlist = bang_order__find(array('id' => $id), array(), $page, $pagesize);
    // hook model_bang_order_find_by_id_end.php
    return $arrlist;
}

// 谁购买过主题
function bang_order_find_by_tid($tid, $page = 1, $pagesize = 20)
{
    // hook model_bang_order_find_by_tid_start.php

    $arrlist = bang_order__find(array('tid' => $tid), array(), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_bang_order_find_by_tid_center.php

    foreach ($arrlist as &$val) {
        bang_order_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['online_status'] = $userlist[$val['uid']]['online_status'];
    }

    // hook model_bang_order_find_by_tid_end.php

    return $arrlist;
}

// 被哪些用户购买
function bang_order_find_by_touid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_bang_order_find_by_touid_start.php

    $arrlist = bang_order__find(array('touid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

    // hook model_bang_order_find_by_touid_before.php

    $uidarr = arrlist_values($arrlist, 'uid');
    $userlist = user_find(array('uid' => $uidarr), array(), 1, count($uidarr));

    // hook model_bang_order_find_by_touid_center.php

    $tidarr = arrlist_values($arrlist, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize, FALSE);

    // hook model_bang_order_find_by_touid_middle.php

    foreach ($arrlist as &$val) {
        bang_order_format($val);
        $val['username'] = $userlist[$val['uid']]['username'];
        $val['avatar_url'] = $userlist[$val['uid']]['avatar_url'];
        $val['subject'] = $threadlist[$val['tid']]['subject'];
        $val['url'] = $threadlist[$val['tid']]['url'];
        // hook model_bang_order_find_by_touid_foreach.php
    }

    // hook model_bang_order_find_by_touid_end.php

    return $arrlist;
}
// 查询所有订单
function bang_order_find_by($cond=array(), $page = 1, $pagesize = 20)
{
    // hook model_bang_order_find_by_uid_start.php

    $arrlist = bang_order__find($cond, array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

  

    return $arrlist;
}

// 用户购买哪些
function bang_order_find_by_uid($_uid, $page = 1, $pagesize = 20)
{
    // hook model_bang_order_find_by_uid_start.php

    $arrlist = bang_order__find(array('uid' => $_uid), array('id' => -1), $page, $pagesize);
    if (!$arrlist) return NULL;

  

    return $arrlist;
}

function bang_order_delete($id)
{
    global $conf;

    if (empty($id)) return FALSE;

    // hook model_bang_order_delete_start.php

    if ('mysql' != $conf['cache']['type']) {
        $arrlist = bang_order__find(array('id' => $id), 1, is_array($id) ? count($id) : 1);

        if (!$arrlist) return FALSE;

        foreach ($arrlist as $val) {
            cache_delete('bang_order_read_by_tid_and_uid_cache_' . $val['tid'] . '_' . $val['uid']);
        }
    }

    $r = bang_order__delete(array('id' => $id));
    if (FALSE === $r) return FALSE;

    // hook model_bang_order_delete_end.php
    return $r;
}

function bang_order_format(&$val)
{
    if (empty($val)) return;

    // hook model_bang_order_format_start.php

    $val['create_date_fmt'] = date('Y-m-d', $val['create_date']);

    $val['type_fmt'] = '';
    switch ($val['type']) {
        case '0':
            $val['type_fmt'] = lang('thread');
            break;
        // hook model_bang_order_format_case.php
    }

    // hook model_bang_order_format_end.php
}

//--------------------------cache--------------------------
// 用户是否购买
function bang_order_read_by_tid_and_uid_cache($tid, $_uid)
{
    global $conf;

    // hook model_bang_order_read_by_tid_and_uid_cache_start.php

    $key = 'bang_order_read_by_tid_and_uid_cache_' . $tid . '_' . $_uid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，跨进程需要再加一层缓存：redis/memcached/xcache/apc

    if (isset($cache[$key])) return $cache[$key];

    if ('mysql' == $conf['cache']['type']) {
        $r = bang_order_read_by_tid_and_uid($tid, $_uid);
    } else {
        $r = cache_get($key);
        if (NULL === $r) {
            $r = bang_order_read_by_tid_and_uid($tid, $_uid);
            $r and cache_set($key, $r, 300);
        }
    }

    $cache[$key] = $r ? $r : NULL;

    // hook model_bang_order_read_by_tid_and_uid_cache_end.php

    return $cache[$key];
}

// hook model_bang_order_end.php