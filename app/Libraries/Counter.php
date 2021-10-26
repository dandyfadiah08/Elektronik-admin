<<<<<<< HEAD
<?php

namespace App\Libraries;

use App\Models\DeviceChecks;
use App\Models\UserPayouts;
use App\Models\Users;

class Counter
{
    /*
    @response $count int
    */
    function unreviewedCount()
    {
        $DeviceCheck = new DeviceChecks();
        return $DeviceCheck->getUnreviewedCount();
    }

    /*
    @response $count int
    */
    function transactionCount()
    {
        $DeviceCheck = new DeviceChecks();
        return $DeviceCheck->getOnAppointmentCount();
        // return 1;
    }
    
    /*
    @response $count int
    */
    function withdrawCount()
    {
        $UserPayout = new UserPayouts();
        return $UserPayout->getWithdrawPendingCount();
        // return 1;
    }

    /*
    @response $count int
    */
    function submissionCount()
    {
        $User = new Users();
        return $User->getSubmissionCount();
        // return 1;
    }

}
=======
<?php

namespace App\Libraries;

use App\Models\DeviceChecks;
use App\Models\UserPayouts;
use App\Models\Users;

class Counter
{
    /*
    @response $count int
    */
    function unreviewedCount()
    {
        $DeviceCheck = new DeviceChecks();
        return $DeviceCheck->getUnreviewedCount();
    }

    /*
    @response $count int
    */
    function transactionCount()
    {
        $DeviceCheck = new DeviceChecks();
        return $DeviceCheck->getOnAppointmentCount();
        // return 1;
    }
    
    /*
    @response $count int
    */
    function withdrawCount()
    {
        $UserPayout = new UserPayouts();
        return $UserPayout->getWithdrawPendingCount();
        // return 1;
    }

    /*
    @response $count int
    */
    function submissionCount()
    {
        $User = new Users();
        return $User->getSubmissionCount();
        // return 1;
    }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
