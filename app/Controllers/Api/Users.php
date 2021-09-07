<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users as UserModel;
use App\Libraries\Mailer;
use App\Models\Appointments;
use App\Models\AvailableDateTime;
use App\Models\DeviceChecks;
use App\Models\Referrals;
use App\Models\UserBalance;
use App\Models\UserPayouts;
use App\Models\RefreshTokens;
use App\Models\UserAdresses;
use App\Models\UserPayments;
use Firebase\JWT\JWT;

class Users extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefreshTokens, $DeviceCheck, $Referral;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new UserModel();
        $this->Referral = new Referrals();
        $this->UserBalance = new UserBalance();
        $this->DeviceCheck = new DeviceChecks();
        $this->UserPayouts = new UserPayouts();
        $this->RefreshTokens = new RefreshTokens();
        $this->UserAddress = new UserAdresses();
        $this->UserPayment = new UserPayments();
        $this->Appointments = new Appointments();
        $this->AvailableDateTime = new AvailableDateTime();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
        helper('user_status');
        helper("format_helper");
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
        // var_dump($decoded);
        // die;

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

    public function updatedNotificationToken()
    {
        $response = initResponse();

        $notification_token = $this->request->getPost('notification_token') ?? '';

        $rules = ['notification_token' => getValidationRules('notification_token')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'user_id', 'user_id DESC');
            if($user) {
                $this->UsersModel->update($user_id, ['notification_token' => $notification_token]);
                $response->success = true;
                $response->message = "Notification token updated. ";
            } else {
                $response->message = "User does not exist. ";
            }
        }
        return $this->respond($response, 200);
    }

    public function sendEmailVerification()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        //cek dulu email ada di db atau tidak
        $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,status', 'user_id DESC');
        if($user) {
            if ($user->status == 'banned') {
                $response->message = "Your account was banned";
            } else {
                $email = $user->email;
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
            $response->message = "User does not exist. ";
        }

        return $this->respond($response, 200);
    }

    public function verifyEmail()
    {
        $response = initResponse();

        $otp = $this->request->getPost('otp') ?? '';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $rules = ['otp' => getValidationRules('otp')];
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,phone_no_verified', 'user_id DESC');
            if($user) {
                $email = $user->email;
                $redis = RedisConnect();
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

                            $this->Referral->where(['child_id' => $user_id])
                            ->set([
                                'status'        => 'active',
                                'updated_at'    => date('Y-m-d H:i:s'),
                            ])->update();
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
                $response->message = "User does not exist ($user_id). ";
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
            $countReferral = $this->Referral->CountAllChild(['parent_id' => $user_id]);

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

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $referral = $this->Referral->getDownlineData($user_id, false,$limit, $start);

        $where = [
            'user_id' => $user_id,
            'status_internal' => '5',
        ];
        
        $total_transaction = $this->DeviceCheck->getDevice($where, 'COUNT(check_id) as total_transaction');
        // var_dump($total_transaction);die;

        $dataUser = $this->UsersModel->getUser(['user_id' => $user_id], 'pending_balance, active_balance, (pending_balance + active_balance) as total_saving');
        

        $main_account = (object)[
            'name' => $decoded->data->name,
            'transaction' => $total_transaction->total_transaction,
            'pending_balance' => $dataUser->pending_balance,
            'active_balance' => $dataUser->active_balance,
            'total_saving' => $dataUser->total_saving,
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

    public function getWithdraw()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $withdraws = $this->UserBalance->getUserBalances(['user_id' => $user_id], 'user_balance_id,
        amount,type,status,created_at,check_id', 'user_balance_id DESC', $limit, $start);
        // var_dump($this->db->getLastQuery());
        // die;

        $response->data = $withdraws;
        $response->success = true;

        return $this->respond($response, 200);
    }

    public function getTransactionSuccess()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $where = [
            'up.user_id'    => $user_id,
            'up.type'       => 'transaction',
            'up.deleted_at' => null,
            'dc.status_internal' => '5',
        ];
        $transactionChecks = $this->UserPayouts->getTransactionUser($where, false,UserPayouts::getFieldForPayout(), false, $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;
        return $this->respond($response, 200);
    }

    public function getTransactionPending()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';
        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $status_pending = ['3','4','8']; //Seharusnya status pending
        $where = [
            'user_id'       => $user_id,
            'status'        => $status_pending,
            'deleted_at'    => null
        ];
        $whereIn = [
            'status'        => $status_pending,
        ];
        
        $transactionChecks = $this->DeviceCheck->getDeviceChecks($where,$whereIn, DeviceChecks::getFieldsForTransactionPending(), false, $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;

        return $this->respond($response, 200);
    }

    public function getAddressUser()
    {
        $response = initResponse('Outdated.');
        $response_code = 200;
        return $this->respond($response, $response_code);

        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $address = $this->UserAddress->getAddressUser(['user_id' => $user_id], UserAdresses::getFieldForAddress(), false, $limit, $start);

        $response->data = $address;
        return $this->respond($response, 200);
    }

    public function getPaymentUser()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';
        $page = ctype_digit($page) ? $page :  '1';
        $type = $this->request->getPost('type') ?? 'default';
        $type = $type == ""? 'default' : $type;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $where = [
            'user_id' => $user_id
        ];
        if($type != 'default'){
            $where += [
            'pm.type' => $type
            ];
        }

        $paymentUser = $this->UserPayment->getPaymentUser($where, UserPayments::getFieldForPayment(), false, $limit, $start);

        $response->data = $paymentUser;
        return $this->respond($response, 200);
    }

    // sudah dipindah ke api/appointment/submitAppointment
    public function submitAppoinment()
    {
        $response = initResponse('Outdated.');
        $response_code = 200;
        return $this->respond($response, $response_code);
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
        $check_id = $this->request->getPost('check_id') ?? '';

        $device_checks = $this->DeviceCheck->getDeviceChecks(['user_id' => $user_id, 'check_id' => $check_id],false, 'COUNT(check_id) as total_check');
        if ($device_checks[0]->total_check == 1) {

            $data_check = $this->Appointments->getAppoinment(['user_id' => $user_id, 'check_id' => $check_id, 'deleted_at' => null], 'COUNT(appointment_id) as total_appoinment')[0];
            if($data_check->total_appoinment >0) {
                $response->message = "Transaction was finished"; //bingung kata katanya (jika check id dan user sudah pernah konek)
                $response->success = false;
            } else {
                $data += [
                    'user_id'           => $user_id,
                    'check_id '         => $check_id,
                    'address_id  '      => $addressId,
                    'user_payment_id  ' => $paymentId,
                    'phone_owner_name ' => $nameOwner,
                    'choosen_date '     => $dateChoose,
                    'choosen_time '     => $timeChoose,
                    'created_at '       => date('Y-m-d H:i:s'),
                    'updated_at '       => date('Y-m-d H:i:s'),
                ];
                
                $this->Appointments->insert($data);
                $this->DeviceCheck->update($check_id, ['status_internal' => 3]); // on appointment

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

    public function saveAddress(){
        $response = initResponse('Outdated.');
        $response_code = 200;
        return $this->respond($response, $response_code);

        $response = initResponse();

        $addressId = (int)$this->request->getPost('address_id') ?? false;
        $districtId = $this->request->getPost('district_id') ?? '1';
        $postal_code = $this->request->getPost('postal_code') ?? 'default';
        $addressName = $this->request->getPost('address_name') ?? 'default';
        $notes = $this->request->getPost('notes') ?? 'default';
        $longitude = $this->request->getPost('longitude') ?? '';
        $latitude = $this->request->getPost('latitude') ?? '';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        

        $rules = getValidationRules('saveAddress');
        // var_dump($this->validate($rules));die;
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
            $data = [
                'user_id '	    => $user_id,
                'district_id '	=> $districtId,
                'postal_code '	=> $postal_code,
                'address_name '	=> $addressName,
                'notes '		=> $notes,
                'longitude '	=> $longitude,
                'latitude '		=> $latitude,
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->db->transStart();
            if($addressId > 0 ){
                // update
                $response->message = "Success for update address";
				$this->UserAddress->saveUpdate(['address_id' => $addressId, 'user_id' => $user_id], $data);
            } else {
                // insert
                $data += [
					'created_at' => date('Y-m-d H:i:s'),
				];

                $response->message = "Success for add address";
				$this->UserAddress->insert($data);
            }

            if ($this->db->transStatus() === FALSE) {
				$response->message = $this->db->error();
				$this->db->transRollback();
                
			} else {
				if($this->db->affectedRows() == 0){
                    $response->message = "Failed To Update (User Id Not Match)";
                } else {
                    $response->success = true;
				    $this->db->transCommit();
                }
			}
            $response_code = 200;
            $this->db->transComplete();
        }
        
        return $this->respond($response, $response_code);

    }

    public function savePaymentUser(){
        $response = initResponse();

        $userPaymentId = (int)$this->request->getPost('user_payment_id') ?? false;
        $paymentMethodId = $this->request->getPost('payment_method_id') ?? '';
        $accountNumber = $this->request->getPost('account_number') ?? 'default';
        $accountName = $this->request->getPost('account_name') ?? 'default';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        

        $rules = getValidationRules('savePaymentUser');
        // var_dump($this->validate($rules));die;
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {
            $data = [
                'user_id '	    => $user_id,
                'payment_method_id  '	=> $paymentMethodId ,
                'account_number '	=> $accountNumber,
                'account_name '	=> $accountName,
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->db->transStart();
            if($userPaymentId > 0 ){
                // update
                $response->message = "Success for update address";
				$this->UserPayment->saveUpdate( ['user_payment_id' => $userPaymentId, 'user_id' => $user_id], $data);
            } else {
                // insert
                $data += [
					'created_at' => date('Y-m-d H:i:s'),
				];

                $response->message = "Success for add payment methode";
				$this->UserPayment->insert($data);
            }

            if ($this->db->transStatus() === FALSE) {
				$response->message = $this->db->error();
				$this->db->transRollback();
                
			} else {
                if($this->db->affectedRows() == 0){
                    $response->message = "Failed To Update (User Id Not Match)";
                } else {
                    $response->success = true;
				    $this->db->transCommit();
                }
			}
            $response_code = 200;
            $this->db->transComplete();
        }
        return $this->respond($response, $response_code);

    }

    public function withdraw(){
        $response = initResponse();

        $userPaymentId = (int)$this->request->getPost('user_payment_id') ?? false;
        $amount = $this->request->getPost('amount') ?? '0';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        // var_dump($decoded);die;
        $user_id = $decoded->data->user_id;
        

        $rules = getValidationRules('withdraw');
        // var_dump($this->validate($rules));die;
        if(!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
			$response_code = 400; // bad request
        } else {

            if($amount > $decoded->data->active_balance) {
                $response->message = "Amount must less than active balance";
                $response->success = false;
                $response_code = 200;
            } else {
                $statusWithdraw = '2'; //status harus pending
                $dataUserBalance = [
                    'user_id'           => $user_id,
                    'currency'	        => 'idr' ,
                    'currency_amount'	=> $amount,
                    'convertion'	    => '1',
                    'amount'            => $amount,
                    'type'              => 'withdraw',
                    'cashflow'          => 'out',
                    'status'            => $statusWithdraw,
                    'notes'             => '',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
                $this->db->transStart();

                // insert to user_balance
                $this->UserBalance->insert($dataUserBalance);

                $user_balance_id = $this->UserBalance->insertID;

                if ($this->UserBalance->transStatus() === FALSE) {
                    $response->message = $this->db->error();
                    $this->db->transRollback();
                    
                } else {
                    $statusUserPayment = '2'; // cek status
                    $dataUserPayout = [
                        'user_id'           => $user_id,
                        'user_balance_id'   => $user_balance_id,
                        'user_payment_id'   => $userPaymentId,
                        'amount'            => $amount,
                        'type'              => 'withdraw',
                        'status'            => $statusUserPayment,
                        'created_at'        => date('Y-m-d H:i:s'),
                    ];
                }
                $this->UserPayouts->insert($dataUserPayout);
                if ($this->UserPayouts->transStatus() === FALSE) {
                    $response->message = $this->db->error();
                    $this->db->transRollback();
                } else {
                    $balance = $decoded->data->active_balance - $amount;
                    $dataUser = [
                        'active_balance'    => $balance
                    ];
                    $this->UsersModel->update($user_id,$dataUser);

                    if ($this->UsersModel->transStatus() === FALSE) {
                        $response->message = $this->db->error();
                        $this->db->transRollback();
                    } else {
                        // var_dump($this->UsersModel->getLastQuery());
                        // die;
                        $response->message = "Success";
                        $response->success = true;
                        $response_code = 200;
                        $this->db->transCommit();
                    }
                }
            }

            $this->db->transComplete();
        }
        return $this->respond($response, $response_code);

    }

    public function submission() {
        $response = initResponse();
        $response_code = 404;
        $nik = $this->request->getPost('nik') ?? '';

        $rules = getValidationRules('register_agent');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
    
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'submission,type,email,status,pin, email_verified');
            if(!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if(!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    if($user->type == 'agent') {
                        $response->message = "User is already an Agent";
                    } elseif($user->submission == 'y') {
                        $response->message = "User is already submit submission";
                    } elseif($user->pin == '') {
                        $response->message = "Please set your PIN before submission";
                    } else {
                        $photo_id = $this->request->getFile('photo_id');
                        $newName = $photo_id->getRandomName();
                        if ($photo_id->move('uploads/photo_id/', $newName)) {
                            $data = [
                                'nik' => $nik,
                                'submission' => 'y',
                                'photo_id' => $newName
                            ];
                            $this->UsersModel->update($user_id, $data);
                            $response->success = true;
                            $response->message = "Success submit submission as Agent";
                            $response_code = 200;
                        } else {
                            $response->message = "Error upload file";
                        }
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

    public function setPin() {
        $response = initResponse();
        $response_code = 404;
        $pin = $this->request->getPost('pin') ?? '';
        $pin_confirm = $this->request->getPost('pin_confirm') ?? '';

        $rules = getValidationRules('set_pin');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
    
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'pin,status,email,email_verified');
            if(!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if(!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    if($user->pin != '') {
                        $response->message = "PIN has been already set";
                    } else {
                        $encrypter = \Config\Services::encrypter();
                        $pin_encrypted =  bin2hex($encrypter->encrypt($pin));
                        $data = ['pin' => $pin_encrypted];
                        $this->UsersModel->update($user_id, $data);
                        $response->success = true;
                        $response->message = "Successfully set PIN";
                        $response_code = 200;
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

    public function updatePin() {
        $response = initResponse();
        $response_code = 404;
        $current_pin = $this->request->getPost('current_pin') ?? '';
        $new_pin = $this->request->getPost('new_pin') ?? '';
        // $new_pin_confirm = $this->request->getPost('new_pin_confirm') ?? ''; // tidak dipakai, tapi harus dikirim karena dicek di validatio rules

        $rules = getValidationRules('update_pin');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
    
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'pin,status,email,email_verified');
            if(!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if(!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } elseif($user->pin == '') {
                    $response->message = "PIN is not set yet";
                } else {
                    $encrypter = \Config\Services::encrypter();
                    $current_pin_decrypted =  $encrypter->decrypt(hex2bin($user->pin));
                    // var_dump($current_pin_decrypted);die;
                    if($current_pin != $current_pin_decrypted) {
                        $response->message = "Current PIN is wrong";
                    } elseif($current_pin == $new_pin) {
                        $response->message = "New PIN can not be the same as Current PIN";
                    } else {
                        $pin_encrypted =  bin2hex($encrypter->encrypt($new_pin));
                        $data = ['pin' => $pin_encrypted];
                        $this->UsersModel->update($user_id, $data);
                        $response->success = true;
                        $response->message = "Successfully update PIN";
                        $response_code = 200;
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

    public function checkPin() {
        $response = initResponse();
        $response_code = 404;
        $pin = $this->request->getPost('pin') ?? '';

        $rules = ['pin' => getValidationRules('pin')];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
    
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'pin,status,email');
            if(!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if(!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } elseif($user->pin == '') {
                    $response->message = "PIN is not set yet";
                } else {
                    $encrypter = \Config\Services::encrypter();
                    $pin_decrypted =  $encrypter->decrypt(hex2bin($user->pin));
                    if($pin != $pin_decrypted) {
                        $response->message = "PIN is incorrect";
                    } else {
                        $response->success = true;
                        $response->message = "PIN is correct";
                        $response_code = 200;
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

    public function getTransactionFailed(){
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        // $start = ($page - 1) * $limit;
        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $status = ['6','7'];
        $where = [
            'up.user_id'            => $user_id,
            'up.type'               => 'transaction',
            'up.deleted_at'         => null,
        ];
        $wherein = [
            'dc.status_internal'    => $status,
        ];
        $transactionChecks = $this->UserPayouts->getTransactionUser($where, $wherein,UserPayouts::getFieldForPayout(), false, $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;
        return $this->respond($response, 200);
    }

    public function validateNik(){
		$response = initResponse();
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



    /* OUTDATED API - TIDAK DIGUNAKAN LAGI */

    // sudah dipindah ke api/appointment/getAvailableDate
    public function getAvailableDate(){
        $response = initResponse('Outdated.');
        $response_code = 200;
        return $this->respond($response, $response_code);

        $where = [
            'type'  => 'date',
        ];

        $setRange = 2; // today, tomorrow and the day after tomorrow
        $listRange = [];
        $listDate = [];

        $dayofweek = date('w') + 1;
        for ($i = 0; $i <= $setRange; $i++) {
            $valuenya = afterAddDays($dayofweek, $i);
            $listRange[$i] = $valuenya;
            $listDate[$i] = getTimeDay($i);
        }
        $wherein = [
            'days'  => $listRange,
        ];
        $data = $this->AvailableDateTime->getAvailableDateTime($where, $wherein, 'status,days');

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->date = $listDate[$i];
        }
        $response->data = $data;
        $response->success = true;
        return $this->respond($response, 200);
    }
    

    // sudah dipindah ke api/appointment/getAvailableTime
    public function getAvailableTime(){
        $response = initResponse('Outdated.');
        $response_code = 200;
        return $this->respond($response, $response_code);

        $days = $this->request->getPost('days') ?? '';
        if(empty($days)) {
            $response->message = "days is required.";
        } else {

            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;

            $where = [
                'type'  => 'time',
                'days'  => $days,
            ];

            $data = $this->AvailableDateTime->getAvailableDateTime($where, false, 'status,value');
            
            $response->data = $data;
            $response->success = true;
        }
        return $this->respond($response, 200);
    }

    
    public function getReferralCode(){
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $dataUser = $this->UsersModel->getUser(['user_id' => $user_id], 'ref_code');
        $response->data = $dataUser;
        $response->success = true;
        return $this->respond($response, 200);
    }

    public function getHistoryBalance(){
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        // $start = ($page - 1) * $limit;
        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $status = ['6','7'];
        $where = [
            'user_id'            => $user_id,
            'type'               => 'bonus',
        ];
        
        $historyBalance = $this->UserBalance->getUserBalances($where,'user_balance_id, currency, currency_amount, convertion, amount, type, from_user_id, status', false, $limit, $start);

        $response->data = $historyBalance;
        $response->success = true;
        return $this->respond($response, 200);
    }
}
