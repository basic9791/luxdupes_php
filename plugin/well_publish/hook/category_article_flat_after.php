<?php exit;
$orderby = param('orderby');
if ('lastpid' == $orderby) {
    $active = 'lastpid';
    $thread_list_from_default = 'lastpid';

    $fids = array();
    $threads = 0;
    if ($forumlist_show) {
        foreach ($forumlist_show as $key => $val) {
            if ($val['fup'] == $fid && 1 == $val['type'] && 0 == $val['category']) {
                $fids[] = $val['fid'];
                $threads += $val['threads'];
            }
        }
    }

    $tidlist = well_thread_find_tid_by_fid_lastpid($fids, $page, $pagesize);
}
?>