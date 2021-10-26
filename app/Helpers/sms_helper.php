<<<<<<< HEAD
<?php

/*
@return object
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function sendSmsOtp($phone, $otp, $signature = '') {
    // implemntasikan kirim sms
    $response = initResponse("Failed to send SMS to $phone");

    $message = "<#> Your OTP code for ".env('app.name')." is $otp. $signature";
    $sendSMS = sendSms($phone, $message);
    if($sendSMS->success) {
        $response->success = true;
        $response->message = "OTP code successfully sent to $phone";
    }
    return $response;
}

/*
@return object
*/
function sendSms($phone, $message) {
    // implemntasikan kirim sms
    $debug = env('otp.debug'); // true = tidak kirim sms, false = kirim sms
    $response = initResponse("Failed to send SMS to $phone");
    

    if ($debug) {
        // $dummyJson = '{"status":0, "array":[[6289602350857,4949887]],"success":1, "fail":0}';
        $dummyJson = '{"ErrorCode":0,"ErrorDescription":null,"Data":[{"MessageErrorCode":0,"MessageErrorDescription":"Success","MobileNumber":"628976563991","MessageId":"0bda17b9-6246-4919-9ec1-801d826d77fd","Custom":""}]}';
        $responseBody = json_decode($dummyJson);
    } else {
        $phone = str_replace(' ', '', $phone);
        $senderId = "Plusphone"; //Sender ID or SMS Masking, jika kosong akan menggunakan sender default
        // $username = "otp_plusphone"; //username Anda
        // $password = "2RcyieVd"; //password Anda
        $apiKey = "LUldDq34bFhs9B3X6+zVyDBKU1g/bgnhW9VLOQ68ibA=";
        $clientId = "2c61ab2d-3df2-4785-ba61-9c5d1633dbff";

        $message = rawurlencode($message);
        // $getUrl = "https://numberic1.tcastsms.net:20005/sendsms?";
        // $apiUrl = $getUrl . 'account=' . $username . '&password=' . $password . '&numbers=' . $phone . '&content=' . $message . '&sender=' . $senderId;
        $apiUrl = "https://api.tcastsms.net/api/v2/SendSMS?ApiKey=$apiKey&ClientId=$clientId&SenderId=$senderId&Message=$message&MobileNumbers=$phone&Is_Unicode=false&Is_Flash=false";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept:application/json'
            ]
        );

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseBody = json_decode($result);
        curl_close($ch);
    }

    // var_dump($responseBody);die;
    // if (isset($responseBody->success)) {
    //     if ($responseBody->success == 1) {
    //         $response->success = true;
    //         $response->message = "Berhasil mengirim SMS ke $phone";
    //     }
    // }
    if (isset($responseBody->ErrorCode)) {
        if ($responseBody->ErrorCode == 0) {
            $response->success = true;
            $response->message = "Successfully to send SMS to $phone";
        }
    }

    return $response;
}
=======
<?php

/*
@return object
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function sendSmsOtp($phone, $otp, $signature = '') {
    // implemntasikan kirim sms
    $response = initResponse("Failed to send SMS to $phone");

    $message = "<#> Your OTP code for ".env('app.name')." is $otp. $signature";
    $sendSMS = sendSms($phone, $message);
    if($sendSMS->success) {
        $response->success = true;
        $response->message = "OTP code successfully sent to $phone";
    }
    return $response;
}

/*
@return object
*/
function sendSms($phone, $message) {
    // implemntasikan kirim sms
    $debug = env('otp.debug'); // true = tidak kirim sms, false = kirim sms
    $response = initResponse("Failed to send SMS to $phone");
    

    if ($debug) {
        // $dummyJson = '{"status":0, "array":[[6289602350857,4949887]],"success":1, "fail":0}';
        $dummyJson = '{"ErrorCode":0,"ErrorDescription":null,"Data":[{"MessageErrorCode":0,"MessageErrorDescription":"Success","MobileNumber":"628976563991","MessageId":"0bda17b9-6246-4919-9ec1-801d826d77fd","Custom":""}]}';
        $responseBody = json_decode($dummyJson);
    } else {
        $phone = str_replace(' ', '', $phone);
        $senderId = "Plusphone"; //Sender ID or SMS Masking, jika kosong akan menggunakan sender default
        // $username = "otp_plusphone"; //username Anda
        // $password = "2RcyieVd"; //password Anda
        $apiKey = "LUldDq34bFhs9B3X6+zVyDBKU1g/bgnhW9VLOQ68ibA=";
        $clientId = "2c61ab2d-3df2-4785-ba61-9c5d1633dbff";

        $message = rawurlencode($message);
        // $getUrl = "https://numberic1.tcastsms.net:20005/sendsms?";
        // $apiUrl = $getUrl . 'account=' . $username . '&password=' . $password . '&numbers=' . $phone . '&content=' . $message . '&sender=' . $senderId;
        $apiUrl = "https://api.tcastsms.net/api/v2/SendSMS?ApiKey=$apiKey&ClientId=$clientId&SenderId=$senderId&Message=$message&MobileNumbers=$phone&Is_Unicode=false&Is_Flash=false";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept:application/json'
            ]
        );

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseBody = json_decode($result);
        curl_close($ch);
    }

    // var_dump($responseBody);die;
    // if (isset($responseBody->success)) {
    //     if ($responseBody->success == 1) {
    //         $response->success = true;
    //         $response->message = "Berhasil mengirim SMS ke $phone";
    //     }
    // }
    if (isset($responseBody->ErrorCode)) {
        if ($responseBody->ErrorCode == 0) {
            $response->success = true;
            $response->message = "Successfully to send SMS to $phone";
        }
    }

    return $response;
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
