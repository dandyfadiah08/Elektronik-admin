<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeviceChecks extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'check_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'check_code' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'key_code' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'imei' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
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
			'os' => [
				'type' => 'VARCHAR',
				'constraint' => '64',
			],
			'promo_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'null' => true,
			],
			'price_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'null' => true,
			],
			'price' => [
				'type' => 'INT',
				'constraint' => 12,
				'unsigned' => true,
				'null' => true,
			],
			'grade' => [
				'type' => 'VARCHAR',
				'constraint' => '14',
				'null' => true,
			],
			'status' => [
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '1',
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
		$this->forge->addPrimaryKey('check_id');
		$this->forge->addForeignKey('promo_id', 'master_promos', 'promo_id');
		$this->forge->addForeignKey('price_id', 'master_prices', 'price_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('device_check', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('DeviceChecks');
	}

	public function down()
	{
		$this->forge->dropTable('device_check');
	}
}
