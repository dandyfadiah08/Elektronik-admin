<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class AdminRoles extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'role_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'role_name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['active', 'inactive'],
				'default' => 'active',
			],
			'r_admin' => [
				'type' => 'ENUM',
				'constraint' => ['y', 'n'],
				'default' => 'y',
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
				'constraint' => '100',
				'null' => true,
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);
		$this->forge->addPrimaryKey('role_id');
		$this->forge->addUniqueKey('role_name');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('admin_roles', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		$seeder = \Config\Database::seeder();
		$seeder->call('AdminRoles');
	}

	public function down()
	{
		$this->forge->dropTable('admin_roles');
	}
}
