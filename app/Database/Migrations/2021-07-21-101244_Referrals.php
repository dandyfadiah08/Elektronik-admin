<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Referrals extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'parent_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'child_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'ref_level' => [
				'type' => 'TINYINT',
				'constraint' => '2',
				'default' => '1',
				'unsigned' => true,
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['pending', 'active', 'inactive', 'banned'],
				'default' => 'pending',
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
			'updated_at' => [
				'type' => 'DATETIME',
			]
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('parent_id','users','user_id','CASCADE','CASCADE');
		$this->forge->addForeignKey('child_id','users','user_id','CASCADE','CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('referrals', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('Referrals');

	}

	public function down()
	{
		$this->forge->dropTable('referrals');
	}
}
