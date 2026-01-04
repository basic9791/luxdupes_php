<?php exit;
if ('lastpid' == $thread_list_from_default && $threadlist) {
    $threadsticky = array();
    $_threadlist = array();
    foreach ($threadlist as $val) {
        isset($stickylist[$val['tid']]) and $threadsticky[$val['tid']] = $val;
        isset($tidlist[$val['tid']]) and $_threadlist[$val['tid']] = $val + $tidlist[$val['tid']];
    }
    !empty($_threadlist) and $_threadlist = array_multisort_key($_threadlist, 'lastpid', FALSE, 'tid');
    $threadlist = $threadsticky + $_threadlist;
    $extra += array('orderby' => $orderby);
}
?>