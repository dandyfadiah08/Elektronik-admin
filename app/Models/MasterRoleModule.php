<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterRoleModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'admin_role';
    protected $primaryKey           = 'id_role';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_role', 'nama_role', 'status', 'tradein', 'statistik', 'admin', 'pameran', 'potongan', 'log', 'produk', 'new_device', 'kategori', 'grading', 'kuisioner', 'created_by', 'created_at', 'updated_by', 'updated_at', 'role', 'deleted_by', 'deleted_at'];

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

    public function saverole($in)
    {
        $nama_role = $in['nama_role'];
        $tradein = $in['tradein'];
        $statistik = $in['statistik'];
        $potongan = $in['potongan'];
        $new_device = $in['new_device'];
        $produk = $in['produk'];
        $pameran = $in['pameran'];
        $admin = $in['admin'];
        $grading = $in['grading'];
        $kategori = $in['kategori'];
        $kuisioner = $in['kuisioner'];
        $role = $in['role'];
        $log = $in['log'];
        $editing = $in['editing'];
        $status = $in['status'];
        $sql = "INSERT INTO admin_role(id_role, nama_role, status, tradein, statistik, admin, pameran, potongan, log, produk, new_device, kategori, grading, kuisioner, created_by, created_at, updated_by, updated_at, role, deleted_by, deleted_at) 
        VALUES(NULL, '$nama_role', '$status', '$tradein', '$statistik', '$admin', '$pameran', '$potongan', '$log', '$produk', '$new_device', '$kategori', '$grading', '$kuisioner', '$editing', NOW(), '$editing', NOW(), '$role', NULL, NULL
        )";
        $this->db->query($sql);
    }
    public function deleteRole($id)
    {
        $sql = "DELETE FROM admin_role WHERE id_role='$id'";
        $this->db->query($sql);
    }
}
