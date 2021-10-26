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

	// setiap awal hari di awal bulan : 00 00 1 * * wget url &> /dev/null
	// untuk reset saldo pending (pending_balance) bagi users yang belum melakukan transaksi
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

	// belum dipakai
	// untuk memvalidasi derail bank yang di input
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

	// setiap ganti hari : 00 00 * * * wget url &> /dev/null
	// untuk membuat transaksi dengan status gantung (wait appointment > 3 hari, confirm appointment > 7 hari) dibuat cancel
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

	// setiap ganti hari : 00 00 * * * wget url &> /dev/null
	// untuk reset status disable dari pin change dan check yang limit
	function resetPinLock()
	{
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		if ($key == $this->key) {
			$this->db->transStart();

			// reset user.pin_check_lock (where user.pin_check_lock>0)
			$this->User = new Users();
			$whereUser = ['pin_check_lock>' => 0];
			$pin_check_lock = $this->User->where($whereUser)
			->set(['pin_check_lock' => 0])
			->update();

			// reset user.pin_change_lock (where user.pin_change_lock>0)
			$whereUser = ['pin_change_lock>' => 0];
			$pin_change_lock = $this->User->where($whereUser)
				->set(['pin_change_lock' => 0])
				->update();

			$this->db->transComplete();

			if ($this->db->transStatus() === FALSE) {
				// transaction has problems
				$response->message = "Failed to perform task! #crn04c";
			} else {
				$response->success = true;
				$response->message = "Success";
				$response->data = [
					'pin_check_lock' => $pin_check_lock,
					'pin_change_lock' => $pin_change_lock,
				];
			}
		}

		writeLog("cron", "resetPendingBalance\n" . json_encode($response));
		return $this->respond($response);
	}

	// setiap awal hari di awal bulan : 00 00 1 * * wget url &> /dev/null
	// untuk reset reminder notification
	function resetReminderNotification()
	{
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		if ($key == $this->key) {
			$this->User = new Users();
			$where = ['reminder_notification>' => 0];
			$users = $this->User->getUsers($where, 'user_id');
			$user_count = count($users);
			if($user_count > 0) {
				$this->db->transStart();

				// reset users.reminder_notification (where users.reminder_notification>0)
				$where = ['reminder_notification>' => 0];
				$reminder_notification = $this->User->where($where)
				->set(['reminder_notification' => 0])
				->update();

				$this->db->transComplete();


				if ($this->db->transStatus() === FALSE) {
					// transaction has problems
					$response->message = "Failed to perform task! #crn05c";
				} else {
					$response->success = true;
					$response->message = "Success";
					$response->data = [
						'reminder_notification' => $reminder_notification,
						'count' => $user_count,
					];
				}
			} else {
				$response->success = true;
				$response->message = "No users selected";
			}
		}

		writeLog("cron", "resetReminderNotification\n" . json_encode($response));
		return $this->respond($response);
	}

	// */20 08-17 23 * * wget url &> /dev/null
	// */20 08-17 28 * * wget url &> /dev/null
	// */20 08-17 29 * * wget url &> /dev/null
	// */20 08-17 30 * * wget url &> /dev/null
	// */20 08-17 31 * * wget url &> /dev/null
	// untuk kirim notifikasi pengingat agar melakukan transaksi jika punya saldo pending
	function sendReminderNotification()
	{
		$user_per_query = 100; // limit 100 user rows per hit
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		$index = (int)($this->request->getGet('index') ?? 0);
		if ($key == $this->key) {
			$user_ids = [];
			$notification_tokens = [];

			// select users.pending_balance > 0
			$this->User = new Users();
			$select = 'user_id,notification_token';
			$where = ['reminder_notification' => $index, 'pending_balance>' => 0];
			$users = $this->User->getUsers($where, $select, false, [$user_per_query, 0]);
			$user_count = count($users);
			if($user_count > 0) {
				for($i = 0; $i < $user_count; $i++) {
					array_push($user_ids, (int)$users[$i]->user_id);
					array_push($notification_tokens, $users[$i]->notification_token);
				}
				
				// update reminder_notification
				$this->db->transStart();
				$response->data['index'] = $index++;
				$this->User->update($user_ids, ['reminder_notification' => $index]);
				$this->db->transComplete();

				if ($this->db->transStatus() === FALSE) {
					// transaction has problems
					$response->message = "Failed to perform task! #crn06c";
				} else {
					$response->success = true;
					$response->message = "Success";
					$response->data += [
						'new_index' => $index,
						'count' => $user_count,
						'users' => $users,
					];

					// kirim notification
					helper('onesignal');
					$title = "Your bonus is awaiting!";
					$content = "Let's make a transaction to get your bonus.";
					// $days_left = 0; // for testing
					$days_left = date('t')-date('d');
					if($days_left == 0) $content = "Today is the last chance! ".$content;
					elseif($days_left == 1) $content = "Tomorrow is your last chance! ".$content;
					else $content = "Only $days_left days left! ".$content;
					$notification_data = ['page' => 'bonus'];
					$send_notif_app_2 = sendNotification($notification_tokens, $title, $content, $notification_data);
					$response->data['send_notif_app_2'] = $send_notif_app_2;
				}
			} else {
				$response->success = true;
				$response->message = "No users selected";
			}
		}

		writeLog("cron", "sendReminderNotification\n" . json_encode($response));
		return $this->respond($response);
	}


}
