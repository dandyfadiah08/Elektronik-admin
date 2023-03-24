<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminsModel extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $protectFields        = false;

    public function getAdmin($where, $select = false)
    {
        $output = null;
        if ($select) $this->select($select);
        if (is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);

        return $output;
    }

    public function getTokenNotifications()
    {
        return $this->select('token_notification')
            ->where(['status' => 'active', 'token_notification is not' => null])
            ->findAll();
    }

    // public function getAdminAndRole($where, $select = false, $order = false)
    // {
    //     $db = \Config\Database::connect();
    //     $builder = $db->table("$this->table a")
    //         ->join("admin_roles ar", "ar.role_id=a.role_id", "left");
    //     if ($select) $builder->select($select);
    //     if ($order) $builder->orderBy($order);
    //     if (is_array($where)) $builder->where($where);

    //     $output = $builder->get()->getResult();
    //     return count($output) > 0 ? $output[0] : false;
    // }
}
