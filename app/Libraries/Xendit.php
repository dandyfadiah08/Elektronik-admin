<?php

namespace App\Libraries;

class Xendit
{

    public function __construct()
    {
        $this->host = 'https://api.xendit.co/';
        helper('rest_api');
        helper('log');
    }

    /*
    @param $external_id string
    @param $amount number
    @param $bank_code string
    @param $account_number string
    @param $account_holder_name string
    @param $description string
    @return $response object
    */
    function create_disbursements($external_id, $amount, $bank_code, $account_number, $account_holder_name, $description)
    {
        $response = initResponse();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.'disbursements',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "external_id": "'.$external_id.'",
                "amount": '.$amount.',
                "bank_code": "'.$bank_code.'",
                "account_number": "'.$account_number.'",
                "account_holder_name": "'.$account_holder_name.'",
                "description":"'.$description.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode(env('xendit.apikey').':').'',
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
        writeLog("xendit",
            "create_disbursements\n" 
            .json_encode([$external_id, $amount, $bank_code, $account_number, $account_holder_name, $description])."\n"
            .json_encode($response)
        );

        return $response;
    }

    /*
    @return $response object
    */
    function available_disbursements_banks()
    {
        $response = initResponse();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.'available_disbursements_banks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode(env('xendit.apikey').':').'',
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
        writeLog("xendit",
            "available_disbursements_banks\n" 
            .json_encode($response)
        );

        return $response;
    }

    /*
    @return $response object
    */
    function validate_bank_detail($bank_code, $bank_account_number)
    {
        $response = initResponse();

        $curl = curl_init();

        // var_dump('{
        //     "bank_code": "'.$bank_code.'",
        //     "bank_account_number": "'.$bank_account_number.'",
        // }');die;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.'bank_account_data_requests',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "bank_code": "'.$bank_code.'",
                "bank_account_number": "'.$bank_account_number.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode(env('xendit.apikey').':').'',
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
        writeLog("xendit",
            "validate_bank_detail\n" 
            .json_encode($response)
        );

        return $response;
    }
}
