<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PaymentsAndPayouts;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceChecks;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;
use App\Models\DeviceCheckDetails;
use App\Models\Users;
use App\Models\Appointments;
use App\Models\UserBalance;
use App\Models\UserPayoutDetails;
use App\Models\UserPayouts;

class Transactions extends BaseController
{
	use ResponseTrait;

	protected $DeviceCheck, $DeviceCheckDetail, $Admin, $AdminRole, $User, $UserBalance, $UserPyout, $UserPayoutDetail, $Appointment;

	public function __construct()
	{
		$this->DeviceCheck = new DeviceChecks();
		$this->DeviceCheckDetail = new DeviceCheckDetails();
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->User = new Users();
		$this->UserBalance = new UserBalance();
		$this->UserPayout = new UserPayouts();
		$this->UserPayoutDetail = new UserPayoutDetails();
		$this->Appointment = new Appointments();
		helper('rest_api');
		helper('grade');
		helper('validation');
		helper('role');
		helper('device_check_status');
	}

	public function index()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());

		// make filter status option 
		$status = getDeviceCheckStatusInternal(-1); // all
		unset($status[1]);
		unset($status[2]);
		$optionStatus = '<option></option><option value="all">All</option>';
		foreach ($status as $key => $val) {
			$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
		}

		$data = [
			'page' => (object)[
				'title' => 'Transaction',
				'subtitle' => 'List',
				'navbar' => 'Transaction',
			],
			'admin' => $this->Admin->find(session()->admin_id),
			'role' => $this->AdminRole->find(session()->admin_id),
			'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
			'optionStatus' => $optionStatus,
		];

		return view('transactions/index', $data);
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$role = $this->AdminRole->find(session()->admin_id);
		$check_role = checkRole($role, 'r_proceed_payment');
		$check_role->success = true; // sementara belum ada role
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
				->join("payment_methods t5", "t5.payment_method_id=t1.payment_method_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"t.created_at",
				"check_code",
				"imei",
				"brand",
				"grade",
				"name",
			);
			// fields to search with
			$fields_search = array(
				"brand",
				"model",
				"t.type",
				"storage",
				"check_code",
				"imei",
				"name",
				"customer_name",
				"customer_phone",
			);
			// select fields
			$select_fields = 't.check_id,check_code,imei,brand,model,storage,t.type,grade,t.status,status_internal,price,t2.name,customer_name,customer_phone,t.created_at,t4.status as payout_status,t5.alias_name as payment_method';

			// building where query
			$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
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
				helper('device_check_status');
				helper('html');
				$url = base_url() . '/device_check/detail/';
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$status = getDeviceCheckStatusInternal($row->status_internal);
					// $attribute_data['default'] = 'data-check_code="'.$row->check_code.'" data-check_id="'.$row->check_id.'" ';
					$attribute_data['default'] =  htmlSetData(['check_code' => $row->check_code, 'check_id' => $row->check_id]);
					$attribute_data['payment_detail'] =  htmlSetData(['payment_method' => $row->payment_method, 'account_name' => $row->customer_name, 'account_number' => $row->customer_phone]);
					$action = '
					<button class="btn btn-xs mb-2 btn-default" title="Step ' . $row->status_internal . '">' . $status . '</button>
					';
					// <br><a href="' . $url . $row->check_id . '" class="btn btn-xs mb-2 py-2 btn-warning btnAction" title="View detail of ' . $row->check_code . '"><i class="fa fa-eye"></i> View</a>
					// ';
					$btn['view'] = [
						'color'	=> 'warning',
						'href'	=>	$url.$row->check_id,
						'class'	=> 'py-2 btnAction btnManualTransfer',
						'title'	=> "View detail of $row->check_code",
						'data'	=> '',
						'icon'	=> 'fas fa-eye',
						'text'	=> 'View',
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
						// on appointment
						$attribute_data['proceed_payment'] = $attribute_data['default'] . htmlSetData(['payment_method' => $row->payment_method, 'account_name' => $row->customer_name, 'account_number' => $row->customer_phone]);
						$btn['mark_as_failed']['text'] = 'Mark as Cancelled';
						$btn['mark_as_failed']['data'] .= ' data-failed="Cancelled"';
						$action .= '
						' . htmlCreateButton([
							'color'	=> 'success',
							'class'	=> 'py-2 btnAction btnProceedPayment',
							'title'	=> 'Finish this this transction with automatic transfer payment process',
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> 'fas fa-credit-card',
							'text'	=> 'Proceed Payment',
						]) . '
						'.htmlCreateButton($btn['mark_as_failed']).'
						';
					} elseif($row->status_internal == 4) {
						$color_payout_status = 'warning';
						if($row->payout_status == 'COMPLETED') $color_payout_status = 'success';
						elseif($row->payout_status == 'FAILED') $color_payout_status = 'danger';
						$btn['mark_as_failed']['data'] .= ' data-failed="Failed"';
						$action .= htmlCreateButton([
							'color'	=> $color_payout_status,
							'class'	=> '',
							'title'	=> 'Payment status is: '.$row->payout_status,
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> '',
							'text'	=> 'Payment: '.$row->payout_status,
						]);

						if($row->payout_status == 'FAILED') {
							$check_role = checkRole($role, 'r_proceed_payment');
							if ($check_role->success) {
								$action .= htmlCreateButton([
									'color'	=> 'outline-success',
									'class'	=> 'py-2 btnAction btnManualTransfer',
									'title'	=> 'Finish this this transction with manual transfer',
									'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
									'icon'	=> 'fas fa-file-invoice-dollar',
									'text'	=> 'Manual Transafer',
								]);
							}
							$action .= htmlCreateButton($btn['mark_as_failed']);
						}
					}
					$action .= htmlCreateAnchor($btn['view']);

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
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->admin_id);
				$check_role = checkRole($role, 'r_proceed_payment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_code,price,dc.user_id,dcd.account_number,dcd.account_name,pm.name as bank_code';
					$where = array('dc.check_id' => $check_id, 'dc.status_internal' => 3, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						// var_dump($device_check);die;
						$payment_and_payout = new PaymentsAndPayouts();
						$response = $payment_and_payout->proceedPaymentLogic($device_check);
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
			$check_id = $this->request->getPost('check_id');
			$rules = getValidationRules('transfer_manual');
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->admin_id);
				$check_role = checkRole($role, 'r_proceed_payment');
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
		return $this->respond($response, 200);
	}

	function manual_transfer_logic($device_check, $notes, $transfer_proof)
	{
		$response = initResponse();

		$this->db = \Config\Database::connect();
		$this->db->transStart();
		
		// lakukan logic payement success
		$payment_and_payout = new PaymentsAndPayouts();
		$payment_success = $payment_and_payout->updatePaymentSuccess($device_check->check_id);

		// update device_check_details.transfer_notes,transfer_proof
		$this->DeviceCheckDetail->update($device_check->check_detail_id, [
			'transfer_notes' => $notes,
			'transfer_proof' => $transfer_proof,
		]);

		// update where(check_id) user_payouts.type='manual'
		$payment_and_payout->updatePayoutDetail($device_check->user_payout_detail_id, [
			'type'			=> 'manual',
			'status'		=> 'COMPLETED',
			'updated_at'	=> date('Y-m-d H:i:s'),
		]);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs02c";
		} elseif(!$payment_success->success) {
			$response->message = $payment_success->message;
		} else {
			$response->success = true;
			$response->message = "Successfully <b>transfer manual</b> for <b>$device_check->check_code</b>";
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
				$role = $this->AdminRole->find(session()->admin_id);
				$check_role = checkRole($role, 'r_mark_as_failed');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'dc.check_id,check_detail_id,check_code,status_internal,dc.user_id,upa.user_payout_id,upad.user_payout_detail_id,upad.description';
					$where = array('dc.check_id' => $check_id, 'dc.status_internal>' => 2,  'dc.status_internal<' => 5, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$notes = $this->request->getPost('notes') ?? '';
						$response = $this->mark_as_failed_logic($device_check, $notes);
					}
				}
			}
		}
		return $this->respond($response, 200);
	}

	function mark_as_failed_logic($device_check, $notes)
	{
		// #belum selesai
		$response = initResponse();
		$this->db = \Config\Database::connect();
		$this->db->transStart();

		if ($device_check->status_internal == 3) {
			$failed_text = 'Cancelled';
			$status_internal = 7; // cancelled
			$this->DeviceCheckDetail->update($device_check->check_detail_id, ['general_notes' => $notes]);
		} elseif ($device_check->status_internal == 4) {
			$failed_text = 'Failed';
			$status_internal = 6; // failed
			// update where(check_id, user_id) user_balance.status=3 (failed) [cashflow=in] [cashflow=out] [tidak bisa jika belum langkah 2]
			$this->UserBalance->where([
                'check_id'  => $device_check->check_id,
                'user_id'   => $device_check->user_id,
                'type'      => 'transaction',
            ])->set(['status' => 3])
            ->update();

			// update where(check_id) user_payouts.status=3 (failed) [tidak bisa jika belum langkah 2]
            $this->UserPayout->update($device_check->user_payout_id, ['status' => 3]);

			// update notes
			$this->UserPayoutDetail->update($device_check->user_payout_detail_id, ['description' => $device_check->description.'. '.$notes]);
		}
		// update device_check
		$this->DeviceCheck->update($device_check->check_id, ['status_internal' => $status_internal]);

		$this->db->transComplete();

		if ($this->db->transStatus() === FALSE) {
			// transaction has problems
			$response->message = "Failed to perform task! #trs03c";
		} else {
			$response->success = true;
			$response->message = "Successfully mark transaction <b>$device_check->check_code</b> as <b>$failed_text</b>";
		}

		return $response;
	}
}
