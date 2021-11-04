<?php

namespace App\Models;

use CodeIgniter\Model;

class Appointments extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'appointments';
	protected $primaryKey           = 'appointment_id ';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	protected $allowedFields        = ['appointment_id','user_id', 'check_id', 'address_id', 'user_payment_id', 'phone_owner_name', 'choosen_date', 'choosen_time', 'created_at', 'updated_at', 'deleted_at'];

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

	public function getAppoinment($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where);
        else $output = $this->find($where);
        return $output->get()->getResult();
    }


	public function getAddressDistrict($where, $select = false, $order = false)
    {
		$db = \Config\Database::connect();
		$builder = $db->table("address_districts");
        $output = null;
        if($select) $builder->select($select);
		if($order) $builder->orderBy($order);
        if($where) $builder->where($where);
		$output = $builder->get()->getResult();
		// die($db->getLastQuery());
        return count($output) > 0 ? $output[0] : false;
    }
}
