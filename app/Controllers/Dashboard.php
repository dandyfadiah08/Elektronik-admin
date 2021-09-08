<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\AdminsModel;

class Dashboard extends BaseController
{
	use ResponseTrait;

	protected $Admin;

	public function __construct() {
		$this->Admin = new AdminsModel();
	}

	public function index()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$data = [
			'page' => (object)[
				'key' => '1-dashboard',
				'title' => 'Dashboard',
				'subtitle' => 'Welcome to '.env('app.name').' Dashboard',
				'control_sidebar' => 'Content control sidebar goes here',
			],
			'admin' => $this->Admin->find(session()->admin_id),
		];

		return view('dashboard/index', $data);
	}

	public function tabs()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$data = [
			'page' => (object)[
				'key' => '1-tabs',
				'title' => 'Tabs',
				'subtitle' => 'Multiple Tabs',
			],
			'admin' => $this->Admin->find(session()->admin_id),
		];

		return view('dashboard/tabs', $data);
	}

	public function update_token()
	{
		helper('rest_api');
		$response = initResponse('Not Authorized.');
		if(session()->has('admin_id')) {
			$token = $this->request->getPost('token');
			$admin_id = session()->get('admin_id');
			$this->Admin->update($admin_id, ['token_notification' => $token]);
			$response->success = true;
			$response->message = 'Success';
		}
		// echo json_encode($response);
		return $this->respond($response, 200);
	}

	public function check_notification_token()
	{
		helper('rest_api');
		$response = initResponse('Not Authorized.');
		if(session()->has('admin_id')) {
			$token = $this->request->getPost('token');
			$admin = $this->Admin->getAdmin(['admin_id' => session()->admin_id], 'token_notification');
			$response->message = 'Your web notification is not working.<br>Please <b class="text-primary">Reset Web Notification</b>';
			if($admin) {
				if($admin->token_notification == $token) {
					$response->success = true;
					$response->message = 'Your web notification is working fine';
				}
			}
		}
		return $this->respond($response, 200);
	}

	public function reset_notification_token()
	{
		helper('rest_api');
		$response = initResponse('Not Authorized.');
		if(session()->has('admin_id')) {
			$this->Admin->update(session()->admin_id, ['token_notification' => null]);
			$response->success = true;
			$response->message = 'Success';
		}
		return $this->respond($response, 200);
	}

	public function logout()
	{
		$this->Admin->update(session()->admin_id, ['token_notification' => null]);
		session()->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}

}
