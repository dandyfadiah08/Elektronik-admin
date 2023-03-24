<?php

namespace App\Controllers;

use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\MasterKategoriModule;
use App\Models\LogModule;
use App\Models\JenisGradingModule;
use App\Models\MasterKuisionerModule;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use mysqli;
use PhpParser\Node\Stmt\Foreach_;

class MasterKuisioner extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $datagrading  = $this->Kategori->getkategori();
        $MasterAdmin = new MasterAdminModule;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKuisioner',
                'title' => 'Data Master Kuisioner',
                'subtitle' => 'Data Master Kuisioner',
                'navbar' => '',
                'data' => $datagrading
            ],
        ];
        $nama_role = 'kuisioner';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_kuisioner/index',
                $this->data
            );
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "a.kuisioner",
            "g.nama_kategori",
            "a.number",

        );
        // fields to search with
        $fields_search = array(
            "a.kuisioner",
            " g.nama_kategori",
            "a.number",
        );
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'master_kuisioner';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $status = $this->request->getPost('kategori');
        $sortColumn = $fields_order[$sortColumn - 1];
        if ($status == 'ALL') {
            $this->builder = $this->db
                ->table("$this->table_name as a")
                ->join("master_kategori as g", "g.id_kategori=a.id_kategori", "inner")
                ->orderBy('g.nama_kategori')
                ->orderBy('a.number');
        } else {
            $this->builder = $this->db
                ->table("$this->table_name as a")
                ->join("master_kategori as g", "g.id_kategori=a.id_kategori", "inner")
                ->where('a.id_kategori', $status)
                ->orderBy('g.nama_kategori')
                ->orderBy('a.number');
        }


        // select fields
        // building where query
        $select_fields = 'a.id_mkuisioner,a.number,a.kuisioner,a.id_kategori,g.nama_kategori,a.date_save,a.deleted_at';
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
            $list = '';
            // looping through data result
            foreach ($dataResult as $row) {
                $i++;
                $aksi = '
                <button href="#" class="btn btn-warning btn-sm mb-1 editModalKuisioner"  data-toggle="modal" data-target="#editModalKuisioner"
                    data-id_mkuisioner="' . $row->id_mkuisioner . '"
                    data-id_kategori="' . $row->id_kategori . '"
                    data-number="' . $row->number . '"
                    data-kuisioner="' . $row->kuisioner . '" 
                    "
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button href="#" data-target="#tombolHapus" class="btn btn-danger btn-sm mb-1 tombolHapus" data-kuisioner="' . $row->kuisioner . '" data-id_mkuisioner="' . $row->id_mkuisioner . '" ">
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';
                $aksi2 = '
                <small class="btn badge badge-info listModalKuisioner" data-toggle="modal" data-target="#listModalKuisioner" 
                data-id_mkuisioner="' . $row->id_mkuisioner . '" data-listmasterkuisioner="' . $row->kuisioner . '" 
                "
                >
                <i class="fa fa-eye"></i> view
              </small>
                ';
                $r = [];
                $r[] = $i;
                $r[] = $row->number;
                $r[] = $row->kuisioner;
                $r[] =  $row->nama_kategori;
                $r[] = $aksi2;
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
                'key' => '2-MasterKuisioner',
                'title' => 'Data Master Kuisioner',
                'subtitle' => 'Data Master Kuisioner',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $kuisioner = $output['params']['data']['kuisioner'];
        $id_jgrading = $output['params']['data']['id_jgrading'];
        $listkuisioner = $output['params']['data']['lisrtkuisioner'];
        $number = $output['params']['data']['numberlevel'];
        $editing = $this->data['admin']->username;
        $in = array(
            'kuisioner' => $kuisioner,
            'id_jgrading' => $id_jgrading,
            'number' => $number

        );
        $out = array(
            'kuisioner' => $kuisioner,
            'id' => 46,
            'editing' => $editing,
        );
        $this->db->transStart();
        $this->MasterKuisioner->savekuisioner($in);
        $coba = $this->MasterKuisioner->dataend();

        $id_mkuisioner = $coba[0]['id_mkuisioner'];
        $this->MasterKuisioner->savelistkuisioner($id_mkuisioner, $listkuisioner);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logkuisioner($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function editkuisioner()
    {

        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKuisioner',
                'title' => 'Data Master Kuisioner',
                'subtitle' => 'Data Master Kuisioner',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_mkuisioner = $this->request->getPost('id_mkuisioner');
        $kuisioner = $this->request->getPost('kuesioner');
        $kategori = $this->request->getPost('kategori');
        $listtext = $this->request->getPost('listtextcek');
        $number = $this->request->getPost('number');

        $data_kuisioner = [];
        $data_kuisioner['id_mkuisioner'] = $id_mkuisioner;
        $data_kuisioner['kuisioner'] = $kuisioner;
        $data_kuisioner['kategori'] = $kategori;
        $data_kuisioner['number'] = $number;
        // // $LogModel = new LogModule;
        $this->db->transStart();
        $this->MasterKuisioner->updatedata($id_mkuisioner, $data_kuisioner);
        $this->MasterKuisioner->deletelistKuisioner($id_mkuisioner);
        $this->MasterKuisioner->savelistkuisioner($id_mkuisioner, $listtext);

        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'kuisioner' => $kuisioner,
                'id' => 47,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editkuisioner($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function   Hapuskuisioner()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKuisioner',
                'title' => 'Data Master Kuisioner',
                'subtitle' => 'Data Master Kuisioner',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_mkuisioner = $this->request->getPost('id_mkuisioner');
        $kuisioner = $this->request->getPost('kuesioner');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $datailidlist = $this->MasterKuisioner->detailidkuisioner($id_mkuisioner);
        foreach ($datailidlist as $value) {
            $data = $this->MasterKuisioner->detaillistgambarview($value['id_listkuisioner']);
            $lengthgambar = count($data);
            if ($lengthgambar > 0) {

                $datailidlist = $this->MasterKuisioner->deletelistgambar($value['id_listkuisioner']);
            }
        }
        $this->MasterKuisioner->deleteKuisioner($id_mkuisioner);
        $this->MasterKuisioner->deletelistKuisioner($id_mkuisioner);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'kuisioner' => $kuisioner,
                'id' => 48,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapuskuisioner($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function listkuisioner()
    {
        $req = $this->request;
        $id = $req->getVar('id');
        $list =  $this->MasterKuisioner->detaillist($id);
        $lengt = count($list);
        $kosong = "";
        if ($lengt > 0) {
            echo json_encode($list);
        } else {
            echo json_encode($kosong);
        }
    }
    function upload_foto()
    {
        $dataidlist = $_REQUEST['idlist'];
        $baseurl = base_url();
        $file = $_FILES['file']['name'];
        $path = 'assets/images/datagambar/' . $file;
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        $datakaterangan = $_REQUEST['keterangan'];
        $list =  $this->MasterKuisioner->detaillistgambar($dataidlist);
        $data1 = array(
            'id_listkuisioner' => $list[0]['id_listkuisioner'],
            'gambar' => $baseurl . '/' . $path,
            'keterangan' => $datakaterangan,
        );
        $save = $this->MasterKuisioner->savelistgambar($data1);
    }
    function detaillistgambar()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id_listkuisioner = $output['params']['data']['idlist'];
        $list =  $this->MasterKuisioner->detaillistgambarview($id_listkuisioner);
        echo json_encode($list);
    }
    function detail_datagambar()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $listkuisioner = $output['params']['data']['datalistgambar'];
        $list =  $this->MasterKuisioner->detaillistkuisioner($listkuisioner);
        echo json_encode($list);
    }
    function view_datagambar()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $idlistkuisioner = $output['params']['data']['datagambar'];
        $listgambar =  $this->MasterKuisioner->detaillistgambarview($idlistkuisioner);
        echo json_encode($listgambar);
    }
    function hapusgambar()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id = $output['params']['data']['id'];
        $listid =  $this->MasterKuisioner->detaillistkuisioner($id);
        $intlist = intval($listid[0]['id_listkuisioner']);
        $idlistoption = $intlist - 1;
        $list =  $this->MasterKuisioner->deletelistgambar($idlistoption);
    }
    function detaillistkuisioner()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id = $output['params']['data']['id'];
        $list =  $this->MasterKuisioner->detaillistkuisioner($id);
        echo json_encode($list);
    }
    function updated_listgambar()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $id = $output['params']['data']['id'];
        $gambar = $output['params']['data']['gambar'];
        $keterangan = $output['params']['data']['keterangan'];
        $in = array(
            'id_listkuisioner' => $id,
            'gambar' => $gambar,
            'keterangan' => $keterangan,

        );
        $list =  $this->MasterKuisioner->savelistgambarkuisioner($in);
        echo json_encode($list);
    }
    function updated_listgambarfile()
    {
        $baseurl = base_url();
        $file = $_FILES['file']['name'];
        $path = 'assets/images/datagambar/' . $file;
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        $dataidlist = $_REQUEST['idlist'];
        $datakaterangan = $_REQUEST['keterangan'];
        $data1 = array(
            'id_listkuisioner' => $dataidlist,
            'gambar' => $baseurl . '/' . $path,
            'keterangan' => $datakaterangan,
        );
        $save = $this->MasterKuisioner->savelistgambar($data1);
    }
    function detaillevel()
    {
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $idkategori = $output['params']['data']['kategori'];
        $number = $output['params']['data']['numberlevel'];
        $level = $this->MasterKuisioner->filterlevel($idkategori, $number);
        $lengthlevel  = count($level);
        echo json_encode($lengthlevel);
    }
}
