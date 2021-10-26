<<<<<<< HEAD
<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Libraries\Token as T;
use App\Models\Referrals;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use DateTime;

class Token extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        $this->ReferralModel = new Referrals();
        $this->RefreshTokens = new RefreshTokens();
        helper('rest_api');
    }

    public function refresh()
    {
        $response = initResponse();

        $token = $this->request->getPost('token') ?? '';
        if(empty($token)) {
            $response->message = "Token is required. ";
        } else {
            // lalu cek token di db ada atau tidak
            $refresh_token = $this->RefreshTokens->getToken(['token' => $token], 'user_id,expired_at');
            if($refresh_token) {
                $now  = new DateTime();
                $expired_at  = new DateTime($refresh_token->expired_at);
                if($now <= $expired_at) {
                    // generate new token
                    $user = $this->UsersModel->getUser(['user_id' => $refresh_token->user_id], Users::getFieldsForToken(), 'user_id DESC');
                    if($user) {
                        $count_referral = $this->ReferralModel->countReferralActiveByParent(['user_id' => $refresh_token->user_id]);
                        if(!$count_referral) $user->count_referral = "0";
                        else $user->count_referral = $count_referral->count_referral;
                        $response->success = true;
                        $response->data = ['token' => T::create($user)];
                        $response->message = "Success creating new token. ";
                    } else {
                        $response->message = "User not found. ";
                    }
                } else {
                    $response->message = "Refresh Token is expired. ";
                }    
            } else {
                $response->message = "Resfresh Token does not exist. ";
            }

        }

        return $this->respond($response, 200);
    }

}
=======
<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use App\Models\Users;
use App\Libraries\Token as T;
use App\Models\Referrals;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use DateTime;

class Token extends BaseController
{

    use ResponseTrait;

    protected $request, $UsersModel;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->UsersModel = new Users();
        $this->ReferralModel = new Referrals();
        $this->RefreshTokens = new RefreshTokens();
        helper('rest_api');
    }

    public function refresh()
    {
        $response = initResponse();

        $token = $this->request->getPost('token') ?? '';
        if(empty($token)) {
            $response->message = "Token is required. ";
        } else {
            // lalu cek token di db ada atau tidak
            $refresh_token = $this->RefreshTokens->getToken(['token' => $token], 'user_id,expired_at');
            if($refresh_token) {
                $now  = new DateTime();
                $expired_at  = new DateTime($refresh_token->expired_at);
                if($now <= $expired_at) {
                    // generate new token
                    $user = $this->UsersModel->getUser(['user_id' => $refresh_token->user_id], Users::getFieldsForToken(), 'user_id DESC');
                    if($user) {
                        $count_referral = $this->ReferralModel->countReferralActiveByParent(['user_id' => $refresh_token->user_id]);
                        if(!$count_referral) $user->count_referral = "0";
                        else $user->count_referral = $count_referral->count_referral;
                        $response->success = true;
                        $response->data = ['token' => T::create($user)];
                        $response->message = "Success creating new token. ";
                    } else {
                        $response->message = "User not found. ";
                    }
                } else {
                    $response->message = "Refresh Token is expired. ";
                }    
            } else {
                $response->message = "Resfresh Token does not exist. ";
            }

        }

        return $this->respond($response, 200);
    }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
