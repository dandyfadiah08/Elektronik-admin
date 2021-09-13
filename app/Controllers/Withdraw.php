<?php

namespace App\Controllers;

use App\Libraries\WithdrawAndPayouts;
use App\Models\AdminRolesModel;
use App\Models\AdminsModel;
use App\Models\UserPayouts;
use CodeIgniter\API\ResponseTrait;

class Withdraw extends BaseController
{
	use ResponseTrait;
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->AdminRole = new AdminRolesModel();
		$this->admin_model = new AdminsModel();
		$this->UserPayouts = new UserPayouts();
		$this->role = $this->AdminRole->find(session()->role_id);

		helper('grade');
		helper('validation');
		helper('device_check_status');

	}
	
	public function index()
	{
		//
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$check_role = checkRole($this->role, 'r_withdraw');
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			$data = [
				'page' => (object)[
					'title' => 'Master',
					'subtitle' => 'Withdraw',
					'navbar' => 'Withdraw',
				],
				'admin' => $this->admin_model->find(session()->admin_id),
				'role' => $this->role,
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
			];

			return view('withdraw/index', $data);
		}
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
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
			$this->table_name = 'user_payouts';
			$this->builder = $this->db
				->table("$this->table_name as upa")
				->join("user_payments as ups", "ups.user_payment_id = upa.user_payment_id")
				->join("payment_methods as pm", "pm.payment_method_id = ups.payment_method_id")
				->join("user_payout_details as upd", "upd.user_payout_id = upa.user_payout_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"pm.type",
				"pm_name",
				"ups.account_number",
				"ups.account_name",
				"upa.amount",
				"status_user_payouts",
			);
			// fields to search with
			$fields_search = array(
				"pm_name",
				"alias_name",
				"account_number",
				"account_name",
				
			);
			// select fields
			$select_fields = 'upa.user_payout_id, upa.user_id, upa.amount, upa.type, upa.status AS status_user_payouts, ups.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, ups.account_number, ups.account_name, upa.created_at, upa.created_by, upa.updated_at, upa.updated_by, upd.status as upd_status';

			// building where query
			$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
			$where = [
				'upa.deleted_at' => null,
				'upa.type' => 'withdraw',
			];

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

			$data = [];
			if (count($dataResult) > 0) {
				$i = $start;
				helper('html');
				helper('number');
				helper('user_balance_status_helper');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;
					$action = '';
					if($row->status_user_payouts == 2 && $row->upd_status == ''){
						$attribute_data['default'] =  htmlSetData(['user_payout_id' => $row->user_payout_id]);
						$action .= htmlButton([
							'color'	=> 'success',
							'class'	=> 'btnProceedPayment',
							'title'	=> 'Finish this this transction with automatic transfer payment process',
							'data'	=> $attribute_data['default'],
							'icon'	=> 'fas fa-credit-card',
							'text'	=> 'Proceed Payment',
						]) ;
					} elseif($row->upd_status) {
						$action = '
						<button class="btn btn-xs mb-2 btn-default">Payment: ' . $row->upd_status . '</button>
						';
					}

					$r = [];
					$r[] = $i;
					$r[] = $row->user_payout_id;
					$r[] = $row->type;
					$r[] = $row->pm_name;
					$r[] = $row->account_number;
					$r[] = $row->account_name;
					$r[] = number_to_currency($row->amount, "IDR");
					$r[] = getUserBalanceStatus($row->status_user_payouts);
					$r[] = substr($row->created_at, 0, 16);
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


	function proceed_payment()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$user_payout_id = $this->request->getPost('user_payout_id');
			$rules = ['user_payout_id' => getValidationRules('user_payout_id')];

			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
			} else {
				$role = $this->AdminRole->find(session()->role_id);
				$check_role = checkRole($role, 'r_proceed_payment');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					$select = 'ups.user_payout_id, ups.user_id, ups.user_balance_id, ups.amount, ups.type AS type_payout, ups.status, upa.payment_method_id, pm.type AS pm_type, pm.name AS bank_code, pm.alias_name, upa.account_number, upa.account_name';
					$where = [
						'ups.user_payout_id' => $user_payout_id,
						'ups.status' => 2,
						'ups.deleted_at' => null
					];
					$dataUser = $this->UserPayouts->getUserPayoutWithDetailPayment($where, $select, false);
					
					if (!$dataUser) {
						$response->message = "Invalid User Payout Id $user_payout_id";
					} else {
						// Request withdraw
						$withdraw_and_payout = new WithdrawAndPayouts();
						$response = $withdraw_and_payout->proceedPaymentLogic($dataUser);
					}
				}
			}
		}
		return $this->respond($response, 200);
	}

}
