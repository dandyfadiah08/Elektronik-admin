<?php

/*
@return string grade
examples
$grade = getGrade(); // output : Bronze-1
$grade = getGrade('S'); // output : Diamond
*/
function getLogCategory($no = -1) {
    $categories = [
        11  => 'Admin: Create',
        13  => 'Admin: Delete',
        12  => 'Admin: Update',
        14  => 'Admin Role: Create',
        16  => 'Admin Role: Delete',
        15  => 'Admin Role: Update',
        17  => 'Commission Rate: Create',
        19  => 'Commission Rate: Delete',
        18  => 'Commission Rate: Update',
        1   => 'Price: Create',
        3   => 'Price: Delete',
        21  => 'Price: Delete All',
        20  => 'Price: Import',
        2   => 'Price: Update',
        4   => 'Promo: Create',
        6   => 'Promo: Delete',
        5   => 'Promo: Update',
        25  => 'Setting: Update Available Date & Time',
        26  => 'Setting: Update Value Setting',
        27  => 'Transaction: Change Address Detail',
        30  => 'Transaction: Change Appoinment Time',
        28  => 'Transaction: Change Courier Detail',
        31  => 'Transaction: Change Grade',
        24  => 'Transaction: Change Payment Detail',
        10  => 'Transaction: Confirm Appointment',
        8   => 'Transaction: Manual Transfer',
        9   => 'Transaction: Mark As Failed',
        7   => 'Transaction: Proceed Payment',
        29  => 'Transaction: Request Payment',
        32  => 'User: Submission',
        23  => 'Withdraw: Manual Transfer',
        22  => 'Withdraw: Proceed Payment',
    ]; // index terakhir: 32 (selalu update ini ketika tambah)
    if($no == -1) return $categories;
    elseif(isset($categories[$no])) return $categories[$no];
    else return $no;
}
