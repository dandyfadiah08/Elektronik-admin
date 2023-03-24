<?php

namespace App\Models;

use CodeIgniter\Model;

class Logs extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'log_aksi';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';

	public function getLogs($where, $select = false, $year = null, $order = false)
	{
		$year = $year ?? date('Y');
		$this->table = "logs_$year";
		$output = null;
		if ($select) $this->select($select);
		if ($order) $this->orderBy($order);
		if (is_array($where)) $output = $this->where($where)->first();
		else $output = $this->find($where);
		return $output;
	}
}
