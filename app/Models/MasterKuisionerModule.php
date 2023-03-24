<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKuisionerModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'admin';
    protected $primaryKey           = 'master_kuisioner';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_mkuisioner', 'number', 'kuisioner', 'id_kategori', 'date_save', 'deleted_at'];

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
    public function savekuisioner($in)
    {
        $kuisioner = $in['kuisioner'];
        $id_jgrading = $in['id_jgrading'];
        $number = $in['number'];
        $sql = "INSERT INTO master_kuisioner(id_mkuisioner, number, kuisioner, id_kategori, date_save, deleted_at) 
        VALUES(NULL, '$number', '$kuisioner', '$id_jgrading', NOW(),  NULL
        )";
        $this->db->query($sql);
    }
    public function updatedata($id_mkuisioner, $data)
    {
        $kuisioner = $data['kuisioner'];
        $kategori = $data['kategori'];
        $number = $data['number'];
        $sql = "UPDATE master_kuisioner
            SET kuisioner= '$kuisioner', id_kategori= '$kategori', number= '$number'
            WHERE id_mkuisioner = '$id_mkuisioner'";
        $this->db->query($sql);
    }
    public function deleteKuisioner($id)
    {
        $sql = "DELETE FROM master_kuisioner WHERE id_mkuisioner='$id'";
        $this->db->query($sql);
    }
    public function deletelistKuisioner($id)
    {
        $sql = "DELETE FROM list_kuisioner WHERE id_mkuisioner='$id'";
        $this->db->query($sql);
    }
    public function dataend()
    {
        $data_pameran = "SELECT id_mkuisioner FROM master_kuisioner where id_mkuisioner IN (SELECT MAX(id_mkuisioner) FROM master_kuisioner)";
        $data = $this->db->query($data_pameran)->getResultArray();
        return $data;
    }
    public function savelistkuisioner($id_mkuisioner, $data)
    {
        foreach ($data as $key => $value) {
            $sql = "INSERT INTO list_kuisioner(id_listkuisioner, id_mkuisioner, list,  date_save, deleted_at) 
            VALUES(NULL, '$id_mkuisioner', '$value', NOW(),  NULL
            )";
            $this->db->query($sql);
        }
    }
    public function savelistkuisionerfile($id_mkuisioner, $data, $deskripsi)
    {

        $sql = "INSERT INTO list_kuisioner(id_listkuisioner, id_mkuisioner, list, deskripsi, date_save, deleted_at) 
            VALUES(NULL, '$id_mkuisioner', '$data', '$deskripsi', NOW(),  NULL
            )";
        $this->db->query($sql);
    }
    public function detaillist($id)
    {
        $sql = "SELECT * FROM list_kuisioner where id_mkuisioner ='$id' ";
        $data = $this->db->query($sql)->getResultArray();
        return $data;
    }
    public function detaillistgambar($idlist)
    {
        $sql = "SELECT id_listkuisioner FROM list_kuisioner where list ='$idlist' ";
        $data = $this->db->query($sql)->getResultArray();
        return $data;
    }
    public function savelistgambar($in)
    {
        $id_listkuisioner = $in['id_listkuisioner'];
        $gambar = $in['gambar'];
        $keterangan = $in['keterangan'];
        $sql = "INSERT INTO list_gambar(id_foto, id_listkuisioner, gambar, keterangan, date_save) 
        VALUES(NULL, '$id_listkuisioner', '$gambar', '$keterangan', NOW()
        )";
        $this->db->query($sql);
    }
    public function savelistgambarkuisioner($in)
    {
        $id_listkuisioner = $in['id_listkuisioner'];
        $gambar = $in['gambar'];
        $keterangan = $in['keterangan'];
        $sql = "INSERT INTO list_gambar(id_foto, id_listkuisioner, gambar, keterangan, date_save) 
        VALUES(NULL, '$id_listkuisioner', '$gambar', '$keterangan', NOW()
        )";
        $this->db->query($sql);
    }
    public function detaillistgambarview($id)
    {
        $id_listkuisioner = $id;
        $sql = "SELECT * FROM list_gambar where id_listkuisioner ='$id' ";
        $data = $this->db->query($sql)->getResultArray();
        return $data;
        $this->db->query($sql);
    }
    public function detaillistkuisioner($list)
    {
        $sql = "SELECT id_listkuisioner FROM list_kuisioner where list ='$list' ";
        $data = $this->db->query($sql)->getResultArray();
        return $data;
        $this->db->query($sql);
    }
    public function deletelistgambar($id)
    {
        $sql = "DELETE FROM list_gambar WHERE id_listkuisioner='$id'";
        $this->db->query($sql);
    }
    public function filterlevel($idkategori, $number)
    {
        $sql = "SELECT * FROM master_kuisioner where id_kategori ='$idkategori' and number = '$number' ";
        $data = $this->db->query($sql)->getResult();
        $this->db->query($sql);
        return $data;
    }
    public function detailidkuisioner($idmkuisioner)
    {
        $sql = "SELECT id_listkuisioner FROM list_kuisioner where id_mkuisioner ='$idmkuisioner' ";
        $data = $this->db->query($sql)->getResultArray();
        return $data;
        $this->db->query($sql);
    }
    public function datamasterkuisioner($id_kategori)
    {
        $sql = "SELECT * FROM master_kuisioner where id_kategori ='$id_kategori' ";
        $data = $this->db->query($sql)->getResult();
        $this->db->query($sql);
        return $data;
    }
    public function datalistkuisioner($id_mkuisioner)
    {
        $sql = "SELECT list_kuisioner.*, list_gambar.gambar FROM list_kuisioner LEFT JOIN list_gambar ON list_kuisioner.id_listkuisioner = list_gambar.id_listkuisioner where list_kuisioner.id_mkuisioner ='$id_mkuisioner'
        ORDER BY list_kuisioner.list  ";
        $data = $this->db->query($sql)->getResult();
        $this->db->query($sql);
        return $data;
    }
    public function showmasterkuisioner()
    {
        $sql = "SELECT * FROM master_kuisioner ";
        $data = $this->db->query($sql)->getResultArray();
        $this->db->query($sql);
        return $data;
    }
    public function showmlistkuisioner()
    {
        $sql = "SELECT * FROM list_kuisioner ";
        $data = $this->db->query($sql)->getResultArray();
        $this->db->query($sql);
        return $data;
    }
    public function deletedlistkuisioner($id)
    {
        $sql = "DELETE FROM kuisioner_grading WHERE id_jgrading='$id'";
        $this->db->query($sql);
    }
    public function savegradinglistkuisioner($in)
    {
        $id_jgrading = $in['id_jgrading'];
        $id_mkuisioner = $in['id_mkuisioner'];
        $id_listkuisioner = $in['id_listkuisioner'];
        $sql = "INSERT INTO kuisioner_grading(id_lgrading, id_jgrading, id_mkuisioner, id_listkuisioner, date_save, deleted_at) 
        VALUES(NULL, '$id_jgrading', '$id_mkuisioner', '$id_listkuisioner', NOW(), NULL
        )";
        $this->db->query($sql);
    }
    public function showmlistkuisionergrading($id)
    {
        $sql = "SELECT * FROM kuisioner_grading where id_jgrading ='$id' ";
        $data = $this->db->query($sql)->getResult();
        $this->db->query($sql);
        return $data;
    }
    public function showmlistkuisionergradingAll()
    {
        $sql = "SELECT * FROM kuisioner_grading  ";
        $data = $this->db->query($sql)->getResultArray();
        $this->db->query($sql);
        return $data;
    }
    public function showmlistkuisionergradinguser($id_liskuisioner)
    {
        $sql = "SELECT id_jgrading FROM kuisioner_grading where id_listkuisioner ='$id_liskuisioner' ";
        $data = $this->db->query($sql)->getResultArray();
        $this->db->query($sql);
        return $data;
    }
    public function datahargakuisioner($id_product, $id_jgrading)
    {

        $sql = "SELECT harga FROM masterharga where id_product ='$id_product' and id_jgrading ='$id_jgrading' ";
        $data = $this->db->query($sql)->getResultArray();
        $this->db->query($sql);
        return $data;
    }
}
