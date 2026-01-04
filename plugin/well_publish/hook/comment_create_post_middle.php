<?php exit;
!group_access($gid, 'allowdelete') && group_access($gid, 'commentverify') AND $post['status'] = 1;
?>