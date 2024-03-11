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


    Route::get("get-wave-payment-url", function () {
        return LaravelMyanmarPaymentsFacade::channel("kbz_pay.pwaapp")->getPaymentScreenUrl();
    });
});
