<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAdresses extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_adresses';
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

	public function getAddressUser($where, $select, $order = false){
		$output = null;
        $this->select($select)
			->from('user_addresses as ua', true)
			->join('address_villages av','av.village_id = ua.village_id')
			->join('address_districts ad','ad.district_id = ua.district_id')
			->join('address_cities ac','ac.city_id = ad.city_id')
			->join('address_provinces ap','ap.province_id = ac.province_id')
			;
		if($order) $this->orderBy($order);
        $this->where($where);
        
		$output = $this->get()->getResult();
        return $output;
	}

	static public function getFieldForAddress(){
		return 'ua.address_id, ua.user_id, ua.district_id, ad.name AS district_name, ua.village_id, av.name AS village_name, ac.city_id, ac.name AS city_name, ap.province_id, ap.name AS province_name, ua.address_name, ua.postal_code, ua.default, ua.longitude, ua.latitude, ua.notes';
	}
}
