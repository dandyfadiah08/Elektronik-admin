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
$status = getUserPayoutStatus(-1); // output : [] of status
$status = getUserPayoutStatus(1); // output : Success
*/
function getUserPayoutStatus($no) {
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
$status = getUserBalanceStatus(-1); // output : [] of status
$status = getUserBalanceStatus(1); // output : Success
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
$status = getAdminStatus(-1); // output : [] of status
$status = getAdminStatus('active'); // output : Active
*/
function getAdminStatus($no) {
    $status = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return mixed string|array
examples
$status = getAdminRoleStatus(-1); // output : [] of status
$status = getAdminRoleStatus('active'); // output : Active
*/
function getAdminRoleStatus($no) {
    $status = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return mixed string|array
examples
$status = getMerchantStatus(-1); // output : [] of status
$status = getMerchantStatus('active'); // output : Active
*/
function getMerchantStatus($no) {
    $status = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/**
 * examples
 * 
 * $status = getCourierStatus(-1); // output : [] of status
 * 
 * $status = getCourierStatus('active'); // output : Active
 * 
 * @param   string|int $no
 * @return  string|array
 */
function getCourierStatus($no) {
    $status = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}
