<<<<<<< HEAD
<?php

namespace App\Controllers;

use App\Models\AdminsModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Login extends Controller
{
	use ResponseTrait;
	
	public function index()
	{
		if(session()->has('admin_id')) return redirect()->to(base_url('/dashboard'));
		return view('login/login', ['site_key' => env('google_recaptcha.site_key')]);
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
					$password = base64_decode($this->request->getPost('password'));
					$model = new AdminsModel();
					$admin = $model->getAdmin(['username' =>$username], 'password,username,admin_id,role_id,status');
					if($admin) {
						$encrypter = \Config\Services::encrypter();
						$password_decrypted =  $encrypter->decrypt(hex2bin($admin->password));
						if($password == $password_decrypted) {
							if($admin->status == 'active') {
								// implement session here
								// $session = session();
								// $this->session = \Config\Services::session();
								// $this->session = 
								$payload = [
									'admin_id'	=> $admin->admin_id,
									'username'	=> $admin->username,
									'role_id'	=> $admin->role_id,
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

	public function test($input = '') {
		header('location: /');
		exit();
		$model = new AdminsModel();
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

}
=======
<?php

namespace App\Controllers;

use App\Models\AdminsModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Login extends Controller
{
	use ResponseTrait;
	
	public function index()
	{
		if(session()->has('admin_id')) return redirect()->to(base_url('/dashboard'));
		return view('login/login', ['site_key' => env('google_recaptcha.site_key')]);
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
					$password = base64_decode($this->request->getPost('password'));
					$model = new AdminsModel();
					$admin = $model->getAdmin(['username' =>$username], 'password,username,admin_id,role_id,status');
					if($admin) {
						$encrypter = \Config\Services::encrypter();
						$password_decrypted =  $encrypter->decrypt(hex2bin($admin->password));
						if($password == $password_decrypted) {
							if($admin->status == 'active') {
								// implement session here
								// $session = session();
								// $this->session = \Config\Services::session();
								// $this->session = 
								$payload = [
									'admin_id'	=> $admin->admin_id,
									'username'	=> $admin->username,
									'role_id'	=> $admin->role_id,
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

	public function test($input = '') {
		header('location: /');
		exit();
		$model = new AdminsModel();
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

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
