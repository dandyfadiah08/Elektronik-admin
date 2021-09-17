<?php

namespace App\Controllers;

use App\Models\Settings;

class Google_authenticator extends BaseController
{
	protected $Setting,$username,$salt,$google,$secret;

	public function __construct() {
		$this->Setting = new Settings();
		$this->google = new \Google\Authenticator\GoogleAuthenticator();
		$setting = $this->Setting->getSetting(['_key' => '2fa_secret'], 'setting_id,val');
		if($setting) {
			if($setting->val == '') {
				$this->secret = $this->google->generateSecret();
				$this->Setting->update($setting->setting_id, ['val' => $this->secret]);
			} else {
				$this->secret = $setting->val;
			}
		} else {
			$this->secret = $this->google->generateSecret();
			$this->Setting->update($setting->setting_id, ['val' => $this->secret]);
		}
		$this->username = 'Payment '.strtoupper(env('app.environment'));
		$this->salt = env('google_authenticator.salt');
	}

	public function index()
	{
		$check_role = checkRole($this->role, 'r_2fa');
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			// cek status 2fa
			$status_2fa = true;
			$image_url = '';
			$setting = $this->Setting->getSetting(['_key' => '2fa_status'], 'val');
			if($setting) {
				if($setting->val == 'n') {
					$image_url = $this->google->getURL($this->username, env('app.domain'), $this->secret);
					$status_2fa = false;
				}
			} else {
				$image_url = $this->google->getURL($this->username, env('app.domain'), $this->secret);
				$status_2fa = false;
			}



			$this->data += [
				'page' => (object)[
					'key' => '2-google_authenticator',
					'title' => 'Google Authenticator',
					'subtitle' => 'Setup',
					'navbar' => 'Google Authenticator',
				],
				'image_url' => $image_url,
				'status_2fa' => $status_2fa,
			];

			return view('google_authenticator/index', $this->data);
		}
	}


	function validate_2fa()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_2fa');
			if ($check_role->success) {
				$code = $this->request->getPost('code');
				$rules = ['code' => 'required'];
				if (!$this->validate($rules)) {
					$errors = $this->validator->getErrors();
					$response->message = "";
					foreach ($errors as $error) $response->message .= "$error ";
				} else {
					if ($this->google->checkCode($this->secret, $code)) {
						$response->success = true;
						$response->message = '2FA Code is valid';

						// update db
						$setting = $this->Setting->getSetting(['_key' => '2fa_status'], 'setting_id,val');
						if($setting) $this->Setting->update($setting->setting_id, ['val' => 'y']);
					} else {
						$response->message = '2FA Code is invalid!';
					}
				}
			}
		}
		return $this->respond($response);
	}

}
