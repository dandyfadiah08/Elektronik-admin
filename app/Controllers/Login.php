<?php

namespace App\Controllers;

use App\Models\AdminModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class Login extends Controller
{
	use ResponseTrait;
	
	public function index()
	{
		return view('login');
	}

	public function test($input = '') {
		$model = new AdminModel();
		// $admin = $model->getAdmin(2);
		$admin = $model->getAdmin(['username' =>'master']);
		var_dump($admin);die;
		$encrypter = \Config\Services::encrypter();

		$plainText = 'master';
		// $encoded = bin2hex(\CodeIgniter\Encryption\Encryption::createKey(32));
		// echo $encoded;
		$ciphertext = $encrypter->encrypt($plainText);

		// Outputs: This is a plain-text message!
		echo $encrypter->decrypt($ciphertext);
		$saved_passwords = bin2hex($ciphertext);
		echo "<br>$saved_passwords<br>";
		echo $encrypter->decrypt(hex2bin($saved_passwords));
		// $words = str_split($input);
		// // var_dump($words);die;
		// $output = ''; 
		// for($i=0; $i<count($words); $i++) {
		// 	$char = $words[$i];
		// 	$output .= dechex(ord($char));
		// } 
		// echo strlen($output) .' - '.$output;
	}

	public function doLogin()
	{
		$response = (object)[
			'success'	=> false,
			'message'	=> 'No message',
		];
		if ($this->request->getMethod() === 'post') {
			if($this->validate([
				'username'	=> 'required',
				'password'	=> 'required',
			])) {
				$username = $this->request->getPost('username');
				$password = $this->request->getPost('password');
				$model = new AdminModel();
				$admin = $model->getAdmin(['username' =>$username]);
				if($admin) {
					$encrypter = \Config\Services::encrypter();
					$password_decrypted =  $encrypter->decrypt(hex2bin($admin->password));
					if($password == $password_decrypted) {
						// implement session here
						// $session = session();
						// $this->session = \Config\Services::session();
						$this->session = session();
						$this->session->start();
						$this->session->set([
							'admin_id'	=> $admin->admin_id,
							'username'	=> $admin->username,
							'role_id'	=> $admin->role_id,
						]);

						$response->message = "Success. Logged as $username!";
						$response->success = true;
					} else {
						$response->message = "Login failed!";
					}
				} else {
					$response->message = "Username not found!";
				}
				return $this->respond($response, 200);
			} else {
				$response->message = 'Username & password can not be blank!';
				return $this->respond($response, 200);
			}
		} else {
			$response->message = 'Unknown method!';
			return $this->respond($response, 200);
		}
		
	}
}
