<<<<<<< HEAD
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

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $updatedField         = 'updated_at';

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
=======
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

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $updatedField         = 'updated_at';

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
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
