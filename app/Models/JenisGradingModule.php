<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisGradingModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'jenis_grading';
    protected $primaryKey           = 'id_jgrading';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_jgrading', 'kode_grading', 'nama_grading', 'date_save', 'deleted_at'];

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
    public function getgrading()
    {
        $query = "SELECT * FROM jenis_grading  ";
        $datas =  $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function savegrading($in)
    {
        $kode_grading = $in['kode_grading'];
        $nama_grading = $in['nama_grading'];
        $sql = "INSERT INTO jenis_grading(id_jgrading, kode_grading, nama_grading,  date_save, deleted_at) 
        VALUES(NULL, '$kode_grading', '$nama_grading', NOW(),  NULL
        )";
        $this->db->query($sql);
    }
    public function deleteGrading($id)
    {
        $sql = "DELETE FROM jenis_grading WHERE id_jgrading='$id'";
        $this->db->query($sql);
    }
}
