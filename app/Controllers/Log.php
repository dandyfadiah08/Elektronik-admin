<?php

namespace App\Controllers;

use App\Models\LogModule;
use App\Models\MasterAdminModule;
use App\Models\TradeinModule;
use mysqli;

class Log extends BaseController
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $MasterAdmin = new MasterAdminModule;
        $LogModel = new LogModule;
        $date = strtotime("tomorrow");
        $end_date = date("m/d/Y h:i a", $date);
        $date2 = strtotime("-1 Months");
        $start_date = date("m/d/Y h:i a", $date2);
        $field = $LogModel->getAllData();
        $this->data += [
            'page' => (object)[
                'key' => '2-logs',
                'title' => 'Data Logs',
                'subtitle' => 'Data Logs',
                'data' => $field,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'navbar' => '',
            ],
        ];
        $nama_role = 'log';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('log/index', $this->data);
        }
    }
    function load_data()
    {
        // var_dump($status);
        // die;

        $fields_order = array(
            null,
            "t.id_log_aksi",
            "t.user",
            "t.kategori",
            "t.aksi",
            "t.created_at",
            "t.deleted_at"

        );
        // fields to search with
        $fields_search = array(
            "t.id_log_aksi",
            "t.user",
            "t.kategori",
            "t.aksi",
            "t.created_at",
            "t.deleted_at"


        );
        $TestModel = new TradeinModule;
        $LogModel = new LogModule();
        $UsernameAksi = $this->data['admin']->username;
        // $datalog = $LogModel->getAllDataLogAksi($UsernameAksi);
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'log_aksi';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        $date = $this->request->getPost('date');
        $startdate = explode("-", $date);
        $status = $this->request->getPost('status');
        if ($date != '') {
            if ($status == 'semua') {
                $starttime = strtotime($startdate[0]);
                $startformat = date('Y-m-d', $starttime);
                $endtime = strtotime($startdate[1]);
                $endformat = date('Y-m-d', $endtime);
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->where('t.created_at >=', $startformat)
                    ->where('t.created_at <=', $endformat)
                    ->where('t.user', $UsernameAksi);
            } else {
                $starttime = strtotime($startdate[0]);
                $startformat = date('Y-m-d', $starttime);
                $endtime = strtotime($startdate[1]);
                $endformat = date('Y-m-d', $endtime);
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->where('t.created_at >=', $startformat)
                    ->where('t.created_at <=', $endformat)
                    ->where('t.kategori ', $status)
                    ->where('t.user', $UsernameAksi);
            }
        } else {
            if ($status == 'semua') {
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->where('t.user', $UsernameAksi);
            } else {
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->where('t.kategori ', $status)
                    ->where('t.user', $UsernameAksi);
            }
        }

        // building where query
        $select_fields = 't.id_log_aksi,t.user,t.kategori,t.aksi,t.created_at,t.deleted_at';
        $reviewed = $req->getVar('reviewed') ?? 0;
        $is_reviewed = $reviewed == 1;
        $status = $req->getVar('status') ?? '';
        $merchant = $req->getVar('merchant') ?? '';
        $date = $req->getVar('date') ?? '';
        // var_dump($status);
        // die;
        if (!empty($date)) {
            $dates = explode(' / ', $date);
            if (count($dates) == 2) {
                $start = $dates[0];
                $end = $dates[1];
                $this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
                $this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
            }
        }
        $where = ['t.deleted_at' => null];


        // add select and where query to builder
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


                $r = [];
                $r[] = $i;
                $r[] = $row->created_at;
                $r[] = $row->user;
                $r[] = $row->aksi;
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
}
