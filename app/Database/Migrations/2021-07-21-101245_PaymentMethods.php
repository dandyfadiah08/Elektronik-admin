<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PaymentMehods extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'payment_method_id' => [
				'type' => 'TINYINT',
				'constraint' => 3,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'type' => [
				'type' => 'ENUM',
				'constraint' => ['bank', 'emoney'],
				'default' => 'bank',
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'alias_name' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
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
				'constraint' => '100',
				'null' => true,
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);
		$this->forge->addPrimaryKey('payment_method_id');
		$attributes = ['ENGINE' => 'InnoDB'];
		$this->forge->createTable('payment_methods', true, $attributes);
		// produces: CREATE TABLE IF NOT EXISTS `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

		// insert data
		// $seeder = \Config\Database::seeder();
		// $seeder->call('PaymentMethods');
	}

	public function down()
	{
		$this->forge->dropTable('payment_methods');
	}
}
