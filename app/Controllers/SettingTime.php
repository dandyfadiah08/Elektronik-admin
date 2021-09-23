<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Log;
use App\Models\AvailableDateTime;

class SettingTime extends BaseController
{
	protected $AvailableDateTime;
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->AvailableDateTime = new AvailableDateTime();
		$this->log = new Log();

		helper('validation');
	}

	public function index()
	{

		$check_role = checkRole($this->role, 'r_withdraw'); // TO DO Role belum ada

		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			helper('html');
			helper('format_helper');
			// make filter status option 
			$days = getNameDays(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			$optionDays = '<option></option><option value="all">All</option>';

			$optionStatus .= '<option value="active">Active</option>';
			$optionStatus .= '<option value="inactive">Inactive</option>';

			foreach ($days as $key => $val) {
				$optionDays .= '<option value="' . $key . '" ' . ($key == 2 ? 'selected' : '') . '>' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-setting_available_date_time',
					'title' => 'Setting',
					'subtitle' => 'Available Date & Time',
					'navbar' => 'Available Date & Time',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionDays' => $optionDays,
			];

			return view('setting_time/index', $this->data);
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
			$this->table_name = 'available_date_time';
			$this->builder = $this->db
				->table("$this->table_name as adt");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"adt.type",
				"adt.status",
			);
			// fields to search with
			$fields_search = array(
				"adt.type",
				"adt.status",
				"adt.days",
				"adt.value",
				"adt.updated_at",
				"adt.updated_by",
			);
			// select fields
			$select_fields = 'adt.*';

			// building where query
			$status = isset($_REQUEST['status']) ? $req->getVar('status') : '';
			$days = isset($_REQUEST['days']) ? $req->getVar('days') : '';
			// var_dump($status);die;
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' - ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(adt.updated_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(adt.updated_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = array();
			if ($status != 'all' && $status != '' && $status > 0) $where += array('adt.status' => $status);
			if ($days != 'all' && $days != '' && $days > 0) $where += array('adt.days' => $days);
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
			// var_dump($this->db->getLastQuery());die;

			$data = array();
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				helper('format_helper');

				// looping through data result
				foreach ($dataResult as $row) {
					// var_dump($row->status);

					$updated_at = $row->updated_at ? $row->updated_at : "-";
					$updated_by = $row->updated_by ? $row->updated_by : "-";

					$btnSwitch = [
						'id' => 'status-' . $row->id,
						'label' => 'Status',
						'class' => 'saveInputCheck',
						'on' => 'ACTIVE',
						'off' => 'INACTIVE',
					];
					if($row->status == 'active'){
						$btnSwitch += [
							'checked' => '',
						];
					}

					$action = htmlSwitch($btnSwitch);

					// if (response.data.status == 'active') $('#status').bootstrapSwitch('state', true)
					// else $('#status').bootstrapSwitch('state', false)
					// var_dump($row->status);die;

					$r = [];
					$r[] = ++$i;
					$r[] = $row->id;
					$r[] = $row->type;
					$r[] = $row->status;
					$r[] = getNameDays($row->days);
					$r[] = $row->value;
					$r[] = $updated_at . "<br> " . $updated_by;
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

	function save_time()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$id_time = $this->request->getPost('id_time');
			$active_time = $this->request->getPost('active_time');
			$rules = getValidationRules('setting_time');

			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$check_role = checkRole($this->role, 'r_mark_as_failed'); // TO DO check role
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$this->db->transStart();
					$hasil_update = $this->AvailableDateTime->update($id_time, ['status' => $active_time]);
					// var_dump($this->AvailableDateTime->getLastQuery());die;
					$dataTime = $this->AvailableDateTime->getAvailableDateTime(['id' => $id_time], false, '*');
					$this->db->transComplete();

					if ($this->db->transStatus() === FALSE) {
						// transaction has problems
						$response->message = "Failed to perform task! #uts01c";
					} elseif ($hasil_update == 1) {
						$response->success = true;
						$response->message = "Successfully update";
						$log_cat = 25;
						$this->log->in(session()->username, $log_cat, json_encode($dataTime));
					} else {
						$response->message = "Failed to perform task! #uts02c";
					}
				}
			}
		}

		return $this->respond($response, 200);
	}
}
