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

	public function getDeviceChecks($where, $select = false, $order = false, $limit = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where);
        else $output = $this->find($where);
		if($limit) $this->limit($limit);
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

	public function getFieldsForTransactionPending() {
		return 'check_id,check_kode,imei,brand,
		model,type,storage,os,price,grade,status';
	}
}
