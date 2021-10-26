<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RunSeeds extends Migration
{
	public function up()
	{
		// insert data
		$seeder = \Config\Database::seeder();

		$seeder->call('AdminRoles');
		$seeder->call('Admins');
		$seeder->call('Users');
		$seeder->call('Referrals');
		$seeder->call('PaymentMethods');
		$seeder->call('UserPayments');
<<<<<<< HEAD
		$seeder->call('AddressProvinces');
		$seeder->call('AddressCities');
		$seeder->call('AddressDistricts');
		$seeder->call('AddressVillages');
=======
		// $seeder->call('AddressProvinces');
		// $seeder->call('AddressCities');
		// $seeder->call('AddressDistricts');
		// $seeder->call('AddressVillages');
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
		$seeder->call('UserAddresses');
		$seeder->call('MasterPromos');
		$seeder->call('MasterPrices');
		$seeder->call('MasterPromoCodes');
		$seeder->call('DeviceChecks');
		$seeder->call('DeviceCheckDetails');
		$seeder->call('Appointments');
		$seeder->call('UserBalance');
		$seeder->call('UserPayouts');
	}

	public function down()
	{
	}
}
