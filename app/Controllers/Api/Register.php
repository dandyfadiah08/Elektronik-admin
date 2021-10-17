<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Models\Referrals;
use App\Libraries\Token;
use App\Libraries\Mailer;

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
                        $newName = $photo_id->getRandomName();
                        if ($photo_id->move('uploads/photo_id/', $newName)) {
                            $data += ['photo_id' => $newName];
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
                    $this->db->transComplete();
            
                    if ($this->db->transStatus() === FALSE) {
                        $response->message = $this->db->error();
                        $response->success = false;
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
                                $response->data = $otp->data;
                            }
                            else {
                                // gagal generate kode otp, mungkin redis error
                                $response->message .= 'Please kindly resend OTP Code. ';
                                $response->message .= $otp->message;
                            }

                            // logs
                            $data_logs['response'] = $response;
                            $data_logs['data'] = $data;
                            $this->log->in($name, 38, json_encode($data_logs), false, $user_id, false);
                        }
                    }
                    $this->db->transComplete();
                }
            }
        } else {
            $response->message = 'Method not allowed';
        }
        helper('log');
        writeLog(
            "api",
            "Register\n"
            . json_encode($this->request->getPost())
            . json_encode($response)
        );
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

                        // logs
                        $data_logs = [
                            'phone' => $phone,
                            'user_id' => $user->user_id
                        ];
                        $this->log->in($user->name, 50, json_encode($data_logs), false, $user->user_id, false);

                        // send email verification
                        // $email = $user->email;
                        // $email_verify = generateEmailVerificationLink($user->user_id, $email);
                        // if ($email_verify->success) {
                        //     // kirim email
                        //     $email_body_data = [
                        //         'template' => 'email_verification_link',
                        //         'd' => (object) [
                        //             'name' => $user->name,
                        //             'link' => $email_verify->message
                        //         ],
                        //     ];
                        //     $email_body = view('email/template', $email_body_data);
                        //     $mailer = new Mailer();
                        //     $data = (object)[
                        //         'receiverEmail' => $email,
                        //         'receiverName' => $user->name,
                        //         'subject' => 'Email Verification',
                        //         'content' => $email_body,
                        //     ];
                        //     $sendEmail = $mailer->send($data);
                        //     if ($sendEmail->success) $response->message .= "Next step, we've sent you a confirmation link to $email. Please confrim your email, thank you.";
                        // }
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

        helper('log');
        writeLog(
            "api",
            "Verify Phone\n"
            . json_encode($this->request->getPost())
            . json_encode($response)
        );
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

                    // logs
                    $data_logs = $response;
                    $this->log->in($user->name, 49, json_encode($data_logs), false, $user->user_id, false);
                    
                }
            } else {
                $response->message = "$phone does not need verification. ";
            }
        }
        return $this->respond($response, 200);
    }

    public function validateNik(){
		$response = initResponse('Outdated.');
        return $this->respond($response, 200);

		// $nik = $this->request->getPost('nik') ?? '';
		$rules = getValidationRules('validate_nik');
		if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
			$response->message = "Valid";
			$response->success = true;
		}
		return $this->respond($response, 200);
	}

	public function validateEmail(){
		$response = initResponse();
		// $email = $this->request->getPost('email') ?? '';
		$rules = getValidationRules('validate_email');
		if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
			$response->message = "Valid";
			$response->success = true;
		}
		return $this->respond($response, 200);
	}

	public function validatePhone(){
		$response = initResponse();
		// $phone = $this->request->getPost('phone') ?? '';
		$rules = getValidationRules('validate_phone');
		if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
			$response->message = "Valid";
			$response->success = true;
		}
		return $this->respond($response, 200);
	}

    public function validateRefCode(){
		$response = initResponse();
		$ref_code = $this->request->getPost('ref_code') ?? '';
		if($ref_code == '') {
            $response->message = "No referral code";
			$response->success = true;
        } else {
            $userParent = $this->UsersModel->getUser(['ref_code' => $ref_code, 'status' => 'active', 'type' => 'agent'], 'user_id, count_referral');
            if ($userParent) {
                $response->message = "Valid";
                $response->success = true;
            } else {
                $response->message = "Invalid referral code";
            }
		}
		return $this->respond($response, 200);
	}

    // public function test($no_hp = '0812345679') {
    //     $response = initResponse();

    //     $response = generateCodeOTP($no_hp);
    //     if($response->success) {
    //         // kirim sms
    //         helper('sms');
    //         $sendSMS = sendSmsOtp($no_hp, $response->message);
    //         $response->message = $sendSMS->message;
    //         if($sendSMS->success) $response->success = true;
    //     }
    //     return $this->respond($response, 200);
    // }

    private function checkParentRefferal($ref_code, $user_id)
    {
        $userParent = $this->UsersModel->getUser(['ref_code' => $ref_code, 'status' => 'active', 'type' => 'agent'], 'user_id, count_referral');
        // var_dump($userParent);die;
        if ($userParent) {
            // referral code valid
            $parent_id = $userParent->user_id;
            $dataReff = [
                'parent_id'     => $parent_id,
                'child_id'      => $user_id,
                'status'        => 'pending',
                'ref_level'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->RefferalModel->insert($dataReff);

            $count_referral = $userParent->count_referral + 1;
            $this->UsersModel->update($parent_id, ['count_referral' => $count_referral]);

            // check if $parent_id is a child
            $userParentHigher = $this->RefferalModel->getReferral(['child_id' => $parent_id], 'parent_id,status,ref_level', 'ref_level DESC');
            
            
            
            if ($userParentHigher) {
                if ($userParentHigher->ref_level < 2) {
                    $userParentLevel = $this->UsersModel->getUser(['user_id' => $userParentHigher->parent_id, 'status' => 'active', 'type' => 'agent'], 'user_id, count_referral');

                    $dataReff = [
                        'parent_id'     => $userParentHigher->parent_id,
                        'child_id'      => $user_id,
                        'status'        => 'pending',
                        'ref_level'     => 2,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];
                    $this->RefferalModel->insert($dataReff);
                    
                    $count_referralHigher = $userParentLevel->count_referral + 1;
                    $this->UsersModel->update($userParentHigher->parent_id, ['count_referral' => $count_referralHigher]);
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
