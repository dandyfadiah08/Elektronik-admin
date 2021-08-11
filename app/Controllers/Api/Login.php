<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users;
use Firebase\JWT\JWT;
use Redis;

class Login extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        helper('rest_api');
        helper('validation');
        helper('otp');
    }

    public function index()
    {
        $response = initResponse();

        $phone = $this->request->getPost('phone') ?? '';

        $rules = ['phone' => getValidationRules('phone')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu no hp ada di db atau tidak
            $user = $this->UsersModel->getUser(['phone_no' => $phone], 'status', 'user_id DESC');
            if($user) {
                if ($user['status'] == 'banned') {
                    $response->message = "Your account was banned";
                } else {
                    $response = generateCodeOTP($phone);
                    if($response->success) {
                        // kirim sms
                        helper('sms');
                        $sendSMS = sendSmsOtp($phone, $response->message);
                        $response->message = $sendSMS->message;
                        if($sendSMS->success) $response->success = true;
                    }
                }    
            } else {
                $response->message = "User with phone number $phone is not found. ";
            }
        }
        return $this->respond($response, 200);
    }

    public function verifyOtp()
    {
        $response = initResponse();

        $phone = $this->request->getPost('phone') ?? '';
        $otp = $this->request->getPost('otp') ?? '';
        $rules = getValidationRules('verify_phone');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu no hp ada di db atau tidak
            $user = $this->UsersModel->getUser(['phone_no' => $phone], 'name,user_id,status,phone_no_verified,email_verified,nik_verified,submission', 'user_id DESC');
            if($user) {
                $redis = new Redis() or false;
                $redis->connect(env('redis.host'), env('redis.port'));
                $redis->auth(env('redis.password'));
                $redis->get(env('redis.password'));
                $key = "otp_$phone";
                $checkCodeOTP = checkCodeOTP($key, $redis);
                if($checkCodeOTP->success) {
                    // OTP for $phone is exist
                    if($otp == $checkCodeOTP->data['otp']) {
                        $response->success = true;
                        $response->message = "Logged in. ";
                        if ($user['phone_no_verified'] == 'n') {
                            $this->UsersModel->update($user['user_id'], ['phone_no_verified' => 'y']);
                            $response->message .= "Phone number is verified. ";
                        }
                        // create session JWT
                        $jwt = new JWT();
                        $payload = [
                            'user_id'           => $user['user_id'],
                            'status'            => $user['status'],
                            'phone_no_verified' => $user['phone_no_verified'],
                            'email_verified'    => $user['email_verified'],
                            'nik_verified'      => $user['nik_verified'],
                            'submission'      => $user['submission'],
                        ];
                        $response->data['token'] = $jwt->encode($payload, $_ENV['jwt.key']);
                        $redis->del($key);
                    } else {
                        $response->message = "Wrong OTP code. ";
                    }
                } else {
                    $response->message = "OTP code for $phone is not found. ";
                }    
            } else {
                $response->message = "$phone does not exist. ";
            }
        }

        return $this->respond($response, 200);
    }

}
