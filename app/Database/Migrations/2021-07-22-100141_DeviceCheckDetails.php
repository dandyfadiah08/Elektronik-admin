<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeviceCheckDetails extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'check_detail_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'check_id' => [
				'type' => 'INT',
				'constraint' => 24,
				'unsigned' => true,
			],

			'quiz_1' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'null' => true,
			],
			'quiz_2' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'null' => true,
			],
			'quiz_3' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'null' => true,
			],
			'quiz_4' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'null' => true,
			],
			'screen' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'camera_back' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'camera_front' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'simcard' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'button_power' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'button_back' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'button_volume' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'cpu' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'harddisk' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'imei_registered' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],
			'fullset' => [
				'type' => 'TINYINT',
				'constraint' => '1',
			],

			'photo_id' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_1' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_2' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_3' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_4' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_5' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_device_6' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_fullset' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			],
			'photo_imei_registered' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
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
		$this->forge->addPrimaryKey('check_detail_id');
		$this->forge->addForeignKey('check_id', 'device_checks', 'check_id', 'CASCADE', 'CASCADE');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('device_check_details', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		$seeder = \Config\Database::seeder();
		$seeder->call('DeviceCheckDetails');
	}

	public function down()
	{
		$this->forge->dropTable('device_check_details');
	}
}
