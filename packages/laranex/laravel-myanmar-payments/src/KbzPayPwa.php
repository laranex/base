<?php

namespace Laranex\LaravelMyanmarPayments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KbzPayPwa
{
    public function getPaymentScreenUrl()
    {
        $kbzPayConfig = config("laravel-myanmar-payments.kbz_pay");
        return  [ 'url' => $this->preCreate($kbzPayConfig)];
    }

    private function preCreate($kbzPayConfig)
    {
        $baseUrl = $kbzPayConfig["base_url"];
        $pwaUrl = $kbzPayConfig["pwa_url"];

        $merchantCode = $kbzPayConfig["merchant_code"];
        $appId = $kbzPayConfig["app_id"];
        $appKey = $kbzPayConfig["app_key"];
//        $nonceStr = strtoupper(Str::random(32));
        $nonceStr = "5K8264ILTKCH16CQ2502SI8ZNMTM67VS";
        $method = "kbz.payment.precreate";

//        $notifyUrl = "https://tikkat-api-uat.onenex.dev/api/v1/purchases/payment-callback";
        $notifyUrl = "https://heal-uatapi.onenex.dev/api/purchases/payment-callback";
        $timestamp = (string)now()->timestamp;
        $tradeType = "PWAAPP";
        $tranCurrency = "MMK";
//        $totalAmount = (string) rand(100000, 9999999);
        $totalAmount = "100";
        $version = "1.0";

        $merchantOrderId = (string) rand(100000000000000, 999999999999999);

        $string = "appid=$appId&merch_code=$merchantCode&merch_order_id=$merchantOrderId&method=$method&nonce_str=$nonceStr&notify_url=$notifyUrl&timestamp=$timestamp&total_amount=$totalAmount&trade_type=$tradeType&trans_currency=$tranCurrency&version=$version&key=$appKey";
        $hash = strtoupper(hash('SHA256', $string));


        $bizContent = [
            "appid" => $appId,
            "merch_code" => $merchantCode,
            "merch_order_id" => $merchantOrderId,
            "total_amount" => $totalAmount,
            "trade_type" => $tradeType,
            "trans_currency" => $tranCurrency
        ];

        $response = Http::post("$baseUrl/precreate", [
            "Request" => [
                "timestamp" => $timestamp,
                "notify_url" => $notifyUrl,
                "method" => $method,
                "nonce_str" => $nonceStr,
                "sign_type" => "SHA256",
                "sign" => $hash,
                "version" => $version,
                "biz_content" => $bizContent
            ]
        ]);

        $result = json_decode($response)->Response;
        $prePayId = $result->prepay_id;
        $secondString = "appid=$appId&merch_code=$merchantCode&nonce_str=$nonceStr&prepay_id=$prePayId&timestamp=$timestamp&key=$appKey";
        $secondHash = strtoupper(hash('SHA256', $secondString));
        return "$pwaUrl/?appid=$appId&merch_code=$merchantCode&nonce_str=$nonceStr&prepay_id=$prePayId&timestamp=$timestamp&sign=$secondHash";
    }

    public function verifyPayment($merchantOrderId)
    {
        $kbzPayConfig = config("laravel-myanmar-payments.kbz_pay");
        $response = $this->getQueryOrderInterface($kbzPayConfig, (string) $merchantOrderId);
        $responseBody = json_decode($response)->Response;
        return $this->verifySignature($responseBody, $kbzPayConfig);
    }
    private function getQueryOrderInterface($kbzPayConfig, $merchantOrderId)
    {
        $baseUrl = $kbzPayConfig["base_url"];

        $merchantCode = $kbzPayConfig["merchant_code"];
        $appId = $kbzPayConfig["app_id"];
        $appKey = $kbzPayConfig["app_key"];
        $nonceStr = "5K8264ILTKCH16CQ2502SI8ZNMTM67VS";
        $method = "kbz.payment.queryorder";

        $timestamp = (string)now()->timestamp;
        $version = "3.0";

        $string = "appid=$appId&merch_code=$merchantCode&merch_order_id=$merchantOrderId&method=$method&nonce_str=$nonceStr&timestamp=$timestamp&version=$version&key=$appKey";
        $hash = strtoupper(hash('SHA256', $string));

        $bizContent = [
            "appid" => $appId,
            "merch_code" => $merchantCode,
            "merch_order_id" => $merchantOrderId
        ];
        info('verify_request_payload',["Request" => [
                "timestamp" => $timestamp,
                "method" => $method,
                "nonce_str" => $nonceStr,
                "sign_type" => "SHA256",
                "sign" => $hash,
                "version" => $version,
                "biz_content" => $bizContent
            ]]);
        return Http::post("$baseUrl/queryorder", [
            "Request" => [
                "timestamp" => $timestamp,
                "method" => $method,
                "nonce_str" => $nonceStr,
                "sign_type" => "SHA256",
                "sign" => $hash,
                "version" => $version,
                "biz_content" => $bizContent
            ]
        ]);
    }

    private function verifySignature($body, $kbzPayConfig)
    {
        $sign = $body->sign;
        $responseBodyCollection = collect(json_decode(json_encode ( $body ) , true));

        // except sign and sign_type
        $filteredCollection = $responseBodyCollection->except(['sign', 'sign_type'])->sortKeys()->all();
        $stringA = http_build_query($filteredCollection);
        $stringAWithAppKey = $stringA . "&key=" . $kbzPayConfig["app_key"];

        $hash = strtoupper(hash('SHA256', $stringAWithAppKey));

        return hash_equals($hash, $sign);
    }
}
