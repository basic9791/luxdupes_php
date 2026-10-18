<?php
/*
 * CLI 优惠券邮件发送任务（不受 web 超时限制）
 *
 * 用法（在项目根目录执行）：
 *   php plugin/bang_order/cli/send_coupon.php
 *   php plugin/bang_order/cli/send_coupon.php --task=12
 *   php plugin/bang_order/cli/send_coupon.php --limit=200 --sleep=100000
 *   php plugin/bang_order/cli/send_coupon.php --loop          # 常驻，每 30 秒扫一次
 *
 * 参数说明：
 *   --task=ID   只处理指定任务
 *   --limit=N   本次最多发送 N 封（0 表示不限）
 *   --sleep=N   每封之间间隔 N 微秒（默认 0，建议 100000 = 0.1 秒）
 *   --loop      常驻循环执行
 *   --interval=N  loop 模式下扫描间隔秒数，默认 30
 *
 * crontab 示例（每分钟执行一次）：
 *   * * * * * /usr/bin/php /网站根目录/plugin/bang_order/cli/send_coupon.php >> /tmp/coupon_send.log 2>&1
 */

if ('cli' != PHP_SAPI) exit("CLI only.\r\n");

define('APP_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
!defined('DEBUG') AND define('DEBUG', 0);
!defined('ADMIN_PATH') AND define('ADMIN_PATH', APP_PATH . 'admin/');
!defined('XIUNOPHP_PATH') AND define('XIUNOPHP_PATH', APP_PATH . 'xiunophp/');

// CLI 下补齐 web 变量，避免 xiunophp 初始化报警
$_GET = array();
$_POST = array();
$_COOKIE = array();
$_REQUEST = array();
$_SERVER['REQUEST_URI'] = '';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
!isset($_SERVER['HTTP_USER_AGENT']) AND $_SERVER['HTTP_USER_AGENT'] = 'cli';

$conf = (@include APP_PATH . 'conf/conf.php') OR exit("conf/conf.php not found.\r\n");

substr($conf['log_path'], 0, 2) == './' AND $conf['log_path'] = APP_PATH . $conf['log_path'];
substr($conf['tmp_path'], 0, 2) == './' AND $conf['tmp_path'] = APP_PATH . $conf['tmp_path'];
substr($conf['upload_path'], 0, 2) == './' AND $conf['upload_path'] = APP_PATH . $conf['upload_path'];
$_SERVER['conf'] = $conf;

set_time_limit(0);
ini_set('memory_limit', '512M');

include XIUNOPHP_PATH . 'xiunophp.min.php';
include APP_PATH . 'model/plugin.func.php';
include XIUNOPHP_PATH . 'xn_send_mail.func.php';
include APP_PATH . 'plugin/bang_order/model/bang_coupon.func.php';
include APP_PATH . 'plugin/bang_order/model/bang_order.func.php';
include APP_PATH . 'plugin/bang_order/model/bang_coupon_task.func.php';

//------------> 参数
$opt = array('task' => 0, 'limit' => 0, 'sleep' => 0, 'loop' => 0, 'interval' => 30);
foreach (array_slice($argv, 1) as $arg) {
    $arg = trim($arg);
    if (FALSE === strpos($arg, '--')) continue;
    $arg = ltrim($arg, '-');
    if (FALSE === strpos($arg, '=')) {
        $opt[$arg] = 1;
    } else {
        list($k, $v) = explode('=', $arg, 2);
        $opt[$k] = $v;
    }
}
$opt['task'] = intval($opt['task']);
$opt['limit'] = intval($opt['limit']);
$opt['sleep'] = intval($opt['sleep']);
$opt['interval'] = max(5, intval($opt['interval']));

//------------> 进程锁，避免并发执行
$lockfile = $conf['tmp_path'] . 'bang_coupon_send.lock';
$lockfp = @fopen($lockfile, 'w');
if (!$lockfp) {
    echo "[" . date('Y-m-d H:i:s') . "] cannot create lock file: $lockfile\r\n";
    exit(1);
}
if (!flock($lockfp, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] another process is running, exit.\r\n";
    exit(0);
}

function coupon_cli_log($s)
{
    echo "[" . date('Y-m-d H:i:s') . "] " . $s . "\r\n";
    function_exists('xn_log') AND xn_log($s, 'bang_coupon_send');
}

coupon_cli_log('start, opt: ' . xn_json_encode($opt));

//------------> 主循环
do {
    $time = time();

    $cond = array();
    if ($opt['task']) {
        $cond['id'] = $opt['task'];
    } else {
        // 待发送 / 发送中，且到了计划的发送时间
        $cond['status'] = array(0, 1);
        $cond['send_time'] = array('<=' => $time);
    }

    $tasklist = bang_coupon_task__find($cond, array('id' => 1), 1, 100);
    if (empty($tasklist)) {
        coupon_cli_log('no pending task.');
    } else {
        foreach ($tasklist as $task) {
            coupon_cli_log("run task #{$task['id']}, coupon: {$task['coupon_code']}, total: {$task['total']}");
            $rs = bang_coupon_task_run($task['id'], $opt['limit'], $opt['sleep']);
            coupon_cli_log("task #{$task['id']} result: sent={$rs['sent']}, fail={$rs['fail']}, total={$rs['total']}, msg={$rs['message']}");
        }
    }

    if ($opt['loop']) {
        sleep($opt['interval']);
    }
} while ($opt['loop']);

flock($lockfp, LOCK_UN);
fclose($lockfp);

coupon_cli_log('finished.');
exit(0);
