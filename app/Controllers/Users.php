<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminRolesModel;
use App\Models\AdminsModel;
use App\Models\Users as ModelsUsers;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{
	use ResponseTrait;
	protected $Admin, $AdminRole, $User;

	public function __construct()
	{
		$this->User = new ModelsUsers();
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->db = \Config\Database::connect();
		$this->role = $this->AdminRole->find(session()->role_id);
		$this->table_name = 'users';
		$this->builder = $this->db->table("$this->table_name as t");
	}

	public function index()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		$check_role = checkRole($this->role, 'r_user');
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			helper('html');
			helper('user_status');

			// make filter status option 
			$status = getUserStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '" '.($key == 'active' ? 'selected' : '').'>' . $val . '</option>';
			}
			$status = getUserType(-1); // all
			$optionType = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionType .= '<option value="' . $key . '">' . $val . '</option>';
			}

			$data = [
				'page' => (object)[
					'key' => '2-users',
					'title' => 'Master',
					'subtitle' => 'User',
					'navbar' => 'User',
				],
				'admin' => $this->Admin->find(session()->admin_id),
				'role' => $this->role,
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionType' => $optionType,
			];
			return view('user/index', $data);
		}
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_user');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {

			// fields order 0, 1, 2, ...
			$fields_order = [
				null,
				"t.name",
				"t.phone_no",
				"t.email",
			];
			// fields to search with
			$fields_search = [
				"t.phone_no",
				"t.email",
				"t.name",
			];
			// select fields
			$select_fields = 't.user_id,t.phone_no,t.email, t.name, t.status, t.type, t.submission, t.photo_id';

			// building where query
			$status = $req->getVar('status') ?? '';
			$submission = $req->getVar('submission') ?? '';
			$where = ['t.deleted_at' => null];
			if ($status != 'all' && !empty($status)) $where += ['t.status' => $status];
			if ($submission != 'all' && !empty($submission)) $where += ['t.submission' => 'y'];

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

			$data = array();
			if (count($dataResult) > 0) {
				helper('html');
				helper('user_status');
				$i = $start;
				$access['submission'] = hasAccess($this->role, 'r_submission');

				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$url_photos = base_url() . '/uploads/photo_id/' . $row->photo_id;

					$attribute_data['default'] =  htmlSetData(['user_id' => $row->user_id]);
					$attribute_data['photo_id'] =  htmlSetData(['photo_id' => $url_photos]);



					$action = "<button class=\"btn btn-xs mb-2 btn-".($row->status == 'active' ? 'success' : 'default')."\">".getUserStatus($row->status)."</button>";
					$action .= "<br><button class=\"btn btn-xs mb-2 btn-".($row->type == 'active' ? 'success' : 'default')."\">".getUserType($row->type)."</button>";
					$submission = "";
					if ($row->submission == "y") {
						$submission .= $access['submission'] ? '<br><div class="btn-group" role="group">
							<button id="btnGroupSubmission" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Submission
							</button>
							<div class="dropdown-menu" aria-labelledby="btnGroupSubmission">
							<a class="dropdown-item btnReview" href="#" ' . $attribute_data['default'] . $attribute_data['photo_id'] . '>Review</a>
							<a class="dropdown-item btnReject" href="#" ' . $attribute_data['default'] . '>Reject</a>
							<a class="dropdown-item btnAccept" href="#" ' . $attribute_data['default'] . '>Accept</a>
							</div>
						</div>' : '';
					}
					$r = array();
					$r[] = $i;
					$r[] = $row->email;
					$r[] = $row->phone_no;
					$r[] = $row->name;
					$r[] = $action.$submission;
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

	function updateSubmission()
	{
		$response = initResponse('Unauthorized.');
		$this->db = \Config\Database::connect();

		if (session()->has('admin_id')) {
			$user_id = $this->request->getPost('user_id');
			$status_submission = $this->request->getPost('status_submission');
			$check_role = checkRole($this->role, 'r_submission');
			if ($check_role->success) {
				$this->db->transStart();
				$where = [
					'user_id' => $user_id,
				];
				$dataUser = $this->User->getUser($where);
				if ($dataUser) {
					if ($dataUser->submission == 'y') {

						if ($status_submission == 'y') { // jika submission di accept atau approve
							$data = [
								'nik_verified' => 'y',
								'submission' => 'n',
								'type' => 'agent',
							];
							$this->User->update($user_id, $data);
						} else if ($status_submission == 'n') { // jika submission di reject atau tolak
							$data = [
								'submission' => 'n',
							];
							$this->User->update($user_id, $data);
							// var_dump($this->User->getLastQuery());
							// die;
						}
						$this->db->transComplete();
						if ($this->db->transStatus() === FALSE) {
							// transaction has problems
							$response->message = "Failed to perform task! #usr01a";
						} else {
							$response->success = true;
							$response->message = "Successfully for update type of user";
						}
					} else {
						$response->message = "User Id $user_id is not available for submission";
					}
				} else {
					$response->message = "User Id Not Found";
				}
			} else {
				$response->message = $check_role->message;
			}
		}

		return $this->respond($response, 200);
	}
}
