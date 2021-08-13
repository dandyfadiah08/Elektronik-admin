<?php

namespace App\Models;

use CodeIgniter\Model;

class RefreshTokens extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'refresh_tokens';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $allowedFields        = ['user_id','token','created_at','expired_at'];

	public function getToken($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }
}
