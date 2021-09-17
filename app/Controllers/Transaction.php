<?php

namespace App\Controllers;

use App\Libraries\PaymentsAndPayouts;
use App\Libraries\Xendit;
use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
use App\Models\Users;
use App\Models\Appointments;
use App\Models\Settings;
use App\Models\UserBalance;
use App\Models\UserPayoutDetails;
use App\Models\UserPayouts;

class Transaction extends BaseController
{
	protected $DeviceCheck, $DeviceCheckDetail, $User, $UserBalance, $UserPyout, $UserPayoutDetail, $Appointment;

	public function __construct()
	{
		$this->DeviceCheck = new DeviceChecks();
		$this->DeviceCheckDetail = new DeviceCheckDetails();
		$this->User = new Users();
		$this->UserBalance = new UserBalance();
		$this->UserPayout = new UserPayouts();
		$this->UserPayoutDetail = new UserPayoutDetails();
		$this->Appointment = new Appointments();
		$this->Setting = new Settings();
		$this->google = new \Google\Authenticator\GoogleAuthenticator();
		helper('grade');
		helper('validation');
		helper('device_check_status');
	}

	public function index()
	{
		helper('html');
		$check_role = checkRole($this->role, 'r_transaction');
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {

			// make filter status option 
			$status = getDeviceCheckStatusInternal(-1); // all
			unset($status[1]);
			// unset($status[2]);
			// sort($status);
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-transaction',
					'title' => 'Finance',
					'subtitle' => 'Transaction & Appointments',
					'navbar' => 'Transaction',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
			];

			return view('transaction/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_transaction');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {

			$this->db = \Config\Database::connect();
			$this->table_name = 'device_checks';
			$this->builder = $this->db
				->table("$this->table_name as t")
				->join("device_check_details as t1", "t1.check_id=t.check_id", "left")
				->join("users as t2", "t2.user_id=t.user_id", "left")
				->join("user_payouts as t3", "t3.user_id=t.check_id", "left")
				->join("user_payout_details as t4", "t4.external_id=t.check_code", "left")
				->join("payment_methods t5", "t5.payment_method_id=t1.payment_method_id", "left")
				->join("appointments t6", "t6.check_id=t.check_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"t.created_at",
				"check_code",
				"imei",
				"brand",
				"grade",
				"t2.name",
			);
			// fields to search with
			$fields_search = array(
				"brand",
				"model",
				"t.type",
				"storage",
				"check_code",
				"imei",
				"t2.name",
				"customer_name",
				"customer_phone",
			);
			// select fields
			$select_fields = 't.check_id,check_code,imei,brand,model,storage,t.type,grade,t.status,status_internal,price,t2.name,customer_name,customer_phone,t.created_at,t4.status as payout_status,t5.alias_name as payment_method,courier_name,courier_phone';

			// building where query
			$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' - ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = array('t.deleted_at' => null);
			if ($status > 0) $where += array('t.status_internal' => $status);
			else $where += array('t.status_internal>' => 1);

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
				helper('number');
				helper('html');
				$url = base_url() . '/device_check/detail/';
				$access = (object)[
					'confirm_appointment'	=> hasAccess($this->role, 'r_confirm_appointment'),
					'proceed_payment' 		=> hasAccess($this->role, 'r_proceed_payment'),
					'manual_transfer' 		=> hasAccess($this->role, 'r_manual_transfer'),
					'mark_as_failed'		=> hasAccess($this->role, 'r_mark_as_failed'),
				];
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$attribute_data['default'] =  htmlSetData(['check_code' => $row->check_code, 'check_id' => $row->check_id]);
					$attribute_data['payment_detail'] =  htmlSetData(['payment_method' => $row->payment_method, 'account_name' => $row->customer_name, 'account_number' => $row->customer_phone]);
					$status = getDeviceCheckStatusInternal($row->status_internal);
					$status_color = 'default';
					if ($row->status_internal == 5) $status_color = 'success';
					elseif ($row->status_internal == 6 || $row->status_internal == 7) $status_color = 'danger';
					elseif ($row->status_internal == 4) $status_color = 'primary';
					elseif ($row->status_internal == 8) $status_color = 'warning';
					$action = '
					<button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Step ' . $row->status_internal . '">' . $status . '</button>
					';
					$btn['view'] = [
						'color'	=> 'outline-secondary',
						'href'	=>	$url.$row->check_id,
						'class'	=> 'py-2 btnAction',
						'title'	=> "View detail of $row->check_code",
						'data'	=> '',
						'icon'	=> 'fas fa-eye',
						'text'	=> 'View',
					];
					$btn['confirm_appointment'] = [
						'color'	=> 'warning',
						'class'	=> 'py-2 btnAction btnAppointment',
						'title'	=> 'Confirm the appointment of ' . $row->check_code,
						'data'	=> $attribute_data['default'].' data-type="confirm"',
						'icon'	=> 'fas fa-map-marker',
						'text'	=> 'Confirm Appointment',
					];
					$btn['proceed_payment'] = [
						'color'	=> 'success',
						'class'	=> 'py-2 btnAction btnProceedPayment',
						'title'	=> 'Finish this this transction with automatic transfer payment process',
						'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
						'icon'	=> 'fas fa-credit-card',
						'text'	=> 'Proceed Payment',
					];
					$btn['mark_as_failed'] = [
						'color'	=> 'danger',
						'class'	=> 'py-2 btnAction btnMarkAsFailed',
						'title'	=> 'Mark this as failed transaction. A pop up confirmation will appears',
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-info-circle',
						'text'	=> 'Mark as Failed',
					];
					if ($row->status_internal == 3) {
						// on appointment, action: confirm appointment, mark as cancel
						$attribute_data['proceed_payment'] = $attribute_data['default'] . $attribute_data['payment_detail'];
						$btn['mark_as_failed']['text'] = 'Mark as Cancelled';
						$btn['mark_as_failed']['data'] .= ' data-failed="Cancelled"';
						$action .= '
						' . ($access->confirm_appointment ? htmlButton($btn['confirm_appointment']) : ''). '
						'.($access->mark_as_failed ? htmlButton($btn['mark_as_failed']) : '').'
						';
					} else if ($row->status_internal == 8) {
						// appointment confirmed
						$btn['confirm_appointment']['text'] = 'Appointment Detail';
						$btn['confirm_appointment']['title'] = 'Appointment of ' . $row->check_code;
						$btn['confirm_appointment']['data'] = str_replace('"confirm"', '"view"', $btn['confirm_appointment']['data']);
						$btn['confirm_appointment']['data'] .= htmlSetData(['courier_name' => $row->courier_name, 'courier_phone' => $row->courier_phone]);
						$attribute_data['proceed_payment'] = $attribute_data['default'] . $attribute_data['payment_detail'];
						$btn['mark_as_failed']['text'] = 'Mark as Cancelled';
						$btn['mark_as_failed']['data'] .= ' data-failed="Cancelled"';
						$action .= '
						' . ($access->confirm_appointment ? htmlButton($btn['confirm_appointment']) : ''). '
						' . ($access->proceed_payment ? htmlButton($btn['proceed_payment']) : ''). '
						'.($access->mark_as_failed ? htmlButton($btn['mark_as_failed']) : '').'
						';
					} elseif ($row->status_internal == 4) {
						$color_payout_status = 'warning';
						if ($row->payout_status == 'COMPLETED') $color_payout_status = 'success';
						elseif ($row->payout_status == 'FAILED') $color_payout_status = 'danger';
						$btn['mark_as_failed']['data'] .= ' data-failed="Failed"';
						$action .= htmlButton([
							'color'	=> $color_payout_status,
							'class'	=> '',
							'title'	=> 'Payment status is: ' . $row->payout_status,
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> '',
							'text'	=> 'Payment: ' . $row->payout_status,
						]);

						// jika payment gateway gagal, show manual transfer
						if($row->payout_status == 'FAILED') {
							$action .= ($access->proceed_payment ? htmlButton($btn['proceed_payment']) : '');
							$action .= ($access->manual_transfer ? htmlButton([
								'color'	=> 'outline-success',
								'class'	=> 'py-2 btnAction btnManualTransfer',
								'title'	=> 'Finish this this transction with manual transfer',
								'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
								'icon'	=> 'fas fa-file-invoice-dollar',
								'text'	=> 'Manual Transafer',
							]) : '');
							$action .= $access->mark_as_failed ? htmlButton($btn['mark_as_failed']) : '';
						}
					}
					$action .= htmlAnchor($btn['view']);

					$r = array();
					$r[] = $i;
					$r[] = substr($row->created_at, 0, 16);
					$r[] = $row->check_code;
					$r[] = $row->imei;
					$r[] = "$row->brand $row->model $row->storage $row->type";
					$r[] = "$row->grade<br>" . number_to_currency($row->price, "IDR");
					$r[] = "$row->name<br>$row->customer_name " . (true ? $row->customer_phone : "");
					$r[] = $action;
					$data[] = $r;
				}
			}

			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval($totalData),  // total number of records
				"recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $data   // total data array
			);
		}

		echo json_encode($json_data);
	}

	function proceed_payment()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$code_auth = $this->request->getPost('codeauth');
			$rules = ['check_id' => getValidationRules('check_id')];
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
						$select = 'dc.check_id,check_code,price,dc.user_id,dcd.account_number,dcd.account_name,pm.name as bank_code';
						$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
						$whereIn = ['status_internal' => [8, 4]];
						$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select, '', $whereIn);
						if (!$device_check) {
							$response->message = "Invalid check_id $check_id";
						} else {
							// var_dump($device_check);die;
							// termasuk logs jika berhasil
							$payment_and_payout = new PaymentsAndPayouts();
							$response = $payment_and_payout->proceedPaymentLogic($device_check);
						}
					} else {
						$response->message = '2FA Code is invalid!';
					}
				}
			}
		}
		return $this->respond($response);
	}

	function manual_transfer()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$rules = getValidationRules('transfer_manual');
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$check_role = checkRole($this->role, 'r_manual_transfer');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_detail_id,check_code,status_internal,user_payout_detail_id';
					$where = array('dc.check_id' => $check_id, 'dc.status_internal' => 4, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						// #belum selesai
						$notes = $this->request->getPost('notes') ?? '';
						$photo_id = $this->request->getFile('transfer_proof');
						$transfer_proof = $photo_id->getRandomName();
						if ($photo_id->move('uploads/transfer/', $transfer_proof)) {
							// main logic of manual_transfer
							$response = $this->manual_transfer_logic($device_check, $notes, $transfer_proof);
						} else {
							$response->message = "Error upload file";
						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	private function manual_transfer_logic($device_check, $notes, $transfer_proof)
	{
		$response = initResponse();

		$this->db = \Config\Database::connect();
		$this->db->transStart();

		$data = [];
		$data['data'] = $device_check;
		// lakukan logic payement success
		$payment_and_payout = new PaymentsAndPayouts();
		$payment_success = $payment_and_payout->updatePaymentSuccess($device_check->check_id);

		// update device_check_details.transfer_notes,transfer_proof
		$data_device_check_detail = [
			'transfer_notes' => $notes,
			'transfer_proof' => $transfer_proof,
		];
		$data['device_check_detail'] = $data_device_check_detail;
		$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_device_check_detail);

		// update where(check_id) user_payouts.type='manual'
		$data_payout_detail = [
			'type'			=> 'manual',
			'status'		=> 'COMPLETED',
			'updated_at'	=> date('Y-m-d H:i:s'),
		];
		$data['payout_detail'] = $data_payout_detail;
		$payment_and_payout->updatePayoutDetail($device_check->user_payout_detail_id, $data_payout_detail);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs02c";
		} elseif (!$payment_success->success) {
			$response->message = $payment_success->message;
		} else {
			$response->success = true;
			$response->message = "Successfully <b>transfer manual</b> for <b>$device_check->check_code</b>";
			$log_cat = 8;
			$this->log->in(session()->username, $log_cat, json_encode($data));
		}

		return $response;
	}

	function mark_as_failed()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$check_role = checkRole($this->role, 'r_mark_as_failed');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_detail_id,check_code,status_internal,dc.user_id,upa.user_payout_id,upad.user_payout_detail_id,upad.description';
					// perlu diubah kondisi where status_internal nya karena ada status 3,8,4
					$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
					$whereIn = ['status_internal' => [3, 8, 4]];
					$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select, '', $whereIn);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$notes = $this->request->getPost('notes') ?? '';
						$response = $this->mark_as_failed_logic($device_check, $notes);
					}
				}
			}
		}
		return $this->respond($response);
	}

	private function mark_as_failed_logic($device_check, $notes)
	{
		// #belum selesai
		$response = initResponse();
		$this->db = \Config\Database::connect();
		$this->db->transStart();

		$data = [];
		$data['data'] = $device_check;
		if ($device_check->status_internal == 3 || $device_check->status_internal == 8) {
			$failed_text = 'Cancelled';
			$status_internal = 7; // cancelled
			$data_device_check_detail = ['general_notes' => $notes];
			$data['device_check_detail'] = $data_device_check_detail;
			$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_device_check_detail);
		} elseif ($device_check->status_internal == 4) {
			$failed_text = 'Failed';
			$status_internal = 6; // failed
			// update where(check_id, user_id) user_balance.status=3 (failed) [cashflow=in] [cashflow=out] [tidak bisa jika belum langkah 2]
			$data_user_balance = ['status' => 3];
			$data['user_balance'] = $data_user_balance;
			$this->UserBalance->where([
				'check_id'  => $device_check->check_id,
				'user_id'   => $device_check->user_id,
				'type'      => 'transaction',
			])->set($data_user_balance)
				->update();

			// update where(check_id) user_payouts.status=3 (failed) [tidak bisa jika belum langkah 2]
			$data_user_payout = ['status' => 3];
			$data['user_payout'] = $data_user_payout;
			$this->UserPayout->update($device_check->user_payout_id, $data_user_payout);

			// update notes
			$data_user_payout_detail = ['description' => $device_check->description . '. ' . $notes];
			$data['user_payout_detail'] = $data_user_payout_detail;
			$this->UserPayoutDetail->update($device_check->user_payout_detail_id, $data_user_payout_detail);
		}
		// update device_check
		$data_device_check = ['status_internal' => $status_internal];
		$data['device_check'] = $data_device_check;
		$this->DeviceCheck->update($device_check->check_id, $data_device_check);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs03c";
		} else {
			$response->success = true;
			$response->message = "Successfully mark transaction <b>$device_check->check_code</b> as <b>$failed_text</b>";
			$log_cat = 9;
			$this->log->in(session()->username, $log_cat, json_encode($data));
		}

		return $response;
	}

	function detail_appointment()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->role_id);
				$check_role = checkRole($role, 'r_confirm_appointment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_code,imei,brand,model,storage,dc.type,grade,price,survey_fullset,customer_name,customer_phone,choosen_date,choosen_time,ap.name as province_name,ac.name as city_name,ad.name as district_name,postal_code,adr.notes as full_address,pm.type as bank_emoney,pm.name as bank_code,account_number,account_name,account_name_check,account_bank_check,account_bank_error,courier_name,courier_phone';
					$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$response->success = true;
						$response->message = 'OK';
						$response->data = $device_check;
					}
				}
			}
		}
		return $this->respond($response);
	}

	function confirm_appointment()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$courier_name = $this->request->getPost('courier_name');
			$courier_phone = $this->request->getPost('courier_phone');
			$rules = getValidationRules('transaction:confirm_appointment');
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->role_id);
				$check_role = checkRole($role, 'r_confirm_appointment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_code,customer_name,appointment_id';
					$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$this->db = \Config\Database::connect();
						$this->db->transStart();
						$data_appointment = [
							'courier_name'			=> $courier_name,
							'courier_phone'			=> $courier_phone,
							'courier_expedition'	=> 'Happy Express',
						];
						$this->Appointment->update($device_check->appointment_id, $data_appointment);
						$data_device_check = ['status_internal' => 8];
						$this->DeviceCheck->update($device_check->check_id, $data_device_check);

						$this->db->transComplete();

						if ($this->db->transStatus() === FALSE) {
							// transaction has problems
							$response->message = "Failed to perform task! #trs03c";
						} else {
							$response->success = true;
							$response->message = "Successfully confirm appointment for <b>$device_check->check_code</b>";
							$log_cat = 10;
							$data = [];
							$data += $data_appointment;
							$data += $data_device_check;
							$data['device_check'] = $device_check;
							$this->log->in(session()->username, $log_cat, json_encode($data));
						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	function validate_bank_account()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_id = $this->request->getPost('check_id');
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->role_id);
				$check_role = checkRole($role, 'r_confirm_appointment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_detail_id,check_code,pm.type as bank_emoney,pm.name as bank_code,account_number,account_name';
					$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$Xendit = new Xendit();
						$valid_bank_detail = $Xendit->validate_bank_detail($device_check->bank_code, $device_check->account_number);
						if(!$valid_bank_detail->success) {
							$response->message = "Unable to check the payment method of $device_check->check_code";
						} else {
							// parse xendit response to update device_check_details
							$status = $valid_bank_detail->data->status ?? 'PENDING';
							$bank_account_holder_name = $valid_bank_detail->data->bank_account_holder_name ?? 'PENDING';
							$data_update = [
								'account_bank_check' => 'pending',
								'account_name_check' => $bank_account_holder_name
							];
							if($status == 'SUCCESS') {
								if($bank_account_holder_name == $device_check->account_name) {
									$data_update['account_bank_check'] = 'valid';
									$response->success = true;
									$response->message = "$device_check->account_number of $device_check->bank_code is <b class=\"text-success\">valid</b>.";
								} else {
									$data_update['account_bank_check'] = 'invalid';
									$data_update['account_bank_error'] = 'DIFFERENT_NAME';
									$response->message = "$device_check->account_number of $device_check->bank_code is <b class=\"text-success\">valid</b>, but has <b class=\"text-danger\">different name</b>.";
								}
							}
							elseif($status == 'FAILURE') {
								$response->message = "$device_check->account_number of $device_check->bank_code is <b class=\"text-danger\">invalid</b>.";
								$data_update['account_bank_check'] = 'invalid';
								$data_update['account_bank_error'] = $valid_bank_detail->data->failure_reason ?? '';
							}

							$this->db = \Config\Database::connect();
							$this->db->transStart();
							$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_update);
							$this->db->transComplete();
	
							if ($this->db->transStatus() === FALSE) {
								// transaction has problems
								$response->message = "Failed to perform task! #trs03c";
								$response->success = false;
							} else {
								// write log
								$log_cat = 10;
								$data['data_update'] = $data_update;
								$data['data_response'] = $valid_bank_detail->data;
								$response->data = $data;
							}
						}
					}
				}
			}
		}
		return $this->respond($response);
	}

}
