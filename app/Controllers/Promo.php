<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;
use App\Models\MasterPromos;

class Promo extends BaseController
{
	use ResponseTrait;
	protected $Admin, $AdminRole, $Appointment, $MasterPromo;

	public function __construct()
	{
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->MasterPromo = new MasterPromos();
		$this->db = \Config\Database::connect();
		helper('validation');
	}

	public function index()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		helper('general_status');
		helper('html');

		// make filter status option 
		$status = getPromoStatus(-1); // all
		$optionStatus = '<option></option><option value="all">All</option>';
		foreach ($status as $key => $val) {
			$optionStatus .= '<option value="' . $key . '">' . $val . '</option>';
		}

		$data = [
			'page' => (object)[
				'key' => '2-promo',
				'title' => 'Promo',
				'subtitle' => 'Master',
				'navbar' => 'Promo',
			],
			'admin' => $this->Admin->find(session()->admin_id),
			'role' => $this->AdminRole->find(session()->admin_id),
			'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
			'optionStatus' => $optionStatus,
		];

		return view('promo/index', $data);
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$role = $this->AdminRole->find(session()->admin_id);
		$check_role = checkRole($role, 'r_admin');
		$check_role->success = true; // sementara belum ada role
		if (!$check_role->success) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'master_promos';
			$this->builder = $this->db
				->table("$this->table_name as t");
				// ->join("device_check_details as t1", "t1.check_id=t.check_id", "left")

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"t.created_at",
				"promo_name",
				"start_date",
				"end_date",
				"status",
			);
			// fields to search with
			$fields_search = array(
				"promo_name",
				"start_date",
				"end_date",
			);
			// select fields
			$select_fields = 'promo_id,promo_name,start_date,end_date,created_by,created_at,updated_by,updated_at,status';

			// building where query
			$status = $req->getVar('status') ?? '';
			$where = array('t.deleted_at' => null);
			if ($status == 'all') {}
			elseif ($status > 0) $where += array('status' => $status);

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
				$check_role = checkRole($role, 'r_admin'); // belum diubah
				$btn_hide = ' d-none';
				if ($check_role->success) {
					$btn_hide = '';
				}
				$url = base_url().'/price/';
				helper('html');
				helper('general_status');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					// $attribute_data['default'] = 'data-check_code="'.$row->check_code.'" data-check_id="'.$row->check_id.'" ';
					$attribute_data['default'] =  htmlSetData([
						'id' => $row->promo_id, 
						'promo_name' => $row->promo_name,
						'start_date' => $row->start_date,
						'end_date' => $row->end_date,
						'status' => $row->status,
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit $btn_hide",
						'title'	=> "Edit promo $row->promo_name",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete $btn_hide",
						'title'	=> "Edit promo $row->promo_name",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$btn['price'] = [
						'color'	=> 'primary',
						'class'	=> "py-2 btnAction",
						'title'	=> "View price of $row->promo_name",
						'data'	=> '',
						'icon'	=> 'fas fa-money-bill-wave',
						'text'	=> 'Price',
						'href'	=> $url.$row->promo_id,
					];
					$status = getPromoStatus($row->status);
					$action = "<button class=\"btn btn-xs mb-2 btn-".($row->status == 1 ? 'success' : 'default')."\">$status</button>";
					$action .= htmlAnchor($btn['price']);
					$action .= htmlButton($btn['edit']);
					$action .= htmlButton($btn['delete']);

					$r = array();
					$r[] = $i;
					$r[] = $row->promo_id;
					$r[] = $row->promo_name;
					$r[] = $row->start_date;
					$r[] = $row->end_date;
					$r[] = "$row->updated_at<br>$row->updated_by";
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
		}

		echo json_encode($json_data);
	}

	public function save()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$role = $this->AdminRole->find(session()->admin_id);
			$check_role = checkRole($role, 'r_admin'); // belum diubah
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$promo_name = $this->request->getPost('promo_name') ?? '';
				$start_date = $this->request->getPost('start_date') ?? '';
				$end_date = $this->request->getPost('end_date') ?? '';
				$status = $this->request->getPost('status') ?? 1;
				$status = $status == 1 ? 1 : 2;

				helper('format');
				$data = [
					'promo_name' 	=> $promo_name,
					'start_date' 	=> $start_date,
					'end_date' 		=> $end_date,
					'status' 		=> $status,
					'updated_at' 	=> date('Y-m-d H:i:s'),
					'updated_by' 	=> session()->get('username'),
				];

				$this->db->transStart();
				if ((int)$id > 0) {
					$response->message = "Promo updated.";
					$this->MasterPromo->update((int)$id, $data);
				} else {
					$data += [
						'created_at' => date('Y-m-d H:i:s'),
						'created_by' => session()->get('username'),
						'updated_at' => date('Y-m-d H:i:s'),
						'updated_by' => session()->get('username'),
					];
					$response->message = "Promo added.";
					$this->MasterPromo->insert($data);
				}
				$this->db->transComplete();

				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
				}
			}
		}

		return $this->respond($response, 200);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$role = $this->AdminRole->find(session()->admin_id);
			$check_role = checkRole($role, 'r_admin'); // belum diubah
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$data = [
					'deleted_at'	=> date('Y-m-d H:i:s'),
					'deleted_by'	=> session()->get('username'),
				];
				$this->db->transStart();
				$this->MasterPromo->update($id, $data);
				$this->db->transComplete();
				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
					$response->message = "Promo deleted.";
				}
			}
		}
		return $this->respond($response, 200);
	}

}
