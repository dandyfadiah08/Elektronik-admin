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

	public function getDeviceDetailPayment($where, $select = false, $order = false)
    {
		$db = \Config\Database::connect();
		$builder = $db->table("$this->table dc")
		->join("device_check_details dcd", "dcd.$this->primaryKey=dc.$this->primaryKey", "left")
		->join("appointments app", "app.check_id=dc.check_id", "left")
		->join("user_payouts upa", "upa.check_id=dc.check_id", "left")
		->join("user_payout_details upad", "upad.user_payout_id=upa.user_payout_id", "left")
		->join("payment_methods pm", "pm.payment_method_id=dcd.payment_method_id", "left");
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if(is_array($where)) $builder->where($where);

        $output = $builder->get()->getResult();
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

	public static function getFieldsForTransactionPending() {
		return 'check_id,imei,brand,
		model,type,storage,os,price,grade,status';
	}
}
