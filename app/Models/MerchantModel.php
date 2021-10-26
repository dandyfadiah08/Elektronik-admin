<?php

namespace App\Models;

use CodeIgniter\Model;

class MerchantModel extends Model
{
    protected $table = 'merchants';
    protected $primaryKey = 'merchant_id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
	protected $protectFields        = false;

    public function getMerchant($where, $select = false)
    {
        $output = null;
        if($select) $this->select($select);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);

        return $output;
    }

    public function getMerchants($select = false)
    {
        if($select) $this->select($select);
        return $this->where(['deleted_at' => null])->get()->getResult();;
    }
}
