<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddressProvinces extends Seeder
{
	public function run()
	{
		$data = [
			['province_id' => '11', 'name' => 'ACEH'],
			['province_id' => '12', 'name' => 'SUMATERA UTARA'],
			['province_id' => '13', 'name' => 'SUMATERA BARAT'],
			['province_id' => '14', 'name' => 'RIAU'],
			['province_id' => '15', 'name' => 'JAMBI'],
			['province_id' => '16', 'name' => 'SUMATERA SELATAN'],
			['province_id' => '17', 'name' => 'BENGKULU'],
			['province_id' => '18', 'name' => 'LAMPUNG'],
			['province_id' => '19', 'name' => 'KEPULAUAN BANGKA BELITUNG'],
			['province_id' => '21', 'name' => 'KEPULAUAN RIAU'],
			['province_id' => '31', 'name' => 'DKI JAKARTA'],
			['province_id' => '32', 'name' => 'JAWA BARAT'],
			['province_id' => '33', 'name' => 'JAWA TENGAH'],
			['province_id' => '34', 'name' => 'DI YOGYAKARTA'],
			['province_id' => '35', 'name' => 'JAWA TIMUR'],
			['province_id' => '36', 'name' => 'BANTEN'],
			['province_id' => '51', 'name' => 'BALI'],
			['province_id' => '52', 'name' => 'NUSA TENGGARA BARAT'],
			['province_id' => '53', 'name' => 'NUSA TENGGARA TIMUR'],
			['province_id' => '61', 'name' => 'KALIMANTAN BARAT'],
			['province_id' => '62', 'name' => 'KALIMANTAN TENGAH'],
			['province_id' => '63', 'name' => 'KALIMANTAN SELATAN'],
			['province_id' => '64', 'name' => 'KALIMANTAN TIMUR'],
			['province_id' => '65', 'name' => 'KALIMANTAN UTARA'],
			['province_id' => '71', 'name' => 'SULAWESI UTARA'],
			['province_id' => '72', 'name' => 'SULAWESI TENGAH'],
			['province_id' => '73', 'name' => 'SULAWESI SELATAN'],
			['province_id' => '74', 'name' => 'SULAWESI TENGGARA'],
			['province_id' => '75', 'name' => 'GORONTALO'],
			['province_id' => '76', 'name' => 'SULAWESI BARAT'],
			['province_id' => '81', 'name' => 'MALUKU'],
			['province_id' => '82', 'name' => 'MALUKU UTARA'],
			['province_id' => '91', 'name' => 'PAPUA BARAT'],
			['province_id' => '94', 'name' => 'PAPUA'],
		];
		$this->db->table('address_provinces')->insertBatch($data);
	}
}