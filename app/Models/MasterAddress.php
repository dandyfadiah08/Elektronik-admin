<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterAddress extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'masteraddresses';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
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

	public function getProvinces(){
		$output = null;
        $this->from('address_provinces AS ap', true);
		$output = $this->get()->getResult();
        return $output;
	}

	public function getCities($where){
		$output = null;
        $this->from('address_cities AS ac', true)
			->where($where);
		$output = $this->get()->getResult();
        return $output;
	}

	public function getDistrict($where){
		$output = null;
        $this->from('address_districts AS ad', true)
			->where($where);
		$output = $this->get()->getResult();
        return $output;
	}
}
