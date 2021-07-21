<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserPayouts extends Seeder
{
	public function run()
	{
		$data = [
			[
				'user_id'			=> '1',
				'user_payment_id'	=> '1',
				'amount'			=> '1500000',
				'type'				=> 'transaction',
				'created_at'		=> 'system',
				'created_at'		=> Time::now(),
				'updated_at'		=> 'system',
				'updated_at'		=> Time::now()
			],
			[
				'user_id'			=> '1',
				'user_payment_id'	=> '2',
				'amount'			=> '500000',
				'type'				=> 'withdraw',
				'created_at'		=> 'system',
				'created_at'		=> Time::now(),
				'updated_at'		=> 'system',
				'updated_at'		=> Time::now()
			],
		];
		$this->db->table('user_payouts')->insertBatch($data);
	}
}
