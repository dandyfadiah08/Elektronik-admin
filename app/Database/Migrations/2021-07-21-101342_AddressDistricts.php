<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdressesDistricts extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'district_id' => [
				'type' => 'CHAR',
				'constraint' => 7,
			],
			'city_id' => [
				'type' => 'CHAR',
				'constraint' => 4,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
			],
		]);
		$this->forge->addPrimaryKey('district_id');
		$this->forge->addForeignKey('city_id', 'address_cities', 'city_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('address_districts', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('AddressDistricts');
	}

	public function down()
	{
		// $this->forge->dropTable('address_districts');
	}
}
