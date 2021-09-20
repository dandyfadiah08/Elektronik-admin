<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\PaymentsAndPayouts;
use App\Models\DeviceChecks;
use App\Models\UserBalance;
use App\Models\UserPayouts;
use CodeIgniter\Controller;

class Xendit extends Controller
{

    use ResponseTrait;

    // protected $request, $response, $logger;


    public function __construct()
    {
        helper('log');
        helper('rest_api');
    }

    // public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	// {
	// 	// Do Not Edit This Line
	// 	parent::initController($request, $response, $logger);

    // }

    public function disbursement()
    {
        $response = initResponse('OK', false);
        $header = $this->request->header('X-Callback-Token');
        $body = $this->request->getJSON();
        if (!$header) {
            $response->message = "X-Callback-Token not available";
        } elseif ($header->getValue() != env('xendit.callback_token')) {
            $response->message = "X-Callback-Token is invalid";
        } else {
            // check if check_code ($body->external_id) exist
            // $check_code = $body->external_id;
            // $user_payout_detail_id = $device_check->user_payout_detail_id;
            $payment_and_payout = new PaymentsAndPayouts();

            /* TESTING FUNCTION */
            // $id = $payment_and_payout->insertPayoutDetail($device_check, 1000, 20);
            // $id = $payment_and_payout->insertPayout($device_check, 1000, 64);
            // $id = $payment_and_payout->insertBalance($device_check, 'transaction-out');
            // var_dump($id);die;

            $data_update = [
                'status'                => $body->status,
                'user_id'                => $body->user_id,
                'external_id'            => $body->external_id,
                'amount'                => $body->amount,
                'bank_code'                => $body->bank_code,
                'account_holder_name'    => $body->account_holder_name,
                'description'            => $body->disbursement_description,
                'updated_at'            => date('Y-m-d H:i:s'),
            ];
            if (isset($body->is_instant)) $data_update += ['is_instant' => $body->is_instant ? 'true' : 'false'];
            if (isset($body->failure_code)) $data_update += ['failure_code' => $body->failure_code];
            else $data_update += ['failure_code' => null];
            if (isset($body->updated_)) {
                $updated_at = DateTime::createFromFormat(DateTime::ISO8601, $body->updated, new DateTimeZone('Asia/Jakarta'));
                $data_update += ['updated_at' => $updated_at->format('Y-m-d H:i:s')];
            }
            $data = [
                'data_update' => $data_update,
            ];
            $payment_and_payout->updatePayoutDetail([
                // 'external_id'   => $check_code,
                'id'            => $body->id,
            ], $data_update);

            $UserPayout = new UserPayouts();
            $user_payout = $UserPayout->getUserPayoutAndDetail(['upd.id' => $body->id], 'ups.type,user_balance_id,check_id,ups.user_id');
            if (!$user_payout) {
                $response->message = "Invalid upd.id $body->id";
            } else {
                $check_id = $user_payout->check_id;
                $user_balance_id = $user_payout->user_balance_id;
                $user_id = $user_payout->user_id;
                if ($user_payout->type == 'transaction') {
                    $DeviceCheck = new DeviceChecks();
                    // $select = 'dc.check_id,dc.user_id,upad.user_payout_detail_id,pm.name as bank_code,dcd.account_number,dcd.account_name,check_code,dc.price';
                    $select = 'dc.check_id';
                    $where = ['dc.check_id' => $check_id, 'dc.deleted_at' => null];
                    // $device_check = $DeviceCheck->getDeviceForXendit($where, $select);
                    $device_check = $DeviceCheck->getDeviceDetailPayment($where, $select);
                    if (!$device_check) {
                        $response->message = "Invalid check_id $check_id";
                    } else {
                        if ($body->status == 'COMPLETED') $data['update_payment_success'] = $payment_and_payout->updatePaymentSuccess($device_check->check_id);
                        $response->success = true;
                        $response->message = 'OK';
                    }
                } elseif ($user_payout->type == 'withdraw') {
                    $UserBalance = new UserBalance();
                    $select = 'ub.user_balance_id';
                    $where = ['ub.user_balance_id' => $user_balance_id];
                    $user_balance = $UserBalance->getBalanceAndDeviceCheck($where, $select);
                    if (!$user_balance) {
                        $response->message = "Invalid user_balance_id $user_balance_id";
                    } else {
                        if ($body->status == 'COMPLETED') $data['update_payment_success'] = $payment_and_payout->updatePaymentWithdrawSuccess($user_balance_id, $user_id);
                        $response->success = true;
                        $response->message = 'OK';
                    }
                }
                $response->data = $data;
            }
        }
        writeLog(
            "xendit",
            "webhook disbursement\n"
            . json_encode($body)
            . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
