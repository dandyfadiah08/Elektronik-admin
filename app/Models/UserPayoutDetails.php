<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPayoutDetails extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_payout_details';
	protected $primaryKey           = 'user_payout_detail_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = [];

	public function getTransactionUser($where,$whereinData, $select, $order = false, $limit = false, $start = 0){
		
		$output = null;
		$this->select($select)
			->from('user_payouts as up', true)
			->join('device_checks dc','dc.check_id = up.check_id')
			->join('user_payout_details upd','upd.user_payout_id = up.user_payout_id')
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

	public function getUserPayoutDetails($where, $select){
		$output = null;
		return $this->select($select)
			->where($where)->first();
	}
}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPayoutDetails extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_payout_details';
	protected $primaryKey           = 'user_payout_detail_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = [];

	public function getTransactionUser($where,$whereinData, $select, $order = false, $limit = false, $start = 0){
		
		$output = null;
		$this->select($select)
			->from('user_payouts as up', true)
			->join('device_checks dc','dc.check_id = up.check_id')
			->join('user_payout_details upd','upd.user_payout_id = up.user_payout_id')
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

	public function getUserPayoutDetails($where, $select){
		$output = null;
		return $this->select($select)
			->where($where)->first();
	}
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
