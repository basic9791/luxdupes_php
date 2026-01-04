<?php exit;
// 审核后写入小表，更新最后回复uid pid
well_comment_verify_create(array('pid' => $pid, 'tid' => $post['tid'], 'uid' => $uid, 'last_date' => $time));

$user_update['well_verify_comments+'] = 1;
?>