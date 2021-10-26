<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class PaymentMethods extends Seeder
{
	public function run()
	{
		$data = [
			[
				'type'			=> 'bank',
				'name'			=> 'Bank Central Asia',
				'alias_name'	=> 'BCA',
				'status'		=> 'active',
				'created_by'	=> 'master',
				'created_at'	=> Time::now(),
				'updated_by'	=> 'master',
				'updated_at'	=> Time::now()
			],
			[
				'type'			=> 'bank',
				'name'			=> 'Bank Rakyat Indonesia',
				'alias_name'	=> 'BRI',
				'status'		=> 'active',
				'created_by'	=> 'master',
				'created_at'	=> Time::now(),
				'updated_by'	=> 'master',
				'updated_at'	=> Time::now()
			],
			[
				'type'			=> 'emoney',
				'name'			=> 'GOPAY',
				'alias_name'	=> 'GOPAY',
				'status'		=> 'active',
				'created_by'	=> 'master',
				'created_at'	=> Time::now(),
				'updated_by'	=> 'master',
				'updated_at'	=> Time::now()
			],
			[
				'type'			=> 'emoney',
				'name'			=> 'OVO',
				'alias_name'	=> 'OVO',
				'status'		=> 'active',
				'created_by'	=> 'master',
				'created_at'	=> Time::now(),
				'updated_by'	=> 'master',
				'updated_at'	=> Time::now()
			],
		];
		$this->db->table('payment_methods')->insertBatch($data);
	}
}
