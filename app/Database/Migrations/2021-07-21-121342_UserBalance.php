<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserBalance extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_balance_id' => [
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
			'amount' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'type' => [
				'type' => 'ENUM',
				'constraint' => ['in', 'out'],
				'default' => 'in',
			],
			'notes' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
		]);
		$this->forge->addPrimaryKey('user_balance_id');
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('user_balance', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		$seeder = \Config\Database::seeder();
		$seeder->call('UserBalance');
	}

	public function down()
	{
		$this->forge->dropTable('user_balance');
	}

}
