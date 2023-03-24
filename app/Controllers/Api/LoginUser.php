<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use \Firebase\JWT\JWT;
use App\Models\Users;


class LoginUser extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
    }
    public function index()
    {
        $response = initResponse();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dev-app.tradeinplus.id/v1/api/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('username' => $username, 'password' => $password, 'app' => 'elektronik', 'device_id' => '12345'),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: bccb58c3f191ad6f32421a83b910ed5ca9e53058da5e503e36a6334aa1610010'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response);

        $message = $result->success;
        if ($result->success == false) {
            if (!$this->validate([
                'username'     => 'required',
                'password'     => 'required',
            ])) {
                return $this->response->setJSON(['success' => false, 'data' => null, "message" => \Config\Services::validation()->getErrors()])->setStatusCode(409);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(409);
            }
        } else {
            $dataUser = $result->data;
            $token = $dataUser->token;
            $refresh_token = $dataUser->refresh_token;

            //user
            $curl2 = curl_init();

            curl_setopt_array($curl2, array(
                CURLOPT_URL => 'https://dev-app.tradeinplus.id/v1/api/user',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token
                ),
            ));

            $response2 = curl_exec($curl2);
            curl_close($curl2);
            $result2 = json_decode($response2);
            $dataUser2 = $result2->data;
            $user_id = $dataUser2->user_id;
            $id_mitra = $dataUser2->id_mitra;
            $usernameUser = $dataUser2->username;
            $nama_te = $dataUser2->nama_te;
            helper("cookie");
            $key = $_SERVER['jwt.key'];
            set_cookie("token", $token);
            $decoded = JWT::decode($token, $key, [env('jwt.hash')]);
            set_cookie("id_mitra", $decoded->data->id_mitra);
            set_cookie("id_toko", $decoded->data->id_toko);
            set_cookie("user_id", $decoded->data->user_id);
            set_cookie("user", $decoded->data->user);
            set_cookie("fullname", $decoded->data->fullname);
            set_cookie("user_type", $decoded->data->user_type);
            set_cookie("nama_te", $decoded->data->nama_te);
            set_cookie("alamat_te", $decoded->data->alamat_te);

            return $this->response->setJSON(['success' => true, 'message' => 'Berhasil Login', 'data' => ['token' => $token, 'refresh_token' => $refresh_token]]);
        }
        helper('log');
        writeLog(
            "api",
            "LoginUser\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
