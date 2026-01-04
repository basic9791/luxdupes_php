<?php exit;
$orderby = param('orderby');
if ('lastpid' == $orderby) {
    $active = 'lastpid';
    $thread_list_from_default = 'lastpid';
    $tidlist = well_thread_find_tid_by_fid_lastpid($fid, $page, $pagesize);
}
?>