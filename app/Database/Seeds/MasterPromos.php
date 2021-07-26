<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MasterPromos extends Seeder
{
	public function run()
	{
		$data = [
			'promo_name' 	=> 'First Promo',
			'start_date' 	=> date('Y-m-d'),
			'end_date'	 	=> date('Y-m-d'),
			'created_by'	=> 'master',
			'created_at'	=> Time::now(),
			'updated_by'	=> 'master',
			'updated_at'	=> Time::now()
		];
		$this->db->table('master_promos')->insert($data);

	}
}
