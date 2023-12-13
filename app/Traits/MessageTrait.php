<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait MessageTrait
{
    //  $apiKey = config::get('services.africastalking.api_key');
    //send message
    public function sendMessage(string $phoneNumber, string $message)
    {
        $key =  Config::get('services.AT.apiKey');
        $username =  Config::get('services.AT.AppName');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.africastalking.com/version1/messaging',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "username=" . $username . "&to=" . urlencode($phoneNumber) . "&message=" . urlencode($message),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'apiKey: ' . $key
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
