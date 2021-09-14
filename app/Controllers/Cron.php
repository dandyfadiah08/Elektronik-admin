<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Backup_user_balance;
use App\Models\Backup_users;
use CodeIgniter\API\ResponseTrait;
use App\Models\Users;
use App\Models\UserBalance;

class Cron extends BaseController
{
	use ResponseTrait;

	protected $DeviceCheck, $DeviceCheckDetail, $Admin, $AdminRole, $User, $UserBalance, $UserPyout, $UserPayoutDetail, $Appointment;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->key = env('cron.key');
		helper('log');
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
			if(count($user) > 0) $this->BackupUser->insertBatch($user);
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
			if(count($user_balance) > 0) $this->BackupUserBalance->insertBatch($user_balance);
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
}
