<?php

namespace App\Libraries;

use App\Models\CommissionRate;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\Referrals;
use App\Models\UserBalance;
use App\Models\UserPayoutDetails;
use App\Models\UserPayouts;
use App\Models\Users;
use App\Libraries\Log;
use App\Libraries\Mailer;
use App\Libraries\Nodejs;
use App\Libraries\FirebaseCoudMessaging;

class PaymentsAndPayouts
{

    protected $DeviceCheck, $DeviceCheckDetail, $UserBalance, $UserPyout, $UserPayoutDetail, $Referral, $CommissionRate, $User, $log;

    public function __construct()
    {
        $this->DeviceCheck = new DeviceChecks();
        $this->DeviceCheckDetail = new DeviceCheckDetails();
        $this->UserBalance = new UserBalance();
        $this->UserPayout = new UserPayouts();
        $this->UserPayoutDetail = new UserPayoutDetails();
        $this->Referral = new Referrals();
        $this->User = new Users();
        $this->log = new Log();
        helper('rest_api');
    }

    /*
    @param $device_check object
    @return $response object
    */
    function proceedPaymentLogic($device_check)
    {
        // #belum selesai
        $response = initResponse();
        $this->db = \Config\Database::connect();
        $this->db->transStart();

        // update device_check status_internal
        $this->DeviceCheck->update($device_check->check_id, ['status_internal' => 4]);

        // insert row user_balance type=transaction cashflow=in status=2 (pending)
        $amount = $this->insertBalance($device_check); // 'transaction-in'

        // insert row user_balance type=transaction cashflow=out status=2 (pending)
        $user_balance_id = $this->insertBalance($device_check, 'transaction-out');

        // insert row user_payouts type=transaction status=2 (pending)
        // $amount = 10000;
        $user_payout_id = $this->insertPayout($device_check, $amount, $user_balance_id);

        // insert row user_payout_details user_payout_details_id=user_payout_id
        $user_payout_detail_id = $this->insertPayoutDetail($device_check, $amount, $user_payout_id);
        // var_dump([$user_payout_id,$user_payout_detail_id]);die;

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #pap01l";
        } else {
            $response->success = true;
            $response->message = "Successfully <b>proceed payment</b> for <b>$device_check->check_code</b>";
            $data = [];

            // hit API payment gateway
            $xendit = new Xendit();
            $payment_gateway_response = $xendit->create_disbursements($device_check->check_code, $amount, $device_check->bank_code, $device_check->account_number, $device_check->account_name, "Sell Device Transfer $device_check->check_code");

            if ($payment_gateway_response->success) {
                // update user_payout_details with $user_payout_id
                $data_update = [
                    'status'                => $payment_gateway_response->data->status,
                    'user_id'                => $payment_gateway_response->data->user_id,
                    'external_id'            => $payment_gateway_response->data->external_id,
                    'amount'                => $payment_gateway_response->data->amount,
                    'bank_code'                => $payment_gateway_response->data->bank_code,
                    'account_holder_name'    => $payment_gateway_response->data->account_holder_name,
                    'description'            => $payment_gateway_response->data->disbursement_description,
                    'id'                    => $payment_gateway_response->data->id,
                    'updated_at'            => date('Y-m-d H:i:s'),
                ];
                $data += $data_update;
                $this->updatePayoutDetail($user_payout_detail_id, $data_update);
                if ($payment_gateway_response->data->status == 'COMPLETED') $this->updatePaymentSuccess($device_check->check_id);
            } else {
                // ngapain ya
                $response->message .= ". But payment gateway has problems occured.";
                $response->data['errors'] = $payment_gateway_response->data;
                $data['errors'] = $response->data['errors'];
            }

            $data['data'] = $device_check;
            $log_cat = 7;
            $this->log->in(session()->username, $log_cat, json_encode($data), session()->admin_id, $device_check->user_id, $device_check->check_id);
        }

        return $response;
    }

    /**
     * @param   object      $device_check
     * @param   string      $cashflow : transaction-in, transaction-out, bonus-in
     * @param   string      $type : *transaction, bonus 
     * @param   int         $status : *2 
     * @param   boolean/int $from_child or int : int passed with user_id
     * @return  int         $out : $amount, $user_payout_id
     */
    public function insertBalance($device_check, $cashflow = 'transaction-in', $type = 'transaction', $status = 2, $from_child = false, $bonus = 0, $admin_name = false, $notes = 'Agent Bonus')
    {
        $currency = 'idr';
        $convertion = 1;
        if ($type == 'bonus' || $type == 'agentbonus') $currency_amount = $bonus;
        else $currency_amount = $device_check->price;
        $amount = $currency_amount * $convertion;
        $now = date('Y-m-d H:i:s');
        $data_user_balance = [
            'user_id'           => $from_child ? $from_child : $device_check->user_id,
            'from_user_id'      => $device_check->user_id,
            'currency'          => $currency,
            'convertion'        => $convertion,
            'currency_amount'   => $currency_amount,
            'amount'            => $amount,
            'type'              => $type,
            'check_id'          => $device_check->check_id,
            'status'            => $status,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        $output_type = 'amount';
        if ($cashflow == 'transaction-in') {
            $data_user_balance += [
                'cashflow'    => 'in',
                'notes'        => 'Sell Device Income',
            ];
        } elseif ($cashflow == 'transaction-out') {
            $data_user_balance += [
                'cashflow'    => 'out',
                'notes'        => 'Sell Device Transfer',
            ];
            $output_type = 'id';
        } elseif ($cashflow == 'bonus-in') {
            $data_user_balance += [
                'cashflow'    => 'in',
                'notes'        => 'Sell Device Commission',
            ];
            $output_type = 'id';
        } elseif ($cashflow == 'agentbonus-in') {
            $data_user_balance += [
                'cashflow'    => 'in',
                'notes'        => htmlentities($notes),
                'created_by'    => $admin_name,
                'updated_by'    => $admin_name,
            ];
            $output_type = 'id';
        }

        $this->db = \Config\Database::connect();
        $this->UserBalance->insert($data_user_balance);
        $out = $output_type == 'amount' ? $amount : $this->db->insertID();
        return $out;
    }

    /*
    @param $device_check object
    @param $amount int
    @param $user_balance_id int
    @return $user_payout_id int 
    */
    public function insertPayout($device_check, $amount, $user_balance_id, $type = 'transaction', $status = 2)
    {
        $now = date('Y-m-d H:i:s');
        $data_user_payout = [
            'user_id'            => $device_check->user_id,
            // 'user_payment_id'	=> $device_check->user_payment_id,
            'user_balance_id'    => $user_balance_id,
            'amount'            => $amount,
            'type'                => $type,
            'check_id'            => $device_check->check_id,
            'status'            => $status,
            'created_by'        => 'system',
            'created_at'        => $now,
            'updated_by'        => 'system',
            'updated_at'        => $now,
        ];
        $this->db = \Config\Database::connect();
        $this->UserPayout->insert($data_user_payout);
        return $this->db->insertID();
    }

    /*
    @param $device_check object
    @param $amount int
    @param $user_balance_id int
    @return $user_payout_id int 
    */
    public function insertPayoutDetail($device_check, $amount, $user_payout_id)
    {
        $now = date('Y-m-d H:i:s');
        $data_user_payout_detail = [
            'user_payout_detail_id'    => $user_payout_id,
            'user_payout_id'        => $user_payout_id,
            'type'                    => 'xendit',
            'amount'                => $amount,
            'bank_code'                => $device_check->bank_code,
            'account_number'        => $device_check->account_number,
            'account_holder_name'    => $device_check->account_name,
            'status'                => 'Inserted',
            'description'            => "Sell Device Transfer $device_check->check_code",
            'created_at'            => $now,
            'updated_at'            => $now,
        ];
        $this->UserPayoutDetail->insert($data_user_payout_detail);
        return $user_payout_id;
    }

    /*
    @param $key int or array
    @param $data_update array
    @return void 
    */
    public function updatePayoutDetail($key, $data_update)
    {
        if (is_array($key))  $this->UserPayoutDetail->where($key)->set($data_update)->update();
        else $this->UserPayoutDetail->update($key, $data_update);
    }

    /*
    @param $check_id int
    @return $response object 
    if success, $response->data contains $device_check
    */
    public function updatePaymentSuccessValidation($check_id)
    {
        $response = initResponse();
        $select = 'dc.check_id,dc.user_id,check_detail_id,dc.price,upa.user_payout_id,dc.type_user,dc.fcm_token,dc.merchant_id';
        // $select for email
        $select .= ',check_code,brand,model,storage,imei,dc.type as dc_type,u.name,customer_name,customer_email,dcd.account_number,dcd.account_name,pm.name as pm_name,ub.notes as ub_notes,ub.type as ub_type,ub.currency,ub.currency_amount,check_code as referrence_number';
        $where = array('dc.check_id' => $check_id, 'dc.status_internal' => 4, 'dc.deleted_at' => null);
        $device_check = $this->DeviceCheck->getDeviceDetailPayment($where, $select);
        if (!$device_check) {
            $response->message = "Invalid check_id $check_id";
        } else {
            $response->success = true;
            $response->message = 'OK';
            $response->data = $device_check;
        }
        return $response;
    }

    /*
    @param $check_id int
    @return $response object 
    */
    public function updatePaymentSuccess($check_id)
    {
        // #belum selesai
        $response = initResponse();
        $validation = $this->updatePaymentSuccessValidation($check_id);
        if (!$validation->success) {
            $response = $validation;
        } else {
            $device_check = $validation->data;
            $check_id = $device_check->check_id;
            $user_id = $device_check->user_id;
            $hasError = false;

            $this->db = \Config\Database::connect();
            $this->db->transStart();

            // update device_check status_internal
            $this->DeviceCheck->update($device_check->check_id, ['status_internal' => 5]);

            // update device_check status_internal
            $this->DeviceCheckDetail->update($device_check->check_detail_id, ['payment_date' => date('Y-m-d H:i:s')]);

            // update where(check_id, user_id) user_balance.status=1 (success) [cashflow=in], [cashflow=out]
            $this->UserBalance->where([
                'check_id'  => $check_id,
                'user_id'   => $user_id,
                'type'      => 'transaction',
            ])->set(['status' => 1])
                ->update();

            // update where(check_id) user_payouts.status=1 (success)
            $this->UserPayout->update($device_check->user_payout_id, ['status' => 1]);

            if ($device_check->type_user == 'agent' && (int)$device_check->merchant_id < 1) {
                // hitung $bonus (berdasarkan tradein.price, commission_rate, level=0)
                $commision_rate_check = PaymentsAndPayouts::getCommisionRate($device_check->price);
                if (!$commision_rate_check->success) {
                    $hasError = true;
                    $response->message = $commision_rate_check->message;
                } else {
                    $commision_rate = $commision_rate_check->data;
                    $bonus = (int)$commision_rate->commission_1;
                    // insert row user_balance type=bonus cashflow=in status=1 (success)
                    $user_balance_id = $this->insertBalance($device_check, 'bonus-in', 'bonus', 1, false, $bonus);

                    // cek user_balance.type=bonus di bulan ini (tradein.created_at) dan user_id ini
                    $user_balance_this_month_check = PaymentsAndPayouts::getUserBalanceThisMonth($user_id);
                    if ($user_balance_this_month_check->success) {
                        // tidak dipakai, hanya menunjukkan ada
                        $user_balance = $user_balance_this_month_check->data;
                        foreach ($user_balance as $ub) {
                            // update where(user_balance_id) users_balance.status=1 (success)
                            $this->UserBalance->update($ub->user_balance_id, ['status' => 1]);
                        }

                        // cek users.pending_balance, ditambahkan ke $bonus dan 0 kan pending_balance
                        $user = $this->User->getUser(['user_id' => $user_id], 'pending_balance');
                        if ($user) {
                            if ((int)$user->pending_balance > 0) {
                                $bonus += (int)$user->pending_balance;
                                $this->updatePendingBalance($user_id, 0);
                            }
                        }
                    }

                    // update where(user_id) users.active_balance (ditambah $bonus)
                    $this->updateActiveBalance($user_id, $bonus);
                }
            }

            // jika ada parent (lv 1 / lv 2), untuk user_id parent :
            $parents = $this->Referral->getActiveReferralByChildId($user_id, 'referral.parent_id,referral.ref_level, u.name, u.notification_token');
            if (count($parents) > 0) {
                // hitung $bonus (berdasarkan tradein.price, commission_rate, referrals.level[1 atau 2])
                $commision_rate_check = PaymentsAndPayouts::getCommisionRate($device_check->price);
                if (!$commision_rate_check->success) {
                    $hasError = true;
                    $response->message = $commision_rate_check->message;
                } else {
                    $commision_rate = $commision_rate_check->data;
                    foreach ($parents as $parent) {
                        $bonus = 0;
                        if ($parent->ref_level == 1) $bonus = (int)$commision_rate->commission_2; // komisi level 1
                        elseif ($parent->ref_level == 2) $bonus = (int)$commision_rate->commission_3; // komisi level 2

                        // update referrals transaction+1, saving+$bonus where(parent_id and child_id)
                        $this->updateReferralSavingAndTransaction($parent->parent_id, $user_id, $bonus);

                        // cek parent ini jika bulan ini ada transaksi :
                        $transaction_this_month_check = PaymentsAndPayouts::getTransactionThisMonth($parent->parent_id);
                        // var_dump($transaction_this_month_check);
                        if ($transaction_this_month_check->success) {
                            // insert row user_balance type=bonus cashflow=in from_user_id=Referrals.child_id status=1 (success)
                            $user_balance_id = $this->insertBalance($device_check, 'bonus-in', 'bonus', 1, $parent->parent_id, $bonus);

                            // update users.active_balance (ditambah $bonus) where(Referrals.parent_id)
                            $this->updateActiveBalance($parent->parent_id, $bonus);
                        } else {
                            // jika bulan ini tidak ada transaksi :
                            // insert row user_balance type=bonus cashflow=in from_user_id=Referrals.child_id status=2 (pending)
                            $user_balance_id = $this->insertBalance($device_check, 'bonus-in', 'bonus', 2, $parent->parent_id, $bonus);

                            // update users.pending_balance (ditambah $bonus) where(Referrals.parent_id)
                            $this->updatePendingBalance($parent->parent_id, $bonus);
                        }
                    } // end foreach
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                // transaction has problems
                $response->message = "Failed to perform task! #pap02l";
            } elseif ($hasError) {
                // transaction has problems, $response->message sudah terisi
            } else {
                $response->success = true;
                $response->message = "Successfully <b>Update Payment Success</b> for <b>$device_check->check_code</b>";

                // notifikasi ke app 1 -> max 1 notif
                try {
                    $title = "New status for $device_check->check_code";
                    $content = "$device_check->check_code was PAID. Check your bank/emoney account or email $device_check->customer_email";
                    $notification_data = [
                        'check_id'    => $device_check->check_id,
                        'type'        => 'final_result'
                    ];

                    // for app_1
                    $fcm = new FirebaseCoudMessaging();
                    $send_notif_app_1 = $fcm->send($device_check->fcm_token, $title, $content, $notification_data);
                    $response->data['send_notif_app_1'] = $send_notif_app_1;
                } catch (\Exception $e) {
                    $response->data['send_notif_app_1'] = $e->getMessage();
                }

                // kirim notif ke app 2 (jika agent) -> max 3 notif
                // bisa jadi kirim email juga (belum diimplementasi)
                if ($device_check->type_user == 'agent' && $device_check->merchant_id == '') {
                    helper('onesignal');
                    $commision_rate_check = PaymentsAndPayouts::getCommisionRate($device_check->price);
                    $bonus = $commision_rate->commission_1;

                    try {
                        // get user
                        $userData = $this->User->getUser(['user_id' => $user_id]);

                        helper('number');
                        $title = "Congatulation For Your bonus!";
                        $content = "Congratulations $userData->name, You get bonus amount " . number_to_currency($bonus, "IDR") . "From Transaction $device_check->check_code";
                        $notification_data = [
                            'type'        => 'notif_bonus'
                        ];
                        $notification_token = $userData->notification_token;
                        $send_notif = sendNotification([$notification_token], $title, $content, $notification_data);
                        $response->data['send_notif'] = $send_notif;
                    } catch (\Exception $e) {
                        $response->message .= " But, unable to send notification: " . $e->getMessage();
                    }

                    if (count($parents) > 0) {
                        // hitung $bonus (berdasarkan tradein.price, commission_rate, referrals.level[1 atau 2])

                        foreach ($parents as $rowParent) {
                            try {
                                $bonus = 0;
                                if ($parent->ref_level == 1) $bonus = (int)$commision_rate->commission_2; // komisi level 1
                                elseif ($parent->ref_level == 2) $bonus = (int)$commision_rate->commission_3; // komisi level 2

                                $title = "Congatulation For Your bonus!";
                                $content = "Congratulations $rowParent->name, You get bonus amount " . number_to_currency($bonus, "IDR");
                                $notification_data = [
                                    'type'        => 'notif_bonus'
                                ];
                                $notification_token = $rowParent->notification_token;
                                $send_notif = sendNotification([$notification_token], $title, $content, $notification_data);
                                $response->data['send_notif'] = $send_notif;
                            } catch (\Exception $e) {
                                $response->message .= " But, unable to send notification: " . $e->getMessage();
                            }
                        }
                    }
                }

                // kirim notif ke admin
                $nodejs = new Nodejs();
                $nodejs->emit('notification', [
                    'type' => 1,
                    'message' => "Payment COMPLETED for $device_check->check_code",
                ]);

                // kirim email
                try {
                    helper('number');
                    $email_body_data = [
                        'template' => 'transaction_success',
                        'd' => $device_check,
                    ];
                    $email_body = view('email/template', $email_body_data);
                    $mailer = new Mailer();
                    $data = (object)[
                        'receiverEmail' => $device_check->customer_email,
                        'receiverName' => $device_check->customer_name,
                        'subject' => "Payment for $device_check->check_code",
                        'content' => $email_body,
                    ];
                    $response->data['email'] = $mailer->send($data);
                } catch (\Exception $e) {
                    $response->data['email'] = $e->getMessage();
                }

                // logs
                unset($device_check->fcm_token);
                $data = [
                    'device' => $device_check,
                    'response' => $response,
                ];
                $this->log->in($device_check->check_code, 61, json_encode($data), false, false, $device_check->check_id);
            }
        }

        return $response;
    }

    /*
    @param $check_id int
    @return $response object 
    */
    public function updatePaymentWithdrawSuccess($user_balance_id, $user_id)
    {
        // #belum selesai
        $response = initResponse();

        $this->db = \Config\Database::connect();
        $this->db->transStart();

        // update user_balance status
        $this->UserBalance->where([
            'user_balance_id'   => $user_balance_id,
            'type'              => 'withdraw',
        ])->set(['status' => 1])
            ->update();
        // var_dump($this->db->getLastQuery());die;

        // update where(check_id) user_payouts.status=1 (success)
        $this->UserPayout->where([
            'user_balance_id' => $user_balance_id
        ])->set(['status' => 1])
            ->update();

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #pap03l";
        } else {
            $response->success = true;
            $response->message = "Successfully <b>Update Withdraw Payment</b> for user_balance_id <b>$user_balance_id</b>";

            // kirim notif ke app 2
            $user = $this->User->getUser(['user_id' => $user_id], 'user_id,name,notification_token');
            try {
                $title = "Congatulation, Your withdraw was Success!";
                $content = "Your withdraw was successfully transfered! Please check your bank/emoney account or email";
                $notification_data = [
                    'type'        => 'notif_withdraw_success'
                ];

                $notification_token = $user->notification_token;
                // var_dump($notification_token);die;
                helper('onesignal');
                $send_notif = sendNotification([$notification_token], $title, $content, $notification_data);
                $response->data['send_notif'] = $send_notif;
            } catch (\Exception $e) {
                $response->message .= " But, unable to send notification: " . $e->getMessage();
            }

            // kirim notif ke admin
            $nodejs = new Nodejs();
            $nodejs->emit('notification', [
                'type' => 1,
                'message' => "Payment COMPLETED for Withdrawal of $user_balance_id",
            ]);

            // kirim email
            try {
                $select = 'ups.user_payout_id, ups.user_id, ups.amount, ups.type, ups.status AS status_user_payouts, upa.payment_method_id, pm.type, pm.name AS pm_name, pm.alias_name, pm.status AS status_payment_methode, upa.account_number, upa.account_name, ups.created_at, ups.created_by, ups.updated_at, ups.updated_by, upd.status as upd_status, ub.user_balance_id, ups.withdraw_ref, upd.user_payout_detail_id';
                // $select for email
                $select .= ',u.name,u.name as customer_name,u.email as customer_email,upa.account_number,upa.account_name,pm.name as pm_name,ub.type as ub_type,ub.currency,ub.currency_amount,withdraw_ref as referrence_number';
                $where = array('ups.user_balance_id ' => $user_balance_id, 'ups.type' => 'withdraw');

                $user_payout = $this->UserPayout->getUserPayoutWithDetailPayment($where, $select);

                if ($user_payout) {

                    helper('number');
                    $email_body_data = [
                        'template' => 'withdraw_success',
                        'd' => $user_payout,
                    ];
                    $email_body = view('email/template', $email_body_data);
                    $mailer = new Mailer();

                    $data = (object)[
                        'receiverEmail' => $user_payout->customer_email,
                        'receiverName' => $user_payout->customer_name,
                        'subject' => "Withdrawal $user_payout->referrence_number",
                        'content' => $email_body,
                    ];
                    $response->data['send_email'] = $mailer->send($data);
                } else {
                    $response->data['send_email'] = "ups.user_balance_id not found ($user_balance_id)";
                }
            } catch (\Exception $e) {
                $response->data['send_email'] = $e->getMessage();
            }

            $data = [
                'user_id' => $user_id,
                'user_balance_id' => $user_balance_id,
                'response' => $response,
            ];
            $this->log->in("$user->name", 60, json_encode($data), false, $user->user_id, false);
        }

        return $response;
    }

    public static function getCommisionRate($price)
    {
        $response = initResponse();
        $CommissionRate = new CommissionRate();
        $commision_rate = $CommissionRate->getCommision([
            'price_from<=' => (int)$price,
            'price_to>=' => (int)$price,
            'deleted_at' => null,
        ], 'price_from,price_to,commission_1,commission_2,commission_3');
        if (!$commision_rate) {
            $response->message = "Commision rate for $price is unavailable!";
        } else {
            $response->success = true;
            $response->message = "OK";
            $response->data = $commision_rate;
        }

        return $response;
    }

    /*
    @param $user_id int
    @param $status int
    @param $type string
    @param $select int
    @return void 
    */
    public static function getUserBalanceThisMonth($user_id, $status = 2, $type = 'bonus', $select = 'user_balance_id,currency,currency_amount,convertion,amount')
    {
        $response = initResponse();
        $UserBalance = new UserBalance();
        $user_balance_this_month = $UserBalance->getUserBalances([
            'user_id'    => $user_id,
            'type'       => $type,
            'status'     => $status,
            'date_format(created_at, "%Y-%m-%d") >=' => date('Y-m-') . '01',
            'date_format(created_at, "%Y-%m-%d") <=' => date('Y-m-d'),
        ], $select);
        // var_dump($user_balance_this_month);die;
        if (!$user_balance_this_month) {
            $response->message = "Void.";
        } else {
            $response->message = "Exist.";
            $response->success = true;
            $response->data = $user_balance_this_month;
        }

        return $response;
    }

    /*
    @param $user_id int
    @param $status int
    @param $type string
    @param $select int
    @return void 
    */
    public static function getTransactionThisMonth($user_id, $status_internal = 5, $type = 'agent', $select = 'dc.check_id,check_detail_id')
    {
        $response = initResponse();
        $DeviceCheck = new DeviceChecks();
        $device_check = $DeviceCheck->getDeviceDetail([
            'user_id'           => $user_id,
            'type_user'         => $type,
            'status_internal'   => $status_internal,
            'date_format(payment_date, "%Y-%m-%d") >=' => date('Y-m-') . '01',
            'date_format(payment_date, "%Y-%m-%d") <=' => date('Y-m-d'),
        ], $select);
        if (!$device_check) {
            $response->message = "Void.";
        } else {
            $response->message = "Exist.";
            $response->success = true;
            $response->data = $device_check;
        }

        return $response;
    }

    /*
    @param $user_id int
    @param $bonus int
    @return void 
    */
    public function updateActiveBalance($user_id, $bonus)
    {
        $this->User->where(['user_id' => $user_id])
            ->set('active_balance', 'active_balance+' . $bonus, false)
            ->update();
    }

    /*
    @param $user_id int
    @param $bonus int
    @return void 
    */
    public function updatePendingBalance($user_id, $bonus)
    {
        $value_update = $bonus;
        if ($bonus > 0)  $value_update = 'pending_balance+' . $bonus;
        $this->User->where(['user_id' => $user_id])
            ->set('pending_balance', $value_update, false)
            ->update();
    }

    /*
    @param $user_id int
    @param $bonus int
    @return void 
    */
    public function updateReferralSavingAndTransaction($parent_id, $user_id, $bonus)
    {
        $this->Referral->where([
            'parent_id' => $parent_id,
            'child_id'  => $user_id
        ])
            ->set('saving', 'saving+' . $bonus, false)
            ->set('transaction', 'transaction+1', false)
            ->set('updated_at', 'NOW()', false)
            ->update();
    }

    /**
     * @param   int     $user_id
     * @param   int     $bonus
     * @return  object  $response 
     */
    public function sendBonus($user_id, $bonus, $notes, $admin_id, $admin_name)
    {
        $response = initResponse();
        // membuat $data sebagai pengganti $device_check yang dibutuhkan di insertBaance()
        $data = (object)[
            'user_id'    => $user_id,
            'check_id'    => null,
        ];

        $this->db = \Config\Database::connect();
        $this->db->transStart();
        $update1 = $this->updateActiveBalance($user_id, $bonus);
        $update2 = $this->insertBalance($data, 'agentbonus-in', 'agentbonus', 1, false, $bonus, $admin_name, $notes);
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #pap04l";
        } else {
            helper('number');
            $bonusFormatted = number_to_currency($bonus, "IDR.");
            $response->success = true;
            $response->message = "Successfully Send Bonus $bonusFormatted";

            // kirim notif ke app 2
            $user = $this->User->getUser(['user_id' => $user_id], 'user_id,name,notification_token,email');
            try {
                $title = "You Got Bonus!";
                $content = "Congratulation you have received " . $bonusFormatted . " bonus as an Agent of " . env('app.name') . ". Note: $notes";
                $notification_data = [
                    'type'        => 'notif_bonus'
                ];

                $notification_token = $user->notification_token;
                // var_dump($notification_token);die;
                helper('onesignal');
                $send_notif = sendNotification([$notification_token], $title, $content, $notification_data);
                $response->data['send_notif'] = $send_notif;
            } catch (\Exception $e) {
                $response->message .= " But, unable to send notification: " . $e->getMessage();
            }

            // kirim email ( belum )
            try {
                $user_balance_id = $update2;
                $where = array('user_balance_id ' => $user_balance_id, 'type' => 'agentbonus', 'cashflow' => 'in'); // return array
                $user_balance = $this->UserBalance->getUserBalance($where);
                if ($user_balance) {
                    $d = $user_balance[0];
                    $d->name = $user->name;
                    $email_body_data = [
                        'template' => 'new_bonus',
                        'd' => $d,
                    ];
                    $email_body = view('email/template', $email_body_data);
                    $mailer = new Mailer();

                    $data = (object)[
                        'receiverEmail' => $user->email,
                        'receiverName' => $user->name,
                        'subject' => "#$user_balance_id - New Agent Bonus $bonusFormatted",
                        'content' => $email_body,
                    ];
                    $response->data['send_email'] = $mailer->send($data);
                } else {
                    $response->data['send_email'] = "user_balance_id not found ($user_balance_id)";
                }
            } catch (\Exception $e) {
                $response->data['send_email'] = $e->getMessage();
            }

            $data = [
                'user_id' => $user_id,
                'name' => $user->name,
                'bonus' => $bonus,
                'notes' => htmlentities($notes),
                'by' => $admin_name,
                'response' => $response,
            ];
            $this->log->in("$admin_name\n$user->name", 66, json_encode($data), $admin_id, $user->user_id, null);
        }

        return $response;
    }
}
