<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\TradeinModule;
use App\Models\MitraPameran;


class Summary_tradein extends BaseController
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
                $session = session();
                $userData = $_SESSION;

                $pameran =  new MasterPameranModule();
                $tradein = new TradeinModule();
                $data = $pameran->kodetradein();
                $subsidi = $pameran->Datapameran($userData["id_pameran"]);
                $datatradein = array(
                    'kode_register' => $userData["kode_register"],
                    'id_mitra' => $userData["id_mitra"],
                    'id_pameran' => $userData["id_pameran"],
                    'id_user' => $userData["id_toko"],
                    'kode_tradein' => $data,
                    'subsidi' => $subsidi,
                    'harga_total' => $subsidi + $userData['harga'],
                    'flag' => 1,
                    'user_id' => $userData["user_id"],
                    'user_type' => $userData["user_type"]
                );
                $datakuisioner = $pameran->kodekuis();
                $datakuisionernew = array(
                    'kode_register' => $userData["kode_register"],
                    'kode_kuis' => $datakuisioner,
                    'id_user' => $userData["id_toko"],
                    'id_pameran' => $userData["id_pameran"],
                    'nama_te' => $userData["nama_te"],
                    'alamat' => $userData["alamat_te"],
                    'kode_produk' => $userData["kode_produk"],
                    'kategori' => $userData["kategori"],
                    'merk' => $userData["merk"],
                    'spec' => $userData["spec"],
                    'size' => $userData["size"],
                    'tahun' => $userData["tahun"],
                    'sn' => $userData["sn"],
                    'harga' => $userData["harga"],
                    'list_kuisioner' => $userData["id_mitra"],
                    'harga_akhir' => $subsidi + $userData['harga'],
                    'status' => 1,
                    'id_mitra' => $userData['identitastoko']['nama_mitra'],
                    'kode_model' => $userData['modeproduct']['kode'],
                    'device_checker' => $userData['identitastoko']['Device_checker'],
                    'no_telp' => $userData['identitastoko']['no_telp'],
                );
                $simpankuisionernew = $tradein->savekuisioner_new($datakuisionernew);
                $datalist = [];
                foreach ($userData as $key => $value) {
                    $coba = explode(" ", $key);
                    if ($coba[0] == 'list_kuisioner') {
                        $datakuisioner = array(
                            'id_mitra' => $userData["id_mitra"],
                            'id_mkuisioner' => $value['id_mkuisioner'],
                            'id_listkuisioner' => $value['id_liskuisioner'],
                            'list' => $value['list'],
                        );
                        $simpan = $tradein->savetchildkategori($datakuisioner);
                    }
                }
                $simpan = $tradein->savetradein($datatradein);
                $dataregister = array(
                    'kode_register' => $userData["kode_register"],
                    'id_user' => $userData["id_toko"],
                    'username' => $userData["username"],
                    'user_id' => $userData["user_id"],
                );
                $simpanregis = $tradein->saveregistertradein($dataregister);
                return $this->response->setJSON(['success' => true, 'message' => 'Data Mitra', 'data' => $datatradein]);
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
