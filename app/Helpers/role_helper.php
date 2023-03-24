<?php

/*
@return object
*/
function checkRole($role, $nama_role)
{
    $response = initResponse('Unauthorized.');

    if (is_array($nama_role)) {
        foreach ($nama_role as $value) {
            var_dump($value);
            die;
            if ($role->{$value} == 1) {
                $response->success = true;
                $response->message = 'OK';

                break;
            }
        }
    } else if ($role->{$nama_role} == 1) {
        $response->success = true;
        $response->message = 'OK';
        var_dump($nama_role);
        die;
    }
    return $response;
}

/*
@param $role object
@param $role_name string|array
@return $hasAccess boolean
*/
function hasAccess($role, $nama_role)
{
    $hasAccess = false;
    if (is_array($nama_role)) {
        foreach ($nama_role as $value) {
            if ($role->{$value} == 1) {
                $hasAccess = true;
                $hasAccess = true;
                break;
            }
        }
    } else
        $hasAccess = $role->{$nama_role} == 1;
    return $hasAccess;
}
