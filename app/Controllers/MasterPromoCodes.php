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
		$this->builder = $this->db->table($this->table_name);
	}

	public function index()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = [
			'page' => (object)[
				'title' => 'Master',
				'subtitle' => 'Promo Codes',
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

		$columns_order = array(
			null,
			"t1.id",
			"t1.code",
			"t1.status",
			"t1.updated_at",
		);
		$columns_search = array(
			"t1.code",
		);
		$data_rows = array();
		$data_rows_filtered = array();
		$status = isset($_REQUEST['status']) ? (int)$req->getVar('status') : '';
		$where = array('t1.deleted_at' => null);
		if ($status == 1) $where += array('t1.status' => 1);
		elseif ($status == 2) $where += array('t1.status' => 0);

		$this->builder
		// ->start_cache()
			->from("$this->table_name as t1")
			->select('t1.id,t1.promo_id,t1.code,t1.status,t1.updated_at,t1.updated_by')
			->where($where);
		if (isset($columns_order[$col])) $this->builder->orderBy($columns_order[$col],  $dir);
		if (isset($columns_order[$col])) $this->builder->orderBy($columns_order[$col],  $dir);
		if (!empty($req->getVar('search')['value'])) {
			$search = $req->getVar('search')['value'];
			$search_array = array();
			foreach ($columns_search as $key) $search_array[$key] = $search;
			$this->builder->groupStart()
				->orLike($search_array)
				->groupEnd();
		}
		// $this->builder->stop_cache();
		$data_rows = $this->builder->get()->getResultArray();
		// dd($data_rows);
		$this->builder
		// ->start_cache()
			->from("$this->table_name as t1")
			->select('t1.id,t1.promo_id,t1.code,t1.status,t1.updated_at,t1.updated_by')
			->where($where);
		if (isset($columns_order[$col])) $this->builder->orderBy($columns_order[$col],  $dir);
		if (isset($columns_order[$col])) $this->builder->orderBy($columns_order[$col],  $dir);
		// dd($req->getVar('search'));
		if (!empty($req->getVar('search')['value'])) {
			$search = $req->getVar('search')['value'];
			$search_array = array();
			foreach ($columns_search as $key) $search_array[$key] = $search;
			$this->builder->groupStart()
				->orLike($search_array)
				->groupEnd();
		}
		$this->builder->limit($length, $start);
		$data_rows_filtered = $this->builder->get()->getResultArray();
		// $this->builder->flush_cache();
		$totalFiltered = count($data_rows);
		// echo $this->db->last_query();die;

		$data = array();
		if (!empty($data_rows_filtered)) {
			$i = $start;
			$btn_disabled = ' disabled';
			$btn_hide = ' d-none';
			// if ((int)$this->session->userdata('master_mitra_full') > 0) {
			// 	$btn_disabled = '';
				$btn_hide = '';
			// }
			foreach ($data_rows_filtered as $row) {
				$i++;

				$btn_edit_data = 'data-id="' . $row['id'] . '"
				data-code="' . $row['code'] . '"
				data-status="' . $row['status'] . '"
				';
				$action = '
				<button class="btn btn-xs mb-2 btn-success btnAction btnEdit '.$btn_hide.'" title="Edit Kode Promo" ' . $btn_edit_data . ' ' . $btn_disabled . '><i class="fa fa-edit"></i> Edit</button>
				<br><button class="btn btn-xs mb-2 btn-danger btnAction btnDelete '.$btn_hide.'" title="Delete Kode Promo" data-id="' . $row['id'] . '" data-code="' . $row['code'] . '" ' . $btn_disabled . '><i class="fa fa-trash-o"></i> Delete</button>
				';

				$r = array();
				$r[] = $i;
				$r[] = $row['id'];
				$r[] = $row['code'];
				$r[] = $row['status'] == 1 ? 'Active' : 'Inactive';
				$r[] = $row['updated_at'].'<br>'.$row['updated_by'];
				$r[] = $action;
				$data[] = $r;
			}
		}
		//$totalFiltered=count($data);

		$json_data = array(
			"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval($totalFiltered),  // total number of records
			"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		echo json_encode($json_data);  // send data as json format
	}

	public function delete_kodepromo()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = array(
			'deleted_at'	=> date('Y-m-d H:i:s')
		);
		$where = array('id' => $_POST['id']);
		$success = false;
		$message = 'No message';
		$this->db->trans_begin();
		$this->access->updatetable('code', $data, $where);
		if ($this->db->trans_status() === FALSE) {
			$message = $this->db->error();
			$this->db->trans_rollback();
		} else {
			$success = true;
			$message = "Berhasil menghapus Kode Promo";
			$this->db->trans_commit();
		}
		if (is_array($message)) $message = "[" . implode("] ", $message);
		echo json_encode(array('success' => $success, 'message' => $message));
	}

	public function save_kodepromo()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$id = isset($_POST['id']) ? (int)$this->input->post('id') : 0;
		$code = isset($_POST['code']) ? $this->input->post('code') : '';
		$status = isset($_POST['status']) ? $this->input->post('status') : '';
		$data = [
			'code'	=> $code,
			'status'			=> (int)$status,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => $this->session->userdata('u_name'),
		];
		$where = array();
		$success = false;
		$message = 'No message';
		// cek apakah kode promo unique
		if(true) {
			$this->db->trans_begin();
			if ($id > 0) {
				// update code field in pameran
				$code_data = $this->db->select('mt1.id,code,id_pameran')
				->from('mapping_code mkp')
				->join('code kp', 't1.id=mt1.id', 'left')
				->where('mt1.id', $id)
				->get()->row();
				if($code_data) {
					$pameran = $this->db->select('code')->where('id_pameran', $code_data->id_pameran)->get('pameran')->row();
					// var_dump($pameran);die;
					if($pameran) {
						$code_arr = explode(', ', $pameran->code);
						$new_code_arr = [];
						foreach($code_arr as $value) {
							$new_code_arr[] = $code_data->code == $value ? $code : $value;
						}
						// print_r($code_arr);
						// print_r($new_code_arr);
						// die;
						// $code_arr = array_map(fn($value) => ($code_data->code == $value) ? $code : $value, $code_arr);
						$code_new = implode(',',$new_code_arr);
						$this->access->updatetable('pameran', array('code' => $code_new), array('id_pameran' => $code_data->id_pameran));
					}
				}
				$where = [
					'id' => $id,
				];
				$this->access->updatetable('code', $data, $where);
				$message = "Berhasil mengupdate Kode Promo: $code";
			} else {
				$data += [
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => $this->session->userdata('u_name'),
				];
				$this->access->inserttable('code', $data);
				$message = "Berhasil menambahkan Kode Promo: $code";
			}
			if ($this->db->trans_status() === FALSE) {
				$message = $this->db->error();
				$this->db->trans_rollback();
			} else {
				$success = true;
				$this->db->trans_commit();
			}
		}
		if (is_array($message)) $message = "[" . implode("] ", $message);
		echo json_encode(array('success' => $success, 'message' => $message));
	}
}
