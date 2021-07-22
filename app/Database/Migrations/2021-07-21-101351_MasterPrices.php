<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterPrices extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'price_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'promo_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],
			'brand' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'model' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'type' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'storage' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'price_s' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'price_a' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'price_b' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'price_c' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'price_d' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],
			'price_e' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
			],

			'initial_quota' => [
				'type' => 'TINYINT',
				'constraint' => 4,
				'default' => '0',
			],
			'quota' => [
				'type' => 'TINYINT',
				'constraint' => 4,
				'default' => '0',
			],
			'used_quota' => [
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '0',
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
		$this->forge->addPrimaryKey('price_id');
		$this->forge->addForeignKey('promo_id', 'master_promos', 'promo_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('master_prices', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		$seeder = \Config\Database::seeder();
		$seeder->call('MasterPrices');
	}

	public function down()
	{
		$this->forge->dropTable('master_prices');
	}
}
