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
        7 => 'User Confirmed',
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
        1 => 'Checking Device',
        2 => 'Wait Appointment',
        3 => 'On Appointment',
        4 => 'Finished',
        5 => 'Failed',
        6 => 'Cancelled',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}
