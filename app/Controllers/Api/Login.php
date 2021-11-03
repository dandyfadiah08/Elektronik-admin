<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Libraries\Token;
use App\Models\MerchantModel;
use App\Models\Referrals;

// use App\Models\RefreshTokens;

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
        helper('redis');
        helper('otp');
    }

    public function index()
    {
        $response = initResponse();

        $phone = $this->request->getPost('phone') ?? '';
        $signature = $this->request->getPost('signature') ?? '';
        $merchant_id = $this->request->getPost('merchant_id') ?? false;

        $rules = ['phone' => getValidationRules('phone')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu no hp ada di db atau tidak
            $user = $this->UsersModel->getUser(['phone_no' => $phone], 'status,merchant_id', 'user_id DESC');
            if($user) {
                if ($user->status == 'banned') {
                    $response->message = "Nomor telah di banned";
                } else {
                    // cek merchant id sesuai atau tidak
                    $hasError = false;
                    if($merchant_id) {
                        $this->Merchant = new MerchantModel();
                        $merchant = $this->Merchant->getMerchant(['merchant_id' => $merchant_id, 'status' => 'active', 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
                        if($merchant) {
                            if($user->merchant_id != '' && $user->merchant_id != $merchant_id) {
                                // merchant_id sudah ada dan tidak sesuai, tidak boleh lanjut
                                $merchant = $this->Merchant->getMerchant(['merchant_id' => $user->merchant_id, 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
                                if($merchant) $response->message = "Nomor $phone telah terdaftar sebagai karwayan Mitra $merchant->merchant_name ($merchant->merchant_code)";
                                else $response->message = "Nomor $phone telah terdaftar sebagai karwayan Mitra lainnya";
                                $hasError = true;
                            }
                        } else {
                            $response->message = "Invalid merchant_id ($merchant_id)";
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        $response = generateCodeOTP($phone);
                        if($response->success) {
                            // kirim sms
                            helper('sms');
                            $sendSMS = sendSmsOtp($phone, $response->message, $signature);
                            $response->message = $sendSMS->message;
                            if($sendSMS->success) $response->success = true;
                        }
                    }
                }    
            } else {
                $response->message = "Nomor $phone belum terdaftar. ";
            }
        }
        helper('log');
        writeLog(
            "api",
            "Login\n"
            . json_encode($this->request->getPost())
            . json_encode($response)
        );
        return $this->respond($response, 200);
    }

    public function verifyOtp()
    {
        $response = initResponse();

        $phone = $this->request->getPost('phone') ?? '';
        $otp = $this->request->getPost('otp') ?? '';
        $merchant_id = $this->request->getPost('merchant_id') ?? false;
        $rules = getValidationRules('verify_phone');
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu no hp ada di db atau tidak
            $user = $this->UsersModel->getUser(['phone_no' => $phone], Users::getFieldsForToken().',merchant_id', 'user_id DESC');
            if($user) {
                // cek merchant id sesuai atau tidak
                $hasError = false;
                // var_dump([$merchant_id, $user->merchant_id == '']);die;
                if($merchant_id) {
                    $this->Merchant = new MerchantModel();
                    $merchant = $this->Merchant->getMerchant(['merchant_id' => $merchant_id, 'status' => 'active', 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
                    if($merchant) {
                        // var_dump([$user->merchant_id != '', $user->merchant_id != $merchant_id, $user->merchant_id, $merchant_id]); die;
                        if($user->merchant_id != '' && $user->merchant_id != $merchant_id) {
                            // merchant_id sudah ada dan tidak sesuai, tidak boleh lanjut
                            $merchant = $this->Merchant->getMerchant(['merchant_id' => $user->merchant_id, 'deleted_at' => null], 'merchant_id,merchant_code,merchant_name');
                            if($merchant) $response->message = "Nomor ini telah terdaftar sebagai karwayan Mitra $merchant->merchant_name ($merchant->merchant_code)";
                            else $response->message = "Nomor ini telah terdaftar sebagai karwayan Mitra lainnya";
                            $hasError = true;
                        } else {
                            // update user.merchant_id 
                            $this->UsersModel->update($user->user_id, [
                                'merchant_id' => $merchant_id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    } else {
                        $response->message = "Invalid merchant_id ($merchant_id)";
                        $hasError = true;
                    }
                }

                if(!$hasError) {
                    $redis = RedisConnect();
                    $key = "otp:$phone";
                    $checkCodeOTP = checkCodeOTP($key, $redis);
                    if($checkCodeOTP->success) {
                        // OTP for $phone is exist
                        if($otp == $checkCodeOTP->data['otp']) {
                            $response->success = true;
                            $response->message = "Berhasil. ";
                            if ($user->phone_no_verified == 'n') {
                                $this->UsersModel->update($user->user_id, ['phone_no_verified' => 'y']);
                                $response->message .= "Nomor HP terferivikasi. ";
                            }

                            // kirim notifikasi logout, ke device yang sudah login dengan no hp ini (#belum)

                            // create session JWT
                            $this->ReferralModel = new Referrals();
                            $count_referral = $this->ReferralModel->countReferralActiveByParent(['user_id' => $user->user_id]);
                            if(!$count_referral) $user->count_referral = "0";
                            else $user->count_referral = $count_referral->count_referral;
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

                            // jika login dari wowmitra, memastikan diupdate kode mitranya jika belum ada

                        } else {
                            $response->message = "Kode OTP salah. ";
                        }
                    } else {
                        $response->message = "Kode OTP untuk $phone tidak valid atau kadaluarsa. ";
                    }
                }
            }    
        }

        helper('log');
        writeLog(
            "api",
            "Verify OTP\n"
            . json_encode($this->request->getPost())
            . json_encode($response)
        );
        return $this->respond($response, 200);
    }

}
