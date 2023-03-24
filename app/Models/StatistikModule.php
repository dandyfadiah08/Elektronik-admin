<?php

namespace App\Models;

use CodeIgniter\Model;

class StatistikModule extends Model
{
    public function getAllDataStatistik($id_mitra, $start_date, $end_date)
    {
        $database = db_connect("default");
        $database2 = db_connect("others");
        $start = date("Y-m-d h:i:s ", strtotime($start_date));
        $end = date("Y-m-d h:i:s ", strtotime($end_date));
        $sql2 = "SELECT no,status,harga_akhir,kategori,date_save FROM data_kuisionernew  where date_save between '" . $start . "' and '" . $end . "' order by no desc";
        $data  = $database->query($sql2)->getResultArray();
        return $data;
    }
    public function getmitra()
    {
        $database2 = db_connect("others");
        $query = "SELECT id_mitra,nama_mitra FROM master_mitra  ";
        $datas = $database2->query($query)->getResultArray();
        return $datas;
    }
    public function getDatareset($id_mitra, $range)
    {
        if ($id_mitra != 'ALL') {
            $query = "SELECT no,status,harga_akhir,kategori,date_save FROM data_kuisionernew  WHERE id_mitra='$id_mitra'  ";
            $datas = $this->db->query($query)->getResultArray();
            return $datas;
        }
        if ($id_mitra == 'ALL') {
            $database = db_connect("default");
            $database2 = db_connect("others");
            $sql2 = "SELECT no,status,harga_akhir,kategori,date_save FROM data_kuisionernew";
            $data  = $database->query($sql2)->getResultArray();;
            return $data;
        }
    }
    public function getkategoriAll()
    {
        $query = "SELECT id_kategori,nama_kategori FROM master_kategori  ";
        $datas =  $this->db->query($query)->getResultArray();
        return $datas;
    }
}
