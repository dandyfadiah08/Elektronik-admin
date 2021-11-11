<?php

namespace App\Libraries;

use App\Libraries\Log;
use App\Models\UserBalance;
use App\Models\UserPayoutDetails;
use App\Models\UserPayouts;
use App\Models\Users;

class WithdrawAndPayouts
{
    protected $log, $User, $UserBalance, $UserPayout, $UserPayoutDetail;
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
    @param $user_payout object
    @return $response object
    */
	function proceedPaymentLogic($user_payout)
	{
        // error_reporting(E_ALL);
        // #belum selesai
		$response = initResponse();
		$this->db = \Config\Database::connect();
		$this->db->transStart();


        // check if user payout detail has created or not
        $user_payout_detail_id = $this->UserPayoutDetail->getUserPayoutDetails(['user_payout_id' => $user_payout->user_payout_id], "user_payout_detail_id" );
        if(!$user_payout_detail_id){
            // insert row user_payout_details user_payout_details_id=user_payout_id
            $user_payout_detail_id = $this->insertPayoutDetail($user_payout);
        } else{
            $user_payout_detail_id = $user_payout_detail_id-> user_payout_detail_id;
        }
        
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE) {
            // transaction has problems
            $response->message = "Failed to perform task! #whd01l";
        } else {
            
            $response->success = true;
            $response->message = "Successfully <b>proceed payment</b> for <b>$user_payout->withdraw_ref</b>";
            // var_dump($user_payout);die;
            // hit API payment gateway
            $xendit = new Xendit();
            $payment_gateway_response = $xendit->create_disbursements($user_payout->user_balance_id, $user_payout->amount, $user_payout->bank_code, $user_payout->account_number, $user_payout->account_name, "Withdraw User $user_payout->user_id");

            $now = date('Y-m-d H:i:s');
            $data = (array)$user_payout;
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
                    'updated_at'			=> $now,
                ];
                $this->updatePayoutDetail($user_payout_detail_id, $data_update);
                $data['xendit'] = $data_update;

                // update date and by xendit
                $data_update = [
                    'updated_at'    => $now,
                    'updated_by'    => 'xendit',
                ];
                $this->UserPayout->saveUpdate(['user_payout_id' => $user_payout->user_payout_id], $data_update);
            } else {
                // ngapain ya
                $response->message .= ". But payment gateway has problems occured.";
                $response->data['errors'] = $payment_gateway_response->data;

                // update date and by user (admin)
                $data_update = [
                    'updated_at'    => $now,
                    'updated_by'    => session()->username,
                ];
                $this->UserPayout->saveUpdate(['user_payout_id' => $user_payout->user_payout_id], $data_update);
            }
            
            $log_cat = 22;
            $user = $this->User->getUser(['user_id' => $user_payout->user_id], 'user_id,name');
            $data += (array)$user;
            $this->log->in("$user->name\n".session()->username, $log_cat, json_encode($data), session()->admin_id, $user->user_id, false);
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