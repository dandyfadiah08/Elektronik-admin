<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserPayouts extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_payout_id' => [
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
			'user_balance_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'user_payment_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'amount' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'type' => [
				'type' => 'ENUM',
				'constraint' => ['transaction', 'withdraw'],
				'default' => 'transaction',
			],
			'status' => [
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '1',
			],
			'check_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'null' => true,
			],
			'created_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
				'default' => 'system',
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
			'updated_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
				'default' => 'system',
			],
			'updated_at' => [
				'type' => 'DATETIME',
			],
			'deleted_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);
		$this->forge->addPrimaryKey('user_payout_id');
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('user_balance_id', 'user_balance', 'user_balance_id');
		$this->forge->addForeignKey('user_payment_id', 'user_payments', 'user_payment_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('user_payouts', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('UserPayouts');
	}

	public function down()
	{
		$this->forge->dropTable('user_payouts');
	}
}
