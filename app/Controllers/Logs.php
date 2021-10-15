<?php

namespace App\Controllers;

use App\Models\AdminsModel;
use App\Models\DeviceChecks;
use App\Models\Logs as L;
use App\Models\Users;

class Logs extends BaseController
{
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		helper('validation');
		helper('log_category');
	}

	public function index()
	{
		$check_role = checkRole($this->role, 'r_logs');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');

			$this->data += [
				'page' => (object)[
					'key' => '2-logs',
					'title' => 'Logs',
					'subtitle' => 'Others',
					'navbar' => 'Logs',
				],
				'optionYear' => $this->getOptionYear(),
				'optionCategory' => $this->getOptionCategory(),
				'log_admin_id' => false,
				'log_user_id' => false,
				'log_check_id' => false,
			];

			return view('logs/index', $this->data);
		}
	}

	public function admin($admin_id = false)
	{
		if (!$admin_id || !hasAccess($this->role, 'r_logs') || !hasAccess($this->role, 'r_admin')) {
			return view('layouts/unauthorized', $this->data);
		} else {
			// get admin info
			$this->Admin = new AdminsModel();
			$admin = $this->Admin->getAdmin(['admin_id' => $admin_id], 'username,name,email');
			if(!$admin) {
				$this->data['url'] = base_url('logs/admin/'.$admin_id);
				return view('layouts/not_found', $this->data);
			} else {
				helper('html');

				$this->data += [
					'page' => (object)[
						'key' => '2-logs',
						'title' => 'Logs',
						'subtitle' => "Admin: $admin->name ($admin->username)",
						'navbar' => 'Logs / Admin',
					],
					'optionYear' => $this->getOptionYear(),
					'optionCategory' => $this->getOptionCategory(),
					'log_admin_id' => $admin_id,
					'log_user_id' => false,
					'log_check_id' => false,
				];
			}

			return view('logs/index', $this->data);
		}
	}

	public function user($user_id = false)
	{
		if (!$user_id || !hasAccess($this->role, 'r_logs') || !hasAccess($this->role, 'r_user')) {
			return view('layouts/unauthorized', $this->data);
		} else {
			// get admin info
			$this->User = new Users();
			$user = $this->User->getUser(['user_id' => $user_id], 'phone_no,name,email');
			if(!$user) {
				$this->data['url'] = base_url('logs/user/'.$user_id);
				return view('layouts/not_found', $this->data);
			} else {
				helper('html');

				$this->data += [
					'page' => (object)[
						'key' => '2-logs',
						'title' => 'Logs',
						'subtitle' => "User: $user->name ($user->phone_no)",
						'navbar' => 'Logs / User',
					],
					'optionYear' => $this->getOptionYear(),
					'optionCategory' => $this->getOptionCategory(),
					'log_admin_id' => false,
					'log_user_id' => $user_id,
					'log_check_id' => false,
				];
			}

			return view('logs/index', $this->data);
		}
	}

	public function device_check($check_id = false)
	{
		if (!$check_id || !hasAccess($this->role, 'r_logs') || !hasAccess($this->role, 'r_user')) {
			return view('layouts/unauthorized', $this->data);
		} else {
			// get device check info
			$this->DeviceCheck = new DeviceChecks();
			$device_check = $this->DeviceCheck->getDevice(['check_id' => $check_id], "check_code,CONCAT(brand,' ',type) as device");
			if(!$device_check) {
				$this->data['url'] = base_url('logs/device_check/'.$check_id);
				return view('layouts/not_found', $this->data);
			} else {
				helper('html');

				$this->data += [
					'page' => (object)[
						'key' => '2-logs',
						'title' => 'Logs',
						'subtitle' => "Device Check: $device_check->check_code ($device_check->device)",
						'navbar' => 'Logs / Device Check',
					],
					'optionYear' => $this->getOptionYear(),
					'optionCategory' => $this->getOptionCategory(),
					'log_admin_id' => false,
					'log_user_id' => false,
					'log_check_id' => $check_id,
				];
			}

			return view('logs/index', $this->data);
		}
	}

	private function getOptionYear() {
		$optionYear = '';
		$year = date('Y');
		for ($i=2020; $i<=$year; $i++) {
			$selected = $year == $i ? 'selected' : '';
			$optionYear .= '<option value="' . $i . '" '.$selected.'>' . $i . '</option>';
		}
		return $optionYear;
	} 

	private function getOptionCategory() {
		// make filter status option 
		$categories = getLogCategory(-1); // all
		$optionCategory = '<option></option><option value="all">All</option>';
		foreach ($categories as $key => $val) {
			$optionCategory .= '<option value="' . $key . '">' . $val . '</option>';
		}
		return $optionCategory;
	} 
	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_logs');
		$year = $this->request->getVar('year') ?? date('Y');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'logs_'.$year;
			$this->builder = $this->db
				->table("$this->table_name as t");
				// ->join("device_check_details as t1", "t1.check_id=t.check_id", "left")

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"created_at",
				"user",
				"category",
			);
			// fields to search with
			$fields_search = array(
				"user",
				"log",
			);
			// select fields
			$select_fields = 'id,user,category,log,created_at';

			// building where query
			$date = $req->getVar('date') ?? '';
			$category = $req->getVar('category') ?? '';
			$admin_id = $req->getVar('admin_id') ?? false;
			$user_id = $req->getVar('user_id') ?? false;
			$check_id = $req->getVar('check_id') ?? false;
			if (!empty($date)) {
				$dates = explode(' - ', $date);
				if(count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			if(!empty($category) && $category != 'all') $this->builder->where(['category' => $category]);
			if($admin_id) $this->builder->where(['admin_id' => $admin_id]);
			elseif($user_id) $this->builder->where(['user_id' => $user_id]);
			elseif($check_id) $this->builder->where(['check_id' => $check_id]);

			// add select and where query to builder
			$this->builder
				->select($select_fields);

			// bulding order query
			$order = $req->getVar('order');
			$length = $req->getVar('length') ?? 10;
			$start = $req->getVar('start') ?? 0;
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

			$data = [];
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$category = getLogCategory($row->category);
					$attribute_data['default'] =  htmlSetData([
						'id' => $row->id,
						'created_at' => $row->created_at,
						'user' => $row->user,
						'category' => $category,
					]);
					$btn['view'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnDetails",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-info-circle',
						'text'	=> 'Details',
					];
					$action = htmlButton($btn['view'], false);

					$r = [];
					$r[] = $i;
					$r[] = $row->created_at;
					$r[] = $row->user;
					$r[] = $category;
					$r[] = substr($row->log, 0, 240);
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

	public function details()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_logs');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$L = new L();
				$log = $L->getLogs(['id' => $id], 'user,category,log,created_at');
				if(!$log) {
					$response->message = "Invalid log ($id)";
				} else {
					$response->success = true;
					$response->message = "Success.";
					$log->log = json_decode($log->log);
					$response->data = $log->log;
				}
			}
		}
		return $this->respond($response, 200);
	}

}
