<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraPameran extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'admin_role';
    protected $primaryKey           = 'id_role';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_mp', 'id_pameran', 'id_mitra', 'updated'];

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


    public function cekmitra($id_mitra)
    {
        $sql = "SELECT * FROM mitra_pameran where id_mitra='$id_mitra'";
        $data = $this->db->query($sql)->getResultArray();

        return $data;
    }
}
