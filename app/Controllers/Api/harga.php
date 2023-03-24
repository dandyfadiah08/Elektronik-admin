<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Controllers\MasterKuisioner;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\MitraPameran;
use App\Models\MasterKuisionerModule;


class harga extends BaseController
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
    public function getharga($id_pameran = null)
    {
        $response = initResponse();
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $MitraPameran = new MasterPameranModule();
                $id_pameran = $this->request->getVar('id_pameran');
                $product = $MitraPameran->gethargaphonenew($id_pameran);
                return $this->response->setJSON(['success' => true, 'message' => 'Data Kuisioner', 'data' => $product]);
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
    public function simpanmodel($id = null)
    {
        $response = initResponse();
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $id = $this->request->getVar('id');
                $kode = $this->request->getVar('kode');
                $brand = $this->request->getVar('brand');
                $model = $this->request->getVar('model');
                $harga = $this->request->getVar('harga');
                $in = array(
                    'id' => $id,
                    'kode' => $kode,
                    'brand' => $brand,
                    'model' => $model,
                    'harga' => $harga
                );
                $session = \Config\Services::session();
                $session = session();
                $session->set('modeproduct', $in);
                $userData = $_SESSION;
                return $this->response->setJSON(['success' => true, 'message' => 'Create Data product harga Successfully', 'data' => $in]);
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
