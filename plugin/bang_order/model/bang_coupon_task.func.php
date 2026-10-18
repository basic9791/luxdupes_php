<?php
/*
 * Copyright (C)
 * 优惠券邮件发送任务：任务记录 + 邮件内容生成 + 发送执行（供后台与 CLI 共用）
 */

// hook model_bang_coupon_task_start.php

//------------> 默认文案
function bang_coupon_task_default_subject()
{
    return 'Something Special for Your Next Luxury Find';
}

// 邮件正文中，只有大括号 { } 内的内容允许编辑，这里返回其默认值
function bang_coupon_task_default_content()
{
    return "Thank you for shopping with us! We truly appreciate your trust and support.\n\nAs a valued returning customer, we'd love to offer you an exclusive discount on your next purchase as a small token of our appreciation.";
}

//------------> 原生CURD，无关联其他数据。
function bang_coupon_task_create($arr = array(), $d = NULL)
{
    // hook model_bang_coupon_task_create_start.php
    $r = db_insert('bang_coupon_task', $arr, $d);
    // hook model_bang_coupon_task_create_end.php
    return $r;
}

function bang_coupon_task__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_bang_coupon_task__update_start.php
    $r = db_update('bang_coupon_task', $cond, $update, $d);
    // hook model_bang_coupon_task__update_end.php
    return $r;
}

function bang_coupon_task__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_bang_coupon_task__read_start.php
    $r = db_find_one('bang_coupon_task', $cond, $orderby, $col, $d);
    // hook model_bang_coupon_task__read_end.php
    return $r;
}

function bang_coupon_task__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_bang_coupon_task__find_start.php
    $arr = db_find('bang_coupon_task', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_bang_coupon_task__find_end.php
    return $arr;
}

function bang_coupon_task__delete($cond = array(), $d = NULL)
{
    // hook model_bang_coupon_task__delete_start.php
    $r = db_delete('bang_coupon_task', $cond, $d);
    // hook model_bang_coupon_task__delete_end.php
    return $r;
}

function bang_coupon_task__count($cond = array(), $d = NULL)
{
    // hook model_bang_coupon_task__count_start.php
    $n = db_count('bang_coupon_task', $cond, $d);
    // hook model_bang_coupon_task__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function bang_coupon_task_update($id, $update)
{
    if (!$id || empty($update)) return FALSE;
    // hook model_bang_coupon_task_update_start.php
    $r = bang_coupon_task__update(array('id' => $id), $update);
    // hook model_bang_coupon_task_update_end.php
    return $r;
}

function bang_coupon_task_read($id)
{
    // hook model_bang_coupon_task_read_start.php
    $r = bang_coupon_task__read(array('id' => $id));
    // hook model_bang_coupon_task_read_end.php
    return $r;
}

function bang_coupon_task_delete($id)
{
    if (empty($id)) return FALSE;
    // hook model_bang_coupon_task_delete_start.php
    $r = bang_coupon_task__delete(array('id' => $id));
    // hook model_bang_coupon_task_delete_end.php
    return $r;
}

function bang_coupon_task_find($cond = array(), $page = 1, $pagesize = 20)
{
    // hook model_bang_coupon_task_find_start.php
    $arrlist = bang_coupon_task__find($cond, array('id' => -1), $page, $pagesize);
    if (empty($arrlist)) return NULL;
    foreach ($arrlist as &$val) {
        bang_coupon_task_format($val);
    }
    // hook model_bang_coupon_task_find_end.php
    return $arrlist;
}

// 状态：0.待发送 1.发送中 2.已完成 3.已失败/取消
function bang_coupon_task_format(&$val)
{
    if (empty($val)) return;
    // hook model_bang_coupon_task_format_start.php

    $val['create_date_fmt'] = $val['create_date'] ? date('Y-m-d H:i', $val['create_date']) : '';
    $val['send_time_fmt'] = $val['send_time'] ? date('Y-m-d H:i', $val['send_time']) : '';
    $val['start_date_fmt'] = $val['start_date'] ? date('Y-m-d H:i', $val['start_date']) : '';
    $val['end_date_fmt'] = $val['end_date'] ? date('Y-m-d H:i', $val['end_date']) : '';

    $status_fmt = array('0' => 'Pending', '1' => 'Sending', '2' => 'Finished', '3' => 'Failed');
    $val['status_fmt'] = isset($status_fmt[$val['status']]) ? $status_fmt[$val['status']] : 'Unknown';

    // hook model_bang_coupon_task_format_end.php
}

//--------------------------业务--------------------------

/*
 * 生成邮件正文（HTML）
 * 只有 {} 内的 $task['content'] 是后台可编辑的，其它部分自动生成
 */
function bang_coupon_task_mail_body($task, $order, $coupon, $conf)
{
    $sitename = isset($conf['sitename']) ? $conf['sitename'] : '';
    $domain = isset($conf['domain']) ? $conf['domain'] : '';

    // 客户名称
    $first = isset($order['first_name']) ? $order['first_name'] : '';
    $last = isset($order['last_name']) ? $order['last_name'] : '';
    $name = trim($first . ' ' . $last);
    empty($name) and $name = 'there';

    $code = $coupon['code'];

    // 优惠描述：1.固定金额 2.百分比
    if (1 == $coupon['type']) {
        $off = '$' . number_format(floatval($coupon['value']), 2, '.', '') . ' OFF';
    } else {
        $off = floatval($coupon['value']) . '% OFF';
    }

    // 有效期
    $end = intval($coupon['end_time']);
    $valid = $end ? date('F j, Y', $end) : date('F j, Y');

    // 可编辑部分（{} 内），仅允许换行与文本
    $content = trim($task['content']);
    $content_html = nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));

    $html = '<div style="max-width:680px; margin:0 auto; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:1.7; color:#333;">';
    $html .= '<p>Hi ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>';
    $html .= '<p>' . $content_html . '</p>';
    $html .= '<p>Your Exclusive Coupon Code: <b style="color:#720eec; font-size:18px; letter-spacing:1px;">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</b></p>';
    $html .= '<p>Enjoy <b style="color:#720eec;">' . htmlspecialchars($off, ENT_QUOTES, 'UTF-8') . '</b> your next order!</p>';
    $html .= '<p>Valid Until: <b>' . htmlspecialchars($valid, ENT_QUOTES, 'UTF-8') . '</b></p>';
    $html .= '<p>Simply enter your coupon code at checkout before it expires to enjoy your special discount.</p>';
    $html .= '<p>Thank you for being part of our community. We look forward to welcoming you back!</p>';
    $html .= '<p>Warm regards,<br />The ' . htmlspecialchars($sitename, ENT_QUOTES, 'UTF-8') . ' Team</p>';
    $html .= '<p style="color:#999; font-size:12px;">Terms and conditions apply.</p>';
    if ($domain) {
        $html .= '<p style="color:#999; font-size:12px;">Website: <a href="https://' . htmlspecialchars($domain, ENT_QUOTES, 'UTF-8') . '">https://' . htmlspecialchars($domain, ENT_QUOTES, 'UTF-8') . '</a></p>';
    }
    $html .= '</div>';

    return $html;
}

// 读取 SMTP 配置（取第一条）
function bang_coupon_task_smtp()
{
    $file = APP_PATH . 'conf/smtp.conf.php';
    if (!is_file($file)) return NULL;
    $smtplist = include $file;
    if (empty($smtplist) || !is_array($smtplist)) return NULL;
    $smtp = array_shift($smtplist);
    return empty($smtp['user']) ? NULL : $smtp;
}

/*
 * 校验参数并创建发送任务（后台「定时发送」与「立即发送」共用）
 * $immediate TRUE 时忽略 $send_time_str，按当前时间立即发送
 * 成功返回 array('task_id'=>int, 'coupon'=>array, 'send_time'=>int, 'total'=>int)
 * 失败返回 array('err'=>错误信息)
 */
function bang_coupon_task_prepare($ids, $coupon_code, $subject, $content, $send_time_str = '', $immediate = FALSE)
{
    $ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids))));
    if (empty($ids)) return array('err' => 'Please select at least one order');

    $coupon_code = trim($coupon_code);
    if (!$coupon_code) return array('err' => 'Please select a coupon');

    $coupon = bang_coupon_read_by_code($coupon_code);
    if (empty($coupon)) return array('err' => 'Coupon not found');
    // 一次性优惠券只允许发给一单
    if (1 == $coupon['use_type'] && count($ids) > 1) {
        return array('err' => 'One-time coupon can only be sent to 1 order');
    }

    $subject = trim($subject);
    empty($subject) and $subject = bang_coupon_task_default_subject();

    $content = trim($content);
    empty($content) and $content = bang_coupon_task_default_content();

    // 发送时间，为空表示立即
    $send_time = 0;
    if (!$immediate && $send_time_str) $send_time = strtotime($send_time_str);
    if (!$send_time || $send_time < 0) $send_time = time();

    $task_id = bang_coupon_task_create(array(
        'coupon_code' => $coupon['code'],
        'subject' => $subject,
        'content' => $content,
        'send_time' => $send_time,
        'status' => 0,
        'total' => count($ids),
        'sent' => 0,
        'fail' => 0,
        'ids' => xn_json_encode($ids),
        'create_date' => time(),
        'start_date' => 0,
        'end_date' => 0,
    ));
    if (FALSE === $task_id) return array('err' => 'Create task failed');

    return array(
        'task_id' => $task_id,
        'coupon' => $coupon,
        'send_time' => $send_time,
        'total' => count($ids),
    );
}

/*
 * 执行一个发送任务
 * $limit 本次最多发送多少封（0 表示全部）
 * $sleep 每封之间间隔微秒（默认 0）
 * 返回 array('total'=>?, 'sent'=>?, 'fail'=>?)
 */
function bang_coupon_task_run($task_id, $limit = 0, $sleep = 0)
{
    $time = time();
    $rs = array('total' => 0, 'sent' => 0, 'fail' => 0, 'message' => '');

    $task = bang_coupon_task_read($task_id);
    if (empty($task)) {
        $rs['message'] = 'Task not found';
        return $rs;
    }
    if (2 == $task['status'] || 3 == $task['status']) {
        $rs['message'] = 'Task already finished';
        return $rs;
    }

    $coupon = bang_coupon_read_by_code($task['coupon_code']);
    if (empty($coupon)) {
        bang_coupon_task_update($task_id, array('status' => 3, 'end_date' => $time));
        $rs['message'] = 'Coupon not found: ' . $task['coupon_code'];
        return $rs;
    }

    $smtp = bang_coupon_task_smtp();
    if (empty($smtp)) {
        bang_coupon_task_update($task_id, array('status' => 3, 'end_date' => $time));
        $rs['message'] = 'SMTP not configured';
        return $rs;
    }

    $conf = array_value($_SERVER, 'conf', array());

    $ids = xn_json_decode($task['ids']);
    empty($ids) and $ids = array();
    $ids = array_filter(array_map('intval', (array)$ids));
    $ids = array_values(array_unique($ids));

    $rs['total'] = count($ids);
    if (empty($ids)) {
        bang_coupon_task_update($task_id, array('status' => 2, 'total' => 0, 'end_date' => $time));
        $rs['message'] = 'No order selected';
        return $rs;
    }

    bang_coupon_task_update($task_id, array(
        'status' => 1,
        'total' => $rs['total'],
        'sent' => 0,
        'fail' => 0,
        'start_date' => $time,
        'end_date' => 0,
    ));

    $sent = 0;
    $fail = 0;
    $done = intval($task['sent']); // 断点续发：跳过已成功的（按 ids 顺序）
    $i = 0;

    foreach ($ids as $id) {
        if ($limit > 0 && ($sent + $fail) >= $limit) break;

        $i++;
        // 断点续发
        if ($i <= $done) {
            $sent++;
            continue;
        }

        $order = bang_order_read($id);
        if (empty($order)) {
            $fail++;
            continue;
        }

        $email = trim($order['email']);
        $subject = $task['subject'];
        $body = bang_coupon_task_mail_body($task, $order, $coupon, $conf);

        $r = FALSE;
        if ($email && FALSE !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sitename = isset($conf['sitename']) ? $conf['sitename'] : '';
            $r = xn_send_mail($smtp, $sitename, $email, $subject, $body);
        }

        if (TRUE === $r || (is_int($r) && $r > 0)) {
            $sent++;
            // 记录发送日期与优惠码到订单
            bang_order_update($id, array(
                'coupon_send_date' => time(),
                'coupon_send_code' => $coupon['code'],
                'coupon_send_num+' => 1,
            ));
        } else {
            $fail++;
        }

        // 每 10 条回写一次进度，减少数据库压力
        if (0 == (($sent + $fail) % 10)) {
            bang_coupon_task_update($task_id, array('sent' => $sent, 'fail' => $fail));
        }

        $sleep > 0 and usleep($sleep);
    }

    $finish = ($sent + $fail) >= $rs['total'];
    bang_coupon_task_update($task_id, array(
        'sent' => $sent,
        'fail' => $fail,
        'status' => $finish ? 2 : 1,
        'end_date' => $finish ? time() : 0,
    ));

    $rs['sent'] = $sent;
    $rs['fail'] = $fail;
    $rs['message'] = 'Done';
    return $rs;
}

// 后台尝试以 CLI 方式后台拉起发送脚本（失败也不影响，cron 会兜底）
function bang_coupon_task_spawn_cli()
{
    if (!function_exists('exec')) return FALSE;
    if (ini_get('safe_mode')) return FALSE;

    $cli = APP_PATH . 'plugin/bang_order/cli/send_coupon.php';
    if (!is_file($cli)) return FALSE;

    $php = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
    $cmd = '"' . $php . '" "' . $cli . '"';

    $log = APP_PATH . 'plugin/bang_order/cli/send_coupon.log';

    try {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = 'start /B "" ' . $cmd . ' > "' . $log . '" 2>&1';
            @pclose(@popen($cmd, 'r'));
        } else {
            @exec($cmd . ' >> "' . $log . '" 2>&1 &');
        }
    } catch (Exception $e) {
        return FALSE;
    }
    return TRUE;
}

// hook model_bang_coupon_task_end.php
