<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'users';
	protected $primaryKey           = 'user_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['nik','phone_no','name','email','ref_code','status','type','created_at','updated_at', 'phone_no_verified', 'email_verified', 'nik_verified', 'submission', 'count_referral', 'active_balance'];

	// Dates
	protected $useTimestamps        = true;
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

	public function getUser($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }

	public function getUsers($where, $select)
    {
        return $this->select($select)
		->where($where)
		->findAll();
    }

	static public function getFieldsForToken() {
		return 'name,user_id,name,email,phone_no,status,phone_no_verified,email_verified,type,submission,active_balance,count_referral';
	}
	
}
