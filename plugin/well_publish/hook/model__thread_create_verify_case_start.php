<?php exit;
case '0':
    // hook model_thread_create_verify_thread_start.php
    well_thread_verify_create(array('tid' => $tid, 'uid' => $uid, 'last_date' => $time));
    // hook model_thread_create_verify_thread_before.php
    $user_update += array('well_verify_threads+' => 1);
    // hook model_thread_create_verify_thread_start.php
    break;
?>