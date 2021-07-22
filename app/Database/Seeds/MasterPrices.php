<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MasterPrices extends Seeder
{
	public function run()
	{
		$data = [
			'promo_id' 		=> '1',
			'brand' 		=> 'APPLE',
			'model' 		=> 'IPHONE 6S',
			'type' 			=> 'IPHONE 6S 64GB',
			'storage' 		=> '64GB',
			'price_s'		=> '1500000',
			'price_a'		=> '1250000',
			'price_b'		=> '1000000',
			'price_c'		=> '750000',
			'price_d'		=> '500000',
			'price_e'		=> '250000',
			'created_at'	=> Time::now(),
			'updated_at'	=> Time::now()
		];
		$this->db->table('master_prices')->insert($data);

	}
}
