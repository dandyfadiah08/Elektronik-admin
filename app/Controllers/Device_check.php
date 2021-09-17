<?php

namespace App\Controllers;

use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
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
			return view('layouts/unauthorized', ['role' => $this->role]);
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
			return view('layouts/unauthorized', ['role' => $this->role]);
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
				'admin' => $this->admin,
				'role' => $this->role,
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
			return view('layouts/unauthorized', ['role' => $this->role]);
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
			$select = 'check_code,dc.status as dc_status,status_internal,imei,brand,model,storage,dc.type,price,grade,type_user,dc.created_at as check_date
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
			helper('number');
			helper('format');
			// var_dump($device_check);die;
			$this->data += ['dc' => $device_check];
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
		$check_role = checkRole($this->role, 'r_device_check');
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
			// die($this->db->getLastQuery());

			$data = array();
			if (count($dataResult) > 0) {
				$i = $start;
				helper('number');
				helper('html');
				helper('format');
				$url = base_url() . '/device_check/detail/';
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
					$action = '<button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Step ' . $row->status . '">' . $status . '</button>';
					if ($is_reviewed) {
						$price = number_to_currency($row->price, "IDR");
						$action = '<button class="btn btn-xs mb-2 btn-' . $status_color . '" title="Status Internal ' . $row->status_internal . '">' . getDeviceCheckStatusInternal($row->status_internal) . '</button>';
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
					$action .= htmlAnchor($btn['view']);

					$r = array();
					$r[] = $i;
					$r[] = formatDate($row->created_at);
					$r[] = $row->check_code;
					$r[] = $row->imei;
					$r[] = "$row->brand $row->model $row->storage $row->type";
					$r[] = "$row->grade<br>$price";
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
		return $this->respond($json_data);
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

		$select = 'dc.status,check_code,dc.price_id,check_detail_id,survey_fullset,user_id,brand,storage,type,fcm_token';
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

				$nodejs = new Nodejs();
				$nodejs->emit('notification', [
					'type' => 1,
					'message' => session()->username . " gives $device_check->check_code grade $grade" . ($price == 0 ? "" : " (" . number_to_currency($price, "IDR") . ")"),
				]);

				// send notification
				if ($send_notification && false) {
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
						$user_model = new Users();
						$user = $user_model->getUser($device_check->user_id);
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
}
