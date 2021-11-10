<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceChecks extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'device_checks';
	protected $primaryKey           = 'check_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['check_code','key_code','price_id','promo_id','brand','model','storage','type','os','imei','created_at','updated_at'];

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

	public function getDeviceChecks($where,$whereinData, $select = false, $order = false, $limit = false, $start = 0)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where);
        else $output = $this->find($where);
		if($limit) $this->limit($limit);
		if($whereinData) 
		{
			$arr_keys = array_keys($whereinData);
			for ($i=0; $i < count($arr_keys); $i++) { 
				$key = $arr_keys[$i];
				$this->whereIn($key, $whereinData[$key]);
			}
		}
		$output = $this->get()->getResult();
        return $output;
    }

	public function getDevice($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }

	public function getDeviceDetail($where, $select = false, $order = false)
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);

        $output = $builder->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
    }

	public function getDeviceDetailUser($where, $select = false, $order = false)
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("users u", "u.user_id=dc.user_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);

        $output = $builder->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
    }

	public function getDeviceDetailAppointment($where, $select = false, $order = false, $whereIn = [])
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("appointments apn", "apn.check_id=dc.check_id", "left")
		->join("addresses adr", "adr.address_id=apn.address_id", "left")
		->join('address_villages av','av.village_id = adr.village_id','left')
		->join('address_districts ad','ad.district_id = adr.district_id','left')
		->join('address_cities ac','ac.city_id = ad.city_id','left')
		->join('address_provinces ap','ap.province_id = ac.province_id','left')
		->join("payment_methods pm", "pm.payment_method_id=dcd.payment_method_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);
		if(count($whereIn) > 0) {
			foreach ($whereIn as $key => $value) $this->whereIn($key, $value);
		}


        $output = $builder->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
    }

	public function getDeviceDetailFull($where, $select = false, $order = false, $whereIn = [])
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("users u", "u.user_id=dc.user_id", "left")
		->join("master_promos mp", "mp.promo_id=dc.promo_id", "left")
		->join("appointments apn", "apn.check_id=dc.check_id", "left")
		->join("addresses adr", "adr.address_id=apn.address_id", "left")
		->join('address_villages av','av.village_id = adr.village_id','left')
		->join('address_districts ad','ad.district_id = adr.district_id','left')
		->join('address_cities ac','ac.city_id = ad.city_id','left')
		->join('address_provinces ap','ap.province_id = ac.province_id','left')
		->join("payment_methods pm", "pm.payment_method_id=dcd.payment_method_id", "left")
		->join("merchants mr", "mr.merchant_id=dc.merchant_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);
		if(count($whereIn) > 0) {
			foreach ($whereIn as $key => $value) $this->whereIn($key, $value);
		}


        $output = $builder->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
    }

	public function getDeviceDetailPayment($where, $select = false, $order = false, $whereIn = [])
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("users u", "u.user_id=dc.user_id", "left")
		->join("appointments app", "app.check_id=dc.check_id", "left")
		->join("user_payouts upa", "upa.check_id=dc.check_id", "left")
		->join("user_payout_details upad", "upad.user_payout_id=upa.user_payout_id", "left")
		->join("user_balance ub", "ub.user_balance_id=upa.user_balance_id", "left")
		->join("payment_methods pm", "pm.payment_method_id=dcd.payment_method_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);
		if(count($whereIn) > 0) {
			// ['status' => [1,2,3]]
			foreach ($whereIn as $key => $value) $builder->whereIn($key, $value);
		}

        $output = $builder->get()->getResult();
		// die($db->getLastQuery());
        return count($output) > 0 ? $output[0] : false;
    }

	public function getDeviceForXendit($where, $select = false, $order = false)
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("user_payout_details upad", "upad.external_id=dc.check_code", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);

        $output = $builder->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
    }

	public function getUnreviewedCount()
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc");
        return $builder->where(['status' => 4])
		->countAllResults();
    }

	// need action on appointment
	public function getOnAppointmentCount()
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc");
        // return $builder->where(['status_internal' => 3]) // appointment confirmation
        // ->orWhere(['status_internal' => 9]) // appointment cancel
		// ->countAllResults();

		// untuk status lebih dari 2
		$status = [3,4,9,10];
		// looping thourh $status array
		$builder->groupStart()
		->where(['status_internal' => $status[0]]);
		if(count($status) > 1)
			for($i = 1; $i < count($status); $i++)
				$builder->orWhere(['status_internal' => $status[$i]]);
		$builder->groupEnd();
        return $builder->countAllResults();
    }

	public function getDeviceDetailAddress($where, $select = false, $order = false, $whereIn = [])
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("appointments app", "app.check_id=dc.check_id", "left")
		->join("addresses add", "add.address_id=app.address_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);
		if(count($whereIn) > 0) {
			// ['status' => [1,2,3]]
			foreach ($whereIn as $key => $value) $builder->whereIn($key, $value);
		}

        $output = $builder->get()->getResult();
		// die($db->getLastQuery());
        return count($output) > 0 ? $output[0] : false;
    }

	public static function getFieldsForTransactionPending() {
		return 'check_id,imei,brand, check_code,
		model,type,storage,os,price,grade,status, created_at, updated_at';
	}

	public function getDeviceDetailForLock($where, $select = false, $whereIn = [])
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left");
        if($select) $builder->select($select);
        $builder->where($where);

		if(count($whereIn) > 0) {
			// ['status' => [1,2,3]]
			foreach ($whereIn as $key => $value) $builder->whereIn($key, $value);
		}

        $output = $builder->get()->getResult();
        return $output;
    }
}
