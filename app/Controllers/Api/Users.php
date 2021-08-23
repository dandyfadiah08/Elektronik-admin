<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users as UserModel;
use App\Libraries\Mailer;
use App\Models\Appointments;
use App\Models\DeviceChecks;
use App\Models\Referrals;
use App\Models\UserBalance;
use App\Models\UserPayouts;
use App\Models\RefreshTokens;
use App\Models\UserAdresses;
use App\Models\UserPayments;
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
        $this->ReferralsModel = new Referrals();
        $this->UserBalance = new UserBalance();
        $this->DeviceChecks = new DeviceChecks();
        $this->UserPayouts = new UserPayouts();
        $this->RefreshTokens = new RefreshTokens();
        $this->UserAddress = new UserAdresses();
        $this->UserPayment = new UserPayments();
        $this->Appointments = new Appointments();
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

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        var_dump($decoded);
        die;

        $where = ['user_id' => $user_id];
        $refresh_token = $this->RefreshTokens->getToken($where, 'user_id,expired_at');
        if ($refresh_token) {
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

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $rules = ['email' => getValidationRules('email')];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,status', 'user_id DESC');
            if ($user) {
                if ($user->email == $email) {
                    if ($user->status == 'banned') {
                        $response->message = "Your account was banned";
                    } else {
                        $response = generateCodeOTP($email);
                        if ($response->success) {
                            // kirim email
                            $mailer = new Mailer();
                            $data = (object)[
                                'receiverEmail' => $email,
                                'receiverName' => $user->name,
                                'subject' => 'Email Verification Code',
                                'content' => "Your email verification code on " . env('app.name') . " is $response->message",
                            ];
                            $sendEmail = $mailer->send($data);
                            $response->message = $sendEmail->message;
                            if ($sendEmail->success) $response->success = true;
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

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $rules = getValidationRules('verify_email');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,phone_no_verified', 'user_id DESC');
            if ($user) {
                if ($user->email == $email) {
                    $redis = new Redis() or false;
                    $redis->connect(env('redis.host'), env('redis.port'));
                    $redis->auth(env('redis.password'));
                    $redis->get(env('redis.password'));
                    $key = "otp:$email";
                    $checkCodeOTP = checkCodeOTP($key, $redis);
                    if ($checkCodeOTP->success) {
                        // OTP for $email is exist
                        if ($otp == $checkCodeOTP->data['otp']) {
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

    public function infoDashboard()
    {
        $response = initResponse();

        $token = $this->request->getPost('token') ?? '';
        try {
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->user_id;

            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'active_balance', 'user_id DESC');
            $countReferral = $this->ReferralsModel->CountAllChild(['parent_id' => $user_id]);

            $response->data['active_balance'] = $user['active_balance'];
            $response->data['count_referral'] = $countReferral;
        } catch (\Exception $e) {
            var_dump($e);
            die;
            $response->message = "Invalid token. ";
        }

        return $this->respond($response, 200);
    }

    public function infoDownline()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $referral = $this->ReferralsModel->getDownlineData($user_id);

        $user_balance = $this->UserBalance->getTotalBalances(['user_id' => $user_id, 'from_user_id' => $user_id], 'SUM(amount) as total_amount, COUNT(amount) as total_transaction', 'from_user_id');

        $main_account = (object)[
            'name' => $decoded->data->name,
            'transaction' => $user_balance->total_transaction,
            'saving' => $user_balance->total_amount,
        ];

        $response->data['main_account'] = $main_account;
        if ($referral) {
            $response->message = "Sukses";
            $response->success = true;

            $response->data['sub_account'] = $referral;
        } else {
            $response->message = "Sukses";
            $response->success = true;
        }

        return $this->respond($response, 200);
    }

    public function getLastWithdraw()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $withdraws = $this->UserBalance->getUserBalances(['user_id' => $user_id], 'user_balance_id,
        amount,type,status,created_at,check_id', 'user_balance_id DESC', false, 3);

        $response->data = $withdraws;

        return $this->respond($response, 200);
    }

    public function getTransactionSuccess()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $transactionChecks = $this->UserPayouts->getTransactionUser($user_id);
        $response->data = $transactionChecks;

        return $this->respond($response, 200);
    }

    public function getTransactionPending()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $status_pending = '1'; //Seharusnya status pending
        $transactionChecks = $this->DeviceChecks->getDeviceChecks(['user_id' => $user_id, 'status' => $status_pending], 'check_id,check_code,imei,brand,
		model,type,storage,os,price,grade,status', false, 3);
        $response->data = $transactionChecks;

        return $this->respond($response, 200);
    }

    public function getAddressUser()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $address = $this->UserAddress->getAddressUser(['user_id' => $user_id], UserAdresses::getFieldForAddress());

        $response->data = $address;
        return $this->respond($response, 200);
    }

    public function getPaymentUser()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $paymentUser = $this->UserPayment->getPaymentUser(['user_id' => $user_id], UserPayments::getFieldForPayment());

        $response->data = $paymentUser;
        return $this->respond($response, 200);
    }

    public function submitAppoinment()
    {
        $response = initResponse();
        $data = [];

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $nameOwner = $this->request->getPost('name_owner') ?? '';
        $addressId = $this->request->getPost('address_id') ?? '';
        $paymentId = $this->request->getPost('payment_id') ?? '';
        $dateChoose = $this->request->getPost('date_choose') ?? '';
        $timeChoose = $this->request->getPost('time_choose') ?? '';
        $checkId = $this->request->getPost('check_id') ?? '';

        $device_checks = $this->DeviceChecks->getDeviceChecks(['user_id' => $user_id, 'check_id' => $checkId], 'COUNT(check_id) as total_check');
        if ($device_checks[0]->total_check == 1) {

            $data_check = $this->Appointments->getAppoinment(['user_id' => $user_id, 'check_id' => $checkId, 'deleted_at' => null], 'COUNT(appointment_id) as total_appoinment')[0];
            if($data_check->total_appoinment >0) {
                $response->message = "Transaction was finished"; //bingung kata katanya (jika check id dan user sudah pernah konek)
                $response->success = false;
            } else {
                $data += [
                    'user_id'           => $user_id,
                    'check_id '         => $checkId,
                    'address_id  '      => $addressId,
                    'user_payment_id  ' => $paymentId,
                    'phone_owner_name ' => $nameOwner,
                    'choosen_date '     => $dateChoose,
                    'choosen_time '     => $timeChoose,
                    'created_at '       => date('Y-m-d H:i:s'),
                    'updated_at '       => date('Y-m-d H:i:s'),
                ];
                
                $this->Appointments->insert($data);
    
                if ($this->db->transStatus() === FALSE) {
                    $response->message = $this->db->error();
                    $response->success = false;
                    $this->db->transRollback();
                } else {
                    $response->message = 'Success';
                    $response->success = true;
                    $this->db->transCommit();
                }
                $this->db->transComplete();
            }
            $this->db->transStart();

            
        } else {
            $response->message = "Transaction Not Found";
            $response->success = false;
        }




        return $this->respond($response, 200);
    }
}
