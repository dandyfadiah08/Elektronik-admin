<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterPromos extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'promo_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'promo_name' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'start_date' => [
				'type' => 'DATE',
				'null' => true,
			],
			'end_date' => [
				'type' => 'DATE',
				'null' => true,
			],
			'codes' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'quota' => [
				'type' => 'ENUM',
				'constraint' => ['y','n'],
				'default' => 'n'
			],
			'quota_type' => [
				'type' => 'ENUM',
				'constraint' => ['promo','type'],
				'default' => 'promo'
			],
			'initial_quota' => [
				'type' => 'TINYINT',
				'constraint' => 4,
				'default' => '0',
			],
			'quota_value' => [
				'type' => 'TINYINT',
				'constraint' => 4,
				'default' => '0',
			],
			'used_quota' => [
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '0',
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
		$this->forge->addPrimaryKey('promo_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('master_promos', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('MasterPromos');
	}

	public function down()
	{
		$this->forge->dropTable('master_promos');
	}
}
