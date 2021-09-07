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
    function in($username, $category, $log)
    {
        $this->Log->insert([
            'user'      => $username,
            'category'  => $category,
            'log'       => $log,
            'created_at'    => date("Y-m-d H:i:s"),
        ]);
    }

}
