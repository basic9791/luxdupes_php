<?php
/*
 * Copyright (C) www.wellcms.cn
*/

// hook model_tag_thread_start.php

// ------------> 最原生的 CURD，无关联其他数据。
function well_tag_thread_create($arr, $d = NULL)
{
    if (empty($arr)) return FALSE;
    // hook model_tag_thread_create_start.php
    $r = db_replace('website_tag_thread', $arr, $d);
    // hook model_tag_thread_create_end.php
    return $r;
}

function well_tag_thread__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_tag_thread__update_start.php
    $r = db_update('website_tag_thread', $cond, $update, $d);
    // hook model_tag_thread__update_end.php
    return $r;
}

function well_tag_thread_delete($id, $d = NULL)
{
    if (empty($id)) return FALSE;
    // hook model_tag_thread_delete_start.php
    $r = db_delete('website_tag_thread', array('id' => $id), $d);
    if (FALSE === $r) return FALSE;
    // hook model_tag_thread_delete_end.php
    return $r;
}

function well_tag_thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_tag_thread__find_start.php
    $arr = db_find('website_tag_thread', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_tag_thread__find_end.php
    return $arr;
}

function well_tag_thread__count($cond = array(), $d = NULL)
{
    // hook model_tag_thread__count_start.php
    $n = db_count('website_tag_thread', $cond, $d);
    // hook model_tag_thread__count_end.php
    return $n;
}

function tag_thread_max_id($col = 'id', $cond = array(), $d = NULL)
{
    // hook model_tag_thread_max_id_start.php
    $id = db_maxid('website_tag_thread', $col, $cond, $d);
    // hook model_tag_thread_max_id_end.php
    return $id;
}

function tag_thread_big_insert($arr = array(), $d = NULL)
{
    // hook model_tag_thread_big_insert_start.php
    $r = db_big_insert('website_tag_thread', $arr, $d);
    // hook model_tag_thread_big_insert_end.php
    return $r;
}

function tag_thread_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_tag_thread_big_update_start.php
    $r = db_big_update('website_tag_thread', $cond, $update, $d);
    // hook model_tag_thread_big_update_end.php
    return $r;
}
//--------------------------强相关--------------------------

function well_tag_thread_update($id, $update)
{
    if (empty($id)) return FALSE;
    // hook model_tag_thread__update_start.php
    $r = well_tag_thread__update(array('id' => $id), $update);
    // hook model_tag_thread__update_end.php
    return $r;
}

function well_tag_thread_find($cond, $page, $pagesize)
{
    // hook model_tag_thread_find_start.php
    $arr = well_tag_thread__find($cond, array('id' => -1), $page, $pagesize);
    // hook model_tag_thread_find_end.php
    return $arr;
}

function well_tag_thread_find_desc($tagid,$tagFid,$page, $pagesize, $d = NULL)
{
    
// db_sql_find("SELECT * FROM `{$d->tablepre}website_thread` WHERE $fidWhere subject LIKE '%$keyword%' LIMIT 60", 'tid', $d);
    // hook model_tag_thread_find_start.php
    // $arr = well_tag_thread__find($cond, array('id' => -1), $page, $pagesize);
    $db = $_SERVER['db'];
    $d = $d ? $d : $db;
    if (!$d) return FALSE;
    $qNum=$page*$pagesize;
    if($page<2)$qNum=1;
    $fidWhere = "tagid=$tagid";
    if($tagFid ){
        $fidWhere.=" AND fid=$tagFid ORDER BY tid DESC";
    }else{
        $fidWhere.=" ORDER BY FIELD(fid,499,501,502,506,309,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,25,26,27,28,29,30,31,32,33,34,35,21,8,15,16,11,10,22,17,14,20,13,23,19,12,9,18,2,6,4,5,7,3,498) DESC, tid DESC";  
    }
    $sql = "SELECT * FROM  `{$d->tablepre}website_tag_thread` WHERE $fidWhere LIMIT $qNum,$pagesize"; 
    // echo "sql>>> $sql";
    $arr = db_sql_find($sql);

    
    // hook model_tag_thread_find_end.php
    return $arr;
}

function well_tag_thread_find_by_tid($tid, $page, $pagesize)
{
    // hook model_tag_thread_find_by_tid_start.php
    $arr = well_tag_thread__find(array('tid' => $tid), array(), $page, $pagesize);
    // hook model_tag_thread_find_by_tid_end.php
    return $arr;
}

// hook model_tag_thread_end.php

?>