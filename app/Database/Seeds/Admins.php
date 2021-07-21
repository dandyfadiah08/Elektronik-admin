<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Admins extends Seeder
{
	public function run()
	{
		$data = [
			'username' 		=> 'master',
			'email'			=> 'master@mail.com',
			'name'			=> 'Master',
			'password'		=> '425e30e5b7ef70adeb9bda82633217381e721449e1730e4d26ff382fece174a72227ec252cb35146201a47afc5c76e3f24c13f6371ecc87c6c1c83c6e9fdc237d8eb0549403fcf15fe8d8261b8c02839bc8944277071', // 'master'
			'role_id'		=> '1',
			'status'		=> 'active',
			'created_by'	=> 'master',
			'created_at'	=> Time::now(),
			'updated_by'	=> 'master',
			'updated_at'	=> Time::now()
		];
		$this->db->table('admins')->insert($data);
	}
}
