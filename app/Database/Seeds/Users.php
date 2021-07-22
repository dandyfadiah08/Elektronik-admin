<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Users extends Seeder
{
	public function run()
	{
		//
		$faker = \Faker\Factory::create('id_ID');

		for ($i=0; $i < 100; $i++) { 
			$data = [
				'nik'				=> $faker->nik(),
				'phone_no'			=> str_replace('+', '', $faker->unique()->e164PhoneNumber), // '+27113456789'
				'email'				=> $faker->unique()->email,
				'name'				=> $faker->unique()->name,
				'ref_code'			=> $faker->unique()->numerify('ref####'),
				'status'			=> $faker->randomElements(['pending', 'active', 'inactive', 'banned']),
				'photo_id'			=> $faker->uuid().".jpg",
				'created_at'		=> $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d H:i:s'),
				'updated_at'		=> Time::now()
			];
	
			// Using Query Builder
			$this->db->table('users')->insert($data);
		}
	}
}
