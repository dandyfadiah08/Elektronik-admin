<?php

/*
@return string grade
examples
$grade = getGrade(); // output : Bronze-1
$grade = getGrade('S'); // output : Diamond
*/
function getLogCategory($no = -1) {
    $categories = [
        1 => 'Price: Create',
        2 => 'Price: Update',
        3 => 'Price: Delete',
        4 => 'Promo: Create',
        5 => 'Promo: Update',
        6 => 'Promo: Delete',
        7 => 'Transaction: Proceed Payment',
        8 => 'Transaction: Manual Transfer',
        9 => 'Transaction: Mark As Failed',
        10 => 'Transaction: Confirm Appointment',
        11 => 'Admin: Create',
        12 => 'Admin: Update',
        13 => 'Admin: Delete',
        14 => 'Admin Role: Create',
        15 => 'Admin Role: Update',
        16 => 'Admin Role: Delete',
        17 => 'Commission Rate: Create',
        18 => 'Commission Rate: Update',
        19 => 'Commission Rate: Delete',
        20 => 'Price: Import',
        21 => 'Price: Delete All',
        22 => 'Withdraw: Proceed Payment',
        23 => 'Withdraw: Manual Transfer',
        24 => 'Transaction: Change Payment Detail',
        25 => 'Setting: Update Available Date & Time',
        26 => 'Setting: Update Value Setting',
        27 => 'Transaction: Change Address Detail',
        28 => 'Transaction: Change Courier Detail',
        29 => 'Transaction: Request Payment',
    ];
    if($no == -1) return $categories;
    elseif(isset($categories[$no])) return $categories[$no];
    else return $no;
}
