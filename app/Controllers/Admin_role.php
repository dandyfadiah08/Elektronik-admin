<?php

namespace App\Controllers;

class Admin_role extends BaseController
{
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->roles = ['r_admin', 'r_admin_role', 'r_user', 'r_commission_rate', 'r_2fa', 'r_transaction', 'r_device_check', 'r_review', 'r_promo', 'r_promo_view', 'r_price', 'r_price_view', 'r_logs', 'r_proceed_payment', 'r_mark_as_failed', 'r_manual_transfer', 'r_withdraw', 'r_submission', 'r_view_photo_id', 'r_view_phone_no', 'r_view_email', 'r_view_payment_detail', 'r_view_address', 'r_confirm_appointment', 'r_change_payment', 'r_change_address', 'r_change_setting', 'r_change_available_date_time', 'r_request_payment', 'r_transaction_success', 'r_change_grade', 'r_export_device_check', 'r_export_transaction', 'r_merchant', 'r_export_withdraw', 'r_export_user', 'r_bonus_view', 'r_send_bonus', 'r_export_bonus', 'r_balance', 'r_tax', 'r_export_tax', 'r_courier', 'r_courier_view'];
		helper('validation');
	}

	public function index()
	{
		
		$check_role = checkRole($this->role, 'r_admin_role');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('general_status');
			// make filter status option 
			$status = getAdminRoleStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-admin_role',
					'title' => 'Admin Role',
					'subtitle' => 'Master',
					'navbar' => 'Admin Role',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'roles' => $this->getRoles(true),
			];
	
			return view('admin_role/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_admin_role');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'admin_roles';
			$this->builder = $this->db
				->table("$this->table_name as t")
				->join("admins as t1", "t1.role_id=t.role_id AND t1.deleted_at IS NULL", "left")
				->groupBy('t1.role_id');

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"role_name",
				"admin_count",
				"t.status",
				"t.updated_at",
			);
			// fields to search with
			$fields_search = array(
				"role_name",
				"t.updated_at",
				// "t.updated_by",
			);
			// select fields
			$select_fields = 't.role_id,role_name,count(t1.role_id) as admin_count,t.status,t.updated_at,t.updated_by';

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
						'id' => $row->role_id, 
					]);
					$attribute_data['delete'] =  htmlSetData([
						'id' => $row->role_id, 
						'role_name' => $row->role_name,
						'status' => $row->status,
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'title'	=> "Edit role $row->role_name",
						'data'	=> $attribute_data['edit'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete".($row->admin_count > 0 ? ' d-none' : ''),
						'title'	=> "Delete role $row->role_name",
						'data'	=> $attribute_data['delete'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$status = getAdminRoleStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-".($row->status == 'active' ? 'success' : 'default')."\">$status</button>";
					$action .= htmlButton($btn['edit']);
					$action .= htmlButton($btn['delete']);

					$r = [];
					$r[] = $i;
					$r[] = $row->role_name;
					$r[] = $row->admin_count;
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
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_admin_role');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$rules = getValidationRules('admin_role:save');
				if (!$this->validate($rules)) {
					$errors = $this->validator->getErrors();
					$response->message = "";
					foreach ($errors as $error) $response->message .= "$error ";
					$response->data = $errors;
				} else {
					
					$id = $this->request->getPost('id') ?? 0;
					$role_name = $this->request->getPost('role_name') ?? '';
					$status = $this->request->getPost('status') ?? 'active';
					$data = [
						'role_name' 		=> $role_name,
						'status' 		=> $status,
						'updated_at'	=> date('Y-m-d H:i:s'),
						'updated_by' 	=> session()->get('username'),
					];
					$data += $this->renderRoles();

					$hasError = false;
					$this->db->transStart();
					if ((int)$id > 0) {
						$role = $this->AdminRole->getAdminRole(['role_id' => $id], 'role_id,role_name,status,'.implode(",", $this->roles));
						if(!$role) {
							$hasError = true;
							$response->message = "Role not found ($id)";
						} else {
							$response->message = "Role $role_name updated.";
							$this->AdminRole->update((int)$id, $data);
							$data = ['new' => $data]; // for logs
							$data['old'] = $role; // for logs
							$log_cat = 15;
						}
					} else {
						$data += [
							'created_at' => date('Y-m-d H:i:s'),
							'created_by' => session()->get('username'),
							'updated_at' => date('Y-m-d H:i:s'),
							'updated_by' => session()->get('username'),
						];
						$response->message = "Role $role_name created.";
						$this->AdminRole->insert($data);
						$log_cat = 14;
					}
					$this->db->transComplete();

					if ($this->db->transStatus() === FALSE || $hasError) {
						if(!$hasError) $response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}

		return $this->respond($response);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_admin_role');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$role = $this->AdminRole->getAdminRole(['role_id' => $id], 'role_id,role_name,status,'.implode(",", $this->roles));
				if(!$role) {
					$response->message = "Role not found ($id)";
				} elseif($id == session()->role_id) {
					$response->message = "You can not delete your own role.";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->get('username'),
					];
					$this->db->transStart();
					$this->AdminRole->update($id, $data);
					$data += (array)$role; // for logs
					$this->db->transComplete();
					if ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$response->message = "Role $role->role_name deleted.";
						$log_cat = 16;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}
		return $this->respond($response);
	}

	public function details()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_admin_role');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$role = $this->AdminRole->getAdminRole(['role_id' => $id]);
				if(!$role) {
					$response->message = "Role not found ($id)";
				} else {
					$response->success = true;
					$response->message = "Success.";
					$response->data = $role;
				}
			}
		}
		return $this->respond($response);
	}

	function renderRoles() {
		$data = [];
		foreach ($this->roles as $value) {
			$data[$value] = $this->request->getPost($value) > 0 ? 'y' : 'n';
		}
		return $data;
	}
	function getRoles($string = false) {
		return $string ? "'".implode("', '", $this->roles)."'" : $this->roles;
	}
}
