<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminRolesModel extends Model
{
    protected $table = 'admin_roles';
    protected $primaryKey = 'role_id';

    protected $useAutoIncrement = true;
	protected $protectFields    = false;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    // protected $allowedFields = ['username', 'password', 'role_id', 'status', 'updated_by', 'created_by'];

    public function getAdminRole($where, $select = false)
    {
        $output = null;
        if($select) $this->select($select);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);

        return $output;
    }

    public function getAllRole($select = false)
    {
        $output = null;
        if($select) $this->select($select);
        $output = $this->where(['deleted_at' => null]);

        return $output->get()->getResult();
    }
}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminRolesModel extends Model
{
    protected $table = 'admin_roles';
    protected $primaryKey = 'role_id';

    protected $useAutoIncrement = true;
	protected $protectFields    = false;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    // protected $allowedFields = ['username', 'password', 'role_id', 'status', 'updated_by', 'created_by'];

    public function getAdminRole($where, $select = false)
    {
        $output = null;
        if($select) $this->select($select);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);

        return $output;
    }

    public function getAllRole($select = false)
    {
        $output = null;
        if($select) $this->select($select);
        $output = $this->where(['deleted_at' => null]);

        return $output->get()->getResult();
    }
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
