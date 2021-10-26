<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'nik' => [
				'type' => 'VARCHAR',
				'constraint' => '16',
			],
			'phone_no' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'email' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['pending', 'active', 'inactive', 'banned'],
				'default' => 'pending',
			],
			'type' => [
				'type' => 'ENUM',
				'constraint' => ['agent', 'nonagent'],
				'default' => 'agent',
			],
			'ref_code' => [
				'type' => 'VARCHAR',
				'constraint' => '32',
			],
			'pending_balance' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'default' => '0',
			],
			'active_balance' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'default' => '0',
			],
			'photo_id' => [
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
			],
		]);
		$this->forge->addPrimaryKey('user_id');
		$this->forge->addUniqueKey('nik');
		$this->forge->addUniqueKey('phone_no');
		$this->forge->addUniqueKey('email');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('users', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('Users');
		
	}

	public function down()
	{
		$this->forge->dropTable('users');
	}
}
