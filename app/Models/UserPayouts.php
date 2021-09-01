<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPayouts extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_payouts';
	protected $primaryKey           = 'user_payout_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	public function getTransactionUser($where,$whereinData, $select, $order = false, $limit = false, $start = 0){
		
		$output = null;
		$this->select($select)
			->from('user_payouts as up', true)
			->join('device_checks dc','dc.check_id = up.check_id')
			->where($where);
		if($whereinData) 
		{
			$arr_keys = array_keys($whereinData);
			for ($i=0; $i < count($arr_keys); $i++) { 
				$key = $arr_keys[$i];
				$this->whereIn($key, $whereinData[$key]);
			}
		}

		if($order) $this->orderBy($order);
		if($limit) $this->limit($limit, $start);
        
        
		$output = $this->get()->getResult();
        return $output;
	}
	static public function getFieldForPayout(){
		return 'up.user_payout_id, up.user_id, up.user_balance_id, up.user_payment_id, up.amount,up.type, up.status, up.check_id,dc.check_code, dc.brand, dc.model, dc.type, dc.storage, dc.os, dc.status';
	}
}
