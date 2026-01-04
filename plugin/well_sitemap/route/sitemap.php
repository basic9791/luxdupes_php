<?php
/*
 * Copyright (C) www.wellcms.cn
*/
!defined('DEBUG') and exit('Access Denied.');

// hook sitemap_start.php

$action = param(1);

$http = http_url_path();
1 < $conf['url_rewrite_on'] and $http = rtrim($http, '/');

// hook sitemap_before.php

switch ($action) {
    // hook sitemap_case_start.php
    case 'list':
        // http://www.x.com/sitemap/list.html

        header("Content-type: text/plain");
        $str = '';
        foreach ($forumlist_show as $_forum) {

            if (0 == $_forum['threads']) continue;
            if (in_array($_forum['category'], array(2, 3)) || 0 == $_forum['type']) continue;
            $str .= "\r\n" . $http . $_forum['url'];
        }

        echo trim($str, "\r\n");
        break;
    // hook sitemap_case_end.php
    default:

        // http://www.x.com/sitemap-page.html
        header("Content-type: text/plain");
        $page = param(1, 0);
        $filename='./tmp/sitemap'.$page.'.txt';
        $str = '';
        if (file_exists($filename) && is_file($filename)) {
            // 读取文件内容
            $str = file_get_contents($filename);
        }else{
        
            $pagesize = 10000;
            $count = thread_tid_count();
            $n = ceil($count / $pagesize);
            $arrlist = thread_tid_find($page, $pagesize, FALSE);
            $tidarr = arrlist_values($arrlist, 'tid');
            $threadlist = well_thread_find($tidarr, $pagesize, FALSE);

            
            foreach ($threadlist as $_thread) {
                $url = $http . $_thread['url'];
                $str .= "\r\n" . $url;
            }
            file_put_contents($filename,$str);
        }

        echo trim($str, "\r\n");
        break;
}

// hook sitemap_end.php

function url_sitemap($url, $suffix)
{
    $conf = _SERVER('conf');

    !isset($conf['url_rewrite_on']) and $conf['url_rewrite_on'] = 0;

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

    return $r . '.' . $suffix;
}

?>