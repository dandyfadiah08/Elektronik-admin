<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\MasterPrices;
use App\Models\MasterPromos;
use App\Models\Users;
use App\Models\AdminsModel;
use CodeIgniter\I18n\Time;
use \Firebase\JWT\JWT;
use App\Libraries\FirebaseCoudMessaging;

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
        $this->dateTimeFormat = 'Y-m-d H:i:s'; // formatted as database needs
        $this->waitingTime = 5; // in minutes
    }
    
    public function scan()
    {
        $response = initResponse();
        $response_code = 200;

        $check_code = $this->request->getPost('check_code') ?? '';

        $promo_codes_valid = true;
        $rules = ['check_code' => getValidationRules('check_code')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id,imei,brand,model,storage,type,price_id';
			$where = array('check_code' => $check_code, 'deleted_at' => null);
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
                    $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission');
                    if($user) {
                        helper('user_status');
                        $user_status = doUserStatusCondition($user);
                        if($user_status->success) {
                            $update_data = [
                                'user_id'   => $user_id,
                                'type_user' => $user->type,
                                'status'    => 2,
                            ];
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
			$select = 'check_id';
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
                $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission');
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
                            $response->data = $response_data;
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';
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
			$select = 'check_id';
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
                $user = $this->User->getUser(['user_id' => $user_id], 'type,status,email,email_verified,submission');
                if($user) {
                    helper('user_status');
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

                            // building responses
                            $response_data = $update_data;
                            $response_data += $update_data_detail;
                            $response->data = $response_data;
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';
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

    public function save_identity()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';
        $customer_name = $this->request->getPost('customer_name') ?? '';
        $customer_phone = $this->request->getPost('customer_phone') ?? '';

        $rules = getValidationRules('app_2:save_identity');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id';
			$where = array('check_id' => $check_id, 'status' => 4, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select);

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
                    helper('user_status');
                    $user_status = doUserStatusCondition($user);
                    if($user_status->success) {
                        $update_data = ['status' => 5];
                        
                        $update_data_detail = [
                            'customer_name'   => $customer_name,
                            'customer_phone'   => $customer_phone,
                        ];

                        // update records
                        $this->DeviceCheck->update($device_check->check_id, $update_data);
                        $this->DeviceCheckDetail->where(['check_id' => $device_check->check_id])
                        ->set($update_data_detail)
                        ->update();

                        // building responses
                        $response_data = $update_data;
                        $response_data += $update_data_detail;
                        $response->data = $response_data;
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
        writeLog("api-check_device", "save_identity\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

        return $this->respond($response, $response_code);
    }

    public function save_photo_id()
    {
        $response = initResponse();
        $response_code = 200;

        $check_id = $this->request->getPost('check_id') ?? '';

        $rules = getValidationRules('app_2:save_photo_id');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
			$select = 'check_id,check_code';
			$where = array('check_id' => $check_id, 'status' => 5, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select);

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
                    helper('user_status');
                    $user_status = doUserStatusCondition($user);
                    if($user_status->success) {
                        $update_data = [
                            'status'            => 6,
                            'status_internal'   => 2, // ready for appointment
                        ];

                        $hasError = false;
                        $tempMessage = "";
                        $now = new Time('now');
                        $waitingDate = new Time('+'.$this->waitingTime.' minutes');
                        $update_data_detail = [
                            'finished_date' => $now->toDateTimeString(), // or $now->toLocalizedString('Y-MM-dd HH:mm:ss')
                            'waiting_date'  => $waitingDate->toDateTimeString(),
                        ];

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
                            $token_notifications = [];
                            $AdminModel = new AdminsModel();
                            $tokens = $AdminModel->getTokenNotifications();
                            foreach($tokens as $token) $token_notifications[] = $token->token_notification;
                            $fcm = new FirebaseCoudMessaging();
                            $send_fcm_push_web = $fcm->sendWebPush($token_notifications, "New Data", "Please review this new data: $device_check->check_code");

                            // building responses
                            $response_data = $update_data;
                            $response_data += $update_data_detail;
                            $response_data += [
                                'server_date' => date($this->dateTimeFormat),
                            ];
                            $response->data = $response_data;
                            $response_code = 200;
                            $response->success = true;
                            $response->message = 'OK';
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
        writeLog("api-check_device", "save_photo_id\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));

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
                    helper('user_status');
                    $user_status = doUserStatusCondition($user);
                    if($user_status->success) {
                        // building responses
                        $promo_name = "";
            			$master_promo = new MasterPromos();
                        $promo = $master_promo->getPromo($device_check->promo_id, "promo_name");
                        if($promo) $promo_name = $promo->promo_name;
                        helper('number');
                        $response->data = [
                            'check_id'                  => $check_id,
                            'check_code'                => $device_check->check_code,
                            'key_code'                  => $device_check->key_code,
                            'grade'                     => empty($device_check->grade) ? "" : $device_check->grade,
                            'price'                     => $device_check->price,
                            'price_formatted'           => number_to_currency($device_check->price, 'IDR'),
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

    function send_test() {
        // helper('onesignal');
        // $response = sendNotification(['e6bb3234-9992-406f-9e9b-d16e95960aae'], 'Judulnya', 'Isinya', ['key' => 'val'], false);
        // $response = sendNotification(['628976563991'], 'Judulnya', 'Isinya', ['key' => 'val']);
        // var_dump($response);

        // send push notif to web user
        $token_notifications = [];
        $AdminModel = new AdminsModel();
        $tokens = $AdminModel->getTokenNotifications();
        foreach($tokens as $token) $token_notifications[] = $token->token_notification;
        // var_dump($token_notifications);        
        $fcm = new FirebaseCoudMessaging();
        $to = [
            'dgft4p2Nl8FBUDbCqEPlaV:APA91bFWx4WDJqRWUPCTX_sGMjTKuyWPwnALr-zGz-YsbsZD4Y5I4yGhDaC_BYt-lpq-Cmr8feY2ek5tTZkkjZHltUnoM4TCi_ZTi3oVXErB3Uycwy0Qss4mzTj7xOsTUADIt8Ww-GvI',
            'c9xnWMKIfqYBWFRBw-t4Eg:APA91bFYBAmJaOdgfhmsa7k6NUX09puRFo4N84ILGA11Ov5HKGsKYXJ2yjkXifGdJdizV2YROSHv0FEQC8L07pZwC967zYI3qYhm-z3c0JbHjAyXbZNTOnh0RbPamPcPW1Vw7IAcNsVf',
        ];
        $send_fcm = $fcm->sendWebPush($token_notifications, "Ayo masuk New", "lah Please this new data: 2101234C", [], 'https://www.google.com/logos/doodles/2021/doodle-champion-island-games-august-24-6753651837108999-s.png');
        // if(!$send_fcm->hasFailures()) echo 'berhasil';
        // else echo 'gagal';
        var_dump($send_fcm);die; // output object dari initResponse()
        // var_dump($send_fcm->success()->count());
        // var_dump($send_fcm->failures()->count());die;

    }
}
