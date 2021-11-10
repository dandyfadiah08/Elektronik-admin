<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\MasterAddress;
use App\Models\MerchantModel;
use App\Models\PaymentMethods;
use App\Models\Settings;
use App\Models\SettingTnc;
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
        $this->SettingTnc = new SettingTnc();
		helper('validation');
        helper('redis');
		
    }

	public function index()
	{
		$response = initResponse();
		return $this->respond($response);
	}

	public function getProvinces(){
		$response = initResponse();
		$dataProvinces = $this->MasterAddress->getProvinces(['status' => 'active']);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response);
	}

	public function getCities(){
		$response = initResponse();
		$provinceId = $this->request->getPost('province_id') ?? '';
		$dataProvinces = $this->MasterAddress->getCities(['province_id' => $provinceId, 'status' => 'active']);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response);
	}

	public function getDistrict(){
		$response = initResponse();
		$cityId = $this->request->getPost('city_id') ?? '';
		$dataProvinces = $this->MasterAddress->getDistrict(['city_id' => $cityId, 'status' => 'active']);
		$response->message = "Success";
		$response->success = true;
		$response->data = $dataProvinces;
		return $this->respond($response);
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
		return $this->respond($response);
	}

	public function get_tnc_app_1()
    {
        $response = initResponse('Success', true);
        $tnc = 'Terms & Conditions';
		$setting_db = $this->SettingTnc->getSetting(['_key' => 'tnc_app1'], 'val');
		$tnc = $setting_db->val;
        $response->data = ['tnc' => $tnc];
        return $this->respond($response);
    }

	public function get_tnc_app_2()
    {
        $response = initResponse('Success', true);
        $tnc = 'Terms & Conditions';
		$setting_db = $this->SettingTnc->getSetting(['_key' => 'tnc_app2'], 'val');
		$tnc = $setting_db->val;
        $response->data = ['tnc' => $tnc];
        return $this->respond($response);
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
                $redis->setex($key, 3600, $url_imei);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            
            $setting_db = $this->Setting->getSetting(['_key' => 'url_imei'], 'setting_id,val');
            $url_imei = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $url_imei);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['url_imei' => $url_imei];
        return $this->respond($response);
    }

	public function chat_app1()
    {
        $url_chat = "https://www.google.com/";
        $key = 'setting:chat_app1';
        try {
            $redis = RedisConnect();
            $url_chat = $redis->get($key);
            if ($url_chat === FALSE) {
                
                $setting_db = $this->Setting->getSetting(['_key' => 'chat_app1'], 'setting_id,val');
                $url_chat = $setting_db->val;
                $redis->setex($key, 3600, $url_chat);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            
            $setting_db = $this->Setting->getSetting(['_key' => 'chat_app1'], 'setting_id,val');
            $url_chat = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $url_chat);
            } catch (\Exception $e) {
            }
        }
		header("Location: $url_chat");
		exit();
    }

	public function chat_app2()
    {
        $url_chat = "https://www.google.com/";
        $key = 'setting:chat_app2';
        try {
            $redis = RedisConnect();
            $url_chat = $redis->get($key);
            if ($url_chat === FALSE) {
                
                $setting_db = $this->Setting->getSetting(['_key' => 'chat_app2'], 'setting_id,val');
                $url_chat = $setting_db->val;
                $redis->setex($key, 3600, $url_chat);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            
            $setting_db = $this->Setting->getSetting(['_key' => 'chat_app2'], 'setting_id,val');
            $url_chat = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $url_chat);
            } catch (\Exception $e) {
            }
        }
        header("Location: $url_chat");
		exit();
    }

	public function chat_app3()
    {
        $url_chat = "https://www.google.com/";
        $key = 'setting:chat_app3';
        try {
            $redis = RedisConnect();
            $url_chat = $redis->get($key);
            if ($url_chat === FALSE) {
                
                $setting_db = $this->Setting->getSetting(['_key' => 'chat_app3'], 'setting_id,val');
                $url_chat = $setting_db->val;
                $redis->setex($key, 3600, $url_chat);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            
            $setting_db = $this->Setting->getSetting(['_key' => 'chat_app3'], 'setting_id,val');
            $url_chat = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $url_chat);
            } catch (\Exception $e) {
            }
        }
        header("Location: $url_chat");
		exit();
    }

    public function tnc1_web(){
		$where = [
			'_key'	=> 'tnc_app1',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
		$val = $dataSetting->val;
		$data = [
			'val' 	=> $val,
			'title'	=> "",
		];
        $dataUpdate = [
            'count' => ($dataSetting->count + 1),
        ];
        $this->SettingTnc->saveUpdate($where, $dataUpdate);
		return view('setting/webview', $data);
	}

    public function tnc2_web(){
		$where = [
			'_key'	=> 'tnc_app2',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
		$val = $dataSetting->val;
		$data = [
			'val' 	=> $val,
			'title'	=> "",
		];
        $dataUpdate = [
            'count' => ($dataSetting->count + 1),
        ];
        $this->SettingTnc->saveUpdate($where, $dataUpdate);
		return view('setting/webview', $data);
	}

    public function tnc3_web(){
		$where = [
			'_key'	=> 'tnc_app3',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
		$val = $dataSetting->val;
		$data = [
			'val' 	=> $val,
			'title'	=> "",
		];
        $dataUpdate = [
            'count' => ($dataSetting->count + 1),
        ];
        $this->SettingTnc->saveUpdate($where, $dataUpdate);
		return view('setting/webview', $data);
	}

    public function tnc2_web_bonus(){
		$where = [
			'_key'	=> 'bonus_tnc_app2',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
		$val = $dataSetting->val;
		$data = [
			'val' 	=> $val,
			'title'	=> "",
		];
        $dataUpdate = [
            'count' => ($dataSetting->count + 1),
        ];
        $this->SettingTnc->saveUpdate($where, $dataUpdate);
		return view('setting/webview', $data);
	}

    public function tnc2_web_bonus_short(){
		$where = [
			'_key'	=> 'short_bonus_tnc_app2',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
		$val = $dataSetting->val;
		$data = [
			'val' 	=> $val,
			'title'	=> "",
		];
        $dataUpdate = [
            'count' => ($dataSetting->count + 1),
        ];
        $this->SettingTnc->saveUpdate($where, $dataUpdate);
		return view('setting/webview', $data);
	}

    public function app3_merchant_code_help(){
        $response = initResponse('Success', true);
		$where = [
			'_key'	=> 'app3_merchant_code_help',
		];
		$dataSetting = $this->SettingTnc->getSetting($where, 'val,count');
        $this->SettingTnc->saveUpdate($where, ['count' => ($dataSetting->count + 1)]); // update counter
        $response->data = ['content' => $dataSetting->val];
        return $this->respond($response);
	}
	
    public function get_version_app_1()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app1';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app1'], 'val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app1'], 'val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

    public function get_version_app_1_ios()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app1_ios';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app1_ios'], 'val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app1_ios'], 'val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

	public function get_version_app_2()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app2';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app2'], 'val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app2'], 'val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

    public function get_version_app_2_ios()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app2_ios';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app2_ios'], 'val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app2_ios'], 'val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

	public function get_version_app_3()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app_3';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app3'], 'val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app3'], 'val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

    public function get_version_app_3_ios()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app3_ios';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {
                $setting_db = $this->Setting->getSetting(['_key' => 'version_app3_ios'], 'setting_id,val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            $setting_db = $this->Setting->getSetting(['_key' => 'version_app3_ios'], 'setting_id,val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
        $response->data = ['version' => $version];
        return $this->respond($response);
    }

    public function get_merchant_code_help()
    {
        $response = initResponse('Success', true);
        $content = "Content";
        $key = 'setting:app3_merchant_code_help';
        try {
            $redis = RedisConnect();
            $content = $redis->get($key);
            if ($content === FALSE) {
                $setting_db = $this->SettingTnc->getSetting(['_key' => 'app3_merchant_code_help'], 'val');
                $content = $setting_db->val;
                $redis->setex($key, 3600, $content);
            }
        } catch (\Exception $e) {
            $setting_db = $this->SettingTnc->getSetting(['_key' => 'app3_merchant_code_help'], 'setting_id,val');
            $content = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $content);
            } catch (\Exception $e) {
            }
        }
        $content = (int)$content;
        $response->data = ['content' => $content];
        return $this->respond($response);
    }

	function checkMerchant()
    {
		$response = initResponse('Merchant Code is invalid.');
		$merchant_code = $this->request->getPost('merchant_code') ?? '';
		if(empty($merchant_code)) {
			$response->message = "Merchant Code is required.";
		} else {
			$this->Merchant = new MerchantModel();
			$merchant = $this->Merchant->getMerchant(['merchant_code' => $merchant_code, 'status' => 'active', 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
			if($merchant) {
				$response->message = "Success";
				$response->success = true;
				$response->data = $merchant;
			}
		}
			return $this->respond($response);
    }

}
