<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserPayments extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_payment_id' => [
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
			'payment_method_id' => [
				'type' => 'TINYINT',
				'constraint' => 3,
				'unsigned' => true,
			],
			'account_number' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'account_name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
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
		$this->forge->addPrimaryKey('user_payment_id');
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('payment_method_id', 'payment_methods', 'payment_method_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('user_payments', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('UserPayments');
	}

	public function down()
	{
		$this->forge->dropTable('user_payments');
	}
}
