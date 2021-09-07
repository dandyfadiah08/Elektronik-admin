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
    ];
    if($no == -1) return $categories;
    elseif(isset($categories[$no])) return $categories[$no];
    else return $no;
}
