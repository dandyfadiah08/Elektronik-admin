<?php

namespace App\Controllers;

// use App\Models\DeviceChecks; // for testing
// use App\Models\UserPayouts; // for testing
use App\Libraries\Xendit;

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

		// payment success
		// $this->DeviceCheck = new DeviceChecks();
		// $select = 'dc.check_id,check_detail_id,status_internal,user_payout_detail_id';
		// // $select for email
		// $select .= ',check_code,brand,model,storage,imei,dc.type as dc_type,u.name,customer_name,customer_email,dcd.account_number,dcd.account_name,pm.name as pm_name,ub.notes as ub_notes,ub.type as ub_type,ub.currency,ub.currency_amount,check_code as referrence_number';
		// $where = ['dc.check_id' => 41, 'dc.deleted_at' => null];
		// $device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
		// helper('number');

		// $data = [
		// 	'template' => 'transaction_success', 
		// 	'd' => $device_check, 
		// ];

		// $select = 'ups.user_payout_id, ups.user_id, ups.amount, ups.type, ups.status AS status_user_payouts, upa.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, upa.account_number, upa.account_name, ups.created_at, ups.created_by, ups.updated_at, ups.updated_by, upd.status as upd_status, ub.user_balance_id, ups.withdraw_ref, upd.user_payout_detail_id';
		// // $select for email
		// $select .= ',u.name,u.name as customer_name,u.email as customer_email,upa.account_number,upa.account_name,pm.name as pm_name,ub.type as ub_type,ub.currency,ub.currency_amount,withdraw_ref as referrence_number';
		// $where = array('ups.user_payout_id ' => 54, 'ups.deleted_at' => null, 'ups.type' => 'withdraw');
		// $this->UserPayouts = new UserPayouts();
		// $user_payout = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select);
		// helper('number');

		// $data = [
		// 	'template' => 'withdraw_success', 
		// 	'd' => $user_payout, 
		// ];

		$data = [
			'template' => 'email_verification_link', 
			'd' => (object)[
				'name' => 'Fajar',
				'link' => base_url('verification/email/1234567890'),
			],
		];

		return view('email/template', $data);
		$data = [
			'template' => 'email', 
			'd' => initResponse('Berhasil', true),
		];

		return view('verification/template', $data);


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

	public function payment_gateway_balance()
	{
		$response = initResponse('Not Authorized.');
		$check_role = checkRole($this->role, 'r_withdraw');
		if (!$check_role->success) {
			$response->message = $check_role->message;
		} else {
			$Xendit = new Xendit();
			$result = $Xendit->balance();
			$balance = 0;
			if($result->success) {
				helper('number');
				$balance = $result->data->balance;
			}
			$response->success = true;
			$response->message = 'Success';
			$response->data['balance'] = $balance;
		}
		return $this->respond($response);
	}

	public function logout()
	{
		$this->Admin->update(session()->admin_id, ['token_notification' => null]);
		session()->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}
}
