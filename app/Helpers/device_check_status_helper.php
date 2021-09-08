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
        4 => 'Payment On Process', // setelah status internal 8
        5 => 'Completed',
        6 => 'Failed', // setelah status internal 4 lalu gagal
        7 => 'Cancelled', // setelah status internal 8 lalau gagal
        8 => 'Appointment Confirm', // setelah status internal 3
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}
