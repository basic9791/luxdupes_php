<?php
/*
 * Copyright (C) www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

function url_sitemap($url, $suffix = 'xml')
{
    $conf = _SERVER('conf');

    !isset($conf['url_rewrite_on']) AND $conf['url_rewrite_on'] = 0;

    $r = $path = $query = '';
    if ($url && FALSE !== strpos($url, '/')) {
        $path = substr($url, 0, strrpos($url, '/') + 1);
        $query = substr($url, strrpos($url, '/') + 1);
    } else {
        $path = '';
        $query = $url;
    }

    if (0 == $conf['url_rewrite_on']) {
        $r = $path . '?' . $query;
    } elseif (1 == $conf['url_rewrite_on']) {
        $r = $path . $query;
    } elseif (2 == $conf['url_rewrite_on'] || 3 == $conf['url_rewrite_on']) {
        $r = $conf['path'] . str_replace('-', '/', $query);
    }

    return $r . '.'. $suffix;
}

$count = thread_tid_count();
$num = ceil($count / 10000);

$http_url = str_replace('/admin', '', http_url_path());

include _include(APP_PATH . 'plugin/well_sitemap/view/htm/map.htm');

?>