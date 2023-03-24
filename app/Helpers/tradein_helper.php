<?php

/*
@return string grade
examples
$status = getDeviceCheckStatus(-1); // output : [] of status
$status = getDeviceCheckStatus(4); // output : Unreviewd
*/
function getTradeinStatus($no)
{
    $status = [
        1 => 'Data Tradein',
        2 => 'Scanned',
        3 => 'Photo Uploaded',
        4 => 'Data Tradein',
        5 => 'Reviewed',
        6 => 'Identity Filled',
        7 => 'Check Finished',
        8 => 'Retry Photo',
    ];
    if ($no == -1) return $status;
    if (isset($status[$no])) return $status[$no];
    else return $no;
}

/*
@return string grade
examples
$status = getDeviceCheckStatusInternal(-1); // output : [] of status
$status = getDeviceCheckStatusInternal(4); // output : Finished
*/
function getDeviceCheckStatusInternal($no)
{
    /**
     * urutan
     * completed: 1 > 2 > 3 > 8 > 10 > 4 > 5
     * cancelled: 1 > 2 > 3 > 7
     * cancelled: 1 > 2 > 3 > 9 > 7
     * cancelled: 1 > 2 > 3 > 8 > 7
     * cancelled: 1 > 2 > 3 > 8 > 9 > 7
     * cancelled: 1 > 2 > 3 > 8 > 10 > 7
     * failed   : 1 > 2 > 3 > 8 > 10 > 4 > 6
     */
    $status = [
        1 => 'Checking Device', // status 1-6
        2 => 'Wait Appointment', // status 7
        3 => 'On Appointment',
        4 => 'Payment On Process', // setelah status_internal 10
        5 => 'Completed',
        6 => 'Failed', // setelah status_internal 4 lalu gagal
        7 => 'Cancelled', // setelah status_internal 8 lalu gagal
        8 => 'Appointment Confirmed', // setelah status_internal 3
        9 => 'Request Cancel', // untuk status_internal 8
        10 => 'Request Payment', // untuk status_internal 8
    ];
    if ($no == -1) return $status;
    if (isset($status[$no])) return $status[$no];
    else return $no;
}

/**
 * muncul di Views/device_check/manual_grade.php
 */
function getRetryPhotoReasons()
{
    return [
        'Lainnya', // harus index ke-0
        'Foto depan kurang jelas',
        'Foto depan blur',
        'Foto depan gelap',
        'Foto depan tidak di mode dark-mode',
        'Foto depan wajib di halaman QR Code',
        'Foto belakang kurang jelas',
        'Foto belakang blur',
        'Foto belakang gelap',
    ];
}

/**
 * muncul di Views/device_check/manual_grade.php
 */
function getRetryPhotoNames()
{
    return [
        null,
        'Depan',
        'Belakang',
        'Kanan',
        'Kiri',
        'Atas',
        'Bawah',
    ];
}

/**
 * muncul di Views/device_check/manual_grade.php
 */
function getGradeDamages()
{
    return [
        'Tidak ada', // harus index ke-0
        'Lainnya', // harus index ke-1
        'LCD Shadow',
        'LCD Retak',
        'LCD whitespot',
        'LCD Dead pixel',
    ];
}
