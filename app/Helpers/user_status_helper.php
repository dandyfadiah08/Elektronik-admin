<<<<<<< HEAD
<?php

/*
@param $user object
@param $skipEmail boolean
@return $response object
require status,email in the $user object
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

/*
@return mixed string|array
examples
$status = getUserStatus(-1); // output : [] of status
$status = getUserStatus('active'); // output : Active
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

/*
@return mixed string|array
examples
$status = getUserType(-1); // output : [] of type
$status = getUserType('agent'); // output : Agent
*/
function getUserType($no) {
    $type = [
        'agent' => 'Agent',
        'nonagent' => 'Non-Agent',
    ];
    if($no == -1) return $type;
    if(isset($type[$no])) return $type[$no];
    else return $no;
}

=======
<?php

/*
@param $user object
@param $skipEmail boolean
@return $response object
require status,email in the $user object
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

/*
@return mixed string|array
examples
$status = getUserStatus(-1); // output : [] of status
$status = getUserStatus('active'); // output : Active
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

/*
@return mixed string|array
examples
$status = getUserType(-1); // output : [] of type
$status = getUserType('agent'); // output : Agent
*/
function getUserType($no) {
    $type = [
        'agent' => 'Agent',
        'nonagent' => 'Non-Agent',
    ];
    if($no == -1) return $type;
    if(isset($type[$no])) return $type[$no];
    else return $no;
}

>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
