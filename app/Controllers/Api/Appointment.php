<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Appointments;
use App\Models\AvailableDateTime;
use App\Models\DeviceChecks;
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
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
    }

    // dipindah ke api/users/submitAppointment dan disesuaikan
    public function submitAppoinment()
    {
        $response = initResponse();
        $data = [];
        $token = $this->request->getPost('token') ?? '';

        try {
            $decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
            if ($decoded) {
                $check_id = $decoded->data->check_id;

                $nameOwner = $this->request->getPost('name_owner') ?? '';
                $addressId = $this->request->getPost('address_id') ?? '';
                $paymentId = $this->request->getPost('payment_id') ?? '';
                $dateChoose = $this->request->getPost('date_choose') ?? '';
                $timeChoose = $this->request->getPost('time_choose') ?? '';
                // $check_id = $this->request->getPost('check_id') ?? '';

                // $device_checks = $this->DeviceCheck->getDeviceChecks(['user_id' => $user_id, 'check_id' => $check_id], 'COUNT(check_id) as total_check');
                // if ($device_checks[0]->total_check == 1) {
                $device_checks = $this->DeviceCheck->getDevice(['check_id' => $check_id], 'user_id');
                if ($device_checks) {
                    $user_id = $device_checks->user_id;
                    $data_check = $this->Appointments->getAppoinment(['user_id' => $user_id, 'check_id' => $check_id, 'deleted_at' => null], 'COUNT(appointment_id) as total_appoinment')[0];
                    if($data_check->total_appoinment >0) {
                        $response->message = "Transaction was finished"; //bingung kata katanya (jika check id dan user sudah pernah konek)
                        $response->success = false;
                    } else {
                        $data += [
                            'user_id'           => $user_id,
                            'check_id '         => $check_id,
                            'address_id  '      => $addressId,
                            'user_payment_id  ' => $paymentId,
                            'phone_owner_name ' => $nameOwner,
                            'choosen_date '     => $dateChoose,
                            'choosen_time '     => $timeChoose,
                            'created_at '       => date('Y-m-d H:i:s'),
                            'updated_at '       => date('Y-m-d H:i:s'),
                        ];
                        
                        $this->Appointments->insert($data);
                        $this->DeviceCheck->update($check_id, ['status_internal' => 3]); // on appointment

                        if ($this->db->transStatus() === FALSE) {
                            $response->message = $this->db->error();
                            $response->success = false;
                            $this->db->transRollback();
                        } else {
                            $response->message = 'Success';
                            $response->success = true;
                            $this->db->transCommit();
                        }
                        $this->db->transComplete();
                    }
                    $this->db->transStart();

                    
                } else {
                    $response->message = "Transaction Not Found";
                    $response->success = false;
                }
            } else {
                $response->message = "Invalid token. ";
            }
        } catch(\Exception $e) {
            $response->message = $e->getMessage();
            if($response->message == 'Expired token') $response_code = 401;
        }

        return $this->respond($response, 200);
    }

    // dipindah dari api/users/getAvailableDate
    public function getAvailableDate(){
        $response = initResponse();

        $where = [
            'type'  => 'date',
        ];

        $setRange = 2; // today, tomorrow and the day after tomorrow
        $listRange = [];
        $listDate = [];

        $dayofweek = date('w') + 1;
        for ($i=0; $i <= $setRange; $i++) { 
            $valuenya = $this->afterAddDays($dayofweek, $i);
            $listRange[$i] = $valuenya;
            $listDate[$i] = $this->getTimeDay($i);
        }
        $wherein = [
            'days'  => $listRange,
        ];
        $data = $this->AvailableDateTime->getAvailableDateTime($where, $wherein, 'status,days');
        
        for ($i=0; $i < count($data); $i++) { 
            $data[$i]->date = $listDate[$i];
        }
        $response->data = $data;
        $response->success = true;
        return $this->respond($response, 200);
    }

    // dipindah dari api/users/getAvailableTime
    public function getAvailableTime(){
        $response = initResponse();

        $days = $this->request->getPost('days') ?? '';
        if(empty($days)) {
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

    private function afterAddDays($current, $add){
        $value = $current + $add;
        $value = $value % 7;
        return $value;
    }

    function getTimeDay($interval)
    {
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        
        $now = date("Y-m-d", time() + ($interval * 60 * 60 * 24)); // in hours
        return $now;
    }
}
