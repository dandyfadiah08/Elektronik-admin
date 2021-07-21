<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserPayments extends Seeder
{
	public function run()
	{
		$data = [
			[
				'user_id'			=> '1',
				'payment_method_id'	=> '1',
				'no_account'		=> '12345678',
				'account_name'		=> 'Fajar Budi Cahyanto',
				'created_at'		=> Time::now(),
				'updated_at'		=> Time::now()
			],
			[
				'user_id'			=> '1',
				'payment_method_id'	=> '3',
				'no_account'		=> '08123456789',
				'account_name'		=> 'Rudi Tabooti',
				'created_at'		=> Time::now(),
				'updated_at'		=> Time::now()
			],
		];
		$this->db->table('user_payments')->insertBatch($data);
	}
}
