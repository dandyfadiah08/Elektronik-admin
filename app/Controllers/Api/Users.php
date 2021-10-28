<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users as UserModel;
use App\Libraries\Mailer;
use App\Libraries\Xendit;
use App\Models\Appointments;
use App\Models\AvailableDateTime;
use App\Models\DeviceChecks;
use App\Models\Referrals;
use App\Models\UserBalance;
use App\Models\UserPayouts;
use App\Models\RefreshTokens;
use App\Models\UserAdresses;
use App\Models\UserPayments;
use App\Models\PaymentMethods;
use App\Models\Settings;
use Firebase\JWT\JWT;
use Hidehalo\Nanoid\GeneratorInterface;
use Hidehalo\Nanoid\Client;
use App\Libraries\Nodejs;
use DateTime;

class Users extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefreshTokens, $DeviceCheck, $Referral, $UserBalance, $UserPayment, $UserPayouts;


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
        $this->Setting = new Settings();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
        helper('user_status');
        helper("format_helper");
        helper('log');
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
        if (!$this->validate($rules)) {
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
            if ($user) {
                $this->UsersModel->update($user_id, ['notification_token' => $notification_token]);
                $response->success = true;
                $response->message = "Notification token updated. ";
            } else {
                $response->message = "User does not exist. ";
            }
        }
        return $this->respond($response, 200);
    }

    // old : 2021-10-11 (sending otp)
    public function sendEmailVerification()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        //cek dulu email ada di db atau tidak
        $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,user_id,status', 'user_id DESC');
        if ($user) {
            if ($user->status == 'banned') {
                $response->message = "Your account was banned";
            } else {
                $email = $user->email;
                $response = generateCodeOTP($email);
                if ($response->success) {
                    // kirim email
                    $email_body_data = [
                        'template' => 'email_verification',
                        'd' => (object) [
                            'name' => $user->name,
                            'otp' => $response->message
                        ],
                    ];
                    $email_body = view('email/template', $email_body_data);
                    $mailer = new Mailer();
                    $data = (object)[
                        'receiverEmail' => $email,
                        'receiverName' => $user->name,
                        'subject' => 'Email Verification Code',
                        // 'content' => "Your email verification code on " . env('app.name') . " is $response->message",
                        'content' => $email_body,
                    ];
                    $sendEmail = $mailer->send($data);
                    $response->message = $sendEmail->message;
                    if ($sendEmail->success) $response->success = true;

                    // logs
                    unset($data->content);
                    $this->log->in($user->name, 34, json_encode($data), false, $user->user_id, false);
                }
            }
        } else {
            $response->message = "User does not exist. ";
        }

        return $this->respond($response, 200);
    }

    // new : 2021-10-11 (sending link)
    public function sendEmailVerificationLink()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        //cek dulu email ada di db atau tidak
        $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,user_id,status', 'user_id DESC');
        if ($user) {
            if ($user->status == 'banned') {
                $response->message = "Your account was banned";
            } else {
                $email = $user->email;
                $response = generateEmailVerificationLink($user_id, $email);
                if ($response->success) {
                    // kirim email
                    $email_body_data = [
                        'template' => 'email_verification_link',
                        'd' => (object) [
                            'name' => $user->name,
                            'link' => $response->message
                        ],
                    ];
                    $email_body = view('email/template', $email_body_data);
                    $mailer = new Mailer();
                    $data = (object)[
                        'receiverEmail' => $email,
                        'receiverName' => $user->name,
                        'subject' => 'Email Verification',
                        'content' => $email_body,
                    ];
                    $sendEmail = $mailer->send($data);
                    $response->message = $sendEmail->message;
                    if ($sendEmail->success) $response->success = true;

                    // logs
                    unset($data->content);
                    $this->log->in($user->name, 34, json_encode($data), false, $user->user_id, false);
                    
                }
            }
        } else {
            $response->message = "User does not exist. ";
        }

        return $this->respond($response, 200);
    }

    public function changeEmail()
    {
        $response = initResponse();

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        //cek dulu email ada di db atau tidak
        $user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,name,user_id,status', 'user_id DESC');
        if ($user) {
            if ($user->status == 'banned') {
                $response->message = "Your account was banned";
            } else {
                $new_email = $this->request->getPost('email') ?? '';
                $rules = ['email' => getValidationRules('email')];
                if (!$this->validate($rules)) {
                    $errors = $this->validator->getErrors();
                    $response->message = "";
                    foreach ($errors as $error) $response->message .= "$error ";
                } else {
                    if($user->email == $new_email) {
                        $response->success = true;
                        $response->message = "No changes were made.";
                    } elseif($this->isEmailUsed($user_id, $new_email)) {
                        $response->message = "Email is already been used.";
                    } else {
                        $response = generateChangeEmailVerificationLink($user_id, $new_email);
                        if ($response->success) {
                            // kirim email
                            $email_body_data = [
                                'template' => 'change_email_verification_link',
                                'd' => (object) [
                                    'name' => $user->name,
                                    'link' => $response->message
                                ],
                            ];
                            $email_body = view('email/template', $email_body_data);
                            $mailer = new Mailer();
                            $data = (object)[
                                'receiverEmail' => $new_email,
                                'receiverName' => $user->name,
                                'subject' => 'Change Email Verification',
                                'content' => $email_body,
                            ];
                            $sendEmail = $mailer->send($data);
                            $response->message = $sendEmail->message;
                            if ($sendEmail->success) {
                                $response->success = true;
                                $response->message = "Change email verification has been sent to your new email $new_email. Please check and confirm.";
                            }
                            // logs
                            unset($data->content);
                            $this->log->in($user->name, 51, json_encode($data), false, $user->user_id, false);

                        }
                    }
                }
            }
        } else {
            $response->message = "User does not exist. ";
        }

        return $this->respond($response, 200);
    }

    private function isEmailUsed($user_id, $email)
    {
        $result = $this->UsersModel->select('user_id')
        ->where('email', $email)
        ->where('user_id!=', $user_id, false)
        ->first();
        // var_dump($result);die;
        return $result ? true : false;
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
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            //cek dulu email ada di db atau tidak
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'user_id,name,email,phone_no_verified', 'user_id DESC');
            if ($user) {
                $email = $user->email;
                $redis = RedisConnect();
                $key = "otp:$email";
                $checkCodeOTP = checkCodeOTP($key, $redis);
                if ($checkCodeOTP->success) {
                    // OTP for $email is exist
                    if ($otp == $checkCodeOTP->data['otp']) {
                        $response->success = true;
                        $response->message = "Email is verified. ";
                        $data = ['email_verified' => 'y'];

                        $selectParent = "u.notification_token, u.user_id, u.name, referral.ref_level";
                        $dataParent = $this->Referral->getReferralWithDetailParent(['child_id' => $user_id], $selectParent);
                        $this->db->transStart();

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
                        $this->db->transComplete();
                        if ($this->db->transStatus() === FALSE) {
                            // transaction has problems
                            $response->message = "Failed to perform task! #usr01a";
                        } else {
                            if ($user->phone_no_verified == 'y') {
                                // var_dump($dataParent);die;
                                foreach ($dataParent as $rowParent) {
                                    if ($rowParent->ref_level == 1) {
                                        try {
                                            $title = "Congatulation $rowParent->name";
                                            $content = "Congratulations $rowParent->name, You get 1 referral!";
                                            $notification_data = [
                                                'type'        => 'notif_activation_referal'
                                            ];

                                            $notification_token = $rowParent->notification_token;
                                            helper('onesignal');
                                            $send_notif_submission = sendNotification([$notification_token], $title, $content, $notification_data);
                                            $response->data['send_notif_submission'] = $send_notif_submission;
                                        } catch (\Exception $e) {
                                            $response->message .= " But, unable to send notification: " . $e->getMessage();
                                        }
                                    }
                                }
                            }
                            $redis->del($key);

                            // logs
                            $data_logs = (array)$user;
                            $this->log->in($user->name, 52, json_encode($data_logs), false, $user->user_id, false);
                            
                        }
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
        $merchant_id = $this->request->getPost('merchant_id') ?? null;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $referral = $this->Referral->getDownlineData($user_id, false, $limit, $start);
        $referralData = $this->Referral->countReferralByParent($user_id);

        if(!$referralData){
            $referralData = [
                'jum_user_active' => '0',
                'jum_user_pending' => '0',
            ];
            $referralData = (object)  $referralData;
        }

        $where = [
            'user_id' => $user_id,
            'status_internal' => '5',
            'merchant_id' => $merchant_id,
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
            'active_referral' => $referralData->jum_user_active,
            'pending_referral' => $referralData->jum_user_pending,
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
        $merchant_id = $this->request->getPost('merchant_id') ?? null;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;
        $where = [
            'up.user_id'            => $user_id,
            'up.type'               => 'transaction',
            'up.deleted_at'         => null,
            'dc.status_internal'    => '5',
            'dc.merchant_id'        => $merchant_id,
        ];
        $transactionChecks = $this->UserPayouts->getTransactionUser($where, false, UserPayouts::getFieldForPayout(), "up.user_payout_id DESC", $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;
        $response->message = "OK";
        return $this->respond($response, 200);
    }

    public function getTransactionPending()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';
        $merchant_id = $this->request->getPost('merchant_id') ?? null;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $status_pending = ['3', '4', '8']; //Seharusnya status pending
        $where = [
            'user_id'       => $user_id,
            'deleted_at'    => null,
            'merchant_id'   => $merchant_id,
        ];
        $whereIn = [
            'status_internal'        => $status_pending,
        ];

        $transactionChecks = $this->DeviceCheck->getDeviceChecks($where, $whereIn, DeviceChecks::getFieldsForTransactionPending(), "check_id DESC", $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;
        $response->message = "OK";

        return $this->respond($response, 200);
    }

    public function getTransactionFailed()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';
        $merchant_id = $this->request->getPost('merchant_id') ?? null;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $status_pending = ['6', '7']; //Seharusnya status pending
        $where = [
            'user_id'       => $user_id,
            'deleted_at'    => null,
            'merchant_id'   => $merchant_id,
        ];
        $whereIn = [
            'status_internal'        => $status_pending,
        ];

        $transactionChecks = $this->DeviceCheck->getDeviceChecks($where, $whereIn, DeviceChecks::getFieldsForTransactionPending(), "check_id DESC", $limit, $start);
        $response->data = $transactionChecks;
        $response->success = true;
        $response->message = "OK";

        return $this->respond($response, 200);
    }

    public function getTransactionChecking()
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

        $status_pending = ['1', '2']; //Seharusnya status pending
        $where = [
            'user_id'       => $user_id,
            'deleted_at'    => null
        ];
        $whereIn = [
            'status_internal'        => $status_pending,
        ];

        $transactionChecks = $this->DeviceCheck->getDeviceChecks($where, $whereIn, DeviceChecks::getFieldsForTransactionPending(), "check_id DESC", $limit, $start);
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
        $type = $type == "" ? 'default' : $type;

        $start = !$limit ? 0 : ($page - 1) * $limit;

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $where = [
            'user_id' => $user_id,
            'up.deleted_at' => null,
        ];
        if ($type != 'default') {
            $where += [
                'pm.type' => $type
            ];
        }

        $paymentUser = $this->UserPayment->getPaymentUser($where, UserPayments::getFieldForPayment(), "up.updated_at DESC", $limit, $start);

        $response->data = $paymentUser;
        return $this->respond($response, 200);
    }

    public function savePaymentUser()
    {
        $response = initResponse();

        $rules = getValidationRules('savePaymentUser');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $userPaymentId = (int)$this->request->getPost('user_payment_id') ?? false;
            $paymentMethodId = $this->request->getPost('payment_method_id') ?? '';
            $accountNumber = $this->request->getPost('account_number') ?? 'default';
            $accountName = $this->request->getPost('account_name') ?? 'default';
            
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'name,user_id,active_balance,type,email,status,pin,email_verified');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    $PaymentMethod = new PaymentMethods();
                    $payment_method = $PaymentMethod->getPaymentMethod(['payment_method_id' => $paymentMethodId], 'name');
                    if (!$payment_method) {
                        $response->message = "Payment Method Id is invalid ($paymentMethodId)";
                    } else {
                        // jika pakai metode validatePaymentUser() maka tidaka perlu $valid_bank_detail
                        // $Xendit = new Xendit();
                        // $valid_bank_detail = $Xendit->validate_bank_detail($payment_method->name, $accountNumber); // first hit status=PENDING, need callback or cronjob to get the result
                        $data = [
                            'user_id'           => $user_id,
                            'payment_method_id' => $paymentMethodId,
                            'account_number'    => $accountNumber,
                            'account_name'      => $accountName,
                            'updated_at'        => date('Y-m-d H:i:s'),
                        ];

                        $this->db->transStart();
                        if ($userPaymentId > 0) {
                            // update
                            $response->message = "Success for update payment";
                            $affected = $this->UserPayment->saveUpdate(['user_payment_id' => $userPaymentId, 'user_id' => $user_id], $data);
                            $log_cat = 54;
                        } else {
                            // insert
                            $data += [
                                'status'        => 'active',
                                'created_at'    => date('Y-m-d H:i:s'),
                            ];
                            
                            $response->message = "Success for add payment methode";
                            $affected = $this->UserPayment->insert($data);
                            $log_cat = 53;
                        }
                        $this->db->transComplete();

                        if ($this->db->transStatus() === FALSE) {
                            $response->message = "Failed to perform task! #users01a.\n" . $this->db->error();
                        } else {
                            if (!$affected && $userPaymentId > 0) {
                                $response->message = "Failed to update (for user id $user_id)";
                            } else {
                                $response->success = true;

                                // logs
                                $data_logs = $data;
                                $this->log->in($user->name, $log_cat, json_encode($data_logs), false, $user->user_id, false);
                            }
                        }
                    }
                    $response_code = 200;
                }
            }
        }

        writeLog("api-users", "savePayementUser\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, $response_code);
    }

    public function withdraw()
    {
        $response = initResponse();
        $response_code = 200;

        $userPaymentId = (int)$this->request->getPost('user_payment_id') ?? false;
        $amount = $this->request->getPost('amount') ?? '0';

        $rules = getValidationRules('withdraw');
        // var_dump($this->validate($rules));die;
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'name,user_id,active_balance,type,email,status,pin,email_verified');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    $active_balance = (int)$user->active_balance;
                    $amount = (int)$amount;
                    $setting_db = $this->Setting->getSetting(['_key' => 'min_withdraw'], 'setting_id,val');
                    $minimalWithdraw = $setting_db->val;

                    if ($amount > $active_balance) {
                        $response->message = "Amount must be less than active balance";
                    } elseif ($amount < $minimalWithdraw) {
                        $response->message = "Amount must be at least IDR " . toPrice($minimalWithdraw);
                    } else {
                        $remain = $active_balance - $amount;
                        $statusWithdraw = '2'; //status harus pending
                        $dataUserBalance = [
                            'user_id'           => $user_id,
                            'currency'          => 'idr',
                            'currency_amount'   => $amount,
                            'convertion'        => '1',
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

                        $dataUpdate = [
                            'active_balance' => $remain,
                        ];

                        $this->UsersModel->update($user_id, $dataUpdate);

                        // $transaction_ref =hash_hmac('sha256', $user_balance_id.'-'.date('YmdHis'), env('encryption.key'));

                        $client = new Client();
                        $transaction_ref = date('Y') . $client->formattedId('0123456789abcdefhijkmnpqrstuvwxyz', 10);

                        // 6d12eced0bd89ade2dc5d772472c533e62c1be406d164ab2a914c6e9bc1ea7cb
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
                                'withdraw_ref'      => $transaction_ref,
                            ];
                        }
                        $this->UserPayouts->insert($dataUserPayout);
                        $this->db->transComplete();
                        if ($this->UserPayouts->transStatus() === FALSE) {
                            $response->message = $this->db->error();
                            $this->db->transRollback();
                            $response->message = $this->db->error();
                        } else {
                            $response->message = "Withdrawal successfully requested. Withdrawals will be processed during business hours Monday to Friday at 08.00 - 17.00 WIB.";
                            $response->success = true;
                            $response_code = 200;
                            $nodejs = new Nodejs();
                            $nodejs->emit('new-withdraw', [
                                'withdraw_ref'      => $transaction_ref,
                            ]);

                            // logs
                            $data = $dataUserPayout;
                            $this->log->in($user->name, 33, json_encode($data), false, $user->user_id, false);
                        }
                    }
                }
            }
        }
        writeLog(
            "api",
            "Withdraw\n"
                . json_encode($this->request->getPost())
                . json_encode($response)
        );
        return $this->respond($response, $response_code);
    }

    public function submission()
    {
        $response = initResponse();
        $response_code = 404;
        $nik = $this->request->getPost('nik') ?? '';

        $rules = getValidationRules('register_agent');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;

            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'submission,type,email,status,pin,email_verified,user_id,name,phone_no');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    if ($user->type == 'agent') {
                        $response->message = "User is already an Red Member";
                    } elseif ($user->submission == 'y') {
                        $response->message = "User is already submit submission";
                    } elseif ($user->pin == '') {
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
                            $response->message = "Success submit submission as Red Member";
                            $response_code = 200;

                            $nodejs = new Nodejs();
                            $nodejs->emit('new-submission', [
                                'name' => $user->name,
                                'phone' => $user->phone_no,
                            ]);

                            // logs
                            unset($data['photo_id']);
                            $data_logs = $data;
                            $data_logs += ['user_id' => $user->user_id];
                            $this->log->in($user->name, 55, json_encode($data_logs), false, $user->user_id, false);
                        } else {
                            $response->message = "Error upload file";
                        }
                    }
                }
            }
        }

        writeLog("api-users", "submission\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, $response_code);
    }

    public function setPin()
    {
        $response = initResponse();
        $response_code = 404;
        
        $rules = getValidationRules('set_pin');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $pin = $this->request->getPost('pin') ?? '';
            $pin_confirm = $this->request->getPost('pin_confirm') ?? '';

            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;

            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'user_id,name,pin,status,email,email_verified');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user, true);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    if ($user->pin != '') {
                        $response->message = "PIN has been already set";
                    } else {
                        $encrypter = \Config\Services::encrypter();
                        $pin_encrypted =  bin2hex($encrypter->encrypt($pin));
                        $data = ['pin' => $pin_encrypted];
                        $this->UsersModel->update($user_id, $data);
                        $response->success = true;
                        $response->message = "Successfully set PIN";
                        $response_code = 200;

                        // logs
                        $this->log->in($user->name, 56, json_encode(['user_id' => $user->user_id]), false, $user->user_id, false);
                    }
                }
            }
        }

        // SENSITIVE
        // writeLog("api-users", "setPin\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, $response_code);
    }

    public function updatePin()
    {
        $response = initResponse('Oops. Please try again.', false, [
            'pin_check_lock' => false,
            'pin_change_lock' => false,
        ]);
        $response_code = 404;
        $current_pin = $this->request->getPost('current_pin') ?? '';
        $new_pin = $this->request->getPost('new_pin') ?? '';
        // $new_pin_confirm = $this->request->getPost('new_pin_confirm') ?? ''; // tidak dipakai, tapi harus dikirim karena dicek di validatio rules

        $rules = getValidationRules('update_pin');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;

            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'user_id,name,pin,status,email,email_verified,pin_check_lock,pin_change_lock');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user, true);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } elseif ($user->pin == '') {
                    $response->message = "PIN is not set yet";
                } else {
                    $pin_change_limit = intval(env('users.pin_change_limit'));
                    $pin_change_lock = intval($user->pin_change_lock) + 1;
                    if ($pin_change_lock >= $pin_change_limit) {
                        $response->data['pin_change_lock'] = true;
                        $tomorrow = new DateTime('+1 day');
                        $response->message = "Pin is incorrect. Change pin is disabled untill " . $tomorrow->format('Y-m-d');

                        // count incorrect tries
                        $this->UsersModel->where(['user_id' => $user_id])
                            ->set('pin_change_lock', 'pin_change_lock+1', false)
                            ->update();
                    } else {
                        $encrypter = \Config\Services::encrypter();
                        $current_pin_decrypted =  $encrypter->decrypt(hex2bin($user->pin));
                        // var_dump($current_pin_decrypted);die;
                        if ($current_pin != $current_pin_decrypted) {
                            $response->message = "Current PIN is incorrect. Limit " . ($pin_change_limit - $pin_change_lock) . " more " . ($pin_change_lock == $pin_change_limit - 1 ? "try." : "tries.");

                            // count incorrect tries
                            $this->UsersModel->where(['user_id' => $user_id])
                                ->set('pin_change_lock', 'pin_change_lock+1', false)
                                ->update();
                        } elseif ($current_pin == $new_pin) {
                            $response->message = "New PIN can not be the same as Current PIN";
                        } else {
                            $pin_encrypted =  bin2hex($encrypter->encrypt($new_pin));
                            $data = ['pin' => $pin_encrypted];
                            $this->UsersModel->update($user_id, $data);
                            $response->success = true;
                            $response->message = "Successfully update PIN";
                            $response_code = 200;

                            // logs
                            $this->log->in($user->name, 57, json_encode(['user_id' => $user->user_id]), false, $user->user_id, false);

                        }
                    }
                }
            }
        }

        // SENSITIVE
        // writeLog("api-users", "updatePin\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, $response_code);
    }

    public function checkPin()
    {
        $response = initResponse('Oops. Please try again.', false, [
            'pin_check_lock' => false,
            'pin_change_lock' => false,
        ]);
        $response_code = 404;
        $pin = $this->request->getPost('pin') ?? '';

        $rules = ['pin' => getValidationRules('pin')];
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;

            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'pin,status,email,pin_check_lock,pin_change_lock');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user, true);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } elseif ($user->pin == '') {
                    $response->message = "PIN is not set yet";
                    $response_code = 201;
                } else {
                    $pin_check_limit = intval(env('users.pin_check_limit'));
                    $pin_check_lock = intval($user->pin_check_lock) + 1;
                    if ($pin_check_lock >= $pin_check_limit) {
                        $response->data['pin_check_lock'] = true;
                        $tomorrow = new DateTime('+1 day');
                        $response->message = "Pin is incorrect. Check pin is disabled untill " . $tomorrow->format('Y-m-d');

                        // count incorrect tries
                        $this->UsersModel->where(['user_id' => $user_id])
                            ->set('pin_check_lock', 'pin_check_lock+1', false)
                            ->update();
                    } else {
                        $encrypter = \Config\Services::encrypter();
                        $pin_decrypted =  $encrypter->decrypt(hex2bin($user->pin));
                        if ($pin != $pin_decrypted) {
                            $response->message = "PIN is incorrect. Limit " . ($pin_check_limit - $pin_check_lock) . " more " . ($pin_check_lock == $pin_check_limit - 1 ? "try." : "tries.");
                            $response_code = 200;

                            // count incorrect tries
                            $this->UsersModel->where(['user_id' => $user_id])
                                ->set('pin_check_lock', 'pin_check_lock+1', false)
                                ->update();
                        } else {
                            $response->success = true;
                            $response->message = "PIN is correct";
                            $response_code = 200;
                        }
                    }
                }
            }
        }

        return $this->respond($response, $response_code);
    }

    public function checkPinStatus()
    {
        $response = initResponse('Oops. Please try again.', false, [
            'pin_check_lock' => false,
            'pin_check_lock_text' => '',
            'pin_change_lock' => false,
            'pin_change_lock_text' => '',
            'pin_is_set' => true,
            'pin_is_set_text' => 'Pin is set.'
        ]);

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $user = $this->UsersModel->getUser(['user_id' => $user_id], 'pin,status,email,pin_check_lock,pin_change_lock');
        if (!$user) {
            $response->message = "User not found ($user_id)";
        } else {
            $user_status = doUserStatusCondition($user, true);
            if (!$user_status->success) {
                // user not active
                $response->message = $user_status->message;
            } else {
                $response->success = true;
                $response->message = "OK";
                $tomorrow = new DateTime('+1 day');
                $pin_check_limit = intval(env('users.pin_check_limit'));
                $pin_check_lock = intval($user->pin_check_lock);
                $pin_change_limit = intval(env('users.pin_change_limit'));
                $pin_change_lock = intval($user->pin_change_lock);
                if ($pin_check_lock >= $pin_check_limit) {
                    $response->data['pin_check_lock'] = true;
                    $response->data['pin_check_lock_text'] = "Check pin is disabled untill " . $tomorrow->format('Y-m-d');
                }
                if ($pin_change_lock >= $pin_change_limit) {
                    $response->data['pin_change_lock'] = true;
                    $response->data['pin_change_lock_text'] = "Change pin is disabled untill " . $tomorrow->format('Y-m-d');
                }
                if ($user->pin == '') {
                    $response->data['pin_is_set'] = false;
                    $response->data['pin_is_set_text'] = "PIN is not set yet";
                }
            }
        }

        return $this->respond($response);
    }

    public function validateNik()
    {
        $response = initResponse();
        // $nik = $this->request->getPost('nik') ?? '';
        $rules = getValidationRules('validate_nik');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
        } else {
            $response->message = "Valid";
            $response->success = true;
        }
        return $this->respond($response, 200);
    }

    public function getReferralCode()
    {
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

    public function getHistoryBalance()
    {
        $response = initResponse();

        $limit = $this->request->getPost('limit') ?? false;
        $page = $this->request->getPost('page') ?? '1';
        $page = ctype_digit($page) ? $page :  '1';

        // $start = ($page - 1) * $limit;
        $start = !$limit ? 0 : ($page - 1) * $limit;

        $type = $this->request->getPost('type') ?? 'default';
        $status = $this->request->getPost('status') ?? 'default';
        if ($type == "") $type = 'default';
        if ($status == "") $status = 'default';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $where = [
            'ub.user_id'   => $user_id,
        ];
        if ($type != 'default') {
            $where += [
                'ub.cashflow' => $type, // in & out
            ];
        }

        if ($status != 'default') {
            $where += [
                'ub.status' => $status, // 1 = success, 2 = pending, 3 = failed
            ];
        }
        $typeTransaction = [
            'bonus', 'withdraw'
        ];
        $whereIn = [
            'ub.type'      => $typeTransaction,
        ];
        $select = "ub.user_id, ub.user_balance_id, ub.amount, ub.type AS type_balance, ub.cashflow, ub.status, dc.check_code, ub.created_at, ub.updated_at, ub.notes";
        $data = array();

        $historyBalance = $this->UserBalance->getHistoryBalance($where, $whereIn, $select, 'ub.user_balance_id DESC', $limit, $start);
        helper("general_status_helper");
        foreach ($historyBalance as $row) {
            $row->status_string = getUserBalanceStatus($row->status);
            $arrayString = (array)$row;
            ksort($arrayString);
            $data[] = (object)$arrayString;
            // $data[] = $row;
        }
        // var_dump($historyBalance);
        // die;
        $response->data = $data;
        $response->success = true;
        return $this->respond($response, 200);
    }

    public function validatePaymentUser()
    {
        $response = initResponse();

        $account_number = $this->request->getPost('account_number') ?? '';
        $bank_code = $this->request->getPost('bank_code') ?? '';

        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;


        $rules = getValidationRules('validatePaymentUser');
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response->message = "";
            foreach ($errors as $error) $response->message .= "$error ";
            $response_code = 400; // bad request
        } else {
            $Xendit = new Xendit();
            $valid_bank_detail = $Xendit->validate_bank_detail($bank_code, $account_number);
            if ($valid_bank_detail->success) {
                $response->success = $valid_bank_detail->data->status == 'SUCCESS';
                $response->data = $valid_bank_detail->data;
            } else {
                // generate custom failure reason
                $response->data['failure_reason'] = "UNABLE_TO_CHECK";
            }
            $response->message = "OK";
            $response_code = 200;
        }

        writeLog("api-users", "validatePaymentUser\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, $response_code);
    }

    public function deletePaymentUser()
    {
        $response = initResponse();
        $userPaymentId = (int)$this->request->getPost('user_payment_id') ?? false;
        if(!$userPaymentId) {
            $response->message = "user_payment_id is required.";
        } else {
            $header = $this->request->getServer(env('jwt.bearer_name'));
            $token = explode(' ', $header)[1];
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            $user_id = $decoded->data->user_id;
            $user = $this->UsersModel->getUser(['user_id' => $user_id], 'name,user_id,active_balance,type,email,status,pin,email_verified');
            if (!$user) {
                $response->message = "User not found ($user_id)";
            } else {
                $user_status = doUserStatusCondition($user);
                if (!$user_status->success) {
                    // user not active
                    $response->message = $user_status->message;
                } else {
                    $where = [
                        'user_payment_id' => $userPaymentId,
                        'user_id' => $user_id,
                    ];
                    $data = [
                        'updated_at' => date('Y-m-d H:i:s'),
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ];
                    $user_payment = $this->UserPayment->where($where)->get()->getResult();
                    // var_dump($user_payment);die;
                    if(count($user_payment) < 1) {
                        $response->message = "User Payment not found ($userPaymentId)";
                    } else {
                        $this->db->transStart();
                        $affected = $this->UserPayment->saveUpdate($where, $data);
                        $this->db->transComplete();
    
                        if (!$affected || $this->db->transStatus() === FALSE) {
                            $response->message = "Failed to perform task! #usersDlt01a.\n" . $this->db->error();
                        } else {
                            $response->success = true;
                            $response->message = "Success to delete payment user";
    
                            // logs
                            unset($user_payment[0]->status);
                            $data += $where;
                            $data += (array)$user_payment[0];
                            $this->log->in($user->name, 58, json_encode($data), false, $user->user_id, false);
                        }
                    }
                }
            }
        }

        writeLog("api-users", "deletePaymentuser\n" . json_encode($this->request->getPost()) . "\n" . json_encode($response));
        return $this->respond($response, 200);
    }

    public function getMinimalWithdraw()
    {
        $response = initResponse();
        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $minimalWithdraw = 1;
        $setting_db = $this->Setting->getSetting(['_key' => 'min_withdraw'], 'setting_id,val');
        $minimalWithdraw = $setting_db->val;
        $response->data = ['minimal_withdraw' => $minimalWithdraw];


        return $this->respond($response, 200);
    }

    public function detailUserBalance()
    {
        $response = initResponse();
        $header = $this->request->getServer(env('jwt.bearer_name'));
        $token = explode(' ', $header)[1];
        $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
        $user_id = $decoded->data->user_id;

        $user_balance_id = $this->request->getPost('user_balance_id');

        $where = [
            'ub.user_id'           => $user_id,
            'ub.user_balance_id'   => $user_balance_id,
        ];
        $select = "ub.user_id, ub.user_balance_id, ub.amount, ub.type AS type_balance, ub.cashflow, ub.from_user_id, ub.status, dc.check_code, dc.imei, dc.brand, dc.model, dc.type, dc.storage, dc.os, ub.created_at, ub.notes, up.withdraw_ref";
        $data = array();

        $historyBalance = $this->UserBalance->getHistoryBalance($where, false, $select, false);
        helper("general_status_helper");
        foreach ($historyBalance as $row) {
            $row->status_string = getUserBalanceStatus($row->status);

            $arrayString = (array)$row;
            ksort($arrayString);
            $data[] = (object)$arrayString;
            // $data[] = $row;
        }
        // var_dump($historyBalance);
        // die;
        $response->data = $data;
        $response->success = true;
        return $this->respond($response, 200);
    }
}
