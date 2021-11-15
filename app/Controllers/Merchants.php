<?php

namespace App\Controllers;

use App\Models\MerchantModel;

class Merchants extends BaseController
{
	protected $db, $Merchant;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->Merchant = new MerchantModel();
		helper('validation');
	}

	public function index()
	{
		
		$check_role = checkRole($this->role, 'r_merchant');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('format');
			helper('general_status');
			// make filter status option 
			$status = getMerchantStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}
	
			$this->data += [
				'page' => (object)[
					'key' => '2-merchants',
					'title' => 'Merchants',
					'subtitle' => 'Master',
					'navbar' => 'Merchants',
				],
				'search' => $this->request->getGet('s') ? "'" . safe2js($this->request->getGet('s')) . "'" : 'null',
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
			];
	
			return view('merchants/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_merchant');
		if (!$check_role->success) {
			$json_data = [
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			];
		} else {
			$this->table_name = 'merchants';
			$this->builder = $this->db
				->table("$this->table_name as t");

			// fields order 0, 1, 2, ...
			$fields_order = [
				null,
				"merchant_name",
				"merchant_code",
				"status",
			];
			// fields to search with
			$fields_search = [
				"merchant_name",
				"merchant_code",
				"t.updated_at",
				"t.updated_by",
			];
			// select fields
			$select_fields = 'merchant_id,merchant_name,merchant_code,status,updated_at,updated_by';

			// building where query
			$status = $req->getVar('status') ?? '';
			$where = ['t.deleted_at' => null];
			if ($status != 'all' && !empty($status)) $where += ['t.status' => $status];

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
				helper('html');
				helper('general_status');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$attribute_data['edit'] =  htmlSetData([
						'id' => $row->merchant_id, 
						'merchant_name' => $row->merchant_name,
						'merchant_code' => $row->merchant_code,
						'status' => $row->status,
					]);
					$attribute_data['delete'] = $attribute_data['edit'];
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'title'	=> "Edit merchant $row->merchant_name",
						'data'	=> $attribute_data['edit'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete",
						'title'	=> "Delete merchant $row->merchant_name",
						'data'	=> $attribute_data['delete'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$status = getMerchantStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-".($row->status == 'active' ? 'success' : 'default')."\">$status</button>";
					$action .= htmlButton($btn['edit']);
					$action .= htmlButton($btn['delete']);

					$r = [];
					$r[] = $i;
					$r[] = $row->merchant_name;
					$r[] = $row->merchant_code;
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
		if (hasAccess($this->role, 'r_merchant')) {
			$rules = getValidationRules('merchant:save');
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
				$response->data = $errors;
			} else {
				
				$id = $this->request->getPost('id') ?? 0;
				$merchant_name = $this->request->getPost('merchant_name') ?? '';
				$merchant_code = $this->request->getPost('merchant_code') ?? '';
				$status = $this->request->getPost('status') ?? 'active';
				$data = [
					'merchant_name' => $merchant_name,
					'merchant_code'	=> $merchant_code,
					'status' 		=> $status,
					'updated_at'	=> date('Y-m-d H:i:s'),
					'updated_by' 	=> session()->get('username'),
				];

				$this->db->transStart();
				if ((int)$id > 0) {
					$merchant = $this->Merchant->getMerchant(['merchant_id' => $id], 'merchant_id,merchant_name,merchant_code,status,updated_at,updated_by');
					$response->message = "Merchant $merchant_name ($merchant_code) updated.";
					$this->Merchant->update((int)$id, $data);
					$data = ['new' => $data]; // for logs
					if($merchant) $data['old'] = $merchant; // for logs
					$log_cat = 64;
				} else {
					$data += [
						'created_at' => date('Y-m-d H:i:s'),
						'created_by' => session()->get('username'),
						'updated_at' => date('Y-m-d H:i:s'),
						'updated_by' => session()->get('username'),
					];
					$response->message = "Merchant $merchant_name ($merchant_code) created.";
					$this->Merchant->insert($data);
					$log_cat = 62;
				}
				$this->db->transComplete();

				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
					$this->log->in(session()->username, $log_cat, json_encode($data), session()->admin_id);
				}
			}
		}

		return $this->respond($response);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_merchant')) {
			$id = $this->request->getPost('id') ?? 0;
			$merchant = $this->Merchant->getMerchant(['merchant_id' => $id], 'merchant_id,merchant_name,merchant_code,status');
			if(!$merchant) {
				$response->message = "Merchant not found ($id)";
			} else {
				$data = [
					'deleted_at'	=> date('Y-m-d H:i:s'),
					'deleted_by'	=> session()->get('username'),
				];
				$this->db->transStart();
				$this->Merchant->update($id, $data);
				$data += (array)$merchant; // for logs
				$this->db->transComplete();
				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
					$response->message = "Merchant $merchant->merchant_name ($merchant->merchant_code) deleted.";
					$log_cat = 63;
					$this->log->in(session()->username, $log_cat, json_encode($data), session()->admin_id);
				}
			}
		}
		return $this->respond($response);
	}

}
