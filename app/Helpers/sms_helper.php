<?php

/*
@return object
need to load helper rest_api in controller before this function is called -> helper('rest_api');
*/
function sendSmsOtp($phone, $otp, $signature = '') {
    // implemntasikan kirim sms
    $response = initResponse("Failed to send SMS to $phone");

    $message = "Your OTP code for ".env('app.name')." is $otp. $signature";
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
        $senderId = env('tcast.senderId'); //Sender ID or SMS Masking, jika kosong akan menggunakan sender default
        $apiKey = env('tcast.apiKey');
        $clientId = env('tcast.clientId');

        $message_encoded = rawurlencode($message);
        $apiUrl = "https://api.tcastsms.net/api/v2/SendSMS?ApiKey=$apiKey&ClientId=$clientId&SenderId=$senderId&Message=$message_encoded&MobileNumbers=$phone&Is_Unicode=false&Is_Flash=false";

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

        // kirim ke semua nomor
        // if(str_starts_with($phone, '6289')) {
            // kirim juga lewat WA
            $result = sendWhatsApp($phone, $message);
            if(json_decode($result)) $result = json_decode($result);
            else $result = json_decode('{"error":true,"message":"'.$result.'","data":null');
                log_message(
                    "debug",
                    "sendWhatsApp\n"
                    . json_encode($result)
                );
            
        // }

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
    log_message(
        "debug",
        "sendSms\n"
        . json_encode($responseBody)."\n"
        . json_encode($response)
    );

    return $response;
}

function sendWhatsApp($number, $message) {
    $data = json_encode([
        'api' => 'wowfone',
        'key_api' => env('whatsapp.key'),
        'message' => $message,
        'number' => $number,
        'url' => '',
        'filename' => '',
        'type' => 'chat',
        'id' => '',
    ]);
    $url = env('whatsapp.host').'/message/send';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    if ($result === FALSE) $result = curl_error($ch);
    // $info = curl_getinfo($ch);
    curl_close($ch);
    return $result;
}
