<?php

/*
@return string (days of week + 1)
examples
$days = afterAddDays(6, 1); // output : 7
$days = afterAddDays(6, 2); // output : 1
*/
function afterAddDays($current, $add){
    $value = $current + $add;
    $value = $value % 7;
    return $value;
}

/*
@return string date (Y-m-d) for get few days from now with interval
examples
$date = getTimeDay(1); // if now is 30-08-2021 output : 31-08-2021
$date = getTimeDay(2); // if now is 30-08-2021 output : 01-09-2021

*/
function getTimeDay($interval)
{
    date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
    $now = date("Y-m-d", time() + ($interval * 60 * 60 * 24)); // in hours
    return $now;
}

function check2Boolean($answer) {
    return $answer > 0;
}

function check2Text($answer) {
    return check2Boolean($answer) ? 'Tidak' : 'Ya';
}

function check2Color($answer) {
    return check2Boolean($answer) ? 'success' : 'danger';
}

function check2Icon($answer) {
    return check2Boolean($answer) ? 'check-circle' : 'times-circle';
}

