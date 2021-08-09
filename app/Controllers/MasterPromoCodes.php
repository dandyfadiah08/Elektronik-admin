<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminsModel;
use App\Models\MasterPromoCodesModel;

class MasterPromoCodes extends BaseController
{
	public function __construct()
	{
		$this->model = new MasterPromoCodesModel();
		$this->admin_model = new AdminsModel();
		$this->db = \Config\Database::connect();
		$this->table_name = 'master_promo_codes';
		$this->builder = $this->db->table("$this->table_name as t");
	}

	public function index()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = [
			'page' => (object)[
				'title' => 'Master',
				'subtitle' => 'Promo Codes',
				'navbar' => 'Promo Codes',
			],
			'admin' => $this->admin_model->find(session()->admin_id),
			'role' => $this->model->find(session()->admin_id),
			'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
		];

		return view('master_promo_codes/index', $data);
	}

	function load_data()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;

		// fields order 0, 1, 2, ...
		$fields_order = array(
			null,
			"t.id",
			"t.code",
			"t.status",
			"t.updated_at",
		);
		// fields to search with
		$fields_search = array(
			"t.code",
		);
		// select fields
		$select_fields = 't.id,t.promo_id,t.code,t.status,t.updated_at,t.updated_by';

		// building where query
		$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
		$where = array('t.deleted_at' => null);
		if ($status == 1) $where += array('t.status' => 1);
		elseif ($status == 2) $where += array('t.status' => 0);

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

				$btn_edit_data = 'data-id="' . $row->id . '"
				data-code="' . $row->code . '"
				data-status="' . $row->status . '"
				';
				$action = '
				<button class="btn btn-xs mb-2 btn-success btnAction btnEdit '.$btn_hide.'" title="Edit Kode Promo" ' . $btn_edit_data . ' ' . $btn_disabled . '><i class="fa fa-edit"></i> Edit</button>
				<br><button class="btn btn-xs mb-2 btn-danger btnAction btnDelete '.$btn_hide.'" title="Delete Kode Promo" data-id="' . $row->id . '" data-code="' . $row->code . '" ' . $btn_disabled . '><i class="fa fa-trash-o"></i> Delete</button>
				';

				$r = array();
				$r[] = $i;
				$r[] = $row->id;
				$r[] = $row->code;
				$r[] = $row->status == 1 ? 'Active' : 'Inactive';
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

		echo json_encode($json_data);
	}

	public function save()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$id = isset($_POST['id']) ? (int)$this->request->getPost('id') : '0';
		
		$code = isset($_POST['code']) ? $this->request->getPost('code') : '';
		$status = isset($_POST['status']) ? $this->request->getPost('status') : '';
		$data = [
			'code'	=> $code,
			'status'			=> (int)$status,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => session()->get('u_name'),
		];
		
		$success = false;
		$message = 'No message';
		// cek apakah kode promo unique
		if(true) {
			$data = [
				'code' => $code,
				'status' => $status,
			];
			$this->db->transStart();
			
			if ($id > 0) {
				// update code field in pameran
				// $code_data = $this->db->select('mt.id,code,id_pameran')
				// ->from('mapping_code mkp')
				// ->join('code kp', 't.id=mt.id', 'left')
				// ->where('mt.id', $id)
				// ->get()->row();
				// if($code_data) {
				// 	$pameran = $this->db->select('code')->where('id_pameran', $code_data->id_pameran)->get('pameran')->row();
				// 	// var_dump($pameran);die;
				// 	if($pameran) {
				// 		$code_arr = explode(', ', $pameran->code);
				// 		$new_code_arr = [];
				// 		foreach($code_arr as $value) {
				// 			$new_code_arr[] = $code_data->code == $value ? $code : $value;
				// 		}
				// 		// print_r($code_arr);
				// 		// print_r($new_code_arr);
				// 		// die;
				// 		// $code_arr = array_map(fn($value) => ($code_data->code == $value) ? $code : $value, $code_arr);
				// 		$code_new = implode(',',$new_code_arr);
				// 		$this->access->updatetable('pameran', array('code' => $code_new), array('id_pameran' => $code_data->id_pameran));
				// 	}
				// }
				$data += [
					'updated_by' => session()->get('username'),
				];
				$message = "Berhasil mengupdate Kode Promo: $code";
				$hasilnya = $this->model->update($id, $data);

			} else {
				$data += [
					// 'created_at' => date('Y-m-d H:i:s'),
					'created_by' => session()->get('username'),
					'updated_at' => date('Y-m-d H:i:s'),
					'updated_by' => session()->get('username'),
				];
				$message = "Berhasil menambahkan Kode Promo: $code";
				
				$this->model->insert($data);
				// var_dump($this->model);
				// die;
			}
			if ($this->db->transStatus() === FALSE) {
				$message = $this->db->error();
				$this->db->transRollback();
			} else {
				$success = true;
				$this->db->transCommit();
			}
		}
		$this->db->transComplete();
		if (is_array($message)) $message = "[" . implode("] ", $message);
		echo json_encode(array('success' => $success, 'message' => $message));
	}

	public function delete()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = array(
			'deleted_at'	=> date('Y-m-d H:i:s'),
			'deleted_by'	=> session()->get('username'),
		);
		$id = isset($_POST['id']) ? (int)$this->request->getPost('id') : '0';
		$success = false;
		$message = 'No message';
		$this->db->transStart();
		$this->model->update($id,$data);
		// die;
		if ($this->db->transStatus() === FALSE) {
			$message = $this->db->error();
			$this->db->transRollback();
		} else {
			$success = true;
			$message = "Berhasil menghapus Kode Promo";
			$this->db->transCommit();
		}
		$this->db->transComplete();
		if (is_array($message)) $message = "[" . implode("] ", $message);
		echo json_encode(array('success' => $success, 'message' => $message));
	}

}
