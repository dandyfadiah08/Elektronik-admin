<?php

/*
@param $user object
@return $response object
require status,email in the $user object
*/
function doUserStatusCondition($user) {
    $response = initResponse();
    if($user->status == 'pending') {
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
