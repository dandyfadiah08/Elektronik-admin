<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Appointments extends Seeder
{
	public function run()
	{
		$data = [
			'user_id'			=> '1',
			'check_id'			=> '1',
			'address_id'		=> '1',
			'user_payment_id'	=> '1',
			'phone_owner_name'	=> 'Fajar',
			'choosen_date'		=> '2021-08-30',
			'choosen_time'		=> '13:00:00',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now()
		];
		$this->db->table('appointments')->insert($data);
	}
}
