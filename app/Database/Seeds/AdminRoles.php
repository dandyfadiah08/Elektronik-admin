<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AdminRoles extends Seeder
{
	public function run()
	{
		$data = [
			'role_name' 	=> 'Master',
			'r_admin'		=> 'y',
			'status'		=> 'active',
			'created_by'	=> 'master',
			'created_at'	=> Time::now(),
			'updated_by'	=> 'master',
			'updated_at'	=> Time::now()
		];
		$this->db->table('admin_roles')->insert($data);
	}
}
