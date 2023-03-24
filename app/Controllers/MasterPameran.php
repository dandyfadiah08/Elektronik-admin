<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\MasterPameranModule;
use App\Models\LogModule;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Firebase\JWT\JWT;
use mysqli;

class MasterPameran extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    //Data Pameran
    function load_data()
    {
        $url = (object)[
            'detail' => base_url() . '/MasterPameran/DataNewPhone/',
        ];
        $url2 = (object)[
            'detail' => base_url() . '/MasterPameran/Harga/',
        ];
        $TestModel = new TradeinModule;
        $PameranModel = new MasterPameranModule;
        $fields_order = array(
            null,
            "a.nama_pameran",
            "a.id_mitra",
            "a.nama_pameran",
            "a.nama_pameran",
            "a.subsidi",
            "a.bulan",


        );
        // fields to search with
        $fields_search = array(
            "a.nama_pameran",
            "a.id_mitra",
            "a.nama_pameran",
            "a.nama_pameran",
            "a.subsidi",
            "a.bulan",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'data_pameran';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_pameran,a.id_mitra,a.nama_pameran,a.jenis_subsidi,a.subsidi,a.bulan,a.updated,a.voucher,a.deleted_at';
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
            // looping through data result
            foreach ($dataResult as $row) {
                $i++;

                $aksi = '
                <button href="#" class="btn btn-primary btn-sm mb-1 editModalPameran"  data-toggle="modal" data-target="#editModalPameran"
                data-id_pameran="' . $row->id_pameran . '"
                data-nama_pameran="' . $row->nama_pameran . '" 
                data-jenis_subsidi="' . $row->jenis_subsidi . '"
                 data-subsidi="' . $row->subsidi . '"
                 data-vocher="' . $row->voucher . '"
                 data-bulan="' . $row->bulan . '"
                 data-jenis_subsidi="' . $row->jenis_subsidi . '"
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <form method="" action="' . $url2->detail . $row->id_pameran . '">
                <button href="#" class="btn btn btn-success btn-sm mb-1 editModalAdmin"  data-toggle="modal" data-target="#editModalAdmin"
                ?????x
                >
                <i class="fas fa-dollar-sign"></i> Harga
                </button>
                </form>
                <form method="" action="' . $url->detail . $row->id_pameran . '">
                <button href="#" class="btn btn-info btn-sm mb-1 editModalAdmin"  data-toggle="modal" data-target="#editModalAdmin"
                ?????x
                >
                    <i class="fas fa-mobile"></i> Data New Phone
                </button>
                </form>
                <button href="#" data-target="#tombolHapus" data-nama_pameran="' . $row->nama_pameran . '" data-id_pameran="' . $row->id_pameran . '" class="btn btn-danger btn-sm mb-1 tombolHapus">
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';

                $subsidi = '';
                if ($row->jenis_subsidi == 0) {
                    $subsidi =  'Subsidi Pameran<br>' . $row->subsidi;
                } else {
                    $subsidi = 'Subsidi Unit';
                }
                $bulan = '';
                $convertDate = date("m", strtotime($row->bulan));
                if ($convertDate == 1) {
                    $bulan = 'Januari';
                } elseif ($convertDate == 2) {
                    $bulan = 'Februari';
                } elseif ($convertDate == 3) {
                    $bulan = 'Maret';
                } elseif ($convertDate == 4) {
                    $bulan = 'April';
                } elseif ($convertDate == 5) {
                    $bulan = 'Mei';
                } elseif ($convertDate == 6) {
                    $bulan = 'Juni';
                } elseif ($convertDate == 7) {
                    $bulan = 'Juli';
                } elseif ($convertDate == 8) {
                    $bulan = 'Agustus';
                } elseif ($convertDate == 9) {
                    $bulan = 'September';
                } elseif ($convertDate == 10) {
                    $bulan = 'Oktober';
                } elseif ($convertDate == 11) {
                    $bulan = 'November';
                } elseif ($convertDate == 12) {
                    $bulan = 'Desember';
                }
                $harga = '';
                $cek_hp_rusak = $PameranModel->cekpameran($row->id_pameran);
                $mitra = $TestModel->getDatamitrapemeran($row->id_pameran);
                if ($cek_hp_rusak == 1) {
                    $harga = '<button href="#" style="color:black" class="btn btn btn-warning btn-sm mb-1 editModalAdmin"  data-toggle="modal" data-target="#editModalAdmin"

                    >
                        <i class="fas fa-dollar-sign"></i> Harga HP Rusak
                    </button>';
                }
                $r = [];
                $r[] = $i;
                $r[] = $row->nama_pameran;
                $r[] = $mitra['nama_mitra'];
                $r[] = $subsidi;
                $r[] = $bulan;
                $r[] = $aksi;

                $data[] = $r;
            }
        }

        $json_data = array(
            "draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        return  $this->respond($json_data);
    }
    function load_data_datanew()
    {
        $url = (object)[
            'detail' => base_url() . '/MasterPameran/DataNewPhone/',
        ];
        $TestModel = new TradeinModule;
        $PameranModel = new MasterPameranModule;
        $fields_order = array(
            null,
            "a.kode",
            "a.brand",
            "a.model",
            "a.black",
            "a.white",
            "a.green",
            "a.gold",
            "a.silver",
            "a.gray",
            "a.stok",
            "a.harga",


        );
        // fields to search with
        $fields_search = array(
            "a.kode",
            "a.brand",
            "a.model",
            "a.black",
            "a.white",
            "a.green",
            "a.gold",
            "a.silver",
            "a.gray",
            "a.stok",
            "a.harga",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $idpameran = $req->getVar('id_pameran') ?? '';
        $this->db = \Config\Database::connect();
        $this->table_name = 'data_product_tambah';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->where('a.id_pameran', $idpameran)
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id,a.id_pameran,a.kode,a.brand,a.model,a.black,a.white,a.green,a.gold,a.silver,a.gray,a.stok,a.harga,a.deleted_at';
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
            // looping through data result
            foreach ($dataResult as $row) {
                $i++;
                $arraypameran = $this->Pameran->namapameran($row->id_pameran);
                $nama_pameran = $arraypameran[0]['nama_pameran'];
                $aksi = '
                <button href="#" class="btn btn-warning btn-sm mb-1 editModalNewPhone"  data-toggle="modal" data-target="#editModalNewPhone"
                data-id="' . $row->id . '"
                data-id_pameran="' . $row->id_pameran . '" 
                data-kode="' . $row->kode . '" 
                data-brand="' . $row->brand . '" 
                data-model="' . $row->model . '"
                data-black="' . $row->black . '"
                data-white="' . $row->white . '"
                data-green="' . $row->green . '"
                data-gold="' . $row->gold . '"
                data-silver="' . $row->silver . '"
                data-gray="' . $row->gray . '"
                data-stok="' . $row->stok . '"
                data-harga="' . $row->harga . '"
                "
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button href="#" data-target="#tombolHapus" data-brand="' . $row->brand . '" data-nama_pameran= "' . $nama_pameran . '" data-id="' . $row->id . '" class="btn btn-danger btn-sm mb-1 tombolHapus" >
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';
                $r = [];
                $r[] = $i;
                $r[] = $row->kode;
                $r[] = $row->brand;
                $r[] = $row->model;
                $r[] = $row->black;
                $r[] = $row->white;
                $r[] = $row->green;
                $r[] = $row->gold;
                $r[] = $row->silver;
                $r[] = $row->gray;
                $r[] = $row->stok;
                $r[] = $row->harga;
                $r[] = $aksi;

                $data[] = $r;
            }
        }

        $json_data = array(
            "draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        return  $this->respond($json_data);
    }
    public function index()
    {
        $MasterAdmin = new MasterAdminModule;
        $TestTradein = new TradeinModule;
        $mitra = $TestTradein->AllDatamitra();
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'data' => $mitra,
                'navbar' => '',
            ],
        ];
        $nama_role = 'pameran';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_pameran/index',
                $this->data
            );
        }
    }
    function tambah_data()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $nama_pameran = $output['params']['data']['nama_pameran'];
        $id_pameran = $output['params']['data']['id_pameran'];
        $jenis_subsidi = $output['params']['data']['jenis_subsidi'];
        $subsidi = $output['params']['data']['subsidi'];
        $voucher = $output['params']['data']['voucher'];
        $datamitra = $output['params']['data']['mitra'];
        $bulan = $output['params']['data']['bulan'];
        $editing = $this->data['admin']->username;
        $in = array(
            'nama_pameran' => $nama_pameran,
            'id_pameran' => $id_pameran,
            'jenis_subsidi' => $jenis_subsidi,
            'subsidi' => $subsidi,
            'voucher' => $voucher,
            'datamitra' => $datamitra,
            'bulan' => $bulan,
            'editing' => $editing,
            'status' => 'active'
        );
        $out = array(
            'nama_pameran' => $nama_pameran,
            'id' => 22,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->Pameran->saveadmin($in);
        $data_mitra = $this->Pameran->dataend();
        $data_mitra = $this->Pameran->mitrapameran($in, $data_mitra[0]['id_pameran']);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logtambahpameran($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function edit_data()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $nama_pameran = $output['params']['data']['nama_pameran'];
        $id_pameran = $output['params']['data']['id_pameran'];
        $jenis_subsidi = $output['params']['data']['jenis_subsidi'];
        $subsidi = $output['params']['data']['subsidi'];
        $voucher = $output['params']['data']['voucher'];
        $datamitra = $output['params']['data']['mitra'];
        $bulan = $output['params']['data']['bulan'];
        $editing = $this->data['admin']->username;
        $in = array(
            'nama_pameran' => $nama_pameran,
            'id_pameran' => $id_pameran,
            'jenis_subsidi' => $jenis_subsidi,
            'subsidi' => $subsidi,
            'voucher' => $voucher,
            'datamitra' => $datamitra,
            'bulan' => $bulan,
            'editing' => $editing,
            'status' => 'active'
        );
        $out = array(
            'nama_pameran' => $nama_pameran,
            'id' => 24,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->Pameran->editpameran($in);
        $data_mitra = $this->Pameran->deletemitra($id_pameran);
        $data_mitra = $this->Pameran->mitrapameran($in, $id_pameran);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logeditpameran($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    //Data New Phone
    function edit_dataNewPhone()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];

        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id = $output['params']['data']['id'];
        $id_pameran = $output['params']['data']['id_pameran'];
        $brand = $output['params']['data']['brand'];
        $kode = $output['params']['data']['kode'];
        $model = $output['params']['data']['model'];
        $black = $output['params']['data']['black'];
        $white = $output['params']['data']['white'];
        $green = $output['params']['data']['green'];
        $gold = $output['params']['data']['gold'];
        $silver = $output['params']['data']['silver'];
        $gray = $output['params']['data']['gray'];
        $stok = $output['params']['data']['stok'];
        $harga = $output['params']['data']['harga'];
        $editing = $this->data['admin']->username;

        $in = array(
            'id' => $id,
            'id_pameran' => $id_pameran,
            'brand' => $brand,
            'kode' => $kode,
            'model' => $model,
            'black' => $black,
            'white' => $white,
            'green' => $green,
            'gold' => $gold,
            'silver' => $silver,
            'gray' => $gray,
            'stok' => $stok,
            'harga' => $harga
        );
        $arraypameran = $this->Pameran->namapameran($id_pameran);
        $nama_pameran = $arraypameran[0]['nama_pameran'];
        $out = array(
            'nama_pameran' => $nama_pameran,
            'brand' => $brand,
            'id' => 29,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->Pameran->editnewphone($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logeditnewphones($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function HapusPameran()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_pameran = $this->request->getPost('id_pameran');
        $nama_pameran = $this->request->getPost('nama_pameran');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Pameran->deletePameran($id_pameran);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_pameran' => $nama_pameran,
                'id' => 23,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapuspameran($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function DataNewPhone($key)
    {
        $MasterAdmin = new MasterAdminModule;
        $TestTradein = new TradeinModule;
        $mitra = $TestTradein->AllDatamitra();

        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'data' => $mitra,
                'idpameran' => $key,
                'navbar' => '',
            ],
        ];
        $nama_role = 'pameran';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_pameran/datanewphone',
                $this->data
            );
        }
    }

    function tambah_data_newphone()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_pameran = $output['params']['data']['id_pameran'];
        $brand = $output['params']['data']['brand'];
        $kode = $output['params']['data']['kode'];
        $model = $output['params']['data']['model'];
        $black = $output['params']['data']['black'];
        $white = $output['params']['data']['white'];
        $green = $output['params']['data']['green'];
        $gold = $output['params']['data']['gold'];
        $silver = $output['params']['data']['silver'];
        $gray = $output['params']['data']['gray'];
        $stok = $output['params']['data']['stok'];
        $harga = $output['params']['data']['harga'];
        $editing = $this->data['admin']->username;
        $in = array(
            'id_pameran' => $id_pameran,
            'brand' => $brand,
            'kode' => $kode,
            'model' => $model,
            'black' => $black,
            'white' => $white,
            'green' => $green,
            'gold' => $gold,
            'silver' => $silver,
            'gray' => $gray,
            'stok' => $stok,
            'harga' => $harga

        );
        $arraypameran = $this->Pameran->namapameran($id_pameran);
        $nama_pameran = $arraypameran[0]['nama_pameran'];
        $out = array(
            'nama_pameran' => $nama_pameran,
            'brand' => $brand,
            'id' => 28,
            'editing' => $editing,
            'status' => 'active'
        );

        $this->db->transStart();
        $this->Pameran->savenewphone($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->LogtambahNewphone($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function HapusdataNewPhone()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;

        $response = initResponse();
        $hasError = false;
        $id = $this->request->getPost('id');
        $brand = $this->request->getPost('brand');
        $nama_pameran = $this->request->getPost('nama_pameran');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Pameran->deletenewphone($id);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_pameran' => $nama_pameran,
                'brand' => $brand,
                'id' => 30,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapusnewphone($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }

    //Data Harga
    function harga($key)
    {
        $MasterAdmin = new MasterAdminModule;
        $TestTradein = new TradeinModule;
        $mitra = $TestTradein->AllDatamitra();
        $datagrading  = $this->Kategori->getkategori();
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'data' => $mitra,
                'kategori' =>  $datagrading,
                'idpameran' => $key,
                'navbar' => '',
            ],
        ];
        $nama_role = 'pameran';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_pameran/harga',
                $this->data
            );
        }
    }
    function load_data_harga()
    {
        $url = (object)[
            'detail' => base_url() . '/MasterPameran/DataNewPhone/',
        ];
        $TestModel = new TradeinModule;
        $PameranModel = new MasterPameranModule;
        $fields_order = array(
            null,
            "a.kode_produk",
            "a.kategori",
            "a.merk",
            "a.spec",
            "a.size",
            "a.tahun",
            "a.subsidi",
            "a.subsidi_mitra1",
            "a.subsidi_mitra2",
            "a.harga",


        );
        // fields to search with
        $fields_search = array(
            "a.kode_produk",
            "a.kategori",
            "a.merk",
            "a.spec",
            "a.size",
            "a.tahun",
            "a.subsidi",
            "a.subsidi_mitra1",
            "a.subsidi_mitra2",
            "a.harga",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $idpameran = $req->getVar('id_pameran') ?? '';
        $this->db = \Config\Database::connect();
        $this->table_name = 'data_product';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->where('a.id_pameran', $idpameran)
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_product,a.id_pameran,a.kode_produk,a.kategori,a.merk,a.spec,a.size,a.tahun,a.subsidi,a.subsidi_mitra1,a.subsidi_mitra2,a.harga,a.date_save,a.deleted_at';
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

            // looping through data result
            foreach ($dataResult as $row) {
                $i++;
                $arraypameran = $this->Pameran->namapameran($row->id_pameran);
                $nama_pameran = $arraypameran[0]['nama_pameran'];
                $aksi = '
                <button href="#" class="btn btn-warning btn-sm mb-1 editModalharga"  data-toggle="modal" data-target="#editModalharga"
                data-id="' . $row->id_product . '"
                data-id_pameran="' . $row->id_pameran . '" 
                data-kode_produk="' . $row->kode_produk . '" 
                data-kategori="' . $row->kategori . '" 
                data-merk="' . $row->merk . '"
                data-spec="' . $row->spec . '"
                data-size="' . $row->size . '"
                data-tahun="' . $row->tahun . '"
                data-subsidi="' . $row->subsidi . '"
                data-harga="' . $row->harga . '"
                "
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button href="#" data-target="#tombolHapus" data-kode_produk="' . $row->kode_produk . '" data-nama_pameran= "' . $nama_pameran . '" data-id_product="' . $row->id_product . '"  class="btn btn-danger btn-sm mb-1 tombolHapus" >
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';
                $view = '
                <small class="btn badge badge-info view" data-toggle="modal" data-target="#view" 
                data-kode_produk="' . $row->kode_produk . '" data-nama_pameran= "' . $nama_pameran . '" data-id_product="' . $row->id_product . '"
                "
                >
                <i class="fa fa-eye"></i> view
              </small>
                
                ';
                $r = [];
                $r[] = $i;
                $r[] = $row->kode_produk;
                $r[] = $row->kategori;
                $r[] = $row->merk;
                $r[] = $row->spec;
                $r[] = $row->size;
                $r[] = $row->tahun;
                $r[] = ' Rp. </span><span style="text-align:right">' . number_format($row->subsidi, 0, ",", ".");
                $r[] = ' Rp. </span><span style="text-align:right">' . number_format($row->subsidi_mitra1, 0, ",", ".");
                $r[] = ' Rp. </span><span style="text-align:right">' . number_format($row->subsidi_mitra2, 0, ",", ".");
                // $r[] = ' Rp. </span><span style="text-align:right">' . number_format($row->harga, 0, ",", ".");
                $r[] = $view;
                $r[] = $aksi;

                $data[] = $r;
            }
        }

        $json_data = array(
            "draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        return  $this->respond($json_data);
    }
    function tambah_data_harga()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_pameran = $output['params']['data']['id_pameran'];
        $kode_produk = $output['params']['data']['kode_produk'];
        $spec = $output['params']['data']['spec'];
        $kategori = $output['params']['data']['kategori'];
        $tahun = $output['params']['data']['tahun'];
        $size = $output['params']['data']['size'];
        $merk = $output['params']['data']['merk'];
        $subsidi = $output['params']['data']['subsidi'];
        $harga = $output['params']['data']['harga'];
        $editing = $this->data['admin']->username;

        $in = array(
            'id_pameran' => $id_pameran,
            'kode_produk' => $kode_produk,
            'spec' => $spec,
            'kategori' => $kategori,
            'tahun' => $tahun,
            'size' => $size,
            'merk' => $merk,
            'subsidi' => $subsidi,
            'harga' => $harga,
        );
        $arraypameran = $this->Pameran->namapameran($id_pameran);
        $nama_pameran = $arraypameran[0]['nama_pameran'];
        $out = array(
            'nama_pameran' => $nama_pameran,
            'kode_produk' => $kode_produk,
            'id' => 25,
            'editing' => $editing,
            'status' => 'active'
        );

        $this->db->transStart();
        $this->Pameran->saveharga($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $id_product = $this->Pameran->cekid_produk($kode_produk);
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logtambahharga($out);
            $response->data = $id_product;
            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function edit_dataHarga()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterPameran',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];

        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_product = $output['params']['data']['id_product'];
        $id_pameran = $output['params']['data']['id_pameran'];
        $kode_produk = $output['params']['data']['kode_produk'];
        $spec = $output['params']['data']['spec'];
        $kategori = $output['params']['data']['kategori'];
        $tahun = $output['params']['data']['tahun'];
        $size = $output['params']['data']['size'];
        $merk = $output['params']['data']['merk'];
        $subsidi = $output['params']['data']['subsidi'];
        $editing = $this->data['admin']->username;
        $in = array(
            'id_product' => $id_product,
            'id_pameran' => $id_pameran,
            'kode_produk' => $kode_produk,
            'spec' => $spec,
            'kategori' => $kategori,
            'tahun' => $tahun,
            'size' => $size,
            'merk' => $merk,
            'subsidi' => $subsidi,
        );
        $arraypameran = $this->Pameran->namapameran($id_pameran);
        $nama_pameran = $arraypameran[0]['nama_pameran'];
        $out = array(
            'nama_pameran' => $nama_pameran,
            'kode_produk' => $kode_produk,
            'id' => 27,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->Pameran->editharga($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logeditharga($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function HapusdataHargaperproduct()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_product = $this->request->getPost('id_product');
        $kode_produk = $this->request->getPost('kode_produk');
        $nama_pameran = $this->request->getPost('nama_pameran');
        $LogModel = new LogModule;
        $this->db->transStart();

        $this->Pameran->deleteharga($id_product);
        $this->Pameran->hapushargamaster($id_product);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_pameran' => $nama_pameran,
                'kode_produk' => $kode_produk,
                'id' => 26,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapusharga($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function HapusdataHargaall()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_pameran = $this->request->getPost('id_pameran');
        $LogModel = new LogModule;
        $this->db->transStart();
        $datain = $this->Pameran->detailproductharga($id_pameran);
        $datapameran = $this->Pameran->detailpameran($id_pameran);
        $namaPameran = $datapameran[0]['nama_pameran'];
        $this->Pameran->deletehargaall($id_pameran);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'datain' => $datain,
                'nama_pameran' => $namaPameran,
                'id' => 26,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapushargaAll($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function exportDataProduct($id_pameran)
    {

        $spreadsheet = new Spreadsheet();
        $dataharga = $this->Pameran->productharga($id_pameran);
        $datain = $this->Pameran->detailpameran($id_pameran);
        $folder = '';
        $jensi_grading = $this->JGrading->getgrading();
        $code = 71;


        $nama_pameran = $datain[0]['nama_pameran'];
        $namafile = "HARGA_ELEKTRONIK-" . $nama_pameran . "-" . date("YMd-His") . ".xlsx";
        $namafile = str_replace(' ', '', $namafile);
        $spreadsheet->getProperties()
            ->setTitle('ENB Mobile Care')
            ->setSubject('ENB Mobile Care')
            ->setDescription('Harga HP Web Admin ')
            ->setCreator('ENB Mobile Care')
            ->setLastModifiedBy('ENB Mobile Care');
        $downloadfile = $downloadfile = $folder . $namafile;
        $total_data = count($dataharga);
        if ($total_data > 0) {
            //Adding data to the excel sheet
            foreach ($jensi_grading as $value) {
                $code = $code + 1;
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Kode Produk')
                    ->setCellValue('B1', 'Kategori')
                    ->setCellValue('C1', 'Merk')
                    ->setCellValue('D1', 'Spec')
                    ->setCellValue('E1', 'Size')
                    ->setCellValue('F1', 'Tahun')
                    ->setCellValue('G1', 'Subsidi')
                    ->setCellValue(chr($code) . '1', 'Harga ' . $value['nama_grading']);
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A1:' . chr($code) . '1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('0000FF');
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15.42);
                $spreadsheet->getActiveSheet()->getColumnDimension(chr($code))->setWidth(15.42);
            }
            $i = 1;
            foreach ($dataharga as $value) {
                $dataid = $this->Pameran->cekid_produk($value['kode_produk']);
                // $dataid[0]['id_product']
                $datahargaproduct = $this->Pameran->detailmasterharga($dataid[0]['id_product']);
                $i++;

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $value['kode_produk'])
                    ->setCellValue('B' . $i, $value['kategori'])
                    ->setCellValue('C' . $i, $value['merk'])
                    ->setCellValue('D' . $i, $value['spec'])
                    ->setCellValue('E' . $i, $value['size'])
                    ->setCellValue('F' . $i, $value['tahun'])
                    ->setCellValue('G' . $i, $value['subsidi']);
                $codevalue = 71;
                foreach ($datahargaproduct as $valuegrading) {
                    $codevalue = $codevalue + 1;
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue(chr($codevalue) . $i, $valuegrading['harga']);
                }
            }
        } else if ($total_data < 1) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'BELUM ADA DATA');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer = new Xlsx($spreadsheet);
        $writer->save($downloadfile);
        header('Content-type:  text/csv');
        header('Content-Length: ' . filesize($downloadfile));
        header('Content-Disposition: attachment; filename="' . $namafile . '"');
        readfile($downloadfile);
        ignore_user_abort(true);
        if (connection_aborted()) {
            unlink($downloadfile);
        }
        unlink($downloadfile);
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Hello World !');
        // $writer = new Xlsx($spreadsheet);
        // $writer->save('hello world.xlsx');
        // return $this->response->download('world.xlsx', null)->setFileName('coba.xlsx');
    }
    function tamplate()
    {
        $jensi_grading = $this->JGrading->getgrading();
        $kategori = $this->Kategori->getkategori();
        $spreadsheet = new Spreadsheet();
        $code = 73;
        $code2 = 73;
        $codevalue = 73;
        $indexvalue = 1;
        $folder = '';
        $namafile = "HARGA_ELEKTRONIK_Tamplate-" . "-" . date("YMd-His") . ".xlsx";
        $namafile = str_replace(' ', '', $namafile);
        $spreadsheet->getProperties()
            ->setTitle('ENB Mobile Care')
            ->setSubject('ENB Mobile Care')
            ->setDescription('Harga HP Web Admin ')
            ->setCreator('ENB Mobile Care')
            ->setLastModifiedBy('ENB Mobile Care');
        $downloadfile = $downloadfile = $folder . $namafile;
        foreach ($jensi_grading as $value) {
            $code = $code + 1;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Kode Produk')
                ->setCellValue('B1', 'Kategori')
                ->setCellValue('C1', 'Merk')
                ->setCellValue('D1', 'Spec')
                ->setCellValue('E1', 'Size')
                ->setCellValue('F1', 'Tahun')
                ->setCellValue('G1', 'Subsidi')
                ->setCellValue('H1', 'Subsidi mitra 1')
                ->setCellValue('I1', 'Subsidi mitra 2')
                ->setCellValue(chr($code) . '1', 'Harga ' . $value['nama_grading']);
        }
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1:' . chr($code) . '1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('0000FF');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15.42);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15.42);
        foreach ($jensi_grading as $value) {
            $code2 = $code2 + 1;
            $spreadsheet->getActiveSheet()->getColumnDimension(chr($code2))->setWidth(15.42);
        }
        foreach ($jensi_grading as $value) {
            $codevalue = $codevalue + 1;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A2', 'T004')
                ->setCellValue('B2', 'Drone')
                ->setCellValue('C2', 'Acer')
                ->setCellValue('D2', 'ok')
                ->setCellValue('E2', '14 INC')
                ->setCellValue('F2', '2500')
                ->setCellValue('G2', 50000)
                ->setCellValue('H2', 50000)
                ->setCellValue('I2', 50000)
                ->setCellValue(chr($codevalue) . '2', 50000);
        }


        $spreadsheet->getActiveSheet()->setTitle('Harga Produk');
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1)
            ->setCellValue('A1', 'Contoh Kategori');

        $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('0000FF');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18.42);
        foreach ($kategori as $data) {
            $indexvalue = $indexvalue + 1;
            $spreadsheet->setActiveSheetIndex(1)
                ->setCellValue('A' . $indexvalue, $data['nama_kategori']);
        }
        $spreadsheet->getActiveSheet()->setTitle('List Kategori');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer = new Xlsx($spreadsheet);
        $writer->save($downloadfile);
        header('Content-type:  text/csv');
        header('Content-Length: ' . filesize($downloadfile));
        header('Content-Disposition: attachment; filename="' . $namafile . '"');
        readfile($downloadfile);
        ignore_user_abort(true);
        if (connection_aborted()) {
            unlink($downloadfile);
        }
        unlink($downloadfile);
    }
    function ImportDataProduct()
    {
        $response = initResponse();
        $hasError = false;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'navbar' => '',
            ],
        ];
        $berhasil = 0;
        $gagal = 0;
        $kodenama = '';
        $editing = $this->data['admin']->username;
        $id_pameran = isset($_POST['id_pameran']) ? $_POST['id_pameran'] : 0;
        $file_excel = $this->request->getFile('fileexcel');
        // File path config 
        if (!empty($_FILES["fileproduk"]["name"])) {

            $fileName = basename($_FILES["fileproduk"]["name"]);
            // $targetFilePath = $uploadDir . $fileName;
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

            // Allow certain file formats 
            $allowTypes = array('xls', 'xlsx');
            if (in_array($fileType, $allowTypes)) {
                // Upload file to the server
                // $fName = $targetFilePath;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($_FILES["fileproduk"]["tmp_name"]);
                $spreadsheet->setActiveSheetIndex(0);
                $row = $spreadsheet->getActiveSheet()->toArray();
                $rowCount = count($row);
                $hasilRow = $rowCount - 1;

                $succ = 0;
                $duplicateKodeProduk = "";

                $msg = "";
                $harga = [];
                $indexawal = 8;
                $lengthvalue = count($row[1]);
                if ($row[0][0] == 'Kode Produk') {
                    for ($i = 1; $i < $rowCount; $i++) {
                        $kode_produk = $row[$i]['0'];
                        $kategori = $row[$i]['1'];
                        $tahun = $row[$i]['5'];
                        $merk = $row[$i]['2'];
                        $spec = $row[$i]['3'];
                        $size = $row[$i]['4'];
                        $subsidi = $row[$i]['6'];
                        $subsidi2 = $row[$i]['7'];
                        $subsidi3 = $row[$i]['8'];
                        for ($datavalue = 9; $datavalue < $lengthvalue; $datavalue++) {
                            array_push($harga, ['id_product' => $kode_produk, 'id_jgrading' => $row[0][$datavalue], 'harga' => $row[$i][$datavalue]]);
                        }
                        $arraypameran = $this->Pameran->namapameran($id_pameran);
                        $nama_pameran = $arraypameran[0]['nama_pameran'];
                        $in = array(
                            'id_pameran' => $id_pameran,
                            'kode_produk' => $kode_produk,
                            'spec' => $spec,
                            'kategori' => $kategori,
                            'tahun' => $tahun,
                            'size' => $size,
                            'merk' => $merk,
                            'subsidi' => $subsidi,
                            'subsidi_mitra1' => $subsidi2,
                            'subsidi_mitra2' => $subsidi3,
                            'harga' => $harga,
                        );

                        $out = array(
                            'nama_pameran' => $nama_pameran,
                            'kode_produk' => $kode_produk,
                            'id' => 25,
                            'editing' => $editing,
                            'status' => 'active'
                        );
                        $this->db->transStart();
                        $cekData = $this->Pameran->cekpameranData($kode_produk, $id_pameran);
                        $SumName = $this->Pameran->cekKode_produk($kode_produk, $id_pameran);
                        if ($cekData > 0) {
                            $gagal++;
                            $kodenama = $kodenama . ' ' . $SumName[0]['kode_produk'];
                        } else {
                            $berhasil++;
                            $this->Pameran->saveharga2($in);
                            foreach ($harga as $value) {
                                $cekproduct = $this->Pameran->cekproduct2($value['id_product']);
                                $cekgrading = $this->Pameran->cekgrading2($value['id_jgrading']);
                                $hargabarang = array(
                                    'id_product' => $cekproduct[0]['id_product'],
                                    'id_jgrading' => $cekgrading[0]['id_jgrading'],
                                    'harga' => $value['harga'],
                                );
                                $dataharga = $this->Pameran->savehargamaster($hargabarang);
                            }
                            $this->db->transComplete();
                            $LogModule = new LogModule;
                            $dataLog   = $LogModule->Logtambahharga($out);
                        }
                    }
                    if ($this->db->transStatus() === FALSE || $hasError) {
                        if (!$hasError) $response = "Faield" / json_encode($this->db->error());
                    } else {
                        $response->nama = $kodenama;
                        $response->gagal =  $gagal;
                        $response->berhasil =  $berhasil;
                        $response->success = TRUE;
                        $response->message = "Sukses";
                    }
                    return $this->respond($response);
                }
            } else {
                var_dump('salah coooy');
                die;
            }
        }
    }
    function listharga()
    {
        $req = $this->request;
        $id_produk = $req->getVar('id_produk');
        $dataout = $this->Pameran->detailproduct($id_produk);
        $datain = [];
        foreach ($dataout as $element) {
            array_push($datain, ['nama_grading' => $element['nama_grading'], 'harga' => 'Rp' . number_format($element['harga'], 0, ",", ".")]);
        }
        echo json_encode($datain);
    }
    function listmasterharga()
    {

        $dataout = $this->JGrading->getgrading();
        echo json_encode($dataout);
    }
    function cekdatakode()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $kode_produk = $output['params']['data']['kode_produk'];
        $dataout = $this->Pameran->cekkodeproduct($kode_produk);
        $lengt = count($dataout);
        echo json_encode($lengt);
    }
    function tambah_datahargagrading()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_product = $output['params']['data']['id_product'];
        $valueharga = $output['params']['data']['valueharga'];
        $dataout = $this->JGrading->getgrading();
        $lengt = count($dataout);
        for ($i = 0; $i < $lengt; $i++) {
            $in = array(
                'id_product' => $id_product,
                'id_jgrading' => $dataout[$i]['id_jgrading'],
                'harga' => $valueharga[$i],
            );
            $datasaveharga = $this->Pameran->savehargamaster($in);
        }
    }
    function showedit_dataharga()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_product = $output['params']['data']['id_product'];
        $dataproduct = $this->Pameran->detailmasterharga2($id_product);

        echo json_encode($dataproduct);
    }
    function hapus_datahargagrading()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_product = $output['params']['data']['id_product'];
        $datahapusharga = $this->Pameran->hapushargamaster($id_product);
    }
}
