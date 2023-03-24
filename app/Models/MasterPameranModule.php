<?php

namespace App\Models;

use CodeIgniter\CLI\Console;
use CodeIgniter\Model;

class MasterPameranModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'data_pameran';
    protected $primaryKey           = 'id_pameran';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_pameran', 'id_mitra', 'nama_pameran', 'jenis_subsidi', 'subsidi', 'bulan', 'updated', 'voucher'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];
    public function cekpameran($id_pameran)
    {
        $sql = "SELECT * FROM data_product where id_pameran='$id_pameran' and kategori='SERVICE'";
        $data = $this->db->query($sql)->getResultArray();
        $out = 0;
        $jumlah = count($data);
        if ($jumlah > 0) {
            $out = 1;
        }
        return $out;
    }
    public function saveadmin($in)
    {
        $nama_pameran = $in['nama_pameran'];
        $jenis_subsidi = $in['jenis_subsidi'];
        $subsidi = $in['subsidi'];
        $bulan = $in['bulan'];
        $vocher = $in['voucher'];
        $rekapvoucher = '';
        if ($vocher == '') {
            $sql = "INSERT INTO data_pameran(id_pameran, id_mitra, nama_pameran, jenis_subsidi, subsidi, bulan, updated, voucher) 
            VALUES(NULL, NULL, '$nama_pameran', '$jenis_subsidi', '$subsidi', '$bulan', NOW(), NULL
            )";
            $this->db->query($sql);
        } else {
            $sql = "INSERT INTO data_pameran(id_pameran, id_mitra, nama_pameran, jenis_subsidi, subsidi, bulan, updated, voucher) 
            VALUES(NULL, NULL, '$nama_pameran', '$jenis_subsidi', '$subsidi', '$bulan', NOW(), '$vocher'
            )";
            $this->db->query($sql);
        }
    }
    public function dataend()
    {
        $data_pameran = "SELECT * FROM data_pameran where id_pameran IN (SELECT MAX(id_pameran) FROM data_pameran)";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function deletemitra($id_pameran)
    {
        $data_pameran = "DELETE FROM mitra_pameran WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran);
    }
    public function deletePameran($id_pameran)
    {
        $data_mitra = "DELETE FROM mitra_pameran WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_mitra);
        $data_pameran = "DELETE FROM data_pameran WHERE id_pameran= '$id_pameran'";
        $data2 = $this->db->query($data_pameran);
    }
    public function namapameran($id_pameran)
    {
        $data_pameran = "SELECT nama_pameran FROM data_pameran WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function namapameranmitra($id_pameran)
    {
        $data_pameran = "SELECT * FROM data_pameran WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function deletenewphone($id)
    {

        $data = "DELETE FROM data_product_tambah WHERE id= '$id'";
        $data2 = $this->db->query($data);
    }
    public function mitrapameran($in, $id_pameran)
    {
        $datamitra = $in['datamitra'];
        foreach ($datamitra as $value) {
            $sql = "INSERT INTO mitra_pameran(id_mp, id_pameran, id_mitra, updated)
            VALUES(NULL, '$id_pameran', '$value', NOW()
            )";
            $this->db->query($sql);
        }
    }
    public function editpameran($in)
    {

        $id_pameran = $in['id_pameran'];
        $nama_pameran = $in['nama_pameran'];
        $jenis_subsidi = $in['jenis_subsidi'];
        $subsidi = $in['subsidi'];
        $bulan = $in['bulan'];
        $vocher = $in['voucher'];
        $rekapvoucher = '';
        if ($vocher == '') {

            $sql = "UPDATE data_pameran
            SET id_mitra = NULL, nama_pameran= '$nama_pameran', jenis_subsidi= '$jenis_subsidi', subsidi= '$subsidi', bulan= '$bulan', updated= NOW(), voucher=NULL
            WHERE id_pameran = '$id_pameran'";
            $this->db->query($sql);
        } else {

            $sql = "UPDATE data_pameran
            SET id_mitra = NULL, nama_pameran= '$nama_pameran', jenis_subsidi= '$jenis_subsidi', subsidi= '$subsidi', bulan= '$bulan', updated= NOW(), voucher= '$vocher'
            WHERE id_pameran = '$id_pameran'";
            $this->db->query($sql);
        }
    }
    public function savenewphone($in)
    {
        $id_pameran = $in['id_pameran'];
        $brand = $in['brand'];
        $kode = $in['kode'];
        $model = $in['model'];
        $black = $in['black'];
        $white = $in['white'];
        $green = $in['green'];
        $gold = $in['gold'];
        $silver = $in['silver'];
        $gray = $in['gray'];
        $stok = $in['stok'];
        $harga = $in['harga'];
        $sql = "INSERT INTO data_product_tambah(id, id_pameran, kode, brand, model, black, white, green, gold, silver, gray, stok, harga, deleted_at) 
        VALUES(NULL, '$id_pameran', '$kode', '$brand', '$model', '$black', '$white', '$green', '$gold', '$silver', '$gray', '$stok', '$harga', NULL
        )";
        $this->db->query($sql);
    }
    public function editnewphone($in)
    {
        $id = $in['id'];
        $id_pameran = $in['id_pameran'];
        $brand = $in['brand'];
        $kode = $in['kode'];
        $model = $in['model'];
        $black = $in['black'];
        $white = $in['white'];
        $green = $in['green'];
        $gold = $in['gold'];
        $silver = $in['silver'];
        $gray = $in['gray'];
        $stok = $in['stok'];
        $harga = $in['harga'];
        $sql = "UPDATE data_product_tambah
        SET id_pameran = '$id_pameran', kode= '$kode', brand= '$brand', model= '$model', black= '$black', white= '$white', green= '$green', gold= '$gold', silver= '$silver', gray= '$gray', stok= '$stok', harga= '$harga', deleted_at= NULL
        WHERE id = '$id'";
        $this->db->query($sql);
    }
    public function saveharga($in)
    {
        $id_pameran = $in['id_pameran'];
        $kode_produk = $in['kode_produk'];
        $spec = $in['spec'];
        $kategori = $in['kategori'];
        $tahun = $in['tahun'];
        $size = $in['size'];
        $merk = $in['merk'];
        $subsidi = $in['subsidi'];
        $harga = $in['harga'];
        $sql = "INSERT INTO data_product(id_product, kode_produk, id_pameran, kategori, merk, spec, size, tahun, subsidi, subsidi_mitra1, subsidi_mitra2, harga, date_save, deleted_at) 
        VALUES(NULL, '$kode_produk', '$id_pameran', '$kategori', '$merk', '$spec', '$size', '$tahun', '$subsidi', '0', '0', '$harga', NOW(), NULL
        )";
        $this->db->query($sql);
    }
    public function editharga($in)
    {
        $id_product = $in['id_product'];
        $id_pameran = $in['id_pameran'];
        $kode_produk = $in['kode_produk'];
        $spec = $in['spec'];
        $kategori = $in['kategori'];
        $tahun = $in['tahun'];
        $size = $in['size'];
        $merk = $in['merk'];
        $subsidi = $in['subsidi'];
        $sql = "UPDATE data_product
        SET kode_produk= '$kode_produk', id_pameran = '$id_pameran', kategori= '$kategori', merk= '$merk', spec= '$spec', size= '$size', tahun= '$tahun', subsidi= '$subsidi', deleted_at= NULL
        WHERE id_product = '$id_product'";
        $this->db->query($sql);
    }
    public function deleteharga($id)
    {

        $data = "DELETE FROM data_product WHERE id_product= '$id'";
        $data2 = $this->db->query($data);
    }
    public function deletehargaall($id_pameran)
    {
        $data = "DELETE FROM data_product WHERE id_pameran= '$id_pameran'";
        $data2 = $this->db->query($data);
    }
    public function detailproductharga($id_pameran)
    {
        $data_pameran = "SELECT kode_produk FROM data_product WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function detailpameran($id_pameran)
    {
        $data_pameran = "SELECT nama_pameran FROM data_pameran WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function productharga($id_pameran)
    {
        $data_pameran = "SELECT * FROM data_product WHERE id_pameran= '$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function producthargaUser($id_pameran, $kategori)
    {
        $data_pameran = "SELECT * FROM data_product WHERE id_pameran= '$id_pameran' AND kategori='$kategori'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function dataProduct($id_pameran, $id_product)
    {
        $data_pameran = "SELECT * FROM data_product WHERE id_pameran= '$id_pameran' AND id_product='$id_product'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function saveharga2($in)
    {
        $id_pameran = $in['id_pameran'];
        $kode_produk = $in['kode_produk'];
        $spec = $in['spec'];
        $kategori = $in['kategori'];
        $tahun = $in['tahun'];
        $size = $in['size'];
        $merk = $in['merk'];
        $subsidi = $in['subsidi'];
        $subsidi_mitra1 = $in['subsidi_mitra1'];
        $subsidi_mitra2 = $in['subsidi_mitra2'];
        $sql = "INSERT INTO data_product(id_product, kode_produk, id_pameran, kategori, merk, spec, size, tahun, subsidi, subsidi_mitra1, subsidi_mitra2, harga, date_save, deleted_at) 
        VALUES(NULL, '$kode_produk', '$id_pameran', '$kategori', '$merk', '$spec', '$size', '$tahun', '$subsidi', '$subsidi_mitra1', '$subsidi_mitra2', NULL, NOW(), NULL
        )";
        $this->db->query($sql);
    }
    public function cekpameranData($kode_produk, $id_pameran)
    {
        $data_pameran = "SELECT * FROM data_product WHERE kode_produk= '$kode_produk' AND id_pameran='$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        $jumlah = count($data);
        return $jumlah;
    }
    public function cekKode_produk($kode_produk, $id_pameran)
    {
        $data_pameran = "SELECT kode_produk FROM data_product WHERE kode_produk= '$kode_produk' AND id_pameran='$id_pameran'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function dataregister($id_user, $username)
    {
        $query = "SELECT max(kode_register) as maxID FROM data_register";
        $hasil = $this->db->query($query)->getResultArray();
        if ($hasil == false) {
            $kodeRegis = "REG00000";
        } else {
            $kodeRegis = $hasil[0]['maxID'];
        }

        $noUrut = (int) substr($kodeRegis, 3, 8);

        $noUrut++;
        $char = "REG";
        $kodeRegist = $char . sprintf("%05s", $noUrut);
        $datedave = date('Y-m-d h:i:s');
        $dataregis = [
            'kode_register' => $kodeRegist,
            'date_save' => $datedave,
        ];
        return $dataregis;
    }
    public function detailproduct($id_product)
    {
        $data_product = "SELECT jenis_grading.nama_grading, masterharga.harga FROM masterharga INNER JOIN jenis_grading ON masterharga.id_jgrading = jenis_grading.id_jgrading WHERE masterharga.id_product= '$id_product'";
        $data = $this->db->query($data_product)->getResultArray();
        return $data;
    }
    public function getgrading($idjgrading)
    {
        $query = "SELECT * FROM jenis_grading  ";
        $datas =  $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function cekproduct2($id_product)
    {
        $data_pameran = "SELECT id_product FROM data_product WHERE kode_produk= '$id_product'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function cekgrading2($id_jgrading)
    {
        $arraydata = explode(' ', $id_jgrading);

        $data_pameran = "SELECT id_jgrading FROM jenis_grading WHERE nama_grading= '$arraydata[1]'";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function savehargamaster($in)
    {
        $id_product = $in['id_product'];
        $id_jgrading = $in['id_jgrading'];
        $harga = $in['harga'];
        $sql = "INSERT INTO masterharga(id_mharga, id_product, id_jgrading, harga, date_save, deleted_at) 
        VALUES(NULL, '$id_product', '$id_jgrading', '$harga', NOW(), NULL
        )";
        $this->db->query($sql);
    }
    public function cekid_produk($kode_produk)
    {
        $data_pameran = "SELECT id_product FROM data_product WHERE kode_produk= '$kode_produk' ";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function detailmasterharga($id_product)
    {
        $data_product = "SELECT masterharga.id_jgrading ,jenis_grading.nama_grading, masterharga.harga FROM masterharga INNER JOIN jenis_grading ON masterharga.id_jgrading = jenis_grading.id_jgrading WHERE masterharga.id_product= '$id_product'";
        $data = $this->db->query($data_product)->getResultArray();
        return $data;
    }
    public function cekkodeproduct($kode_produk)
    {
        $data_product = "SELECT kode_produk FROM data_product WHERE kode_produk= '$kode_produk'";
        $data = $this->db->query($data_product)->getResultArray();
        return $data;
    }
    public function detailmasterharga2($id_product)
    {

        $data_product = "SELECT masterharga.id_jgrading ,jenis_grading.nama_grading, masterharga.harga FROM masterharga INNER JOIN jenis_grading ON masterharga.id_jgrading = jenis_grading.id_jgrading WHERE masterharga.id_product= '$id_product'";
        $data = $this->db->query($data_product)->getResultArray();
        return $data;
    }
    public function hapushargamaster($id)
    {
        $data = "DELETE FROM masterharga WHERE id_product= '$id'";
        $data2 = $this->db->query($data);
    }
    public function gethargaphonenew($id)
    {
        $data_product = "SELECT * FROM data_product_tambah WHERE id_pameran = '$id'";
        $data = $this->db->query($data_product)->getResultArray();
        return $data;
    }
    public function kodetradein()
    {
        $query = "SELECT max(kode_tradein) as maxID FROM data_tukar";
        $hasil = $this->db->query($query)->getResultArray();
        $kodetradein = $hasil[0]['maxID'] + 1;
        return $kodetradein;
    }
    public function Datapameran($id_pameran)
    {
        $sql = "SELECT * FROM data_pameran where id_pameran='$id_pameran'";
        $data = $this->db->query($sql)->getResultArray();

        return $data[0]['subsidi'];
    }
    public function kodekuis()
    {
        $query = "SELECT max(kode_kuis) as maxID FROM data_kuisionernew";
        $hasil = $this->db->query($query)->getResultArray();
        if ($hasil == false) {
            $kodeRegis = "QUE00000";
        } else {
            $kodeRegis = $hasil[0]['maxID'];
        }
        if ($kodeRegis == NULL) {
            $kodeRegis = "QUE00000";
        } else {
            $kodeRegis = $hasil[0]['maxID'];
        }

        $noUrut = (int) substr($kodeRegis, 3, 8);

        $noUrut++;
        $char = "QUE";
        $kodeRegist = $char . sprintf("%05s", $noUrut);
        $kodekuis = $kodeRegist;
        return $kodekuis;
    }
}
