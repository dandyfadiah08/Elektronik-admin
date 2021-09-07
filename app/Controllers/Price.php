<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;
use App\Models\MasterPromos;
use App\Models\MasterPrices;

class Price extends BaseController
{
	use ResponseTrait;
	protected $Admin, $AdminRole, $Appointment, $MasterPromo, $MasterPrice;

	public function __construct()
	{
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->MasterPromo = new MasterPromos();
		$this->MasterPrice = new MasterPrices();
		$this->db = \Config\Database::connect();
		helper('validation');
	}

	public function index($promo_id = 0)
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		// if($)
		helper('html');
		helper('general_status');

		$data = [
			'page' => (object)[
				'key' => '2-promo',
				'title' => 'Price',
				'subtitle' => 'Promo Name',
				'navbar' => 'Price',
			],
			'admin' => $this->Admin->find(session()->admin_id),
			'role' => $this->AdminRole->find(session()->role_id),
		];
		$select = 'promo_name,promo_id,start_date,end_date,status';
		$where = array('promo_id' => $promo_id, 'deleted_at' => null);
		$promo = $this->MasterPromo->getPromo($where, $select);
		if(!$promo) {
			$data += ['url' => base_url().'price/'.$promo_id];
			return view('layouts/not_found', $data);
		}
		if($promo_id < 1) return view('layouts/unauthorized', $data);
		$data += ['p' => $promo];

		return view('price/index', $data);
	}

	function load_data()
	{
		if (!session()->has('admin_id')) return redirect()->to(base_url());
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$role = $this->AdminRole->find(session()->role_id);
		$check_role = checkRole($role, 'r_admin');
		$check_role->success = true; // sementara belum ada role
		$id = $this->request->getVar('id') ?? 0;
		if (!$check_role->success || $id < 1) {
			$json_data = array(
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			);
		} else {
			$this->table_name = 'master_prices';
			$this->builder = $this->db
				->table("$this->table_name as t");
				// ->join("device_check_details as t1", "t1.check_id=t.check_id", "left")

			// fields order 0, 1, 2, ...
			$fields_order = array(
				null,
				"brand",
				"model",
				"storage",
				"type",
				"price_s",
				"price_fullset",
				"updated_at",
			);
			// fields to search with
			$fields_search = array(
				"brand",
				"model",
				"storage",
				"type",
				"price_s",
				"price_fullset",
				"updated_at",
				"updated_by",
			);
			// select fields
			$select_fields = 'price_id,brand,model,type,storage,price_s,price_a,price_b,price_c,price_d,price_e,price_fullset,updated_at,updated_by';

			// building where query
			$where = [
				't.deleted_at' => null,
				't.promo_id' => $id,
			];

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
				$check_role = checkRole($role, 'r_admin'); // belum diubah
				$btn_hide = ' d-none';
				if ($check_role->success) {
					$btn_hide = '';
				}
				helper('html');
				helper('format');
				helper('general_status');
				// looping through data result
				foreach ($dataResult as $row) {
					$i++;

					// $attribute_data['default'] = 'data-check_code="'.$row->check_code.'" data-check_id="'.$row->check_id.'" ';
					$attribute_data['default'] =  htmlSetData([
						'id' => $row->price_id, 
						'brand' => $row->brand,
						'model' => $row->model,
						'storage' => $row->storage,
						'type' => $row->type
					]);
					$attribute_data['price'] =  htmlSetData([
						'price_s' => toPrice($row->price_s),
						'price_a' => toPrice($row->price_a),
						'price_b' => toPrice($row->price_b),
						'price_c' => toPrice($row->price_c),
						'price_d' => toPrice($row->price_d),
						'price_e' => toPrice($row->price_e),
						'price_fullset' => toPrice($row->price_fullset),
					]);
					$btn['edit'] = [
						'color'	=> 'warning',
						'class'	=> "py-2 btnAction btnEdit",
						'data'	=> $attribute_data['default'].$attribute_data['price'],
						'icon'	=> 'fas fa-eye',
						'text'	=> 'View',
					];
					$btn['delete'] = [
						'color'	=> 'danger',
						'class'	=> "py-2 btnAction btnDelete $btn_hide",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$action = htmlButton($btn['edit'], false);
					$action .= htmlButton($btn['delete']);

					$r = [];
					$r[] = $i;
					$r[] = $row->brand;
					$r[] = $row->model;
					$r[] = $row->storage;
					$r[] = $row->type;
					$r[] = toPrice($row->price_s);
					$r[] = toPrice($row->price_fullset);
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
			$role = $this->AdminRole->find(session()->role_id);
			$check_role = checkRole($role, 'r_admin'); // belum diubah
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				helper('format');
				$rules = getValidationRules('price:save');
				if (!$this->validate($rules)) {
					$errors = $this->validator->getErrors();
					$response->message = "";
					foreach ($errors as $error) $response->message .= "$error ";
				} else {
					$promo_id = $this->request->getPost('promo_id') ?? 0;
					$id = $this->request->getPost('id') ?? 0;
					$brand = $this->request->getPost('brand') ?? '';
					$model = $this->request->getPost('model') ?? '';
					$storage = $this->request->getPost('storage') ?? '';
					$type = $this->request->getPost('type') ?? '';
					$price_s = $this->request->getPost('price_s') ?? '';
					$price_a = $this->request->getPost('price_a') ?? '';
					$price_b = $this->request->getPost('price_b') ?? '';
					$price_c = $this->request->getPost('price_c') ?? '';
					$price_d = $this->request->getPost('price_d') ?? '';
					$price_e = $this->request->getPost('price_e') ?? '';
					$price_fullset = $this->request->getPost('price_fullset') ?? '';
					$data_default = [
						'brand' 		=> $brand,
						'model' 		=> $model,
						'storage' 		=> $storage,
					];
					$data = [
						'type' 			=> $type,
						'price_s' 		=> removeComma($price_s),
						'price_a' 		=> removeComma($price_a),
						'price_b' 		=> removeComma($price_b),
						'price_c' 		=> removeComma($price_c),
						'price_d' 		=> removeComma($price_d),
						'price_e' 		=> removeComma($price_e),
						'price_fullset'	=> removeComma($price_fullset),
						'updated_at' 	=> date('Y-m-d H:i:s'),
						'updated_by' 	=> session()->get('username'),
					];

					$hasError = false;
					$this->db->transStart();
					if ((int)$id > 0) {
						$price = $this->MasterPrice->getPrice(['price_id' => $id], 'price_id,brand,model,type,storage,price_s,price_a,price_b,price_c,price_d,price_e,price_fullset,updated_at,updated_by');
						$data += $data_default;
						$response->message = "Price updated.";
						$this->MasterPrice->update((int)$id, $data);
						$data = ['new' => $data]; // for logs
						if($price) $data['old'] = $price; // for logs
						$log_cat = 2;
					} else {
						$price = $this->MasterPrice->getPrice($data_default, 'type');
						if($price) {
							$response->message = "Price is exist.";
							$response->data = $price->type;
							$hasError = true;
						} else {
							$data += $data_default;
							$data += [
								'promo_id'		=> $promo_id,
								'created_at'	=> date('Y-m-d H:i:s'),
								'created_by'	=> session()->get('username'),
								'updated_at'	=> date('Y-m-d H:i:s'),
								'updated_by'	=> session()->get('username'),
							];
							$response->message = "Price added.";
							$this->MasterPrice->insert($data);
							$log_cat = 1;
						}
					}
					$this->db->transComplete();

					if ($hasError) {
					} elseif ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}

		return $this->respond($response, 200);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$role = $this->AdminRole->find(session()->role_id);
			$check_role = checkRole($role, 'r_admin'); // belum diubah
			if (!$check_role->success) {
				$response->message = $check_role->message;
			} else {
				$id = $this->request->getPost('id') ?? 0;
				$price = $this->MasterPrice->getPrice(['price_id' => $id], 'price_id,brand,model,type,storage,price_s,price_a,price_b,price_c,price_d,price_e,price_fullset,updated_at,updated_by');
				if(!$price) {
					$response->message = "Price not valid ($id)";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->username,
					];
					$this->db->transStart();
					$this->MasterPrice->update($id, $data);
					$data += (array)$price; // for logs
					$this->db->transComplete();
					if ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$response->message = "Price deleted.";
						$log_cat = 3;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}
		return $this->respond($response, 200);
	}

}
