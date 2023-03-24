<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\MasterRoleModule;
use App\Models\MasterKategoriModule;
use App\Models\LogModule;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use mysqli;

class MasterKategori extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $MasterAdmin = new MasterAdminModule;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKategori',
                'title' => 'Data Master Kategori',
                'subtitle' => 'Data Master Kategori',
                'navbar' => '',
            ],
        ];
        $nama_role = 'kategori';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_kategori/index',
                $this->data
            );
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "a.nama_kategori",

        );
        // fields to search with
        $fields_search = array(
            "a.nama_kategori",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'master_kategori';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_kategori,a.nama_kategori,a.deskripsi,a.date_save,a.deleted_at';
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
                <button href="#" class="btn btn-warning btn-sm mb-1 editModalKategori"  data-toggle="modal" data-target="#editModalKategori"
                    data-id_kategori="' . $row->id_kategori . '"
                    data-deskripsi="' . $row->deskripsi . '"
                    data-nama_kategori="' . $row->nama_kategori . '" 
                    "
                >
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button href="#" data-target="#tombolHapus" class="btn btn-danger btn-sm mb-1 tombolHapus" data-nama_kategori="' . $row->nama_kategori . '" data-id_kategori="' . $row->id_kategori . '" ">
                    <i class="fas fa-trash"></i> HAPUS
                </button>
                ';
                $r = [];
                $r[] = $i;
                $r[] = $row->nama_kategori;
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
                'key' => '2-MasterKategori',
                'title' => 'Data Master Kategori',
                'subtitle' => 'Data Master Kategori',
                'navbar' => '',
            ],
        ];
        $kategori = $_REQUEST['kategori'];
        $file = $_FILES['file']['name'];
        $path = 'assets/images/datakategori/' . $file;
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        $editing = $this->data['admin']->username;

        $baseurl = base_url();
        $in = array(
            'kategori' => $kategori,
            'deskripsi' => $baseurl . '/' . $path,

        );
        $out = array(
            'kategori' => $kategori,
            'id' => 40,
            'editing' => $editing,
        );
        $this->db->transStart();
        $this->Kategori->savekategori($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logkategori($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function editkategori()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKategori',
                'title' => 'Data Master Kategori',
                'subtitle' => 'Data Master Kategori',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_kategori = $_REQUEST['id'];
        $nama_kategori = $_REQUEST['kategori'];
        $file = $_FILES['file']['name'];
        $path = 'assets/images/datakategori/' . $file;
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        $baseurl = base_url();
        $data_kategori = [];
        $data_kategori['id_kategori'] = $id_kategori;
        $data_kategori['nama_kategori'] = $nama_kategori;
        $data_kategori['deskripsi'] = $baseurl . '/' . $path;
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Kategori->updatedata($id_kategori, $data_kategori);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'kategori' => $nama_kategori,
                'id' => 41,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editkategori($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function editkategoriempty()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKategori',
                'title' => 'Data Master Kategori',
                'subtitle' => 'Data Master Kategori',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_kategori = $this->request->getPost('id');
        $nama_kategori = $this->request->getPost('kategori');

        $data_kategori = [];
        $data_kategori['id_kategori'] = $id_kategori;
        $data_kategori['nama_kategori'] = $nama_kategori;

        // var_dump($data_kategori);
        // die;
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Kategori->updatedataempty($id_kategori, $data_kategori);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'kategori' => $nama_kategori,
                'id' => 41,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editkategori($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function Hapuskategori()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterKategori',
                'title' => 'Data Master Kategori',
                'subtitle' => 'Data Master Kategori',
                'navbar' => '',
            ],
        ];
        $editing = $this->data['admin']->username;
        $response = initResponse();
        $hasError = false;
        $id_kategori = $this->request->getPost('id');
        $nama_kategori = $this->request->getPost('kategori');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Kategori->deleteKategori($id_kategori);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'kategori' => $nama_kategori,
                'id' => 42,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapuskategori($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
}
