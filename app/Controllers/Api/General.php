<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\MasterAddress;
use App\Models\PaymentMethods;
use App\Models\Users as UserModel;

class General extends BaseController
{
	use ResponseTrait;
	protected $request;

	public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->MasterAddress = new MasterAddress();
		$this->PaymentMethod = new PaymentMethods();
		helper('validation');
        helper('rest_api');
		
    }

	public function index()
	{
		//
	}

	public function getProvinces(){
		$response = initResponse();
		$dataProvinces = $this->MasterAddress->getProvinces();
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}

	public function getCities(){
		$response = initResponse();
		$provinceId = $this->request->getPost('province_id') ?? '';
		$dataProvinces = $this->MasterAddress->getCities(['province_id' => $provinceId]);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}

	public function getDistrict(){
		$response = initResponse();
		$cityId = $this->request->getPost('city_id') ?? '';
		$dataProvinces = $this->MasterAddress->getDistrict(['city_id' => $cityId]);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}

	public function getPaymentMethod(){
		$response = initResponse();
		$type = $this->request->getPost('type') ?? 'default';
		$where = [
			'deleted_at' => null
		];
		if($type != 'default'){
			$where += [
				'type' => $type
			];
		}
		$dataProvinces = $this->PaymentMethod->getPaymentMethod($where);
		// var_dump($this->db->getLastQuery());die;
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}
}
