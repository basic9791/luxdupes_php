<?php exit;
if (1 != $gid && 1 != $comment['status'] && group_access($gid, 'commentverify')) {
    $update['status'] = 1;

    // 删除小表
    comment_pid_delete($pid);

    // 创建审核表
    well_comment_verify_create(array('pid' => $pid, 'tid' => $tid, 'uid' => $comment['uid'], 'last_date' => $time));

    user_update($comment['uid'], array('comments-' => 1, 'well_verify_comments+' => 1));
}
?>