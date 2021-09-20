<?php

namespace App\Libraries;

use App\Libraries\Log;
use App\Models\UserBalance;
use App\Models\UserPayoutDetails;
use App\Models\UserPayouts;
use App\Models\Users;

class WithdrawAndPayouts
{
    public function __construct() 
    {
        $this->UserBalance = new UserBalance();
        $this->UserPayout = new UserPayouts();
        $this->UserPayoutDetail = new UserPayoutDetails();
        $this->User = new Users();
        $this->log = new Log();
        helper('rest_api');
    }

    /*
    @param $dataUser object
    @return $response object
    */
	function proceedPaymentLogic($dataUser)
	{
        error_reporting(E_ALL);
        // #belum selesai
		$response = initResponse();
		$this->db = \Config\Database::connect();
		$this->db->transStart();

        // insert row user_payout_details user_payout_details_id=user_payout_id
        $user_payout_detail_id = $this->insertPayoutDetail($dataUser);
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #whd01l";
        } else {
            
            $response->success = true;
            $response->message = "Successfully <b>proceed payment</b> for <b>$dataUser->user_balance_id</b>";
            // var_dump($dataUser);die;
            // hit API payment gateway
            $xendit = new Xendit();
            $payment_gateway_response = $xendit->create_disbursements($dataUser->user_balance_id, $dataUser->amount, $dataUser->bank_code, $dataUser->account_number, $dataUser->account_name, "Withdraw User $dataUser->user_id");

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

                $data['data'] = $dataUser;
                $log_cat = 22;
                $this->log->in(session()->username, $log_cat, json_encode($data));
                // if($payment_gateway_response->data->status == 'COMPLETED') $this->updatePaymentSuccess($device_check->check_id);
            } else {
                // ngapain ya
                $response->message .= ". But payment gateway has problems occured.";
                $response->data['errors'] = $payment_gateway_response->data;
            }

        }

        return $response;
    }


    /*
    @param $dataUser object
    @return $user_payout_id int 
    */
    public function insertPayoutDetail($dataUser) {
        $now = date('Y-m-d H:i:s');
        $data_user_payout_detail = [
            'user_payout_detail_id'	=> $dataUser->user_payout_id,
            'user_payout_id'		=> $dataUser->user_payout_id,
            'type'					=> 'xendit',
            'amount'				=> $dataUser->amount,
            'bank_code'				=> $dataUser->bank_code,
            'account_number'		=> $dataUser->account_number,
            'account_holder_name'	=> $dataUser->account_name,
            'status'				=> 'Inserted',
            'description'			=> "Withdraw User $dataUser->user_id",
            'created_at'			=> $now,
            'updated_at'			=> $now,
        ];
        $this->UserPayoutDetail->insert($data_user_payout_detail);
        return $dataUser->user_payout_id;
    }

    /*
    @param $key int or array
    @param $data_update array
    @return void 
    */
    public function updatePayoutDetail($key, $data_update) {
        if(is_array($key))  $this->UserPayoutDetail->where($key)->set($data_update)->update();
        else $this->UserPayoutDetail->update($key, $data_update);
    }
}