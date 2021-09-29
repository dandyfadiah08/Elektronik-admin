<?php

namespace App\Controllers;

use App\Models\DeviceChecks;

class Dashboard extends BaseController
{
	protected $Admin, $AdminRole;

	public function index()
	{
		$this->data += [
			'page' => (object)[
				'key' => '1-dashboard',
				'title' => 'Dashboard',
				'subtitle' => 'Dashboard',
				'navbar' => '',
			],
		];

		return view('dashboard/index', $this->data);
	}

	public function tabs()
	{
		$this->data += [
			'page' => (object)[
				'key' => '1-tabs',
				'title' => 'Tabs',
				'subtitle' => 'Multiple Tabs',
			]
		];

		return view('dashboard/tabs', $this->data);
	}

	public function email()
	{
		// return redirect()->to(base_url());
		// testing email view
		$this->DeviceCheck = new DeviceChecks();
		$select = 'dc.check_id,check_detail_id,status_internal,user_payout_detail_id';
		// $select for email
		$select .= ',check_code,brand,model,storage,imei,dc.type as dc_type,u.name,customer_name,customer_email,dcd.account_number,dcd.account_name,pm.name as pm_name,ub.notes as ub_notes,ub.type as ub_type,ub.currency,ub.currency_amount,check_code as referrence_number';
		$where = ['dc.check_id' => 41, 'dc.deleted_at' => null];
		$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
		helper('number');

		$data = [
			'd' => $device_check, 
		];

		return view('email/payment_success', $data);
	}

	public function update_token()
	{
		helper('rest_api');
		$response = initResponse('Not Authorized.');
		if (session()->has('admin_id')) {
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
		if (session()->has('admin_id')) {
			$token = $this->request->getPost('token');
			$admin = $this->Admin->getAdmin(['admin_id' => session()->admin_id], 'token_notification');
			$response->message = 'Your web notification is not working.<br>Please <b class="text-primary">Reset Web Notification</b>';
			if ($admin) {
				if ($admin->token_notification == $token) {
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
		if (session()->has('admin_id')) {
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
