<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAdresses extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'addresses';
	protected $primaryKey           = 'address_id';
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

	public function getAddressUser($where, $select, $order = false, $limit = false, $start = 0){
		$output = null;
        $this->select($select)
			->from('addresses as ua', true)
			->join('address_districts ad','ad.district_id = ua.district_id','left')
			->join('address_cities ac','ac.city_id = ad.city_id','left')
			->join('address_provinces ap','ap.province_id = ac.province_id','left')
			;
		if($order) $this->orderBy($order);
		if($limit) $this->limit($limit, $start);
        $this->where($where);
        
		$output = $this->get()->getResult();
        return $output;
	}

	public function saveUpdate($where, $data){
		$output = null;
        return $this->where($where)
			->set($data)
			->update()
			;
	}
	

	static public function getFieldForAddress(){
		return 'ua.address_id, ua.check_id, ua.district_id, ad.name AS district_name, ua.village_id, av.name AS village_name, ac.city_id, ac.name AS city_name, ap.province_id, ap.name AS province_name, ua.address_name, ua.postal_code, ua.default, ua.longitude, ua.latitude, ua.notes';
	}
}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAdresses extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'addresses';
	protected $primaryKey           = 'address_id';
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

	public function getAddressUser($where, $select, $order = false, $limit = false, $start = 0){
		$output = null;
        $this->select($select)
			->from('addresses as ua', true)
			->join('address_districts ad','ad.district_id = ua.district_id','left')
			->join('address_cities ac','ac.city_id = ad.city_id','left')
			->join('address_provinces ap','ap.province_id = ac.province_id','left')
			;
		if($order) $this->orderBy($order);
		if($limit) $this->limit($limit, $start);
        $this->where($where);
        
		$output = $this->get()->getResult();
        return $output;
	}

	public function saveUpdate($where, $data){
		$output = null;
        return $this->where($where)
			->set($data)
			->update()
			;
	}
	

	static public function getFieldForAddress(){
		return 'ua.address_id, ua.check_id, ua.district_id, ad.name AS district_name, ua.village_id, av.name AS village_name, ac.city_id, ac.name AS city_name, ap.province_id, ap.name AS province_name, ua.address_name, ua.postal_code, ua.default, ua.longitude, ua.latitude, ua.notes';
	}
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
