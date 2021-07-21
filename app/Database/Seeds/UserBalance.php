<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserBalance extends Seeder
{
	public function run()
	{
		$data = [
			[
				'user_id'			=> '1',
				'amount'			=> '1500000',
				'type'				=> 'in',
				'notes'				=> 'Sell Device Income',
				'created_at'		=> Time::now(),
			],
			[
				'user_id'			=> '1',
				'amount'			=> '500000',
				'type'				=> 'in',
				'notes'				=> 'Commission',
				'created_at'		=> Time::now(),
			],
			[
				'user_id'			=> '1',
				'amount'			=> '1500000',
				'type'				=> 'out',
				'notes'				=> 'Sell Device Transfer',
				'created_at'		=> Time::now(),
			],
			[
				'user_id'			=> '1',
				'amount'			=> '500000',
				'type'				=> 'out',
				'notes'				=> 'Withdraw balance',
				'created_at'		=> Time::now(),
			],
		];
		$this->db->table('user_balance')->insertBatch($data);
	}
}
