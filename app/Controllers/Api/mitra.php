<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\MitraPameran;


class mitra extends BaseController
{
    use ResponseTrait;
    protected $request;
    public function __construct()
    {
        $this->db = \Config\Database::connect();

        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
    }
    public function index($id_mitra = null)
    {
        $response = initResponse();
        $MitraPameran = new MitraPameran();
        helper("cookie");
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $id_mitra = $this->request->getVar('id_mitra');
                $mitra = $MitraPameran->cekmitra($id_mitra);
                $data = [];
                foreach ($mitra as $key => $value) {
                    $namapameran = new MasterPameranModule();
                    $datapameran = $namapameran->namapameranmitra($value['id_pameran']);
                    foreach ($datapameran as $key => $valuedata) {
                        $countdata = count($valuedata);
                        array_push($data, $valuedata);
                    }
                }
                return $this->response->setJSON(['success' => true, 'message' => 'Data Mitra', 'data' => $data]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }

        helper('log');
        writeLog(
            "api",
            "mitra\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
