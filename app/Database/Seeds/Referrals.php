<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Referrals extends Seeder
{
	public function run()
	{
		// user_id=1 refs user_id=2
		$data[] = [
			'parent_id'			=> '1',
			'child_id'			=> '2',
			'ref_level'			=> '1',
			'status'			=> 'active',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
		];

		// user_id=2 refs user_id=3
		$data[] = [
			'parent_id'			=> '2',
			'child_id'			=> '3',
			'ref_level'			=> '1',
			'status'			=> 'active',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
		];
		$data[] = [
			'parent_id'			=> '1',
			'child_id'			=> '3',
			'ref_level'			=> '2',
			'status'			=> 'active',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
		];

		// user_id=3 refs user_id=4
		$data[] = [
			'parent_id'			=> '3',
			'child_id'			=> '4',
			'ref_level'			=> '1',
			'status'			=> 'active',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
		];
		$data[] = [
			'parent_id'			=> '2',
			'child_id'			=> '4',
			'ref_level'			=> '2',
			'status'			=> 'active',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
		];

		// Using Query Builder
		$this->db->table('referrals')->insertBatch($data);
	}
}
