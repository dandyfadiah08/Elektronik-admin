<?php

/*
@return object
*/
function checkRole($role, $role_name) {
    $response = initResponse('Unauthorized.');
    if($role->{$role_name} == 'y') {
        $response->success = true;
        $response->message = 'OK';
    }
    return $response;
}
