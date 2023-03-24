<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'log_aksi';
    protected $primaryKey           = 'id_log_aksi';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_log_aksi', 'user', 'aksi', 'created_at', 'deleted_at'];

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
    function getAllData()
    {
        $query = "SELECT * FROM log_kategori";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    function getAllDataLogAksi($username)
    {
        $query = "SELECT * FROM log_aksi  WHERE user='$username'";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    function Log_aksi($in)
    {
        $id = $in['id'];
        $user = $in['user'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . $in['ket'] . ",\n" . $in['Pertanyaan1'] . ",\n" . $in['que_1'] . ",\n" . $in['Pertanyaan2'] . ",\n" . $in['que_2'] . ",\n" . $in['Pertanyaan3'] . ",\n" . $in['que_3'] . ",\n" . $in['Pertanyaan4'] . ",\n" . $in['que_4'] . ",\n" . $in['Pertanyaan5'] . ",\n" . $in['que_5'] . ",\n" . $in['Pertanyaan6'] . ",\n" . $in['que_6'] . ",\n" . $in['Pertanyaan7'] . ",\n" . $in['que_7'] . ",\n" . $in['Pertanyaan8'] . ",\n" . $in['que_8'] . ",\n" . $in['Pertanyaan9'] . ",\n" . $in['que_9'] . ",\n" . $in['Pertanyaan10'] . ",\n" . $in['que_10'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        // var_dump($sql);
        // die();
        $this->db->query($sql);
    }
    function Logadmin($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'new Admin: ' . $in['username'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }

    function Log_editadmin($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Admin: ' . $in['username'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapusadmin($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Admin: ' . $in['username'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_tambahrole($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'new Role: ' . $in['nama_role'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_editrole($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Role: ' . $in['nama_role'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapusrole($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Role: ' . $in['username'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }

    function Logtambahpameran($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'New Pameran: ' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Logeditpameran($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Pameran: ' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Logeditharga($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Kode Produk: ' . $in['kode_produk'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Logtambahharga($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Kode Produk: ' . $in['kode_produk'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function LogtambahNewphone($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'nama New Brand: ' . $in['brand'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Logeditnewphones($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Nama Brand : ' . $in['brand'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapuspameran($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Pameran: ' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapusnewphone($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Nama Brand: ' . $in['brand'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapusharga($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Kode Product: ' . $in['kode_produk'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapushargaAll($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $datain = $in['datain'];

        foreach ($datain as $value) {
            $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
            $data  =  $this->db->query($id_kategori)->getResultArray();
            $out = $data[0]['kategori'];
            $ket = "[" . $out . "] \n" . 'Hapus Kode Product: ' . $value['kode_produk'] . ',' . 'Dari Pameran:' . $in['nama_pameran'];
            $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
            VALUES(NULL, '$user', '$id', '$ket', NOW()
            )";
            $this->db->query($sql);
        }
    }
    function Logkategori($in)
    {

        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'new Kategori: ' . $in['kategori'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_editkategori($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Kategori: ' . $in['kategori'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapuskategori($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Kategori: ' . $in['kategori'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Loggrading($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'new Jenis Grading: ' . $in['nama_grading'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_editgrading($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Grading: ' . $in['nama_grading'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapusgrading($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Jenis Grading: ' . $in['nama_grading'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Logkuisioner($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'New Point Kuisioner: ' . $in['kuisioner'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_editkuisioner($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Edit Point Kuisioner: ' . $in['kuisioner'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
    function Log_hapuskuisioner($in)
    {
        $id = $in['id'];
        $user = $in['editing'];
        $id_kategori = "SELECT kategori FROM log_kategori WHERE id_log_kategori='$id'";
        $data  =  $this->db->query($id_kategori)->getResultArray();
        $out = $data[0]['kategori'];
        $ket = "[" . $out . "] \n" . 'Hapus Point Kuisioner: ' . $in['kuisioner'];
        $sql = "INSERT INTO log_aksi(id_log_aksi, user, kategori, aksi, created_at) 
        VALUES(NULL, '$user', '$id', '$ket', NOW()
        )";
        $this->db->query($sql);
    }
}
