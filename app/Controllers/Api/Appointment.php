<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Appointments;
use App\Models\AvailableDateTime;
use App\Models\DeviceCheckDetails;
use App\Models\DeviceChecks;
use App\Models\UserAdresses;
use App\Models\PaymentMethods;
use App\Libraries\Xendit;
use Firebase\JWT\JWT;

class Appointment extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefreshTokens, $DeviceCheck;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->DeviceCheck = new DeviceChecks();
        $this->Appointments = new Appointments();
        $this->AvailableDateTime = new AvailableDateTime();
        $this->UserAddress = new UserAdresses();
        $this->DeviceCheckDetails = new DeviceCheckDetails();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
        helper("format_helper");
    }

    // dipindah ke api/users/submitAppointment dan disesuaikan
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
                    
                    // $check_id = $this->request->getPost('check_id') ?? '';

                    // $device_checks = $this->DeviceCheck->getDeviceChecks(['user_id' => $user_id, 'check_id' => $check_id], 'COUNT(check_id) as total_check');
                    // if ($device_checks[0]->total_check == 1) {
                    $device_checks = $this->DeviceCheck->getDevice(['check_id' => $check_id], 'user_id');
                    if ($device_checks) {
                        $user_id = $device_checks->user_id;
                        $data_check = $this->Appointments->getAppoinment(['user_id' => $user_id, 'check_id' => $check_id, 'deleted_at' => null], 'COUNT(appointment_id) as total_appoinment')[0];
                        if ($data_check->total_appoinment > 0) {
                            $response->message = "Transaction was finished"; //bingung kata katanya (jika check id dan user sudah pernah konek)
                            $response->success = false;
                        } else {
                            $PaymentMethod = new PaymentMethods();
                            $payment_method = $PaymentMethod->getPaymentMethod(['payment_method_id' => $paymentMethodId], 'name');
                            if(!$payment_method) {
                                $response->message = "Payment Method Id is invalid ($paymentMethodId)";
                            } else {
                                $Xendit = new Xendit();
                                $valid_bank_detail = $Xendit->validate_bank_detail($payment_method->name, $accountNumber); // first hit status=PENDING, need callback or another hit (by admin)
                
                                $this->db->transStart();
                                $dataAddress = [
                                    'district_id '	=> $districtId,
                                    'postal_code '	=> $postal_code,
                                    'check_id '         => $check_id,
                                    'address_name '	=> $addressName,
                                    'notes '		=> $notes,
                                    'longitude '	=> $longitude,
                                    'latitude '		=> $latitude,
                                    'updated_at'    => date('Y-m-d H:i:s'),
                                ];
                                $addressId = $this->UserAddress->insert($dataAddress);

                                $dataPayment = [
                                    'payment_method_id'	    => $paymentMethodId ,
                                    'account_number'	    => $accountNumber,
                                    'account_name'	        => $accountName,
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

                                $this->Appointments->insert($data);
                                $this->DeviceCheck->update($check_id, ['status_internal' => 3]); // on appointment
                                $this->DeviceCheckDetails->saveUpdate(['check_id' => $check_id], $dataPayment); // on appointment
                                

                                $this->db->transComplete();

                                if ($this->db->transStatus() === FALSE) {
                                    $response->message = $this->db->error();
                                    $response->success = false;
                                } else {
                                    $response->message = 'Success';
                                    $response->success = true;
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
            // var_dump($e);die;
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

        $setRange = 2; // today, tomorrow and the day after tomorrow
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

            $response->data = $data;
            $response->success = true;
        }
        return $this->respond($response, 200);
    }
}
