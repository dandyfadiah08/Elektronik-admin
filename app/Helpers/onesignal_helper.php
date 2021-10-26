<?php

/*
@return $output string
*/
function sendNotification($notification_id = [], $title = '', $body = '', $data = [], $external = true) {
    $output = initResponse("Error");
    $response = json_decode(sendNotificationOneSignal($notification_id, $title, $body, $data, $external));
    if($response === null) {
        $output->data = ['error' => 'No response from the platform. '];
    } elseif(isset($response->errors)) {
        $output->data = ['error' => $response->errors];
    } else {
        $output->success = true;
        $output->message = "Success";
    }
    return $output;
}
/*
@return $response string
*/
function sendNotificationOneSignal($notification_id = [], $title = '', $body = '', $data = [], $external = true) {
    $notification_type = $external ? 'include_external_user_ids' : 'include_player_ids';
    $fields = [
        'app_id' => env('onesignal.app_id_mobile'),
        $notification_type => $notification_id,
        'channel_for_external_user_ids' => 'push',
        'data' => $data,
        'headings' => ["en" => $title],
        'contents' => ["en" => $body],
    ];
    
    $fields = json_encode($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.env('onesignal.rest_api_auth')
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}
