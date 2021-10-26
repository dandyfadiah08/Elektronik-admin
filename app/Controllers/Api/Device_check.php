<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\MasterPrices;
use App\Models\MasterPromos;
use App\Models\Users;
use App\Models\AdminsModel;
use CodeIgniter\I18n\Time;
use \Firebase\JWT\JWT;
use App\Libraries\FirebaseCoudMessaging;
use App\Libraries\Nodejs;
use App\Models\MerchantModel;

class Device_check extends BaseController
{

    use ResponseTrait;

    protected $request,$User,$MasterPrice,$DeviceCheck,$DeviceCheckDetail;
    protected $dateTimeFormat,$waitingTime;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->User = new Users();
        $this->MasterPrice = new MasterPrices();
        $this->DeviceCheck = new DeviceChecks();
        $this->DeviceCheckDetail = new DeviceCheckDetails();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('grade');
        helper('log');
        helper('user_status');
        $this->dateTimeFormat = 'Y-m-d H:i:s'; // formatted as database needs
        $this->waitingTime = 5; // in minutes
    }
    
    public function scan()
    {
        $response = initResponse();
        $response_code = 200;

        $check_code = $this->request->getPost('check_code') ?? '';
        $merchant_id = $this->request->getPost('merchant_id') ?? false;

        $promo_codes_valid = true;
        $rules = ['check_code' => getValidationRules('check_code')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id,user_id,imei,brand,model,storage,type,price_id';
			$where = array('check_code' => $check_code, 'status' => 1, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select);

			if (!$device_check) {
				$response_code	= 404;
				$response->message = "Invalid check_code. ";
			} else {
				// get price
				$price_id = $device_check->price_id;
                $select_price = 'brand,model,storage,type,price_s,price_a,price_b,price_c,price_d,price_e';
                $where = ['price_id' => $price_id, 'deleted_at' => null];
				$master_price = $this->MasterPrice->getPrice($where, $select_price);
    			// var_dump($master_price);die;
				
				if (!empty($master_price)) {
                    $header = $this->request->getServer(env('jwt.bearer_name'));
                    $token = explode(' ', $header)[1];
                    $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                    $user_id = $decoded->data->user_id;
                    $user = $this->User->getUser(['user_id' => $user_id], 'name,type,status,email,email_verified,submission');
                    if($user) {
                        $user_status = doUserStatusCondition($user);
                        if($user_status->success) {
                            $update_data = [
                                'user_id'   => $user_id,
                                'type_user' => $user->type,
                                'status'    => 2,
                            ];
                            $data_logs = ['name' => $user->name];

                            $hasError = false;
                            if($merchant_id > 0) {
                                $this->Merchant = new MerchantModel();
                                $merchant = $this->Merchant->getMerchant(['merchant_id' => $merchant_id, 'status' => 'active', 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
                                if($merchant) {
                                    $update_data += ['merchant_id' => $merchant_id];
                                    $data_logs += ['merchant_name' => $merchant->merchant_name, 'merchant_code' => $merchant->merchant_code];
                                } else {
                                    $response->message = "Invalid user_id ($user_id)";
                                    $hasError = true;
                                }
                            }

                            if($hasError) {
                                $response_code = 404;
                            } else {
                                $this->DeviceCheck->update($device_check->check_id, $update_data);
    
                                $warning_text = '';
                                if($user->type == 'nonagent') {
                                    $warning_text = "You are not agent, you will not get commision on this transaction. ";
                                    if($user->submission == 'y') $warning_text = "Your submission is still in review. ";
                                }
                                $data = [
                                    'check_id'		=> $device_check->check_id,
                                    'imei'		    => $device_check->imei,
                                    'brand'			=> $device_check->brand,
                                    'model'			=> $device_check->model,
                                    'storage'		=> $device_check->storage,
                                    'type'			=> $device_check->type,
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
                                    'warning_text'  => $warning_text,
                                ];
                                ksort($data);
                                $response->data = $data;
                                $response_code = 200;
                                $response->success = true;
                                $response->message = 'OK';
    
                                $data_log = array_merge($data_logs, (array)$device_check, $update_data);
                                $this->log->in("$check_code\n$user->name", 45, json_encode($data_log), false, $device_check->user_id, $device_check->check_id);
                            }
                        } else {
                            $response_code = 404;
                            $response->message = $user_status->message;
                        }
                    } else {
                        $response_code = 404;
                        $response->message = "Invalid user_id ($user_id)";
                    }
				} else {
                    $response_code	= 404;
                    $response->message = "Price is not found ($price_id)";
				}
			}
		}
        writeLog("api-check_device", "scan\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function save_photo()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';
        $imei = $this->request->getPost('imei') ?? '';

        $rules = getValidationRules('app_2:save_photos');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id,check_code,user_id';
			$where = array('check_id' => $check_id, 'status' => 2, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select);

			if (!$device_check) {
				$response_code	= 404;
				$response->message = "Invalid check_id ($check_id). ";
			} else {
                $header = $this->request->getServer(env('jwt.bearer_name'));
                $token = explode(' ', $header)[1];
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                $user_id = $decoded->data->user_id;
                $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission,name');
                if($user) {
                    $response_code = 404;
                    if($user->status == 'pending') {
                        $response->message = "Your account is pending. ";
                        if($user->email_verified == 'n') $response->message = "Please confirm that is $user->email is your email. ";
                    } elseif($user->status == 'inactive') {
                        $response->message = "Your account is inactive, please ask customer service for further information. ";
                    } elseif($user->status == 'banned') {
                        $response->message = "Your account is banned. ";
                    } else {

                        $update_data = [
                            'imei'      => $imei,
                            'status'    => 3,
                        ];

                        $hasError = false;
                        $tempMessage = "";
                        $update_data_detail = [];
                        $photos = [
                            1 => $this->request->getFile('photo_device_1'),
                            2 => $this->request->getFile('photo_device_2'),
                            3 => $this->request->getFile('photo_device_3'),
                            4 => $this->request->getFile('photo_device_4'),
                            5 => $this->request->getFile('photo_device_5'),
                            6 => $this->request->getFile('photo_device_6'),
                        ];
                        for($i = 1; $i <= count($photos); $i++) {
                            $newName = $photos[$i]->getRandomName();
                            if ($photos[$i]->move('uploads/device_checks/', $newName)) {
                                $update_data_detail += [
                                    "photo_device_$i" => $newName,
                                ];
                            } else {
                                $tempMessage .= "Error upload file";
                                $hasError = true;
                            }
                        }

                        if($hasError) {
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

                            $data_log = $update_data;
                            $this->log->in("$device_check->check_code\n$user->name", 41, json_encode($data_log), false, $device_check->user_id, $device_check->check_id);
                        }
                    }
                } else {
                    $response_code = 404;
                    $response->message = "Invalid user_id ($user_id)";
                }
			}
		}
        writeLog("api-check_device", "save_photo\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function save_quiz()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';
        $imei_registered = $this->request->getPost('imei_registered') ?? '';
        $quiz_1 = $this->request->getPost('quiz_1') ?? '';
        $quiz_2 = $this->request->getPost('quiz_2') ?? '';
        $quiz_3 = $this->request->getPost('quiz_3') ?? '';
        $quiz_4 = $this->request->getPost('quiz_4') ?? '';

        $rules = getValidationRules('app_2:save_quiz');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id,check_code,user_id';
			$where = array('check_id' => $check_id, 'status' => 3, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select);

			if (!$device_check) {
				$response_code	= 404;
				$response->message = "Invalid check_id ($check_id). ";
			} else {
                $header = $this->request->getServer(env('jwt.bearer_name'));
                $token = explode(' ', $header)[1];
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                $user_id = $decoded->data->user_id;
                $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission,name');
                if($user) {
                    $user_status = doUserStatusCondition($user);
                    if($user_status->success) {
                        $update_data = ['status' => 4];

                        $hasError = false;
                        $tempMessage = "";
                        $update_data_detail = [
                            'imei_registered'   => $imei_registered == 1 ? 1 : 0,
                            'quiz_1'             => $quiz_1 == 1 ? 1 : 0,
                            'quiz_2'             => $quiz_2 == 1 ? 1 : 0,
                            'quiz_3'             => $quiz_3 == 1 ? 1 : 0,
                            'quiz_4'             => $quiz_4 == 1 ? 1 : 0,
                        ];
                        $now = new Time('now');
                        $waitingDate = new Time('+' . $this->waitingTime . ' minutes');
                        $update_data_detail += [
                            // 'finished_date' => $now->toDateTimeString(), // or $now->toLocalizedString('Y-MM-dd HH:mm:ss')
                            'waiting_date'  => $waitingDate->toDateTimeString(),
                        ];
    
                        // uploads photo_imei_registered
                        $photo_imei_registered = $this->request->getFile('photo_imei_registered');
                        $newName = $photo_imei_registered->getRandomName();
                        if ($photo_imei_registered->move('uploads/device_checks/', $newName)) {
                            $update_data_detail += [
                                "photo_imei_registered" => $newName,
                            ];
                        } else {
                            $tempMessage .= "Error upload file";
                            $hasError = true;
                        }

                        // uploads photo_fullset if provided
                        $photo_fullset = $this->request->getFile('photo_fullset');
                        if($photo_fullset) {
                            // validate photo_fullset 
                            $rules = ['photo_fullset' => getValidationRules('photo_fullset')];
                            if($this->validate($rules)) {
                                $newName = $photo_fullset->getRandomName();
                                if ($photo_fullset->move('uploads/device_checks/', $newName)) {
                                    $update_data_detail += [
                                        "photo_fullset" => $newName,
                                        "fullset" => 1,
                                    ];
                                } else {
                                    $tempMessage .= "Error upload file";
                                    $hasError = true;
                                }
                            } else {
                                $errors = $this->validator->getErrors();
                                foreach($errors as $error) $tempMessage .= "$error ";
                            }
                        }

                        if($hasError) {
                            // has any error
                            $response_code = 400;
                            $response->message = $tempMessage;
                        } else {
                            // update records
                            $this->DeviceCheck->update($device_check->check_id, $update_data);
                            $this->DeviceCheckDetail->where(['check_id' => $device_check->check_id])
                            ->set($update_data_detail)
                            ->update();

                            // send push notif to admin web
                            try {
                                $token_notifications = [];
                                $AdminModel = new AdminsModel();
                                $tokens = $AdminModel->getTokenNotifications();
                                foreach($tokens as $token) $token_notifications[] = $token->token_notification;
                                $fcm = new FirebaseCoudMessaging();
                                $data_push_notif = ['type' => 'survey', 'check_id' => $check_id];
                                $send_fcm_push_web = $fcm->sendWebPush($token_notifications, "New Data", "Please review this new data: $device_check->check_code", $data_push_notif);
                                $nodejs = new Nodejs();
                                $nodejs->emit('new-data', [
                                    'check_code' => $device_check->check_code,
                                    'check_id' => $device_check->check_id,
                                ]);
                                writeLog("api-notification_web", "save_quiz\n" . json_encode($send_fcm_push_web));
                            } catch(\Exception $e) {
                                writeLog("api-notification_web", "save_quiz\n" . json_encode($this->request->getPost()) . "\n". $e->getMessage());
                            }

                            // building responses
                            $response_data = $update_data;
                            $response_data += $update_data_detail;
                            $response_data += [
                                'server_date' => date($this->dateTimeFormat),
                            ];
                            ksort($response_data);
                            $response->data = $response_data;
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';

                            $data_log = $response_data;
                            $this->log->in("$device_check->check_code\n$user->name", 47, json_encode($data_log), false, $device_check->user_id, $device_check->check_id);
                        }
                    } else {
                        $response_code = 404;
                        $response->message = $user_status->message;
                    }
                } else {
                    $response_code = 404;
                    $response->message = "Invalid user_id ($user_id)";
                }
			}
		}
        writeLog("api-check_device", "save_quiz\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function refresh()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';

        $rules = ['check_id' => getValidationRules('check_id')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_code,key_code,imei,brand,model,storage,type,price_id,promo_id,status,user_id,type_user,grade,price,imei_registered,quiz_1,quiz_2,quiz_3,quiz_4,photo_id,photo_imei_registered,photo_fullset,photo_device_1,photo_device_2,photo_device_3,photo_device_4,photo_device_5,photo_device_6,finished_date,waiting_date,fullset_price';
			$where = array('dc.check_id' => $check_id, 'dc.deleted_at' => null);
			$device_check = $this->DeviceCheck->getDeviceDetail($where, $select);
            // var_dump($device_check);die;

			if (!$device_check) {
				$response_code	= 404;
				$response->message = "Invalid check_id ($check_id). ";
			} else {
                $header = $this->request->getServer(env('jwt.bearer_name'));
                $token = explode(' ', $header)[1];
                $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
                $user_id = $decoded->data->user_id;
                $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission');
                if($user) {
                    $user_status = doUserStatusCondition($user);
                    if($user_status->success) {
                        // building responses
                        $promo_name = "";
            			$master_promo = new MasterPromos();
                        $promo = $master_promo->getPromo($device_check->promo_id, "promo_name");
                        if($promo) $promo_name = $promo->promo_name;
                        $price_unit = "".($device_check->price-$device_check->fullset_price); // harga hp tanpa fullset, string
                        helper('number');
                        $data = [
                            'check_id'                  => $check_id,
                            'check_code'                => $device_check->check_code,
                            'key_code'                  => $device_check->key_code,
                            'grade'                     => empty($device_check->grade) ? "" : $device_check->grade,
                            'price'                     => $device_check->price,
                            'price_formatted'           => number_to_currency($device_check->price, 'IDR'),
                            'price_unit'                => $price_unit,
                            'price_unit_formatted'      => number_to_currency($price_unit, 'IDR'),
                            'fullset_price'             => $device_check->fullset_price,
                            'fullset_price_formatted'   => number_to_currency($device_check->fullset_price, 'IDR'),
                            'brand'			            => $device_check->brand,
                            'model'			            => $device_check->model,
                            'storage'	                => $device_check->storage,
                            'type'			            => $device_check->type,
                            'price_id'			        => $device_check->price_id,
                            'promo_id'			        => $device_check->promo_id,
                            'promo_name'		        => $promo_name,
                            'user_id'			        => $device_check->user_id,
                            'type_user'			        => $device_check->type_user,
                            'status'			        => $device_check->status,
                            'imei_registered'           => $device_check->imei_registered,
                            'quiz_1'                    => $device_check->quiz_1,
                            'quiz_2'                    => $device_check->quiz_2,
                            'quiz_3'                    => $device_check->quiz_3,
                            'quiz_4'                    => $device_check->quiz_4,
                            'server_date'               => date($this->dateTimeFormat),
                            'finished_date'             => $device_check->finished_date,
                            'waiting_date'              => $device_check->waiting_date,
                            'photo_id'                  => empty($device_check->photo_id) ? 'n' : 'y',
                            'photo_fullset'             => empty($device_check->photo_fullset) ? 'n' : 'y',
                            'photo_imei_registered'     => empty($device_check->photo_imei_registered) ? 'n' : 'y',
                            'photo_device_1'            => empty($device_check->photo_device_1) ? 'n' : 'y',
                            'photo_device_2'            => empty($device_check->photo_device_2) ? 'n' : 'y',
                            'photo_device_3'            => empty($device_check->photo_device_3) ? 'n' : 'y',
                            'photo_device_4'            => empty($device_check->photo_device_4) ? 'n' : 'y',
                            'photo_device_5'            => empty($device_check->photo_device_5) ? 'n' : 'y',
                            'photo_device_6'            => empty($device_check->photo_device_6) ? 'n' : 'y',
                        ];
                        ksort($data);
                        $response->data = $data;
                        $response_code = 200;
                        $response->success = true;
                        $response->message = 'OK';
                    } else {
                        $response_code = 404;
                        $response->message = $user_status->message;
                    }
                } else {
                    $response_code = 404;
                    $response->message = "Invalid user_id ($user_id)";
                }
			}
		}
        writeLog("api-check_device", "refresh\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function get_url_check_imei()
    {
        $response = initResponse('OK', true);
        $response_code = 200;

        $key = 'app_2:url_check_imei';
        try {
            $redis = RedisConnect();
            $url_check_imei = $redis->get($key);
            if ($url_check_imei === FALSE) {
                // read from db, currently, hardcoded 
                $url_check_imei = 'https://imei.kemenperin.go.id';
                $redis->set($key, $url_check_imei);
            }
            $url_check_imei = $url_check_imei;
        } catch (\Exception $e) {
            // $response->message = $e->getMessage();
            // read from db, currently, hardcoded 
            $url_check_imei = 'https://imei.kemenperin.go.id';
            try {
                $redis = RedisConnect();
                $redis->set($key, $url_check_imei);
            } catch (\Exception $e) {
            }
        }
        $response->data = ['url' => $url_check_imei];

        writeLog("api-check_device", "get_url_check_imei\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    function send_test() {
        die;
        // send push notif using onesignal
        // helper('onesignal');
        // $response = sendNotification(['e6bb3234-9992-406f-9e9b-d16e95960aae'], 'Judulnya', 'Isinya', ['key' => 'val'], false);
        // $response = sendNotification(['628976563991'], 'Judulnya', 'Isinya', ['key' => 'val']);
        // var_dump($response);

        // send push notif to web user
        // $token_notifications = [];
        // $AdminModel = new AdminsModel();
        // $tokens = $AdminModel->getTokenNotifications();
        // foreach($tokens as $token) $token_notifications[] = $token->token_notification;
        // $fcm = new FirebaseCoudMessaging();
        // $send_fcm = $fcm->sendWebPush($token_notifications, "Ayo masuk New", "lah Please this new data: 2101234C", [], 'https://www.google.com/logos/doodles/2021/doodle-champion-island-games-august-24-6753651837108999-s.png');
        // if(!$send_fcm->hasFailures()) echo 'berhasil';
        // else echo 'gagal';
        // var_dump($send_fcm);die; // output object dari initResponse()
        // var_dump($send_fcm->success()->count());
        // var_dump($send_fcm->failures()->count());die;

        // hit nodejs
        $nodejs = new Nodejs();
        var_dump($nodejs->emit('new-data', [
            'check_code' => 'hehe',
            'check_id' => '12',
        ]));
    }
}
