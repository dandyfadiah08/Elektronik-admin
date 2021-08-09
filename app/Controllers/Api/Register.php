<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users;
use App\Models\Referrals;
use Redis;

class Register extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel, $RefferalModel;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        // $this->RefferalModel = new Referrals();
    }

    public function index()
    {
        $response = (object)[
            'success'    => false,
            'message'    => 'No message',
        ];

        $method = strtoupper($this->request->getMethod());
        if($method == 'POST') {
            $name = $this->request->getPost('name') ?? '';
            $email = $this->request->getPost('email') ?? '';
            $phone = $this->request->getPost('phone') ?? '';
            $type = $this->request->getPost('type') ?? 2;
            $refferalCode = $this->request->getPost('refferalCode') ?? '';
            $nik = $this->request->getPost('nik') ?? '';
            $generateRefferalCode = $this->generateRefferalCode();
            $strtype = 'agent';

            if(empty($name)) {
                $response->message = "Name is required.";
            } elseif(empty($email)) {
                $response->message = "Email is required.";
            // } elseif(empty($email)) {
                // cek apakah email unik
            //     $response->message = "Email is required.";
            } elseif(empty($phone)) {
                $response->message = "Phone is required.";
            // } elseif(empty($phone)) {
                // cek apakah no_hp unik
            //     $response->message = "Phone is required.";
            } elseif(substr($phone, 0, 3) != '628') {
                $response->message = 'Phone is invalid, should start with 628.';
            } elseif((int)$type != 1 && (int)$type != 2) {
                $response->message = "Type is invalid. ($type)";
            } elseif((int)$type == 1 && empty($nik)) {
                $response->message = 'NIK is required.';
            } else {
                $data = [];

                $hasError = false;
                if ($type == 1) {
                    // agent, upload photo_ktp and add nik
                    // cek jika nik unik

                    $validated = $this->validate([
                        'photo_id' => [
                            'rules' => 'uploaded[photo_id]|max_size[photo_id,1024]',
                        ]
                    ]);
        
                    if ($validated) {
                        $photo_id = $this->request->getFile('photo_id');
                        $newName = $photo_id->getRandomName() . '.' . $photo_id->getExtension();
                        if ($photo_id->move('uploads/images/', $newName)) {
                            $data += [
                                'photo_id' => $newName,
                            ];
                        } else {
                            $response->message = "Error upload file";
                            $hasError = true;
                        }
                    } else {
                        $error = '';
                        if ($this->validator->hasError('photo_id')) {
                            $error = $this->validator->getError('photo_id');
                        }
                        $response->message = $error;
                        $hasError = true;
                    }
                } else {
                    $strtype = 'nonagent';
                }
        
                $data = [
                    'nik' => $nik,
                    'phone_no' => $phone,
                    'name' => $name,
                    'email' => $email,
                    'ref_code' => $generateRefferalCode,
                    'status' => 'pending',
                    'type' => $strtype,
                ];

                if(!$hasError) {
                    $this->db->transStart();
                    $this->UsersModel->insert($data);
            
                    if ($this->db->transStatus() === FALSE) {
                        $response->message = $this->db->error();
                        $response->success = false;
                        $this->db->transRollback();
                    } else {
                        $user_id = $this->UsersModel->getInsertID();
                        $this->cekParentRefferal($refferalCode, $user_id);
                        $response->success = true;
                        $response->message = 'Pendaftaran berhasil. Silahkan verifikasi nomor handphone kamu.';
                        // kirim otp
                        $otp = $this->generateCodeOTP();
                        if(!$otp->success) {
                            // tidak berhasil buat otp, sarankan klik resendOtp ?
                            $response->message .= ' Kode OTP mungkin perlu di kirim ulang.';
                        }
                        $this->db->transCommit();
                    }
                    $this->db->transComplete();
                }
            }
        } else {
            $response->message = 'Method not allowed';
        }
        return $this->respond($response, 200);
    }

    public function resendOtp()
    {
        $response = (object)[
            'success'    => false,
            'message'    => 'No message',
        ];
        $phone = $this->request->getPost('phone') ?? '';
        if(empty($phone)) {
            $response->message = "Phone number is required.";
        } else {
            //cek dulu no hp ada di db atau tidak
            $response = $this->generateCodeOTP($phone);
            if($response->success) {
                // kirim sms
                helper('sms');
                $sendSMS = sendSmsOtp($phone, $response->message);
                $response->message = $sendSMS->message;
                if($sendSMS->success) $response->success = true;
            }
        }

        return $this->respond($response, 200);
    }

    public function test($no_hp = '0812345679') {
        $response = (object)[
            'success'    => false,
            'message'    => 'no message',
        ];
        $response = $this->generateCodeOTP($no_hp);
        if($response->success) {
            // kirim sms
            helper('sms');
            $sendSMS = sendSmsOtp($no_hp, $response->message);
            $response->message = $sendSMS->message;
            if($sendSMS->success) $response->success = true;
        }
        return $this->respond($response, 200);
    }

    public function generateCodeOTP($no_hp = false) {
        $response = (object)[
            'success'    => false,
            'message'    => 'Phone number is required.',
        ];
        if($no_hp) {
            try {
                $redis = new Redis() or false;
                $redis->connect(env('redis.host'), env('redis.port'));
                $redis->auth(env('redis.password'));
                $redis->get(env('redis.password'));
                $key = "otp_$no_hp";
                $otp = $redis->get($key);
                if($otp !== FALSE) {
                    // sudah ada dan belum boleh kirim sms lagi seharusnya
                    $second = $redis->ttl($key);
                    $response->message = "Mohon menunggu $second detik lagi";
                } else {
                    // belum ada, buat baru
                    $otp = $this->generateCode();
                    $redis->setex($key, env('otp.duration'), $otp); // jika pakai otp lama, akan diupdate expired nya
                    $response->message = $otp;
                    $response->success = true;
                }
            } catch(\Exception $e) {
                $response->message = $e->getMessage();
            }
        }
        return $response;
    }
    private function generateCode($length = 0)
    {
        $length = (int)$length < 1 ? env('otp.length') : $length;
        $pool = '1234567890';
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $otp;
    }

    private function generateRefferalCode($length = 6)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $word = '';
        for ($i = 0; $i < $length; $i++) {
            $word .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $word;
    }
    private function cekParentRefferal($refferalCode, $idUser)
    {
        $UserParent = $this->UsersModel->getUser(['ref_code' => $refferalCode], 'user_id,status');
        if ($UserParent) {
            $id_parent = $UserParent['user_id'];
            $refferalParent = $this->RefferalModel->getRefferal(['parent_id' => $id_parent], 'parent_id,status, ref_level', 'ref_level DESC');
            $level_parent = 1;
            if ($refferalParent) {
                $level_parent = $refferalParent['ref_level'];
                if ($level_parent >= 2) $level_parent = 2;
                else $level_parent = (int) $level_parent + 1;
            }
            $dataReff = [
                'parent_id' => $id_parent,
                'child_id' => $idUser,
                'status' => 'pending',
                'ref_level' => $level_parent,
            ];
            $this->RefferalModel->insert($dataReff);

            if ($this->db->transStatus() === FALSE) {

                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
