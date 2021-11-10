<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdressesCities extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'city_id' => [
				'type' => 'CHAR',
				'constraint' => 4,
			],
			'province_id' => [
				'type' => 'CHAR',
				'constraint' => 2,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
			],
		]);
		$this->forge->addPrimaryKey('city_id');
		$this->forge->addForeignKey('province_id', 'address_provinces', 'province_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('address_cities', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('AddressCities');
	}

	public function down()
	{
		// $this->forge->dropTable('address_cities');
	}
}
