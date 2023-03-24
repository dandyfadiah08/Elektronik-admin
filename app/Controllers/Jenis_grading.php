<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\JenisGradingModule;
use App\Models\MasterKuisionerModule;
use App\Models\MasterKategoriModule;
use App\Models\LogModule;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use mysqli;

class Jenis_grading extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $kategoriModel = new MasterKategoriModule;
        $datakategori   = $kategoriModel->getkategori();
        $MasterAdmin = new MasterAdminModule;
        $this->data += [
            'page' => (object)[
                'key' => '2-JenisGrading',
                'title' => 'Data Jenis Garding',
                'subtitle' => 'Data Jenis Grading ',
                'data' => $datakategori,
                'navbar' => '',
            ],
        ];
        $nama_role = 'grading';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'jenis_grading/index',
                $this->data
            );
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "a.nama_grading",

        );
        // fields to search with
        $fields_search = array(
            "a.nama_grading",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'jenis_grading';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_jgrading,a.kode_grading,a.nama_grading,a.date_save,a.deleted_at';
        $reviewed = $req->getVar('reviewed') ?? 0;
        $is_reviewed = $reviewed == 1;

        $where = ['a.deleted_at' => null];

        $this->builder
            ->select($select_fields)
            ->where($where);

        // bulding order query
        $order = $req->getVar('order');
        $length = isset($_REQUEST['length']) ? (int)$req->getVar('length') : 10;
        $start = isset($_REQUEST['start']) ? (int)$req->getVar('start') : 0;
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
        }
        if ($dir != "asc" && $dir != "desc") $dir = "asc";
        if (isset($fields_order[$col])) $this->builder->orderBy($fields_order[$col],  $dir); // add order query to builder

        // bulding search query
        if (!empty($req->getVar('search')['value'])) {
            $search = $req->getVar('search')['value'];
            $keywords = explode(" ", $search);
            $this->builder->groupStart();
            foreach ($keywords as $keyword) {
                $search_array = [];
                foreach ($fields_search as $key) $search_array[$key] = $keyword;
                // add search query to builder
                $this->builder
                    ->orGroupStart()
                    ->orLike($search_array)
                    ->groupEnd();
            }
            $this->builder->groupEnd();
        }
        $totalData = count($this->builder->get(0, 0, false)->getResult()); // 3rd parameter is false to NOT reset query

        $this->builder->limit($length, $start); // add limit for pagination
        $dataResult = [];
        $dataResult = $this->builder->get()->getResult();
        // die($this->db->getLastQuery());

        $data = [];
        if (count($dataResult) > 0) {
            $i = $start;
            helper('number');
            helper('html');
            helper('format');
            $url = (object)[
                'detail' => base_url() . '/tradein/cekTradein/',
            ];
            // looping through data result
            foreach ($dataResult as $row) {
                $i++;
                $aksi = '
                <button href="#" data-target="#listModalGrading" data-toggle="modal" class="btn btn-primary btn-sm mb-1 listModalGrading" data-nama_grading="' . $row->nama_grading . '" data-id_jgrading="' . $row->id_jgrading . '" ">
                <i class="fas fa-lock"></i> Syarat
                </button>
                <button href="#" class="btn btn-warning btn-sm mb-1 editModalGrading"  data-toggle="modal" data-target="#editModalGrading"
                    data-id_jgrading="' . $row->id_jgrading . '"
                    data-nama_grading="' . $row->nama_grading . '" 
                    data-kode_grading="' . $row->kode_grading . '" 
                    "
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button href="#" data-target="#tombolHapus" class="btn btn-danger btn-sm mb-1 tombolHapus" data-nama_grading="' . $row->nama_grading . '" data-id_jgrading="' . $row->id_jgrading . '" ">
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';
                $r = [];
                $r[] = $i;
                $r[] = $row->kode_grading;
                $r[] = $row->nama_grading;
                $r[] = $aksi;

                $data[] = $r;
            }
            // $tampquery   = $TestModel->getAllDatamitra($row->id_mitra);

            // print_r($data);
        }

        $json_data = array(
            "draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        return  $this->respond($json_data);
    }
    function tambah_data()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-JenisGrading',
                'title' => 'Jenis Grading',
                'subtitle' => 'Jenis Grading',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $kode_grading = $output['params']['data']['kodegrading'];
        $nama_grading = $output['params']['data']['namagrading'];
        $editing = $this->data['admin']->username;


        $in = array(
            'kode_grading' => $kode_grading,
            'nama_grading' => $nama_grading,

        );
        $out = array(
            'nama_grading' => $nama_grading,
            'id' => 43,
            'editing' => $editing,
        );
        $this->db->transStart();
        $this->JGrading->savegrading($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Loggrading($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function editgrading()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-JenisGrading',
                'title' => 'Jenis Grading',
                'subtitle' => 'Jenis Grading',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_jgrading = $this->request->getPost('id');
        $kode_grading = $this->request->getPost('kode_grading');
        $nama_grading = $this->request->getPost('nama_grading');

        $data_grading = [];
        $data_grading['id_jgrading'] = $id_jgrading;
        $data_grading['kode_grading'] = $kode_grading;
        $data_grading['nama_grading'] = $nama_grading;

        // var_dump($data_grading);
        // die;
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->JGrading->update($id_jgrading, $data_grading);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_grading' => $nama_grading,
                'id' => 44,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editgrading($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function Hapusgrading()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-JenisGrading',
                'title' => 'Jenis Grading',
                'subtitle' => 'Jenis Grading',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_jgrading = $this->request->getPost('id');
        $nama_grading = $this->request->getPost('nama_grading');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->JGrading->deleteGrading($id_jgrading);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_grading' => $nama_grading,
                'id' => 45,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapusgrading($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function datakuisioner()
    {
        $response = initResponse();
        $datavalue = [];
        $datavaluemkuisioner2 = [];
        $arrayid_mkuisioner = [];
        $array2id_mkuisioner = [];
        $arraysameid_mkuisioner = [];
        $id_jgrading = $this->request->getPost('id');
        $datakuisioner = $this->MasterKuisioner->showmasterkuisioner();
        $datalistkuisioner = $this->MasterKuisioner->showmlistkuisioner();
        $datalistkuisionerAll = $this->MasterKuisioner->showmlistkuisionergradingAll();
        $lengthdata = 0;
        $lengthdataAll = count($datalistkuisionerAll);
        $lengthdataAll2 = count($datalistkuisionerAll);
        foreach ($datalistkuisioner as $key => $datavaluekuisioner) {
            array_push($arrayid_mkuisioner, $datavaluekuisioner['id_mkuisioner']);
        }
        $jumlahdata =  array_count_values($arrayid_mkuisioner);
        foreach ($datalistkuisionerAll as $key => $datavaluekuisioner2) {
            array_push($array2id_mkuisioner, $datavaluekuisioner2['id_mkuisioner']);
        }
        $jumlahdata2 =  array_count_values($array2id_mkuisioner);
        foreach ($jumlahdata as $key3 => $valueid_kuisioner) {
            foreach ($jumlahdata2 as $key4 => $value2id_kuisioner) {
                if ($key3 == $key4) {
                    if ($valueid_kuisioner == $value2id_kuisioner) {
                        array_push($arraysameid_mkuisioner, $key4);
                    }
                }
            }
        }
        $lengthmkuisioner = count($arraysameid_mkuisioner);
        foreach ($datalistkuisioner as $key => $value) {
            $validasi = false;
            if ($lengthdataAll > 0) {
                for ($i = 0; $i < $lengthdataAll; $i++) {
                    if ($value['id_listkuisioner'] == $datalistkuisionerAll[$i]['id_listkuisioner'] && $id_jgrading != $datalistkuisionerAll[$i]['id_jgrading']) {
                        $validasi = true;
                        continue;
                    }
                }
            }
            if ($validasi == false) {
                array_push($datavalue, $value);
            }
        }
        foreach ($datakuisioner as $key => $valuedata) {
            $validasi2 = false;
            for ($d = 0; $d < $lengthmkuisioner; $d++) {
                if ($valuedata['id_mkuisioner'] == $arraysameid_mkuisioner[$d]) {
                    $validasi2 = true;
                    continue;
                }
            }
            if ($validasi2 == false) {
                array_push($datavaluemkuisioner2, $valuedata);
            }
        }
        $arrayjenisgrading = [];
        foreach ($arraysameid_mkuisioner as $key => $valuekuis2) {
            foreach ($datalistkuisionerAll as $key => $valueall) {
                if ($valuekuis2 == $valueall['id_mkuisioner']) {
                    array_push($arrayjenisgrading, $valueall['id_jgrading']);
                }
            }
        }
        $arrayjenisgrading = array_unique($arrayjenisgrading);
        $datalistkuisionergrading = $this->MasterKuisioner->showmlistkuisionergrading($id_jgrading);
        $json_data = array(
            "datakuisioner" =>  $datakuisioner,
            "datalistkuisioner" => $datavalue,
            "datachecked" => $datalistkuisionergrading,
            "datalisgreding" => $datalistkuisionerAll,
            "datalistsum" => $arraysameid_mkuisioner,
            "datakuisioner2" => $datavaluemkuisioner2,
            'duplicatedgrading' => $arrayjenisgrading,
        );
        return  $this->respond($json_data);
    }
    function kuisionergrading()
    {
        $response = initResponse();
        $arrayid_mkuisioner = [];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_lgrading = $output['params']['data']['id_lgrading'];
        $valuelist = $output['params']['data']['valuelist'];
        $deletedlistkuisioner = $this->MasterKuisioner->deletedlistkuisioner($id_lgrading);
        foreach ($valuelist as $key => $valuedata) {
            $in = array(
                "id_jgrading" => $id_lgrading,
                "id_mkuisioner" => $valuedata['id_mkuisioner'],
                "id_listkuisioner" => $valuedata['value'],
            );
            $savelistkuisioner = $this->MasterKuisioner->savegradinglistkuisioner($in);
        }
        $json_data = array(
            "id_Jgrading" =>  $id_lgrading,
            "datalistkuisioner" => $valuelist

        );
        return  $this->respond($json_data);
    }
}
