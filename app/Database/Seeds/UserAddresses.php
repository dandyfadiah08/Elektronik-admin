<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserAddresses extends Seeder
{
	public function run()
	{
		$data = [
			'user_id'			=> '1',
			'district_id'		=> '1101010',
			'village_id'		=> '1101010001',
			'address_name'		=> 'Test Address',
			'postal_code'		=> '123456',
			'default'			=> 'y',
			'notes'				=> 'Jl. Test No 123',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now()
		];
		$this->db->table('user_addresses')->insert($data);
	}
}
