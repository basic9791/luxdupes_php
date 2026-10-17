<?php
/*
 * Copyright (C) 
 * 后台：优惠券管理
*/
!defined('DEBUG') and exit('Access Denied.');


$safe_token = well_token_set($uid);
$action = param(1);

include _include(APP_PATH . 'plugin/bang_order/model/bang_coupon.func.php');

switch ($action) {
    case 'create':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        $arr = bang_coupon_post_data(TRUE);
        $id = bang_coupon_create($arr);
        FALSE === $id and message(1, 'Create failed');
        message(0, 'Created successfully');
        break;

    case 'update':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        $id = param('id');
        $id or message(1, 'Missing id');

        $arr = bang_coupon_post_data(FALSE);
        $old = bang_coupon_read($id);
        if (!$old) message(1, 'Coupon not found');

        // 优惠码创建后禁止修改，强制保持原值
        $arr['code'] = $old['code'];

        bang_coupon_update($id, $arr);
        message(0, 'Updated successfully');
        break;

    case 'delete':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        $id = param('id');
        $id or message(1, 'Missing id');
        bang_coupon_delete($id);
        message(0, 'Deleted successfully');
        break;

    case 'read':
        $id = param('id');
        $id or message(1, 'Missing id');
        $r = bang_coupon_read($id);
        $r or message(1, 'Coupon not found');
        bang_coupon_format($r);
        message(0, xn_json_encode($r));
        break;

    default:
        $page = param('page', 1);
        $pagesize = 20;
        $safe_token = well_token_set($uid);

        $code = trim(param('code', ''));
        $status = param('status', '');

        $cond = array();
        $page_url = '/admin/index.php?0=bangCoupon&page={page}';

        if ($code) {
            $cond['code'] = $code;
            $page_url .= '&code=' . urlencode($code);
        }
        if ('' !== $status && FALSE !== $status) {
            $cond['status'] = intval($status);
            $page_url .= '&status=' . intval($status);
        }

        $couponlist = bang_coupon_find($cond, $page, $pagesize);
        $num = bang_coupon__count($cond);
        $pagination = pager($page_url, $num, $page, $pagesize);

        include _include(APP_PATH . 'plugin/bang_order/view/htm/coupon_list.htm');
}
