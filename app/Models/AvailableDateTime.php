<?php

namespace App\Models;

use CodeIgniter\Model;

class AvailableDateTime extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'available_date_time';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
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

	function getAvailableDateTime($where, $wherein, $select){
		$output = null;
		$this->select($select)
			->where($where);
		if($wherein) 
		{
			$arr_keys = array_keys($wherein);
			for ($i=0; $i < count($arr_keys); $i++) { 
				$key = $arr_keys[$i];
				$this->whereIn($key, $wherein[$key]);
			}
		}
		$output = $this->get()->getResult();
        return $output;
	}
}
