<<<<<<< HEAD
<?php

/*
@return object
*/
function checkRole($role, $role_name) {
    $response = initResponse('Unauthorized.');
    if(is_array($role_name)) {
        foreach ($role_name as $value) {
            if($role->{$value} == 'y') {
                $response->success = true;
                $response->message = 'OK';
                break;
            }
        }
    } else if($role->{$role_name} == 'y') {
        $response->success = true;
        $response->message = 'OK';
    }
    return $response;
}

/*
@param $role object
@param $role_name string|array
@return $hasAccess boolean
*/
function hasAccess($role, $role_name) {
    $hasAccess = false;
    if(is_array($role_name)) {
        foreach ($role_name as $value) {
            if($role->{$value} == 'y') {
                $hasAccess = true;
                break;
            }
        }
    } else
    $hasAccess = $role->{$role_name} == 'y';
    return $hasAccess;
}
=======
<?php

/*
@return object
*/
function checkRole($role, $role_name) {
    $response = initResponse('Unauthorized.');
    if(is_array($role_name)) {
        foreach ($role_name as $value) {
            if($role->{$value} == 'y') {
                $response->success = true;
                $response->message = 'OK';
                break;
            }
        }
    } else if($role->{$role_name} == 'y') {
        $response->success = true;
        $response->message = 'OK';
    }
    return $response;
}

/*
@param $role object
@param $role_name string|array
@return $hasAccess boolean
*/
function hasAccess($role, $role_name) {
    $hasAccess = false;
    if(is_array($role_name)) {
        foreach ($role_name as $value) {
            if($role->{$value} == 'y') {
                $hasAccess = true;
                break;
            }
        }
    } else
    $hasAccess = $role->{$role_name} == 'y';
    return $hasAccess;
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
