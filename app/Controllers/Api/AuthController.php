<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Users;
use App\Libraries\JWTCI4;

class AuthController extends BaseController
{
    use ResponseTrait;
    protected $request, $UsersModel;

    public function login()
    {
        if (!$this->validate([
            'username'     => 'required',
            'password'     => 'required',
        ])) {
            return $this->response->setJSON(['success' => false, 'data' => null, "message" => \Config\Services::validation()->getErrors()]);
        }

        $db = new Users;
        $user  = $db->where('username', $this->request->getVar('username'))->first();
        if ($user) {
            if (password_verify($this->request->getVar('password'), $user['password'])) {
                $jwt = new JWTCI4;
                $token = $jwt->token();

                return $this->response->setJSON(['token' => $token]);
            }
        } else {

            return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(409);
        }
    }
}
