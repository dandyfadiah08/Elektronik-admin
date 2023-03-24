<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class Admins extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'admin_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'username' => [
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
			'password' => [
				'type' => 'TEXT',
			],
			'role_id' => [
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
				'default' => '1',
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['active', 'inactive'],
				'default' => 'active',
			],
			'created_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100'
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
			'updated_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100'
			],
			'updated_at' => [
				'type' => 'DATETIME',
			],
			'deleted_by' => [
				'type' => 'VARCHAR',
				'constraint' => '100'
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);
		$this->forge->addPrimaryKey('admin_id');
		$this->forge->addUniqueKey('username');
		$this->forge->addUniqueKey('email');
		$this->forge->addForeignKey('id_role', 'admin_roles', 'id_role');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('admins', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('Admins');
	}

	public function down()
	{
		$this->forge->dropTable('admins');
	}
}
