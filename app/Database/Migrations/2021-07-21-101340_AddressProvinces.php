<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdressesProvincess extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'province_id' => [
				'type' => 'CHAR',
				'constraint' => 2,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
			],
		]);
		$this->forge->addPrimaryKey('province_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('address_provinces', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('AddressProvinces');
	}

	public function down()
	{
		// $this->forge->dropTable('address_provinces');
	}
}
