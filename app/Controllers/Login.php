<?php

namespace App\Controllers;

use App\Models\adminsModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Login extends Controller
{
	use ResponseTrait;

	public function index()
	{
		if (session()->has('id_admin')) return redirect()->to(base_url('/dashboard'));
		return view('login/login', ['site_key' => env('google_recaptcha.site_key')]);
	}

	public function doLogin()
	{
		$response = (object)[
			'success'	=> false,
			'message'	=> 'No message',
		];
		if ($this->request->getMethod() === 'post') {
			if ($this->validate([
				'username'	=> 'required',
				'password_enk'	=> 'required',
			])) {
				// $recaptcha = 'salahslahsalah';
				$recaptcha = $this->request->getPost('recaptcha');
				// $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . env('google_recaptcha.secret_key') . '&response=' . $recaptcha);
				$client = \Config\Services::curlrequest();
				$verifyResponse = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
					'form_params' => [
						'secret' => env('google_recaptcha.secret_key'),
						'response' => $recaptcha
					]
				]);
				$responseData = json_decode($verifyResponse->getBody());
				if ($responseData->success) {
					$username = $this->request->getPost('username');
					$password_enk = base64_decode($this->request->getPost('password_enk'));
					$model = new adminsModel();
					$admins = $model->getadmin(['username' => $username], 'password_enk,username,id_admin,id_role,status');
					if ($admins) {
						$encrypter = \Config\Services::encrypter();
						$password_decrypted =  $encrypter->decrypt(hex2bin($admins->password_enk));
						if ($password_enk == $password_decrypted) {
							if ($admins->status == 'active') {
								// implement session here
								// $session = session();
								// $this->session = \Config\Services::session();
								// $this->session = 
								$payload = [
									'id_admin'	=> $admins->id_admin,
									'username'	=> $admins->username,
									'id_role'	=> $admins->id_role,
								];
								session()->set($payload);
								$jwt = new JWT();
								$payload = $payload;
								$token = $jwt->encode($payload, $_ENV['jwt.key']);
								// var_dump($token);die;

								$response->message = "Success. Logged as $username!";
								$response->success = true;
							} else {
								$response->message = "Login failed. Account is disabled/inactive!";
							}
						} else {
							$response->message = "Login failed. Password incorrect!";
						}
					} else {
						$response->message = "Username not found!";
					}
				} else {
					$response->message = 'Invalid captcha!';
					$response->data = $responseData;
				}
			} else {
				$response->message = 'Username & password can not be blank!';
			}
		} else {
			$response->message = 'Unknown method!';
		}
		return $this->respond($response);
	}

	public function test($input = '')
	{
		header('location: /');
		exit();
		$model = new adminsModel();
		// $admins = $model->getadmins(2);
		$admins = $model->getadmins(['username' => 'edi']);
		$encrypter = \Config\Services::encrypter();
		$plainText = 'edi';
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
}
