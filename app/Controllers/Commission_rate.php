<?php

namespace App\Controllers;

use App\Models\CommissionRate;

class Commission_rate extends BaseController
{
	protected $CommisionRate, $db, $table_name, $builder;
	public function __construct()
	{
		$this->CommissionRate = new CommissionRate();
		$this->db = \Config\Database::connect();
		$this->table_name = 'commission_rate';
		$this->builder = $this->db->table("$this->table_name as t");
		helper('rest_api');
		helper('role');
	}

	public function index()
	{
		//
		$check_role = checkRole($this->role, 'r_commission_rate');
		if (!$check_role->success) {
			return view('layouts/unauthorized', $this->data);
		} else {

			$this->data += [
				'page' => (object)[
					'key' => '2-commission_rate',
					'title' => 'Commision Rate',
					'subtitle' => 'Master',
					'navbar' => 'Commision Rate',
				],
				'status' => !empty($this->request->getPost('status')) ? (int)$this->request->getPost('status') : '',
			];
			helper('html');

			return view('commission_rate/index', $this->data);
		}
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		// ini_set('memory_limit', '-1');
		$req = $this->request;
		$check_role = checkRole($this->role, 'r_commission_rate');
		if (!$check_role->success) {
			$json_data = [
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			];
		} else {

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
				// $check_role = checkRole($this->role, 'r_commission_rate'); // belum diubah
				// if ($check_role->success) {
				$btn_disabled = '';
				$btn_hide = '';
				// }
				// }
				helper('format');
				helper('html');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					$attribute_data['default'] =  htmlSetData([
						'id'			=> $row->id,
						'price_from'	=> toPrice($row->price_from),
						'price_to'		=> toPrice($row->price_to),
					]);
					$attribute_data['commission'] =  htmlSetData([
						'commission_1'	=> toPrice($row->commission_1),
						'commission_2'	=> toPrice($row->commission_2),
						'commission_3'	=> toPrice($row->commission_3),
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> 'py-2 btnAction btnEdit ' . $btn_hide,
						'title'	=> 'Edit commision rate ' . toPrice($row->price_from) . ' to ' . toPrice($row->price_to),
						'data'	=> $attribute_data['default'] . $attribute_data['commission'],
						'icon'	=> 'fas fa-edit',
						'text'	=> 'Edit',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> 'py-2 btnAction btnDelete ' . $btn_hide,
						'title'	=> 'Delete commision rate ' . toPrice($row->price_from) . ' to ' . toPrice($row->price_to),
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-trash-o',
						'text'	=> 'Delete',
					];

					$action = htmlButton($btn['edit'], false);
					$action .= htmlButton($btn['delete']);

					$r = array();
					$r[] = $i;
					$r[] = $row->id;
					$r[] = toPrice($row->price_from);
					$r[] = toPrice($row->price_to);
					$r[] = toPrice($row->commission_1);
					$r[] = toPrice($row->commission_2);
					$r[] = toPrice($row->commission_3);
					$r[] = "$row->updated_at<br>$row->updated_by";
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

		echo json_encode($json_data);
	}

	public function save()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$role = $this->AdminRole->find(session()->role_id);
			$check_role = checkRole($role, 'r_commission_rate');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$from = $this->request->getPost('from') ?? '';
				$to = $this->request->getPost('to') ?? '';
				$commission_1 = $this->request->getPost('commission_1') ?? '';
				$commission_2 = $this->request->getPost('commission_2') ?? '';
				$commission_3 = $this->request->getPost('commission_3') ?? '';

				helper('format');
				$data = [
					'price_from'	=> removeComma($from),
					'price_to' 		=> removeComma($to),
					'commission_1' 	=> removeComma($commission_1),
					'commission_2' 	=> removeComma($commission_2),
					'commission_3' 	=> removeComma($commission_3),
					'updated_at' 	=> date('Y-m-d H:i:s'),
					'updated_by' 	=> session()->get('username'),
				];
				$hasError = false;
				$this->db->transStart();
				if ((int)$id > 0) {
					$commission_rate = $this->CommissionRate->getCommision(['id' => $id], 'id,price_from,price_to,commission_1,commission_2,commission_3,updated_at,updated_by');
					if (!$commission_rate) {
						$hasError = true;
						$response->message = "Commission Rate not found ($id)";
					} else {
						$response->message = "Commission Rate updated.";
						$this->CommissionRate->update((int)$id, $data);
						$data = ['new' => $data]; // for logs
						$data['old'] = $commission_rate; // for logs
						$log_cat = 18;
					}
				} else {
					$data += [
						'created_at' => date('Y-m-d H:i:s'),
						'created_by' => session()->get('username'),
						'updated_at' => date('Y-m-d H:i:s'),
						'updated_by' => session()->get('username'),
					];
					$response->message = "Commission Rate added.";
					$this->CommissionRate->insert($data);
					$log_cat = 17;
				}
				$this->db->transComplete();

				if ($this->db->transStatus() === FALSE || $hasError) {
					if (!$hasError) $response->message = "Failed. " . json_encode($this->db->error());
				} else {
					$response->success = true;
					$this->log->in(session()->username, $log_cat, json_encode($data));
				}
			}
		}

		return $this->respond($response, 200);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_commission_rate');
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$commission_rate = $this->CommissionRate->getCommision(['id' => $id], 'id,price_from,price_to,commission_1,commission_2,commission_3,updated_at,updated_by');
				if (!$commission_rate) {
					$response->message = "Commission Rate not found ($id)";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->get('username'),
					];
					$this->db->transStart();
					$this->CommissionRate->update($id, $data);
					$data += (array)$commission_rate; // for logs
					$this->db->transComplete();
					if ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$response->message = "Commission Rate deleted.";
						$log_cat = 19;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}
		return $this->respond($response, 200);
	}
}
