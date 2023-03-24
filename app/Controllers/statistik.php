<?php

namespace App\Controllers;

use App\Models\StatistikModule;
use App\Models\TradeinModule;
use App\Models\MasterAdminModule;

use mysqli;

class Statistik extends BaseController
{
    public function loadstatistik()
    {

        $statistikaModel = new StatistikModule;
        $datamitra   = $statistikaModel->getmitra();
        $listmitra = $this->request->getPost('id_mitra');
        $listrange = $this->request->getPost('range');
        $date = strtotime("tomorrow");
        $end_date = date("m/d/Y h:i a", $date);
        $date2 = strtotime("-1 Months");
        $start_date = date("m/d/Y h:i a", $date2);
        $id_mitra = '';
        $datastatistik   = $statistikaModel->getAllDataStatistik($id_mitra, $start_date, $end_date);
        // var_dump($datastatistik);
        // die;
        $berhasilMAcbook = 0;
        $berhasilTAB = 0;
        $berhasilEarbuds = 0;
        $berhasilGAMECONSOLE = 0;
        $gagalMAcbook = 0;
        $gagalTAB = 0;
        $gagalEaebuds = 0;
        $presentasiConsole = 0;
        $gagalGAMECONSOLE = 0;
        $presentasiTab = 0;
        $hargaMac = 0;
        $hargaTab = 0;
        $presentasiEarbuds = 0;
        $hargaEarbuds = 0;
        $presentasiMac = 0;
        $hargaConsole = 0;
        foreach ($datastatistik as $value) {
            if ($value['kategori'] == 'MACBOOK') {
                if ($value['status'] == 1) {
                    $berhasilMAcbook++;
                }
                if ($value['status'] == 0) {
                    $gagalMAcbook++;
                }
                $hargaMac = $hargaMac + $value['harga_akhir'];
            }
            if ($value['kategori'] == 'TAB WIFI ONLY') {
                if ($value['status'] == 1) {
                    $berhasilTAB;
                }
                if ($value['status'] == 0) {
                    $gagalTAB++;
                }
                $hargaTab = $hargaTab + $value['harga_akhir'];
            }
            if ($value['kategori'] == 'EARBUDS') {
                if ($value['status'] == 1) {
                    $berhasilEarbuds++;
                }
                if ($value['status'] == 0) {
                    $gagalEaebuds++;
                }
                $hargaEarbuds = $hargaEarbuds + $value['harga_akhir'];
            }
            if ($value['kategori'] == 'GAME CONSOLE') {
                if ($value['status'] == 1) {
                    $berhasilGAMECONSOLE++;
                }
                if ($value['status'] == 0) {
                    $gagalGAMECONSOLE++;
                }
                $hargaConsole = $hargaConsole + $value['harga_akhir'];
            }
        }
        if ($berhasilMAcbook != 0) {

            $presentasiMac = ($berhasilMAcbook / ($gagalMAcbook + $berhasilMAcbook)) * 100;
        }
        if ($berhasilTAB != 0) {

            $presentasiTab = ($berhasilTAB / ($gagalTAB + $berhasilTAB)) * 100;
        }
        if ($berhasilEarbuds != 0) {

            $presentasiEarbuds = ($berhasilEarbuds / ($gagalEaebuds + $berhasilEarbuds)) * 100;
        }
        if ($berhasilGAMECONSOLE != 0) {
            $presentasiConsole = ($berhasilGAMECONSOLE / ($gagalGAMECONSOLE + $berhasilGAMECONSOLE)) * 100;
        }
        $MACBOOK = array(
            'Data' => array(
                'berhasi' => $berhasilMAcbook,
                'gagal' => $gagalMAcbook,
                'presentasiberhasil' =>  $presentasiMac,
                'jumlahharga' =>  $hargaMac,
            ),

        );
        $TAB_WIFI_ONLY = array(
            'Data' => array(
                'berhasi' => $berhasilTAB,
                'gagal' => $gagalTAB,
                'presentasiberhasil' =>  $presentasiTab,
                'jumlahharga' =>  $hargaTab,
            ),

        );
        $EARBUDS = array(
            'Data' => array(
                'berhasi' => $berhasilEarbuds,
                'gagal' => $gagalEaebuds,
                'presentasiberhasil' =>  $presentasiEarbuds,
                'jumlahharga' =>  $hargaEarbuds,
            ),

        );
        $GAME_CONSOLE = array(
            'Data' => array(
                'berhasi' => $berhasilGAMECONSOLE,
                'gagal' => $gagalGAMECONSOLE,
                'presentasiberhasil' =>  $presentasiConsole,
                'jumlahharga' =>  $hargaConsole,
            ),

        );
        $berhasil = array(
            'MACBOOK' => $MACBOOK,
            'TAB WIFI ONLY' => $TAB_WIFI_ONLY,
            'EARBUDS' => $EARBUDS,
            'GAME CONSOLE' => $GAME_CONSOLE,
        );
        // dd($berhasil);
        $jumlah = 0;
        $this->data += [
            'page' => (object)[
                'key' => '2-Statistik',
                'title' => 'Data Statistik',
                'subtitle' => 'Data Statistik',
                'navbar' => '',
                'datamitra' => $datamitra,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
        ];
        // dd($this->data);
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'statistik';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('statistik/index', $this->data);
        }
    }
    function load_data()
    {

        $fields_order = array(
            null,
            "t.id_kategori",
            "t.nama_kategori",
            "t.deskripsi",
            "t.date_save"

        );
        // fields to search with
        $fields_search = array(
            "t.id_kategori",
            "t.nama_kategori",
            "t.deskripsi",
            "t.date_save"


        );
        $TestModel = new TradeinModule;
        ini_set('memory_limit', '-1');
        $req = $this->request;
        $listrange = $this->request->getPost('date');
        $status = $this->request->getPost('status');
        $startdate = explode("-", $listrange);

        $this->db = \Config\Database::connect();
        $this->table_name = 'master_kategori';
        $this->builder = $this->db
            ->table("$this->table_name as t")
            ->orderBy(1, 'desc');
        $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 1;
        $sortColumn = $fields_order[$sortColumn - 1];


        // select fields

        // building where query
        $select_fields = 't.id_kategori,t.nama_kategori,t.deskripsi,t.date_save';
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
            $berhasil = 0;
            $gagal = 0;
            $presentasi = 0;
            $jumlah = 0;
            $indexpersen = 100;
            $i = 0;
            foreach ($dataResult as $row) {
                $i++;
                if ($status == 'ALL') {
                    if ($listrange == '') {
                        $filedberhasi = 1;
                        $filedgagal = 0;
                        $berhasil = $TestModel->getberhasil($row->nama_kategori, $filedberhasi);
                        $gagal = $TestModel->getgagal($row->nama_kategori, $filedgagal);
                        $jumlah = $TestModel->gettotal($row->nama_kategori);
                        if ($jumlah == 0 && $berhasil == 0) {
                            $presentasi = 0;
                        } else {

                            $presentasi = ($berhasil / $jumlah) * 100;
                        }
                    } else {
                        $startdate = explode("-", $listrange);
                        $starttime = strtotime($startdate[0]);
                        $startformat = date('Y-m-d', $starttime);
                        $endtime = strtotime($startdate[1]);
                        $endformat = date('Y-m-d', $endtime);
                        $filedberhasi = 1;
                        $filedgagal = 0;
                        $berhasil = $TestModel->getberhasildate($row->nama_kategori, $filedberhasi, $startformat, $endformat);
                        $gagal = $TestModel->getgagaldate($row->nama_kategori, $filedgagal, $startformat, $endformat);
                        $jumlah = $TestModel->gettotaldate($row->nama_kategori, $startformat, $endformat);
                        if ($jumlah == 0 && $berhasil == 0) {
                            $presentasi = 0;
                        } else {
                            $presentasi = ($berhasil / $jumlah) * 100;
                        }
                    }
                } else {
                    $berhasil = 0;
                    $jumlah = 0;
                    $gagal = 0;

                    $presentasi = 0;
                    if ($listrange == '') {
                        $dataStatus = $TestModel->getmitrapameran($row->nama_kategori);
                        $countStatus = count($dataStatus);
                        if ($countStatus > 0) {
                            $arrayid_pameran = [];
                            $arrayberhasil = [];
                            $arraygagal = [];
                            foreach ($dataStatus as $key => $value) {
                                array_push($arrayid_pameran, $value['id_pameran']);
                                if ($value['status'] == 1) {
                                    array_push($arrayberhasil, $value['id_pameran']);
                                }
                                if ($value['status'] == 0) {
                                    array_push($arraygagal, $value['id_pameran']);
                                }
                            }
                            //total
                            $count_pameran = array_count_values($arrayid_pameran);
                            $masterpameran = [];
                            foreach ($count_pameran as $key => $value) {
                                array_push($masterpameran, $key);
                            }
                            foreach ($masterpameran as $key => $value) {
                                $dataMitra = $TestModel->getmit($value, $status);
                                $countmitra = count($dataMitra);
                                if ($countmitra > 0) {
                                    $jumlah++;
                                }
                            }
                            //berhasil
                            $count_berhasil = array_count_values($arrayberhasil);
                            $jumlahberhasil = count($arrayberhasil);
                            if ($jumlahberhasil > 0) {
                                $masterberhasil = [];
                                foreach ($count_berhasil as $key => $value) {
                                    array_push($masterberhasil, $key);
                                }
                                foreach ($masterberhasil as $key => $value) {
                                    $dataMitraBerhasil = $TestModel->getmit($value, $status);
                                    $countberhasil = count($dataMitraBerhasil);
                                    if ($countberhasil > 0) {
                                        $berhasil++;
                                    }
                                }
                            }
                            //gagal
                            $count_gagal = array_count_values($arraygagal);
                            $jumlahgagal = count($arraygagal);
                            if ($jumlahgagal > 0) {
                                $mastergagal = [];
                                foreach ($count_gagal as $key => $value) {
                                    array_push($mastergagal, $key);
                                }
                                foreach ($mastergagal as $key => $value) {
                                    $dataMitraGagal = $TestModel->getmit($value, $status);
                                    $countgagal = count($dataMitraGagal);
                                    if ($countgagal > 0) {
                                        $gagal++;
                                    }
                                }
                            }
                            if ($jumlah == 0 && $berhasil == 0) {
                                $presentasi = 0;
                            } else {
                                $presentasi = ($berhasil / $jumlah) * 100;
                            }
                        }
                    } else {
                        $startdate = explode("-", $listrange);
                        $starttime = strtotime($startdate[0]);
                        $startformat = date('Y-m-d', $starttime);
                        $endtime = strtotime($startdate[1]);
                        $endformat = date('Y-m-d', $endtime);
                        $dataStatus = $TestModel->getmitrapamerandate($row->nama_kategori, $startformat, $endformat);
                        $countStatus = count($dataStatus);
                        if ($countStatus > 0) {
                            $arrayid_pameran = [];
                            $arrayberhasil = [];
                            $arraygagal = [];
                            foreach ($dataStatus as $key => $value) {
                                array_push($arrayid_pameran, $value['id_pameran']);
                                if ($value['status'] == 1) {
                                    array_push($arrayberhasil, $value['id_pameran']);
                                }
                                if ($value['status'] == 0) {
                                    array_push($arraygagal, $value['id_pameran']);
                                }
                            }
                            //total
                            $count_pameran = array_count_values($arrayid_pameran);
                            $masterpameran = [];
                            foreach ($count_pameran as $key => $value) {
                                array_push($masterpameran, $key);
                            }
                            foreach ($masterpameran as $key => $value) {
                                $dataMitra = $TestModel->getmit($value, $status);
                                $countmitra = count($dataMitra);
                                if ($countmitra > 0) {
                                    $jumlah++;
                                }
                            }
                            //berhasil
                            $count_berhasil = array_count_values($arrayberhasil);
                            $jumlahberhasil = count($arrayberhasil);
                            if ($jumlahberhasil > 0) {
                                $masterberhasil = [];
                                foreach ($count_berhasil as $key => $value) {
                                    array_push($masterberhasil, $key);
                                }
                                foreach ($masterberhasil as $key => $value) {
                                    $dataMitraBerhasil = $TestModel->getmit($value, $status);
                                    $countberhasil = count($dataMitraBerhasil);
                                    if ($countberhasil > 0) {
                                        $berhasil++;
                                    }
                                }
                            }
                            //gagal
                            $count_gagal = array_count_values($arraygagal);
                            $jumlahgagal = count($arraygagal);
                            if ($jumlahgagal > 0) {
                                $mastergagal = [];
                                foreach ($count_gagal as $key => $value) {
                                    array_push($mastergagal, $key);
                                }
                                foreach ($mastergagal as $key => $value) {
                                    $dataMitraGagal = $TestModel->getmit($value, $status);
                                    $countgagal = count($dataMitraGagal);
                                    if ($countgagal > 0) {
                                        $gagal++;
                                    }
                                }
                            }
                            if ($jumlah == 0 && $berhasil == 0) {
                                $presentasi = 0;
                            } else {
                                $presentasi = ($berhasil / $jumlah) * 100;
                            }
                        }
                    }
                }
                $r = [];
                $r[] = $i;
                $r[] = $row->nama_kategori;
                $r[] = $berhasil . '/' . $gagal;
                $r[] = $presentasi . '%';
                $r[] = $jumlah;
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
    function printData($status, $date)
    {
        $kategori = new StatistikModule;
        $tablekategori = [];
        $dataKategori   = $kategori->getkategoriAll();
        $i = 0;
        $berhasil = 0;
        $gagal = 0;
        $presentasi = 0;
        $jumlah = 0;
        $indexpersen = 100;
        $TestModel = new TradeinModule;
        foreach ($dataKategori as $key => $value) {
            $i++;

            if ($status == 'ALL') {
                $startdate = explode(" ", $date);
                $startstep1 = explode("-", $startdate[0]);
                $awalDate = $startstep1[2] . '/' . $startstep1[0] . '/' . $startstep1[1];
                $starttime = strtotime($awalDate);
                $startformat = date('Y-m-d', $starttime);
                $startstep2 = explode("-", $startdate[1]);
                $akhirDate = $startstep2[2] . '/' . $startstep2[0] . '/' . $startstep2[1];
                $endtime = strtotime($akhirDate);
                $endformat = date('Y-m-d', $endtime);
                $filedberhasi = 1;
                $filedgagal = 0;
                $berhasil = $TestModel->getberhasildate($value['nama_kategori'], $filedberhasi, $startformat, $endformat);
                $gagal = $TestModel->getgagaldate($value['nama_kategori'], $filedgagal, $startformat, $endformat);
                $jumlah = $TestModel->gettotaldate($value['nama_kategori'], $startformat, $endformat);
                if ($jumlah == 0 && $berhasil == 0) {
                    $presentasi = 0;
                } else {
                    $presentasi = ($berhasil / $jumlah) * 100;
                }
                array_push($tablekategori, ['no' => $i, 'nama_kategori' => $value['nama_kategori'], 'berhasil-gagal' => $berhasil . '/' . $gagal, 'presentasi' => $presentasi, 'jumlah' => $jumlah]);
            } else {
                $berhasil = 0;
                $jumlah = 0;
                $gagal = 0;
                $presentasi = 0;
                $startdate = explode(" ", $date);
                $startstep1 = explode("-", $startdate[0]);
                $awalDate = $startstep1[2] . '/' . $startstep1[0] . '/' . $startstep1[1];
                $starttime = strtotime($awalDate);
                $startformat = date('Y-m-d', $starttime);
                $startstep2 = explode("-", $startdate[1]);
                $akhirDate = $startstep2[2] . '/' . $startstep2[0] . '/' . $startstep2[1];
                $endtime = strtotime($akhirDate);
                $endformat = date('Y-m-d', $endtime);
                $dataStatus = $TestModel->getmitrapamerandate($value['nama_kategori'], $startformat, $endformat);
                $countStatus = count($dataStatus);
                if ($countStatus > 0) {
                    $arrayid_pameran = [];
                    $arrayberhasil = [];
                    $arraygagal = [];
                    foreach ($dataStatus as $key => $value8) {
                        array_push($arrayid_pameran, $value8['id_pameran']);
                        if ($value8['status'] == 1) {
                            array_push($arrayberhasil, $value8['id_pameran']);
                        }
                        if ($value8['status'] == 0) {
                            array_push($arraygagal, $value8['id_pameran']);
                        }
                    }
                    //total
                    $count_pameran = array_count_values($arrayid_pameran);
                    $masterpameran = [];
                    foreach ($count_pameran as $key => $value9) {
                        array_push($masterpameran, $key);
                    }
                    foreach ($masterpameran as $key => $value9) {
                        $dataMitra = $TestModel->getmit($value9, $status);
                        $countmitra = count($dataMitra);
                        if ($countmitra > 0) {
                            $jumlah++;
                        }
                    }
                    //berhasil
                    $count_berhasil = array_count_values($arrayberhasil);
                    $jumlahberhasil = count($arrayberhasil);
                    if ($jumlahberhasil > 0) {
                        $masterberhasil = [];
                        foreach ($count_berhasil as $key => $value10) {
                            array_push($masterberhasil, $key);
                        }
                        foreach ($masterberhasil as $key => $value11) {
                            $dataMitraBerhasil = $TestModel->getmit($value11, $status);
                            $countberhasil = count($dataMitraBerhasil);
                            if ($countberhasil > 0) {
                                $berhasil++;
                            }
                        }
                    }
                    //gagal
                    $count_gagal = array_count_values($arraygagal);
                    $jumlahgagal = count($arraygagal);
                    if ($jumlahgagal > 0) {
                        $mastergagal = [];
                        foreach ($count_gagal as $key => $value12) {
                            array_push($mastergagal, $key);
                        }
                        foreach ($mastergagal as $key => $value13) {
                            $dataMitraGagal = $TestModel->getmit($value13, $status);
                            $countgagal = count($dataMitraGagal);
                            if ($countgagal > 0) {
                                $gagal++;
                            }
                        }
                    }
                    if ($jumlah == 0 && $berhasil == 0) {
                        $presentasi = 0;
                    } else {
                        $presentasi = ($berhasil / $jumlah) * 100;
                    }
                }
                array_push($tablekategori, ['no' => $i, 'nama_kategori' => $value['nama_kategori'], 'berhasil-gagal' => $berhasil . '/' . $gagal, 'presentasi' => $presentasi, 'jumlah' => $jumlah]);
            }
        }
        $this->data += [
            'page' => (object)[
                'key' => '2-Statistik',
                'title' => 'Data Statistik',
                'subtitle' => 'Data Statistik',
                'navbar' => '',
                'data' => $tablekategori,
                'date' => $date,
            ],
        ];
        // dd($this->data);
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'statistik';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('statistik/printstatistik', $this->data);
        }
    }
    function printDatanotdate($status)
    {
        $kategori = new StatistikModule;
        $tablekategori = [];
        $dataKategori   = $kategori->getkategoriAll();
        $i = 0;
        $berhasil = 0;
        $gagal = 0;
        $presentasi = 0;
        $jumlah = 0;
        $indexpersen = 100;
        $TestModel = new TradeinModule;
        foreach ($dataKategori as $key => $value) {
            $i++;

            if ($status == 'ALL') {
                $filedberhasi = 1;
                $filedgagal = 0;
                $berhasil = $TestModel->getberhasil($value['nama_kategori'], $filedberhasi);
                $gagal = $TestModel->getgagal($value['nama_kategori'], $filedgagal);
                $jumlah = $TestModel->gettotal($value['nama_kategori']);
                if ($jumlah == 0 && $berhasil == 0) {
                    $presentasi = 0;
                } else {

                    $presentasi = ($berhasil / $jumlah) * 100;
                }
                array_push($tablekategori, ['no' => $i, 'nama_kategori' => $value['nama_kategori'], 'berhasil-gagal' => $berhasil . '/' . $gagal, 'presentasi' => $presentasi, 'jumlah' => $jumlah]);
            } else {

                $berhasil = 0;
                $jumlah = 0;
                $gagal = 0;
                $presentasi = 0;
                $dataStatus = $TestModel->getmitrapameran($value['nama_kategori']);
                $countStatus = count($dataStatus);
                if ($countStatus > 0) {
                    $arrayid_pameran = [];
                    $arrayberhasil = [];
                    $arraygagal = [];
                    foreach ($dataStatus as $key => $value1) {
                        array_push($arrayid_pameran, $value1['id_pameran']);
                        if ($value1['status'] == 1) {
                            array_push($arrayberhasil, $value1['id_pameran']);
                        }
                        if ($value1['status'] == 0) {
                            array_push($arraygagal, $value1['id_pameran']);
                        }
                    }
                    //total
                    $count_pameran = array_count_values($arrayid_pameran);
                    $masterpameran = [];
                    foreach ($count_pameran as $key => $value2) {
                        array_push($masterpameran, $key);
                    }
                    foreach ($masterpameran as $key => $value3) {
                        $dataMitra = $TestModel->getmit($value3, $status);
                        $countmitra = count($dataMitra);
                        if ($countmitra > 0) {
                            $jumlah++;
                        }
                    }
                    //berhasil
                    $count_berhasil = array_count_values($arrayberhasil);
                    $jumlahberhasil = count($arrayberhasil);
                    if ($jumlahberhasil > 0) {
                        $masterberhasil = [];
                        foreach ($count_berhasil as $key => $value4) {
                            array_push($masterberhasil, $key);
                        }
                        foreach ($masterberhasil as $key => $value5) {
                            $dataMitraBerhasil = $TestModel->getmit($value5, $status);
                            $countberhasil = count($dataMitraBerhasil);
                            if ($countberhasil > 0) {
                                $berhasil++;
                            }
                        }
                    }
                    //gagal
                    $count_gagal = array_count_values($arraygagal);
                    $jumlahgagal = count($arraygagal);
                    if ($jumlahgagal > 0) {
                        $mastergagal = [];
                        foreach ($count_gagal as $key => $value6) {
                            array_push($mastergagal, $key);
                        }
                        foreach ($mastergagal as $key => $value7) {
                            $dataMitraGagal = $TestModel->getmit($value7, $status);
                            $countgagal = count($dataMitraGagal);
                            if ($countgagal > 0) {
                                $gagal++;
                            }
                        }
                    }
                    if ($jumlah == 0 && $berhasil == 0) {
                        $presentasi = 0;
                    } else {
                        $presentasi = ($berhasil / $jumlah) * 100;
                    }
                }
                array_push($tablekategori, ['no' => $i, 'nama_kategori' => $value['nama_kategori'], 'berhasil-gagal' => $berhasil . '/' . $gagal, 'presentasi' => $presentasi, 'jumlah' => $jumlah]);
            }
        }
        $this->data += [
            'page' => (object)[
                'key' => '2-Statistik',
                'title' => 'Data Statistik',
                'subtitle' => 'Data Statistik',
                'navbar' => '',
                'data' => $tablekategori
            ],
        ];
        // dd($this->data);
        $MasterAdmin = new MasterAdminModule;
        $nama_role = 'statistik';
        $role = $MasterAdmin->roleadmin($this->data['admin']->id_role, $nama_role);
        if ($role == false) {
            return view(
                'notfound/index',
                $this->data
            );
        } else {
            return view('statistik/printstatistik', $this->data);
        }
    }
}
