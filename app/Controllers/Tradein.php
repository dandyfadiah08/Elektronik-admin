<?php

namespace App\Controllers;

use App\Models\TradeinModule;
use App\Models\LogModule;
use App\Models\MasterAdminModule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use mysqli;

class Tradein extends BaseController
{

    public function index()
    {
        $date = strtotime("tomorrow");
        $end_date = date("m/d/Y h:i a", $date);
        $date2 = strtotime("-1 Months");
        $start_date = date("m/d/Y h:i a", $date2);
        $this->data += [
            'page' => (object)[
                'key' => '2-Tradein',
                'title' => 'Data Tradein',
                'subtitle' => 'Data Tradein',
                'navbar' => 'Data Tradein',
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
        ];
        $TestModel = new TradeinModule;
        $datatamp2 = [];
        // $datas = $this->TradeinModule->getAllData();
        $datatamp   = $TestModel->getAllData();
        $i = 0;
        $datastatus = '';
        foreach ($datatamp as $value) {
            $date = date('d-m-Y', strtotime($value['date_save']));
            $hargaAkhir = $value['harga_akhir'] + $value['subsidi'];
            if ($value["status"] == 0) {
                $status = "Gagal";
            } else {
                $status = "Berhasil";
            }
            $datatamp[] = [
                'primary' => $value['no'],
                'tanggal' => $date,
                'status' => $status,
                'toko' => $value['nama_te'],
                'kode_tradein' => $value['kode_tradein'],
                'merk' => $value['merk'],
                'spec' => $value['spec'],
                'size' => $value['size'],
                'tahun' => $value['tahun'],
                'sn' => $value['sn'],
                'harga_akhir' => $value['harga_akhir'],
                'harga_subsidi' => $value['subsidi'],
                'harga_tradein' => $hargaAkhir

            ];
        }
        // print_r($datatamp[0]);
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'tradein';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view(
                'tradein/index',
                $this->data
            );
        }
    }
    public function cekTradein($key)
    {
        $TestModel = new TradeinModule;
        $category = '';
        $kode_registrasi = '';
        $id_pameran = '';
        $kode_model = '';
        $notes = '';
        $id_mitra = '';
        $kode_kuis = '';
        // $datas = $this->TradeinModule->getAllData();
        $datatamp   = $TestModel->getAllDatanew();
        foreach ($datatamp as $value) {
            if ($value['list_kuisioner'] == $key) {
                $category = $value['kategori'];
                $id_mitra = $value['id_mitra'];
                $notes = $value['notes'];
                $kode_kuis = $value['kode_kuis'];
                $kode_model = $value['kode_model'];
                $kode_registrasi = $value['kode_register'];
                if ($value['id_pameran'] != Null) {
                    $id_pameran = $value['id_pameran'];
                }
            }
        }
        $pameran = '';
        $datanewphone = '';
        $datastore2 = '';
        $id_jeniskuisioner = [];
        $datacategory = $TestModel->cekDatakategori($key);
        $datamaster = $TestModel->cekDatamasterkuisioner();
        foreach ($datacategory as $key => $value) {
            array_push($id_jeniskuisioner, $value['id_mkuisioner']);
        }
        $count_jeniskuisioner = array_count_values($id_jeniskuisioner);


        $arraymaster = [];
        foreach ($datamaster as $keydata => $data1) {
            foreach ($count_jeniskuisioner as $key => $data2) {
                if ($key == $data1['id_mkuisioner']) {
                    $arraylis = [];
                    foreach ($datacategory as $keydatakategori => $data3) {
                        if ($data1['id_mkuisioner'] == $data3['id_mkuisioner']) {
                            array_push($arraylis, $data3);
                        }
                    }
                    array_push($arraymaster, ["kuisioner" => $data1['kuisioner'], 'list_kuisioner' => $arraylis]);
                }
            }
        }
        $tradeinData   = $TestModel->getDatatradein($kode_registrasi);
        $tradeinPameran   = $TestModel->getDatapameran($id_pameran);
        $tradeinPameranModel   = $TestModel->tradeinPameranModel($kode_model);
        $datatradein2 = array(
            'date_save' => $tradeinData[0]['date_save'],
            'kode_tradein' => 'E-' . ' ' . $tradeinData[0]['kode_tradein'],
            'toko' => $tradeinData[0]['nama_te'],
            'sn' =>  $tradeinData[0]['sn'],
            'kategori' => $tradeinData[0]['kategori'],
            'merk_tahun' => $tradeinData[0]['merk'] . '/' .  $tradeinData[0]['tahun'],
            'spec_size' => $tradeinData[0]['spec'] . '/' .  $tradeinData[0]['size'],
            'harga_subsidi' => 'Rp ' . $tradeinData[0]['harga'] . ' + ' . 'Rp ' .  $tradeinData[0]['subsidi'],
            'harga_total' =>  'Rp ' . $tradeinData[0]['harga_total'],
            'alamat' =>  'Rp ' . $tradeinData[0]['alamat'],
            'device_checker' => $tradeinData[0]['device_checker'],
            'no_telp' => $tradeinData[0]['no_telp'],
            'nama_pameran' => $tradeinPameran[0]['nama_pameran'],
            'kode_product' => $tradeinPameranModel[0]['kode'],
        );
        $this->data += [
            'page' => (object)[
                'key' => '2-Tradein',
                'title' => 'Data Tradein',
                'subtitle' => 'Data Tradein',
                'navbar' => 'Data Tradein',
                'kuisioner' => $arraymaster,
                'tradein' => $datatradein2,
                'notes' => $notes,
                'kode_kuis' => $kode_kuis,
            ],
        ];
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'tradein';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('tradein/cek', $this->data);
        }
        // var_dump($this->data['page']->datatamp2[0]['kode_tradein']);
        // die;

    }
    function load_data()
    {

        $fields_order = array(
            null,
            "t.date_save",
            "t2.name",
            "t.id_mitra",
            "t1.kode_tradein",
            "t.kategori",

        );
        // fields to search with
        $fields_search = array(
            "t.no",
            "t.date_save",
            "t.nama_te",
            "t1.kode_tradein",
            "t.kategori",
            "t.merk",
            "t.spec",
            "t.size",
            "t.tahun",
            "t.sn",
            "t.id_mitra",
            "t.harga_akhir",
            't1.subsidi'


        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $listrange = $this->request->getPost('date');
        $status = $this->request->getPost('status');
        $startdate = explode("-", $listrange);

        $this->db = \Config\Database::connect();
        $this->table_name = 'data_kuisionernew';
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];
        //daterange
        if ($listrange != '') {
            if ($status == 'All') {
                $starttime = strtotime($startdate[0]);
                $startformat = date('Y-m-d', $starttime);
                $endtime = strtotime($startdate[1]);
                $endformat = date('Y-m-d', $endtime);
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->join("user_toko as t2", "t2.id_user_toko=t.id_user", "left")
                    ->where('t.date_save >=', $startformat)
                    ->where('t.date_save <=', $endformat)
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
            if ($status == 0) {
                $starttime = strtotime($startdate[0]);
                $startformat = date('Y-m-d', $starttime);
                $endtime = strtotime($startdate[1]);
                $endformat = date('Y-m-d', $endtime);
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->join("user_toko as t2", "t2.id_user_toko=t.id_user", "left")
                    ->where('t.date_save >=', $startformat)
                    ->where('t.date_save <=', $endformat)
                    ->where('status', $status)
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
            if ($status == 1) {
                $starttime = strtotime($startdate[0]);
                $startformat = date('Y-m-d', $starttime);
                $endtime = strtotime($startdate[1]);
                $endformat = date('Y-m-d', $endtime);
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->join("user_toko as t2", "t2.id_user_toko=t.id_user", "left")
                    ->where('t.date_save >=', $startformat)
                    ->where('t.date_save <=', $endformat)
                    ->where('status', $status)
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
        } else {
            if ($status == 'All') {
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
            if ($status == 0) {
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->where('status', 0)
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
            if ($status == 1) {
                $this->builder = $this->db
                    ->table("$this->table_name as t")
                    ->join("data_tukar as t1", "t1.kode_register=t.kode_register", "left")
                    ->where('status', 1)
                    ->orderBy(1, 'desc')
                    ->groupBy('t.kode_kuis');
            }
        }

        // var_dump($sortColumn);
        // die;


        // select fields

        // building where query
        $select_fields = 't.no,t.date_save,t.status,t.nama_te,t1.kode_tradein,t.kategori,t.merk,t.spec,t.size,t.tahun,t.sn,t.harga_akhir,t1.subsidi,t.list_kuisioner,t.id_mitra';
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
                $detail_status = '';
                if ($row->status == 0) {
                    $detail_status = 'Gagal';
                }
                if ($row->status == 1) {
                    $detail_status = 'Berhasil';
                }
                $hargasub = '';
                if ($row->subsidi == 0) {
                    $hargasub = 0;
                }
                if ($row->subsidi != 0) {
                    $hargasub = $row->subsidi;
                }


                $hargaakhir = $row->harga_akhir + $hargasub;

                $data_cek = '<form method="" action="' . $url->detail . $row->list_kuisioner . '">' . '<button class="btn btn-warning btn-sm mb-1 tombolcek" type="submit" ?????>' . '<i class="fas fa-search"></i>
                </button>' . '</form>';
                $tgl = date("Y-m-d", strtotime($row->date_save));
                $r = [];
                $r[] = $i;
                $r[] = $tgl;
                $r[] = $detail_status;
                $r[] =   $row->id_mitra . "/" . $row->nama_te;
                $r[] = 'E-' . $row->kode_tradein . '/' . '<br>' . $row->sn;
                $r[] = $row->kategori . '<br>' . $row->merk . '/' . $row->spec . '/' . $row->size . '/' . $row->tahun;
                $r[] = '<span style="float:left;"> Rp. </span><span style="text-align:right">' . number_format($row->harga_akhir, 0, ",", ".") . '</span>';
                $r[] = '<span style="float:left;"> Rp. </span><span style="text-align:right">' . number_format($hargasub, 0, ",", ".") . '</span>';
                $r[] = '<span style="float:left;"> Rp. </span><span style="text-align:right">' . number_format($hargaakhir, 0, ",", ".") . '</span>';
                $r[] = $data_cek;
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
    function notescek()
    {
        $TestModel = new TradeinModule;
        $LogModel = new LogModule;
        $id = $this->request->getPost('no');
        $listnotes = $this->request->getPost('notes');
        $datatamp   = $TestModel->notescek($id, $listnotes);
    }
    public function exporttradein()
    {
        $TestModel = new TradeinModule;

        $data = [
            'datatradein' =>   $TestModel->getAllData()
        ];
        //print_r($data);
        echo view('tradein/cetakData', [
            'data' => $data
        ]);
    }
    function exportDataTradein($status)
    {
        $spreadsheet = new Spreadsheet();
        $TestModel = new TradeinModule;
        $datatamp   = $TestModel->getStatus($status);
        $folder = '';
        $namafile = "TradeIn-All-ELektronik" . date("Ymd-His") . ".xlsx";
        $namafile = str_replace(' ', '', $namafile);
        $spreadsheet->getProperties()
            ->setTitle('ENB Mobile Care')
            ->setSubject('ENB Mobile Care')
            ->setDescription('Harga HP Web Admin ')
            ->setCreator('ENB Mobile Care')
            ->setLastModifiedBy('ENB Mobile Care');
        $downloadfile = $downloadfile = $folder . $namafile;
        $total_data = count($datatamp);
        if ($total_data > 0) {
            //Adding data to the excel sheet
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'NO')
                ->setCellValue('B1', 'Tanggal Transaksi')
                ->setCellValue('C1', 'Status')
                ->setCellValue('D1', 'Kode Tradein')
                ->setCellValue('E1', 'Kategori')
                ->setCellValue('F1', 'Merk')
                ->setCellValue('G1', 'Spek')
                ->setCellValue('H1', 'Size')
                ->setCellValue('I1', 'Tahun')
                ->setCellValue('J1', 'Serial Number')
                ->setCellValue('K1', 'Harga Device')
                ->setCellValue('L1', 'Subsidi')
                ->setCellValue('M1', 'Harga Total')
                ->setCellValue('N1', 'Kode Toko')
                ->setCellValue('O1', 'Region')
                ->setCellValue('P1', 'Mitra')
                ->setCellValue('Q1', 'Pertanyaan 1')
                ->setCellValue('R1', 'Pertanyaan 2')
                ->setCellValue('S1', 'Pertanyaan 3')
                ->setCellValue('T1', 'Pertanyaan 4')
                ->setCellValue('U1', 'Pertanyaan 5')
                ->setCellValue('V1', 'Pertanyaan 6')
                ->setCellValue('W1', 'Pertanyaan 7')
                ->setCellValue('X1', 'Pertanyaan 8')
                ->setCellValue('Y1', 'Pertanyaan 9')
                ->setCellValue('Z1', 'Pertanyaan 10');
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(15.42);
            $i = 1;

            foreach ($datatamp as $row) {
                $i++;
                $tgl_created = strtotime($row['date_save']);
                $tgl_created2 = date("d/m/Y", $tgl_created);
                if ($row["status"] == 0) {
                    $status = "Gagal";
                } else {
                    $status = "Selesai";
                }
                $kode_tradein = 'E-' . $row["kode_tradein"];
                if (intval($row["kode_tradein"]) > 90000000) $kode_tradein = 'M-' . $row["kode_tradein"];
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $i - 1)
                    ->setCellValue('B' . $i, $tgl_created2)
                    ->setCellValue('C' . $i, $status)
                    ->setCellValue('D' . $i, $kode_tradein)
                    ->setCellValue('D' . $i, $row['kategori'])
                    ->setCellValue('F' . $i, $row['merk'])
                    ->setCellValue('G' . $i, $row['spec'])
                    ->setCellValue('H' . $i, $row['size'])
                    ->setCellValue('I' . $i, $row['tahun'])
                    ->setCellValue('J' . $i, $row['sn'])
                    ->setCellValue('K' . $i, $row['harga_akhir'])
                    ->setCellValue('L' . $i, $row['subsidi'])
                    ->setCellValue('M' . $i, $row['harga_total'])
                    ->setCellValue('N' . $i, $row['kode_toko'])
                    ->setCellValue('O' . $i, $row['region'])
                    ->setCellValue('P' . $i, $row['mitra'])
                    ->setCellValue('Q' . $i, $row['que_1'])
                    ->setCellValue('R' . $i, $row['que_2'])
                    ->setCellValue('S' . $i, $row['que_3'])
                    ->setCellValue('T' . $i, $row['que_4'])
                    ->setCellValue('U' . $i, $row['que_5'])
                    ->setCellValue('V' . $i, $row['que_6'])
                    ->setCellValue('W' . $i, $row['que_7'])
                    ->setCellValue('X' . $i, $row['que_8'])
                    ->setCellValue('Y' . $i, $row['que_9'])
                    ->setCellValue('Z' . $i, $row['que_10']);
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
        foreach ($datatamp as $row) {

            $detail_status = '';
            if ($row['status'] == 0) {
                $detail_status = 'Gagal';
            }
            if ($row['status'] == 1) {
                $detail_status = 'Berhasil';
            }
            $hargasub = '';
            if ($row['subsidi'] == 0) {
                $hargasub = 0;
            }
            if ($row['subsidi'] != 0) {
                $hargasub = $row['subsidi'];
            }


            $hargaakhir = $row['harga_akhir'] + $hargasub;
        }
    }
    function exportDataTradeinDate($date, $status)
    {
        $spreadsheet = new Spreadsheet();
        $TestModel = new TradeinModule;
        $datatamp   = $TestModel->getDateStatus($date, $status);

        $folder = '';
        $namafile = "TradeIn-All-ELektronik" . date("Ymd-His") . ".xlsx";
        $namafile = str_replace(' ', '', $namafile);
        $spreadsheet->getProperties()
            ->setTitle('ENB Mobile Care')
            ->setSubject('ENB Mobile Care')
            ->setDescription('Harga HP Web Admin ')
            ->setCreator('ENB Mobile Care')
            ->setLastModifiedBy('ENB Mobile Care');
        $downloadfile = $downloadfile = $folder . $namafile;
        $total_data = count($datatamp);
        if ($total_data > 0) {
            //Adding data to the excel sheet
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'NO')
                ->setCellValue('B1', 'Tanggal Transaksi')
                ->setCellValue('C1', 'Status')
                ->setCellValue('D1', 'Kode Tradein')
                ->setCellValue('E1', 'Kategori')
                ->setCellValue('F1', 'Merk')
                ->setCellValue('G1', 'Spek')
                ->setCellValue('H1', 'Size')
                ->setCellValue('I1', 'Tahun')
                ->setCellValue('J1', 'Serial Number')
                ->setCellValue('K1', 'Harga Device')
                ->setCellValue('L1', 'Subsidi')
                ->setCellValue('M1', 'Harga Total')
                ->setCellValue('N1', 'Nama Toko')
                ->setCellValue('O1', 'Region')
                ->setCellValue('P1', 'Mitra')
                ->setCellValue('Q1', 'Kuisioner')
                ->setCellValue('R1', 'List Kuisioner');

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(60.60);
            $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(20.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(15.42);
            $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(15.42);
            $i = 1;
            $flex = 1;
            foreach ($datatamp as $row) {
                $i++;
                $flex++;
                $tgl_created = strtotime($row['date_save']);
                $tgl_created2 = date("d/m/Y", $tgl_created);
                if ($row["status"] == 0) {
                    $status = "Gagal";
                } else {
                    $status = "Selesai";
                }
                $kode_tradein = 'E-' . $row["kode_tradein"];
                if (intval($row["kode_tradein"]) > 90000000) $kode_tradein = 'M-' . $row["kode_tradein"];
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $flex - 1)
                    ->setCellValue('B' . $i, $tgl_created2)
                    ->setCellValue('C' . $i, $status)
                    ->setCellValue('D' . $i, $kode_tradein)
                    ->setCellValue('E' . $i, $row['kategori'])
                    ->setCellValue('F' . $i, $row['merk'])
                    ->setCellValue('G' . $i, $row['spec'])
                    ->setCellValue('H' . $i, $row['size'])
                    ->setCellValue('I' . $i, $row['tahun'])
                    ->setCellValue('J' . $i, $row['sn'])
                    ->setCellValue('K' . $i, $row['harga'])
                    ->setCellValue('L' . $i, $row['subsidi'])
                    ->setCellValue('M' . $i, $row['harga_total'])
                    ->setCellValue('N' . $i, $row['nama_te'])
                    ->setCellValue('O' . $i, $row['alamat'])
                    ->setCellValue('P' . $i, $row['id_mitra']);

                $datalist   = $TestModel->cekDatakategori($row['list_kuisioner']);
                $id_jeniskuisioner = [];
                foreach ($datalist as $key => $value) {
                    array_push($id_jeniskuisioner, $value['id_mkuisioner']);
                }
                $count_jeniskuisioner = array_count_values($id_jeniskuisioner);
                $datamaster = $TestModel->cekDatamasterkuisioner();
                $arraymaster = [];
                $index = $i;
                $parg = $index;
                foreach ($datamaster as $keydata => $data1) {
                    foreach ($count_jeniskuisioner as $key => $data2) {
                        if ($key == $data1['id_mkuisioner']) {
                            $spreadsheet->setActiveSheetIndex(0)
                                ->setCellValue('Q' . $index, $data1['kuisioner']);
                            foreach ($datalist as $keydatakategori => $data3) {
                                if ($data1['id_mkuisioner'] == $data3['id_mkuisioner']) {
                                    $spreadsheet->setActiveSheetIndex(0)
                                        ->setCellValue('R' . $parg, $data3['list']);
                                    $parg++;
                                }
                            }
                            $index = $parg;
                        }
                    }
                }
                $i = $parg - 1;
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
        foreach ($datatamp as $row) {

            $detail_status = '';
            if ($row['status'] == 0) {
                $detail_status = 'Gagal';
            }
            if ($row['status'] == 1) {
                $detail_status = 'Berhasil';
            }
            $hargasub = '';
            if ($row['subsidi'] == 0) {
                $hargasub = 0;
            }
            if ($row['subsidi'] != 0) {
                $hargasub = $row['subsidi'];
            }


            $hargaakhir = $row['harga_akhir'] + $hargasub;
        }
    }
}
