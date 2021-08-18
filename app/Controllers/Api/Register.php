<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users;
use App\Models\Referrals;
use App\Libraries\Token;

class Register extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefferalModel;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        $this->RefferalModel = new Referrals();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
    }

    public function index()
    {
        $response = initResponse();
        $response_code = 200;

        $method = strtoupper($this->request->getMethod());
        if($method == 'POST') {
            $name = $this->request->getPost('name') ?? '';
            $email = $this->request->getPost('email') ?? '';
            $phone = $this->request->getPost('phone') ?? '';
            $type = $this->request->getPost('type') ?? 2;
            $ref_code = $this->request->getPost('ref_code') ?? '';
            $signature = $this->request->getPost('signature') ?? '';

            $rules = getValidationRules('register');

            if(!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                $response->message = "";
                foreach($errors as $error) $response->message .= "$error ";

                // cek apakah nomor terdaftar, langsung kirim otp, response code : 202 (#belum)
                if($this->validator->hasError('phone')) {
                    if(str_contains($this->validator->getError('phone'), 'has been used')) $response_code = 202;
                }
            } else {
                $data = [];

                $hasError = false;
                if ($type == 1) {
                    // agent, upload photo_ktp and add nik
                    $nik = $this->request->getPost('nik') ?? '';

                    $rules = getValidationRules('register_agent');
                    $validated = $this->validate($rules);

                    if ($validated) {
                        $data += [
                            'nik' => $nik,
                            'submission' => 'y',
                        ];
                        $photo_id = $this->request->getFile('photo_id');
                        $newName = $photo_id->getRandomName() . '.' . $photo_id->getExtension();
                        if ($photo_id->move('uploads/images/', $newName)) {
                            $data += [
                                'photo_id' => $newName,
                            ];
                        } else {
                            $response->message = "Error upload file";
                            $hasError = true;
                        }
                    } else {
                        $errors = $this->validator->getErrors();
                        $response->message = "";
                        foreach($errors as $error) $response->message .= "$error ";
                        $hasError = true;
                    }
                }

                helper('referral_code');
                $data += [
                    'phone_no'  => $phone,
                    'name'      => $name,
                    'email'     => $email,
                    'ref_code'  => generateReferralCode([$name,4]),
                    'status'    => 'pending',
                    'type'      => 'nonagent',
                ];

                if(!$hasError) {
                    $this->db->transStart();
                    $this->UsersModel->insert($data);
            
                    if ($this->db->transStatus() === FALSE) {
                        $response->message = $this->db->error();
                        $response->success = false;
                        $this->db->transRollback();
                    } else {
                        $user_id = $this->UsersModel->getInsertID();
                        // logic cek refferal code
                        if(!empty($ref_code)) {
                            $is_referral_code_valid = $this->checkParentRefferal($ref_code, $user_id);
                            if(!$is_referral_code_valid) {
                                $hasError = true;
                                $response->message = "Refferal code $ref_code is invalid. ";
                            }
                        }

                        if(!$hasError) {
                            $response->success = true;
                            $response->message = 'Success. To complete your registration, please verify your phone number. ';

                            // kirim otp
                            $otp = generateCodeOTP($phone);
                            if($otp->success) {
                                // kirim sms
                                helper('sms');
                                $sendSMS = sendSmsOtp($phone, $otp->message, $signature);
                                // tidak berhasil buat otp, sarankan klik resendOtp ?
                                if(!$sendSMS->success) $response->message .= 'OTP Code might be need to be resent. ';
                            }
                            else {
                                // gagal generate kode otp, mungkin redis error
                                $response->message .= 'Please kindly resend OTP Code. ';
                                $response->message .= $otp->message;
                            }
                            $this->db->transCommit();
                        } else $this->db->transRollback();
                    }
                    $this->db->transComplete();
                }
            }
        } else {
            $response->message = 'Method not allowed';
        }
        return $this->respond($response, $response_code);
    }

    public function verifyPhone()
    {
        $response = initResponse();

        $phone = $this->request->getPost('phone') ?? '';
        $otp = $this->request->getPost('otp') ?? '';
        $login = $this->request->getPost('login') ?? 0;

        $rules = getValidationRules('verify_phone');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu no hp ada di db atau tidak
            $user = $this->UsersModel->getUser(['phone_no' => $phone, 'phone_no_verified' => 'n'], Users::getFieldsForToken(), 'user_id DESC');
            if($user) {
                $redis = RedisConnect();
                $key = "otp:$phone";
                $checkCodeOTP = checkCodeOTP($key, $redis);
                if($checkCodeOTP->success) {
                    // OTP for $phone is exist
                    if($otp == $checkCodeOTP->data['otp']) {
                        $this->UsersModel->update($user->user_id, ['phone_no_verified' => 'y']);
                        $redis->del($key);
                        if($login == 1) {
                            $response->data['token'] = Token::create($user);
                            $response->data['refresh_token'] = Token::createRefreshToken($user); // create and add to db and redis
                        }

                        $response->success = true;
                        $response->message = "Phone number verified. ";
                    } else {
                        $response->message = "Wrong OTP code. ";
                    }
                } else {
                    $response->message = "OTP code for $phone is not found. ";
                }    
            } else {
                $response->message = "$phone does not need verification. ";
            }
        }

        return $this->respond($response, 200);
    }

    public function resendOtp()
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
            $user = $this->UsersModel->getUser(['phone_no' => $phone, 'phone_no_verified' => 'n'], 'name,user_id', 'user_id DESC');
            if($user) {
                $response = generateCodeOTP($phone);
                if($response->success) {
                    // kirim sms
                    helper('sms');
                    $sendSMS = sendSmsOtp($phone, $response->message);
                    $response->message = $sendSMS->message;
                    if($sendSMS->success) $response->success = true;
                }
            } else {
                $response->message = "$phone does not need verification. ";
            }
        }
        return $this->respond($response, 200);
    }

    public function test($no_hp = '0812345679') {
        $response = initResponse();

        $response = generateCodeOTP($no_hp);
        if($response->success) {
            // kirim sms
            helper('sms');
            $sendSMS = sendSmsOtp($no_hp, $response->message);
            $response->message = $sendSMS->message;
            if($sendSMS->success) $response->success = true;
        }
        return $this->respond($response, 200);
    }

    private function checkParentRefferal($ref_code, $user_id)
    {
        $userParent = $this->UsersModel->getUser(['ref_code' => $ref_code, 'status' => 'active', 'type' => 'agent'], 'user_id');
        // var_dump($userParent);die;
        if ($userParent) {
            // referral code valid
            $parent_id = $userParent['user_id'];
            $dataReff = [
                'parent_id'     => $parent_id,
                'child_id'      => $user_id,
                'status'        => 'pending',
                'ref_level'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->RefferalModel->insert($dataReff);

            // check if $parent_id is a child
            $userParentHigher = $this->RefferalModel->getReferral(['child_id' => $parent_id], 'parent_id,status,ref_level', 'ref_level DESC');
            if ($userParentHigher) {
                if ($userParentHigher['ref_level'] < 2) {
                    $dataReff = [
                        'parent_id'     => $userParentHigher['parent_id'],
                        'child_id'      => $user_id,
                        'status'        => 'pending',
                        'ref_level'     => 2,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];
                    $this->RefferalModel->insert($dataReff);
                }
            }

            if ($this->db->transStatus() === FALSE) {

                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
