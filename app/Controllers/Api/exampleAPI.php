<?php

namespace App\Controllers\Api;

class exampleAPI extends BaseController
{
    public function index()
    {
        $client = \Config\Services::curlrequest();
        $token = "";
        $url = "http://localhost/ci4resapi/public/pegawai";
        $response = $client->request('GET', $url);
        print_r($response);
    }
}
