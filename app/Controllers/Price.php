<?php

namespace App\Controllers;

use App\Models\MasterPromos;
use App\Models\MasterPrices;

class Price extends BaseController
{
	protected $MasterPromo, $MasterPrice;

	public function __construct()
	{
		$this->MasterPromo = new MasterPromos();
		$this->MasterPrice = new MasterPrices();
		$this->db = \Config\Database::connect();
		helper('validation');
		$this->table_name = 'master_prices';
	}

	public function index($promo_id = 0)
	{
		$check_role = checkRole($this->role, ['r_price', 'r_price_view']);
		if (!$check_role->success) {
			return view('layouts/unauthorized', ['role' => $this->role]);
		} else {
			helper('html');
			helper('general_status');

			$this->data += [
				'page' => (object)[
					'key' => '2-promo',
					'title' => 'Price',
					'subtitle' => 'Promo Name',
					'navbar' => 'Price',
				],
			];
			$select = 'promo_name,promo_id,start_date,end_date,status';
			$where = array('promo_id' => $promo_id, 'deleted_at' => null);
			$promo = $this->MasterPromo->getPromo($where, $select);
			if(!$promo) {
				$this->data += ['url' => base_url().'price/'.$promo_id];
				return view('layouts/not_found', $this->data);
			}
			if($promo_id < 1) return view('layouts/unauthorized', $this->data);
			$this->data += ['p' => $promo];

			return view('price/index', $this->data);
		}
	}

	function load_data()
	{
		ini_set('memory_limit', '-1');
		$req = $this->request;
		$role = $this->AdminRole->find(session()->role_id);
		$check_role = checkRole($role, ['r_price', 'r_price_view']);
		$id = $this->request->getVar('id') ?? 0;
		if (!$check_role->success || $id < 1) {
			$json_data = [
				"draw"            => intval($req->getVar('draw')),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => 0,  // total number of records
				"recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => []   // total data array
			];
		} else {
			$this->builder = $this->db
			->table("$this->table_name as t");

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
				$access['delete'] = hasAccess($this->role, 'r_price');
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
						'class'	=> "py-2 btnAction btnDelete",
						'data'	=> $attribute_data['default'],
						'icon'	=> 'fas fa-trash',
						'text'	=> 'Delete',
					];
					$action = htmlButton($btn['edit'], false);
					$action .= ($access['delete'] ? htmlButton($btn['delete']) : '');

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

		return $this->respond($json_data);
	}

	public function save()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_price');
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

		return $this->respond($response);
	}

	public function delete()
	{
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			$check_role = checkRole($this->role, 'r_price');
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
		return $this->respond($response);
	}

	public function delete_all($promo_id = 0)
	{
		$response = initResponse('Unauthorized.');
		if (hasAccess($this->role, 'r_price')) {
			if($promo_id == 0) {
				$response->message = "Invalid Promo ID.";
			} else {
				$select = 'promo_name,promo_id';
				$where = array('promo_id' => $promo_id, 'deleted_at' => null);
				$promo = $this->MasterPromo->getPromo($where, $select);
				if(!$promo) {
					$response->message = "Promo is not found ($promo_id)";
				} else {
					$data = [
						'deleted_at'	=> date('Y-m-d H:i:s'),
						'deleted_by'	=> session()->username,
					];
					$this->db->transStart();
					$this->MasterPrice->where([
						'promo_id' => $promo_id,
						'deleted_at' => null
					])
					->set($data)
					->update();
					$data += (array)$promo; // for logs
					$data['changes'] = $this->db->affectedRows();
					$this->db->transComplete();
					if ($this->db->transStatus() === FALSE) {
						$response->message = "Failed. " . json_encode($this->db->error());
					} else {
						$response->success = true;
						$response->message = "Price deleted.";
						$log_cat = 21;
						$this->log->in(session()->username, $log_cat, json_encode($data));
					}
				}
			}
		}
		return $this->respond($response);
	}

	public function import($promo_id = 0) {
		$response = initResponse('Unathorized.');
		if (hasAccess($this->role, 'r_price')) {
			if($promo_id == 0) {
				$response->message = "Invalid Promo ID.";
			} else {
				$select = 'promo_name,promo_id';
				$where = array('promo_id' => $promo_id, 'deleted_at' => null);
				$promo = $this->MasterPromo->getPromo($where, $select);
				if(!$promo) {
					$response->message = "Promo is not found ($promo_id)";
				} else {
					$csv_separator = $this->request->getPost('csv_separator') ?? ';';
					$file_import = $this->request->getFile('file_import');
					$filename = $file_import->getRandomName();
					$path = 'uploads/price/';
					try {
						$file_real_name = $file_import->getName();
						if ($file_import->move($path, $filename)) {
							$file = fopen('../public/'.$path.$filename, "r");
							if(!$file) {
								$response->message = "Unable to open file $filename";
							} else {
								// lloping trough rows
								$no = 0;
								$data_insert = [];
								$data_update = [];
								$insert_count = 0;
								$update_count = 0;
								$row = fgetcsv($file, 0, $csv_separator); // remove 1st line
								while (($row = fgetcsv($file, 0, $csv_separator)) !== FALSE) {
									$no++;
									$basic_data = [
										'promo_id'		=> $promo_id,
										'brand'			=> $row[0], // A
										'model'			=> $row[1], // B
										'storage'		=> $row[2], // C, dst.
									];
									$where = ['deleted_at' => null];
									$where += $basic_data;
									$price = $this->MasterPrice->getPrice($where, 'price_id');
									$basic_data += [
										'updated_at'	=> date('Y-m-d H:i:s'),
										'updated_by'	=> session()->username,
										'type'			=> $row[3],
										'price_s'		=> $row[4],
										'price_a'		=> $row[5],
										'price_b'		=> $row[6],
										'price_c'		=> $row[7],
										'price_d'		=> $row[8],
										'price_e'		=> $row[9],
									];
									if($price) {
										// update
										$basic_data += ['price_id' => $price->price_id];
										$data_update[] = $basic_data;
									} else {
										// insert
										$data_insert[] = $basic_data;
									}
								}
								fclose($file);
								// var_dump($data);die;

								// insert batch to db
								$this->db->transStart();
								$this->builder = $this->db
								->table("$this->table_name");
								if(count($data_insert) > 0) $insert_count = $this->builder->insertBatch($data_insert);
								if(count($data_update) > 0) $update_count = $this->builder->updateBatch($data_update, 'price_id');
								$this->db->transComplete();

								if ($this->db->transStatus() === FALSE) {
									$response->message = "Failed. " . json_encode($this->db->error());
								} else {
									$file_data = [
										'uploaded_at' => date('Y-m-d H:i:s'),
										'file_name' => $file_real_name,
										'file_name_server' => $filename,
										'count' => $no,
										'insert_count' => $insert_count,
										'update_count' => $update_count,
									];

									$response->success = true;
									$response->message = "Successfully import $no prices to $promo->promo_name ($insert_count insert(s), $update_count update(s))";
									$log_cat = 20;
									$data_log['promo'] = $promo;
									$data_log['file'] = $file_data;
									$this->log->in(session()->username, $log_cat, json_encode($data_log));
								}
							}
						} else {
							$response->message = "Error upload file";
						}
					} catch(\Exception $ex) {
						$response->message = "Error upload file ".$ex->getMessage();
					}
				}
			}
		}
		return $this->respond($response);
	}

}
