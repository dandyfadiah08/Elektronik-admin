<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DeviceChecks extends Seeder
{
	public function run()
	{
		$data = [
			'check_code' 	=> '21000123AZ',
			'key_code' 		=> 'AZ',
			'imei' 			=> '123456789012345',
			'brand' 		=> 'APPLE',
			'model' 		=> 'IPHONE 6S',
			'type' 			=> 'IPHONE 6S 64GB',
			'storage' 		=> '64GB',
			'os' 			=> 'ios 15',
			'promo_id'		=> '1',
			'price_id'		=> '1',
			'price'			=> '1500000',
			'grade'			=> 'S',
			'created_at'	=> Time::now(),
			'updated_at'	=> Time::now()
		];
		$this->db->table('device_check')->insert($data);
	}
}
