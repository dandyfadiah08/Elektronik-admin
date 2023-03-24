<?php

namespace App\Controllers;

use App\Models\PotonganModule;
use App\Models\MasterAdminModule;
use App\Models\LogModule;
use mysqli;

class Potongan extends BaseController
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-potongan',
                'title' => 'Data Potongan',
                'subtitle' => 'Data Potongan',
                'navbar' => '',
            ],
        ];
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'potongan';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('potongan/index', $this->data);
        }
    }
    function load_data()
    {
        $this->data += [
            'page' => (object)[
                'key' => '2-potongan',
                'title' => 'Data Potongan',
                'subtitle' => 'Data Potongan',
                'navbar' => '',
            ],
        ];
        $fields_order = array(
            null,
            "t.id",
            "t.kategori",
            "t.que_1",
            "t.que_2",
            "t.que_3",
            "t.que_4",
            "t.que_5",
            "t.que_6",
            "t.que_7",
            "t.que_8",
            "t.que_9",
            "t.que_10",

        );
        // fields to search with
        $fields_search = array(
            "t.id",
            "t.kategori",
            "t.que_1",
            "t.que_2",
            "t.que_3",
            "t.que_4",
            "t.que_5",
            "t.que_6",
            "t.que_7",
            "t.que_8",
            "t.que_9",
            "t.que_10",


        );
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $this->db = \Config\Database::connect();
        $this->table_name = 'data_potong';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        // var_dump($sortColumn);
        // die;
        $this->builder = $this->db
            ->table("$this->table_name as t");

        // select fields

        // building where query
        $select_fields = 't.id,t.kategori,t.que_1,t.que_2,t.que_3,t.que_4,t.que_5,t.que_6,t.que_7,t.que_8,t.que_9,t.que_10,t.pertanyaan_1,t.pertanyaan_2,t.pertanyaan_3,t.pertanyaan_4,t.pertanyaan_5,t.pertanyaan_6,t.pertanyaan_7,t.pertanyaan_8,t.pertanyaan_9,t.pertanyaan_10';
        $reviewed = $req->getVar('reviewed') ?? 0;
        $is_reviewed = $reviewed == 1;
        $merchant = $req->getVar('merchant') ?? '';
        $date = $req->getVar('date') ?? '';
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

        if ($merchant != 'all' && !empty($merchant)) $where += ['t.merchant_id' => $merchant];

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
                $aksi = '
        <button href="#" data-toggle="modal" data-target="#editModal" class="btn btn-warning btn-sm mb-1 editModal" 
            data-id="' . $row->id . '"
            data-kategori="' . $row->kategori . '" 
            data-que_1="' . $row->que_1 . '" 
            data-que_2="' . $row->que_2 . '"
            data-que_3="' . $row->que_3 . '"
            data-que_4="' . $row->que_4 . '"
            data-que_5="' . $row->que_5 . '"
            data-que_6="' . $row->que_6 . '"
            data-que_7="' . $row->que_7 . '"
            data-que_8="' . $row->que_8 . '"
            data-que_9="' . $row->que_9 . '"
            data-que_10="' . $row->que_10 . '"
            data-pertanyaan1="' . $row->pertanyaan_1 . '"
            data-pertanyaan2="' . $row->pertanyaan_2 . '"
            data-pertanyaan3="' . $row->pertanyaan_3 . '"
            data-pertanyaan4="' . $row->pertanyaan_4 . '"
            data-pertanyaan5="' . $row->pertanyaan_5 . '"
            data-pertanyaan6="' . $row->pertanyaan_6 . '"
            data-pertanyaan7="' . $row->pertanyaan_7 . '"
            data-pertanyaan8="' . $row->pertanyaan_8 . '"
            data-pertanyaan9="' . $row->pertanyaan_9 . '"
            data-pertanyaan10="' . $row->pertanyaan_10 . '"
        >
            <i class="fas fa-edit"></i> EDIT
        </button>
        ';
                $r = [];
                $r[] = $i;
                $r[] = $row->kategori;
                $r[] = $row->que_1;
                $r[] =  $row->que_2;
                $r[] = $row->que_3;
                $r[] = $row->que_4;
                $r[] = $row->que_5;
                $r[] = $row->que_6;
                $r[] = $row->que_7;
                $r[] = $row->que_8;
                $r[] = $row->que_9;
                $r[] = $row->que_10;
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
        $coba = 'lkfjlkfj';
        $json_data = array(
            "draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
        );
        return  $this->respond($json_data);
    }
    function editPotongan()
    {
        $response = initResponse();
        $hasError = false;
        $id = $this->request->getPost('id');
        $kategori = $this->request->getPost('kategori');
        $que_1 = $this->request->getPost('que_1');
        $que_2 = $this->request->getPost('que_2');
        $que_3 = $this->request->getPost('que_3');
        $que_4 = $this->request->getPost('que_4');
        $que_5 = $this->request->getPost('que_5');
        $que_6 = $this->request->getPost('que_6');
        $que_7 = $this->request->getPost('que_7');
        $que_8 = $this->request->getPost('que_8');
        $que_9 = $this->request->getPost('que_9');
        $que_10 = $this->request->getPost('que_10');
        $Pertanyaan1 = $this->request->getPost('Pertanyaan1');
        $Pertanyaan2 = $this->request->getPost('Pertanyaan2');
        $Pertanyaan3 = $this->request->getPost('Pertanyaan3');
        $Pertanyaan4 = $this->request->getPost('Pertanyaan4');
        $Pertanyaan5 = $this->request->getPost('Pertanyaan5');
        $Pertanyaan6 = $this->request->getPost('Pertanyaan6');
        $Pertanyaan7 = $this->request->getPost('Pertanyaan7');
        $Pertanyaan8 = $this->request->getPost('Pertanyaan8');
        $Pertanyaan9 = $this->request->getPost('Pertanyaan9');
        $Pertanyaan10 = $this->request->getPost('Pertanyaan10');
        $data_potong = [];
        $data_potong['id'] = $id;
        $data_potong['kategori'] = $kategori;
        $data_potong['que_1'] = $que_1;
        $data_potong['que_2'] = $que_2;
        $data_potong['que_3'] = $que_3;
        $data_potong['que_4'] = $que_4;
        $data_potong['que_5'] = $que_5;
        $data_potong['que_6'] = $que_6;
        $data_potong['que_7'] = $que_7;
        $data_potong['que_8'] = $que_8;
        $data_potong['que_9'] = $que_9;
        $data_potong['que_10'] = $que_10;
        $data_potong['Pertanyaan1'] = $Pertanyaan1;
        $data_potong['Pertanyaan2'] = $Pertanyaan2;
        $data_potong['Pertanyaan3'] = $Pertanyaan3;
        $data_potong['Pertanyaan4'] = $Pertanyaan4;
        $data_potong['Pertanyaan5'] = $Pertanyaan5;
        $data_potong['Pertanyaan6'] = $Pertanyaan6;
        $data_potong['Pertanyaan7'] = $Pertanyaan7;
        $data_potong['Pertanyaan8'] = $Pertanyaan8;
        $data_potong['Pertanyaan9'] = $Pertanyaan9;
        $data_potong['Pertanyaan10'] = $Pertanyaan10;
        $LogModel = new LogModule;
        $this->db->transStart();
        $this->Potongan->update($id, $data_potong);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE || $hasError) {
            if (!$hasError) $response = "Faield" / json_encode($this->db->error());
        } else {
            $date = strtotime("now");
            $waktu = date("Y-m-d h:i:s ", $date);
            $nama_user = $this->data['admin']->username;
            $in = array(
                'user' => $nama_user,
                'id' => 32,
                'ket' => 'Kategori: ' . $kategori . '',
                'Pertanyaan1' => 'Pertanyaan 1: ' . $Pertanyaan1 . '',
                'que_1' =>  'Presentasi Harga 1:' . $que_1 . '',
                'Pertanyaan2' => 'Pertanyaan 2: ' . $Pertanyaan2 . '',
                'que_2' =>  'Presentasi Harga 2:' . $que_2 . '',
                'Pertanyaan3' => 'Pertanyaan 3: ' . $Pertanyaan3 . '',
                'que_3' =>  'Presentasi Harga 3:' . $que_3 . '',
                'Pertanyaan4' => 'Pertanyaan 4: ' . $Pertanyaan4 . '',
                'que_4' =>  'Presentasi Harga 4:' . $que_4 . '',
                'Pertanyaan5' => 'Pertanyaan 5: ' . $Pertanyaan5 . '',
                'que_5' =>  'Presentasi Harga 5:' . $que_5 . '',
                'Pertanyaan6' => 'Pertanyaan 6: ' . $Pertanyaan6 . '',
                'que_6' =>  'Presentasi Harga 6:' . $que_6 . '',
                'Pertanyaan7' => 'Pertanyaan 7: ' . $Pertanyaan7 . '',
                'que_7' =>  'Presentasi Harga 7:' . $que_7 . '',
                'Pertanyaan8' => 'Pertanyaan 8: ' . $Pertanyaan8 . '',
                'que_8' =>  'Presentasi Harga 8:' . $que_8 . '',
                'Pertanyaan9' => 'Pertanyaan 9: ' . $Pertanyaan9 . '',
                'que_9' =>  'Presentasi Harga 9:' . $que_9 . '',
                'Pertanyaan10' => 'Pertanyaan 10: ' . $Pertanyaan10 . '',
                'que_10' =>  'Presentasi Harga 10:' . $que_10 . '',
            );
            $datatamp   = $LogModel->Log_aksi($in);

            $response->success = TRUE;
            $response->message = "Sukses";
            $response->data = $data_potong;
            json_encode($data_potong);
        }
        return $this->respond($response);
    }
}
