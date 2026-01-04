<?php exit;
if ('lastpid' == $thread_list_from_default && $threadlist) {
    $threadsticky = array();
    $_threadlist = array();
    foreach ($threadlist as $val) {
        isset($stickylist[$val['tid']]) AND $threadsticky[$val['tid']] = $val;
        isset($tidlist[$val['tid']]) AND $_threadlist[$val['tid']] = $val + $tidlist[$val['tid']];
    }
    $_threadlist = array_multisort_key($_threadlist, 'lastpid', FALSE, 'tid');
    $threadlist = $threadsticky + $_threadlist;
    $extra['orderby'] = $orderby;
}
?>