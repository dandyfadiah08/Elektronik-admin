<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterAdminModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'admin';
    protected $primaryKey           = 'id_admin';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id_admin', 'id_role', 'username', 'password', 'name', 'password_enk', 'status', 'token_notification', 'created_by', 'created_at', 'updated_by', 'update_at', 'deleted_at', 'deleted_at'];

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
    public function getadmin()
    {
        $query = "SELECT * FROM admin_role";

        $datas = $this->db->query($query)->getResultArray();
        return $datas;
    }
    public function saveadmin($in)
    {
        $id_role = $in['id_role'];
        $username = $in['username'];
        $password = $in['password'];
        $name = '';
        $password_enk = $in['password_enk'];
        $status = $in['status'];
        $created_by = $in['editing'];
        $updated_by = $in['editing'];
        $sql = "INSERT INTO admin(id_admin, id_role, username, password, name, password_enk, status, token_notification, created_by, created_at, updated_by, updated_at, deleted_by, deleted_at) 
        VALUES(NULL, '$id_role', '$username', '$password', '$name', '$password_enk', '$status', NULL, '$created_by', NOW(), '$updated_by', NULL, NULL, NULL
        )";
        $this->db->query($sql);
    }
    public function deleteAdmin($id)
    {
        $sql = "DELETE FROM admin WHERE id_admin='$id'";
        $this->db->query($sql);
    }
    public function roleadmin($id_role, $nama_role)
    {
        $validasi = false;
        $query = "SELECT * FROM admin_role WHERE id_role = '$id_role'";
        $datas = $this->db->query($query)->getResultArray();
        if ($nama_role == 'admin') {
            if ($datas[0]['admin'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'role') {
            if ($datas[0]['role'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'pameran') {
            if ($datas[0]['pameran'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'kuisioner') {
            if ($datas[0]['kuisioner'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'statistik') {
            if ($datas[0]['statistik'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'tradein') {
            if ($datas[0]['tradein'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'grading') {
            if ($datas[0]['grading'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'potongan') {
            if ($datas[0]['potongan'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'kategori') {
            if ($datas[0]['kategori'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }
        if ($nama_role == 'log') {
            if ($datas[0]['log'] == 0) {
                $validasi = false;
            } else {
                $validasi = true;
            }
        }

        return $validasi;
    }
}
