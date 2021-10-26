<?php

/*
@return string grade
examples
$grade = getGrade(); // output : Bronze-1
$grade = getGrade('S'); // output : Diamond
*/
function getLogCategory($no = -1) {
    $categories = [
        1   => 'Price: Create',
        2   => 'Price: Update',
        3   => 'Price: Delete',
        4   => 'Promo: Create',
        5   => 'Promo: Update',
        6   => 'Promo: Delete',
        7   => 'Transaction: Proceed Payment',
        8   => 'Transaction: Manual Transfer',
        9   => 'Transaction: Mark As Failed',
        10  => 'Transaction: Confirm Appointment',
        11  => 'Admin: Create',
        12  => 'Admin: Update',
        13  => 'Admin: Delete',
        14  => 'Admin Role: Create',
        15  => 'Admin Role: Update',
        16  => 'Admin Role: Delete',
        17  => 'Commission Rate: Create',
        18  => 'Commission Rate: Update',
        19  => 'Commission Rate: Delete',
        20  => 'Price: Import',
        21  => 'Price: Delete All',
        22  => 'Withdraw: Proceed Payment',
        23  => 'Withdraw: Manual Transfer',
        24  => 'Transaction: Change Payment Detail',
        25  => 'Setting: Update Available Date & Time',
        26  => 'Setting: Update Value Setting',
        27  => 'Transaction: Change Address Detail',
        28  => 'Transaction: Change Courier Detail',
        29  => 'Transaction: Request Payment',
        30  => 'Transaction: Change Appoinment Time',
        31  => 'Transaction: Change Grade',
        32  => 'User: Submission Accepted',
        33  => 'User: Withdraw',
        34  => 'User: Email Verification',
        35  => 'Device Check: Check Finished',
        36  => 'Device Check: Checking Device',
        37  => 'Transaction: Completed',
        38  => 'User: Register',
        39  => 'Device Check: Identity Filled',
        40  => 'Device Check: On Appointment',
        41  => 'Device Check: Photo Uploaded',
        42  => 'Device Check: Reviewed',
        43  => 'Device Check: Request Cancel',
        44  => 'Device Check: Request Payment',
        45  => 'Device Check: Scanned',
        46  => 'Device Check: Software Checked',
        47  => 'Device Check: Unrviewed',
        48  => 'Device Check: Waiting Appointment',
        49  => 'User: Resend Phone OTP',
        50  => 'User: Phone Verified',
        51  => 'User: Change Email',
        52  => 'User: Email Verified',
        53  => 'User: Add Payment Method',
        54  => 'User: Update Payment Method',
        55  => 'User: Submission',
        56  => 'User: Set Pin',
        57  => 'User: Update Pin',
        58  => 'User: Delete Payment Method',
        59  => 'User: Submission Rejected',
        60  => 'User: Withdraw Completed',
        61  => 'Transaction: Payment Completed',
    ]; // index terakhir: 48 (selalu update ini ketika tambah)
    if($no == -1) {
        asort($categories);
        return $categories;
    }
    elseif(isset($categories[$no])) return $categories[$no];
    else return $no;
}
