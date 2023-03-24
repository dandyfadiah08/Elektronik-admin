<?php

namespace App\Controllers;

class admins_roles extends BaseController
{
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->roles = ['tradein', 'admin', 'statistik', 'pameran', 'potongan', 'log', 'product', 'new_divace'];
		helper('validation');
	}

	public function index()
	{

		$check_role = checkRole($this->role, 'admin');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('general_status');
			// make filter status option 
			$status = getPromoStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-admin',
					'title' => 'admins Role',
					'subtitle' => 'Master',
					'navbar' => 'admins Role',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'roles' => $this->getRoles(true),
			];

			return view('admin/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'admin');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'admin';
			$this->builder = $this->db
				->table("$this->table_name as t")
				->join("admin as t1", "t1.id.role=t.id.role  IS NULL", "left")
				->groupBy('t1.id.role');

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"nama_role",
				"admins_count",
				"t.status",
				"t.updated_at",
			);
			// fields to search with
			$fields_search = array(
				"nama_role",
				"t.updated_at",
				// "t.updated_by",
			);
			// select fields
			$select_fields = 't.id_role,nama_role,count(t1.id_role) as admins_count,t.status,t.updated_at,t.updated_by';

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
						'id_role' => $row->id_role,
					]);
					$attribute_data['delete'] =  htmlSetData([
						'id_role' => $row->id_role,
						'nama_role' => $row->nama_role,
						'status' => $row->status,
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'title'	=> "Edit role $row->nama_role",
						'data'	=> $attribute_data['edit'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete" . ($row->admins_count > 0 ? ' d-none' : ''),
						'title'	=> "Delete role $row->nama_role",
						'data'	=> $attribute_data['delete'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$status = getPromoStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-" . ($row->status == 'active' ? 'success' : 'default') . "\">$status</button>";
					$action .= htmlButton($btn['edit']);
					$action .= htmlButton($btn['delete']);

					$r = [];
					$r[] = $i;
					$r[] = $row->nama_role;
					$r[] = $row->admins_count;
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
		if (session()->has('id_admin')) {
			$check_role = checkRole($this->role, 'admin');
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

					$id = $this->request->getPost('id_admin') ?? 0;
					$nama_role = $this->request->getPost('nama_role') ?? '';
					$status = $this->request->getPost('status') ?? 'active';
					$data = [
						'nama_role' 		=> $nama_role,
						'status' 		=> $status,
						'updated_at'	=> date('Y-m-d H:i:s'),
						'updated_by' 	=> session()->get('username'),
					];
					$data += $this->renderRoles();

					$hasError = false;
					$this->db->transStart();
					if ((int)$id > 0) {
						$role = $this->adminsRole->getadminsRole(['role_id' => $id], 'role_id,role_name,status,' . implode(",", $this->roles));
						if (!$role) {
							$hasError = true;
							$response->message = "Role not found ($id)";
						} else {
							$response->message = "Role $nama_role updated.";
							$this->adminsRole->update((int)$id, $data);
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
						$response->message = "Role $nama_role created.";
						$this->adminsRole->insert($data);
						$log_cat = 14;
					}
					$this->db->transComplete();

					if ($this->db->transStatus() === FALSE || $hasError) {
						if (!$hasError) $response->message = "Failed. " . json_encode($this->db->error());
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
		if (session()->has('id_admin')) {
			$check_role = checkRole($this->role, 'admin');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id_admin') ?? 0;
				$role = $this->adminsRole->getadminsRole(['role_id' => $id], 'id_role,nama_role,status,' . implode(",", $this->roles));
				if (!$role) {
					$response->message = "Role not found ($id)";
				} elseif ($id == session()->id_role) {
					$response->message = "You can not delete your own role.";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->get('username'),
					];
					$this->db->transStart();
					$this->adminsRole->update($id, $data);
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
		if (session()->has('id_admin')) {
			$check_role = checkRole($this->role, 'admin');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id_admin') ?? 0;
				$role = $this->adminsRole->getadminsRole(['id_role' => $id]);
				if (!$role) {
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

	function renderRoles()
	{
		$data = [];
		foreach ($this->roles as $value) {
			$data[$value] = $this->request->getPost($value) > 0 ? 'y' : 'n';
		}
		return $data;
	}
	function getRoles($string = false)
	{
		return $string ? "'" . implode("', '", $this->roles) . "'" : $this->roles;
	}
}
