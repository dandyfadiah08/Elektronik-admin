<?php

namespace App\Controllers;

use App\Libraries\PaymentsAndPayouts;
use App\Libraries\WithdrawAndPayouts;
use App\Libraries\Mailer;
use App\Models\Settings;
use App\Models\UserPayouts;
use App\Models\Users;

class Withdraw extends BaseController
{
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->UserPayouts = new UserPayouts();
		$this->Setting = new Settings();
		$this->google = new \Google\Authenticator\GoogleAuthenticator();

		helper('grade');
		helper('validation');
		helper('device_check_status');
		helper('user_balance_status');
	}

	public function index()
	{

		$check_role = checkRole($this->role, 'r_withdraw');

		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('format');

			// make filter status option 
			$status = getUserBalanceStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';

			$payoutStatusDetail = getPayoutDetailStatus(-1);
			// var_dump($payoutStatusDetail);die;
			$optionPayoutStatusDetail = '<option></option><option value="all">All</option>';

			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '" ' . ($key == 2 ? 'selected' : '') . '>' . $val . '</option>';
			}
			foreach ($payoutStatusDetail as $key => $val) {
				$optionPayoutStatusDetail .= '<option value="' . $key . '" ' . ($key == "PENDING" ? 'selected' : '') . '>' . $val . '</option>';
			}
			// var_dump($optionPayoutStatusDetail);die;

			$this->data += [
				'page' => (object)[
					'key' => '2-withdraw',
					'title' => 'Withdraw',
					'subtitle' => 'List',
					'navbar' => 'Withdraw',
				],
				'search' => $this->request->getGet('s') ? "'" . safe2js($this->request->getGet('s')) . "'" : 'null',
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionStatusPayment' => $optionPayoutStatusDetail,
			];

			return view('withdraw/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;

		$check_role = checkRole($this->role, 'r_withdraw');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->db = \Config\Database::connect();
			$this->table_name = 'user_payouts';
			$this->builder = $this->db
				->table("$this->table_name as upa")
				->join("user_payments as ups", "ups.user_payment_id = upa.user_payment_id", "left")
				->join("payment_methods as pm", "pm.payment_method_id = ups.payment_method_id", "left")
				->join("user_payout_details as upd", "upd.user_payout_id = upa.user_payout_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"pm.type",
				"pm_name",
				"ups.account_number",
				"ups.account_name",
				"upa.amount",
				"status_user_payouts",
			);
			// fields to search with
			$fields_search = array(
				"pm.name",
				"pm.alias_name",
				"ups.account_number",
				"ups.account_name",
				"upa.withdraw_ref",
			);
			// select fields
			$select_fields = 'upa.user_payout_id,upa.user_id,upa.amount,upa.type,upa.status AS status_user_payouts,ups.payment_method_id,pm.type,pm.name AS pm_name,pm.alias_name,pm.status AS status_payment_methode,ups.account_number,ups.account_name,upa.created_at,upa.created_by,upa.updated_at,upa.updated_by,upd.status as upd_status,upa.withdraw_ref,ups.user_id';

			// building where query
			$status = $req->getVar('status') ?? '';
			$status_payment = $req->getVar('status_payment') ?? '';
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = [
				'upa.deleted_at' => null,
				'upa.type' => 'withdraw'
			];
			if ($status != 'all' && $status != '' && $status > 0) $where += ['upa.status' => $status];

			// filter $status_payment
			// var_dump($status_payment);die;
			if (is_array($status_payment) && !in_array('all', $status_payment)) {
				// replace value 'null' to be null
				$key_null = array_search('null', $status_payment);
				if ($key_null > -1) $status_payment[$key_null] = null;
				// looping thourh $status_payment array
				$this->builder->groupStart()
					->where(['upd.status' => $status_payment[0]]);
				if (count($status_payment) > 1)
					for ($i = 1; $i < count($status_payment); $i++)
						$this->builder->orWhere(['upd.status' => $status_payment[$i]]);
				$this->builder->groupEnd();
			}

			// add select and where query to builder
			$this->builder
				->select($select_fields)
				->where($where);

			// bulding order query
			$order = $req->getVar('order');
			$length = isset($_REQUEST['length']) ? (int)$req->getVar('length') : 10;
			$start = isset($_REQUEST['start']) ? (int)$req->getVar('start') : 0;
			$col = 0;
			$dir = "";
			if (!empty($order)) {
				$col = $order[0]['column'];
				$dir = $order[0]['dir'];
			}
			if ($dir != "asc" && $dir != "desc") $dir = "asc";
			if (isset($fields_order[$col])) $this->builder->orderBy($fields_order[$col],  $dir); // add order query to builder

			// bulding search query
			if (!empty($req->getVar('search')['value'])) {
				$search = $req->getVar('search')['value'];
				$search_array = [];
				foreach ($fields_search as $key) $search_array[$key] = $search;
				// add search query to builder
				$this->builder
					->groupStart()
					->orLike($search_array)
					->groupEnd();
			}
			$totalData = count($this->builder->get(0, 0, false)->getResult()); // 3rd parameter is false to NOT reset query

			$this->builder->limit($length, $start); // add limit for pagination
			$dataResult = [];
			// echo $this->builder->getCompiledSelect();die;
			$dataResult = $this->builder->get()->getResult();

			$data = [];
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				helper('number');
				helper('user_balance_status_helper');
				$access = (object)[
					'proceed_payment' 		=> hasAccess($this->role, 'r_proceed_payment'),
					'manual_transfer' 		=> hasAccess($this->role, 'r_manual_transfer'),
					'mark_as_failed'		=> hasAccess($this->role, 'r_mark_as_failed'),
				];
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$action = '';
					$attribute_data['default'] =  htmlSetData(['user_payout_id' => $row->user_payout_id]);
					$attribute_data['view_user'] =  htmlSetData(['user_id' => $row->user_id]);
					$attribute_data['withdraw_detail'] =  htmlSetData(['method' => $row->pm_name, 'account_name' => $row->account_name, 'account_number' => $row->account_number, 'withdraw_ref' => $row->withdraw_ref]);

					$btn['btnProcess'] = [
						'color'	=> 'success',
						'class'	=> 'py-2 btnAction btnProceedPayment',
						'title'	=> 'Finish this this withdraw payment with automatic transfer withdraw process',
						'data'	=> $attribute_data['default'] . $attribute_data['withdraw_detail'],
						'icon'	=> 'fas fa-credit-card',
						'text'	=> 'Withdraw Payment',
						'id'	=> 'wp-' . $row->withdraw_ref,
					];

					$with_break = false;
					if (!empty($row->upd_status)) {
						$with_break = true;
						$color_upd_status = 'warning';
						if ($row->upd_status == 'COMPLETED') $color_upd_status = 'success';
						if ($row->upd_status == 'FAILED') $color_upd_status = 'danger';
						$action .= htmlButton([
							'color'	=> $color_upd_status,
							'class'	=> 'btnStatusPayment',
							'data'	=> $attribute_data['default'],
							'title'	=> 'Payment status is: ' . $row->upd_status,
							'icon'	=> '',
							'text'	=> 'Payment: ' . $row->upd_status,
						], false);

						if ($row->upd_status == 'FAILED') {
							$btn['btnProcess']['text'] = 'Retry Withdraw Payment';
							$btn['btnProcess']['icon']	= 'fas fa-sync-alt';
							$action .= ($access->proceed_payment ? htmlButton($btn['btnProcess']) : '')
								. ($access->manual_transfer ? htmlButton([
									'color'	=> 'outline-success',
									'class'	=> 'py-2 btnAction btnManualTransfer',
									'title'	=> 'Finish this withdraw payment with manual transfer',
									'data'	=> $attribute_data['default'] . $attribute_data['withdraw_detail'],
									'icon'	=> 'fas fa-file-invoice-dollar',
									'text'	=> 'Manual Transafer',
									'id'	=> 'mt-' . $row->withdraw_ref,
								]) : '');
						}
					} elseif ($row->status_user_payouts == 2) {
						$action .= $access->proceed_payment ? htmlButton($btn['btnProcess'], $with_break) : '';
					}
					if ($row->status_user_payouts == 3) {
						$action .= $access->mark_as_failed ? htmlButton([
							'color'	=> 'danger',
							'class'	=> '',
							'title'	=> 'Payment status is Failed',
							'icon'	=> '',
							'text'	=> 'Failed',
						], false) : '';
					}
					$action .= htmlButton([
						'color'	=> 'outline-info',
						'class'	=> 'py-2 btnAction btnViewUser',
						'title'	=> 'View user details',
						'data'	=> $attribute_data['view_user'],
						'icon'	=> 'fas fa-user',
						'text'	=> 'User',
					]);

					$r = [];
					$r[] = $i;
					$r[] = $row->user_payout_id;
					$r[] = $row->withdraw_ref;
					$r[] = $row->type;
					$r[] = $row->pm_name;
					$r[] = $row->account_number;
					$r[] = $row->account_name;
					$r[] = number_to_currency($row->amount, "IDR");
					$r[] = getUserBalanceStatus($row->status_user_payouts);
					$r[] = substr($row->updated_at, 0, 16);
					$r[] = $action;
					$data[] = $r;
				}
			}

			$json_data = [
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval($totalData),  // total number of records
				"recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $data   // total data array
			];
		}

		return $this->respond($json_data);
	}

	function proceed_payment()
	{
		$response = initResponse('Unauthorized.');
		$rules = ['user_payout_id' => getValidationRules('user_payout_id')];
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$user_payout_id = $this->request->getPost('user_payout_id');
			$code_auth = $this->request->getPost('codeauth');
			$check_role = checkRole($this->role, 'r_proceed_payment');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$setting = $this->Setting->getSetting(['_key' => '2fa_secret'], 'setting_id,val');

				if ($this->google->checkCode($setting->val, $code_auth) || env('app.environment') == 'local') {
					$select = 'ups.user_payout_id,ups.user_id,ups.user_balance_id,ups.amount,ups.type AS type_payout,ups.status,upa.payment_method_id,pm.type AS pm_type,pm.name AS bank_code,pm.alias_name,upa.account_number,upa.account_name,ups.withdraw_ref';
					$where = [
						'ups.user_payout_id' => $user_payout_id,
						'ups.status' => 2,
						'ups.deleted_at' => null
					];
					$user_payout = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select, false);

					if (!$user_payout) {
						$response->message = "Invalid User Payout Id $user_payout_id";
					} else {
						// Proccess Requested withdraw
						$withdraw_and_payout = new WithdrawAndPayouts();
						$response = $withdraw_and_payout->proceedPaymentLogic($user_payout);
					}
				} else {
					$response->message = '2FA Code is invalid!';
				}
			}
		}

		return $this->respond($response);
	}

	function manual_transfer()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$user_payout_id = $this->request->getPost('user_payout_id');
			$rules = getValidationRules('withdraw_manual');
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$check_role = checkRole($this->role, 'r_manual_transfer');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'ups.user_payout_id, ups.user_id, ups.amount, ups.type, ups.status AS status_user_payouts, upa.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, upa.account_number, upa.account_name, ups.created_at, ups.created_by, ups.updated_at, ups.updated_by, upd.status as upd_status, ub.user_balance_id, ups.withdraw_ref, upd.user_payout_detail_id';
					$where = array('ups.user_payout_id ' => $user_payout_id, 'ups.status' => 2, 'ups.deleted_at' => null, 'ups.type' => 'withdraw');

					$user_payout = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select);

					if (!$user_payout) {
						$response->message = "Invalid user payout id $user_payout_id";
					} else {
						// #belum selesai
						$notes = $this->request->getPost('notes') ?? '';
						$photo_id = $this->request->getFile('transfer_proof');
						$transfer_proof = $photo_id->getRandomName();
						if ($photo_id->move('uploads/transfer/', $transfer_proof)) {
							// main logic of manual_transfer
							$response = $this->manual_transfer_logic($user_payout, $notes, $transfer_proof);
						} else {
							$response->message = "Error upload file";
						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	private function manual_transfer_logic($user_payout, $notes, $transfer_proof)
	{
		$response = initResponse();

		$this->db = \Config\Database::connect();
		$this->db->transStart();

		$data = [];
		$data['user_payout'] = $user_payout;
		// lakukan logic payement success
		$payment_and_payout = new PaymentsAndPayouts();
		$payment_success = $payment_and_payout->updatePaymentWithdrawSuccess($user_payout->user_balance_id, $user_payout->user_id);

		// update user_payout.transfer_notes,transfer_proof
		$data_payout = [
			'transfer_notes' => $notes,
			'transfer_proof' => $transfer_proof,
			'status'		 => 1,
			'updated_at'    => date('Y-m-d H:i:s'),
			'updated_by'    => session()->username,
		];
		// var_dump($user_payout);die;
		$data['user_payout_update'] = $data_payout;
		$this->UserPayouts->saveUpdate(['user_payout_id' => $user_payout->user_payout_id], $data_payout);


		// update where(check_id) user_payouts.type='manual'
		$data_payout_detail = [
			'type'			=> 'manual',
			'status'		=> 'COMPLETED',
			'updated_at'	=> date('Y-m-d H:i:s'),
		];
		$data['user_payout_detail'] = $data_payout_detail;
		$payment_and_payout->updatePayoutDetail(['user_payout_id' => $user_payout->user_payout_detail_id], $data_payout_detail);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs02c";
		} elseif (!$payment_success->success) {
			$response->message = $payment_success->message;
		} else {
			$response->success = true;
			$response->message = "Successfully <b>transfer manual</b> for <b>$user_payout->withdraw_ref</b>";

			// send notif
			$User = new Users();
			$user = $User->getUser(['user_id' => $user_payout->user_id], 'user_id,name,notification_token');

			// try {
			// 	$title = "Congatulation, Your fund was transferred!";
			// 	$content = "Your withdrawal request was completed. Please check your fund history";
			// 	$notification_data = [
			// 		'type'		=> 'notif_withdraw_success'
			// 	];

			// 	$notification_token = $user->notification_token;
			// 	// var_dump($notification_token);die;
			// 	helper('onesignal');
			// 	$send_notif_submission = sendNotification([$notification_token], $title, $content, $notification_data); // hanya ke app2
			// 	$response->data['send_notif_submission'] = $send_notif_submission;
			// } catch (\Exception $e) {
			// 	$response->message .= " But, unable to send notification: " . $e->getMessage();
			// }

			// logs
			$log_cat = 23;
			$data['user_payout'] = (array)$user_payout;
			$this->log->in("$user->name\n" . session()->username, $log_cat, json_encode($data), session()->admin_id, $user->user_id, false);
		}

		return $response;
	}

	function export()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_export_withdraw')) {
			$status = $req->getVar('status') ?? '';
			$status_payment = $req->getVar('status_payment') ?? '';
			$date = $req->getVar('date') ?? '';

			if (empty($date)) {
				$response->message = "Date range can not be blank";
			} else {
				$this->db = \Config\Database::connect();
				$this->table_name = 'user_payouts';
				$this->builder = $this->db
					->table("$this->table_name as upa")
					->join("user_payments as ups", "ups.user_payment_id = upa.user_payment_id")
					->join("users as usr", "usr.user_id = ups.user_id")
					->join("payment_methods as pm", "pm.payment_method_id = ups.payment_method_id")
					->join("user_payout_details as upd", "upd.user_payout_id = upa.user_payout_id", "left");
	
				// select fields
				$select_fields = 'upa.user_payout_id,upa.user_id,upa.amount,upa.type,upa.status AS status_user_payouts,ups.payment_method_id,pm.type,pm.name AS pm_name,pm.alias_name,pm.status AS status_payment_methode,ups.account_number,ups.account_name,upa.created_at,upa.created_by,upa.updated_at,upa.updated_by,upd.status as upd_status,upa.withdraw_ref,usr.name as user_name,usr.nik';

				// building where query
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
				$where = [
					'upa.deleted_at' => null,
					'upa.type' => 'withdraw'
				];
				if ($status != 'all' && $status != '' && $status > 0) $where += ['upa.status' => $status];
	
				// filter $status_payment
				// var_dump($status_payment);die;
				if (is_array($status_payment) && !in_array('all', $status_payment)) {
					// replace value 'null' to be null
					$key_null = array_search('null', $status_payment);
					if ($key_null > -1) $status_payment[$key_null] = null;
					// looping thourh $status_payment array
					$this->builder->groupStart()
						->where(['upd.status' => $status_payment[0]]);
					if (count($status_payment) > 1)
						for ($i = 1; $i < count($status_payment); $i++)
							$this->builder->orWhere(['upd.status' => $status_payment[$i]]);
					$this->builder->groupEnd();
				}
	

				// add select and where query to builder
				$this->builder
					->select($select_fields)
					->where($where);

				$dataResult = [];
				$dataResult = $this->builder->get()->getResult();
				// die($this->db->getLastQuery());

				if (count($dataResult) < 1) {
					$response->message = "Empty data!";
				} else {
					$i = 1;
					helper('number');
					helper('html');
					helper('format');
					$access = [
						'view_photo_id' => hasAccess($this->role, 'r_view_photo_id'),
					];
					$path = 'temp/csv/';
					$filename = 'withdraw-' . date('YmdHis') . '.csv';
					$fp = fopen($path . $filename, 'w');
					$headers = [
						'No',
						'Withdraw Request Date',
						'Withdraw Updated Date',
						'Withdraw ref',
						'Payment Type',
						'Payment Name',
						'Account Number',
						'Account Name',
						'Amount',
						'Status',
						'User Name',
					];
					if ($access['view_photo_id'])  array_push($headers, 'User NIK');

					fputcsv($fp, $headers);

					// looping through data result & put in csv
					foreach ($dataResult as $row) {
						// var_dump($row);die;
						$r = [
							$i++,
							substr($row->created_at, 0, 10),
							substr($row->updated_at, 0, 10),
							$row->withdraw_ref,
							$row->type,
							$row->pm_name,
							$row->account_number,
							$row->account_name,
							number_to_currency($row->amount, "IDR"),
							getUserBalanceStatus($row->status_user_payouts),
							$row->user_name,
						];
						if ($access['view_photo_id']) array_push($r, $row->nik);

						fputcsv($fp, $r);
					}
					$response->success = true;
					$response->message = "Done";
					$response->data = base_url('download/csv/?file=' . $filename);
				}
			}
		}
		return $this->respond($response);
	}

	function view_user()
	{
		$response = initResponse('Unauthorized.');
		$rules = ['user_id' => getValidationRules('user_id')];
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$user_id = $this->request->getPost('user_id');
			$this->User = new Users();
			$select = 'user_id,name,nik,photo_id';
			$where = ['user_id' => $user_id, 'deleted_at' => null];
			$user = $this->User->getUser($where, $select);
			if (!$user) {
				$response->message = "Invalid user_id $user_id";
			} else {
				if (!hasAccess($this->role, 'r_view_photo_id') || empty($user->nik)) {
					$user->nik = '-';
					$user->photo_id = base_url("assets/images/photo-unavailable.png");
				} else {
					$user->photo_id = base_url("uploads/photo_id/$user->photo_id");
				}
				$response->success = true;
				$response->message = 'OK';
				$response->data = $user;
			}
		}
		return $this->respond($response);
	}

	function status_payment()
	{
		$response = initResponse('Unauthorized.');
		$rules = ['user_payout_id' => getValidationRules('user_payout_id')];
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$user_payout_id = $this->request->getPost('user_payout_id');
			$select = 'ups.user_payout_id,withdraw_ref,upd.type,upd.amount,upd.bank_code,upd.account_holder_name as account_name,upd.account_number,upd.description,upd.status,upd.failure_code,ups.created_at,ups.updated_at';
			$where = ['ups.user_payout_id' => $user_payout_id];
			$user_payout = $this->UserPayouts->getUserPayoutAndDetail($where, $select);
			if (!$user_payout) {
				$response->message = "Invalid user_payout_id $user_payout_id";
			} else {
				$response->success = true;
				$response->message = 'OK';
				$response->data = $user_payout;
			}
		}
		return $this->respond($response);
	}


}
