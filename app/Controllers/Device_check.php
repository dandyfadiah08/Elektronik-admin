<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceChecks;
use App\Models\AdminRolesModel;
use App\Models\DeviceCheckDetails;
use App\Models\MasterPrices;
use App\Models\Users;
use App\Libraries\FirebaseCoudMessaging;

class Device_check extends BaseController
{
	use ResponseTrait;

	protected $DeviceCheck, $AdminRole;

	public function __construct()
	{
		$this->DeviceCheck = new DeviceChecks();
		$this->DeviceCheckDetail = new DeviceCheckDetails();
		$this->AdminRole = new AdminRolesModel();
		helper('rest_api');
		helper('grade');
		helper('validation');
		helper('role');
	}

	public function index()
	{
	}

	function manual_grade()
	{
		$response_code = 401;
		$response = initResponse('Unauthorized.');
		if (session()->has('admin_id')) {
			// $grades = ['S', 'A', 'B', 'C', 'D', 'E', 'Reject'];
			$grades = getGradeDefinition('all'); // get all grades
			$grades += ['Reject' => 'Rejected']; // add reject

			$check_id = $this->request->getPost('check_id');
			$grade = $this->request->getPost('grade');
			$fullset = $this->request->getPost('fullset') ?? 0;
			$rules = ['check_id' => getValidationRules('check_id')];
			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();
				$response->message = "";
				foreach ($errors as $error) $response->message .= "$error ";
				$response_code = 400; // bad request
			} elseif (!array_key_exists($grade, $grades)) {
				$response->message = 'Grade tidak diketahui: ' . $grade;
			} else {
				$role = $this->AdminRole->find(session()->admin_id);
				$check_role = checkRole($role, 'r_survey');
				if (!$check_role->success) {
					$response->message = $check_role->message;
				} else {
					// update survey_fullset
					$this->DeviceCheckDetail
					->where(['check_id' => $check_id])
					->set(['survey_fullset' => $fullset])
					->update();
					$survey_by = session()->admin_id;
					$survey_name = session()->username_id;
					$response = $this->survey($check_id, $grade, $survey_by, $survey_name);
					if (!$response->success) $response_code = 400;
					else $response_code = 200;
				}
			}
		}
		return $this->respond($response, $response_code);
	}

	private function survey($check_id, $grade, $survey_id, $survey_name, $survey_log = 'manual', $send_notification = true, $quiz = [1, 1, 1, 1])
	{
		$response = initResponse('Failed add grade!');

		$select = 'dc.status,dc.price_id,check_detail_id,survey_fullset,user_id,brand,storage,type,fcm_token';
		$where = array('dc.check_id' => $check_id, 'dc.status' => 4, 'dc.deleted_at' => null);
		$device_check = $this->DeviceCheck->getDeviceDetail($where, $select);
		if (!$device_check) {
			$response->message = "Invalid check_id $check_id";
		} else {
			$response->success = true;
			$response->message = "Success give $grade grade. ";
			$price = 0;
			$fullset_price = 0;
			if ($grade != 'Reject') {
				// $where_price = ['promo_id' => $device_check->promo_id, 'brand' => $device_check->brand, 'model' => $device_check->model, 'storage' => $device_check->storage, 'deleted_at' => null];
				$where_price = ['price_id' => $device_check->price_id, 'deleted_at' => null];
				$select_price = 'price_s,price_a,price_b,price_c,price_d,price_e,price_fullset';
				$MasterPrice = new MasterPrices();
				$master_price = $MasterPrice->getPrice($where_price, $select_price); //, 'price_id DESC');
				if (!$master_price) {
					$response->message = "Invalid price ($device_check->price_id)!";
					$response->success = false;
				} else {
					// define $price
					if ($grade == 'S') $price = $master_price->price_s;
					elseif ($grade == 'A') $price = $master_price->price_a;
					elseif ($grade == 'B') $price = $master_price->price_b;
					elseif ($grade == 'C') $price = $master_price->price_c;
					elseif ($grade == 'D') $price = $master_price->price_d;
					elseif ($grade == 'E') $price = $master_price->price_e;

					// define $fullset_price & update $price
					if($device_check->survey_fullset == 1) {
						$fullset_price = $master_price->price_fullset;
						$price += $price;
					}
					helper('number');
					$response->message .= "(".number_to_currency($price, "IDR").")";
				}
			}

			if ($response->success) {
				// update data
				$data_update = [
					'price'		=> $price,
					'grade'		=> $grade,
					'status'	=> 5,
				];
				$data_update_detail = [
					'fullset_price' => $fullset_price,
					'survey_quiz_1' => $quiz[0],
					'survey_quiz_2' => $quiz[1],
					'survey_quiz_3' => $quiz[2],
					'survey_quiz_4' => $quiz[3],
					'survey_id'		=> $survey_id,
					'survey_name'	=> $survey_name,
					'survey_log'	=> $survey_log,
					'survey_date'	=> date('Y-m-d H:i:s'),
				];
				$this->DeviceCheck->update($check_id, $data_update);
				$this->DeviceCheckDetail->update($device_check->check_detail_id, $data_update_detail);

				// send notification
				if($send_notification) {
					$title = $grade == 'Reject' ? "Sorry" : "Congatulation, Your $device_check->type price is ready!";
					$content = $grade == 'Reject' 
					? "Unfortunately, we could not calculate a price for your phone."
					: "Your phone $device_check->brand $device_check->type $device_check->storage price is ".number_to_currency($price, "IDR");
					$notification_data = [
						'check_id'	=> $check_id,
						'type'		=> 'final_result'
					];
	
					// for app_1
					$fcm = new FirebaseCoudMessaging();
					$send_notif_app_1 = $fcm->send($device_check->fcm_token, $title, $content, $notification_data);
					$response->data['send_notif_app_1'] = $send_notif_app_1;

					// for app_2
					// get notification_token from $device_check->user_id
					$user_model = new Users();
					$user = $user_model->getUser($device_check->user_id);
					if($user) {
						$notification_token = $user->notification_token;
						// var_dump($notification_token);die;
						helper('onesignal');
						$send_notif_app_2 = sendNotification([$notification_token], $title, $content, $notification_data);
						$response->data['send_notif_app_2'] = $send_notif_app_2;
					}
				}
			}
		}
		return $response;
	}
}
