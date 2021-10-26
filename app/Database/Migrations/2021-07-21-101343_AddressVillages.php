<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdressesVillages extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'village_id' => [
				'type' => 'CHAR',
				'constraint' => 10,
			],
			'district_id' => [
				'type' => 'CHAR',
				'constraint' => 7,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
			],
		]);
		$this->forge->addPrimaryKey('village_id');
		$this->forge->addForeignKey('district_id', 'address_districts', 'district_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('address_villages', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('AddressVillages');
	}

	public function down()
	{
		// $this->forge->dropTable('address_villages');
	}
}
