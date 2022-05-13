<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterCouriers extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'couriers';
	protected $primaryKey           = 'courier_id';
	protected $useAutoIncrement     = false;
	protected $protectFields        = false;
	protected $returnType           = 'object';

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	public function getCouriers($where = false, $select = false, $order = false){
		$output = null;
		if($select) $this->select($select);
		if($order) $this->orderBy($order);
		$this->where($where);
		$output = $this->get()->getResult();
		return $output;
	}

	public function getCourier($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }
}
