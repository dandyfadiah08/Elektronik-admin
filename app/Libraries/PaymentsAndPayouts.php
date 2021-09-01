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

class PaymentsAndPayouts
{

	protected $DeviceCheck, $DeviceCheckDetail, $UserBalance, $UserPyout, $UserPayoutDetail, $Referral, $CommissionRate, $User;

    public function __construct() {
		$this->DeviceCheck = new DeviceChecks();
		$this->DeviceCheckDetail = new DeviceCheckDetails();
		$this->UserBalance = new UserBalance();
		$this->UserPayout = new UserPayouts();
		$this->UserPayoutDetail = new UserPayoutDetails();
        $this->Referral = new Referrals();
        $this->User = new Users();
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
        $amount = $this->insertBalance($device_check);// 'transaction-in'

        // insert row user_balance type=transaction cashflow=out status=2 (pending)
        $user_balance_id = $this->insertBalance($device_check, 'transaction-out');

        // insert row user_payouts type=transaction status=2 (pending)
        $user_payout_id = $this->insertPayout($device_check, $amount, $user_balance_id);

        // insert row user_payout_details user_payout_details_id=user_payout_id
        $user_payout_detail_id = $this->insertPayoutDetail($device_check, $amount, $user_payout_id);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #trs01l";
        } else {
            $response->success = true;
            $response->message = "Successfully <b>proceed payment</b> for <b>$device_check->check_code</b>";

            // hit API payment gateway
            $xendit = new Xendit();
            $payment_gateway_response = $xendit->create_disbursements($device_check->check_code, $amount, $device_check->bank_code, $device_check->account_number, $device_check->account_name, "Sell Device Transfer $device_check->check_code");

            if($payment_gateway_response->success) {
                // update user_payout_details with $user_payout_id
                $data_update = [
                    'status'				=> $payment_gateway_response->data->status,
                    'user_id'				=> $payment_gateway_response->data->user_id,
                    'external_id'			=> $payment_gateway_response->data->external_id,
                    'amount'				=> $payment_gateway_response->data->amount,
                    'bank_code'				=> $payment_gateway_response->data->bank_code,
                    'account_holder_name'	=> $payment_gateway_response->data->account_holder_name,
                    'description'	        => $payment_gateway_response->data->disbursement_description,
                    'id'        	        => $payment_gateway_response->data->id,
                    'updated_at'			=> date('Y-m-d H:i:s'),
                ];
                $this->updatePayoutDetail($user_payout_detail_id, $data_update);
                if($payment_gateway_response->data->status == 'COMPLETED') $this->updatePaymentSuccess($device_check->check_id);
            } else {
                // ngapain ya
                $response->message .= ". But payment gateway has problems occured.";
                $response->data['errors'] = $payment_gateway_response->data;
            }

            // $response->data = ['data_user_balance_in' => $data_user_balance_in, 'data_user_balance_out' => $data_user_balance_out, 'data_user_payout' => $data_user_payout, 'data_user_payout_detail' => $data_user_payout_detail, 'user_payout_id' => $user_payout_id];
        }

        return $response;
	}

    /*
    @param $device_check object
    @param $cashflow string : transaction-in, transaction-out, bonus-in
    @param $type string : *transaction, bonus 
    @param $status int : *2 
    @param $from_child boolean or int : int passed with user_id
    @return $out int : $amount, $user_payout_id
    */
    private function insertBalance($device_check, $cashflow = 'transaction-in', $type = 'transaction', $status = 2, $from_child = false, $bonus = 0) {
        $currency = 'idr';
        $convertion = 1;
        if($type = 'bonus') $currency_amount = $bonus;
        else $currency_amount = $device_check->price;
        $amount = $currency_amount * $convertion;
        $now = date('Y-m-d H:i:s');
        $data_user_balance = [
            'user_id'		    => $from_child ? $from_child : $device_check->user_id,
            'from_user_id'		=> $device_check->user_id,
            'currency'			=> $currency,
            'convertion'		=> $convertion,
            'currency_amount'	=> $currency_amount,
            'amount'			=> $amount,
            'type'				=> $type,
            'check_id'			=> $device_check->check_id,
            'status'			=> $status,
            'created_at'		=> $now,
            'updated_at'		=> $now,
        ];

        $output_type = 'amount';
        if($cashflow == 'transaction-in') {
            $data_user_balance += [
                'cashflow'	=> 'in',
                'notes'		=> 'Sell Device Income',
            ];
        } elseif($cashflow == 'transaction-out') {
            $data_user_balance += [
                'cashflow'	=> 'out',
                'notes'		=> 'Sell Device Transfer',
            ];
            $output_type = 'id';
        } elseif($cashflow == 'bonus-in') {
            $data_user_balance += [
                'cashflow'	=> 'in',
                'notes'		=> 'Sell Device Commission',
            ];
            $output_type = 'id';
        }

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
    private function insertPayout($device_check, $amount, $user_balance_id, $type = 'transaction', $status = 2) {
        $now = date('Y-m-d H:i:s');
        $data_user_payout = [
            'user_id'			=> $device_check->user_id,
            'user_payment_id'	=> $device_check->user_payment_id,
            'user_balance_id'	=> $user_balance_id,
            'amount'			=> $amount,
            'type'				=> $type,
            'check_id'			=> $device_check->check_id,
            'status'			=> $status,
            'created_by'		=> 'system',
            'created_at'		=> $now,
            'updated_by'		=> 'system',
            'updated_at'		=> $now,
        ];
        $this->UserPayout->insert($data_user_payout);
        return $this->db->insertID();
    }

    /*
    @param $device_check object
    @param $amount int
    @param $user_balance_id int
    @return $user_payout_id int 
    */
    private function insertPayoutDetail($device_check, $amount, $user_payout_id) {
        $now = date('Y-m-d H:i:s');
        $data_user_payout_detail = [
            'user_payout_detail_id'	=> $user_payout_id,
            'user_payout_id'		=> $user_payout_id,
            'type'					=> 'xendit',
            'amount'				=> $amount,
            'bank_code'				=> $device_check->bank_code,
            'account_number'		=> $device_check->account_number,
            'account_holder_name'	=> $device_check->account_name,
            'status'				=> 'Inserted',
            'description'			=> "Sell Device Transfer $device_check->check_code",
            'created_at'			=> $now,
            'updated_at'			=> $now,
        ];
        $this->UserPayoutDetail->insert($data_user_payout_detail);
    }

    /*
    @param $user_payout_detail_id int
    @param $data_update array
    @return void 
    */
    public function updatePayoutDetail($user_payout_detail_id, $data_update) {
        $this->UserPayoutDetail->update($user_payout_detail_id, $data_update);
    }

    /*
    @param $check_id int
    @return $response object 
    if success, $response->data contains $device_check
    */
    public function updatePaymentSuccessValidation($check_id) {
		$response = initResponse();
        $select = 'dc.check_id,dc.check_code,dc.user_id,check_detail_id,dc.price,upa.user_payout_id,dc.type_user';
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
    public function updatePaymentSuccess($check_id) {
        // #belum selesai
		$response = initResponse();
        $validation = $this->updatePaymentSuccessValidation($check_id);
        if(!$validation->success) {
            $response = $validation;
        } else {
            $device_check = $validation->data;
            $check_id = $device_check->check_id;
            $user_id = $device_check->user_id;
            $hasError = false;

            $this->db = \Config\Database::connect();
            $this->db->transStart();

            // update device_check status_internal
            // $this->DeviceCheck->update($device_check->check_id, ['status_internal' => 5]);

            // update device_check status_internal
            // $this->DeviceCheckDetail->update($device_check->check_detail_id, ['payment_date' => date('Y-m-d H:i:s')]);

            // update where(check_id, user_id) user_balance.status=1 (success) [cashflow=in], [cashflow=out]
            // $this->UserBalance->where([
            //     'check_id'  => $check_id,
            //     'user_id'   => $user_id
            // ])->set(['status' => 1])
            // ->update();

            // update where(check_id) user_payouts.status=1 (success)
            // $this->UserPayout->update($device_check->user_payout_id, ['status' => 1]);

            if($device_check->type_user == 'agent') {
                // hitung $bonus (berdasarkan device_checks.price, commission_rate, level=0)
                $commision_rate_check = PaymentsAndPayouts::getCommisionRate($device_check->price);
                if(!$commision_rate_check->success) {
                    $hasError = true;
                    $response->message = $commision_rate_check->message;
                } else {
                    $commision_rate = $commision_rate_check->data;
                    $bonus = (int)$commision_rate->commission_1;
                    // insert row user_balance type=bonus cashflow=in status=1 (success)
                    // $user_balance_id = $this->insertBalance($device_check, 'bonus-in', 'bonus', 1);

                    // cek user_balance.type=bonus di bulan ini (device_checks.created_at) dan user_id ini
                    $user_balance_this_month_check = PaymentsAndPayouts::getUserBalanceThisMonth($user_id);
                    if($user_balance_this_month_check->success) {
                        $user_balance = $user_balance_this_month_check->data;
                        // (looping) update where(user_balance_id) users_balance.status=1 (success)

                        // cek users.pending_balance, ditambahkan ke $bonus dan 0 kan pending_balance
                    }

                    // update where(user_id) users.active_balance (ditambah $bonus)
                    $this->updateActiveBalance($user_id, $bonus);
                }
            }

            // jika ada parent (lv 1 / lv 2), untuk user_id parent :
            $parents = $this->Referral->getActiveReferralByChildId($user_id, 'parent_id,ref_level');
            // var_dump($parents);die;
            // var_dump($this->db->getLastQuery());die;
            if(count($parents) > 0) {
                // hitung $bonus (berdasarkan device_checks.price, commission_rate, referrals.level[1 atau 2])
                $commision_rate_check = PaymentsAndPayouts::getCommisionRate($device_check->price);
                if(!$commision_rate_check->success) {
                    $hasError = true;
                    $response->message = $commision_rate_check->message;
                } else {
                    $commision_rate = $commision_rate_check->data;
                    // var_dump($parents);die;
                    foreach ($parents as $parent) {
                        $bonus = 0;
                        if($parent->ref_level == 1) $bonus = (int)$commision_rate->commission_2; // komisi level 1
                        elseif($parent->ref_level == 2) $bonus = (int)$commision_rate->commission_3; // komisi level 2

                        // update referrals transaction+1, saving+$bonus where(parent_id and child_id)
                        $this->updateReferralSavingAndTransaction($parent->parent_id, $user_id, $bonus);

                        // cek parent ini jika bulan ini ada transaksi :
                        $user_balance_this_month_check = PaymentsAndPayouts::getUserBalanceThisMonth($parent->parent_id, 1, 'transaction');
                        // var_dump($user_balance_this_month_check);
                        if($user_balance_this_month_check->success) {
                            // tidak dipakai, hanya menunjukkan ada
                            $user_balance = $user_balance_this_month_check->data;

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
                $response->message = "Failed to perform task! #trs02l";
            } elseif ($hasError) {
                // transaction has problems, $response->message sudah terisi
            } else {
                $response->success = true;
                $response->message = "Successfully <b>Update Payment Success</b> for <b>$device_check->check_code</b>";
            }
        }

        return $response;
    }

    public static function getCommisionRate($price) {
        $response = initResponse();
        $CommissionRate = new CommissionRate();
        $commision_rate = $CommissionRate->getCommision([
            'price_from<=' => (int)$price,
            'price_to>=' => (int)$price,
        ],'price_from,price_to,commission_1,commission_2,commission_3');
        if(!$commision_rate) {
            $response->message = "Commision rate for $price is unavailable!";
        } else {
            $response->success = true;
            $response->message = "OK";
            $response->data = $commision_rate;
        }

        return $response;
    }

    public static function getUserBalanceThisMonth($user_id, $status = 2, $type = 'bonus', $select = 'user_balance_id,currency,currency_amount,convertion,amount') {
        $response = initResponse();
        $UserBalance = new UserBalance();
        $user_balance_this_month = $UserBalance->getBalanceAndDeviceCheck([
            'ub.user_id'   => $user_id,
            'ub.type'      => $type,
            'ub.status'    => $status,
            'date_format(ub.created_at, "%Y-%m-%d") >=' => date('Y-m-').'01',
            'date_format(ub.created_at, "%Y-%m-%d") <=' => date('Y-m-d'),
        ], $select);
        // var_dump($user_balance_this_month);die;
        if(!$user_balance_this_month) {
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
    @param $bonus int
    @return void 
    */
    public function updateActiveBalance($user_id, $bonus) {
        $this->User->where(['user_id' => $user_id])
        ->set('active_balance', 'active_balance+'.$bonus, false)
        ->update();
    }

    /*
    @param $user_id int
    @param $bonus int
    @return void 
    */
    public function updatePendingBalance($user_id, $bonus) {
        $this->User->where(['user_id' => $user_id])
        ->set('pending_balance', 'pending_balance+'.$bonus, false)
        ->update();
    }

    /*
    @param $user_id int
    @param $bonus int
    @return void 
    */
    public function updateReferralSavingAndTransaction($parent_id, $user_id, $bonus) {
        $this->Referral->where([
            'parent_id' => $parent_id,
            'child_id'  => $user_id
        ])
        ->set('saving', 'saving+'.$bonus, false)
        ->set('transaction', 'transaction+1', false)
        ->set('updated_at', 'NOW()', false)
        ->update();
    }


}
