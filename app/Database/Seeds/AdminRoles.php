<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AdminRoles extends Seeder
{
	public function run()
	{
		$data = [
			'role_name' 	=> 'edi',
			'r_admin'		=> 'y',
			'status'		=> 'active',
			'created_by'	=> 'edi',
			'created_at'	=> Time::now(),
			'updated_by'	=> 'master',
			'updated_at'	=> Time::now()
		];
		$this->db->table('admin_roles')->insert($data);
	}
}
