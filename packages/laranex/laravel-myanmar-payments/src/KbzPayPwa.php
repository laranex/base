<?php

namespace Laranex\LaravelMyanmarPayments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KbzPayPwa
{
    public function getPaymentScreenUrl()
    {
        $kbzPayConfig = config("laravel-myanmar-payments.kbz_pay");
        $this->preCreate($kbzPayConfig);
    }

    private function preCreate($kbzPayConfig)
    {
        $baseUrl = $kbzPayConfig["base_url"];

        $merchantCode = $kbzPayConfig["merchant_code"];
        $appId = $kbzPayConfig["app_id"];
        $appKey = $kbzPayConfig["app_key"];
        $nonceStr = strtoupper(Str::random(32));
        $method = "kbz.payment.precreate";
        $notifyUrl = "https://tikkat-api-uat.onenex.dev/api/v1/purchases/payment-callback";
        $timestamp = (string)now()->timestamp;
        $tradeType = "PWAAPP";
        $tranCurrency = "MMK";
        $totalAmount = rand(100000, 9999999);
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

        dd($response->json());
    }
}
