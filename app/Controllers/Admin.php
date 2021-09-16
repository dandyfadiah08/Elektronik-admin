<?php

namespace App\Controllers;

class Admin extends BaseController
{
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		helper('validation');
	}

	public function index()
	{
		
		$check_role = checkRole($this->role, 'r_admin');
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			helper('html');
			helper('general_status');
			// make filter status option 
			$status = getAdminStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}

			// make role option 
			$roles = $this->AdminRole->getAllRole('role_id,role_name'); // all
			$optionRole = '<option></option>';
			foreach ($roles as $val) {
				$optionRole .= '<option value="' . $val->role_id . '">' . $val->role_name . '</option>';
			}
	
			$this->data += [
				'page' => (object)[
					'key' => '2-admin',
					'title' => 'Admin',
					'subtitle' => 'Master',
					'navbar' => 'Admin',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionRole' => $optionRole,
			];
	
			return view('admin/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_admin');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'admins';
			$this->builder = $this->db
				->table("$this->table_name as t")
				->join("admin_roles as t1", "t1.role_id=t.role_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"name",
				"username",
				"email",
				"role_name",
				"t.status",
				null,
				"t.updated_at",
			);
			// fields to search with
			$fields_search = array(
				"name",
				"username",
				"email",
				"role_name",
				"t.updated_at",
				"t.updated_by",
			);
			// select fields
			$select_fields = 'admin_id,username,name,email,t.role_id,role_name,token_notification,t.status,t.updated_at,t.updated_by';

			// building where query
			$status = $req->getVar('status') ?? '';
			$notification = $req->getVar('notification') ?? '';
			$where = ['t.deleted_at' => null];
			if ($status != 'all' && !empty($status)) $where += ['t.status' => $status];
			if ($notification == 1) $where += ['token_notification is not' => null];
			elseif ($notification == 2) $where += ['token_notification' => null];

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
						'id' => $row->admin_id, 
					]);
					$attribute_data['delete'] =  htmlSetData([
						'id' => $row->admin_id, 
						'username' => $row->username,
						'role_name' => $row->role_name,
						'status' => $row->status,
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'title'	=> "Edit admin $row->name",
						'data'	=> $attribute_data['edit'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete".(session()->admin_id == $row->admin_id ? ' d-none' : ''),
						'title'	=> "Delete admin $row->name",
						'data'	=> $attribute_data['delete'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$status = getAdminStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-".($row->status == 'active' ? 'success' : 'default')."\">$status</button>";
					$action .= htmlButton($btn['edit']);
					$action .= htmlButton($btn['delete']);

					$notification_status = empty($row->token_notification) 
					? '<i class="fas fa-bell-slash text-danger" title="Web Notification Inactive"></i>' 
					: '<i class="fas fa-bell text-success" title="Web Notification Active"></i>';

					$r = [];
					$r[] = $i;
					$r[] = $row->name;
					$r[] = $row->username;
					$r[] = $row->email;
					$r[] = $row->role_name.$notification_status;
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
			$check_role = checkRole($this->role, 'r_admin');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$rules = getValidationRules('admin:save');
				if (!$this->validate($rules)) {
					$errors = $this->validator->getErrors();
					$response->message = "";
					foreach ($errors as $error) $response->message .= "$error ";
					$response->data = $errors;
				} else {
					
					$id = $this->request->getPost('id') ?? 0;
					$username = $this->request->getPost('username') ?? '';
					$password = $this->request->getPost('password') ?? '';
					$email = $this->request->getPost('email') ?? '';
					$name = $this->request->getPost('name') ?? '';
					$role_id = $this->request->getPost('role_id') ?? '';
					$status = $this->request->getPost('status') ?? 'active';
					$role = $this->AdminRole->getAdminRole(['role_id' => $role_id], 'role_name');
					if(!$role) {
						$response->message = "Role not found ($role_id)";
					} else {
						$encrypter = \Config\Services::encrypter();
						$password = bin2hex($encrypter->encrypt(base64_decode($password)));
						$data = [
							'username' 		=> $username,
							'password'	 	=> $password,
							'email' 		=> $email,
							'name'	 		=> $name,
							'role_id' 		=> $role_id,
							'status' 		=> $status,
							'updated_at'	=> date('Y-m-d H:i:s'),
							'updated_by' 	=> session()->get('username'),
						];
	
						$this->db->transStart();
						if ((int)$id > 0) {
							$admin = $this->Admin->getAdminAndRole(['admin_id' => $id], 'admin_id,username,email,name,a.status,a.updated_at,a.updated_by,role_name');
							$response->message = "Admin $name ($username) updated.";
							$this->Admin->update((int)$id, $data);
							unset($data['password']);
							$data['role_name'] = $role->role_name;
							$data = ['new' => $data]; // for logs
							if($admin) $data['old'] = $admin; // for logs
							$log_cat = 12;
						} else {
							$data += [
								'created_at' => date('Y-m-d H:i:s'),
								'created_by' => session()->get('username'),
								'updated_at' => date('Y-m-d H:i:s'),
								'updated_by' => session()->get('username'),
							];
							$response->message = "Admin $name ($username) created.";
							$this->Admin->insert($data);
							unset($data['password']);
							$log_cat = 11;
						}
						$this->db->transComplete();
	
						if ($this->db->transStatus() === FALSE) {
							$response->message = "Failed. " . json_encode($this->db->error());
						} else {
							$response->success = true;
							$this->log->in(session()->username, $log_cat, json_encode($data));
						}
					}

				}
			}
		}

		return $this->respond($response, 200);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_admin');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$admin = $this->Admin->getAdminAndRole(['admin_id' => $id], 'username,name,email,a.status,role_name');
				if(!$admin) {
					$response->message = "Admin not found ($id)";
				} elseif($id == session()->admin_id) {
					$response->message = "You can not delete yourself.";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->get('username'),
					];
					$this->db->transStart();
					$this->Admin->update($id, $data);
					$data += (array)$admin; // for logs
					$this->db->transComplete();
					if ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$response->message = "Admin $admin->name ($admin->username) deleted.";
						$log_cat = 13;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}
		return $this->respond($response, 200);
	}

	public function details()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_admin');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$admin = $this->Admin->getAdmin(['admin_id' => $id], 'username,password,name,email,status,role_id');
				if(!$admin) {
					$response->message = "Admin not found ($id)";
				} else {
					$response->success = true;
					$response->message = "Success.";
					$encrypter = \Config\Services::encrypter();
					$admin->password = base64_encode($encrypter->decrypt(hex2bin($admin->password)));
					$response->data = $admin;
				}
			}
		}
		return $this->respond($response, 200);
	}

}
