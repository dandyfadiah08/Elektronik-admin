<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\MasterPameranModule;
use App\Models\TradeinModule;

class customer extends BaseController
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
        helper("cookie");
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $session = session();
                $userData = $_SESSION;
                $tradein = new TradeinModule();
                $koderegister = $this->request->getVar('kode_register');
                $id_user = $this->request->getVar('id_user');
                $nama = $this->request->getVar('nama');
                $no_hp = $this->request->getVar('no_hp');
                $email = $this->request->getVar('email');
                $motherboard = $this->request->getVar('motherboard');
                $asuransi = $this->request->getVar('asuransi');
                $hapus_data = $this->request->getVar('hapus_data');
                $datacustomer = array(
                    'kode_register' => $koderegister,
                    'id_user' => $id_user,
                    'nama' => $nama,
                    'no_hp' => $no_hp,
                    'email' => $email,
                    'motherboard' => $motherboard,
                    'asuransi' => $asuransi,
                    'hapus_data' => $hapus_data,
                );
                $benefitmotherboard = '';
                $benefitsuransi = '';
                $benefithapus_data = '';
                if ($motherboard != 0) {
                    $benefitmotherboard = 'Motherboard';
                }
                if ($asuransi != 0) {
                    $benefitsuransi = 'Asuransi';
                }
                if ($hapus_data != 0) {
                    $benefithapus_data = 'Hapus Data';
                }
                $Datatukar = $tradein->detailtukar($userData["kode_register"]);
                $savecustomer = $tradein->savecustomer($datacustomer);
                $viewData = array(
                    'kode_tradein' => 'E-' . $Datatukar[0]['kode_tradein'],
                    'harga_total' => $Datatukar[0]['harga_total'],
                    'benefit' => array(
                        'motherboard' => $benefitmotherboard,
                        'asuransi' => $benefitsuransi,
                        'hapus_data' => $benefithapus_data,
                    ),
                );
                return $this->response->setJSON(['success' => true, 'message' => 'Data Custumer save successfully', 'data' => $viewData]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }

        helper('log');
        writeLog(
            "api",
            "customer\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
