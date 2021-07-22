<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserBalance extends Seeder
{
	public function run()
	{
		$data = [
			'user_id'			=> '1',
			'currency'			=> 'idr',
			'currency_amount'	=> '1500000',
			'convertion'		=> '1',
			'amount'			=> '1500000',
			'type'				=> 'transaction',
			'cashflow'			=> 'in',
			'notes'				=> 'Sell Device Income',
			'check_id'			=> '1',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now(),
		];
		$this->db->table('user_balance')->insert($data);

		$data = [
			'user_id'			=> '1',
			'currency'			=> 'idr',
			'currency_amount'	=> '500000',
			'convertion'		=> '1',
			'amount'			=> '500000',
			'type'				=> 'bonus',
			'cashflow'			=> 'in',
			'notes'				=> 'Bonus',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now(),
		];
		$this->db->table('user_balance')->insert($data);

		$data = [
			'user_id'			=> '1',
			'currency'			=> 'idr',
			'currency_amount'	=> '1500000',
			'convertion'		=> '1',
			'amount'			=> '1500000',
			'type'				=> 'transaction',
			'cashflow'			=> 'out',
			'notes'				=> 'Sell Device Transfer',
			'check_id'			=> '1',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now(),
		];
		$this->db->table('user_balance')->insert($data);

		$data = [
			'user_id'			=> '1',
			'currency'			=> 'idr',
			'currency_amount'	=> '1500000',
			'convertion'		=> '1',
			'amount'			=> '500000',
			'type'				=> 'bonus',
			'cashflow'			=> 'out',
			'notes'				=> 'Withdraw balance',
			'created_at'		=> Time::now(),
			'updated_at'		=> Time::now(),
		];
		$this->db->table('user_balance')->insert($data);
	}
}
