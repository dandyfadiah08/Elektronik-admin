<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKategoriModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'admin';
    protected $primaryKey           = 'id_admin';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_kategori', 'nama_kategori', 'deskripsi', 'date_save', 'deleted_at'];

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

    public function getkategori()
    {
        $query = "SELECT id_kategori,nama_kategori FROM master_kategori  ";
        $datas =  $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function showkategori()
    {
        $query = "SELECT * FROM master_kategori  ";
        $datas =  $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function savekategori($in)
    {
        $kategori = $in['kategori'];
        $deskripsi = $in['deskripsi'];
        $sql = "INSERT INTO master_kategori(id_kategori, nama_kategori, deskripsi, date_save, deleted_at) 
        VALUES(NULL, '$kategori', '$deskripsi', NOW(),  NULL
        )";
        $this->db->query($sql);
    }
    public function updatedataempty($id_kategori, $data)
    {
        $nama_kategori = $data['nama_kategori'];
        $sql = "UPDATE master_kategori
            SET nama_kategori= '$nama_kategori'
            WHERE id_kategori = '$id_kategori'";
        $this->db->query($sql);
    }
    public function updatedata($id_kategori, $data)
    {
        $nama_kategori = $data['nama_kategori'];
        $deskripsi = $data['deskripsi'];
        $sql = "UPDATE master_kategori
            SET nama_kategori= '$nama_kategori', deskripsi= '$deskripsi'
            WHERE id_kategori = '$id_kategori'";
        $this->db->query($sql);
    }
    public function deleteKategori($id)
    {
        $sql = "DELETE FROM master_kategori WHERE id_kategori='$id'";
        $this->db->query($sql);
    }
}
