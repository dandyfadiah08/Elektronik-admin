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
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function checkCodeOTP($key, $redis) {
    $response = initResponse();
    try {
        $otp = $redis->get($key);
        if($otp !== FALSE) {
            // otp found
            $second = $redis->ttl($key);
            $response->success = true;
            $response->message = "OTP is found";
            $response->data = [
                'otp' => $otp,
                'ttl' => $second,
            ];
        } else {
            $response->message = "OTP is not found";
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
    for ($i = 0; $i < $length; $i++) {
        $otp .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
    }
    return $otp;
}
