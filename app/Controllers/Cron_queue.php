<<<<<<< HEAD
<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
use App\Models\NotificationQueues;
use App\Libraries\FirebaseCoudMessaging;

class Cron_queue extends Controller
{
	use ResponseTrait;

	protected $DeviceCheck, $DeviceCheckDetail, $NotificationQueue;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->key = env('cron.key');
		helper('log');
		helper('rest_api');
	}

	// setiap 10 menit: */10 * * * * wget url &> /dev/null
	// untuk kirim notifikasi pada antrian sesuai tanggal terjadwal
	function sendNotificationQueue()
	{
		$cron_limit_per_query = 100; // limit 100 user rows per hit
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		if ($key == $this->key) {
			// select notification_queues sort scheduled asc
			$this->NotificationQueue = new NotificationQueues();
			$select = 'id,token,token_type,data,scheduled';
			$where = [
				'date_format(scheduled, "%Y-%m-%d %H: %i: %s")>' => date('Y-m-d H: i: s'), // sengaja H: i: s bukan H:i:s, karena CI 4 menambahkan spasi setelah : pada generated query
				'date_format(scheduled, "%Y-%m-%d %H: %i: %s")<=' => date('Y-m-d H: i: s', strtotime("+10 minute"))
			];
			$queues = $this->NotificationQueue->getQueues($where, $select, 'scheduled ASC', [$cron_limit_per_query, 0]);
			// die($this->db->getLastQuery());
			$queue_count = count($queues);
			if ($queue_count > 0) {
				helper('onesignal');
				$this->db->transStart();
				for ($i = 0; $i < $queue_count; $i++) {
					// queue diproses dan dihapus
					if ($this->proccessQueue($queues[$i])) {
						$this->NotificationQueue->delete($queues[$i]->id);
					}
				}
				$this->db->transComplete();


				if ($this->db->transStatus() === FALSE) {
					// transaction has problems
					$response->message = "Failed to perform task! #crn06c";
				} else {
					$response->success = true;
					$response->message = "Success";
					$response->data += [
						'count' => $queue_count,
						'queues' => $queues,
					];
				}
			} else {
				$response->success = true;
				$response->message = "No queues available";
			}
		}

		writeLog("cron_queue", "sendNotificationQueue\n" . json_encode($response));
		return $this->respond($response);
	}


	private function proccessQueue($queue)
	{
		$response = initResponse();
		try {
			$token = $queue->token;
			$data = json_decode($queue->data);
			if ($data) {
				$notification = $this->proccessNotificationData($data); // menentukan isi notifikasi
				// var_dump($notification);
				if ($notification->success) {
					$title = $notification->data['title'];
					$content = $notification->data['content'];
					$notification_data = $notification->data['notification_data'];

					// mengirim notifikasi
					if ($queue->token_type == 'fcm') {
						// for app_1
						$fcm = new FirebaseCoudMessaging();
						$send_notif_app_1 = $fcm->send($token, $title, $content, $notification_data);
						// var_dump($send_notif_app_1);
						if ($send_notif_app_1->success) $response->success = true;
						$response->data = $send_notif_app_1;
						// $response->message = $send_notif_app_1->message;
					} else {
						// for app_2
						$send_notif_app_2 = sendNotification([$token], $title, $content, $notification_data);
						// var_dump($send_notif_app_2);
						if ($send_notif_app_2->success) $response->success = true;
						$response->data = $send_notif_app_2;
					}
				} else {
					$response->message = $notification->message;
				}
			} else {
				$response->message = "Error Malformatted JSON\n" . $queue->data;
			}
		} catch (\Exception $e) {
			$response->message = "Unable to send notification: " . $e->getMessage();
		}
		writeLog("cron_queue", "proccessQueue\n" . json_encode($response));
		return $response->success;
	}


	private function proccessNotificationData($data)
	{
		// menentukan isi notifikasi (title,content,notificaton_data) sesuai $data->type
		$response = initResponse('No result found for ' . $data->type);
		switch ($data->type) {
			default:
				break;
			case 'appointment_reminder_1': // sama dengan appointment_reminder_2
			case 'appointment_reminder_2': // sama dengan appointment_reminder_3
			case 'appointment_reminder_3': {
					// cek apakah lock
					$this->DeviceCheck = new DeviceChecks();
					$device_check = $this->DeviceCheck->getDevice(['check_id' => $data->check_id], 'status_internal');
					if (!$device_check) {
						$response->message = "check_id $data->check_id is not found";
					} else {
						if ($device_check->status_internal != 2) {
							// jika bukan status on apppintment, tidak perlu kirim notifikasi
							$response->message = "check_id $data->check_id is not waiting appointment";
						} else {
							$response->success = true;
							$response->message = "OK";
							if ($data->type == 'appointment_reminder_1') {
								// D+1
								$response->data = [
									'title'				=> "One step closer!",
									'content'			=> "To sell your smartphone, let's make an appointment",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} elseif ($data->type == 'appointment_reminder_2') {
								// D+2
								$response->data = [
									'title'				=> "Let's make an appointment",
									'content'			=> "Last chance to sell your smartphone! You only need to make an appointment to sell your smartphone",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} else {
								// D+3 H-1
								$response->data = [
									'title'				=> "Let's make an appointment. Less than 1 hour left!",
									'content'			=> "Last minutes to sell your smartphone! You only need to make an appointment to sell your smartphone",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							}
						}
					}


					break;
				}
			case 'appointment_confirm_reminder_1': // sama dengan appointment_confirm_reminder_2
			case 'appointment_confirm_reminder_2': // sama dengan appointment_confirm_reminder_3
			case 'appointment_confirm_reminder_3': {
					// cek apakah lock
					$this->DeviceCheck = new DeviceChecks();
					$device_check = $this->DeviceCheck->getDevice(['check_id' => $data->check_id], 'status_internal');
					if (!$device_check) {
						$response->message = "check_id $data->check_id is not found";
					} else {
						if (!in_array($device_check->status_internal, [3,4])) {
							// jika bukan status on apppintment, tidak perlu kirim notifikasi
							$response->message = "check_id $data->check_id is not on appointment";
						} else {
							$response->success = true;
							$response->message = "OK";
							if ($data->type == 'appointment_confirm_reminder_1') {
								// D+6
								$response->data = [
									'title'				=> "Wait for your courier!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} elseif ($data->type == 'appointment_confirm_reminder_2') {
								// D+7 H-6
								$response->data = [
									'title'				=> "Wait for your courier! Less than 6 hours left!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} else {
								// D+7 H-3
								$response->data = [
									'title'				=> "Wait for your courier! Less than 3 hours left!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							}
						}
					}


					break;
				}
		}

		return $response;
	}
}
=======
<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceChecks;
use App\Models\DeviceCheckDetails;
use App\Models\NotificationQueues;
use App\Libraries\FirebaseCoudMessaging;

class Cron_queue extends Controller
{
	use ResponseTrait;

	protected $DeviceCheck, $DeviceCheckDetail, $NotificationQueue;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->key = env('cron.key');
		helper('log');
		helper('rest_api');
	}

	// setiap 10 menit: */10 * * * * wget url &> /dev/null
	// untuk kirim notifikasi pada antrian sesuai tanggal terjadwal
	function sendNotificationQueue()
	{
		$cron_limit_per_query = 100; // limit 100 user rows per hit
		$response = initResponse('Unauthorized.');
		$key = $this->request->getGet('key');
		if ($key == $this->key) {
			// select notification_queues sort scheduled asc
			$this->NotificationQueue = new NotificationQueues();
			$select = 'id,token,token_type,data,scheduled';
			$where = [
				'date_format(scheduled, "%Y-%m-%d %H: %i: %s")>' => date('Y-m-d H: i: s'), // sengaja H: i: s bukan H:i:s, karena CI 4 menambahkan spasi setelah : pada generated query
				'date_format(scheduled, "%Y-%m-%d %H: %i: %s")<=' => date('Y-m-d H: i: s', strtotime("+10 minute"))
			];
			$queues = $this->NotificationQueue->getQueues($where, $select, 'scheduled ASC', [$cron_limit_per_query, 0]);
			// die($this->db->getLastQuery());
			$queue_count = count($queues);
			if ($queue_count > 0) {
				helper('onesignal');
				$this->db->transStart();
				for ($i = 0; $i < $queue_count; $i++) {
					// queue diproses dan dihapus
					if ($this->proccessQueue($queues[$i])) {
						$this->NotificationQueue->delete($queues[$i]->id);
					}
				}
				$this->db->transComplete();


				if ($this->db->transStatus() === FALSE) {
					// transaction has problems
					$response->message = "Failed to perform task! #crn06c";
				} else {
					$response->success = true;
					$response->message = "Success";
					$response->data += [
						'count' => $queue_count,
						'queues' => $queues,
					];
				}
			} else {
				$response->success = true;
				$response->message = "No queues available";
			}
		}

		writeLog("cron_queue", "sendNotificationQueue\n" . json_encode($response));
		return $this->respond($response);
	}


	private function proccessQueue($queue)
	{
		$response = initResponse();
		try {
			$token = $queue->token;
			$data = json_decode($queue->data);
			if ($data) {
				$notification = $this->proccessNotificationData($data); // menentukan isi notifikasi
				// var_dump($notification);
				if ($notification->success) {
					$title = $notification->data['title'];
					$content = $notification->data['content'];
					$notification_data = $notification->data['notification_data'];

					// mengirim notifikasi
					if ($queue->token_type == 'fcm') {
						// for app_1
						$fcm = new FirebaseCoudMessaging();
						$send_notif_app_1 = $fcm->send($token, $title, $content, $notification_data);
						// var_dump($send_notif_app_1);
						if ($send_notif_app_1->success) $response->success = true;
						$response->data = $send_notif_app_1;
						// $response->message = $send_notif_app_1->message;
					} else {
						// for app_2
						$send_notif_app_2 = sendNotification([$token], $title, $content, $notification_data);
						// var_dump($send_notif_app_2);
						if ($send_notif_app_2->success) $response->success = true;
						$response->data = $send_notif_app_2;
					}
				} else {
					$response->message = $notification->message;
				}
			} else {
				$response->message = "Error Malformatted JSON\n" . $queue->data;
			}
		} catch (\Exception $e) {
			$response->message = "Unable to send notification: " . $e->getMessage();
		}
		writeLog("cron_queue", "proccessQueue\n" . json_encode($response));
		return $response->success;
	}


	private function proccessNotificationData($data)
	{
		// menentukan isi notifikasi (title,content,notificaton_data) sesuai $data->type
		$response = initResponse('No result found for ' . $data->type);
		switch ($data->type) {
			default:
				break;
			case 'appointment_reminder_1': // sama dengan appointment_reminder_2
			case 'appointment_reminder_2': // sama dengan appointment_reminder_3
			case 'appointment_reminder_3': {
					// cek apakah lock
					$this->DeviceCheck = new DeviceChecks();
					$device_check = $this->DeviceCheck->getDevice(['check_id' => $data->check_id], 'status_internal');
					if (!$device_check) {
						$response->message = "check_id $data->check_id is not found";
					} else {
						if ($device_check->status_internal != 2) {
							// jika bukan status on apppintment, tidak perlu kirim notifikasi
							$response->message = "check_id $data->check_id is not waiting appointment";
						} else {
							$response->success = true;
							$response->message = "OK";
							if ($data->type == 'appointment_reminder_1') {
								// D+1
								$response->data = [
									'title'				=> "One step closer!",
									'content'			=> "To sell your smartphone, let's make an appointment",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} elseif ($data->type == 'appointment_reminder_2') {
								// D+2
								$response->data = [
									'title'				=> "Let's make an appointment",
									'content'			=> "Last chance to sell your smartphone! You only need to make an appointment to sell your smartphone",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} else {
								// D+3 H-1
								$response->data = [
									'title'				=> "Let's make an appointment. Less than 1 hour left!",
									'content'			=> "Last minutes to sell your smartphone! You only need to make an appointment to sell your smartphone",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							}
						}
					}


					break;
				}
			case 'appointment_confirm_reminder_1': // sama dengan appointment_confirm_reminder_2
			case 'appointment_confirm_reminder_2': // sama dengan appointment_confirm_reminder_3
			case 'appointment_confirm_reminder_3': {
					// cek apakah lock
					$this->DeviceCheck = new DeviceChecks();
					$device_check = $this->DeviceCheck->getDevice(['check_id' => $data->check_id], 'status_internal');
					if (!$device_check) {
						$response->message = "check_id $data->check_id is not found";
					} else {
						if (!in_array($device_check->status_internal, [3,4])) {
							// jika bukan status on apppintment, tidak perlu kirim notifikasi
							$response->message = "check_id $data->check_id is not on appointment";
						} else {
							$response->success = true;
							$response->message = "OK";
							if ($data->type == 'appointment_confirm_reminder_1') {
								// D+6
								$response->data = [
									'title'				=> "Wait for your courier!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} elseif ($data->type == 'appointment_confirm_reminder_2') {
								// D+7 H-6
								$response->data = [
									'title'				=> "Wait for your courier! Less than 6 hours left!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							} else {
								// D+7 H-3
								$response->data = [
									'title'				=> "Wait for your courier! Less than 3 hours left!",
									'content'			=> "Please wait for our courier at the specified place and time",
									'notification_data'	=> [
										'check_id'	=> $data->check_id,
										'type'		=> 'final_result'
									],
								];
							}
						}
					}


					break;
				}
		}

		return $response;
	}
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
