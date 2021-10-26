<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionRate extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'commission_rate';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['price_form','price_to','commision_1','commision_2','commision_3','updated_at','updated_by','created_at','created_by', 'deleted_at', 'deleted_by'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	public function getCommision($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }

}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionRate extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'commission_rate';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['price_form','price_to','commision_1','commision_2','commision_3','updated_at','updated_by','created_at','created_by', 'deleted_at', 'deleted_by'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	public function getCommision($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
