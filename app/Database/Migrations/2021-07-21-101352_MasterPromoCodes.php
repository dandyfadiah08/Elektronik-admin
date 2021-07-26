<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterPromoCodes extends Migration
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
			'promo_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'null' => true,
			],
			'code' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'status' => [
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '1',
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
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('promo_id', 'master_promos', 'promo_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('master_promo_codes', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('PromoCodes');
	}

	public function down()
	{
		$this->forge->dropTable('master_promo_codes');
	}
}
