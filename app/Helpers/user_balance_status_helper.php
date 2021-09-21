<?php

/*
@return string grade
examples
$status = getUserBalanceStatus(-1); // output : [] of status
$status = getUserBalanceStatus(2); // output : Pending
*/
function getUserBalanceStatus($no) {
    $status = [
        1 => 'Success',
        2 => 'Pending',
        3 => 'Failed',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return mixed string|array
examples
$status_user_payout_detail = getPayoutDetailStatus(-1); // output : [] of status
$status_user_payout_detail = getPayoutDetailStatus('PENDING'); // output : Pending
*/
function getPayoutDetailStatus($no) {
    $status = [
        'PENDING' => 'Pending',
        'FAILED' => 'Failed',
        'COMPLETED' => 'Completed',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}