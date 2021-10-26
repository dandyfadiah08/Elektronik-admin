<<<<<<< HEAD
<?php

namespace App\Libraries;

use App\Models\Logs;

class Log
{
    protected $Log;
    public function __construct() {
        $this->Log = new Logs();
        $this->Log->setTable('logs_'.date('Y'));
    }

    /*
    @param $username string
    @param $category string
    @param $log string
    */
    function in($username, $category, $log, $admin_id = null, $user_id = null, $check_id = null)
    {
        $this->Log->insert([
            'user'      => $username,
            'category'  => $category,
            'admin_id'  => $admin_id,
            'user_id'   => $user_id,
            'check_id'  => $check_id,
            'log'       => $log,
            'created_at'    => date("Y-m-d H:i:s"),
        ]);
    }

}
=======
<?php

namespace App\Libraries;

use App\Models\Logs;

class Log
{
    protected $Log;
    public function __construct() {
        $this->Log = new Logs();
        $this->Log->setTable('logs_'.date('Y'));
    }

    /*
    @param $username string
    @param $category string
    @param $log string
    */
    function in($username, $category, $log, $admin_id = null, $user_id = null, $check_id = null)
    {
        $this->Log->insert([
            'user'      => $username,
            'category'  => $category,
            'admin_id'  => $admin_id,
            'user_id'   => $user_id,
            'check_id'  => $check_id,
            'log'       => $log,
            'created_at'    => date("Y-m-d H:i:s"),
        ]);
    }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
