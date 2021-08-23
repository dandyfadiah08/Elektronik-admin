<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\CheckCode;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\MasterPrices;
use App\Models\MasterPromos;

class App_1 extends BaseController
{

    use ResponseTrait;

    protected $request,$MasterPromo,$MasterPrice,$DeviceCheck,$DeviceCheckDetail;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->MasterPromo = new MasterPromos();
        $this->MasterPrice = new MasterPrices();
        $this->DeviceCheck = new DeviceChecks();
        $this->DeviceCheckDetail = new DeviceCheckDetails();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('grade');
        helper('log');
        $this->text_invalid_promo_code = 'Promo Code is invalid.';
		$this->text_price_0 = '<b>Sorry, your device could not found a suitable price, here are some of the reason :</b><br><br>1. Internasional Model Category<br>2. This is a new product we could not accept yet';
		$this->text_price_1 = 'Please contact our customer service for further information';
    }
    
    public function get_version_app_1()
    {
        $response = initResponse('Success', true);
        $version = 1;
        $key = 'app_1:version';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if($version === FALSE) {
                // read from db, currently, hardcoded 
                $version = 1;
                $redis->set($key, $version);
            }
            $version = (int)$version;
        } catch(\Exception $e) {
            // $response->message = $e->getMessage();
            // read from db, currently, hardcoded 
            $version = 1;
            try {
                $redis = RedisConnect();
                $redis->set($key, $version);
            } catch(\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response, 200);
    }

    public function get_version_app_2()
    {
        $response = initResponse('Success', true);
        $version = 1;
        $key = 'app_2:version';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if($version === FALSE) {
                // read from db, currently, hardcoded 
                $version = 1;
                $redis->set($key, $version);
            }
            $version = (int)$version;
        } catch(\Exception $e) {
            // $response->message = $e->getMessage();
            // read from db, currently, hardcoded 
            $version = 1;
            try {
                $redis = RedisConnect();
                $redis->set($key, $version);
            } catch(\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response, 200);
    }

    public function get_price()
    {
        $response = initResponse();
        $response_code = 200;

        $brand = $this->request->getPost('brand') ?? '';
        $model = $this->request->getPost('model') ?? '';
        $storage = $this->request->getPost('storage') ?? '';
        $promo_codes = $this->request->getPost('promo_codes') ?? '';

        $promo_codes_valid = true;
        $rules = getValidationRules('app_1:get_price');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$now = date('Y-m-d');
			$select_promo = 'promo_id,quota,quota_type,initial_quota,quota_value,used_quota';
			$where = array('deleted_at' => null, 'date_format(end_date, "%Y-%m-%d") >= ' => $now, 'date_format(start_date, "%Y-%m-%d") <= ' => $now);
			$master_promo = $this->MasterPromo->getPromo($where, $select_promo, 'promo_id DESC');
            // var_dump($master_promo);die;

            // skip, belum diimplementasi (#belum)
			// if (!empty($promo_codes)) {
				// cari $master_promo untuk kode_promo tersebut
				// unset($where['kode_promo']);
				// $where += [
				// 	'pc.kode_promo' => $promo_codes,
				// 	'pc.deleted_at' => null,
				// 	'pc.aktif' => 1,
				// ];
				// $master_promo_new = $this->db->from('promo_codes pc')
				// 	->select($select_promo)
				// 	->join('mapping_kode_promo mkp', 'mpc.id_kode_promo=pc.id_kode_promo', 'left')
				// 	->join('mapping_mitra mm', 'mm.promo_id=mpc.promo_id', 'left')
				// 	->join('master_promo p', 'promo_id=mpc.promo_id', 'left')
				// 	->where($where)
				// 	->get()->row();

				// // cek jika promo ada
				// $promo_codes_valid = false;
				// if ($master_promo_new) {
				// 	// cek quota masih ada atau tidak
				// 	$master_price = $this->access->readtable('master_harga', $select_price, array('promo_id' => $master_promo_new->promo_id, 'lower(merk)' => $merk, 'lower(type)' => $tipe, 'lower(storage)' => $storage, 'deleted_at' => null))->row();
				// 	if (!empty($master_price)) {
				// 		// cek apakah menggunakan quota
				// 		if ($master_promo_new->quota > 0 && $master_price->quota > $master_price->kuota_terpakai) {
				// 			// quota masih ada, bisa pakai promo tersebut
				// 			$master_promo = $master_promo_new;
				// 			$promo_codes_valid = true;
				// 		} // jika kosong, akan dapat master_promo default
				// 	} // jika kosong, akan dapat master_promo default
				// }
			// }

			if (empty($master_promo)) {
				$response_code	= 404;
				$response->message = "Not Available";
				$response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>'.$this->text_price_0.'</center></div>'];
			} else {
				// get price
				$promo_id = $master_promo->promo_id;
                $select_price = 'price_id,promo_id,brand,model,storage,type,price_s,price_a,price_b,price_c,price_d,price_e,initial_quota,quota_value,used_quota';
                $where = ['promo_id' => $promo_id, 'brand' => $brand, 'model' => $model, 'storage' => $storage, 'deleted_at' => null];
				$master_price = $this->MasterPrice->getPrice($where, $select_price, 'price_id DESC');
    			// var_dump($master_price);die;
				
				if (!empty($master_price)) {
					// clone and unset other value than prices
					$price = clone($master_price);
					unset($price->price_id);
					unset($price->promo_id);
					unset($price->brand);
					unset($price->model);
					unset($price->storage);
					unset($price->type);
					unset($price->quota);
					unset($price->initial_quota);
					unset($price->quota_value);
					unset($price->used_quota);
					// mencari min value
					$range_end = (int)$master_price->price_e;
					foreach ($price as $value) {
						if((int)$value > $range_end) $range_end = (int)$value;
					}
					// mencari max value
					$range_start = $range_end;
					foreach ($price as $value) {
						if((int)$value < $range_start && $value > 0) $range_start = (int)$value;
					}
					$international_model = ((int)$master_price->price_s == 1
					&& (int)$master_price->price_a == 1
					&& (int)$master_price->price_b == 1
					&& (int)$master_price->price_c == 1
					&& (int)$master_price->price_d == 1
					&& (int)$master_price->price_e == 1
					) ? true : false;
					if(!$international_model) {
						$data = [
							'price_id'		=> $master_price->price_id,
							'promo_id'  	=> $master_price->promo_id,
							'brand'			=> $master_price->brand,
							'model'			=> $master_price->model,
							'storage'		=> $master_price->storage,
							'type'			=> $master_price->type,
							'grade_s'		=> $master_price->price_s,
							'grade_a'		=> $master_price->price_a,
							'grade_b'		=> $master_price->price_b,
							'grade_c'		=> $master_price->price_c,
							'grade_d'		=> $master_price->price_d,
							'grade_e'		=> $master_price->price_e,
							'grade_s_text'  => getGradeDefinition('s'),
							'grade_a_text'  => getGradeDefinition('a'),
							'grade_b_text'  => getGradeDefinition('b'),
							'grade_c_text'  => getGradeDefinition('c'),
							'grade_d_text'  => getGradeDefinition('d'),
							'grade_e_text'  => getGradeDefinition('e'),
							'range_start'	=> $range_start,
							'range_end'		=> $range_end,
							'kode_promo' 	=> $promo_codes,
                        ];
						ksort($data);
                        $response->data = $data;
						if ($promo_codes_valid) {
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';
						} else {
                            $response_code = 201;
                            $response->message = $this->text_invalid_promo_code;
						}
					} else {
                        $response_code	= 202;
                        $response->message = "International Model";
                        $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>'.$this->text_price_1.'</center></div>'];
					}
				} else {
                    $response_code	= 404;
                    $response->message = "Not Available";
                    $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>'.$this->text_price_0.'</center></div>'];
                    writeLog("api-app_1", "get_price\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
				}
			}
		}

        return $this->respond($response, $response_code);
    }

    public function software_check()
    {
        $response = initResponse();
        $response_code = 200;

        $price_id = $this->request->getPost('price_id') ?? '';
        $promo_codes = $this->request->getPost('promo_codes') ?? '';
        $fcm_token = $this->request->getPost('fcm_token') ?? '';
        $imei = $this->request->getPost('imei') ?? '';
        $os = $this->request->getPost('os') ?? '';
        $simcard = $this->request->getPost('simcard') ?? '';
        $cpu = $this->request->getPost('cpu') ?? '';
        $harddisk = $this->request->getPost('harddisk') ?? '';
        $battery = $this->request->getPost('battery') ?? '';
        $root = $this->request->getPost('root') ?? '';
        $button_back = $this->request->getPost('button_back') ?? '';
        $button_volume = $this->request->getPost('button_volume') ?? '';
        $button_power = $this->request->getPost('button_power') ?? '';
        $camera_back = $this->request->getPost('camera_back') ?? '';
        $camera_front = $this->request->getPost('camera_front') ?? '';
        $screen = $this->request->getPost('screen') ?? '';
        $cpu_detail = $this->request->getPost('cpu_detail') ?? '';
        $harddisk_detail = $this->request->getPost('harddisk_detail') ?? '';
        $battery_detail = $this->request->getPost('battery_detail') ?? '';
        $root_detail = $this->request->getPost('root_detail') ?? '';

        $rules = getValidationRules('app_1:software_check');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
            $select_price = 'price_id,promo_id,brand,model,storage,type,price_e';
            $where = ['price_id' => $price_id, 'deleted_at' => null];
            $master_price = $this->MasterPrice->getPrice($where, $select_price, 'price_id DESC');
            // var_dump($master_price);die;
            if (empty($master_price)) {
                $response_code	= 400;
                $response->message = "Invalid price_id $price_id";
                writeLog("api-app_1", "software_chek\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
            } else {
                // insert data to device_checks
                $now = date('Y-m-d H:i:s');
                $data = [
                    'price_id'		=> $master_price->price_id,
                    'promo_id'  	=> $master_price->promo_id,
                    'brand'			=> $master_price->brand,
                    'model'			=> $master_price->model,
                    'storage'	    => $master_price->storage,
                    'type'			=> $master_price->type,
                    'price'			=> $master_price->price_e,
                    'grade'			=> getGrade(),
                    'fcm_token'     => $fcm_token,
                    'os'            => $os,
                    'imei'          => $imei,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
                // var_dump($data);die;
                $this->DeviceCheck->insert($data);
                $check_id = $this->DeviceCheck->getInsertID();
                if($check_id < 1) {
                    $response_code = 202;
                    $response->message = 'Insert failed. ';
                } else {
                    // insert data to device_check_details
                    $data_detail = [
                        'check_id'		=> $check_id,
                        'simcard'       => $simcard,
                        'cpu'           => $cpu,
                        'harddisk'      => $harddisk,
                        'battery'       => $battery,
                        'root'          => $root,
                        'button_back'   => $button_back,
                        'button_volume' => $button_volume,
                        'button_power'  => $button_power,
                        'camera_back'   => $camera_back,
                        'camera_front'  => $camera_front,
                        'screen'        => $screen,
                        'cpu_detail'        => $cpu_detail,
                        'harddisk_detail'   => $harddisk_detail,
                        'battery_detail'    => $battery_detail,
                        'root_detail'       => $root_detail,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                    $this->DeviceCheckDetail->insert($data_detail);
                    $check_detail_id = $this->DeviceCheckDetail->getInsertID();
                    if($check_detail_id < 1) {
                        $response_code = 202;
                        $response->message = 'Insert detail failed. ';
                    } else {
                        // create and update check_code
                        $this->CheckCode = new CheckCode();
                        $check_code = false;
                        while(!$check_code) {
                            $key_code = $this->CheckCode->makeKey();
                            $check_code = $this->CheckCode->make($check_id, $key_code);
                        }
                        $data_update = [
                            'key_code'      => $key_code,
                            'check_code'    => $check_code,
                        ];
                        $this->DeviceCheck->update($check_id, $data_update);

                        // add more data to response
                        $data += $data_update;
                        $data += $data_detail;
                        $data += ['check_detail_id' => $check_detail_id];

                        ksort($data);
                        $response->data = $data;
                        $response_code = 200;
                        $response->success = true;
                        $response->message = 'OK';
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

}
