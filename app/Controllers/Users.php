<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminRolesModel;
use App\Models\AdminsModel;
use App\Models\Users as ModelsUsers;

class Users extends BaseController
{
	public function __construct()
	{
		$this->model = new AdminRolesModel();
		$this->modelUser = new ModelsUsers();
		$this->admin_model = new AdminsModel();
		$this->db = \Config\Database::connect();
		$this->table_name = 'users';
		$this->builder = $this->db->table("$this->table_name as t");
	}
	public function index()
	{
		// $faker = \Faker\Factory::create('id_ID');
		// dd($faker->dateTimeBetween('-1 month', '+1 month')->format('YmdHis'));

		if(!session()->has('admin_id')) return redirect()->to(base_url());

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
		if(!session()->has('admin_id')) return redirect()->to(base_url());
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
		$select_fields = 't.user_id,t.phone_no,t.email, t.name, t.status, t.type';

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
			$i = $start;
			$btn_disabled = ' disabled';
			$btn_hide = ' d-none';
			// if ((int)$this->session->userdata('master_mitra_full') > 0) {
				$btn_disabled = '';
				$btn_hide = '';
			// }
			// looping through data result
			foreach ($dataResult as $row) {
				$i++;

				$btn_edit_data = 'data-user_id="' . $row->user_id . '"
				
				';
				$action = '
				<button class="btn btn-xs mb-2 btn-success btnAction btnEdit '.$btn_hide.'" title="Edit Kode Promo" ' . $btn_edit_data . ' ' . $btn_disabled . '><i class="fa fa-edit"></i> Edit</button>
				<br>
				';

				$r = array();
				$r[] = $i;
				$r[] = $row->email;
				$r[] = $row->phone_no;
				$r[] = $row->name;
				$r[] = $row->status;
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
}
