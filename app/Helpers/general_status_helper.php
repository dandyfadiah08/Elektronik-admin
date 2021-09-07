<?php

/*
@return mixed string|array
examples
$status = getPromoStatus(-1); // output : [] of status
$status = getPromoStatus(1); // output : Active
*/
function getPromoStatus($no) {
    $status = [
        1 => 'Active',
        2 => 'Inactive',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return mixed string|array
examples
$status = getPromoStatus(-1); // output : [] of status
$status = getPromoStatus(1); // output : Success
*/
function getUserPayoutStatus($no) {
    $status = [
        1 => 'Success',
        2 => 'Pending',
        2 => 'Failed',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return mixed string|array
examples
$status = getPromoStatus(-1); // output : [] of status
$status = getPromoStatus(1); // output : Success
*/
function getUserBalanceStatus($no) {
    $status = [
        1 => 'Success',
        2 => 'Pending',
        2 => 'Failed',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

