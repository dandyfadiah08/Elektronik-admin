<?php

namespace App\Controllers;

use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
use App\Models\GradeChanges;
use App\Models\MasterPrices;
use App\Models\Users;
use App\Libraries\FirebaseCoudMessaging;
use App\Libraries\Nodejs;

class Device_check extends BaseController
{
	protected $DeviceCheck, $DeviceCheckDetail, $Admin, $AdminRole;

	public function __construct()
	{
		$this->DeviceCheck = new DeviceChecks();
		$this->DeviceCheckDetail = new DeviceCheckDetails();
		helper('grade');
		helper('validation');
		helper('device_check_status');
		helper('log');
	}

	public function index()
	{
		helper('html');

		$check_role = checkRole($this->role, 'r_device_check');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			// make filter status option 
			$status = getDeviceCheckStatus(-1); // all
			unset($status[5]);
			unset($status[6]);
			unset($status[7]);
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$selected = $key == 4 ? 'selected' : '';
				$optionStatus .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-unreviewed',
					'title' => 'Unreviewed',
					'subtitle' => 'Device Checks',
					'navbar' => 'Unreviewed',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'reviewed' => 0,
				'optionStatus' => $optionStatus,
			];

			return view('device_check/index', $this->data);
		}
	}

	public function reviewed()
	{
		helper('html');

		$check_role = checkRole($this->role, 'r_device_check');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			// make filter status option 
			$status = getDeviceCheckStatus(-1); // all
			unset($status[1]);
			unset($status[2]);
			unset($status[3]);
			unset($status[4]);
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-reviewed',
					'title' => 'Reviewed',
					'subtitle' => 'Device Checks',
					'navbar' => 'Reviewed',
				],
				'unreviewed_count' => $this->unreviewed_count,
				'transaction_count' => $this->transaction_count,
				'withdraw_count' => $this->withdraw_count,
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'reviewed' => 1,
				'optionStatus' => $optionStatus,
			];

			return view('device_check/index', $this->data);
		}
	}

	public function detail($check_id = 0)
	{
		// if (!session()->has('admin_id')) return redirect()->to(base_url());
		$check_role = checkRole($this->role, 'r_device_check');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			$this->data += [
				'page' => (object)[
					'key' => '2-unreviewed',
					'title' => 'Device Checks',
					'subtitle' => 'Details',
					'navbar' => 'Details',
				],
				'isResultPage' => false,
			];

			if ($check_id < 1) return view('layouts/unauthorized', $this->data);
			$select = 'check_code,dc.status as dc_status,status_internal,imei,brand,model,storage,dc.type,price,grade,type_user,dc.created_at as check_date,dc.price_id,dc.promo_id
			,mp.promo_name
			,u.name
			,pm.alias_name as pm_name,pm.type as pm_type
			,adr.postal_code,ap.name as province_name,ac.name as city_name,ad.name as district_name,adr.notes as full_address
			,dcd.*';
			$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
			$device_check = $this->DeviceCheck->getDeviceDetailFull($where, $select);
			if (!$device_check) {
				$this->data += ['url' => base_url() . 'device_check/detail/' . $check_id];
				return view('layouts/not_found', $this->data);
			}
			helper('html');
			helper('number');
			helper('format');
			// var_dump($device_check);die;
			$this->data += [
				'dc' => $device_check,
				'access_logs' => hasAccess($this->role, 'r_logs'),
			];
			$this->data['page']->subtitle = $device_check->check_code;
			if ($device_check->dc_status > 4) {
				$view = 'result';
				$this->data['isResultPage'] = true;
				$this->data['page']->key = '2-reviewed';
			} else $view = 'detail';
			return view('device_check/' . $view, $this->data);
		}
	}


	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		if (!hasAccess($this->role, 'r_device_check')) {
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
				->join("users as t2", "t2.user_id=t.user_id", "left");

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
			$select_fields = 't.check_id,check_code,imei,brand,model,storage,t.type,grade,t.status,status_internal,price,name,customer_name,customer_phone,t.created_at';

			// building where query
			$reviewed = $req->getVar('reviewed') ?? 0;
			$is_reviewed = $reviewed == 1;
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
			if ($status > 0 && $status != 'all') $where += ['t.status' => $status];
			elseif ($is_reviewed) $where += ['t.status>' => 4];
			else $where += ['t.status<' => 5];

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
			$dataResult = $this->builder->get()->getResult();
			// die($this->db->getLastQuery());

			$data = [];
			if (count($dataResult) > 0) {
				$i = $start;
				helper('number');
				helper('html');
				helper('format');
				$url = base_url() . '/device_check/detail/';
				$access['logs'] = hasAccess($this->role, 'r_logs');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$status = getDeviceCheckStatus($row->status);
					$status_color = 'default';
					if ($row->status_internal == 5) $status_color = 'success';
					elseif ($row->status_internal > 5) $status_color = 'danger';
					elseif ($row->status == 4 || $row->status_internal == 4) $status_color = 'primary';
					elseif ($row->status == 7) $status_color = 'success';
					$price = "-";
					$action = '<button class="btn btn-xs mb-2 btn-default">' . $status . '</button>';
					if ($is_reviewed) {
						$price = number_to_currency($row->price, "IDR");
						$action .= '<br><button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Status Internal ' . $row->status_internal . '">' . getDeviceCheckStatusInternal($row->status_internal) . '</button>';
					}
					$btn['view'] = [
						'color'	=> 'outline-secondary',
						'href'	=>	$url . $row->check_id,
						'class'	=> 'py-2 btnAction',
						'title'	=> "View detail of $row->check_code",
						'data'	=> '',
						'icon'	=> 'fas fa-eye',
						'text'	=> 'View',
					];
					$btn['logs'] = [
						'color'	=> 'outline-primary',
						'class'	=> "btnLogs".($access['logs'] ? '' : ' d-none'),
						'title'	=> "View logs of $row->check_code",
						'data'	=> 'data-id="'.$row->check_id.'"',
						'icon'	=> 'fas fa-history',
						'text'	=> '',
					];
					$action .= htmlAnchor($btn['view']);

					$r = [];
					$r[] = $i;
					$r[] = formatDate($row->created_at);
					$r[] = $row->check_code;
					$r[] = $row->imei;
					$r[] = "$row->brand $row->model $row->storage $row->type";
					$r[] = "$row->grade<br>$price";
					$r[] = "$row->name<br>$row->customer_name " . (true ? $row->customer_phone : "");
					$r[] = htmlAnchor($btn['logs'], false).$action;
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
		return $this->respond($json_data);
	}

	function export()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_export_device_check')) {
			$reviewed = $req->getVar('reviewed') ?? 0;
			$status = $req->getVar('status') ?? '';
			$date = $req->getVar('date') ?? '';

			if(empty($date)) {
				$response->message = "Date range can not be blank";
			} else {
				$this->db = \Config\Database::connect();
				$this->table_name = 'device_checks';
				$this->builder = $this->db
					->table("$this->table_name as t")
					->join("device_check_details as t1", "t1.check_id=t.check_id", "left")
					->join("users as t2", "t2.user_id=t.user_id", "left");

				// select fields
				$select_fields = 't.check_id,check_code,imei,brand,model,storage,t.type,grade,t.status,status_internal,price,fullset_price,name,customer_name,customer_phone,t.created_at';

				// building where query
				$is_reviewed = $reviewed == 1;
				$dates = explode(' - ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}

				$where = ['t.deleted_at' => null];
				if ($status > 0 && $status != 'all') $where += ['t.status' => $status];
				elseif ($is_reviewed) $where += ['t.status>' => 4];
				else $where += ['t.status<' => 5];

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
					$path = 'temp/csv/';
					$filename = 'device-check-'.date('YmdHis').'.csv';
					$fp = fopen($path.$filename, 'w');
					fputcsv($fp, [
						'No',
						'Transaction Date',
						'Check Code',
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
						'Status',
						'Status Internal',
					]);

					// looping through data result & put in csv
					foreach ($dataResult as $row) {
						$r = [];
						$r[] = $i++;
						$r[] = $row->created_at;
						$r[] = $row->check_code;
						$r[] = $row->imei;
						$r[] = $row->brand;
						$r[] = $row->model;
						$r[] = $row->storage;
						$r[] = $row->type;
						$r[] = $row->grade;
						$r[] = $row->price;
						$r[] = $row->fullset_price;
						$r[] = $row->name;
						$r[] = $row->customer_name;
						$r[] = getDeviceCheckStatus($row->status);
						$r[] = getDeviceCheckStatusInternal($row->status_internal);

						fputcsv($fp, $r);
					}
					$response->success = true;
					$response->message = "Done";
					$response->data = base_url('download/csv/?file='.$filename);
				}
			}
		}
		return $this->respond($response);
	}

	function manual_grade()
	{
		$response_code = 200;
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$grades = getGradeDefinition('all'); // get all grades
			$grades += ['Reject' => 'Rejected']; // add reject

			$check_id = $this->request->getPost('check_id');
			$grade = $this->request->getPost('grade');
			$fullset = $this->request->getPost('fullset') ?? 0;
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} elseif (!array_key_exists($grade, $grades)) {
				$response->message = 'Grade tidak diketahui: ' . $grade;
			} else {
				$role = $this->AdminRole->find(session()->role_id);
				$check_role = checkRole($role, 'r_review');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					// update survey_fullset
					$this->DeviceCheckDetail
						->where(['check_id' => $check_id])
						->set(['survey_fullset' => $fullset])
						->update();
					$survey_by = session()->admin_id;
					$survey_name = session()->username;
					$response = $this->survey($check_id, $grade, $survey_by, $survey_name);
					// if (!$response->success) $response_code = 400;
					// else $response_code = 200;
				}
			}
		}
		return $this->respond($response, $response_code);
	}

	private function survey($check_id, $grade, $survey_id, $survey_name, $survey_log = 'manual', $send_notification = true, $quiz = [1, 1, 1, 1])
	{
		$response = initResponse('Failed add grade!');

		$select = 'dc.status,check_code,dc.user_id,dc.price_id,check_detail_id,survey_fullset,user_id,brand,storage,type,fcm_token';
		$where = array('dc.check_id' => $check_id, 'dc.status' => 4, 'dc.deleted_at' => null);
		$device_check = $this->DeviceCheck->getDeviceDetail($where, $select);
		if (!$device_check) {
			$response->message = "Invalid check_id $check_id";
		} else {
			$response->success = true;
			$response->message = "Success give $grade grade. ";
			$response->data['web'] = [
				'check_code' => $device_check->check_code,
				'grade' => $grade,
				'price' => '0',
				'survey_name' => $survey_name,
			];
			$price = 0;
			$fullset_price = 0;
			if ($grade != 'Reject') {
				// $where_price = ['promo_id' => $device_check->promo_id, 'brand' => $device_check->brand, 'model' => $device_check->model, 'storage' => $device_check->storage, 'deleted_at' => null];
				$where_price = ['price_id' => $device_check->price_id, 'deleted_at' => null];
				$select_price = 'price_s,price_a,price_b,price_c,price_d,price_e,price_fullset';
				$MasterPrice = new MasterPrices();
				$master_price = $MasterPrice->getPrice($where_price, $select_price); //, 'price_id DESC');
				if (!$master_price) {
					$response->message = "Invalid price ($device_check->price_id)!";
					$response->success = false;
				} else {
					// define $price
					if ($grade == 'S') $price = $master_price->price_s;
					elseif ($grade == 'A') $price = $master_price->price_a;
					elseif ($grade == 'B') $price = $master_price->price_b;
					elseif ($grade == 'C') $price = $master_price->price_c;
					elseif ($grade == 'D') $price = $master_price->price_d;
					elseif ($grade == 'E') $price = $master_price->price_e;

					// define $fullset_price & update $price
					if ($device_check->survey_fullset == 1) {
						$fullset_price = $master_price->price_fullset;
						$price += $fullset_price;
					}
					helper('number');
					$response->message .= "(" . number_to_currency($price, "IDR") . ")";
					$response->data['web']['price'] = $price;
				}
			}

			if ($response->success) {
				// update data
				$data_update = [
					'price'		=> $price,
					'grade'		=> $grade,
					'status'	=> 5,
				];
				$data_update_detail = [
					'fullset_price' => $fullset_price,
					'survey_quiz_1' => $quiz[0],
					'survey_quiz_2' => $quiz[1],
					'survey_quiz_3' => $quiz[2],
					'survey_quiz_4' => $quiz[3],
					'survey_id'		=> $survey_id,
					'survey_name'	=> $survey_name,
					'survey_log'	=> $survey_log,
					'survey_date'	=> date('Y-m-d H:i:s'),
				];
				$this->DeviceCheck->update($check_id, $data_update);
				$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_update_detail);

				$data_log = array_merge($data_update, $data_update_detail);
				$this->log->in("$device_check->check_code\n".session()->username, 42, json_encode($data_log), session()->admin_id, $device_check->user_id, $check_id);

				$nodejs = new Nodejs();
				$nodejs->emit('notification', [
					'type' => 1,
					'message' => session()->username . " gives $device_check->check_code grade $grade" . ($price == 0 ? "" : " (" . number_to_currency($price, "IDR") . ")"),
				]);

				// send notification
				if ($send_notification) {
					try {
						$title = $grade == 'Reject' ? "Sorry" : "Congatulation, Your $device_check->type price is ready!";
						$content = $grade == 'Reject'
							? "Unfortunately, we could not calculate a price for your phone."
							: "Your phone $device_check->brand $device_check->type $device_check->storage price is " . number_to_currency($price, "IDR");
						$notification_data = [
							'check_id'	=> $check_id,
							'type'		=> 'final_result'
						];

						// for app_1
						$fcm = new FirebaseCoudMessaging();
						$send_notif_app_1 = $fcm->send($device_check->fcm_token, $title, $content, $notification_data);
						$response->data['send_notif_app_1'] = $send_notif_app_1;

						// for app_2
						// get notification_token from $device_check->user_id
						$UserModel = new Users();
						$user = $UserModel->getUser(['user_id' => $device_check->user_id], 'name,notification_token');
						if ($user) {
							$notification_token = $user->notification_token;
							// var_dump($notification_token);die;
							helper('onesignal');
							$send_notif_app_2 = sendNotification([$notification_token], $title, $content, $notification_data);
							$response->data['send_notif_app_2'] = $send_notif_app_2;
						}
					} catch (\Exception $e) {
						$response->message .= " But, unable to send notification: " . $e->getMessage();
					}
					writeLog("api-notification_app", "survey\n" . json_encode($response));
				}
			}
		}
		return $response;
	}

	public function get_price()
	{
		$response = initResponse();

		$price_id = $this->request->getPost('price_id') ?? '';
		$select_price = 'price_id,promo_id,brand,model,storage,type,price_s,price_a,price_b,price_c,price_d,price_e,price_fullset';
		$where = ['price_id' => $price_id, 'deleted_at' => null];
		$MasterPrice = new MasterPrices();
		$master_price = $MasterPrice->getPrice($where, $select_price);
		if (!empty($master_price)) {
			$data = [
				'S'		=> ['unit_only' => (int)$master_price->price_s, 'fullset' => intval($master_price->price_s) + intval($master_price->price_fullset)],
				'A'		=> ['unit_only' => (int)$master_price->price_a, 'fullset' => intval($master_price->price_a) + intval($master_price->price_fullset)],
				'B'		=> ['unit_only' => (int)$master_price->price_b, 'fullset' => intval($master_price->price_b) + intval($master_price->price_fullset)],
				'C'		=> ['unit_only' => (int)$master_price->price_c, 'fullset' => intval($master_price->price_c) + intval($master_price->price_fullset)],
				'D'		=> ['unit_only' => (int)$master_price->price_d, 'fullset' => intval($master_price->price_d) + intval($master_price->price_fullset)],
				'E'		=> ['unit_only' => (int)$master_price->price_e, 'fullset' => intval($master_price->price_e) + intval($master_price->price_fullset)],
			];
			$response->data		= $data;
			$response->success	= true;
			$response->message	= 'OK';
		} else {
			$response->message	= "Not Available";
		}

		return $this->respond($response);
	}

	function change_grade()
	{
		$response = initResponse('Unauthorized.');

		$check_id = $this->request->getPost('check_id') ?? '';
		$grade = $this->request->getPost('grade') ?? '';
		if (empty($check_id) || empty($grade)) {
			$response->message = "check_id and grade is required!";
		} else {
			if (hasAccess($this->role, 'r_change_grade')) {
				$survey_by = session()->admin_id;
				$survey_name = session()->username;

				// change_grade logic start
				$select = 'dc.check_id,check_code,dc.user_id,dc.price_id,check_detail_id,survey_fullset,user_id,brand,storage,type,fcm_token,price,grade,fullset_price,survey_fullset,survey_date,survey_name,survey_id,survey_log';
				$where = array('dc.check_id' => $check_id, 'dc.status_internal' => 8, 'dc.deleted_at' => null);
				$device_check = $this->DeviceCheck->getDeviceDetail($where, $select);
				if (!$device_check) {
					$response->message = "Invalid check_id $check_id";
				} else {
					$response->data['web'] = [
						'check_code' => $device_check->check_code,
						'grade' => $grade,
						'price' => '0',
						'survey_name' => $survey_name,
					];
					$price = 0;
					$fullset_price = 0;
					$survey_fullset = 0;
					$where_price = ['price_id' => $device_check->price_id, 'deleted_at' => null];
					$select_price = 'price_s,price_a,price_b,price_c,price_d,price_e,price_fullset';
					$MasterPrice = new MasterPrices();
					$master_price = $MasterPrice->getPrice($where_price, $select_price);
					if (!$master_price) {
						$response->message = "Invalid price ($device_check->price_id)!";
						$response->success = false;
					} else {
						// define $price
						switch ($grade) {
							case 'S': $price = $master_price->price_s; break;
							case 'A': $price = $master_price->price_a; break;
							case 'B': $price = $master_price->price_b; break;
							case 'C': $price = $master_price->price_c; break;
							case 'D': $price = $master_price->price_d; break;
							case 'E': $price = $master_price->price_e; break;
							case 'SF': {
								$price = $master_price->price_s;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'S';
								break;
							}
							case 'AF': {
								$price = $master_price->price_a;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'A';
								break;
							}
							case 'BF': {
								$price = $master_price->price_b;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'B';
								break;
							}
							case 'CF': {
								$price = $master_price->price_c;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'C';
								break;
							}
							case 'DF': {
								$price = $master_price->price_d;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'D';
								break;
							}
							case 'EF': {
								$price = $master_price->price_e;
								$fullset_price = $master_price->price_fullset;
								$price += $fullset_price;
								$survey_fullset = 1;
								$grade = 'E';
								break;
							}
						}

						if ($price > 0) {
							// update data
							$now = date('Y-m-d H:i:s');
							$data_update_change = [
								'check_id'				=> $device_check->check_id,
								'check_code'			=> $device_check->check_code,
								'price_id'				=> $device_check->price_id,
								'old_price'				=> $device_check->price,
								'old_grade'				=> $device_check->grade,
								'old_fullset_price' 	=> $device_check->fullset_price,
								'old_survey_fullset'	=> $device_check->survey_fullset,
								'old_survey_id'			=> $device_check->survey_id,
								'old_survey_name'		=> $device_check->survey_name,
								'old_survey_log'		=> $device_check->survey_log,
								'old_survey_date'		=> $device_check->survey_date,
								'new_price'				=> $price,
								'new_grade'				=> $grade,
								'new_fullset_price' 	=> $fullset_price,
								'new_survey_fullset'	=> $survey_fullset,
								'new_survey_id'			=> $survey_by,
								'new_survey_name'		=> $survey_name,
								'new_survey_log'		=> 'change grade',
								'new_survey_date'		=> $now,
								'created_at'			=> $now,
								'created_by'			=> $survey_name,
							];
							// var_dump($data_update_change);die;
							$data_update = [
								'price'			=> $price,
								'grade'			=> $grade,
								'updated_at'	=> $now,
							];
							$data_update_detail = [
								'fullset_price' 	=> $fullset_price,
								'survey_fullset'	=> $survey_fullset,
								'survey_id'			=> $survey_by,
								'survey_name'		=> $survey_name,
								'survey_log'		=> 'change grade',
								'survey_date'		=> $now,
								'updated_at'		=> $now,
							];

							$this->db = \Config\Database::connect();
							$this->db->transStart();
							$GradeChange = new GradeChanges();
							$GradeChange->insert($data_update_change);
							$this->DeviceCheck->update($check_id, $data_update);
							$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_update_detail);
							$this->db->transComplete();

							if ($this->db->transStatus() === FALSE) {
								$response->message = "Failed. " . json_encode($this->db->error());
							} else {
								helper('number');
								$response->success = true;
								$response->message = "Success change grade from <b>$device_check->grade to $grade</b> grade. ";
								$response->message .= "(" . number_to_currency($price, "IDR") . ")";
								$response->data['web']['price'] = $price;
	
								$data = [];
								$data['device_check'] = $device_check; // for logs
								$data['grade_changes'] = $data_update_change; // for logs
								$data['device_check_update'] = $data_update; // for logs
								$data['device_check_detail_update'] = $data_update_detail; // for logs
								$log_cat = 31;
								$this->log->in("$device_check->check_code\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $check_id);

								$nodejs = new Nodejs();
								$nodejs->emit('notification', [
									'type' => 1,
									'message' => session()->username . " gives changes to $device_check->check_code grade from $device_check->grade to $grade" . ($price == 0 ? "" : " (" . number_to_currency($price, "IDR") . ")"),
								]);

								// send notification
								try {
									$title = "Congatulation, Your $device_check->type price is ready!";
									$content = "Your phone price is updated to " . number_to_currency($price, "IDR");
									$notification_data = [
										'check_id'	=> $check_id,
										'type'		=> 'final_result'
									];

									// for app_1
									$fcm = new FirebaseCoudMessaging();
									$send_notif_app_1 = $fcm->send($device_check->fcm_token, $title, $content, $notification_data);
									$response->data['send_notif_app_1'] = $send_notif_app_1;

									// for app_2
									// get notification_token from $device_check->user_id
									$UserModel = new Users();
									$user = $UserModel->getUser(['user_id' => $device_check->user_id], 'name,notification_token');
									if ($user) {
										$notification_token = $user->notification_token;
										// var_dump($notification_token);die;
										helper('onesignal');
										$send_notif_app_2 = sendNotification([$notification_token], $title, $content, $notification_data);
										$response->data['send_notif_app_2'] = $send_notif_app_2;
									}
								} catch (\Exception $e) {
									$response->message .= " But, unable to send notification: " . $e->getMessage();
								}
								writeLog("api-notification_app", "change_grade\n" . json_encode($response));
							}
						} else {
							$response->message = "Can not make changes!";
						}
					}
				}
				// change_grade logic end
			}
		}
		return $this->respond($response);
	}
}
