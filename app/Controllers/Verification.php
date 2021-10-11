<?php

namespace App\Controllers;

use App\Models\Referrals;
use App\Models\Users;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Verification extends Controller
{
	use ResponseTrait;
	
	public function index()
	{
		return redirect()->to(base_url('/verification/email'));
	}

	public function email($user_id = false, $otp = false)
	{
		helper('rest_api');
		helper('redis');
		helper('otp');
        $this->db = \Config\Database::connect();
		$this->UsersModel = new Users();
		$this->Referral = new Referrals();
		$response = initResponse('Invalid verification code.');
		if($otp || $user_id) {
			$user = $this->UsersModel->getUser(['user_id' => $user_id], 'email,phone_no_verified', 'user_id DESC');
            if ($user) {
                $email = $user->email;
                $redis = RedisConnect();
                $key = "otp:$email";
                $checkCodeOTP = checkCodeOTP($key, $redis);
                if ($checkCodeOTP->success) {
                    // OTP for $email is exist
                    if ($otp == $checkCodeOTP->data['otp']) {
                        $response->success = true;
                        $response->message = "Email is verified. ";
                        $data = ['email_verified' => 'y'];

                        $selectParent = "u.notification_token, u.user_id, u.name, referral.ref_level";
                        $dataParent = $this->Referral->getReferralWithDetailParent(['child_id' => $user_id], $selectParent);
                        $this->db->transStart();

                        if ($user->phone_no_verified == 'y') {
                            $data += ['status' => 'active'];
                            $response->message .= "You can start transaction. ";


                            $this->Referral->where(['child_id' => $user_id])
                                ->set([
                                    'status'        => 'active',
                                    'updated_at'    => date('Y-m-d H:i:s'),
                                ])->update();
                        }
                        $this->UsersModel->update($user_id, $data);
                        $this->db->transComplete();
                        if ($this->db->transStatus() === FALSE) {
                            // transaction has problems
                            $response->message = "Failed to perform task! #vrf01a";
                        } else {
                            $response->message = "Congratulation, your email is verified";
                            if ($user->phone_no_verified == 'y') {
                                // var_dump($dataParent);die;
                                foreach ($dataParent as $rowParent) {
                                    if($rowParent->ref_level == 1){
                                        try {
                                            $title = "Congatulation $rowParent->name";
                                            $content = "Congratulations $rowParent->name, You get 1 referral!";
                                            $notification_data = [
                                                'type'        => 'notif_activation_referal'
                                            ];
    
                                            $notification_token = $rowParent->notification_token;
                                            helper('onesignal');
                                            $send_notif_submission = sendNotification([$notification_token], $title, $content, $notification_data);
                                            $response->data['send_notif_submission'] = $send_notif_submission;
                                        } catch (\Exception $e) {
                                            // $response->message .= " But, unable to send notification: " . $e->getMessage();
                                        }
                                    }
                                }
                            }
							try {
								$redis->del($key);
							} catch(\Exception $ex) {}
                        }
                    } else {
                        $response->message = "Wrong Verification Code. ";
                    }
                } else {
                    $response->message = "Verification Code is invalid or expired. ";
                }
            } else {
                $response->message = "User does not exist ($user_id). ";
            }

		}
		$data = [
			'template' => 'email', 
			'd' => $response,
		];

		return view('verification/template', $data);

	}

}
