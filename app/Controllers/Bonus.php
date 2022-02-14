<?php

namespace App\Controllers;

use App\Libraries\PaymentsAndPayouts;
use App\Models\Settings;
use App\Models\Users;

class Bonus extends BaseController
{
	var $User, $UserPayouts;
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->User = new Users();
		$this->Setting = new Settings();
		$this->google = new \Google\Authenticator\GoogleAuthenticator();

		helper('grade');
		helper('validation');
		helper('device_check_status');
		helper('user_balance_status');
	}

	public function index()
	{

		$check_role = checkRole($this->role, 'r_bonus_view');

		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {
			helper('html');
			helper('format');

			// make filter users option 
			$where = [
				'internal_agent' => 'y',
				'deleted_at' => null,
				'status' => 'active',
			];
			$users = $this->User->getUsers($where, 'user_id,name,nik');
			$optionUsers = '<option></option><option value="all">All</option>';
			foreach ($users as $user) {
				$optionUsers .= '<option value="' . $user->user_id . '">' . $user->name . ' / ' . $user->nik . '</option>';
			}

			$this->data += [
				'page' => (object)[
					'key' => '2-bonus',
					'title' => 'Agent Bonus',
					'subtitle' => 'List',
					'navbar' => 'Agent Bonus',
				],
				'search' => $this->request->getGet('s') ? "'" . safe2js($this->request->getGet('s')) . "'" : 'null',
				'optionUsers' => $optionUsers,
			];

			return view('bonus/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;

		$check_role = checkRole($this->role, 'r_bonus_view');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->db = \Config\Database::connect();
			$this->table_name = 'user_balance';
			$this->builder = $this->db
				->table("$this->table_name as ub")
				->join("users as u", "u.user_id = ub.user_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"user_balance_id",
				"name",
				"amount",
				"notes",
				"ub.updated_at",
			);
			// fields to search with
			$fields_search = array(
				"user_balance_id",
				"name",
				"amount",
				"notes",
				"ub.updated_at",
				"ub.updated_by",
			);
			// select fields
			$select_fields = 'user_balance_id,name,amount,notes,ub.updated_at,ub.updated_by';

			// building where query
			$user_id = $req->getVar('user_id') ?? '';
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = [
				'ub.type' => 'agentbonus'
			];
			if ($user_id != 'all' && $user_id != '' && $user_id > 0) $where += ['ub.user_id' => $user_id];

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
			// echo $this->builder->getCompiledSelect();die;
			$dataResult = $this->builder->get()->getResult();

			$data = [];
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				helper('number');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$r = [];
					$r[] = $i;
					$r[] = $row->user_balance_id;
					$r[] = $row->name;
					$r[] = number_to_currency($row->amount, "IDR");
					$r[] = $row->notes;
					$r[] = substr($row->updated_at, 0, 16);
					$r[] = $row->updated_by;
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

	function export()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_export_bonus')) {
			$status = $req->getVar('status') ?? '';
			$status_payment = $req->getVar('status_payment') ?? '';
			$date = $req->getVar('date') ?? '';

			if (empty($date)) {
				$response->message = "Date range can not be blank";
			} else {
				$this->db = \Config\Database::connect();
				$this->table_name = 'user_balance';
				$this->builder = $this->db
					->table("$this->table_name as ub")
					->join("users as u", "u.user_id = ub.user_id", "left");

				// select fields
				$select_fields = 'user_balance_id,name,amount,notes,ub.updated_at,ub.updated_by';

				// building where query
				$user_id = $req->getVar('user_id') ?? '';
				$date = $req->getVar('date') ?? '';
				if (!empty($date)) {
					$dates = explode(' / ', $date);
					if (count($dates) == 2) {
						$start = $dates[0];
						$end = $dates[1];
						$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
						$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
					}
				}
				$where = [
					'ub.type' => 'agentbonus'
				];
				if ($user_id != 'all' && $user_id != '' && $user_id > 0) $where += ['ub.user_id' => $user_id];


				// add select and where query to builder
				$this->builder
					->select($select_fields)
					->where($where);

				$dataResult = [];
				$dataResult = $this->builder->get()->getResult();
				// die($this->db->getLastQuery());

				if (count($dataResult) < 1) {
					$response->message = "Empty data!";
				} else {
					$i = 1;
					helper('number');
					helper('html');
					helper('format');
					$access = [
						'view_photo_id' => hasAccess($this->role, 'r_view_photo_id'),
					];
					$path = 'temp/csv/';
					$filename = 'withdraw-' . date('YmdHis') . '.csv';
					$fp = fopen($path . $filename, 'w');
					$headers = [
						'No',
						'Withdraw Request Date',
						'Withdraw Updated Date',
						'Withdraw ref',
						'Payment Type',
						'Payment Name',
						'Account Number',
						'Account Name',
						'Amount',
						'Status',
						'User Name',
					];
					if ($access['view_photo_id'])  array_push($headers, 'User NIK');

					fputcsv($fp, $headers);

					// looping through data result & put in csv
					foreach ($dataResult as $row) {
						// var_dump($row);die;
						$r = [
							$i++,
							substr($row->created_at, 0, 10),
							substr($row->updated_at, 0, 10),
							$row->withdraw_ref,
							$row->type,
							$row->pm_name,
							$row->account_number,
							$row->account_name,
							number_to_currency($row->amount, "IDR"),
							getUserBalanceStatus($row->status_user_payouts),
							$row->user_name,
						];
						if ($access['view_photo_id']) array_push($r, $row->nik);

						fputcsv($fp, $r);
					}
					$response->success = true;
					$response->message = "Done";
					$response->data = base_url('download/csv/?file=' . $filename);
				}
			}
		}
		return $this->respond($response);
	}

	function sendBonus()
	{
		helper('log');
		$response = initResponse('Unauthorized.');
		$rules = getValidationRules('send_bonus');
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			$response->message = "";
			foreach ($errors as $error) $response->message .= "$error ";
		} else {
			helper('format');
			$user_id = $this->request->getPost('user_id');
			$bonus = removeComma($this->request->getPost('bonus'));
			$notes = $this->request->getPost('notes');
			$code_auth = $this->request->getPost('codeauth');

			// check role
			$response = checkRole($this->role, 'r_send_bonus');
			if (!$response->success) return $this->respond($response);

			$setting = $this->Setting->getSetting(['_key' => '2fa_secret'], 'setting_id,val');
			if ($this->google->checkCode($setting->val, $code_auth) || env('app.environment') == 'local') {
				$where = [
					'internal_agent' => 'y',
					'deleted_at' => null,
					'status' => 'active',
				];
				$this->User = new Users();
				$user = $this->User->getUser($where, 'user_id,name,nik');
				if (!$user) {
					$response->message = "Invalid User Id $user_id";
				} else {
					// Proccess Requested withdraw
					$payment_and_payout = new PaymentsAndPayouts();
					$response = $payment_and_payout->sendBonus($user_id, $bonus, $notes, session()->admin_id, session()->username);
					// $response = initResponse("Berhasil", true); // dummy, test, ceritanya berhasil
				}
			} else {
				$response->message = '2FA Code is invalid!';
			}
		}
        writeLog("bonus", "sendBonus\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

		return $this->respond($response);
	}
}
