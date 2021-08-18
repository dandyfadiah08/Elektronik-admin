<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPayouts extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_payouts';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

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


	public function getTransactionUser($id_user){
		return $this
					->select('up.user_payout_id, up.user_id, up.user_balance_id, up.user_payment_id,
								up.amount,up.type, up.status, up.check_id,dc.check_code, dc.brand, dc.model, dc.type, dc.storage, dc.os, dc.status'
							)
					->from('user_payouts as up', true)
					->join('device_checks dc','dc.check_id = up.check_id')
                    ->where('up.type', 'transaction')
					->where('up.user_id', $id_user)
                    ->get()
                    ->getResult();
	}
}
