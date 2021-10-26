<<<<<<< HEAD
<?php

namespace App\Libraries;

class Nodejs
{

    public function __construct()
    {
        $this->host = env('nodejs.url').':'.env('nodejs.port').'/';
        helper('rest_api');
    }

    /*
    @param $event string
    @param $data mixed string|array|object
    @return $response object
    */
    public function emit($event, $data)
    {
        $response = initResponse();

        $curl = curl_init();

        $postData = json_encode([
            'event' => $event,
            'data' => $data,
        ]);
        // var_dump($postData);die;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.'emit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode('key:'.env('nodejs.key')),
            ),
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        if (curl_errno($curl)) {
            $response->data['error'] = curl_error($curl);
        } else {
            $result = json_decode($result);
            $response->data = $result;
            if(isset($result->error_code)) {
                $response->message = 'Problems occured.';
            } else {
                $response->success = true;
                $response->message = 'OK';
            }
        }
        return $response;
    }

}
=======
<?php

namespace App\Libraries;

class Nodejs
{

    public function __construct()
    {
        $this->host = env('nodejs.url').':'.env('nodejs.port').'/';
        helper('rest_api');
    }

    /*
    @param $event string
    @param $data mixed string|array|object
    @return $response object
    */
    public function emit($event, $data)
    {
        $response = initResponse();

        $curl = curl_init();

        $postData = json_encode([
            'event' => $event,
            'data' => $data,
        ]);
        // var_dump($postData);die;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.'emit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode('key:'.env('nodejs.key')),
            ),
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        if (curl_errno($curl)) {
            $response->data['error'] = curl_error($curl);
        } else {
            $result = json_decode($result);
            $response->data = $result;
            if(isset($result->error_code)) {
                $response->message = 'Problems occured.';
            } else {
                $response->success = true;
                $response->message = 'OK';
            }
        }
        return $response;
    }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
