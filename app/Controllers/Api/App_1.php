<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Libraries\CheckCode;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\MasterPrices;
use App\Models\MasterPromos;
use App\Libraries\Token;
use App\Libraries\Nodejs;
use App\Models\Settings;
use App\Models\NotificationQueues;
use Firebase\JWT\JWT;
use CodeIgniter\I18n\Time;
use DateTime;

class App_1 extends BaseController
{

    use ResponseTrait;

    protected $MasterPromo, $MasterPrice, $DeviceCheck, $DeviceCheckDetail, $Setting, $NotificationQueue;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->MasterPromo = new MasterPromos();
        $this->MasterPrice = new MasterPrices();
        $this->DeviceCheck = new DeviceChecks();
        $this->DeviceCheckDetail = new DeviceCheckDetails();
        $this->Setting = new Settings();
        $this->NotificationQueue = new NotificationQueues();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('grade');
        helper('log');
        $this->text_invalid_promo_code = 'Promo Code is invalid.';
        $this->text_price_0 = '<b>Sorry, your device could not found a suitable price, here are some of the reason :</b><br><br>1. Internasional Model Category<br>2. This is a new product we could not accept yet';
        $this->text_price_1 = 'Please contact our customer service for further information';
        $this->text_price_valid = 'Currently available in DKI Jakarta, Bogor, Depok, Tangerang & Bekasi only.';
    }

    // nanti tolong dipindah di api/general 
    public function get_version_app_1()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:app_1_version';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {

                $setting_db = $this->Setting->getSetting(['_key' => 'version_app1'], 'setting_id,val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();

            $setting_db = $this->Setting->getSetting(['_key' => 'version_app1'], 'setting_id,val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->setex($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response, 200);
    }

    // nanti tolong dipindah di api/general 
    public function get_version_app_2()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:app_2_version';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {

                $setting_db = $this->Setting->getSetting(['_key' => 'version_app2'], 'setting_id,val');
                $version = $setting_db->val;
                $redis->setex($key, 3600, $version);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();

            $setting_db = $this->Setting->getSetting(['_key' => 'version_app2'], 'setting_id,val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->set($key, 3600, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
        $response->data = ['version' => $version];
        return $this->respond($response, 200);
    }

    // nanti tolong dipindah di api/general 
    public function get_version_app_1_ios()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app1_ios';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {

                $setting_db = $this->Setting->getSetting(['_key' => 'version_app1_ios'], 'setting_id,val');
                $version = $setting_db->val;
                $redis->set($key, $version);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();

            $setting_db = $this->Setting->getSetting(['_key' => 'version_app1_ios'], 'setting_id,val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->set($key, $version);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['version' => $version];
        return $this->respond($response, 200);
    }

    // nanti tolong dipindah di api/general 
    public function get_version_app_2_ios()
    {
        $response = initResponse('Success', true);
        $version = "1";
        $key = 'setting:version_app2_ios';
        try {
            $redis = RedisConnect();
            $version = $redis->get($key);
            if ($version === FALSE) {

                $setting_db = $this->Setting->getSetting(['_key' => 'version_app2_ios'], 'setting_id,val');
                $version = $setting_db->val;
                $redis->set($key, $version);
            }
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();

            $setting_db = $this->Setting->getSetting(['_key' => 'version_app2_ios'], 'setting_id,val');
            $version = $setting_db->val;
            try {
                $redis = RedisConnect();
                $redis->set($key, $version);
            } catch (\Exception $e) {
            }
        }
        $version = (int)$version;
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
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $now = date('Y-m-d');
            $select_promo = 'promo_id,quota,quota_type,initial_quota,quota_value,used_quota';
            $where = ['status' => 1, 'deleted_at' => null, 'date_format(end_date, "%Y-%m-%d") >= ' => $now, 'date_format(start_date, "%Y-%m-%d") <= ' => $now];
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
                $response_code    = 404;
                $response->message = "Not Available";
                $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>' . $this->text_price_0 . '</center></div>'];
            } else {
                // get price
                $promo_id = $master_promo->promo_id;
                $select_price = 'price_id,promo_id,brand,model,storage,type,price_s,price_a,price_b,price_c,price_d,price_e,initial_quota,quota_value,used_quota';
                $where = ['promo_id' => $promo_id, 'brand' => $brand, 'model' => $model, 'storage' => $storage, 'deleted_at' => null];
                $master_price = $this->MasterPrice->getPrice($where, $select_price, 'price_id DESC');
                // var_dump($master_price);die;

                if (!empty($master_price)) {
                    // clone and unset other value than prices
                    $price = clone ($master_price);
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
                        if ((int)$value > $range_end) $range_end = (int)$value;
                    }
                    // mencari max value
                    $range_start = $range_end;
                    foreach ($price as $value) {
                        if ((int)$value < $range_start && $value > 0) $range_start = (int)$value;
                    }
                    $international_model = ((int)$master_price->price_s == 1
                        && (int)$master_price->price_a == 1
                        && (int)$master_price->price_b == 1
                        && (int)$master_price->price_c == 1
                        && (int)$master_price->price_d == 1
                        && (int)$master_price->price_e == 1) ? true : false;
                    if (!$international_model) {
                        $data = [
                            'price_id'        => $master_price->price_id,
                            'promo_id'      => $master_price->promo_id,
                            'brand'            => $master_price->brand,
                            'model'            => $master_price->model,
                            'storage'        => $master_price->storage,
                            'type'            => $master_price->type,
                            'grade_s'        => $master_price->price_s,
                            'grade_a'        => $master_price->price_a,
                            'grade_b'        => $master_price->price_b,
                            'grade_c'        => $master_price->price_c,
                            'grade_d'        => $master_price->price_d,
                            'grade_e'        => $master_price->price_e,
                            'grade_s_text'  => getGradeDefinition('s'),
                            'grade_a_text'  => getGradeDefinition('a'),
                            'grade_b_text'  => getGradeDefinition('b'),
                            'grade_c_text'  => getGradeDefinition('c'),
                            'grade_d_text'  => getGradeDefinition('d'),
                            'grade_e_text'  => getGradeDefinition('e'),
                            'range_start'    => $range_start,
                            'range_end'        => $range_end,
                            'kode_promo'     => $promo_codes,
                        ];
                        ksort($data);
                        $response->data = $data;
                        if ($promo_codes_valid) {
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';
                            $response->data += ['info_warning' => empty($this->text_price_valid) ? '' : '<div style="text-align:center;padding:10px;color:#06E606;background-color:#D9FFD9;"><center>' . $this->text_price_valid . '</center></div>'];
                        } else {
                            $response_code = 201;
                            $response->message = $this->text_invalid_promo_code;
                            $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>' . $this->text_invalid_promo_code . '</center></div>'];
                        }
                    } else {
                        $response_code    = 202;
                        $response->message = "International Model";
                        $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>' . $this->text_price_1 . '</center></div>'];
                    }
                } else {
                    $response_code    = 404;
                    $response->message = "Not Available";
                    $response->data = ['info_warning' => '<div style="text-align:center;padding:10px;color:#E60606;background-color:#FFD9D9;"><center>' . $this->text_price_0 . '</center></div>'];
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
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $select_price = 'price_id,promo_id,brand,model,storage,type';
            $where = ['price_id' => $price_id, 'deleted_at' => null];
            $master_price = $this->MasterPrice->getPrice($where, $select_price, 'price_id DESC');
            // var_dump($master_price);die;
            if (empty($master_price)) {
                $response_code    = 400;
                $response->message = "Invalid price_id $price_id";
            } else {
                // insert data to device_checks
                $now = date('Y-m-d H:i:s');
                $data = [
                    'price_id'        => $master_price->price_id,
                    'promo_id'      => $master_price->promo_id,
                    'brand'            => $master_price->brand,
                    'model'            => $master_price->model,
                    'storage'        => $master_price->storage,
                    'type'            => $master_price->type,
                    'fcm_token'     => $fcm_token,
                    'os'            => $os,
                    'imei'          => $imei,
                    'price'         => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
                // var_dump($data);die;
                $this->DeviceCheck->insert($data);
                $check_id = $this->DeviceCheck->getInsertID();
                if ($check_id < 1) {
                    $response_code = 202;
                    $response->message = 'Insert failed. ';
                } else {
                    // insert data to device_check_details
                    $data_detail = [
                        'check_id'      => $check_id,
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
                        'token'         => Token::createApp1Token($check_id),
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                    $this->DeviceCheckDetail->insert($data_detail);
                    $check_detail_id = $this->DeviceCheckDetail->getInsertID();
                    if ($check_detail_id < 1) {
                        $response_code = 202;
                        $response->message = 'Insert detail failed. ';
                    } else {
                        // create and update check_code
                        $this->CheckCode = new CheckCode();
                        $check_code = false;
                        $key_code = '';
                        while (!$check_code) {
                            // $key_code = $this->CheckCode->makeKey();
                            // $check_code = $this->CheckCode->make($check_id, $key_code);
                            $check_code = $this->CheckCode->make();
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
                        unset($data_detail['token']);
                        $data_log = $data_detail;
                        $data_log += [
                            'brand'     => $master_price->brand,
                            'model'     => $master_price->model,
                            'storage'   => $master_price->storage,
                            'type'      => $master_price->type,
                        ];
                        $this->log->in($check_code, 46, json_encode($data_log), false, false, $check_id);
                        $this->log->in($check_code, 36, json_encode($data_log), false, false, $check_id); // status_internal
                    }
                }
            }
        }
        writeLog("api-app_1", "software_chek\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function save_identity()
    {
        $response = initResponse();
        $response_code = 200;

        $token = $this->request->getPost('token') ?? '';
        $customer_name = $this->request->getPost('customer_name') ?? '';
        $customer_phone = $this->request->getPost('customer_phone') ?? '';
        $customer_email = $this->request->getPost('customer_email') ?? '';

        $rules = getValidationRules('app_1:save_identity');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            try {
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                if ($decoded) {
                    $check_id = $decoded->data->check_id;

                    $select = 'check_id,check_code,user_id';
                    $where = array('check_id' => $check_id, 'status' => 5, 'deleted_at' => null);
                    $device_check = $this->DeviceCheck->getDevice($where, $select);

                    if (!$device_check) {
                        $response->message = "Invalid check_id ($check_id). ";
                    } else {
                        $update_data = ['status' => 6];

                        $update_data_detail = [
                            'customer_name'     => $customer_name,
                            'customer_phone'    => $customer_phone,
                            'customer_email'    => $customer_email,
                        ];

                        // update records
                        $this->DeviceCheck->update($device_check->check_id, $update_data);
                        $this->DeviceCheckDetail->where(['check_id' => $device_check->check_id])
                            ->set($update_data_detail)
                            ->update();

                        // building responses
                        $response_data = $update_data;
                        $response_data += $update_data_detail;
                        ksort($response_data);
                        $response->data = $response_data;
                        $response_code = 200;
                        $response->success = true;
                        $response->message = 'OK';

                        $this->log->in($device_check->check_code, 39, json_encode($response_data), false, $device_check->user_id, $device_check->check_id);
                    }
                } else {
                    $response->message = "Invalid token. ";
                }
            } catch (\Exception $e) {
                $response->message = $e->getMessage();
                if ($response->message == 'Expired token') $response_code = 401;
            }
        }
        writeLog("api-app_1", "save_identity\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function save_photo_id()
    {
        $response = initResponse();
        $response_code = 200;

        $token = $this->request->getPost('token') ?? '';

        $rules = getValidationRules('app_1:save_photo_id');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            try {
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                if ($decoded) {
                    $check_id = $decoded->data->check_id;

                    $select = 'check_id,fcm_token,check_code,user_id';
                    $where = array('check_id' => $check_id, 'status' => 6, 'deleted_at' => null);
                    $device_check = $this->DeviceCheck->getDevice($where, $select);

                    if (!$device_check) {
                        $response_code    = 404;
                        $response->message = "Invalid check_id ($check_id). ";
                    } else {
                        $update_data = [
                            'status'            => 7,
                            'status_internal'   => 2, // ready for appointment
                        ];

                        $hasError = false;
                        $tempMessage = "";
                        $this->lockTime = env('app1.lock_1'); // in days
                        $now = new Time('now');
                        $lockUntilDate = new Time('+' . $this->lockTime . ' days');
                        $update_data_detail = [
                            'finished_date' => $now->toDateTimeString(), // or $now->toLocalizedString('Y-MM-dd HH:mm:ss')
                            'lock_until_date'  => $lockUntilDate->toDateTimeString(),
                        ];
                        // $update_data_detail = [];

                        // uploads photo_id
                        $photo_id = $this->request->getFile('photo_id');
                        $newName = $photo_id->getRandomName();
                        if ($photo_id->move('uploads/photo_id/', $newName)) {
                            $update_data_detail += [
                                "photo_id" => $newName,
                            ];
                        } else {
                            $tempMessage .= "Error upload file";
                            $hasError = true;
                        }

                        if ($hasError) {
                            // has any error
                            $response_code = 400;
                            $response->message = $tempMessage;
                        } else {
                            // update records
                            $this->DeviceCheck->update($device_check->check_id, $update_data);
                            $this->DeviceCheckDetail->where(['check_id' => $device_check->check_id])
                                ->set($update_data_detail)
                                ->update();

                            // building responses
                            $response_data = $update_data;
                            $response_data += $update_data_detail;
                            ksort($response_data);
                            $response->data = $response_data;
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';

                            unset($response_data['photo_id']);
                            $this->log->in($device_check->check_code, 48, json_encode($response_data), false, $device_check->user_id, $device_check->check_id);
                            $this->log->in($device_check->check_code, 35, json_encode($response_data), false, $device_check->user_id, $device_check->check_id); // status_internal


                            // add notification queue
                            // notifikasi D+1, D+2, D+3 H-1 (hari ke 3 kurang 1 jam)
                            try {
                                $queue = [
                                    'token'         => $device_check->fcm_token,
                                    'token_type'    => 'fcm',
                                    'created_at'    => date('Y-m-d H:i:s'),
                                ];
                                $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+1 day"));
                                $queue['data'] = json_encode([
                                    'type'      => 'appointment_reminder_1',
                                    'check_id'  => $device_check->check_id
                                ]);
                                $this->NotificationQueue->insert($queue);
                                $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+2 day"));
                                $queue['data'] = json_encode([
                                    'type'      => 'appointment_reminder_2',
                                    'check_id'  => $device_check->check_id
                                ]);
                                $this->NotificationQueue->insert($queue);
                                $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+3 day", strtotime("-1 hour")));
                                $queue['data'] = json_encode([
                                    'type'      => 'appointment_reminder_3',
                                    'check_id'  => $device_check->check_id
                                ]);
                                $this->NotificationQueue->insert($queue);
                            } catch (\Exception $e) {
                                writeLog("api-notification_queue", "submitAppointment\n" . json_encode($this->request->getPost()) . "\n" . $e->getMessage());
                            }
                        }
                    }
                } else {
                    $response->message = "Invalid token. ";
                }
            } catch (\Exception $e) {
                $response->message = $e->getMessage();
                if ($response->message == 'Expired token') $response_code = 401;
            }
        }
        writeLog("api-app_1", "save_photo_id\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function cancel()
    {
        $response = initResponse();
        $response_code = 200;

        $token = $this->request->getPost('token') ?? '';

        $rules = getValidationRules('app_1:cancel');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            try {
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                if ($decoded) {
                    $check_id = $decoded->data->check_id;

                    $select = 'dc.check_id,check_code,dc.user_id,check_detail_id,status_internal';
                    $where = ['dc.check_id' => $check_id, 'status' => 7, 'dc.deleted_at' => null];
                    $device_check = $this->DeviceCheck->getDeviceDetail($where, $select);

                    if (!$device_check) {
                        $response->message = "Invalid check_id ($check_id). ";
                    } else {
                        $data = [];
                        $send_notif = false;
                        if ($device_check->status_internal == 3) {
                            // jika appointment belum dikonfirmasi
                            $update_data = ['status_internal' => 7];
                            $data_device_check_detail = ['general_notes' => 'Cancelled by user (before appointment confirmed)'];
                            $this->DeviceCheckDetail->update($device_check->check_detail_id, $data_device_check_detail);
                            $data = $data_device_check_detail;
                        } else {
                            // jika sudah dikonfirmasi maka akan update ke 9 (request cancel)
                            $update_data = ['status_internal' => 9];
                            $send_notif = true;
                        }

                        // update records
                        $this->DeviceCheck->update($device_check->check_id, $update_data);

                        // building responses
                        $data += $update_data;
                        $response->data = $data;
                        $response_code = 200;
                        $response->success = true;
                        $response->message = 'OK';

                        $this->log->in($device_check->check_code, 43, json_encode($data), false, $device_check->user_id, $device_check->check_id);

                        // send notif to web admin
                        if ($send_notif) {
                            $nodejs = new Nodejs();
                            $nodejs->emit('new-cancel', [
                                'check_code' => $device_check->check_code,
                                'check_id' => $device_check->check_id,
                            ]);
                        }
                    }
                } else {
                    $response->message = "Invalid token. ";
                }
            } catch (\Exception $e) {
                $response->message = $e->getMessage();
                if ($response->message == 'Expired token') $response_code = 401;
            }
        }
        writeLog("api-app_1", "cancel\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function refresh()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';

        $rules = ['check_id' => getValidationRules('check_id')];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $select = 'check_code,imei,brand,model,storage,dc.type,dc.status,status_internal,grade,price,imei_registered,fullset_price,promo_id,finished_date,customer_name,customer_phone,choosen_date,choosen_time,account_number,account_name,pm.type as payment_type,pm.alias_name as payment_name,ap.name as province_name,ac.name as city_name,ad.name as district_name,postal_code,adr.notes as full_address,lock_until_date,courier_name,courier_phone';
            $where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
            $device_check = $this->DeviceCheck->getDeviceDetailAppointment($where, $select);

            if (!$device_check) {
                $response_code    = 404;
                $response->message = "Invalid check_id ($check_id). ";
            } else {
                // building responses
                $promo_name = "";
                $master_promo = new MasterPromos();
                $promo = $master_promo->getPromo($device_check->promo_id, "promo_name");
                if ($promo) $promo_name = $promo->promo_name;
                $price_unit = "" . ($device_check->price - $device_check->fullset_price); // harga hp tanpa fullset, string
                helper('number');
                $now = new DateTime();
                $lock_until_date = new DateTime($device_check->lock_until_date);
                $data = [
                    'check_id'                  => $check_id,
                    'check_code'                => $device_check->check_code,
                    'grade'                     => empty($device_check->grade) ? "" : $device_check->grade,
                    'price'                     => $device_check->price,
                    'price_formatted'           => number_to_currency($device_check->price, 'IDR'),
                    'price_unit'                => $price_unit,
                    'price_unit_formatted'      => number_to_currency($price_unit, 'IDR'),
                    'fullset_price'             => $device_check->fullset_price,
                    'fullset_price_formatted'   => number_to_currency($device_check->fullset_price, 'IDR'),
                    'brand'                     => $device_check->brand,
                    'model'                     => $device_check->model,
                    'storage'                   => $device_check->storage,
                    'type'                      => $device_check->type,
                    'promo_name'                => $promo_name,
                    'status'                    => $device_check->status,
                    'imei_registered'           => $device_check->imei_registered,
                    'server_date'               => $now->format('Y-m-d H:i:s'),
                    'finised_date'              => $device_check->finished_date,
                    'lock_until_date'           => $lock_until_date->format('Y-m-d H:i:s'),
                    'lock'                      => $lock_until_date > $now && $device_check->status == 7,
                ];

                // check the token if given
                $hasError = false;
                $token = $this->request->getPost('token') ?? '';
                if (!empty($token)) {
                    // var_dump($token);die;
                    try {
                        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                        if ($decoded) {
                            if ($check_id == $decoded->data->check_id) {
                                // add more private data
                                helper('device_check_status');
                                $data += [
                                    'status_internal'       => $device_check->status_internal,
                                    'status_internal_text'  => getDeviceCheckStatusInternal($device_check->status_internal),
                                    'customer_name'         => $device_check->customer_name,
                                    'customer_phone'        => $device_check->customer_phone,
                                    'province_name'         => $device_check->province_name,
                                    'province_name'         => $device_check->province_name,
                                    'city_name'             => $device_check->city_name,
                                    'district_name'         => $device_check->district_name,
                                    'postal_code'           => $device_check->postal_code,
                                    'full_address'          => $device_check->full_address,
                                    'account_number'        => $device_check->account_number,
                                    'account_name'          => $device_check->account_name,
                                    'payment_type'          => strtoupper($device_check->payment_type),
                                    'payment_name'          => $device_check->payment_name,
                                    'choosen_date'          => $device_check->choosen_date,
                                    'choosen_time'          => $device_check->choosen_time,
                                    'courier_name'          => $device_check->courier_name ?? "-",
                                    'courier_phone'         => $device_check->courier_phone ?? "-",
                                    'appointment_status'    => $device_check->status_internal == 8 ? 'Confirmed' : 'Unconfirmed',
                                ];
                            } else {
                                $hasError = true;
                                $response->message = "Invalid check_id from token. ";
                            }
                        } else {
                            $hasError = true;
                            $response->message = "Invalid token. ";
                        }
                    } catch (\Exception $e) {
                        $hasError = true;
                        $response->message = $e->getMessage();
                        if ($response->message == 'Expired token') $response_code = 401;
                    }
                }

                ksort($data);
                $response->data = $data;
                $response_code = 200;
                if (!$hasError) {
                    $response->success = true;
                    $response->message = 'OK';
                }
            }
        }
        writeLog("api-check_device", "refresh\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }
}
