<?php
/*
 * Copyright (C) 
 */

// hook model_bang_coupon_start.php

// ------------> 原生CURD，无关联其他数据。
function bang_coupon_create($arr = array(), $d = NULL)
{
    // hook model_bang_coupon_create_start.php
    $r = db_insert('bang_coupon', $arr, $d);
    // hook model_bang_coupon_create_end.php
    return $r;
}

function bang_coupon__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_coupon__update_start.php
    $r = db_update('bang_coupon', $cond, $update, $d);
    // hook model_bang_coupon__update_end.php
    return $r;
}

function bang_coupon__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_bang_coupon__read_start.php
    $r = db_find_one('bang_coupon', $cond, $orderby, $col, $d);
    // hook model_bang_coupon__read_end.php
    return $r;
}

function bang_coupon__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_bang_coupon__find_start.php
    $arr = db_find('bang_coupon', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_bang_coupon__find_end.php
    return $arr;
}

function bang_coupon__delete($cond = array(), $d = NULL)
{
    // hook model_bang_coupon__delete_start.php
    $r = db_delete('bang_coupon', $cond, $d);
    // hook model_bang_coupon__delete_end.php
    return $r;
}

function bang_coupon__count($cond = array(), $d = NULL)
{
    // hook model_bang_coupon__count_start.php
    $n = db_count('bang_coupon', $cond, $d);
    // hook model_bang_coupon__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function bang_coupon_update($id, $update)
{
    if (!$id || empty($update)) return FALSE;
    // hook model_bang_coupon_update_start.php
    $r = bang_coupon__update(array('id' => $id), $update);
    // hook model_bang_coupon_update_end.php
    return $r;
}

function bang_coupon_read($id)
{
    // hook model_bang_coupon_read_start.php
    $r = bang_coupon__read(array('id' => $id));
    // hook model_bang_coupon_read_end.php
    return $r;
}

// 按优惠码读取（唯一键）
function bang_coupon_read_by_code($code)
{
    $code = trim($code);
    if (empty($code)) return NULL;
    // hook model_bang_coupon_read_by_code_start.php
    $r = bang_coupon__read(array('code' => $code));
    // hook model_bang_coupon_read_by_code_end.php
    return $r;
}

function bang_coupon_delete($id)
{
    if (empty($id)) return FALSE;
    // hook model_bang_coupon_delete_start.php
    $r = bang_coupon__delete(array('id' => $id));
    // hook model_bang_coupon_delete_end.php
    return $r;
}

function bang_coupon_find($cond = array(), $page = 1, $pagesize = 20)
{
    // hook model_bang_coupon_find_start.php
    $arrlist = bang_coupon__find($cond, array('id' => -1), $page, $pagesize);
    if (empty($arrlist)) return NULL;

    foreach ($arrlist as &$val) {
        bang_coupon_format($val);
    }
    // hook model_bang_coupon_find_end.php
    return $arrlist;
}

function bang_coupon_format(&$val)
{
    if (empty($val)) return;

    // hook model_bang_coupon_format_start.php

    $val['create_date_fmt'] = $val['create_date'] ? date('Y-m-d H:i', $val['create_date']) : '';
    $val['start_time_fmt'] = $val['start_time'] ? date('Y-m-d', $val['start_time']) : '';
    $val['end_time_fmt'] = $val['end_time'] ? date('Y-m-d', $val['end_time']) : '';

    // 优惠类型：1.固定金额 2.订单百分比
    $val['type_fmt'] = 1 == $val['type'] ? 'Fixed Amount' : 'Percentage';
    // 使用类型：1.一次性 2.可重复
    $val['use_type_fmt'] = 1 == $val['use_type'] ? 'One-time' : 'Reusable';
    // 状态：1.启用 0.禁用
    $val['status_fmt'] = 1 == $val['status'] ? 'Enabled' : 'Disabled';

    // 优惠值展示
    $val['value_fmt'] = 1 == $val['type'] ? '$' . $val['value'] : $val['value'] . '%';

    // 是否已过期 / 是否可用
    $time = time();
    $val['is_expire'] = ($val['end_time'] > 0 && $val['end_time'] < $time) ? 1 : 0;
    $val['is_used_up'] = 0;
    if (1 == $val['use_type']) {
        $val['is_used_up'] = $val['used_num'] >= 1 ? 1 : 0;
    } elseif ($val['total_num'] > 0 && $val['used_num'] >= $val['total_num']) {
        $val['is_used_up'] = 1;
    }
    $val['is_available'] = (!$val['is_expire'] && !$val['is_used_up'] && $val['status']) ? 1 : 0;

    // hook model_bang_coupon_format_end.php
}

//--------------------------业务--------------------------

/*
 * 校验优惠券并计算优惠金额
 * $code   优惠码
 * $amount 订单小计金额（优惠前）
 * 返回 array(
 *   'code'     => 0 可用 / 非0 不可用,
 *   'message'  => 提示信息,
 *   'coupon'   => 优惠券数据,
 *   'discount' => 优惠金额,
 *   'payable'  => 应付金额
 * )
 */
function bang_coupon_check($code, $amount)
{
    $amount = floatval($amount);
    $rs = array(
        'code' => -1,
        'message' => 'Invalid coupon code',
        'coupon' => NULL,
        'discount' => 0,
        'payable' => $amount
    );

    $coupon = bang_coupon_read_by_code($code);
    if (empty($coupon)) {
        $rs['message'] = 'Coupon code not found';
        return $rs;
    }

    if (empty($coupon['status'])) {
        $rs['message'] = 'This coupon has been disabled';
        return $rs;
    }

    $time = time();
    if ($coupon['start_time'] > 0 && $coupon['start_time'] > $time) {
        $rs['message'] = 'This coupon is not active yet';
        return $rs;
    }
    if ($coupon['end_time'] > 0 && $coupon['end_time'] < $time) {
        $rs['message'] = 'This coupon has expired';
        return $rs;
    }

    if ($amount < floatval($coupon['min_amount'])) {
        $rs['message'] = 'Minimum order amount is $' . $coupon['min_amount'];
        return $rs;
    }

    // 使用次数判断
    if (1 == $coupon['use_type']) {
        // 一次性
        if ($coupon['used_num'] >= 1) {
            $rs['message'] = 'This coupon has already been used';
            return $rs;
        }
    } else {
        // 可重复
        if ($coupon['total_num'] > 0 && $coupon['used_num'] >= $coupon['total_num']) {
            $rs['message'] = 'This coupon has reached its usage limit';
            return $rs;
        }
    }

    // 计算优惠金额：1.固定金额 2.订单百分比
    $discount = 0;
    if (1 == $coupon['type']) {
        $discount = floatval($coupon['value']);
    } else {
        $discount = round($amount * floatval($coupon['value']) / 100, 2);
    }
    $discount > $amount and $discount = $amount;
    $discount = round($discount, 2);

    bang_coupon_format($coupon);

    $rs['code'] = 0;
    $rs['message'] = 'Coupon applied successfully';
    $rs['coupon'] = $coupon;
    $rs['discount'] = $discount;
    $rs['payable'] = round($amount - $discount, 2);
    return $rs;
}

/*
 * 核销优惠券（下单成功时调用）
 * 一次性：次数+1 并禁用
 * 可重复：次数+1
 */
function bang_coupon_use($code)
{
    $coupon = bang_coupon_read_by_code($code);
    if (empty($coupon)) return FALSE;

    // hook model_bang_coupon_use_start.php

    $update = array('used_num+' => 1);
    if (1 == $coupon['use_type']) {
        $update['status'] = 0; // 一次性使用后禁用
    }

    $r = bang_coupon_update($coupon['id'], $update);

    // hook model_bang_coupon_use_end.php
    return $r;
}

/*
 * 接收并校验后台表单字段，返回可用于写入的数组
 * $is_create TRUE=创建时（校验唯一、初始化计数） FALSE=更新时
 * 校验失败直接 message() 中断
 */
function bang_coupon_post_data($is_create = TRUE)
{
    $code = trim(param('code'));
    $name = trim(param('name'));
    $type = param('type', 1);
    $value = param('value', 0);
    $use_type = param('use_type', 1);
    $total_num = param('total_num', 0);
    $min_amount = param('min_amount', 0);
    $status = param('status', 1);
    $notes = trim(param('notes', ''));
    $start_time = trim(param('start_time', ''));
    $end_time = trim(param('end_time', ''));

    $code or message(1, 'Coupon code is required');
    $name or message(1, 'Coupon name is required');

    // 优惠码格式：字母数字_-，3-32位
    if (!preg_match('/^[A-Za-z0-9_-]{3,32}$/', $code)) {
        message(1, 'Coupon code must be 3-32 characters of letters, numbers, - or _');
    }

    $type = intval($type);
    $use_type = intval($use_type);
    $value = floatval($value);
    $total_num = intval($total_num);
    $min_amount = floatval($min_amount);
    $status = intval($status) ? 1 : 0;

    if (!in_array($type, array(1, 2))) message(1, 'Invalid discount type');
    if (!in_array($use_type, array(1, 2))) message(1, 'Invalid usage type');
    if ($value <= 0) message(1, 'Discount value must be greater than 0');
    // 百分比不能超过 100
    if (2 == $type && $value > 100) message(1, 'Percentage cannot exceed 100');
    if (2 == $use_type && $total_num <= 0) message(1, 'Usage limit must be greater than 0');

    // 起止日期默认值：开始日期=当天，结束日期=3个月后
    if ('' === $start_time) $start_time = date('Y-m-d');
    if ('' === $end_time) $end_time = date('Y-m-d', strtotime('+3 months'));

    // 日期转时间戳（开始日期补到当天 00:00:00，结束日期补到当天 23:59:59）
    $start = $start_time ? strtotime($start_time . ' 00:00:00') : 0;
    if ($start_time && !$start) $start = strtotime($start_time);
    $end = $end_time ? strtotime($end_time . ' 23:59:59') : 0;
    if ($end_time && !$end) $end = strtotime($end_time);
    if ($start && $end && $start > $end) message(1, 'Start date cannot be later than end date');

    $arr = array(
        'code' => $code,
        'name' => $name,
        'type' => $type,
        'value' => $value,
        'use_type' => $use_type,
        'total_num' => 1 == $use_type ? 1 : $total_num,
        'min_amount' => $min_amount,
        'status' => $status,
        'start_time' => $start,
        'end_time' => $end,
        'notes' => $notes,
    );

    if ($is_create) {
        $arr['used_num'] = 0;
        $arr['create_date'] = $GLOBALS['time'];
        // 优惠码唯一
        bang_coupon_read_by_code($code) and message(1, 'Coupon code already exists');
    }

    return $arr;
}

// hook model_bang_coupon_end.php
