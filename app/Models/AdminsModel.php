<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminsModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
	protected $protectFields        = false;
    // protected $allowedFields = ['username', 'password', 'role_id', 'status', 'updated_by', 'created_by'];

    public function getAdmin($where, $select = false)
    {
        $output = null;
        if($select) $this->select($select);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);

        return $output;
    }

    public function getTokenNotifications()
    {
        return $this->
        select('token_notification')
        ->where(['status' => 'active', 'token_notification is not' => null])
        ->findAll();
    }
}
