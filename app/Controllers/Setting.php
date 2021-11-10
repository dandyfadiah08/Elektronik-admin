<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Log;
use App\Models\Settings;
use App\Models\SettingTnc;

class Setting extends BaseController
{

    public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->Settings = new Settings();
		$this->log = new Log();
		$this->SettingTnc = new SettingTnc();
		$this->db = \Config\Database::connect();

		helper('validation');
	}

    public function index()
    {
        $check_role = checkRole($this->role, 'r_change_setting'); 

		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
            helper('html');
			$dataSetting = $this->Settings->getAllSetting("*");
			$dataSettingTnc = $this->SettingTnc->getAllSetting("*");

			$newDataSetting = [];
			foreach ($dataSetting as $rowSetting) {
				$newDataSetting[$rowSetting->_key] = $rowSetting;
			}
			foreach ($dataSettingTnc as $rowSetting) {
				$newDataSetting[$rowSetting->_key] = $rowSetting;
			}
			$dataSetting = (Object)$newDataSetting;
			
            $this->data += [
				'page' => (object)[
					'key' => '2-setting',
					'title' => 'Setting',
					'subtitle' => 'Setting',
					'navbar' => 'Setting',
				],
				'dataSetting' => $dataSetting,
			];

			return view('setting/index', $this->data);
        }
    }

	public function save(){
		$response = initResponse('Unauthorized.');

		if (hasAccess($this->role, 'r_change_setting')) {
			$key = $this->request->getPost('_key');
			$val = $this->request->getPost('val');
			$title = $this->request->getPost('title');

			$data_update= [
				'val' => $val,
				'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => session()->get('username'),
			];
			$this->db->transStart();
			$this->Settings->saveUpdate(['_key' => $key],$data_update);
			$this->db->transComplete();
			$key = 'setting:' . $key;
			helper('redis');
			try {
				$redis = RedisConnect();
				$redis->setex($key, 3600, $val);
			} catch (\Exception $e) {
			}

			if ($this->db->transStatus() === FALSE) {
				// transaction has problems
				$response->message = "Failed to perform task! #stg01c";
			} else {
				$response->success = true;
				$response->message = "Success for Update Setting " . $title;
				$data_update += [
					'setting' => $key,
					'title' => $title,
				];
				if($key == "tnc_app1" || $key = "tnc_app2"){
					$data_update = [
						'setting' => $key,
						'title' => $title
					];
				}
				$log_cat = 26;
				$this->log->in(session()->username, $log_cat, json_encode($data_update));
			}
		}
		return $this->respond($response);
	}

	public function saveTnc(){
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_change_setting')) {
			$key = $this->request->getPost('_key');
			$val = $this->request->getPost('val');
			$title = $this->request->getPost('title');

			$data_update= [
				'val' => $val,
				'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => session()->get('username'),
			];
			$this->db->transStart();
			$this->SettingTnc->saveUpdate(['_key' => $key],$data_update);
			$this->db->transComplete();

			if ($this->db->transStatus() === FALSE) {
				// transaction has problems
				$response->message = "Failed to perform task! #stg02c";
			} else {
				$response->success = true;
				$response->message = "Success for Update Setting " . $title;
				$data_update = [
					'setting' => $key,
					'title' => $title
				];
				$log_cat = 26;
				$this->log->in(session()->username, $log_cat, json_encode($data_update));
			}
		}
		return $this->respond($response);
	}
	
}
