<?php

/**
 * require status,email in the $user object
 * @param $user object
 * @param $skipEmail boolean
 * @return $response object
*/
function doUserStatusCondition($user, $skipEmail = false) {
    $response = initResponse();
    if($user->status == 'pending' && !$skipEmail) {
        $response->message = "Your account is pending. ";
        if($user->email_verified == 'n') $response->message = "Please confirm that is $user->email is your email. ";
    } elseif($user->status == 'inactive') {
        $response->message = "Your account is inactive, please ask customer service for further information. ";
    } elseif($user->status == 'banned') {
        $response->message = "Your account is banned. ";
    } else {
        $response->success = true;
        $response->message = "OK";
    }
    return $response;
}

/**
 * examples
 * 
 * $status = getUserStatus(-1); // output : [] of status
 * 
 * $status = getUserStatus('active'); // output : Active
 * @return mixed string|array
*/
function getUserStatus($no) {
    $status = [
        'active' => 'Active',
        'banned' => 'Banned',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
    ];
    if($no == -1) return $status;
    if(isset($status[$no])) return $status[$no];
    else return $no;
}

/**
 * examples
 * 
 * $status = getUserType(-1); // output : [] of type
 * 
 * $status = getUserType('agent'); // output : Red Member
 * @return mixed string|array
*/
function getUserType($no) {
    $type = [
        'agent' => 'Red Member',
        'nonagent' => 'Non Red Member',
    ];
    if($no == -1) return $type;
    if(isset($type[$no])) return $type[$no];
    else return $no;
}

/**
 * examples
 * 
 * $status = getUserType(-1); // output : [] of type
 * 
 * $status = getUserType('agent'); // output : Red Member
 * @return mixed string|array
*/
function getUserLevel($no) {
    $type = [
        '0' => 'Red Member',
        '1' => 'Green Member',
        '2' => 'Blue Member',
    ];
    if($no == -1) return $type;
    if(isset($type[$no])) return $type[$no];
    else return $no;
}

