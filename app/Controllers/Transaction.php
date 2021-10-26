<?php

namespace App\Controllers;

use App\Libraries\PaymentsAndPayouts;
use App\Libraries\Xendit;
use App\Libraries\Mailer;
use App\Libraries\Nodejs;
use App\Libraries\FirebaseCoudMessaging;
use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
use App\Models\Users;
use App\Models\Appointments;
use App\Models\PaymentMethods;
use App\Models\Settings;
use App\Models\UserAdresses;
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
			return view('layouts/unauthorized', $this->data);
		} else {

			// make filter status option 
			$status = getDeviceCheckStatusInternal(-1); // all
			// unset($status[1]);
			// unset($status[2]);
			asort($status);
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '" ' . (in_array($key, [3, 4, 9, 10]) ? 'selected' : '') . '>' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-transaction',
					'title' => 'Transaction',
					'subtitle' => 'Appointments & Payments',
					'navbar' => 'Transaction',
				],
				'search' => $this->request->getGet('s') ?? '',
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'transaction_success' => false, // success transaction only
			];

			return view('transaction/index', $this->data);
		}
	}

	public function success()
	{
		// success only transaction, no action, for RDC
		helper('html');
		$check_role = checkRole($this->role, 'r_transaction_success');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {

			// make filter status option 
			$status = getDeviceCheckStatusInternal(-1); // all
			unset($status[1]);
			// unset($status[2]);
			// sort($status);
			$optionStatus = '<option value="5" selected>Completed</option>';

			$this->data += [
				'page' => (object)[
					'key' => '2-transaction_success',
					'title' => 'Transaction',
					'subtitle' => 'Success',
					'navbar' => 'Transaction',
				],
				'search' => $this->request->getGet('s') ?? '',
				'optionStatus' => $optionStatus,
				'transaction_success' => hasAccess($this->role, 'r_transaction_success') && !hasAccess($this->role, 'r_transaction'), // success transaction only
			];

			return view('transaction/index', $this->data);
		}
	}

	public function request_payment()
	{
		helper('html');
		$check_role = checkRole($this->role, 'r_request_payment');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			$this->data += [
				'page' => (object)[
					'key' => '2-request_payment',
					'title' => 'Transaction',
					'subtitle' => 'Request Payment',
					'navbar' => 'Request Payment',
				],
			];

			return view('transaction/request_payment', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, ['r_transaction', 'r_transaction_success']);
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
				// ->join("user_payouts as t3", "t3.user_id=t.check_id", "left")
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
			$select_fields = 't.check_id,check_code,imei,brand,model,storage,t.type,grade,t.status,status_internal,price,t2.name,customer_name,customer_phone,t.created_at,t4.status as payout_status,t5.alias_name as payment_method,courier_name,courier_phone, t6.address_id, t6.choosen_date, t6.choosen_time';

			// building where query
			$status = $req->getVar('status') ?? '';
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
			$where = ['t.deleted_at' => null];

			// filter $status
			if (hasAccess($this->role, 'r_transaction_success') && !hasAccess($this->role, 'r_transaction')) {
				// view success transaction only (and not have full access of transaction)
				$where += ['t.status_internal' => 5];
			} else
			if (is_array($status) && !in_array('all', $status)) {
				// replace value 'null' to be null
				// $key_null = array_search('null', $status);
				// if($key_null > -1) $status[$key_null] = null;
				// looping thourh $status array
				$this->builder->groupStart()
					->where(['t.status_internal' => $status[0]]);
				if (count($status) > 1)
					for ($i = 1; $i < count($status); $i++)
						$this->builder->orWhere(['t.status_internal' => $status[$i]]);
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
			// var_dump($this->db->getLastQuery());die;

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
					'change_payment' 		=> hasAccess($this->role, 'r_change_payment'),
					'change_address' 		=> hasAccess($this->role, 'r_change_address'),
					'transaction_success' 	=> hasAccess($this->role, 'r_transaction_success') && !hasAccess($this->role, 'r_transaction'),
					'logs'			 		=> hasAccess($this->role, 'r_logs'),
				];
				// var_dump($access);die;
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$attribute_data['default'] =  htmlSetData(['check_code' => $row->check_code, 'check_id' => $row->check_id]);
					$attribute_data['payment_detail'] =  htmlSetData(['payment_method' => $row->payment_method, 'account_name' => $row->customer_name, 'account_number' => $row->customer_phone]);
					$attribute_data['address_detail'] =  htmlSetData(['address_id' => $row->address_id]);
					$attribute_data['courier_detail'] =  htmlSetData(['courier_name' => $row->courier_name, 'courier_phone' => $row->courier_phone]);
					$changeTimeArr = explode("-", $row->choosen_time);

					$timeStart = $changeTimeArr[0];
					$timeStart = str_replace(".", ":", $timeStart);

					$timeLast = count($changeTimeArr) == 1 ? '' : $changeTimeArr[count($changeTimeArr) - 1];
					$timeLast = str_replace(".", ":", $timeLast);

					$choosen_date = $row->choosen_date;
					$choosen_date = date("Y-m-d", strtotime($choosen_date));
					// var_dump($newDate);die;


					$attribute_data['time_detail'] =  htmlSetData(['choosen_date' => $choosen_date, 'choosen_time' => $row->choosen_time, 'time_start' => $timeStart, 'time_last' => $timeLast]);

					// for status / status_internal
					$status = getDeviceCheckStatusInternal($row->status_internal);
					$status_color = 'default';
					if ($row->status_internal == 5) $status_color = 'success';
					elseif ($row->status_internal == 6 || $row->status_internal == 7) $status_color = 'danger';
					elseif ($row->status_internal == 4) $status_color = 'primary';
					elseif ($row->status_internal == 8) $status_color = 'warning';

					// for status_payment
					$color_payout_status = 'warning';
					if ($row->payout_status == 'COMPLETED') $color_payout_status = 'success';
					elseif ($row->payout_status == 'FAILED') $color_payout_status = 'danger';

					$action = '
					<button class="btn btn-xs mb-2 btn-' . $status_color . '">' . $status . '</button>
					';
					$btn = [
						'view' => !$access->transaction_success ? htmlAnchor([
							'color'	=> 'outline-secondary',
							'href'	=>	$url . $row->check_id,
							'class'	=> 'py-2 btnAction',
							'title'	=> "View detail of $row->check_code",
							'data'	=> '',
							'icon'	=> 'fas fa-eye',
							'text'	=> 'View',
						]) : '',
						'logs' => $access->logs ? htmlAnchor([
							'color'	=> 'outline-primary',
							'class'	=> "btnLogs",
							'title'	=> "View logs of $row->check_code",
							'data'	=> 'data-id="'.$row->check_id.'"',
							'icon'	=> 'fas fa-history',
							'text'	=> '',
						], false) : '',
						'status_payment' => htmlButton([
							'color'	=> $color_payout_status,
							'class'	=> 'btnStatusPayment',
							'title'	=> 'Payment status is: ' . $row->payout_status,
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> '',
							'text'	=> 'Payment: ' . $row->payout_status,
						]),
						'confirm_appointment' => $access->confirm_appointment ? htmlButton([
							'color'	=> 'warning',
							'class'	=> 'py-2 btnAction btnAppointment',
							'title'	=> 'Confirm the appointment of ' . $row->check_code,
							'data'	=> $attribute_data['default'] . ' data-type="confirm"',
							'icon'	=> 'fas fa-map-marker',
							'text'	=> 'Confirm Appointment',
						]) : '',
						'view_appointment' => $access->confirm_appointment ? htmlButton([
							'color'	=> 'warning',
							'class'	=> 'py-2 btnAction btnAppointment',
							'title'	=> 'Appointment of ' . $row->check_code,
							'data'	=> $attribute_data['default'] . $attribute_data['courier_detail'] . ' data-type="view"',
							'icon'	=> 'fas fa-map-marker',
							'text'	=> 'Appointment Detail',
						]) : '',
						'proceed_payment' => $access->proceed_payment ? htmlButton([
							'color'	=> 'success',
							'class'	=> 'py-2 btnAction btnProceedPayment',
							'title'	=> 'Finish this this transction with automatic transfer payment process',
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> 'fas fa-credit-card',
							'text'	=> 'Proceed Payment',
							'id'	=> 'pp-' . $row->check_code,
						]) : '',
						'manual_transfer' => $access->manual_transfer ? htmlButton([
							'color'	=> 'outline-success',
							'class'	=> 'py-2 btnAction btnManualTransfer',
							'title'	=> 'Finish this this transction with manual transfer',
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> 'fas fa-file-invoice-dollar',
							'text'	=> 'Manual Transafer',
						]) : '',
						'change_address' => $access->change_address ? htmlButton([
							'color'	=> 'info',
							'class'	=> 'py-2 btnAction btnChangeAddress',
							'title'	=> 'Change Address detail',
							'data'	=> $attribute_data['default'] . $attribute_data['address_detail'],
							'icon'	=> 'fas fa-edit',
							'text'	=> 'Change Address',
						]) : '',
						'change_courier' => $access->change_address ? htmlButton([
							'color'	=> 'outline-warning',
							'class'	=> 'py-2 btnAction btnChangeCourier',
							'title'	=> 'Change courier detail',
							'data'	=> $attribute_data['default'] . $attribute_data['courier_detail'],
							'icon'	=> 'fas fa-edit',
							'text'	=> 'Change courier',
						]) : '',
						'change_payment' => $access->change_payment ? htmlButton([
							'color'	=> 'primary',
							'class'	=> 'py-2 btnAction btnChangePayment',
							'title'	=> 'Change payment detail',
							'data'	=> $attribute_data['default'] . $attribute_data['payment_detail'],
							'icon'	=> 'fas fa-edit',
							'text'	=> 'Change Payment',
						]) : '',
						'mark_as_failed' => $access->mark_as_failed ? htmlButton([
							'color'	=> 'danger',
							'class'	=> 'py-2 btnAction btnMarkAsFailed',
							'title'	=> 'Mark this as failed transaction. A pop up confirmation will appears',
							'data'	=> $attribute_data['default'],
							'icon'	=> 'fas fa-info-circle',
							'text'	=> 'Mark as Failed',
						]) : '',
						'mark_as_cancelled' => $access->mark_as_failed ? htmlButton([
							'color'	=> 'danger',
							'class'	=> 'py-2 btnAction btnMarkAsFailed',
							'title'	=> 'Mark this as cancelled transaction. A pop up confirmation will appears',
							'data'	=> $attribute_data['default'] . ' data-failed="Cancelled"',
							'icon'	=> 'fas fa-info-circle',
							'text'	=> 'Mark as Cancelled',
						]) : '',
						'change_time' => $access->change_address ? htmlButton([
							'color'	=> 'outline-info',
							'class'	=> 'py-2 btnAction btnChangeTime',
							'title'	=> 'Change Time',
							'data'	=> $attribute_data['default'] . $attribute_data['time_detail'],
							'icon'	=> 'fas fa-edit',
							'text'	=> 'Change Time',
						]) : '',
					];

					if ($row->status_internal == 3) {
						// on appointment, action: confirm appointment, mark as cancel, change address, change payment
						$action .=	$btn['confirm_appointment']
							. $btn['change_address']
							. $btn['change_payment']
							. $btn['change_time']
							. $btn['mark_as_cancelled'];
					} elseif ($row->status_internal == 8) {
						// appointment confirmed, view appointment, change address, change courier, change payment, mark as cancelled
						$action .=	$btn['view_appointment']
							. $btn['change_time']
							. $btn['change_address']
							. $btn['change_courier']
							. $btn['change_payment']
							. $btn['mark_as_cancelled'];
					} elseif ($row->status_internal == 9) {
						// request cancel, action: mark as cancelled
						$action .=	$btn['mark_as_cancelled'];
					} elseif ($row->status_internal == 10) {
						// request payment, action: proceed payment, change payment, mark as cancelled 
						$action .= $btn['change_payment']
							. $btn['proceed_payment']
							. $btn['mark_as_cancelled'];
					} elseif ($row->status_internal == 4) {
						// Payment On Proces 
						$action .= $btn['status_payment'];

						// jika payment gateway gagal, show manual transfer
						if ($row->payout_status == 'FAILED') {
							$action .= $btn['change_payment']
								. $btn['proceed_payment']
								. $btn['manual_transfer']
								. $btn['mark_as_failed'];
						}
					}
					$action .= $btn['view'];

					$r = array();
					$r[] = $i;
					$r[] = substr($row->created_at, 0, 16);
					$r[] = $row->check_code;
					$r[] = $row->imei;
					$r[] = "$row->brand $row->model $row->storage $row->type";
					$r[] = "$row->grade<br>" . number_to_currency($row->price, "IDR");
					$r[] = "$row->name<br>$row->customer_name " . (true ? $row->customer_phone : "");
					$r[] = $btn['logs'].$action;
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

	function export()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_export_transaction')) {
			$status = $req->getVar('status') ?? '';
			$date = $req->getVar('date') ?? '';

			if (empty($date)) {
				$response->message = "Date range can not be blank";
			} else {
				$this->db = \Config\Database::connect();
				$this->table_name = 'device_checks';
				$this->builder = $this->db
					->table("$this->table_name as t")
					->join("device_check_details as t1", "t1.check_id=t.check_id", "left")
					->join("users as t2", "t2.user_id=t.user_id", "left")
					// ->join("user_payouts as t3", "t3.user_id=t.check_id", "left")
					->join("user_payout_details as t4", "t4.external_id=t.check_code", "left")
					->join("payment_methods t5", "t5.payment_method_id=t1.payment_method_id", "left")
					->join("appointments t6", "t6.check_id=t.check_id", "left")
					->join("view_addresses t7", "t7.check_id=t.check_id", "left");
	
				// select fields
				$select_fields = 'check_code,imei,brand,model,storage,t.type,grade,status_internal,price,fullset_price,t2.name
				,customer_name,customer_phone,customer_email,t.created_at as transaction_date
				,t4.status as payment_status,t4.created_at as payment_date,t4.updated_at as payment_update,t4.failure_code as payment_failure,t4.bank_code as payment_method,t4.account_holder_name as payment_acc_name,t4.account_number as payment_acc_number
				,t7.province,t7.city,t7.district,t7.postal_code,t7.notes as address_detail
				,courier_name,courier_phone,t6.choosen_date,t6.choosen_time
				,t1.general_notes
				';

				// building where query
				$dates = explode(' - ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
				$where = ['t.deleted_at' => null];
	
				// filter $status
				if (is_array($status) && !in_array('all', $status)) {
					// replace value 'null' to be null
					// $key_null = array_search('null', $status);
					// if($key_null > -1) $status[$key_null] = null;
					// looping thourh $status array
					$this->builder->groupStart()
						->where(['t.status_internal' => $status[0]]);
					if (count($status) > 1)
						for ($i = 1; $i < count($status); $i++)
							$this->builder->orWhere(['t.status_internal' => $status[$i]]);
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
						'view_phone_no' => hasAccess($this->role, 'r_view_phone_no'),
						'view_email' => hasAccess($this->role, 'r_view_email'),
						'view_payment_detail' => hasAccess($this->role, 'r_view_payment_detail'),
						'view_address' => hasAccess($this->role, 'r_view_address'),
					];
					$path = 'temp/csv/';
					$filename = 'transaction-' . date('YmdHis') . '.csv';
					$fp = fopen($path . $filename, 'w');
					$headers = [
						'No',
						'Transaction Date',
						'Check Code',
						'Status Internal',
						'IMEI',
						'Brand',
						'Model',
						'Storage',
						'Type',
						'Grade',
						'Price',
						'Fullset Price',
						'Member Name',
						'Customer Name',
					];
					if($access['view_phone_no'])  array_push($headers, 'Customer Phone');
					if($access['view_email']) array_push($headers, 'Customer Email');
					if($access['view_payment_detail']) $headers = array_merge($headers, [
						'Payment Status',
						'Payment Date',
						'Payment Update',
						'Failure Payment',
						'Payment Acc. Name',
						'Payment Acc. Number',
						'Payment Method',
					]);
					if($access['view_address']) $headers = array_merge($headers, [
						'Province',
						'City',
						'District',
						'Postal Code',
						'Address Detail',
					]);
					$headers = array_merge($headers, [
						'Courier Name',
						'Courier Phone',
						'Choosen Date',
						'Choosen Time',
						'Notes',
					]);
					fputcsv($fp, $headers);

					// looping through data result & put in csv
					foreach ($dataResult as $row) {
						// var_dump($row);die;
						$r = [
							$i++,
							$row->transaction_date,
							$row->check_code,
							getDeviceCheckStatusInternal($row->status_internal),
							$row->imei,
							$row->brand,
							$row->model,
							$row->storage,
							$row->type,
							$row->grade,
							$row->price,
							$row->fullset_price,
							$row->name,
							$row->customer_name,
						];
						if($access['view_phone_no']) array_push($r, $row->customer_phone);
						if($access['view_email']) array_push($r, $row->customer_email);
						if($access['view_payment_detail']) $r = array_merge($r, [
							$row->payment_status,
							$row->payment_date,
							$row->payment_update,
							$row->payment_failure,
							$row->payment_acc_name,
							$row->payment_acc_number,
							$row->payment_method,
						]);
						if($access['view_address']) $r = array_merge($r, [
							$row->province,
							$row->city,
							$row->district,
							$row->postal_code,
							$row->address_detail,
						]);
						$r = array_merge($r, [
							$row->courier_name,
							$row->courier_phone,
							$row->choosen_date,
							$row->choosen_time,
							$row->general_notes,
						]);

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
					if ($this->google->checkCode($setting->val, $code_auth) || env('app.environment') == 'local') {
						$select = 'dc.check_id,check_code,price,dc.user_id,dcd.account_number,dcd.account_name,pm.name as bank_code';
						$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
						$whereIn = ['status_internal' => [10, 4]]; // for status request payment && payment on process
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
					$select = 'dc.check_id,check_detail_id,status_internal,user_payout_detail_id,dc.fcm_token,dc.user_id';
					// $select for email
					$select .= ',check_code,brand,model,storage,imei,dc.type as dc_type,u.name,customer_name,customer_email,dcd.account_number,dcd.account_name,pm.name as pm_name,ub.notes as ub_notes,ub.type as ub_type,ub.currency,ub.currency_amount,check_code as referrence_number';
					$where = array('dc.check_id' => $check_id, 'dc.status_internal' => 4, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						// #belum selesai
						$notes = $this->request->getPost('notes') ?? '';
						$transfer_proof = $this->request->getFile('transfer_proof');
						$transfer_proof_random_name = $transfer_proof->getRandomName();
						if ($transfer_proof->move('uploads/transfer/', $transfer_proof_random_name)) {
							// main logic of manual_transfer
							$response = $this->manual_transfer_logic($device_check, $notes, $transfer_proof_random_name);
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
		// update device_check_details.transfer_notes,transfer_proof
		$data_device_check_detail = [
			'transfer_notes' => $notes,
			'transfer_proof' => $transfer_proof,
		];
		$data['device_check_detail'] = $data_device_check_detail;
		// $this->DeviceCheckDetail->update($device_check->check_detail_id, $data_device_check_detail);

		// update where(check_id) user_payouts.type='manual'
		$data_payout_detail = [
			'type'			=> 'manual',
			'status'		=> 'COMPLETED',
			'updated_at'	=> date('Y-m-d H:i:s'),
		];
		$data['payout_detail'] = $data_payout_detail;
		// $payment_and_payout->updatePayoutDetail($device_check->user_payout_detail_id, $data_payout_detail);

		// lakukan logic payement success
		$payment_and_payout = new PaymentsAndPayouts();
		$payment_success = $payment_and_payout->updatePaymentSuccess($device_check->check_id);


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
			unset($device_check->fcm_token);
			$this->log->in("$device_check->check_code\n".session()->username, 37, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);
			$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);
		}

		return $response;
	}

	function mark_as_failed()
	{
		$response = initResponse('Unauthorized.');
		$rules = ['check_id' => getValidationRules('check_id')];
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$check_id = $this->request->getPost('check_id');
			if (hasAccess($this->role, 'r_mark_as_failed')) {
				$select = 'dc.check_id,check_detail_id,check_code,status_internal,dc.user_id,upa.user_payout_id,upad.user_payout_detail_id,upad.description,dc.fcm_token';
				$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
				$whereIn = ['status_internal' => [3, 8, 4, 9, 10]];
				$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select, '', $whereIn);
				if (!$device_check) {
					$response->message = "Invalid check_id $check_id";
				} else {
					$notes = $this->request->getPost('notes') ?? '';
					$response = $this->mark_as_failed_logic($device_check, $notes);
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
		if (in_array($device_check->status_internal, [3, 8, 9, 10])) { // status_internal 3,8,9,10 untuk cancel
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

			// send notif
			$content = "Unfortunately, your transaction is mark as $failed_text";
			$this->sendNotificationUpdateToApp1($response, $device_check, $content);

			// logs
			$log_cat = 9;
			unset($device_check->fcm_token);
			$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

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
				if (hasAccess($this->role, 'r_confirm_appointment')) {
					$select = 'dc.check_id,check_code,imei,brand,model,storage,dc.type,grade,price,survey_fullset,customer_name,customer_phone,choosen_date,choosen_time,ap.name as province_name, ap.province_id,ac.name as city_name, ac.city_id,ad.name as district_name, ad.district_id, postal_code,adr.notes as full_address,pm.type as bank_emoney,pm.name as bank_code,account_number,account_name,account_name_check,account_bank_check,account_bank_error,courier_name,courier_phone,dcd.payment_method_id, adr.address_id';
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
				if (hasAccess($this->role, 'r_confirm_appointment')) {
					$select = 'dc.check_id,check_code,customer_name,appointment_id,dc.fcm_token,dc.user_id';
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

							// send notif
							$content = "Yeay!! Your appointment was Confirmed. Courier: $courier_name ($courier_phone)";
							$this->sendNotificationUpdateToApp1($response, $device_check, $content);

							// logs
							$log_cat = 10;
							$data = [];
							$data += $data_appointment;
							$data += $data_device_check;
							$data['device_check'] = $device_check;
							unset($device_check->fcm_token);
							$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

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
		$rules = getValidationRules('transaction:validate_bank');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			if (hasAccess($this->role, 'r_confirm_appointment')) {
				$check_id = $this->request->getPost('check_id');
				$payment_method_id = $this->request->getPost('payment_method_id');
				$account_number = $this->request->getPost('account_number');
				$account_name = $this->request->getPost('account_name');

				$where = [
					'deleted_at' => null,
					'payment_method_id' => $payment_method_id,
					'status' => 'active'
				];
				$PaymentMethod = new PaymentMethods();
				$payment_method = $PaymentMethod->getPaymentMethod($where, 'payment_method_id,type,name,alias_name');
				if (!$payment_method) {
					$response->message = "No payment method available ($payment_method_id)";
				} else {
					$select = 'dc.check_id,check_detail_id,check_code,pm.type as bank_emoney,pm.name as bank_code,account_number,account_name';
					$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
					$device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select);
					if (!$device_check) {
						$response->message = "Invalid check_id $check_id";
					} else {
						$Xendit = new Xendit();
						$valid_bank_detail = $Xendit->validate_bank_detail($payment_method->name, $account_number);
						if (!$valid_bank_detail->success) {
							$response->message = "Unable to check the payment method of $device_check->check_code";
						} else {
							// parse xendit response to update device_check_details
							$status = $valid_bank_detail->data->status ?? 'PENDING';
							$bank_account_holder_name = $valid_bank_detail->data->bank_account_holder_name ?? 'PENDING';
							$data_update = [
								'account_bank_check' => 'pending',
								'account_name_check' => $bank_account_holder_name
							];
							$response->message = "Validation is on process, please try again in a view seconds.";
							if ($status == 'SUCCESS') {
								if ($bank_account_holder_name == $account_name) {
									$data_update['account_bank_check'] = 'valid';
									$response->success = true;
									$response->message = "$account_number of $payment_method->name is <b class=\"text-success\">valid</b>.";
								} else {
									$data_update['account_bank_check'] = 'invalid';
									$data_update['account_bank_error'] = 'DIFFERENT_NAME';
									$response->message = "$account_number of $payment_method->name is <b class=\"text-success\">valid</b>, but has <b class=\"text-danger\">different name</b>.";
								}
							} elseif ($status == 'FAILURE') {
								$response->message = "$account_number of $payment_method->name is <b class=\"text-danger\">invalid</b>.";
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

	function change_payment()
	{
		$response = initResponse('Unauthorized.');
		$rules = getValidationRules('transaction:change_payment');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			if (hasAccess($this->role, 'r_proceed_payment')) {
				$check_id = $this->request->getPost('check_id');
				$payment_method_id = $this->request->getPost('payment_method_id');
				$account_number = $this->request->getPost('account_number');
				$account_name = $this->request->getPost('account_name');
				$select = 'dc.check_id,check_code,check_detail_id,price,dc.user_id,dcd.account_number,dcd.account_name,pm.name as bank_code,pm.payment_method_id,dc.fcm_token';
				$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
				$whereIn = ['status_internal' => [3, 8, 4]];
				$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select, '', $whereIn);
				if (!$device_check) {
					$response->message = "Invalid check_id $check_id";
				} else {
					$where = [
						'deleted_at' => null,
						'payment_method_id' => $payment_method_id,
						'status' => 'active'
					];
					$PaymentMethod = new PaymentMethods();
					$payment_method = $PaymentMethod->getPaymentMethod($where, 'payment_method_id,type,name,alias_name');
					if (!$payment_method) {
						$response->message = "No payment method available ($payment_method_id)";
					} else {
						$data_update = [
							'payment_method_id' => $payment_method_id,
							'account_number' => $account_number,
							'account_name' => $account_name,
						];
						$Xendit = new Xendit();
						$valid_bank_detail = $Xendit->validate_bank_detail($payment_method->name, $account_number);
						if ($valid_bank_detail->success) {
							$status = $valid_bank_detail->data->status ?? 'PENDING';
							if ($status == 'SUCCESS') {
								if ($valid_bank_detail->data->bank_account_holder_name == $account_name) {
									$data_update['account_bank_check'] = 'valid';
									$data_update['account_bank_error'] = '';
								} else {
									$data_update['account_bank_check'] = 'invalid';
									$data_update['account_bank_error'] = 'DIFFERENT_NAME';
								}
							} elseif ($status == 'FAILURE') {
								$data_update['account_bank_check'] = 'invalid';
								$data_update['account_bank_error'] = $valid_bank_detail->data->failure_reason ?? '';
							}
						}
						$this->db = \Config\Database::connect();
						$this->db->transStart();
						$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_update);
						$this->db->transComplete();

						if ($this->db->transStatus() === FALSE) {
							// transaction has problems
							$response->message = "Failed to perform task! #trs03c";
						} else {
							$response->success = true;
							$response->message = "Successfully change payment detail for <b>$device_check->check_code</b>";

							// send notif
							$content = "Changes on Payment Details";
							$this->sendNotificationUpdateToApp1($response, $device_check, $content);

							// logs
							$log_cat = 24;
							$data_update += [
								'type' => $payment_method->type,
								'name' => $payment_method->name,
								'alias_name' => $payment_method->alias_name
							];
							$data = [
								'payment' => $data_update,
								'device' => $device_check,
							];
							unset($device_check->fcm_token);
							$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	function payment_method()
	{
		$response = initResponse('Unauthorized.');
		$check_id = $this->request->getPost('check_id');
		if (hasAccess($this->role, 'r_change_payment')) {
			$type = $this->request->getPost('type') ?? 'bank';
			$type = $type == 'bank' ? $type : 'emoney';
			$where = [
				'deleted_at' => null,
				'type' => $type,
				'status' => 'active'
			];
			$PaymentMethod = new PaymentMethods();
			$payment_method = $PaymentMethod->getPaymentMethods($where, 'payment_method_id,type,name,alias_name');

			if (!$payment_method) {
				$response->message = "No payment method available";
			} else {
				$response->success = true;
				$response->message = 'OK';
				$response->data = $payment_method;
			}
		}
		return $this->respond($response);
	}

	function change_address()
	{
		$response = initResponse('Unauthorized.');

		$rules = getValidationRules('transaction:change_address');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			if (hasAccess($this->role, 'r_change_address')) {
				$check_id = $this->request->getPost('check_id');
				$address_id = $this->request->getPost('address_id');
				$district_id = $this->request->getPost('district_id');
				$postal_code = $this->request->getPost('postal_code');
				$full_address = $this->request->getPost('full_address');

				$select = 'dc.check_id,check_code,check_detail_id,price,dc.user_id,dc.fcm_token,';
				$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
				$whereIn = ['status_internal' => [3, 8, 4]];
				$device_check = $this->DeviceCheck->getDeviceDetailAddress($where, $select, '', $whereIn);
				if (!$device_check) {
					$response->message = "Invalid check_id $check_id";
				} else {
					$where = [
						'district_id' => $district_id,
						'status' => 'active'
					];
					$district_data = $this->Appointment->getAddressDistrict($where, 'district_id,name');
					if (!$district_data) {
						$response->message = "No district id available ($district_id)";
					} else {
						$data_update = [
							'district_id' => $district_id,
							'postal_code' => $postal_code,
							'notes' => $full_address,
							'updated_at'	=> date('Y-m-d H:i:s'),
						];

						$this->db = \Config\Database::connect();
						$this->db->transStart();
						$addresses = new UserAdresses();
						$addresses->update($address_id, $data_update);
						$this->db->transComplete();

						if ($this->db->transStatus() === FALSE) {
							// transaction has problems
							$response->message = "Failed to perform task! #uAu01a";
						} else {
							$response->success = true;
							$response->message = "Successfully change address detail for <b>$device_check->check_code</b>";

							// send notif
							$content = "Changes on Address Details";
							$this->sendNotificationUpdateToApp1($response, $device_check, $content);

							// logs
							$log_cat = 27;
							$data_update += [
								'name district' => $district_data->name,
							];
							$data = [
								'addresses' => $data_update,
								'device' => $device_check,
							];
							unset($device_check->fcm_token);
							$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	function change_courier()
	{
		$response = initResponse('Unauthorized.');
		$check_id = $this->request->getPost('check_id');
		$courier_name = $this->request->getPost('courier_name');
		$courier_phone = $this->request->getPost('courier_phone');
		$rules = getValidationRules('transaction:confirm_appointment');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			if (hasAccess($this->role, 'r_change_address')) {
				$select = 'dc.check_id,check_code,customer_name,appointment_id,dc.fcm_token,dc.user_id';
				$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null, 'dc.status_internal' => '8');
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

					$this->db->transComplete();

					if ($this->db->transStatus() === FALSE) {
						// transaction has problems
						$response->message = "Failed to perform task! #trs03c";
					} else {
						$response->success = true;
						$response->message = "Successfully Change Courier for <b>$device_check->check_code</b>";

						// send notif
						$content = "Changes on Courier Details. Courier: $courier_name ($courier_phone)";
						$this->sendNotificationUpdateToApp1($response, $device_check, $content);

						// logs
						$log_cat = 28;
						$data = [];
						$data += $data_appointment;
						$data['device_check'] = $device_check;
						unset($device_check->fcm_token);
						$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

					}
				}
			}
		}
		return $this->respond($response);
	}

	function do_request_payment()
	{
		$response = initResponse('Unauthorized.');
		$rules = getValidationRules('transaction:request_payment');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$check_code = $this->request->getPost('check_code');
			$account_number = $this->request->getPost('account_number');
			if (hasAccess($this->role, 'r_request_payment')) {
				$select = 'dc.check_id,check_code,price,dc.user_id,dcd.account_number,dcd.account_name,pm.name as bank_code,dc.fcm_token';
				$where = array('dc.check_code' => $check_code, 'status_internal' => 8, 'dc.deleted_at' => null);
				$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
				if (!$device_check) {
					$response->message = "Invalid check_code $check_code";
				} else {
					if ($device_check->account_number != $account_number) {
						$response->message = "Account number $account_number did not match. Please recheck the data.";
					} else {
						$this->db = \Config\Database::connect();
						$data_device_check = ['status_internal' => 10];
						$affected_row = $this->DeviceCheck->update($device_check->check_id, $data_device_check);
						if ($affected_row < 1) {
							$response->message = "Update operation error. Please try again or contact your IT Master. " . json_encode($this->db->error());
						} else {
							helper("number");
							$response->message = "Payment Requested for <b>$device_check->check_code</b> to <b>$account_number</b> (<b>$device_check->bank_code</b> a.n <b>$device_check->account_name</b>) <b>" . number_to_currency($device_check->price, "IDR") . "</b>";
							$response->success = true;
							
							// send notif
							$content = "Yeay!! Payment for $device_check->check_code was requested.";
							$this->sendNotificationUpdateToApp1($response, $device_check, $content);

							// broadcast notif
							$nodejs = new Nodejs();
							$nodejs->emit('notification', [
								'type' => 1,
								'message' => session()->username . " request payment for $device_check->check_code",
							]);

							// logs
							$log_cat = 29;
							$data = [];
							$data['update'] = $data_device_check;
							$data['device_check'] = $device_check;
							unset($device_check->fcm_token);
							$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

						}
					}
				}
			}
		}
		return $this->respond($response);
	}

	function change_time()
	{
		$response = initResponse('Unauthorized.');
		$check_id = $this->request->getPost('check_id');
		$choosen_date = $this->request->getPost('choosen_date');
		$choosen_time = $this->request->getPost('choosen_time');
		$rules = getValidationRules('transaction:change_appoinment_time');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			if (hasAccess($this->role, 'r_change_address')) {
				$select = 'dc.check_id,check_code,customer_name,appointment_id,dc.fcm_token,dc.user_id';
				$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
				$whereIn = [
					'status_internal' => [3, 8],
				];
				$device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select, false, $whereIn);
				if (!$device_check) {
					$response->message = "Invalid check_id $check_id";
				} else {
					$this->db = \Config\Database::connect();
					$this->db->transStart();
					$data_appointment = [
						'choosen_date'			=> $choosen_date,
						'choosen_time'			=> $choosen_time,
					];
					$this->Appointment->update($device_check->appointment_id, $data_appointment);

					$this->db->transComplete();

					if ($this->db->transStatus() === FALSE) {
						// transaction has problems
						$response->message = "Failed to perform task! #trs03c";
					} else {
						$response->success = true;
						$response->message = "Successfully Change Appoinment Time for <b>$device_check->check_code</b>";

						// send notif
						$content = "Changes on Appointment: $choosen_date $choosen_time WIB";
						$this->sendNotificationUpdateToApp1($response, $device_check, $content);

						// logs
						$log_cat = 30;
						$data = [];
						$data += $data_appointment;
						$data['device_check'] = $device_check;
						unset($device_check->fcm_token);
						$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);

					}
				}
			}
		}
		return $this->respond($response);
	}

	function status_payment()
	{
		$response = initResponse('Unauthorized.');
		$check_id = $this->request->getPost('check_id');
		$rules = ['check_id' => getValidationRules('check_id')];
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			$select = 'dc.check_id,check_code,upad.type,upad.amount,upad.bank_code,upad.account_holder_name as account_name,upad.account_number,upad.description,upad.status,upad.failure_code,upad.created_at,upad.updated_at';
			$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
			$device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
			if (!$device_check) {
				$response->message = "Invalid check_id $check_id";
			} else {
				$response->success = true;
				$response->message = 'OK';
				$response->data = $device_check;
			}
		}
		return $this->respond($response);
	}

	private function sendNotificationUpdateToApp1(&$response, $device_check, $content) {
		try {
			$title = "New status for $device_check->check_code";
			$notification_data = [
				'check_id'	=> $device_check->check_id,
				'type'		=> 'final_result'
			];

			// for app_1
			$fcm = new FirebaseCoudMessaging();
			$send_notif_app_1 = $fcm->send($device_check->fcm_token, $title, $content, $notification_data);
			$response->data['send_notif_app_1'] = $send_notif_app_1;
		} catch (\Exception $e) {
			$response->message .= " But, unable to send notification: " . $e->getMessage();
		}
	}
}
