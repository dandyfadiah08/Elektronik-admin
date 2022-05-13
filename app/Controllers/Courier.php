<?php

namespace App\Controllers;

use App\Models\MasterCouriers;

class Courier extends BaseController
{
	protected $expedition, $MasterCourier;

	public function __construct()
	{
		$this->MasterCourier = new MasterCouriers();
		$this->expedition = [
			'Happy Express' => 'Happy Express',
			'Agen' => 'Agen'
		];
		$this->db = \Config\Database::connect();
		helper('validation');
	}

	public function index()
	{
		$check_role = checkRole($this->role, ['r_courier', 'r_courier_view']);
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('general_status');
			helper('html');

			// make filter status option 
			$status = getCourierStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}
			$optionExpedition = '<option></option>';
			foreach ($this->expedition as $key => $val) {
				$optionExpedition .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-courier',
					'title' => 'Courier',
					'subtitle' => 'Master',
					'navbar' => 'Courier',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionExpedition' => $optionExpedition,
			];

			return view('courier/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, ['r_courier', 'r_courier_view']);
		$check_role->success = true; // sementara belum ada role
		if (!$check_role->success) {
			$json_data = [
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			];
		} else {
			$this->table_name = 'couriers';
			$this->builder = $this->db
				->table("$this->table_name");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"courier_id",
				"courier_name",
				"courier_phone",
				"courier_expedition",
				"updated_at",
				"status",
			);
			// fields to search with
			$fields_search = array(
				"courier_id",
				"courier_name",
				"courier_phone",
				"courier_expedition",
			);
			// select fields
			$select_fields = 'courier_id,courier_name,courier_phone,courier_expedition,created_by,created_at,updated_by,updated_at,status';

			// building where query
			$status = $req->getVar('status') ?? '';
			$expedition = $req->getVar('expedition') ?? '';
			$where = ['deleted_at' => null];
			if ($status != 'all' && $status > 0) $where += ['status' => $status];
			if ($expedition != 'all') $where += ['courier_expedition' => $expedition];

			// add select and where query to builder
			$this->builder
				->select($select_fields)
				->where($where);

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
				$access['edit'] = hasAccess($this->role, 'r_courier');
				helper('html');
				helper('general_status');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$courier_name = htmlentities($row->courier_name);
					$courier_phone = htmlentities($row->courier_phone);

					// $attribute_data['default'] = 'data-check_code="'.$row->check_code.'" data-check_id="'.$row->check_id.'" ';
					$attribute_data['default'] =  htmlSetData([
						'id' => $row->courier_id,
						'courier_name' => $courier_name,
						'courier_phone' => $courier_phone,
						'courier_expedition' => $row->courier_expedition,
						'status' => $row->status,
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'title'	=> "Edit courier detail",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete",
						'title'	=> "Delete courier",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$status = getCourierStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-" . ($row->status == 'active' ? 'success' : 'default') . "\">$status</button>";
					if ($access['edit']) {
						$action .= htmlButton($btn['edit']);
						$action .= htmlButton($btn['delete']);
					}

					$r = [];
					$r[] = $i;
					$r[] = $row->courier_id;
					$r[] = $courier_name;
					$r[] = $courier_phone;
					$r[] = $row->courier_expedition;
					$r[] = "$row->updated_at<br>$row->updated_by";
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

	public function save()
	{
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_courier')) {
			$id = $this->request->getPost('id') ?? false;
			$courier_name = $this->request->getPost('courier_name') ?? '';
			$courier_phone = $this->request->getPost('courier_phone') ?? '';
			$courier_expedition = $this->request->getPost('courier_expedition') ?? '';
			$status = $this->request->getPost('status') ?? 'active';

			helper('format');
			$data = [
				'courier_name' 	=> htmlentities($courier_name),
				'courier_phone' 	=> htmlentities($courier_phone),
				'courier_expedition' 	=> $courier_expedition,
				'status' 		=> $status,
				'updated_at' 	=> date('Y-m-d H:i:s'),
				'updated_by' 	=> session()->get('username'),
			];

			$this->db->transStart();
			if ($id) {
				$courier = $this->MasterCourier->getCourier(['courier_id' => $id], 'courier_id,courier_name,courier_phone,courier_expedition,status');
				$response->message = "Courier updated.";
				$this->MasterCourier->update($id, $data);
				$data = ['new' => $data]; // for logs
				if ($courier) $data['old'] = $courier; // for logs
				$log_cat = 71;
			} else {
				$data += [
					'courier_id' => strtoupper(uniqid('C')),
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => session()->get('username'),
					'updated_at' => date('Y-m-d H:i:s'),
					'updated_by' => session()->get('username'),
				];
				$response->message = "Courier added.";
				$this->MasterCourier->insert($data);
				$log_cat = 69;
			}
			$this->db->transComplete();

			if ($this->db->transStatus() === FALSE) {
				$response->message = "Failed. " . json_encode($this->db->error());
			} else {
				$response->success = true;
				$this->log->in(session()->username, $log_cat, json_encode($data));
			}
		}

		return $this->respond($response);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_courier')) {
			$id = $this->request->getPost('id') ?? false;
			$courier = $this->MasterCourier->getCourier(['courier_id' => $id], 'courier_id,courier_name,courier_phone,courier_expedition,status');
			if (!$courier) {
				$response->message = "Courier not valid ($id)";
			} else {
				$data = [
					'deleted_at'	=> date('Y-m-d H:i:s'),
					'deleted_by'	=> session()->get('username'),
				];
				$this->db->transStart();
				$this->MasterCourier->update($id, $data);
				$data += (array)$courier; // for logs
				$this->db->transComplete();
				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
					$response->message = "Courier deleted.";
					$log_cat = 70;
					$this->log->in(session()->username, $log_cat, json_encode($data));
				}
			}
		}
		return $this->respond($response);
	}
}
