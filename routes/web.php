<?php

use Illuminate\Support\Facades\Route;
use Laranex\LaravelMyanmarPayments\LaravelMyanmarPaymentsFacade;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(["prefix" => "laravel-myanmar-payments"], function () {


    Route::get("get-kbzpay-payment-url", function () {
        return LaravelMyanmarPaymentsFacade::channel("kbz_pay.pwaapp")->getPaymentScreenUrl();
    });

    Route::get("get-kbzpay-payment-verify/{merchant_order_id}", function ($merchant_order_id) {
        return LaravelMyanmarPaymentsFacade::channel("kbz_pay.pwaapp")->verifyPayment($merchant_order_id);
    });
});
