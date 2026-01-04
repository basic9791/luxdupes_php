<?php exit;
if (isset($forum['model']) && 0 == $forum['model']) {
    $thread['url_update'] = $thread['allowupdate'] ? url('home-content', array('op'=>'update','tid' => $thread['tid'])) : '';
}
?>