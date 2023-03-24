<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;
use App\Models\LogModule;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use mysqli;

class MasterAdmin extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $MasterAdmin = new MasterAdminModule;
        $datamasterAdmin   = $MasterAdmin->getadmin();
        $this->data += [
            'page' => (object)[
                'key' => '2-MasterAdmin',
                'title' => 'Data Master Admin',
                'subtitle' => 'Data Master Admin',
                'role' => $datamasterAdmin,
                'navbar' => '',
            ],
        ];
        $nama_role = 'admin';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'master_admin/index',
                $this->data
            );
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "a.username",
            "r.nama_role",

        );
        // fields to search with
        $fields_search = array(
            "a.username",
            "r.nama_role",
        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'admin';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $this->builder = $this->db
            ->table("$this->table_name as a")
            ->join("admin_role as r", "a.id_role=r.id_role", "left")
            ->orderBy(1, 'desc');

        // select fields

        // building where query
        $select_fields = 'a.id_admin,a.username,a.password,a.id_role,r.nama_role,a.deleted_at';
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
            data-id_admin="' . $row->id_admin . '"
            data-username="' . $row->username . '" 
            data-password="' . $row->password . '" 
            data-role="' . $row->id_role . '" 
            "
        >
            <i class="fas fa-edit"></i> EDIT
        </button>
        <button href="#" data-target="#tombolHapus" class="btn btn-danger btn-sm mb-1 tombolHapus" data-username="' . $row->username . '" data-id_admin="' . $row->id_admin . '" ">
            <i class="fas fa-trash"></i> HAPUS
        </button>
        ';
                $r = [];
                $r[] = $i;
                $r[] = $row->username;
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
        $output = array(
            'params' => array(
                'aksi' => $_POST['aksi'],
                'data' => $_POST['data']
            )
        );
        $username = $output['params']['data']['username'];
        $password = $output['params']['data']['password'];
        $role = $output['params']['data']['role'];
        $editing = $this->data['admin']->username;
        $encrypter = \Config\Services::encrypter();
        $password_enk = bin2hex($encrypter->encrypt($password));

        $in = array(
            'username' => $username,
            'id_role' => $role,
            'password_enk' => $password_enk,
            'password' => $password,
            'editing' => $editing,
            'status' => 'active'
        );
        $out = array(
            'username' => $username,
            'id' => 7,
            'id_role' => $role,
            'password_enk' => $password_enk,
            'password' => $password,
            'editing' => $editing,
            'status' => 'active'
        );
        $this->db->transStart();
        $this->Admin->saveadmin($in);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $LogModule = new LogModule;
            $dataLog   = $LogModule->Logadmin($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
    function editAdmin()
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
        $password = $this->request->getPost('password');
        $id_role = $this->request->getPost('id_role');
        $encrypter = \Config\Services::encrypter();
        $password_enk = bin2hex($encrypter->encrypt($password));
        $data_potong = [];
        $data_potong['id_admin'] = $id_admin;
        $data_potong['id_role'] = $id_role;
        $data_potong['username'] = $username;
        $data_potong['password'] = $password;
        $data_potong['password_enk'] = $password_enk;

        // var_dump($data_potong);
        // die;
        // $LogModel = new LogModule;
        $this->db->transStart();
        $this->Admin->update($id_admin, $data_potong);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'username' => $username,
                'id' => 8,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_editadmin($out);

            $response->success = TRUE;
            $response->message = "Sukses";
            $response->data = $data_potong;
            json_encode($data_potong);
        }
        return $this->respond($response);
    }
    function HapusAdmin()
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
        $this->Admin->deleteAdmin($id_admin);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $out = array(
                'username' => $username,
                'id' => 9,
                'editing' => $editing
            );
            $LogModule = new LogModule;
            $datatamp   = $LogModule->Log_hapusadmin($out);

            $response->success = TRUE;
            $response->message = "Sukses";
        }
        return $this->respond($response);
    }
}
