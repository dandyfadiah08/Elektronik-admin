<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
	public function index()
	{
		$faker = \Faker\Factory::create('id_ID');
		dd($faker->dateTimeBetween('-1 month', '+1 month')->format('YmdHis'));
	}
}
