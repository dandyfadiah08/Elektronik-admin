<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationQueues extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'notification_queues';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	public function getQueues($where, $select, $order = false, $limit = false)
    {
		if($order) $this->orderBy($order);
		else $this->orderBy('scheduled ASC');
		$this->select($select)
		->where($where);
		if(is_array($limit)) 
	        return $this->findAll($limit[0], $limit[1]);
		else
    	    return $this->findAll();
    }
}
