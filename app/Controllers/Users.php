<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminRolesModel;
use App\Models\AdminsModel;
use App\Models\DeviceChecks;
use App\Models\Users as ModelsUsers;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{
	use ResponseTrait;
	public function __construct()
	{
		$this->model = new AdminRolesModel();
		$this->modelUser = new ModelsUsers();
		$this->admin_model = new AdminsModel();
		$this->DeviceCheck = new DeviceChecks();
		$this->db = \Config\Database::connect();
		$this->table_name = 'users';
		$this->builder = $this->db->table("$this->table_name as t");

		helper('rest_api');
		helper('role');
	}
	public function index()
	{
		// $faker = \Faker\Factory::create('id_ID');
		// dd($faker->dateTimeBetween('-1 month', '+1 month')->format('YmdHis'));

		if (!session()->has('admin_id')) return redirect()->to(base_url());

		$data = [
			'page' => (object)[
				'key' => '2-users',
				'title' => 'Master',
				'subtitle' => 'User',
				'navbar' => 'User',
			],
			'admin' => $this->admin_model->find(session()->admin_id),
			'role' => $this->model->find(session()->admin_id),
			'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
		];

		return view('user/index', $data);
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;

		// fields order 0, 1, 2, ...
		$fields_order = array(
			null,
			"t.user_id",
			"t.phone_no",
			"t.email",
			"t.name",
			"t.status",
			"t.type",
		);
		// fields to search with
		$fields_search = array(
			"t.phone_no",
			"t.email",
			"t.name",
		);
		// select fields
		$select_fields = 't.user_id,t.phone_no,t.email, t.name, t.status, t.type, t.submission, t.photo_id';

		// building where query
		// $status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
		$where = array('t.deleted_at' => null);
		// if ($status == 1) $where += array('t.status' => 1);
		// elseif ($status == 2) $where += array('t.status' => 0);

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

		$data = array();
		if (count($dataResult) > 0) {
			helper('html');
			$i = $start;
			$btn_disabled = ' disabled';
			$btn_hide = ' d-none';
			// if ((int)$this->session->userdata('master_mitra_full') > 0) {
			$btn_disabled = '';
			$btn_hide = '';
			$url = base_url() . '/users/detail/';
			// }
			// looping through data result
			foreach ($dataResult as $row) {
				$i++;
				$action = "";

				$url_photos = base_url() . '/uploads/photo_id/' . $row->photo_id;

				$attribute_data['default'] =  htmlSetData(['user_id' => $row->user_id]);
				$attribute_data['photo_id'] =  htmlSetData(['photo_id' => $url_photos]);



				$status_submission = "Need Review";
				if ($row->submission == "n") $status_submission = "-";
				else {
					$action .= '<br><div class="btn-group" role="group">
						<button id="btnGroupSubmission" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Submission
						</button>
						<div class="dropdown-menu" aria-labelledby="btnGroupSubmission">
						<a class="dropdown-item btnReview" href="#" ' . $attribute_data['default'] . $attribute_data['photo_id'] . '>Review</a>
						<a class="dropdown-item btnReject" href="#" ' . $attribute_data['default'] . '>Reject</a>
						<a class="dropdown-item btnAccept" href="#" ' . $attribute_data['default'] . '>Accept</a>
						</div>
					</div>';
				}
				$btn['view'] = [
					'color'	=> 'outline-secondary',
					'href'	=>	$url.$row->user_id,
					'class'	=> 'py-2 btnAction btnManualTransfer',
					'title'	=> "View detail of $row->user_id",
					'data'	=> '',
					'icon'	=> 'fas fa-eye',
					'text'	=> 'View',
				];
				$action .= htmlAnchor($btn['view']);
				$r = array();
				$r[] = $i;
				$r[] = $row->email;
				$r[] = $row->phone_no;
				$r[] = $row->name;
				$r[] = $row->status;
				$r[] = $status_submission;
				$r[] = $row->type;
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

		echo json_encode($json_data);
	}
	function updateSubmission()
	{
		$response = initResponse('Unauthorized.');
		$this->db = \Config\Database::connect();

		if (session()->has('admin_id')) {
			$role = $this->model->find(session()->admin_id);
			$user_id = $this->request->getPost('user_id');
			$status_submission = $this->request->getPost('status_submission');
			$check_role = checkRole($role, 'r_submission');
			if ($check_role->success) {
				$this->db->transStart();
				$where = [
					'user_id' => $user_id,
				];
				$dataUser = $this->modelUser->getUser($where);
				if ($dataUser) {
					if ($dataUser->submission == 'y') {

						if ($status_submission == 'y') { // jika submission di accept atau approve
							$data = [
								'nik_verified' => 'y',
								'submission' => 'n',
								'type' => 'agent',
							];
							$this->modelUser->update($user_id, $data);
						} else if ($status_submission == 'n') { // jika submission di reject atau tolak
							$data = [
								'submission' => 'n',
							];
							$this->modelUser->update($user_id, $data);
							// var_dump($this->modelUser->getLastQuery());
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
						$response->success = true;
						$response->message = "User Id $user_id is not available for submission";
					}
					if($response->success == true){
						try{
							$title = $status_submission == 'n'? "Sorry" : "Congatulation, Your submission is approved!";
							$content = $status_submission == 'n' ? "Please reapply to become a wowfone agent" : "Congratulations, now you have become a wowfone agent";
							$notification_data = [
								'type'		=> 'notif_submission'
							];

							$notification_token = $dataUser->notification_token;
							// var_dump($notification_token);die;
							helper('onesignal');
							$send_notif_submission = sendNotification([$notification_token], $title, $content, $notification_data);
							$response->data['send_notif_submission'] = $send_notif_submission;
						} catch(\Exception $e){
							$response->message .= " But, unable to send notification: ".$e->getMessage();
						}
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

	public function detail($user_id = 0)
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$data = [
			'page' => (object)[
				'key' => '2-users',
				'title' => 'Users',
				'subtitle' => 'Details',
				'navbar' => 'Details',
			],
			'admin' => $this->admin_model->find(session()->admin_id),
			'role' => $this->admin_model->find(session()->role_id),
		];

		if($user_id < 1) return view('layouts/unauthorized', $data);
		$select = false;
		$where = array('user_id' => $user_id, 'deleted_at' => null);
		$dataUser = $this->modelUser->getUser($where, $select);
		if(!$dataUser) {
			$data += ['url' => base_url().'users/detail/'.$user_id];
			return view('layouts/not_found', $data);
		}
		$where = [
            'user_id' => $user_id,
            'status_internal' => '5',
        ];
        $total_transaction = $this->DeviceCheck->getDevice($where, 'COUNT(check_id) as total_transaction');
		$dataUser->transaction = $total_transaction;
		helper('number');
		helper('format');
		$data += ['u' => $dataUser];
		// var_dump($device_check);die;
		// $data += ['dc' => $device_check];
		$data['page']->subtitle = $dataUser->name;
		// var_dump($device_check->price);die;
		$view = 'detail';
		return view('user/'.$view, $data);
	}
}
