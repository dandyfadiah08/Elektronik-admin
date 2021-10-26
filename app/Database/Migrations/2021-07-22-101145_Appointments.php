<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Appointments extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'appointment_id' => [
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
			'check_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'address_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'user_payment_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'phone_owner_name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'choosen_date' => [
				'type' => 'CHAR',
				'constraint' => '10',
			],
			'choosen_time' => [
				'type' => 'CHAR',
				'constraint' => '8',
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
		$this->forge->addPrimaryKey('appointment_id');
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('check_id', 'device_checks', 'check_id');
		$this->forge->addForeignKey('address_id', 'user_addresses', 'address_id');
		$this->forge->addForeignKey('user_payment_id', 'user_payments', 'user_payment_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('appointments', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('Appointments');
	}

	public function down()
	{
		$this->forge->dropTable('appointments');
	}
}
