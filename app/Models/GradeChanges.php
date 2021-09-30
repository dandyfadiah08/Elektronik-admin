<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeChanges extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'grade_changes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

}
