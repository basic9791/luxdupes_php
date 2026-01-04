<?php exit;
if ('lastpid' == $thread_list_from_default) {
    if (!empty($forumlist_show)) {
        $fids = array();
        $threads = 0;
        foreach ($forumlist_show as $key => $val) {
            if (1 == $val['type'] && 1 == $val['display'] && 0 == $val['category']) {
                $fids[] = $val['fid'];
                $threads += $val['threads'];
            }
        }

        $tidlist = empty($fids) ? array() : well_thread_find_tid_by_fid_lastpid($fids, $page, $conf['pagesize'], TRUE);
    }
}
?>