<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBalance extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_balance';
	protected $primaryKey           = 'user_balance_id';
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

	public function getUserBalances($where, $select = false, $order = false, $limit = false, $start = 0)
	{
		$output = null;
		if ($select) $this->select($select);
		if ($order) $this->orderBy($order);
		if (is_array($where)) $this->where($where);
		else $this->find($where);
		if ($limit) $this->limit($limit, $start);
		$output = $this->get()->getResult();

		return $output;
	}

	// public function getTotalBalances($where, $select, $groupBy)
	// { //where is required AND select is required AND group by is require
	public function getUserBalance($where, $select)
    {
        return $this->select($select)
		->where($where)
		->findAll();
    }

	public function getTotalBalances($where, $select, $groupBy){ //where is required AND select is required AND group by is require
		$output = null;
		$this->select($select);
		$this->groupBy($groupBy);
		if (is_array($where)) $output = $this->where($where)->first();
		// $output = $this->get()->getResult();
		// return $output[0];
		return $output;
	}

	// tidak dipakai
	public function getBalanceAndDeviceCheck($where, $select = false, $order = false)
	{
		$output = null;
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table ub")
			->join('device_checks dc', 'dc.check_id = ub.check_id', 'left')
			->join('device_check_details dcd', 'dcd.check_id = dc.check_id', 'left');
		if ($select) $builder->select($select);
		if ($order) $builder->orderBy($order);
		if (is_array($where)) $builder->where($where);

		$output = $builder->get()->getResult();
		return count($output) > 0 ? $output : false;
	}

	/*
	@return string grade
	examples
	$where = where for select 
	$status = getDeviceCheckStatusInternal(4); // output : Finished
	*/
	// For historyBalance
	public function getHistoryBalance($where, $whereinData, $select = false, $order = false, $limit = false, $start = 0)
	{
		$output = null;
		$this->select($select)
			->from('user_balance as ub', true)
			->join('device_checks dc', 'dc.check_id = ub.check_id')
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
}
