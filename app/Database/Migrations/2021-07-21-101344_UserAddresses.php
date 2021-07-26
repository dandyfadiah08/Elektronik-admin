<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserAddresses extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'address_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'district_id' => [
				'type' => 'CHAR',
				'constraint' => 7,
			],
			'village_id' => [
				'type' => 'CHAR',
				'constraint' => 10,
				'null' => true,
			],
			'address_name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			],
			'postal_code' => [
				'type' => 'VARCHAR',
				'constraint' => '6',
			],
			'default' => [
				'type' => 'ENUM',
				'constraint' => ['y','n'],
				'default' => 'y',
			],
			'longitude' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'latitude' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'notes' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
			'updated_at' => [
				'type' => 'DATETIME',
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			]
		]);
		$this->forge->addPrimaryKey('address_id');
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('district_id', 'address_districts', 'district_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('user_addresses', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('UserAddresses');
	}

	public function down()
	{
		$this->forge->dropTable('user_addresses');
	}
}
