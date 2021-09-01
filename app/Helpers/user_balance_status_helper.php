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

