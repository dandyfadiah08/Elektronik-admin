<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Controllers\MasterKuisioner;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\MitraPameran;
use App\Models\MasterKuisionerModule;
use PhpParser\Node\Expr\CallLike;

class kuisioner extends BaseController
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
    public function list($id_kategori = null)
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
                $arraydatalistkuisikner = [];
                $id_kategori = $this->request->getVar('id_kategori');
                $Listkuisioner = new MasterKuisionerModule();
                $Kuisioner = $Listkuisioner->datamasterkuisioner($id_kategori);
                foreach ($Kuisioner as $key => $value) {
                    $lisKuisioner = $Listkuisioner->datalistkuisioner($value->id_mkuisioner);
                    array_push($arraydatalistkuisikner, ['id' => $value->id_mkuisioner, 'levelnumber' => $value->number, 'kuisioner' => $value->kuisioner, 'listkuisioner' => $lisKuisioner]);
                }
                return $this->response->setJSON(['success' => true, 'message' => 'Data Kuisioner', 'data' => $arraydatalistkuisikner]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }
        helper('log');
        writeLog(
            "api",
            "kuisioner\n"
                . json_encode($this->request->getGet()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function simpan($id_mkuisioner = null, $id_liskuisioner = null)
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

                $id_mkuisioner = $this->request->getVar('id_mkuisioner');
                $id_liskuisioner = $this->request->getVar('id_liskuisioner');
                $list = $this->request->getVar('list');

                $listkuisioner = array(
                    'id_mkuisioner' => $id_mkuisioner,
                    'id_liskuisioner' => $id_liskuisioner,
                    'list' => $list,
                );
                $session = \Config\Services::session();
                $session = session();
                $session->set('list_kuisioner' . ' ' . $id_liskuisioner, $listkuisioner);
                $userData = $_SESSION;

                return $this->response->setJSON(['success' => true, 'message' => 'Data Kuisioner', 'data' => 'Data Telah Tersimpan di Season']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }
        helper('log');
        writeLog(
            "api",
            "savekuisioner\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function simpankuisioner($id_mkuisioner = null, $id_liskuisioner = null)
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

                $session = \Config\Services::session();
                $listkuionermodel = new MasterKuisionerModule();

                $datalist = [];
                $session = session();
                $userData = $_SESSION;
                foreach ($userData as $key => $value) {
                    $coba = explode(" ", $key);
                    if ($coba[0] == 'list_kuisioner') {
                        array_push($datalist, $value);
                    }
                }
                $datagrading = [];
                foreach ($datalist as $key => $value) {
                    $id_list = $value["id_liskuisioner"];
                    $datakusioner   = $listkuionermodel->showmlistkuisionergradinguser($id_list);
                    foreach ($datakusioner as $key => $tamp) {
                        # code...
                        array_push($datagrading, $tamp['id_jgrading']);
                    }
                }
                $count_values = array_count_values($datagrading);
                $dataidgrading = [];
                foreach ($count_values as $key => $value) {
                    array_push($dataidgrading, $key);
                }
                $id_product = $userData["id_product"];
                $dataharga = [];
                foreach ($dataidgrading as $key => $value) {
                    $datahargauser   = $listkuionermodel->datahargakuisioner($id_product, $value);
                    array_push($dataharga, $datahargauser[0]["harga"]);
                }
                $hargaterkecil = min($dataharga);
                $session->set('harga', $hargaterkecil);
                return $this->response->setJSON(['success' => true, 'message' => 'Data Kuisioner', 'data' => 'Data harga grading telah terupdate']);
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
