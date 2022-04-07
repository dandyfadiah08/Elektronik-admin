<?php

namespace App\Controllers;

use App\Models\Users;

class Tax extends BaseController
{
	var $User;
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->User = new Users();
		helper('validation');
	}

	public function index()
	{

		$check_role = checkRole($this->role, 'r_tax');
		if (!$check_role->success) return view('layouts/unauthorized', $this->data);

		helper(['html', 'format']);
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
				'key' => '2-tax',
				'title' => 'Tax Data',
				'subtitle' => 'List',
				'navbar' => 'Tax Data',
			],
			'search' => $this->request->getGet('s') ? "'" . safe2js($this->request->getGet('s')) . "'" : 'null',
			'optionUsers' => $optionUsers,
		];

		return view('tax/index', $this->data);
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;

		$check_role = checkRole($this->role, 'r_tax');
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->db = \Config\Database::connect();
			$this->builder = $this->db->table("user_balance as ub")
				->join("users as u", "u.user_id = ub.user_id", "left");
			$this->builder2 = $this->db->table("device_checks as dc")
				->join("users as u", "u.user_id = dc.user_id", "left");

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"data_type",
				"user_balance_id",
				"name",
				"amount",
				"notes",
				"updated_at",
			);
			// fields to search with
			$fields_search = array(
				"user_balance_id",
				"name",
				"amount",
				"notes",
				"ub.updated_at",
			);
			$fields_search2 = array(
				"check_id",
				"name",
				"price",
				"check_code",
				"dc.updated_at",
			);
			// select fields
			$select_fields = "IF(ub.type='agentbonus','agentbonus','bonus') as data_type,user_balance_id,name,amount,notes,ub.updated_at,ub.updated_by";
			$select_fields2 = "'transaction',check_id,name,price,check_code,dc.created_at,'-'";

			// building where query
			$date = $req->getVar('date') ?? '';
			if (!empty($date)) {
				$dates = explode(' / ', $date);
				if (count($dates) == 2) {
					$start = $dates[0];
					$end = $dates[1];
					$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
					$this->builder2->where("date_format(dc.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
					$this->builder2->where("date_format(dc.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
				}
			}
			$where = ['ub.cashflow' => 'in', 'ub.type!=' => 'transaction'];
			$where2 = ['dc.status_internal' => 5];

			// add select and where query to builder
			$this->builder->select($select_fields)->where($where);
			$this->builder2->select($select_fields2)->where($where2);

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
			if (isset($fields_order[$col])) {
				$orderby = ' ORDER BY '. $fields_order[$col].' '.$dir;
			}

			// bulding search query
			if (!empty($req->getVar('search')['value'])) {
				$search = $req->getVar('search')['value'];
				$search_array = [];
				$search_array2 = [];
				foreach ($fields_search as $key) $search_array[$key] = $search;
				foreach ($fields_search2 as $key) $search_array2[$key] = $search;
				// add search query to builder
				$this->builder->groupStart()->orLike($search_array)->groupEnd();
				$this->builder2->groupStart()->orLike($search_array2)->groupEnd();
			}
			$query1 = $this->builder->getCompiledSelect();
			$query2 = $this->builder2->getCompiledSelect();
			$final_query = $this->db->query($query1 . ' UNION ' . $query2);
			$totalData = count($final_query->getResult()); // 3rd parameter is false to NOT reset query
			
			$final_query = $this->db->query("$query1 UNION $query2 $orderby LIMIT $start,$length");
			$dataResult = $final_query->getResult();

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
					$r[] = $row->data_type;
					$r[] = $row->user_balance_id;
					$r[] = $row->name;
					$r[] = number_to_currency($row->amount, "IDR");
					$r[] = $row->notes;
					$r[] = substr($row->updated_at, 0, 16);
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
		if (hasAccess($this->role, 'r_export_tax')) {
			$date = $req->getVar('date') ?? '';

			if (empty($date)) {
				$response->message = "Date range can not be blank";
			} else {
				$this->db = \Config\Database::connect();
				$this->builder = $this->db->table("user_balance as ub")
				->join("users as u", "u.user_id = ub.user_id", "left");
			$this->builder2 = $this->db->table("device_checks as dc")
				->join("device_check_details as dcd", "dcd.check_id = dc.check_id", "left")
				->join("users as u", "u.user_id = dc.user_id", "left");

				// select fields
				$select_fields = "IF(ub.type='agentbonus','agentbonus','bonus') as data_type,user_balance_id,name,amount,notes,ub.updated_at,'payment_date',u.nik";
				$select_fields2 = "'transaction',dc.check_id,name,price,check_code,dc.created_at,dcd.payment_date,u.nik";
	
				// building where query
				$date = $req->getVar('date') ?? '';
				if (!empty($date)) {
					$dates = explode(' / ', $date);
					if (count($dates) == 2) {
						$start = $dates[0];
						$end = $dates[1];
						$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
						$this->builder->where("date_format(ub.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
						$this->builder2->where("date_format(dc.created_at, \"%Y-%m-%d\") >= '$start'", null, false);
						$this->builder2->where("date_format(dc.created_at, \"%Y-%m-%d\") <= '$end'", null, false);
						}
				}
				$where = ['ub.cashflow' => 'in', 'ub.type!=' => 'transaction'];
				$where2 = ['dc.status_internal' => 5];
	
				// add select and where query to builder
				$this->builder->select($select_fields)->where($where);
				$this->builder2->select($select_fields2)->where($where2);
	
					$query1 = $this->builder->getCompiledSelect();
					$query2 = $this->builder2->getCompiledSelect();
					$final_query = $this->db->query($query1 . ' UNION ' . $query2);
					$dataResult = $final_query->getResult();
		
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
					$filename = 'tax-' . date('YmdHis') . '.csv';
					$fp = fopen($path . $filename, 'w');
					$headers = [
						'No',
						'Type',
						'ID',
						'User',
						'Amount',
						'Notes / Check Code',
						'Created Date',
						'Updated / Payment Date',
					];
					if ($access['view_photo_id']) array_push($headers, "NIK");

					fputcsv($fp, $headers);

					// looping through data result & put in csv
					foreach ($dataResult as $row) {
						// var_dump($row);die;
						$r = [
							$i++,
							$row->data_type,
							$row->user_balance_id,
							$row->name,
							$row->amount,
							$row->notes,
							substr($row->updated_at, 0, 10),
							$row->payment_date == 'payment_date' ? '' : substr($row->payment_date, 0, 10),
						];
						if ($access['view_photo_id']) array_push($r, "'$row->nik");

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
}
