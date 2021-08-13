<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users;
use App\Libraries\Token;
use App\Models\RefreshTokens;

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
                if ($user->status == 'banned') {
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
            $user = $this->UsersModel->getUser(['phone_no' => $phone], Users::getFieldsForToken(), 'user_id DESC');
            if($user) {
                helper('redis');
                $redis = RedisConnect();
                $key = "otp:$phone";
                $checkCodeOTP = checkCodeOTP($key, $redis);
                if($checkCodeOTP->success) {
                    // OTP for $phone is exist
                    if($otp == $checkCodeOTP->data['otp']) {
                        $response->success = true;
                        $response->message = "Logged in. ";
                        if ($user->phone_no_verified == 'n') {
                            $this->UsersModel->update($user->user_id, ['phone_no_verified' => 'y']);
                            $response->message .= "Phone number is verified. ";
                        }
                        // create session JWT
                        $response->data['token'] = Token::create($user);
                        // create refresh_token even if already exist (will be replaced)
                        $response->data['refresh_token'] = Token::createRefreshToken($user); // create and add to db and redis

                        // not reate refresh_token if already exist
                        // $refreshTokens = new RefreshTokens();
                        // $token_refresh = $refreshTokens->getToken(['user_id' => $user->user_id], 'token,expired_at');
                        // if($token_refresh) {
                        //     $response->data['refresh_token'] = $token_refresh->token;
                        //     Token::saveToRedis($token_refresh->token, "refresh_token:".$user->user_id);
                        // } else {
                        //     $response->data['refresh_token'] = Token::createRefreshToken($user); // create and add to db and redis
                        // }

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
