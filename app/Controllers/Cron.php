<?php

namespace App\Controllers;


use App\Libraries\Xendit;
use App\Models\Backup_user_balance;
use App\Models\Backup_users;
use App\Models\DeviceChecks;
use CodeIgniter\API\ResponseTrait;
use App\Models\Users;
use App\Models\UserBalance;
use App\Models\UserPayments;
use CodeIgniter\Controller;
use PhpParser\Node\Expr\Cast\Object_;

class Cron extends Controller
{
	use ResponseTrait;

	protected $DeviceCheck, $DeviceCheckDetail, $Admin, $AdminRole, $User, $UserBalance, $UserPyout, $UserPayoutDetail, $Appointment;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->key = env('cron.key');
		helper('log');
		helper('rest_api');
	}

	function resetPendingBalance()
	{
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		// var_dump($key);die;
		if ($key == $this->key) {
			$this->db->transStart();

			// reset user.pending_balance (where user.pending_balance>0)
			$this->User = new Users();
			$this->BackupUser = new Backup_users();
			$whereUser = ['pending_balance>' => 0];
			$user = $this->User->getUsers($whereUser, '*');
			if (count($user) > 0) $this->BackupUser->insertBatch($user);
			$this->User->where($whereUser)
				->set(['pending_balance' => 0])
				->update();

			// update user_blalance.status=3 (where status=2, type=bonus, cashflow=in)
			$this->UserBalance = new UserBalance();
			$this->BackupUserBalance = new Backup_user_balance();
			$whereUserBalance = [
				'status'	=> 2,
				'type'		=> 'bonus',
				'cashflow'	=> 'in',
			];
			$user_balance = $this->UserBalance->getUserBalance($whereUserBalance, '*');
			if (count($user_balance) > 0) $this->BackupUserBalance->insertBatch($user_balance);
			$this->UserBalance->where($whereUserBalance)
				->set(['status' => 3])
				->update();

			$this->db->transComplete();

			if ($this->db->transStatus() === FALSE) {
				// transaction has problems
				$response->message = "Failed to perform task! #crn01c";
			} else {
				$response->success = true;
				$response->message = "Success";
				$response->data = [
					'user' => $user,
					'user_balance' => $user_balance,
				];
			}
		}

		writeLog("cron", "resetPendingBalance\n" . json_encode($response));
		return $this->respond($response);
	}

	function validateBankAccount()
	{
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		// var_dump($key);die;
		if ($key == $this->key) {
			$this->UserPayment = new UserPayments();
			$where = [
				'up.status' => 'pending',
				'up.deleted_at' => null,
			];
			$user_payment = $this->UserPayment->getPaymentUser($where, 'user_payment_id,account_number,account_name,pm.name as bank_code,pm.type', 'user_payment_id ASC', 15, 0);
			if ($user_payment) {
				$response->data['user_payment'] = $user_payment;
				$response->data['bank_account'] = [];
				$this->Xendit = new Xendit();
				foreach ($user_payment as $up) {
					$xendit = $this->Xendit->validate_bank_detail($up->bank_code, $up->account_number);
					if ($xendit->success) {
						if ($xendit->data->status == 'SUCCESS') {
							$data_update = ['status' => 'active'];
							if ($up->type == 'bank') $data_update += ['account_name' => $xendit->data->bank_account_holder_name];
							$this->UserPayment->where(['user_payment_id' => $up->user_payment_id])
								->set($data_update)
								->update();
						} elseif ($xendit->data->status == 'FAILURE') {
							$data_update = ['status' => 'invalid'];
							$this->UserPayment->where(['user_payment_id' => $up->user_payment_id])
								->set($data_update)
								->update();
						}
						$response->data['bank_account'][$up->user_payment_id] = $xendit->data;
					}
				}
			}
			$response->success = true;
			$response->message = "OK";
		}

		writeLog("cron", "validateBankAccount\n" . json_encode($response));
		return $this->respond($response);
	}

	function failedTransactionByStatus()
	{
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		if ($key == $this->key) {
			$this->db->transStart();
			$DeviceCheck = new DeviceChecks();
			$where =
				'DATE(dcd.lock_until_date) < CURRENT_DATE';
			$whereIn = [
				'dc.status_internal' => [2, 3, 8],
			];
			$dataCek = $DeviceCheck->getDeviceDetailForLock($where, 'dc.check_id, "7" as "status_internal", dc.status_internal as "status_internal_old"', $whereIn);
			
			if ($dataCek) {
				
				$dataUpdate = [];
				foreach($dataCek as $row){
					$dataRow = [];
					$dataRow = (object) $dataRow;
					$dataRow->check_id = $row->check_id;
					$dataRow->status_internal = $row->status_internal;
					$dataUpdate[] = $dataRow;
					
				}
				// print(json_encode($dataUpdate));
				// die;
				// var_dump($dataCek);
				$DeviceCheck->updateBatch($dataUpdate, 'check_id');
				$this->db->transComplete();

				if ($this->db->transStatus() === FALSE) {
					$response->message = "Failed to perform task! #crn03c";
				} else {
					$response->success = true;
					$response->message = "Success";
					$response->data = $dataCek;
				}
			} else {
				$response->message = "No Update Data";
			}
		}

		writeLog("cron", "failedTransactionByStatus\n" . json_encode($response));
		return $this->respond($response);
	}
}
