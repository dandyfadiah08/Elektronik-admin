<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;


class Qiscus extends BaseController
{

    use ResponseTrait;

    protected $request;


    public function __construct()
    {
        helper('rest_api');
        helper('log');
    }

    public function webhook()
    {
        $response = initResponse('OK', true);
        writeLog("qiscus", 
            json_encode($this->request->getJSON())
        );

        return $this->respond($response, 200);
    }

}
