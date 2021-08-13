<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users as UserModel;
use App\Libraries\Mailer;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use Redis;

class Users extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefreshTokens;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new UserModel();
        $this->RefreshTokens = new RefreshTokens();
        helper('rest_api');
        helper('validation');
        helper('otp');
    }

    public function index()
    {
        $response = initResponse('Not implemented');
        return $this->respond($response, 200);
    }

    public function logout()
    {
        $response = initResponse();

        $header = $this->request->getServer('REDIRECT_HTTP_AUTHORIZATION');
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        var_dump($decoded);die;

        $where = ['user_id' => $user_id];
        $refresh_token = $this->RefreshTokens->getToken($where, 'user_id,expired_at');
        if($refresh_token) {
            $this->RefreshTokens->where($where)->delete();
            $response->message = "Logout successfully. ";
        } else {
            $response->message = "User does not exist. ";
        }
        return $this->respond($response, 200);
    }

    public function sendEmailVerification()
    {
        $response = initResponse();

        $token = $this->request->getPost('token') ?? '';
        $email = $this->request->getPost('email') ?? '';

        $header = $this->request->getServer('REDIRECT_HTTP_AUTHORIZATION');
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $rules = ['email' => getValidationRules('email')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,status', 'user_id DESC');
            if($user) {
                if($user->email == $email) {
                    if ($user->status == 'banned') {
                        $response->message = "Your account was banned";
                    } else {
                        $response = generateCodeOTP($email);
                        if($response->success) {
                            // kirim email
                            $mailer = new Mailer();
                            $data = (object)[
                                'receiverEmail' => $email,
                                'receiverName' => $user->name,
                                'subject' => 'Email Verification Code',
                                'content' => "Your email verification code on ".env('app.name')." is $response->message",
                            ];
                            $sendEmail = $mailer->send($data);
                            $response->message = $sendEmail->message;
                            if($sendEmail->success) $response->success = true;
                        }
                    }    
                } else {
                    $response->message = "$email does not exist. ";
                }
            } else {
                $response->message = "User does not exist. ";
            }
        }
        return $this->respond($response, 200);
    }

    public function verifyEmail()
    {
        $response = initResponse();

        $email = $this->request->getPost('email') ?? '';
        $otp = $this->request->getPost('otp') ?? '';

        $header = $this->request->getServer('REDIRECT_HTTP_AUTHORIZATION');
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $rules = getValidationRules('verify_email');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,phone_no_verified', 'user_id DESC');
            if($user) {
                if($user->email == $email) {
                    $redis = new Redis() or false;
                    $redis->connect(env('redis.host'), env('redis.port'));
                    $redis->auth(env('redis.password'));
                    $redis->get(env('redis.password'));
                    $key = "otp:$email";
                    $checkCodeOTP = checkCodeOTP($key, $redis);
                    if($checkCodeOTP->success) {
                        // OTP for $email is exist
                        if($otp == $checkCodeOTP->data['otp']) {
                            $response->success = true;
                            $response->message = "Email is verified. ";
                            $data = ['email_verified' => 'y'];
                            if ($user->phone_no_verified == 'y') {
                                $data += ['status' => 'active'];
                                $response->message .= "You can start transaction. ";
                            }
                            $this->UsersModel->update($user_id, $data);
                            $redis->del($key);
                        } else {
                            $response->message = "Wrong OTP code. ";
                        }
                    } else {
                        $response->message = "OTP code for $email is not found. ";
                    }
                } else {
                    $response->message = "$email does not exist. ";
                }
            } else {
                $response->message = "User does not exist. ";
            }
        }
    
        return $this->respond($response, 200);
    }

}
