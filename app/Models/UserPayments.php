<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPayments extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_payments';
	protected $primaryKey           = 'user_payment_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	protected $allowedFields        = [];

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

	public function getPaymentUser($where, $select, $order = false, $limit = false, $start = 0){
		$output = null;
        $this->select($select)
			->from('user_payments AS up', true)
			->join('payment_methods pm','pm.payment_method_id = up.payment_method_id')
			;
		if($order) $this->orderBy($order);
		if($limit) $this->limit($limit, $start);
        $this->where($where);
        
		$output = $this->get()->getResult();
        return $output;
	}

	public function saveUpdate($where, $data){
		$output = null;
        return $this->where($where)
			->set($data)
			->update()
			;
	}

	static public function getFieldForPayment(){
		return 'up.user_payment_id, up.user_id, up.payment_method_id, pm.type, pm.name, pm.alias_name, up.account_number, up.account_name';
	}
}
