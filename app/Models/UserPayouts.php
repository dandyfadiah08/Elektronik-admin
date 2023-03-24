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

	public function getUserPayout($where, $select, $order = false)
	{
		$output = null;
		$this->select($select)
			->where($where);
		if ($order) $this->orderBy($order);
		$output = $this->get()->getResult();
		return $output;
	}

	public function getTransactionUser($where, $whereinData, $select, $order = false, $limit = false, $start = 0)
	{

		$output = null;
		$this->select($select)
			->from('user_payouts as up', true)
			->join('device_check dc', 'dc.check_id = up.check_id')
			->where($where);
		if ($whereinData) {
			$arr_keys = array_keys($whereinData);
			for ($i = 0; $i < count($arr_keys); $i++) {
				$key = $arr_keys[$i];
				$this->whereIn($key, $whereinData[$key]);
			}
		}

		if ($order) $this->orderBy($order);
		if ($limit) $this->limit($limit, $start);


		$output = $this->get()->getResult();
		return $output;
	}
	public function getUserPayoutWithDetailPayment($where, $select, $order = false)
	{
		$output = null;
		$this->select($select)
			->where($where)
			->from('user_payouts as ups', true)
			->join('users u', 'u.user_id = ups.user_id', 'left')
			->join('user_payments upa', 'upa.user_payment_id = ups.user_payment_id')
			->join('payment_methods pm', 'pm.payment_method_id = upa.payment_method_id')
			->join('user_payout_details upd', 'upd.user_payout_id = ups.user_payout_id', 'left')
			->join('user_balance ub', 'ub.user_balance_id = ups.user_balance_id', 'left');

		if ($order) $this->orderBy($order);
		$output = $this->get()->getResult();
		return count($output) > 0 ? $output[0] : false;
	}

	public function getUserPayoutAndDetail($where, $select, $order = false)
	{
		$output = null;
		$this->select($select)
			->where($where)
			->from('user_payouts as ups', true)
			->join('user_payout_details upd', 'upd.user_payout_id = ups.user_payout_id', 'left');

		if ($order) $this->orderBy($order);
		$output = $this->get()->getResult();
		return count($output) > 0 ? $output[0] : false;
	}

	public function getWithdrawPendingCount()
	{
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table ups")
			->join('user_payout_details upd', 'upd.user_payout_id = ups.user_payout_id', 'left');
		return $builder->where(['ups.status' => 2, 'ups.type' => 'withdraw']) // belum benar
			->groupStart()
			->where(['upd.status' => 'FAILED'])
			->orWhere(['upd.status' => null])
			->groupEnd()
			->countAllResults();
	}

	static public function getFieldForPayout()
	{
		return 'up.user_payout_id, up.user_id, up.user_balance_id, up.user_payment_id, up.amount,up.type, up.status, up.check_id,dc.check_code, dc.brand, dc.model, dc.type, dc.storage, dc.os, dc.status, up.created_at, up.updated_at, dc.check_code, dc.grade';
	}

	public function saveUpdate($where, $data)
	{
		return $this->where($where)
			->set($data)
			->update();
	}
}
