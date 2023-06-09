<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Appointments;
use App\Models\AvailableDateTime;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\UserAdresses;
use App\Models\PaymentMethods;
use App\Models\AdminsModel;
use App\Libraries\Xendit;
use App\Libraries\FirebaseCoudMessaging;
use App\Libraries\Nodejs;
use App\Models\NotificationQueues;
use Firebase\JWT\JWT;
use DateTime;

class Appointment extends BaseController
{

    use ResponseTrait;

    protected $UsersAddress, $DeviceCheck, $DeviceCheckDetails, $Appointment, $NotificationQueue;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->DeviceCheck = new DeviceChecks();
        $this->Appointment = new Appointments();
        $this->AvailableDateTime = new AvailableDateTime();
        $this->UserAddress = new UserAdresses();
        $this->DeviceCheckDetails = new DeviceCheckDetails();
        $this->NotificationQueue = new NotificationQueues();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
        helper("format_helper");
    }

    // dipindah dari api/users/submitAppointment dan sudah disesuaikan
    public function submitAppoinment()
    {
        $response = initResponse();
        $data = [];
        $response_code = 200;
        $token = $this->request->getPost('token') ?? '';

        try {
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            if ($decoded) {
                $check_id = $decoded->data->check_id;

                $nameOwner = $this->request->getPost('name_owner') ?? '';
                $dateChoose = $this->request->getPost('date_choose') ?? '';
                $timeChoose = $this->request->getPost('time_choose') ?? '';

                // address transaction apointment
                $districtId = $this->request->getPost('district_id') ?? '1';
                $postal_code = $this->request->getPost('postal_code') ?? 'default';
                $addressName = $this->request->getPost('address_name') ?? 'default';
                $notes = $this->request->getPost('notes') ?? 'default';
                $longitude = $this->request->getPost('longitude') ?? '';
                $latitude = $this->request->getPost('latitude') ?? '';

                // payment transaction
                $paymentMethodId = $this->request->getPost('payment_method_id') ?? '';
                $accountNumber = $this->request->getPost('account_number') ?? 'default';
                $accountName = $this->request->getPost('account_name') ?? 'default';

                $rules = getValidationRules('saveAddress');
                if (!$this->validate($rules)) {
                    $errors = $this->validator->getErrors();
                    $response->message = "";
                    foreach ($errors as $error) $response->message .= "$error ";
                    $response_code = 400; // bad request
                } else {
                    // $tradein = $this->DeviceCheck->getDeviceChecks(['user_id' => $user_id, 'check_id' => $check_id], 'COUNT(check_id) as total_check');
                    // if ($tradein[0]->total_check == 1) {
                    $device_check = $this->DeviceCheck->getDeviceDetail(['dc.check_id' => $check_id], 'dc.check_id,check_code,user_id,finished_date,fcm_token,status,status_internal');
                    if ($device_check) {
                        $user_id = $device_check->user_id;
                        $data_appointment = $this->Appointment->getAppoinment(['user_id' => $user_id, 'check_id' => $check_id, 'deleted_at' => null], 'COUNT(appointment_id) as total_appoinment')[0];
                        if ($data_appointment->total_appoinment > 0) {
                            $response->message = "Transaction was finished"; //bingung kata katanya (jika check id dan user sudah pernah konek)
                        } elseif ($device_check->status < 7 || $device_check->status_internal != 2) {
                            $response->message = "Device Check is not completed. Please retry the device checking process."; //bingung kata katanya (jika check id dan user sudah pernah konek)
                        } else {
                            $PaymentMethod = new PaymentMethods();
                            $payment_method = $PaymentMethod->getPaymentMethod(['payment_method_id' => $paymentMethodId], 'name');
                            if (!$payment_method) {
                                $response->message = "Payment Method Id is invalid ($paymentMethodId)";
                            } else {
                                $Xendit = new Xendit();
                                $valid_bank_detail = $Xendit->validate_bank_detail($payment_method->name, $accountNumber); // first hit status=PENDING, need callback or another hit (by admin)

                                $this->db->transStart();
                                $dataAddress = [
                                    'district_id '    => $districtId,
                                    'postal_code '    => $postal_code,
                                    'check_id '     => $check_id,
                                    'address_name '    => htmlentities($addressName),
                                    'notes '        => htmlentities($notes),
                                    'longitude '    => $longitude,
                                    'latitude '        => $latitude,
                                    'updated_at'    => date('Y-m-d H:i:s'),
                                ];
                                $addressId = $this->UserAddress->insert($dataAddress);

                                $this->lockTime = env('app1.lock_2'); // in days

                                // x hari sejak submit foto ktp
                                // $finishedDate = new DateTime($device_check->finished_date);
                                // $lockUntilDate = $finishedDate->modify("+$this->lockTime days"); 

                                // x hari sejak submit appointment
                                $lockUntilDate = new DateTime();
                                $lockUntilDate->modify("+$this->lockTime days");

                                $dataDetail = [
                                    'payment_method_id' => $paymentMethodId,
                                    'account_number'    => $accountNumber,
                                    'account_name'        => htmlentities($accountName),
                                    'lock_until_date'   => $lockUntilDate->format('Y-m-d H:i:s'),
                                ];

                                $data += [
                                    'user_id'           => $user_id,
                                    'check_id '         => $check_id,
                                    'address_id  '      => $addressId,
                                    'phone_owner_name ' => $nameOwner,
                                    'choosen_date '     => $dateChoose,
                                    'choosen_time '     => $timeChoose,
                                    'created_at '       => date('Y-m-d H:i:s'),
                                    'updated_at '       => date('Y-m-d H:i:s'),
                                ];

                                $this->Appointment->insert($data);
                                $this->DeviceCheck->update($check_id, ['status_internal' => 3]); // on appointment
                                $this->DeviceCheckDetails->saveUpdate(['check_id' => $check_id], $dataDetail);


                                $this->db->transComplete();

                                if ($this->db->transStatus() === FALSE) {
                                    $response->message = $this->db->error();
                                    $response->success = false;
                                } else {
                                    $response->message = 'Success';
                                    $response->success = true;

                                    $data_log = array_merge($data, $dataDetail, $dataAddress);
                                    $this->log->in($device_check->check_code, 40, json_encode($data_log), false, $device_check->user_id, $device_check->check_id);

                                    // send push notif to admin web
                                    try {
                                        $token_notifications = [];
                                        $AdminModel = new AdminsModel();
                                        $tokens = $AdminModel->getTokenNotifications();
                                        foreach ($tokens as $token) $token_notifications[] = $token->token_notification;
                                        $fcm = new FirebaseCoudMessaging();
                                        $data_push_notif = ['type' => 'appointment', 'check_id' => $check_id];
                                        $send_fcm_push_web = $fcm->sendWebPush($token_notifications, "New Appointment", "Please review this new appointment request: $device_check->check_code", $data_push_notif);
                                        $nodejs = new Nodejs();
                                        $nodejs->emit('new-appointment', [
                                            'check_code' => $device_check->check_code,
                                            'check_id' => $device_check->check_id,
                                        ]);
                                        writeLog("api-notification_web", "submitAppointment\n" . json_encode($send_fcm_push_web));
                                    } catch (\Exception $e) {
                                        writeLog("api-notification_web", "submitAppointment\n" . json_encode($this->request->getPost()) . "\n" . $e->getMessage());
                                    }

                                    // add notification queue
                                    // notifikasi D+6, D+7 H-6, D+7 H-3 (hari ke 7 kurang 3 jam)
                                    try {
                                        $queue = [
                                            'token'         => $device_check->fcm_token,
                                            'token_type'    => 'fcm',
                                            'created_at'    => date('Y-m-d H:i:s'),
                                        ];
                                        $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+6 day"));
                                        $queue['data'] = json_encode([
                                            'type'      => 'appointment_confirm_reminder_1',
                                            'check_id'  => $device_check->check_id
                                        ]);
                                        $this->NotificationQueue->insert($queue);
                                        $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+7 day", strtotime("-6 hour")));
                                        $queue['data'] = json_encode([
                                            'type'      => 'appointment_confirm_reminder_2',
                                            'check_id'  => $device_check->check_id
                                        ]);
                                        $this->NotificationQueue->insert($queue);
                                        $queue['scheduled'] = date('Y-m-d H:i:s', strtotime("+7 day", strtotime("-3 hour")));
                                        $queue['data'] = json_encode([
                                            'type'      => 'appointment_confirm_reminder_3',
                                            'check_id'  => $device_check->check_id
                                        ]);
                                        $this->NotificationQueue->insert($queue);
                                    } catch (\Exception $e) {
                                        writeLog("api-notification_queue", "submitAppointment\n" . json_encode($this->request->getPost()) . "\n" . $e->getMessage());
                                    }
                                }
                            }
                        }
                    } else {
                        $response->message = "Transaction Not Found";
                        $response->success = false;
                    }
                }
            } else {
                $response->message = "Invalid token. ";
            }
        } catch (\Exception $e) {
            $response->message = $e->getMessage();
            if ($response->message == 'Expired token') $response_code = 401;
        }

        return $this->respond($response, $response_code);
    }

    // dipindah dari api/users/getAvailableDate
    public function getAvailableDate()
    {
        $response = initResponse();

        $where = [
            'type'  => 'date',
        ];

        $setRange = 3; // today, tomorrow and the day after tomorrow
        $listRange = [];
        $listDate = [];

        $dayofweek = date('w') + 1;
        for ($i = 0; $i <= $setRange; $i++) {
            $valuenya = afterAddDays($dayofweek, $i);
            $listRange[$i] = $valuenya;
            $listDate[$i] = getTimeDay($i);
        }
        $wherein = [
            'days'  => $listRange,
        ];
        $data = $this->AvailableDateTime->getAvailableDateTime($where, $wherein, 'status,days');

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->date = $listDate[$i];
        }
        $response->data = $data;
        $response->success = true;
        return $this->respond($response, 200);
    }

    // dipindah dari api/users/getAvailableTime
    public function getAvailableTime()
    {
        $response = initResponse();

        $days = $this->request->getPost('days') ?? '';
        if (empty($days)) {
            $response->message = "days is required.";
        } else {
            $where = [
                'type'  => 'time',
                'days'  => $days,
            ];

            $data = $this->AvailableDateTime->getAvailableDateTime($where, false, 'status,value');
            $dayofweek = date('w') + 1;
            $dateNow = date("h");
            $batasBawah = $dateNow + 2; // membatasi waktu awal appoinment

            $newData = $data;
            if ($dayofweek == $days) {
                foreach ($data as $row) {
                    $val_compare = substr($row->value, 0, 2);
                    if ($batasBawah > $val_compare) {
                        $row->status = "inactive";
                    }
                    $newData[] = $row;
                    // var_dump($val_compare);
                }
            }
            $response->data = $newData;
            $response->success = true;
        }
        return $this->respond($response, 200);
    }
}
