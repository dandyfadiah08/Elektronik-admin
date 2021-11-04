<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingTnc extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'settings_tnc';
    protected $primaryKey           = 'settings_tnc_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = [];

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
