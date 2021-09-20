<?php

namespace App\Controllers;

use App\Libraries\PaymentsAndPayouts;
use App\Libraries\WithdrawAndPayouts;
use App\Models\AdminRolesModel;
use App\Models\AdminsModel;
use App\Models\Settings;
use App\Models\UserPayouts;

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
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			helper('html');
			// make filter status option 
			$status = getUserBalanceStatus(-1); // all
			// unset($status[1]);
			// unset($status[2]);
			// sort($status);
			$optionStatus = '<option></option><option value="all">All</option>';
	
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}
			
			$this->data += [
				'page' => (object)[
					'key' => '2-withdraw',
					'title' => 'Finance',
					'subtitle' => 'Withdraw',
					'navbar' => 'Withdraw',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
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
				->join("user_payments as ups", "ups.user_payment_id = upa.user_payment_id")
				->join("payment_methods as pm", "pm.payment_method_id = ups.payment_method_id")
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
				"pm_name",
				"alias_name",
				"account_number",
				"account_name",

			);
			// select fields
			$select_fields = 'upa.user_payout_id, upa.user_id, upa.amount, upa.type, upa.status AS status_user_payouts, ups.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, ups.account_number, ups.account_name, upa.created_at, upa.created_by, upa.updated_at, upa.updated_by, upd.status as upd_status, upa.withdraw_ref';

			// building where query
			$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' - ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(upa.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = array('upa.deleted_at' => null);
			$where += array('upa.type' => 'withdraw');
			if ($status != 'all' && $status != '' && $status > 0) $where += array('upa.status' => $status);
			// var_dump($where);die;

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
				$search_array = array();
				foreach ($fields_search as $key) $search_array[$key] = $search;
				// add search query to builder
				$this->builder
					->groupStart()
					->orLike($search_array)
					->groupEnd();
			}
			$totalData = count($this->builder->get(0, 0, false)->getResult()); // 3rd parameter is false to NOT reset query

			$this->builder->limit($length, $start); // add limit for pagination
			$dataResult = array();
			$dataResult = $this->builder->get()->getResult();

			$data = array();
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				helper('number');
				helper('user_balance_status_helper');
				$access = (object)[
					'confirm_appointment'	=> hasAccess($this->role, 'r_confirm_appointment'),
					'proceed_payment' 		=> hasAccess($this->role, 'r_proceed_payment'),
					'manual_transfer' 		=> hasAccess($this->role, 'r_manual_transfer'),
					'mark_as_failed'		=> hasAccess($this->role, 'r_mark_as_failed'),
				];
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$action = '';
					$attribute_data['default'] =  htmlSetData(['user_payout_id' => $row->user_payout_id]);
					$attribute_data['withdraw_detail'] =  htmlSetData([
						'method' => $row->pm_name, 
						'account_name' => $row->account_name, 
						'account_number' => $row->account_number, 
						'withdraw_ref' => $row->withdraw_ref,
						]
					);

					$btn['btnProcess'] = [
						'color'	=> 'success',
						'class'	=> 'btnProceedPayment',
						'title'	=> 'Finish this this withdraw payment with automatic transfer withdraw process',
						'data'	=> $attribute_data['default'] . $attribute_data['withdraw_detail'],
						'icon'	=> 'fas fa-credit-card',
						'text'	=> 'Withdraw Payment',
					];
					$btn['mark_as_failed'] = [
						'color'	=> 'danger',
						'class'	=> 'py-2 btnAction btnMarkAsFailed',
						'title'	=> 'Mark this as failed transaction. A pop up confirmation will appears',
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-info-circle',
						'text'	=> 'Mark as Failed',
					];

					$status = getUserBalanceStatus($row->status_user_payouts);
					$status_color = 'default';
					if ($row->status_user_payouts == 1) $status_color = 'success';
					elseif ($row->status_user_payouts == 2) $status_color = 'primary';
					elseif ($row->status_user_payouts == 3) $status_color = 'danger';
					$action = '
					<button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Step ' . $row->status_user_payouts . '">' . $status . '</button>
					';
					
					
					if($row->upd_status == null){
						$action .= htmlButton($btn['btnProcess']);
					} else{
						$color_payout_status = 'warning';
						if ($row->upd_status == 'COMPLETED') $color_payout_status = 'success';
						elseif ($row->upd_status == 'FAILED') $color_payout_status = 'danger';

						$btn['mark_as_failed']['data'] .= ' data-failed="Failed"';
						$action .= htmlButton([
							'color'	=> $color_payout_status,
							'class'	=> '',
							'title'	=> 'Withdraw status is: ' . $row->upd_status,
							'data'	=> $attribute_data['default'] . $attribute_data['withdraw_detail'],
							'icon'	=> '',
							'text'	=> 'Withdraw: ' . $row->upd_status,
						]);
					}

					

					if ($row->status_user_payouts == 2) {
						// jika payment gateway gagal, show manual transfer
						if($row->upd_status == 'FAILED') {
							$action .= ($access->manual_transfer ? htmlButton([
								'color'	=> 'outline-success',
								'class'	=> 'py-2 btnAction btnManualTransfer',
								'title'	=> 'Finish this this withdraw with manual transfer',
								'data'	=> $attribute_data['default'] . $attribute_data['withdraw_detail'],
								'icon'	=> 'fas fa-file-invoice-dollar',
								'text'	=> 'Manual Transafer',
							]) : '');
							$action .= $access->mark_as_failed ? htmlButton($btn['mark_as_failed']) : '';
							
						}

						
						// $action .= htmlAnchor($btn['view']);
					} else if ($row->status_user_payouts == 3) {
						
					}
					// $newstring = substr($dynamicstring, -7);
					$account_number = "****" . substr($row->account_number, -4);
					// $account_number = $row->account_number;

					$r = [];
					$r[] = $i;
					$r[] = $row->user_payout_id;
					$r[] = $row->type;
					$r[] = $row->pm_name;
					$r[] = $account_number;
					$r[] = $row->account_name;
					$r[] = number_to_currency($row->amount, "IDR");
					$r[] = substr($row->created_at, 0, 16);
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
		if (session()->has('admin_id')) {
			$user_payout_id = $this->request->getPost('user_payout_id');
			$code_auth = $this->request->getPost('codeauth');
			$rules = ['user_payout_id' => getValidationRules('user_payout_id')];

			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$check_role = checkRole($this->role, 'r_proceed_payment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$setting = $this->Setting->getSetting(['_key' => '2fa_secret'], 'setting_id,val');

					if ($this->google->checkCode($setting->val, $code_auth)) {
						$select = 'ups.user_payout_id, ups.user_id, ups.user_balance_id, ups.amount, ups.type AS type_payout, ups.status, upa.payment_method_id, pm.type AS pm_type, pm.name AS bank_code, pm.alias_name, upa.account_number, upa.account_name';
						$where = [
							'ups.user_payout_id' => $user_payout_id,
							'ups.status' => 2,
							'ups.deleted_at' => null
						];
						$dataUser = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select, false);

						if (!$dataUser) {
							$response->message = "Invalid User Payout Id $user_payout_id";
						} else {
							// Request withdraw
							$withdraw_and_payout = new WithdrawAndPayouts();
							$response = $withdraw_and_payout->proceedPaymentLogic($dataUser);
						}
					} else {
						$response->message = '2FA Code is invalid!';
					}
				}
			}
		}

		return $this->respond($response, 200);
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
					$select = 'ups.user_payout_id, ups.user_id, ups.amount, ups.type, ups.status AS status_user_payouts, upa.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, upa.account_number, upa.account_name, ups.created_at, ups.created_by, ups.updated_at, ups.updated_by, upd.status as upd_status, ub.user_balance_id, ups.withdraw_ref';
					$where = array('ups.user_payout_id ' => $user_payout_id, 'ups.status' => 2, 'ups.deleted_at' => null, 'ups.type' => 'withdraw');

					$user_payout = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select);
					// var_dump($user_payout); die;

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
		$data['data'] = $user_payout;
		// lakukan logic payement success
		$payment_and_payout = new PaymentsAndPayouts();
		$payment_success = $payment_and_payout->updatePaymentWithdrawSuccess($user_payout->user_payout_id,$user_payout->user_id );
		var_dump($payment_success);die;

		// update user_payout.transfer_notes,transfer_proof
		$data_payout = [
			'transfer_notes' => $notes,
			'transfer_proof' => $transfer_proof,
		];
		$data['device_check_detail'] = $data_payout;
		$this->UserPayouts->update($user_payout->user_payout_id, $data_payout);
		

		// update where(check_id) user_payouts.type='manual'
		$data_payout_detail = [
			'type'			=> 'manual',
			'status'		=> 'COMPLETED',
			'updated_at'	=> date('Y-m-d H:i:s'),
		];
		$data['payout_detail'] = $data_payout_detail;
		$payment_and_payout->updatePayoutDetail($user_payout->user_payout_detail_id, $data_payout_detail);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs02c";
		} elseif (!$payment_success->success) {
			$response->message = $payment_success->message;
		} else {
			$response->success = true;
			$response->message = "Successfully <b>transfer manual</b> for <b>$user_payout->withdraw_ref</b>";
			$log_cat = 8;
			$this->log->in(session()->username, $log_cat, json_encode($data));
		}

		return $response;
	}
}
