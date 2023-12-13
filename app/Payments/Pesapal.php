<?php

namespace App\Payments;

use Illuminate\Support\Facades\Config;

class Pesapal
{
    protected static $pesapalBaseUrl = "https://pay.pesapal.com/v3";
    protected static $body;
    protected static $headers;
    protected static $response;
    protected static $options;
    protected static $manager;

    protected static $consumerKey;
    protected static $consumerSecret;

    public static function loadConfig()
    {
        self::$consumerKey = Config::get('services.pesapal.consumer_key');
        self::$consumerSecret = Config::get('services.pesapal.consumer_secret');
    }

    public static function pesapalBaseUrl()
    {
        try {
            //code...
            return self::$pesapalBaseUrl;
        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
    }


    public static function pesapalAuth()
    {

        try {
            //code...
            self::loadConfig();
            $url = self::$pesapalBaseUrl . "/api/Auth/RequestToken";
            $headers = array("Content-Type" => "application/json", 'accept' => 'application/json');
            $body = json_encode(array(
                'consumer_key' => self::$consumerKey,
                'consumer_secret' => self::$consumerSecret,
            ));

            $data = Curl::PostToken($url, $headers, $body);
            $data = json_decode(json_encode($data));

            return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }

    public static function pesapalRegisterIPN(string $ipnUrl)
    {
        //return $url;
        try {

            //code...
            $token = self::pesapalAuth();

            if (!$token->success) {
                throw new \Exception("Failed to obtain Token");
            }

            $url = self::$pesapalBaseUrl . "/api/URLSetup/RegisterIPN";
            $headers = array("Content-Type" => "application/json", 'accept' => 'application/json', 'Authorization' => 'Bearer ' . $token->message->token);

            $body = json_encode(array(
                "url" => $ipnUrl,
                "ipn_notification_type" => 'POST',
            ));

            $data = Curl::Post($url, $headers, $body);
            $data = json_decode(json_encode($data));
            return $data;
        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
        //18213
    }

    public static function listIPNS()
    {
        try {
            //code...
            $token = self::pesapalAuth();

            if (!$token->success) {
                throw new \Exception("Failed to obtain Token");
            }

            $url = self::$pesapalBaseUrl . "/api/URLSetup/GetIpnList";
            $headers = array("Content-Type" => "application/json", 'accept' => 'application/json', 'Authorization' => 'Bearer ' . $token->message->token);

            $data = Curl::Get($url, $headers);
            $data = json_decode(json_encode($data));

            return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }

    public static function orderProcess($amount, $phone, $callback, $updatePesapalIPNID = "adeac997-b32d-4f06-9846-ddd65093eab4")
    {
        try {
            //code...
            $token = self::pesapalAuth();
            // $supportedCurrencies = strtoupper(self::$options->businessCurrency);

            $payload = json_encode(array(
                'id' => rand(0, 9999999999),
                'currency' => 'UGX',
                'amount' => $amount,
                'description' => 'testApi',
                'redirect_mode' => 'PARENT_WINDOW',
                'callback_url' => $callback,
                'notification_id' => $updatePesapalIPNID,
                'billing_address' => array(
                    'phone_number' => $phone,
                    'first_name' => 'Katene',
                    'last_name' => 'Nicholas',

                )
            ));

            if (!$token->success) {
                throw new \Exception("Failed to obtain Token");
            }

            $url = self::$pesapalBaseUrl . "/api/Transactions/SubmitOrderRequest";
            $headers = array("Content-Type" => "application/json", 'accept' => 'application/json', 'Authorization' => 'Bearer ' . $token->message->token);
            $data = Curl::Post($url, $headers, $payload);

            $data = json_decode(json_encode($data));

            return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public static function transactionStatus()
    {
        try {
            //code...
            $transId = $_GET['OrderTrackingId'];
            $merchant = $_GET['OrderMerchantReference'];

            if (!isset($transId) || empty($transId)) {


                return response()->json(['success' => false, 'message' => 'Missing Transaction ID']);
            }

            $token = self::pesapalAuth();
            if (!$token->success) {
                return response()->json(['success' => false, 'message' => 'Failed to obtain Token', 'response' => $token]);
            }

            $url = self::$pesapalBaseUrl . "/api/Transactions/GetTransactionStatus?orderTrackingId={$transId}";
            $headers = array("Content-Type" => "application/json", 'accept' => 'application/json', 'Authorization' => 'Bearer ' . $token->message->token);
            $data = Curl::Get($url, $headers);

            $data = json_decode(json_encode($data));

            return $data;
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
