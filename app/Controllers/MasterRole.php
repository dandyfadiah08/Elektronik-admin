<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\MasterRoleModule;
use App\Models\LogModule;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use mysqli;

class MasterRole extends BaseController
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $MasterAdmin = new MasterAdminModule;
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterRole',
                'title' => 'Data Master Role',
                'subtitle' => 'Data Master Role',
                'navbar' => '',
            ],
        ];
        $nama_role = 'role';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_role/index',
                $this->data
            );
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "a.nama_role",

        );
        // fields to search with
        $fields_search = array(
            "a.nama_role",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'admin_role';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_role,a.nama_role,a.status,a.tradein,a.statistik,a.admin,a.pameran,a.potongan,a.log,a.produk,a.new_device,a.kategori,a.kuisioner,a.grading,a.created_by,a.created_at,a.updated_by,a.updated_at,a.role,a.deleted_by,a.deleted_at';
        $reviewed = $req->getVar('reviewed') ?? 0;
        $is_reviewed = $reviewed == 1;
        // $status = $req->getVar('status') ?? '';
        // $merchant = $req->getVar('merchant') ?? '';
        // $date = $req->getVar('date') ?? '';
        // if (!empty($date)) {
        //     $dates = explode(' / ', $date);
        //     if (count($dates) == 2) {
        //         $start = $dates[0];
        //         $end = $dates[1];
        //         $this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
        //         $this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
        //     }
        // }
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
                // foreach ($tampquery as $value) {
                //     $tampdata = $value['nama_mitra'];
                //     break;
                // }
                // print_r($tampdata);
                //         $status = getDeviceCheckStatus($row->status);
                //         $status_color = 'default';
                //         if ($row->status_internal == 5) $status_color = 'success';
                //         elseif ($row->status_internal > 5) $status_color = 'danger';
                //         elseif ($row->status == 4 || $row->status_internal == 4) $status_color = 'primary';
                //         elseif ($row->status == 7) $status_color = 'success';
                //         $price = "-";
                //         $action = '<button class="btn btn-xs mb-2 btn-default">' . $status . '</button>';
                //         if ($is_reviewed) {
                //             $price = number_to_currency($row->price, "IDR");
                //             $action .= '<br><button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Status Internal ' . $row->status_internal . '">' . getDeviceCheckStatusInternal($row->status_internal) . '</button>';
                //         }

                //         $btn['logs'] = [
                //             'class'    => "btnLogs",
                //             'title'    => "View logs of $row->check_code",
                //             'data'    => 'data-id="' . $row->check_id . '"',
                //             'icon'    => 'fas fa-history',
                //             'text'    => '',
                //         ];
                //         // $action .= htmlAnchor($btn['view']);
                //         $merchant = $row->merchant_id > 0 ? '<br><a class="btn btn-xs mb-2 btn-warning" href="' . $url->merchant . $row->merchant_code . '" target="_blank" title="View merchant">' . $row->merchant_name . '</a>' : '';
                //         $check_code = '<a href="' . $url->detail . $row->check_id . '" title="View detail of ' . $row->check_code . '" target="_blank">' . $row->check_code . '</a>';



                // $data_cek = '<a href="' . $url->detail . $row->no . '" title="View detail of '  . '" target="_blank">' . '<button class="btn btn-warning btn-sm mb-1 tombolcek">' . '<i class="fas fa-search"></i>
                // </button>' . '</a>';
                // $tgl = date("Y-m-d", strtotime($row->date_save));
                $aksi = '
        <button href="#" class="btn btn-warning btn-sm mb-1 editModalAdmin"  data-toggle="modal" data-target="#editModalAdmin"
        data-id_role="' . $row->id_role . '"
        data-nama_role="' . $row->nama_role . '" 
        data-status="' . $row->status . '" 
        data-tradein="' . $row->tradein . '"
        data-statistik="' . $row->statistik . '" 
        data-admin="' . $row->admin . '" 
        data-kategori="' . $row->kategori . '" 
        data-pameran="' . $row->pameran . '" 
        data-grading="' . $row->grading . '"
        data-kuisioner="' . $row->kuisioner . '"
        data-potongan="' . $row->potongan . '" 
        data-log="' . $row->log . '" 
        data-produk="' . $row->produk . '" 
        data-newdevice="' . $row->new_device . '"  
        data-role="' . $row->role . '"  
        >
            <i class="fas fa-edit"></i> EDIT
        </button>
        <button href="#" data-target="#tombolHapus" data-username="' . $row->nama_role . '" data-id_role="' . $row->id_role . '" class="btn btn-danger btn-sm mb-1 tombolHapus"  ">
            <i class="fas fa-trash"></i> HAPUS
        </button>
        ';
                $r = [];
                $r[] = $i;
                $r[] = $row->nama_role;
                $r[] = $aksi;

                //         $r[] = formatDate($row->created_at);
                //         $r[] =
                //             $r[] = $row->imei;
                //         $r[] = "$row->brand $row->model $row->storage $row->type";
                //         $r[] = "$row->grade<br>$price";
                //         $r[] = "$row->name<br>$row->customer_name " . (true ? $row->customer_phone : "");
                //         $r[] = $action;
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
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterRole',
                'title' => 'Data Master Role',
                'subtitle' => 'Data Master Role',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $response = initResponse();
        $hasError = false;
        $nama_role = $output['params']['data']['nama_role'];
        $tradein = $output['params']['data']['tradein'];
        $statistik = $output['params']['data']['statistik'];
        $potongan = $output['params']['data']['potongan'];
        $new_device = $output['params']['data']['produk'];
        $produk = $output['params']['data']['produk'];
        $pameran = $output['params']['data']['pameran'];
        $admin = $output['params']['data']['admin'];
        $kategori = $output['params']['data']['kategori'];
        $grading = $output['params']['data']['grading'];
        $kuisioner = $output['params']['data']['kuisioner'];
        $role = $output['params']['data']['role'];
        $log = $output['params']['data']['log'];
        $editing = $this->data['admin']->username;
        $in = array(
            'nama_role' => $nama_role,
            'tradein' => $tradein,
            'statistik' => $statistik,
            'potongan' => $potongan,
            'new_device' => $new_device,
            'produk' => $produk,
            'pameran' => $pameran,
            'grading' => $grading,
            'kuisioner' => $kuisioner,
            'admin' => $admin,
            'kategori' => $kategori,
            'role' => $role,
            'log' => $log,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->masterRole->saverole($in);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_role' => $nama_role,
                'id' => 37,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_tambahrole($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function update_data()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterRole',
                'title' => 'Data Master Role',
                'subtitle' => 'Data Master Role',
                'navbar' => '',
            ],
        ];
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $response = initResponse();
        $hasError = false;
        $id_role = $output['params']['data']['id_role'];
        $nama_role = $output['params']['data']['nama_role'];
        $tradein = $output['params']['data']['tradein'];
        $statistik = $output['params']['data']['statistik'];
        $potongan = $output['params']['data']['potongan'];
        $kuisioner = $output['params']['data']['kuisioner'];
        $new_device = $output['params']['data']['new_device'];
        $produk = $output['params']['data']['produk'];
        $pameran = $output['params']['data']['pameran'];
        $admin = $output['params']['data']['admin'];
        $kategori = $output['params']['data']['kategori'];
        $grading = $output['params']['data']['grading'];
        $role = $output['params']['data']['role'];
        $log = $output['params']['data']['log'];
        $editing = $this->data['admin']->username;
        $data_potong = [];
        $data_potong = [];
        $data_potong['id_role'] = $id_role;
        $data_potong['nama_role'] = $nama_role;
        $data_potong['tradein'] = $tradein;
        $data_potong['statistik'] = $statistik;
        $data_potong['potongan'] = $potongan;
        $data_potong['new_device'] = $new_device;
        $data_potong['produk'] = $produk;
        $data_potong['pameran'] = $pameran;
        $data_potong['admin'] = $admin;
        $data_potong['kategori'] = $kategori;
        $data_potong['kuisioner'] = $kuisioner;
        $data_potong['grading'] = $grading;
        $data_potong['role'] = $role;
        $data_potong['log'] = $log;
        $this->db->transStart();
        $this->masterRole->update($id_role, $data_potong);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'nama_role' => $nama_role,
                'id' => 38,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editrole($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function HapusRole()
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
        $id_admin = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->masterRole->deleteRole($id_admin);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'username' => $username,
                'id' => 39,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapusrole($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
}
