<?php

/*
@return object
need to load helper rest_api and redis in controller before this function is called -> helper('rest_api');
*/
function generateCodeOTP($destination = false) {
    $response = initResponse('Destination is required.');
    if($destination) {
        try {
            $redis = RedisConnect();
            $key = "otp:$destination";
            $checkCodeOTP = checkCodeOTP($key, $redis);
            if($checkCodeOTP->success) {
                // sudah ada dan belum boleh kirim sms lagi seharusnya
                $second = $checkCodeOTP->data['ttl'];
                $response->message = "Please wait another $second seconds before resent OTP code.";
            } else {
                // belum ada, buat baru
                $otp = generateRandomNumericCode();
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

/*
@return object
need to load helper rest_api and redis in controller before this function is called -> helper('rest_api');
*/
function generateEmailVerificationLink($user_id, $destination) {
    $response = initResponse('Failed');
    try {
        $redis = RedisConnect();
        $key = "otp:$destination";
        // belum ada, buat baru
        $otp = generateRandomNumericCode();
        $otp = hash_hmac('sha256', "$otp::$destination", env('encryption.key'));
        $redis->setex($key, env('otp.duration.email'), $otp); // jika pakai otp lama, akan diupdate expired nya
        $link = base_url("verification/email/$user_id/$otp");
        $response->message = $link;
        $response->success = true;
    } catch(\Exception $e) {
        $response->message = $e->getMessage();
    }
    return $response;
}

/*
@return object
need to load helper rest_api and redis in controller before this function is called -> helper('rest_api');
*/
function generateChangeEmailVerificationLink($user_id, $destination) {
    $response = initResponse('Failed');
    try {
        $redis = RedisConnect();
        $key = "change_email:$user_id";
        $key_value = "change_email_value:$user_id";
        // belum ada, buat baru
        $otp = generateRandomNumericCode();

        $otp = hash_hmac('sha256', "$otp::$destination", env('encryption.key'));
        $redis->setex($key, 86400, $otp); // jika pakai otp lama, akan diupdate expired nya, 86400 (s) = 1 day
        $redis->setex($key_value, 86430, $destination);
        $link = base_url("verification/change_email/$user_id/$otp");
        $response->message = $link;
        $response->success = true;
    } catch(\Exception $e) {
        $response->message = $e->getMessage();
    }
    return $response;
}

/*
@return object
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function getRedisValue($key, $redis) {
    $response = initResponse();
    try {
        $value = $redis->get($key);
        if($value !== FALSE) {
            // otp found
            $second = $redis->ttl($key);
            $response->success = true;
            $response->message = "$key is found";
            $response->data = [
                'value' => $value,
                'ttl' => $second,
            ];
        } else {
            $response->message = "$key is not found";
        }
    } catch(\Exception $e) {
        $response->message = $e->getMessage();
    }
    return $response;
}

/*
@return object
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function checkCodeOTP($key, $redis, $otp_text = 'OTP') {
    $response = initResponse();
    try {
        $otp = $redis->get($key);
        if($otp !== FALSE) {
            // otp found
            $second = $redis->ttl($key);
            $response->success = true;
            $response->message = "$otp_text is found";
            $response->data = [
                'otp' => $otp,
                'ttl' => $second,
            ];
        } else {
            $response->message = "$otp_text is not found";
        }
    } catch(\Exception $e) {
        $response->message = $e->getMessage();
    }
    return $response;
}

/*
@return string
*/
function generateRandomNumericCode($length = 0)
{
    $length = (int)$length < 1 ? env('otp.length') : $length;
    $pool = '1234567890';
    $otp = '';
    if(env('CI_ENVIRONMENT') == 'development') {
        // development output : 111111
        for ($i = 0; $i < $length; $i++)
            $otp .= substr($pool, 0, 1); // output : 1
    } else {
        for ($i = 0; $i < $length; $i++) 
            $otp .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
    }
    return $otp;
}
