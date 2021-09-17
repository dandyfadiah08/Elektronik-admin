<?php

namespace App\Libraries;

use App\Models\DeviceChecks;
use App\Models\UserPayouts;

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
        // return $DeviceCheck->getOnAppointmentCount();
        return 1;
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

}
