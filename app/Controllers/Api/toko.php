<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Controllers\MasterKuisioner;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\MitraPameran;
use App\Models\MasterKuisionerModule;


class toko extends BaseController
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
    public function simpantoko($id = null)
    {
        $response = initResponse();
        helper("cookie");
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $nama_mitra = $this->request->getVar('nama_mitra');
                $nama_toko = $this->request->getVar('nama_toko');
                $Device_checker = $this->request->getVar('Device_checker');
                $no_telp = $this->request->getVar('no_telp');
                $in = array(
                    'nama_mitra' => $nama_mitra,
                    'nama_toko' => $nama_toko,
                    'Device_checker' => $Device_checker,
                    'no_telp' => $no_telp
                );
                $session = \Config\Services::session();
                $session = session();
                $session->set('identitastoko', $in);
                $userData = $_SESSION;
                return $this->response->setJSON(['success' => true, 'message' => 'Create Data Identitas Toko Successfully', 'data' => $in]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }
        helper('log');
        writeLog(
            "api",
            "\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
