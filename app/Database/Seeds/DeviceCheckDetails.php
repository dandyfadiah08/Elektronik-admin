<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DeviceCheckDetails extends Seeder
{
	public function run()
	{
		$faker = \Faker\Factory::create('id_ID');
		$photo = $faker->uuid().".jpg";
		$data = [
			'check_id' 				=> '1',
			'quiz_1' 				=> '1',
			'quiz_2' 				=> '1',
			'quiz_3' 				=> '1',
			'quiz_4' 				=> '1',
			'screen' 				=> '1',
			'camera_back' 			=> '1',
			'camera_front' 			=> '1',
			'simcard' 				=> '1',
			'button_power' 			=> '1',
			'button_back' 			=> '1',
			'button_volume' 		=> '1',
			'button_volume' 		=> '1',
			'cpu' 					=> '1',
			'harddisk'		 		=> '1',
			'imei_registered' 		=> '1',
			'fullset' 				=> '0',
			'photo_id' 				=> "$photo-id.jpg",
			'photo_device_1' 		=> "$photo-1.jpg",
			'photo_device_2' 		=> "$photo-2.jpg",
			'photo_device_3' 		=> "$photo-3.jpg",
			'photo_device_4' 		=> "$photo-4.jpg",
			'photo_device_5' 		=> "$photo-5.jpg",
			'photo_device_6' 		=> "$photo-6.jpg",
			'photo_fullset' 		=> "",
			'photo_imei_registered'	=> "$photo-imei.jpg",
			'created_at'	=> Time::now(),
			'updated_at'	=> Time::now()
		];
		$this->db->table('device_check_details')->insert($data);
	}
}
