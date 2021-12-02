<?php

namespace App\Controllers;

use App\Models\DeviceChecks;
use App\Models\MerchantModel;
use App\Models\Referrals;
use App\Models\Users as ModelsUsers;

class Users extends BaseController
{
	protected $User, $DeviceCheck, $Referral;

	public function __construct()
	{
		$this->User = new ModelsUsers();

		$this->db = \Config\Database::connect();
		$this->table_name = 'users';
		$this->builder = $this->db->table("$this->table_name as t");
	}

	public function index()
	{
		$check_role = checkRole($this->role, 'r_user');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('format');
			helper('user_status');

			// make filter status option 
			$status = getUserStatus(-1); // all
			$optionStatus = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionStatus .= '<option value="' . $key . '" ' . ($key == 'active' ? 'selected' : '') . '>' . $val . '</option>';
			}
			$status = getUserType(-1); // all
			$optionType = '<option></option><option value="all">All</option>';
			foreach ($status as $key => $val) {
				$optionType .= '<option value="' . $key . '">' . $val . '</option>';
			}

			// make merchant option 
			$this->Merchant = new MerchantModel();
			$merchants = $this->Merchant->getMerchants('merchant_id,merchant_name'); // all
			$optionMerchant = '<option></option><option value="all">All</option>';
			if ($merchants) foreach ($merchants as $val) {
				$optionMerchant .= '<option value="' . $val->merchant_id . '">' . $val->merchant_name . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-users',
					'title' => 'Master',
					'subtitle' => 'User',
					'navbar' => 'User',
				],
				'search' => $this->request->getGet('s') ? "'" . safe2js($this->request->getGet('s')) . "'" : 'null',
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
				'optionStatus' => $optionStatus,
				'optionType' => $optionType,
				'optionMerchant' => $optionMerchant,
			];
			return view('user/index', $this->data);
		}
	}

	function load_data()
	{
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
				"t.created_at",
				"t.name",
				"t.phone_no",
				"t.email",
			];
			// fields to search with
			$fields_search = [
				"t.created_at",
				"t.phone_no",
				"t.email",
				"t.name",
				"t.nik",
				"t.ref_code",
			];
			$this->builder->join("merchants as t1", "t1.merchant_id=t.merchant_id", "left");
			// select fields
			$select_fields = 't.user_id,t.phone_no,t.email,t.name,t.status,t.type,t.submission,t.photo_id,t.nik,t.created_at,t.merchant_id,t1.merchant_name,t1.merchant_code';

			// building where query
			$status = $req->getVar('status') ?? '';
			$submission = $req->getVar('submission') ?? 'true';
			$type = $req->getVar('type') ?? '';
			$merchant = $req->getVar('merchant') ?? '';
			$date = $req->getVar('date') ?? '';
			if (!empty($date) && $submission == 'false') {
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(t.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = [
				't.deleted_at' => null,
				't.phone_no_verified' => 'y',
			];
			if ($status != 'all' && !empty($status)) $where += ['t.status' => $status];
			if ($submission == 'true') $where += ['t.submission' => 'y'];
			if ($type != 'all' && !empty($type)) $where += ['t.type' => $type];
			if ($merchant != 'all' && !empty($merchant)) $where += ['t.merchant_id' => $merchant];

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
			if (isset($fields_order[$col])) $this->builder->orderBy($fields_order[$col], $dir); // add order query to builder

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
				$access = [
					'view_phone_no' => hasAccess($this->role, 'r_view_phone_no'),
					'view_email' => hasAccess($this->role, 'r_view_email'),
					'submission' => hasAccess($this->role, 'r_submission'),
					'logs' => hasAccess($this->role, 'r_logs'),
				];
				$url = [
					'detail'	=> base_url() . '/users/detail/',
					'merchant'	=> base_url() . '/merchants?s=',
					'photo_id'	=> base_url() . '/uploads/photo_id/',
				];
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$action = "";

					$attribute_data['default'] =  htmlSetData(['user_id' => $row->user_id, 'nik' => $row->nik, 'name' => $row->name]);
					$attribute_data['photo_id'] =  htmlSetData(['photo_id' => $url['photo_id'] . $row->photo_id]);

					$action = "<button class=\"btn btn-xs mb-2 btn-" . ($row->status == 'active' ? 'success' : 'default') . "\">" . getUserStatus($row->status) . "</button>";
					$action .= "<br><button class=\"btn btn-xs mb-2 btn-" . ($row->type == 'active' ? 'success' : 'default') . "\">" . getUserType($row->type) . "</button>";
					$submission = "";
					if ($row->submission == "y") {
						$submission .= !$access['submission'] ? '' :
							'<br><div class="btn-group" role="group">
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
					$btn['logs'] = [
						'class'	=> "btnLogs" . ($access['logs'] ? '' : ' d-none'),
						'title'	=> "View logs of user $row->name",
						'data'	=> 'data-id="' . $row->user_id . '"',
						'icon'	=> 'fas fa-history',
						'text'	=> '',
					];
					$merchant = $row->merchant_id > 0 ? '<br><a class="btn btn-xs mb-2 btn-warning" href="' . $url['merchant'] . $row->merchant_code . '" target="_blank" title="View merchant">' . $row->merchant_name . '</a>' : '';
					$name = '<a href="' . $url['detail'] . $row->user_id . '"  target="_blank" title="View user details">' . $row->name . '</a>';
					$r = array();
					$r[] = $i;
					$r[] = $row->created_at;
					$r[] = htmlLink($btn['logs'], false) . $name . $merchant;
					$r[] = $access['view_phone_no'] ? $row->phone_no : '628***';
					$r[] = $access['view_email'] ? $row->email : '**@**';
					$r[] = $action . $submission;
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
				$user = $this->User->getUser($where, 'user_id,name,submission,notification_token,nik');
				if ($user) {
					if ($user->submission == 'y') {

						if ($status_submission == 'y') { // jika submission di accept atau approve
							$data = [
								'nik_verified' => 'y',
								'submission' => 'n',
								'type' => 'agent',
							];
							$log_cat = 32; // accepted
						} else if ($status_submission == 'n') { // jika submission di reject atau tolak
							$data = [
								'submission' => 'n',
							];
							$log_cat = 59; // rejected
						}
						$this->User->update($user_id, $data);
						$this->db->transComplete();
						if ($this->db->transStatus() === FALSE) {
							// transaction has problems
							$response->message = "Failed to perform task! #usr01a";
						} else {
							$response->success = true;
							$response->message = "Successfully for update submission of user";

							// log
							$data += [
								'user_id' => $user->user_id,
								'nik' => $user->nik
							];
							$this->log->in(session()->username, $log_cat, json_encode($data));
							$this->log->in("$user->name\n" . session()->username, $log_cat, json_encode($data), session()->admin_id, $user->user_id, false);
						}
					} else {
						$response->success = true;
						$response->message = "User Id $user_id is not available for submission";
					}
					if ($response->success == true) {
						try {
							$title = $status_submission == 'n' ? "Sorry" : "Congatulation, Your submission is approved!";
							$content = $status_submission == 'n' ? "Please reapply to become a Wowfonet Red Member" : "Congratulations, now you have become a Wowfonet Red Member";
							$notification_data = [
								'type'		=> 'notif_submission'
							];

							$notification_token = $user->notification_token;
							// var_dump($notification_token);die;
							helper('onesignal');
							$send_notif_submission = sendNotification([$notification_token], $title, $content, $notification_data); // hanya ke app2
							$response->data['send_notif_submission'] = $send_notif_submission;
						} catch (\Exception $e) {
							$response->message .= " But, unable to send notification: " . $e->getMessage();
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

	function detail($user_id = 0)
	{
		$check_role = checkRole($this->role, 'r_user');
		if (!$check_role->success) return view('layouts/unauthorized', $this->data);
		elseif ($user_id < 1) return view('layouts/unauthorized', $this->data);
		else {
			$this->data += [
				'page' => (object)[
					'key' => '2-users',
					'title' => 'User',
					'subtitle' => 'Details',
					'navbar' => 'Details',
				],
			];

			// select user
			$where = ['u.user_id' => $user_id, 'u.deleted_at' => null];
			$user = $this->User->getUserDetail($where);
			if (!$user) {
				$this->data += ['url' => base_url() . 'users/detail/' . $user_id];
				return view('layouts/not_found', $this->data);
			} else {
				helper('user_status');
				helper('html');
				helper('number');
				helper('format');

				// make filter status option 
				$status = getUserStatus(-1); // all
				$optionStatus = '<option></option><option value="all">All</option>';
				foreach ($status as $key => $val) {
					$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
				}
				// make filter type option 
				$types = getUserType(-1); // all
				$optionType = '<option></option><option value="all">All</option>';
				foreach ($types as $key => $val) {
					$optionType .= '<option value="' . $key . '">' . $val . '</option>';
				}
				// make merchant option 
				$this->Merchant = new MerchantModel();
				$merchants = $this->Merchant->getMerchants('merchant_id,merchant_name'); // all
				$optionMerchant = '<option></option><option value="all">All</option>';
				if ($merchants) foreach ($merchants as $val) {
					$optionMerchant .= '<option value="' . $val->merchant_id . '">' . $val->merchant_name . '</option>';
				}
				// make filter level option 
				$levels = getUserLevel(-1); // all
				unset($levels['0']);
				$optionLevel = '<option></option><option value="all" selected>All</option>';
				foreach ($levels as $key => $val) {
					$optionLevel .= '<option value="' . $key . '">' . $val . '</option>';
				}

				$where = [
					'user_id'			=> $user_id,
					'status_internal'	=> '5',
				];
				$this->DeviceCheck = new DeviceChecks();
				$this->Referral = new Referrals();

				$transactions = $this->DeviceCheck->getDevice($where, 'COUNT(check_id) as total_transaction, SUM(price) as total_transaction_price');
				$referralStatus = $this->Referral->countReferralByParent($user_id);

				// var_dump($referralStatus);die;
				// if (!$referralStatus) {
				// 	$referralStatus = [
				// 		'jum_user_active'	=> '0',
				// 		'jum_user_pending'	=> '0',
				// 	];
				// 	$referralStatus = (object)  $referralStatus;
				// }

				$this->data += [
					'user'	=> $user,
					'other'	=> (object)[
						'total_transaction'			=> $transactions->total_transaction ?? 0,
						'total_transaction_price'	=> $transactions->total_transaction_price ?? 0,
						'active_referral'			=> $referralStatus->jum_user_active ?? 0,
						'pending_referral'			=> $referralStatus->jum_user_pending ?? 0,
					],
					'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
					'optionStatus' => $optionStatus,
					'optionType' => $optionType,
					'optionMerchant' => $optionMerchant,
					'optionLevel' => $optionLevel,
					'access' => ['export' => hasAccess($this->role, 'r_export_user')],
				];
				$this->data['page']->subtitle = $user->name;
				$view = 'detail';
				return view('user/' . $view, $this->data);
			}
		}
	}

	public function load_referrals()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$user_id = $req->getVar('user_id') ?? false;
		$check_role = checkRole($this->role, 'r_user');
		if (!$check_role->success || !$user_id) {
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
				"u2.created_at",
				"ur.ref_level",
				"u2.name",
				"t1.merchant_name",
				"u2.phone_no",
				"u2.email",
				"u2.status",
				"u2.type",
				"u2.submission",
			];
			// fields to search with
			$fields_search = [
				"u2.created_at",
				"u2.phone_no",
				"u2.email",
				"u2.name",
				"u2.nik",
				"u2.ref_code",
			];

			$this->Referral = new Referrals();
			$this->builder2 = $this->Referral->getReferralLevel1($user_id, false);

			$this->builder2->join("merchants as t1", "t1.merchant_id=u2.merchant_id", "left");
			// select fields
			$select_fields = 'u2.user_id,u2.phone_no,u2.email,u2.name,u2.status,u2.type,u2.submission,u2.photo_id,u2.nik,u2.created_at,u2.merchant_id,t1.merchant_name,t1.merchant_code,ur.ref_level';

			// building where query
			$status = $req->getVar('status') ?? '';
			$submission = $req->getVar('submission') ?? 'true';
			$type = $req->getVar('type') ?? '';
			$merchant = $req->getVar('merchant') ?? '';
			$level = $req->getVar('level') ?? 'all';
			$date = $req->getVar('date') ?? '';
			if (!empty($date) && $submission == 'false') {
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder2->where("date_format(u2.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder2->where("date_format(u2.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = [
				'u2.deleted_at' => null,
				'u2.phone_no_verified' => 'y',
			];
			if ($status != 'all' && !empty($status)) $where += ['u2.status' => $status];
			if ($submission == 'true') $where += ['u2.submission' => 'y'];
			if ($type != 'all' && !empty($type)) $where += ['u2.type' => $type];
			if ($merchant != 'all' && !empty($merchant)) $where += ['u2.merchant_id' => $merchant];
			if ($level != 'all') $where += ['ur.ref_level' => $level];

			// add select and where query to builder
			$this->builder2
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
			if (isset($fields_order[$col])) $this->builder2->orderBy($fields_order[$col], $dir); // add order query to builder

			// bulding search query
			if (!empty($req->getVar('search')['value'])) {
				$search = $req->getVar('search')['value'];
				$search_array = [];
				foreach ($fields_search as $key) $search_array[$key] = $search;
				// add search query to builder
				$this->builder2
					->groupStart()
					->orLike($search_array)
					->groupEnd();
			}

			// var_dump($this->builder2->getCompiledSelect());die;
			$totalData = count($this->builder2->get(0, 0, false)->getResult()); // 3rd parameter is false to NOT reset query

			$this->builder2->limit($length, $start); // add limit for pagination
			$dataResult = [];
			$dataResult = $this->builder2->get()->getResult();

			$data = array();
			if (count($dataResult) > 0) {
				helper('html');
				helper('user_status');
				$i = $start;
				$access = [
					'view_phone_no' => hasAccess($this->role, 'r_view_phone_no'),
					'view_email' => hasAccess($this->role, 'r_view_email'),
					'logs' => hasAccess($this->role, 'r_logs'),
				];
				$url = [
					'detail'	=> base_url() . '/users/detail/',
					'merchant'	=> base_url() . '/merchants?s=',
					'photo_id'	=> base_url() . '/uploads/photo_id/',
				];
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$status_user = "<button class=\"btn btn-xs mb-2 btn-" . ($row->status == 'active' ? 'success' : 'default') . "\">" . getUserStatus($row->status) . "</button>";
					$type_user = "<button class=\"btn btn-xs mb-2 btn-" . ($row->type == 'active' ? 'success' : 'default') . "\">" . getUserType($row->type) . "</button>";
					$submission = 'No';
					if ($row->submission == "y") {
						$submission = '<br><button class="btn btn-xs mb-2 btn-warning">On Submission</button>';
					}
					$btn['logs'] = [
						'class'	=> "btnLogs",
						'title'	=> "View logs of user $row->name",
						'data'	=> 'data-id="' . $row->user_id . '"',
						'icon'	=> 'fas fa-history',
						'text'	=> '',
					];
					$name = '<a href="' . $url['detail'] . $row->user_id . '"  target="_blank" title="View user details">' . $row->name . '</a>';
					$merchant = $row->merchant_id > 0 ? '<a class="btn btn-xs mb-2 btn-warning" href="' . $url['merchant'] . $row->merchant_code . '" target="_blank" title="View merchant">' . $row->merchant_name . '</a>' : '-';
					$ref_level = "<button class=\"btn btn-xs mb-2 btn-" . ($row->ref_level == 1 ? 'success' : 'primary') . "\">" . getUserLevel($row->ref_level) . "</button>";
					$r = array();
					$r[] = $i;
					$r[] = $row->created_at;
					$r[] = $ref_level;
					$r[] = ($access['logs'] ? htmlLink($btn['logs'], false) : '').$name;
					$r[] = $merchant;
					$r[] = $access['view_phone_no'] ? $row->phone_no : '628***';
					$r[] = $access['view_email'] ? $row->email : '**@**';
					$r[] = $type_user;
					$r[] = $status_user;
					$r[] = $submission;
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
}
