<?php

namespace App\Libraries;

use App\Models\RefreshTokens;
use Firebase\JWT\JWT;

class Token
{
    /*
    @param $data object
    @return $token string
    */
    static function create($data)
    {
        $jwt = new JWT();
        $payload = [
            'iat' => time(),
            'exp' => time()+env('jwt.expire'),
            'data' => [
                'user_id'           => $data->user_id,
                'name'              => $data->name,
                'email'             => $data->email,
                'phone_no'          => $data->phone_no,
                'status'            => $data->status,
                'phone_no_verified' => $data->phone_no_verified,
                'email_verified'    => $data->email_verified,
                'type'              => $data->type,
                'submission'        => $data->submission,
                'active_balance'    => $data->active_balance,
                'count_referral'    => $data->count_referral,
            ],
        ];
        return $jwt->encode($payload, env('jwt.key'));
    }

    /*
    @param $data object
    @return $token string
    */
    static function createRefreshToken($data)
    {
        $jwt = new JWT();
        $payload = [
            'iat' => time(),
            'data' => [
                'user_id'   => $data->user_id,
                'random'    => rand(10000,99999),
            ],
        ];
        $token = $jwt->encode($payload, env('jwt.key'));
        Token::saveToDatabase($token, $data->user_id, env('jwt.expire_refresh_token'));
        // Token::saveToRedis($token, "refresh_token:".$data->user_id);
        return $token;
    }

    /*
    @param $token string
    @param $user_id integer
    @param $duration integer (in days, default 30)
    */
    static function saveToDatabase($token, $user_id, $duration = 30)
    {
        $created_at = date("Y-m-d H:i:s");
        $expired_at = date('Y-m-d H:i:s', strtotime("$created_at+$duration days"));

        $refreshTokens = new RefreshTokens();
        $existing_data = $refreshTokens->getToken(['user_id' => $user_id]);
        $data = [
            'user_id'       => $user_id,
            'token'         => $token,
            'created_at'    => $created_at,
            'expired_at'    => $expired_at,
        ];
        if($existing_data) {
            $refreshTokens->where(['user_id' => $user_id])->set($data)->update(); // update
        } else {
            $refreshTokens->insert($data); // create new / insert
        }
    }

    /*
    @param $token string
    @param $key string
    @param $duration integer (in seconds, default 3600)
    */
    static function saveToRedis($token, $key, $duration = 3600)
    {
        helper('redis');
        $redis = RedisConnect();
        $redis->setex($key, $duration, $token);
    }

        /*
    @param $data object
    @return $token string
    */
    static function createApp1Token($check_id)
    {
        $jwt = new JWT();
        $duration = env('app1.token_expire');
        $created_at = date('now');
        $expired_at = strtotime("+$duration days");
        $payload = [
            'iat' => $created_at,
            'exp' => $expired_at,
            'data' => [
                'check_id'  => $check_id,
            ],
        ];
        return $jwt->encode($payload, env('jwt.key'));
    }


}
