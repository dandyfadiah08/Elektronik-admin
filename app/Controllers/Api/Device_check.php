<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\CheckCode;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\MasterPrices;
use App\Models\Users;
use \Firebase\JWT\JWT;

class Device_check extends BaseController
{

    use ResponseTrait;

    protected $request,$User,$MasterPrice,$DeviceCheck,$DeviceCheckDetail;


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
        $this->text_invalid_promo_code = 'Promo Code is invalid.';
		$this->text_price_0 = '<b>Sorry, your device could not found a suitable price, here are some of the reason :</b><br><br>1. Internasional Model Category<br>2. This is a new product we could not accept yet';
		$this->text_price_1 = 'Please contact our customer service for further information';
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
			$select_promo = 'check_id,imei,brand,model,storage,type,price_id';
			$where = array('check_code' => $check_code, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select_promo);

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

    public function save_photos()
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
			$select_promo = 'check_id,imei,brand,model,storage,type,price_id';
			$where = array('check_code' => $check_code, 'deleted_at' => null);
			$device_check = $this->DeviceCheck->getDevice($where, $select_promo);

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

    // function send_test() {
    //     helper('onesignal');
    //     $response = sendNotification(['e6bb3234-9992-406f-9e9b-d16e95960aae'], 'Judulnya', 'Isinya', ['key' => 'val'], false);
    //     $response = sendNotification(['628976563991'], 'Judulnya', 'Isinya', ['key' => 'val']);
    //     var_dump($response);
    // }
}
