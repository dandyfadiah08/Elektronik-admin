<?php

/*
@return string grade
examples
$status = getDeviceCheckStatus(-1); // output : [] of status
$status = getDeviceCheckStatus(4); // output : Unreviewd
*/
function getDeviceCheckStatus($no) {
    $status = [
        1 => 'Software Checked',
        2 => 'Scanned',
        3 => 'Photo Uploaded',
        4 => 'Unreview',
        5 => 'Reviewed',
        6 => 'Identity Filled',
        7 => 'Finished',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return string grade
examples
$status = getDeviceCheckStatusInternal(-1); // output : [] of status
$status = getDeviceCheckStatusInternal(4); // output : Finished
*/
function getDeviceCheckStatusInternal($no) {
    $status = [
        1 => 'Checking Device', // status 1-6
        2 => 'Wait Appointment', // status 7
        3 => 'On Appointment',
        4 => 'Payment On Process', // setelah status_internal 10
        5 => 'Completed',
        6 => 'Failed', // setelah status_internal 4 lalu gagal
        7 => 'Cancelled', // setelah status_internal 8 lalu gagal
        8 => 'Appointment Confirm', // setelah status_internal 3
        9 => 'Request Cancel', // untuk status_internal 8
        10 => 'Request Payment', // untuk status_internal 8
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}
