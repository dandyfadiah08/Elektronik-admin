<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'settings';
	protected $primaryKey           = 'setting_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $updatedField         = 'updated_at';

	public function getSetting($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }
	
	public function getAllSetting($select)
    {
        $output = null;
        if($select) $this->select($select);
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

	
	
}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'settings';
	protected $primaryKey           = 'setting_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $updatedField         = 'updated_at';

	public function getSetting($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }
	
	public function getAllSetting($select)
    {
        $output = null;
        if($select) $this->select($select);
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

	
	
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
