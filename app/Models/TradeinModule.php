<?php

namespace App\Models;

use CodeIgniter\Model;

class TradeinModule extends Model
{

    // protected $table = 'data_kuisioner';
    // protected $primaryKey = 'no';
    // protected $returnType     = 'object';
    // protected $allowedFields = ['no', 'date_save', 'status', 'id_user'];
    public function getAllData()
    {
        $query = "SELECT dk.*,tr.subsidi,tr.kode_tradein,u.region,u.kode_toko,u.nama_pic,u.name,
        (dk.harga_akhir + tr.subsidi) as harga_tradein
        FROM data_kuisionernew dk
        Left join  data_tukar tr on tr.kode_register=dk.kode_register 
        Left JOIN user_toko u ON dk.id_user = u.id_user_toko 
        ";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function getAllDatanew()
    {
        // subsidi,tr.kode_tradein
        // (dk.harga_akhir + tr.subsidi) as harga_tradein
        $query = "SELECT dk.*, (dk.harga_akhir + tr.subsidi) as harga_tradein
        FROM data_kuisionernew dk
        Left join  data_tukar tr on tr.kode_register=dk.kode_register 
        ";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function getDatatradein($key)
    {
        // subsidi,tr.kode_tradein
        // (dk.harga_akhir + tr.subsidi) as harga_tradein
        $query = "SELECT dk.*, tr.*
        FROM data_kuisionernew dk
        Left join  data_tukar tr on tr.kode_register=dk.kode_register where dk.kode_register='$key'  
        ";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function getDatapameran($idpameran)
    {
        $query = "SELECT * FROM data_pameran WHERE id_pameran='$idpameran'";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function tradeinPameranModel($kode_model)
    {
        $query = "SELECT * FROM data_product_tambah WHERE kode='$kode_model'";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekDatamasterkuisioner()
    {
        $query = "SELECT * FROM master_kuisioner";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function test($var = 1)
    {
        return $var;
    }
    public function cekData($kategory)
    {
        $query = "SELECT * FROM data_potong  WHERE kategori='$kategory'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekDatakategori($idkateg)
    {
        $query = "SELECT * FROM child_kuisioner  WHERE id_mitra='$idkateg'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekData2($key)
    {
        $query = "SELECT dk.*,tr.subsidi,tr.kode_tradein,u.region,u.kode_toko,u.nama_pic,u.name,
        (dk.harga_akhir + tr.subsidi) as harga_tradein
        FROM data_kuisionernew dk
        Left join  data_tukar tr on tr.kode_register=dk.kode_register 
        Left JOIN user_toko u ON dk.id_user = u.id_user_toko WHERE dk.no ='$key'
        ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekData3($kode_register)
    {
        $query = "SELECT * FROM data_store  WHERE kode_register='$kode_register'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekData4($id_pameran)
    {
        $query = "SELECT * FROM data_pameran  WHERE id_pameran='$id_pameran'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekData5($id_pameran)
    {
        $query = "SELECT * FROM data_product_tambah  WHERE id_pameran='$id_pameran'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function getAllDatamitra($id)
    {

        $database2 = db_connect("others");
        $query = "SELECT nama_mitra FROM master_mitra  WHERE id_mitra='$id'  ";
        $data  = $database2->query($query);
        $out = "Tidak Ada Mitra";
        if ($data->getNumRows() > 0) {
            $row = $data->getRowArray();
            $out = $row["nama_mitra"];
        }
        return $out;
    }
    public function getDatamitrapemeran($id)
    {
        $query = "SELECT id_mitra,id_pameran FROM mitra_pameran Where id_pameran='$id' ";
        $datas = $this->db->query($query)->getResultArray();
        $database2 = db_connect("others");
        $query2 = "SELECT nama_mitra,id_mitra FROM master_mitra   ";
        $datas = $this->db->query($query);
        $data  = $database2->query($query2)->getResultArray();
        $out = array(
            'id_mitra' => "",
            'nama_mitra' => "-",
        );
        if ($datas->getNumRows() > 0) {
            $datas2 = $this->db->query($query)->getResultArray();
            $i = 0;
            $out['id_mitra'] = "";
            $out['nama_mitra'] = "";
            foreach ($datas2 as $value) {
                $i++;
                foreach ($data as $value2) {
                    if ($value['id_mitra'] == $value2['id_mitra']) {
                        $out['id_mitra'] .= $value2['id_mitra'];
                        $out['nama_mitra'] .= $value2['nama_mitra'];
                        $out['id_mitra'] .= ",";
                        if ($i != $datas->getNumRows()) {
                            $out['nama_mitra'] .= ",";
                        } else {
                            $out['nama_mitra'] .= " ";
                        }
                    }
                }
            }
        }
        return $out;
    }
    public function edit($no)
    {
        $query = "SELECT * FROM data_kuisionernew  WHERE no='$no'  ";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function notescek($no, $notes)
    {
        $model = $this->db->table('data_kuisionernew');
        $model->where('kode_kuis', $no)->set('notes', $notes)->update();
        $message = "telah Berhasil";
        return $message;
    }
    public function AllDatamitra()
    {

        $database2 = db_connect("others");
        $query = "SELECT * FROM master_mitra ";
        $data  = $database2->query($query)->getResultArray();
        return $data;
    }
    public function getStatus($status)
    {
        if ($status == 'All') {
            $sql = "SELECT 
                dk.*, tr.kode_tradein, tr.subsidi, tr.harga_total,u.region,u.kode_toko,u.nama_pic, m.mitra
                FROM data_kuisionernew dk
                LEFT join data_tukar tr on tr.kode_register=dk.kode_register 
                 LEFT JOIN data_mitra m on m.id_mitra=tr.id_mitra
                 LEFT JOIN user_toko u ON dk.id_user = u.id_user_toko
                ";
            $datas = $this->db->query($sql)->getResultArray();
            return $datas;
        } else {
            $sql = "SELECT 
            dk.*, tr.kode_tradein, tr.subsidi, tr.harga_total,u.region,u.kode_toko,u.nama_pic, m.mitra
            FROM data_kuisionernew dk
            LEFT join data_tukar tr on tr.kode_register=dk.kode_register 
             LEFT JOIN data_mitra m on m.id_mitra=tr.id_mitra
             LEFT JOIN user_toko u ON dk.id_user = u.id_user_toko
             WHERE dk.status='$status'
            ";
            $datas = $this->db->query($sql)->getResultArray();
            return $datas;
        }
    }
    public function getDateStatus($date, $status)
    {
        $Date = explode(" ", $date);

        if ($status == 'All') {
            $starttime = strtotime($Date[0]);
            $startformat = date('Y-m-d', $starttime);
            $endtime = strtotime($Date[1]);
            $endformat = date('Y-m-d', $endtime);
            $sql = "SELECT 
            dk.*, tr.kode_tradein, tr.subsidi, tr.harga_total, m.mitra
            FROM data_kuisionernew dk
            LEFT join data_tukar tr on tr.kode_register=dk.kode_register 
             LEFT JOIN data_mitra m on m.id_mitra=tr.id_mitra
             WHERE dk.date_save >= '$startformat' AND dk.date_save <= ' $endformat'
            ";
            $datas = $this->db->query($sql)->getResultArray();
            return $datas;
        } else {
            $starttime = strtotime($Date[0]);
            $startformat = date('Y-m-d', $starttime);
            $endtime = strtotime($Date[1]);
            $endformat = date('Y-m-d', $endtime);
            $sql = "SELECT 
            dk.*, tr.kode_tradein, tr.subsidi, tr.harga_total, m.mitra
            FROM data_kuisionernew dk
            LEFT join data_tukar tr on tr.kode_register=dk.kode_register 
             LEFT JOIN data_mitra m on m.id_mitra=tr.id_mitra
             WHERE dk.date_save >= '$startformat' AND dk.date_save <= ' $endformat' AND dk.status='$status'
            ";
            $datas = $this->db->query($sql)->getResultArray();
            return $datas;
        }
    }
    public function savetradein($in)
    {
        $kode_register = $in['kode_register'];
        $id_mitra = $in['id_mitra'];
        $id_pameran = $in['id_pameran'];
        $id_user = $in['id_user'];
        $kode_tradein = $in['kode_tradein'];
        $subsidi = $in['subsidi'];
        $flag = $in['flag'];
        $user_id = $in['user_id'];
        $user_type = $in['user_type'];
        $harga_total = $in['harga_total'];

        $sql = "INSERT INTO data_tukar(kode_register, id_mitra, id_pameran, id_user, kode_tradein, subsidi, harga_total, date_save, flag, date_expired, group_mitra, voucher, waktu_voucher, flag_auto, user_id, user_type) 
        VALUES('$kode_register', '$id_mitra', '$id_pameran', '$id_user', '$kode_tradein',  '$subsidi', '$harga_total', NOW(), '$flag', NOW(), NULL, NULL, NULL, 0, '$user_id', '$user_type'
        )";
        $this->db->query($sql);
    }
    public function savetchildkategori($in)
    {
        $id_mitra = $in['id_mitra'];
        $id_mkuisioner = $in['id_mkuisioner'];
        $id_listkuisioner = $in['id_listkuisioner'];
        $list = $in['list'];
        $sql = "INSERT INTO child_kuisioner(id, id_mitra, id_mkuisioner, id_listkuisioner, list) 
        VALUES(NULL, '$id_mitra', '$id_mkuisioner', '$id_listkuisioner', '$list')";
        $this->db->query($sql);
    }
    public function savekuisioner_new($in)
    {
        $kode_register = $in['kode_register'];
        $kode_kuis = $in['kode_kuis'];
        $id_user = $in['id_user'];
        $id_pameran = $in['id_pameran'];
        $nama_te = $in['nama_te'];
        $alamat = $in['alamat'];
        $kode_produk = $in['kode_produk'];
        $kategori = $in['kategori'];
        $merk = $in['merk'];
        $spec = $in['spec'];
        $size = $in['size'];
        $tahun = $in['tahun'];
        $sn = $in['sn'];
        $harga = $in['harga'];
        $list_kuisioner = $in['list_kuisioner'];
        $harga_akhir = $in['harga_akhir'];
        $status = $in['status'];
        $id_mitra = $in['id_mitra'];
        $device_checker = $in['device_checker'];
        $no_telp = $in['no_telp'];
        $kode_model = $in['kode_model'];


        $sql = "INSERT INTO data_kuisionernew(kode_register, kode_kuis, id_user, id_pameran, nama_te, alamat, kode_produk, kategori, kode_model, device_checker, no_telp, merk, spec, size, tahun, sn, harga, list_kuisioner, harga_akhir, date_save, status, id_mitra, notes, deleted_at) 
        VALUES('$kode_register', '$kode_kuis', '$id_user', '$id_pameran', '$nama_te',  '$alamat', '$kode_produk', '$kategori', '$kode_model', '$device_checker', '$no_telp', '$merk', '$spec', '$size', '$tahun', '$sn', '$harga', '$list_kuisioner', '$harga_akhir', NOW(), '$status', '$id_mitra', NULL, NULL
        )";
        $this->db->query($sql);
    }
    public function getberhasil($nama_kategori, $status)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE status='$status' and kategori='$nama_kategori'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function getgagal($nama_kategori, $status)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE status='$status' and kategori='$nama_kategori'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function gettotal($nama_kategori)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE kategori='$nama_kategori'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function getberhasildate($nama_kategori, $status, $start, $end)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE status='$status' and kategori='$nama_kategori' and  date_save >= '$start' and date_save <= '$end'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function getgagaldate($nama_kategori, $status, $start, $end)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE status='$status' and kategori='$nama_kategori' and  date_save >= '$start' and date_save <= '$end'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function gettotaldate($nama_kategori, $start, $end)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE kategori='$nama_kategori' and date_save >= '$start' and date_save <= '$end'";
        $datas = $this->db->query($query)->getResultArray();
        $jumlah = count($datas);
        return $jumlah;
    }
    public function getmitrapameran($kategori)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE kategori='$kategori'";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function getmit($id_pameran, $status)
    {
        $query = "SELECT * FROM mitra_pameran WHERE id_pameran='$id_pameran' and id_mitra='$status' ";
        $datas = $this->db->query($query)->getResultArray();

        return $datas;
    }
    public function getmitrapamerandate($kategori, $start, $end)
    {
        $query = "SELECT * FROM data_kuisionernew WHERE kategori='$kategori'and date_save >= '$start' and date_save <= '$end'";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function saveregistertradein($in)
    {
        $kode_register = $in['kode_register'];
        $username = $in['username'];
        $user_id = $in['user_id'];
        $sql = "INSERT INTO data_register(id, kode_register, id_user, username, date_save) 
        VALUES(NULL, '$kode_register', '$user_id', '$username', NOW())";
        $this->db->query($sql);
    }
    public function detailtukar($register)
    {
        $query = "SELECT * FROM data_tukar WHERE kode_register ='$register'";
        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function savecustomer($in)
    {
        $kode_register = $in['kode_register'];
        $id_user = $in['id_user'];
        $nama = $in['nama'];
        $no_hp = $in['no_hp'];
        $email = $in['email'];
        $motherboard = $in['motherboard'];
        $asuransi = $in['asuransi'];
        $hapus_data = $in['hapus_data'];
        $sql = "INSERT INTO data_customer(id, kode_register, id_user, nama, no_hp, email, date_save, motherboard, asuransi, hapus_data) 
        VALUES(NULL, '$kode_register', '$id_user', '$nama', '$no_hp', '$email', NOW(), '$motherboard', '$asuransi', '$hapus_data')";
        $this->db->query($sql);
    }
}
