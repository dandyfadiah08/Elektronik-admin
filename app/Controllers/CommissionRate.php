<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;
use App\Models\CommissionRate as ModelsCommissionRate;

class CommissionRate extends BaseController
{
	public function __construct()
	{
		$this->model = new AdminRolesModel();
		$this->modelCommision = new ModelsCommissionRate();
		$this->admin_model = new AdminsModel();
		$this->db = \Config\Database::connect();
		$this->table_name = 'commission_rate';
		$this->builder = $this->db->table("$this->table_name as t");
	}
	
	public function index()
	{
		//
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = [
			'page' => (object)[
				'key' => '2-commission_rate',
				'title' => 'Commision Rate',
				'subtitle' => 'Master',
				'navbar' => 'Commision Rate',
			],
			'admin' => $this->admin_model->find(session()->admin_id),
			'role' => $this->model->find(session()->admin_id),
			'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
		];

		return view('commission_rate/index', $data);
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
			"abs(t.price_from)",
			"abs(t.price_to)",
			"abs(t.commission_1)",
			"abs(t.commission_2)",
			"abs(t.commission_3)",
			"abs(t.updated_at)",
		);
		// fields to search with
		$fields_search = array(
			"t.price_from",
			"t.price_to",
			"t.commission_1",
			"t.commission_2",
			"t.commission_3",
			"t.updated_at",
		);
		// select fields
		$select_fields = 't.id,t.price_from,t.price_to,t.commission_1,t.commission_2,t.commission_3,t.updated_at,t.updated_by';

		// building where query
		$where = array('t.deleted_at' => null);

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
			helper('number');
			helper('html');
			// looping through data result
			foreach ($dataResult as $row) {
				$i++;

				$attribute_data['default'] =  htmlSetData([
					'id'			=> $row->id,
					'price_from'	=> $row->price_from,
					'price_to'		=> $row->price_to,
					'commission_1'	=> $row->commission_1,
					'commission_2'	=> $row->commission_2,
					'commission_3'	=> $row->commission_3,
				]);
				$btn['edit'] = [
					'color'	=> 'warning',
					'class'	=> 'py-2 btnAction btnEdit '.$btn_hide,
					'title'	=> 'Edit commision rate '.number_to_currency($row->price_from, 'IDR').' to '.number_to_currency($row->price_to, 'IDR'),
					'data'	=> $attribute_data['default'],
					'icon'	=> 'fas fa-edit',
					'text'	=> 'Edit',
				];
				$btn['delete'] = [
					'color'	=> 'danger',
					'class'	=> 'py-2 btnAction btnDelete '.$btn_hide,
					'title'	=> 'Delete commision rate '.number_to_currency($row->price_from, 'IDR').' to '.number_to_currency($row->price_to, 'IDR'),
					'data'	=> $attribute_data['default'],
					'icon'	=> 'fas fa-trash-o',
					'text'	=> 'Delete',
				];

				$action = htmlCreateButton($btn['edit'], false);
				$action .= htmlCreateButton($btn['delete']);

				$r = array();
				$r[] = $i;
				$r[] = $row->id;
				$r[] = number_to_currency($row->price_from, 'IDR');
				$r[] = number_to_currency($row->price_to, 'IDR');
				$r[] = number_to_currency($row->commission_1, 'IDR');
				$r[] = number_to_currency($row->commission_2, 'IDR');
				$r[] = number_to_currency($row->commission_3, 'IDR');
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
		$from = isset($_POST['from']) ? $this->request->getPost('from') : '';
		$to = isset($_POST['to']) ? $this->request->getPost('to') : '';
		$commission_1 = isset($_POST['commission_1']) ? $this->request->getPost('commission_1') : '';
		$commission_2 = isset($_POST['commission_2']) ? $this->request->getPost('commission_2') : '';
		$commission_3 = isset($_POST['commission_3']) ? $this->request->getPost('commission_3') : '';
		
		$data = [
			'price_from'	=> $from,
			'price_to' 		=> $to,
			'commission_1' 	=> $commission_1,
			'commission_2' 	=> $commission_2,
			'commission_3' 	=> $commission_3,
			'updated_at' 	=> date('Y-m-d H:i:s'),
			'updated_by' 	=> session()->get('username'),
		];
		
		$success = false;
		$message = 'No message';
		// cek apakah kode promo unique
		if(true) {
			// $data = [
			// 	'code' => $code,
			// 	'status' => $status,
			// ];
			$this->db->transStart();
			
			if ($id > 0) {

				
				$message = "Berhasil mengupdate Commission rate";
				$this->modelCommision->update($id, $data);

			} else {
				$data += [
					// 'created_at' => date('Y-m-d H:i:s'),
					'created_by' => session()->get('username'),
					'updated_at' => date('Y-m-d H:i:s'),
					'updated_by' => session()->get('username'),
				];
				$message = "Berhasil menambahkan Commission Rate";
				var_dump($data);
				// die;
				
				$this->modelCommision->insert($data);
				var_dump($this->db->getLastQuery());
				die;
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
		$this->modelCommision->update($id,$data);
		// die;
		if ($this->db->transStatus() === FALSE) {
			$message = $this->db->error();
			$this->db->transRollback();
		} else {
			$success = true;
			$message = "Berhasil menghapus Commission Rate";
			$this->db->transCommit();
		}
		$this->db->transComplete();
		if (is_array($message)) $message = "[" . implode("] ", $message);
		echo json_encode(array('success' => $success, 'message' => $message));
	}
}
