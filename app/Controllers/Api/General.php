<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\MasterAddress;
use App\Models\PaymentMethods;
use App\Models\Settings;
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
		$this->Setting = new Settings();
		helper('validation');
        helper('redis');
		
    }

	public function index()
	{
		//
	}

	public function getProvinces(){
		$response = initResponse();
		$dataProvinces = $this->MasterAddress->getProvinces(['status' => 'active']);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}

	public function getCities(){
		$response = initResponse();
		$provinceId = $this->request->getPost('province_id') ?? '';
		$dataProvinces = $this->MasterAddress->getCities(['province_id' => $provinceId, 'status' => 'active']);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response, 200);
	}

	public function getDistrict(){
		$response = initResponse();
		$cityId = $this->request->getPost('city_id') ?? '';
		$dataProvinces = $this->MasterAddress->getDistrict(['city_id' => $cityId, 'status' => 'active']);
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
				'type' => $type,
				'status' => 'active'
			];
		}
		$data = $this->PaymentMethod->getPaymentMethods($where, 'payment_method_id,type,name,alias_name');
		// var_dump($this->db->getLastQuery());die;
		$response->message = "Success";
		$response->success = true;
		$response->data = $data;
		return $this->respond($response, 200);
	}

	public function get_tnc_app_1()
    {
        $response = initResponse('Success', true);
        $tnc = 1;
		$setting_db = $this->Setting->getSetting(['_key' => 'tnc_app1'], 'setting_id,val');
		$tnc = $setting_db->val;
        $response->data = ['tnc' => $tnc];
        return $this->respond($response, 200);
    }

	public function get_tnc_app_2()
    {
        $response = initResponse('Success', true);
        $tnc = 1;
		$setting_db = $this->Setting->getSetting(['_key' => 'tnc_app2'], 'setting_id,val');
		$tnc = $setting_db->val;
        $response->data = ['tnc' => $tnc];
        return $this->respond($response, 200);
    }

	public function get_cekImei()
    {
		$response = initResponse('Success', true);
        $url_imei = 1;
        $key = 'cek:imei';
        try {
            $redis = RedisConnect();
            $url_imei = $redis->get($key);
            if ($url_imei === FALSE) {
                
                $setting_db = $this->Setting->getSetting(['_key' => 'url_imei'], 'setting_id,val');
                $url_imei = $setting_db->val;
                $redis->set($key, $url_imei);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            
            $setting_db = $this->Setting->getSetting(['_key' => 'url_imei'], 'setting_id,val');
            $url_imei = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->set($key, $url_imei);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['url_imei' => $url_imei];
        return $this->respond($response, 200);
    }

}
