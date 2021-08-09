<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Users;
use App\Models\Referrals;

class Api extends BaseController
{

    use ResponseTrait;

    protected $UsersModel, $RefferalModel;

    public function __construct()
    {
        $this->UsersModel = new Users();
        $this->db = \Config\Database::connect();
        $this->RefferalModel = new Referrals();
    }

    public function register()
    {

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $refferalCode = $this->request->getPost('refferalCode');
        $generateRefferalCode = $this->generateRefferalCode();
        $type = $this->request->getPost('type');
        $nik = $this->request->getPost('nik');
        $strtype = 'Agent';

        $response = (object)[
            'success'    => false,
            'message'    => 'No message',
        ];
        $data = array();

        if ($type != 1) {
            $strtype = 'nonagent';
        } else {
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
                        // 'created_at' => date('Y-m-d H:i:s'),
                        'photo_id' => $newName,
                    ];
                } else {
                    $response->message = "Error upload file";
                    $response->success = false;
                    return $this->respond($response, 200);
                }
            } else {
                $error = '';
                if ($this->validator->hasError('photo_id')) {
                    $error = $this->validator->getError('photo_id');
                }
                $response->message = $error;
                $response->success = false;
                return $this->respond($response, 200);
            }
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

        $this->db->transStart();
        $this->UsersModel->insert($data);

        if ($this->db->transStatus() === FALSE) {
            $response->message = $this->db->error();
            $response->success = false;
            $this->db->transRollback();
        } else {
            $user_id = $this->UsersModel->getInsertID();
            $this->cekParentRefferal($refferalCode, $user_id);
            $response->message = 'Pendaftaran sukses';
            $response->success = true;
            $this->db->transCommit();
        }
        $this->db->transComplete();
        return $this->respond($response, 200);
    }

    public function login()
    {

        $phone_no = $this->request->getPost('phone_no');
        $user = $this->UsersModel->getUser(['phone_no' => $phone_no], 'user_id,phone_no,email,status');

        $response = (object)[
            'success'    => false,
            'message'    => 'No message',
        ];

        if (!$foo = cache('foo')) {
            die('Saving to the cache!<br />');
            $foo = 'foobarbaz!';

            // Save into the cache for 5 minutes
            cache()->save('foo', $foo, 20);
        }

        die($foo);


        if ($user) {
            if ($user['status'] == 'banned') {
                $response->message = "Your account was banned";
                $response->success = false;
            } else {
                $OTPCode = $this->generateCodeOTP();
                //save to redis
                $response->message = "ini otp nya " . $OTPCode;
                $response->success = true;
            }
        } else {
            $response->message = 'Phone number doesnt exist';
            $response->success = false;
        }
        return $this->respond($response, 200);
    }

    public function resendOtp()
    {
        $response = (object)[
            'success'    => false,
            'message'    => 'No message',
        ];
        $OTPCode = $this->generateCodeOTP();
        // save to redis
        $response->message = "ini otp nya " . $OTPCode;
        $response->success = false;
        return $this->respond($response, 200);
    }


    private function generateCodeOTP($length = 6)
    {
        $pool = '1234567890';
        $word = '';
        for ($i = 0; $i < $length; $i++) {
            $word .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $word;
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
